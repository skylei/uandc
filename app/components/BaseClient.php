<?php
/**
 * swoole客户端的基类
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/21
 * Time: 14:12
 */
namespace components;

use \src\dao\redis\redisDao as redisDao;
class BaseClient{


    const CHAT = 1;         //聊天
    const OLLIST = 2;       //获取在线列表
    const HB = 3;           //心跳
    const LOGOUT = 9;       //退出登录
    const ERROR = -1;

    

    public function onStart($server){
        $config = \Ouno\Ouno::config("SERVER");

        if(!empty($config['TIMER'])) {
            foreach ($config['TIMER'] as $time) {
                $server->addtimer($time);
            }
        }


	    echo "client start" . PHP_EOL;
    }	
   



    public function getRedis(){
        $config = \Ouno\Ouno::config("REDIS");
        return new redisDao();
    }


    /**
     * here should write into log
     *
     */
    public function onOpen(\swoole_websocket_server $server, $fd){
        echo "open $fd";

    }

    /*
     * 当客户端关闭时触发事件
     * @param resouce $server
     * @param int $client_id fd
     * @param int $from_id
     * @return void
     * */
    public function onClose($server, $fd, $from_id){
        $uid = $this->getRedis()->getUidByFd($fd);
        if($uid){
            $this->getRedis()->deletFromChannel($channel = 'ALL', $uid);
            $this->getRedis()->removeUser($fd);
            $data = array(
                "active"=>'offline',
                'uid'=> $uid,
                'to_uid' => 0,
                'message' => $uid . "下线了"
            );
            echo "close $uid";
            $this->sendToChannel($server, $data, $channel);
        }
    }

    public function onWorkerStart($server, $worker_id){
        echo "WorkerStart[$worker_id]|pid=" . posix_getpid() . ".\n";
    }

    public function onWorkerStop($server, $worker_id){
        $params = func_get_args();
        $worker_id = $params[1];
        echo "WorkerStop[$worker_id]|pid=" . posix_getpid() . ".\n";
    }

    public function onTask()
    {
	    echo "Task";
    }

    function onFinish($serv, $task_id, $data){
    	echo "finish \r\n";    
    }

    public function onShutdown(){
	    $params = func_get_args();
	    var_dump($params);
    }

    /**
     * bind fd to 0 user
     * @param resource $server
     * @param int $fd
     * @param int $from_id
     */
    public function OnConnect($server, $fd, $from_id){
        echo "onconneect" . $fd . PHP_EOL;
//        $this->getRedis()->clear();
//        $this->getRedis()->bindFdToUid($fd, 0);

    }
    
    /**
     * the headle client send message not the recevie action
     * @param resource $server
     * @param int $fd
     * @param string $data
     * @param unknow $opcode
     * @param int $fin
     */
    public function onMessage($server, $fd, $data, $opcode, $fin) {
    	echo "message from {$fd}:{$data},opcode:{$opcode},fin:{$fin}\n";

        $currentUser = $this->getRedis()->getUidByFd($fd);

	    $message = json_decode($data, true);
        var_dump($message);
        if(!$message['active'])
            return false;
        $allow = array("online", "chat", "chatall", "offline");
        if(in_array($message['active'], $allow));
            call_user_func_array(array($this, $message['active']), array($server, $fd, $message));
    }

    /*
     * 用户上线
     * */
    private function online($server, $fd, $data){
//        echo "online " . PHP_EOL;
        if(!$data['uid'])
            echo "uid inexist";
        $this->getRedis()->addFd($fd, $data['uid']);
        $this->getRedis()->bindChanel($channel = "ALL", $data['uid'], $fd);

        //由于windows上没装redis调试困难故而在此处获取好友列表
        $userList = $this->getRedis()->getChannelAllMember("ALL");
        $data['userList'] = $userList ? $userList : 0;
        $this->sendToChannel($server, $data, $channel);
    }

    /*
     * 聊天
     * */
    private function chat($server, $fd, $data){
        //群聊，@todo 暂时没开启小组功能
        echo "chat:" . $data['to_uid']. PHP_EOL;
        if(strval($data['to_uid']) == '0'){
            echo "chat send to All" . $data['to_uid'] . PHP_EOL;
            $this->sendToChannel($server, $data);
        }else{
            $to_fd = $this->getRedis()->getFdByUid($data['to_uid']);
            echo "chat $fd send TO one $to_fd " .PHP_EOL;
            $this->sendOne($server, $to_fd, $data);
        }
    }

    public function offline($server, $fd, $data){
        $server->close($fd);

    }

    /*
     * 想频道成员广播
     * */
    public function sendToChannel($server, $data, $channel = 'ALL')
    {

        $list = $this->getRedis()->getChannelAllMember($channel);
        if (empty($list)) {
            echo "channel ： {$channel} empty==".PHP_EOL;
            return;
        }

        foreach ($list as $uid=>$fd) {
            $this->sendOne($server, $fd, $data);
        }
    }

    /*
     * 单独向用户广播
     * */
    public function sendOne($server, $fd, $data)
    {
        if (empty($server) || empty($fd) || empty($data)) {
            return;
        }
        echo "send to  {$fd}  data:".json_encode($data).PHP_EOL;
        $data = json_encode($data);
        return $server->push($fd, $data);
    }

    /*
     * 触发定时器
     * */
    public function onTimer()
    {
        $params = func_get_args();
        $serv = $params[0];
        $interval = $params[1];
        switch ($interval) {
            case 66000: //heartbeat check
                $this->hbcheck($serv);
                break;
        }
    }

   
    public function getRedisService(){
	return \Ouno\Ouno::service('index', 'redis');
    }



}
