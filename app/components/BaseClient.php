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
     * 收到socket发来的信息
     * @param resource $server swoole server
     * @param int $fd
     * @param int $from_id
     * @param string $jdata json data
     * */
    public function onReceive($server, $fd, $from_id, $jdata)
    {
            
    }

    /*
     * 当客户端关闭时触发事件
     * @param resouce $server
     * @param int $client_id fd
     * @param int $from_id
     * @return void
     * */
    public function onClose($server, $client_id, $from_id){
	    echo "this is close $client_id" . PHP_EOL;
        $uid = $this->getRedis()->getUidByFd($client_id);
        $this->getRedis()->removeUser($client_id);
        $this->getRedis()->deletFromChannel($channel = "ALL", $uid);
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
        $this->sendToChannel($server, $data, $channel);
    }

    /*
     * 聊天
     * */
    private function chat($server, $fd, $data){
//        echo "chat" . PHP_EOL;
        //群聊，@todo 暂时没开启小组功能
        echo "chat:" . $data['to_uid']. PHP_EOL;
        if($data['to_uid'] == 0){
            $this->sendToChannel($server, $data);
        }else{
            $this->sendOne($server, $fd, $data);
        }
    }

    public function offline($server, $fd, $data){

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
