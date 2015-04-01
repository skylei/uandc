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

    const LOGIN = 1; //登录
    const LOGIN_SUCC = 2; //登录成功
    const RELOGIN = 3;      //重复登录
    const NEED_LOGIN = 4; //需要登录
    const LOGIN_ERROR = 5;  //登录失败
    const HB = 6;           //心跳
    const CHAT = 7;         //聊天
    const OLLIST = 8;       //获取在线列表
    const LOGOUT = 9;       //退出登录
    const ERROR = -1;

    public function __construct(){
        echo "this is BaseClient" . PHP_EOL;
    }

    public function onStart($server){
        $config = \Ouno\Ouno::config("SERVER");
        if(!empty($config['TIMER'])) {
            foreach ($config['TIMER'] as $time) {
                $server->addtimer($time);
            }
        }

	    echo "this is client start" . PHP_EOL;
    }	

    public function OnConnect($server, $fd, $from_id){
        echo "onconneect" . $fd . PHP_EOL;
        $this->getRedis()->bindFdToUid($fd, 0);
    }

    public function getRedis(){
        $config = \Ouno\Ouno::config("REDIS");
        return redisDao::getInstance($config);
    }

    /*
     * 收到socket发来的信息
     * @param resource $server swoole server
     * @param int $fd
     * @param int $from_id
     * @param string $jdata json data
     * */
    public function onReceive($server, int $fd, int $from_id, string $jdata)
    {
        $data = json_decode($jdata);
        echo "onreveive" . $fd . "|" . $jdata . PHP_EOL;
        $uid = $this->getRedis()->getUid($fd);
        $this->sendToChannel($server, $fd, self::CHAT, array($uid, "++receive++", $uid));
        return;

        if(!is_array($data))
            return null;
        if($data[0] == CHAT){
            $toId = \intval($data[1][0]);
            $msg = \strip_tags($data[1][1]);
            $uid = $this->getRedis()->getUiByFd($fd);
            if(empty($toId)){  //公共聊天
                $this->sendToChannel($server, self::CHAT, array($uid, $msg, $toId));
            } else { //私聊
                $toInfo = $this->getRedis()->getFdByUid($toId);
                if(!empty($toInfo)){
                    $this->sendOne($server, $toInfo['fd'], self::CHAT, array($uid, $msg, $toId));
                    $this->sendOne($server, $fd, self::CHAT, array($uid, $msg, $toId));
                }
            }
        }else if($data[0] == ONLINE){

        }
    }

    /*
     * 当客户端关闭时触发事件
     * @param resouce $server
     * @param int $client_id fd
     * @param int $from_id
     * @return void
     * */
    public function onClose($server, $client_id, $from_id){
        $uid = $this->getRedis()->getUid($client_id);
        $this->getRedis()->delete($client_id, $uid);
        $this->sendToChannel($server, self::LOGOUT, array($uid));
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

    }

    function onFinish($serv, $task_id, $data){
        
    }

    public function onShutdown(){
	    $params = func_get_args();
	    var_dump($params);
    }

    function onMessage($client_id, $ws){

    }


    /*
     * 收到用户发来消息
     * */
    public function onReceiv2e()
    {
        $params = func_get_args();
        $_data = $params[3];
        $serv = $params[0];
        $fd = $params[1];
        echo "from {$fd}: get data: {$_data}".PHP_EOL;
        $result = json_decode($_data, true);
        if(!is_array($result)) {
            return null;
        }

        switch ($result[0]) {
            /*
             *  array(
             *        1, array(uid, token)
             * )
             * */
            case self::LOGIN:
                $routeResult = $this->_route(array(
                    'a'=>'chat/main',
                    'm'=>'check',
                    'uid'=>$result[1][0],
                    'token'=>$result[1][1],
                ));

                if($routeResult) {  //登录成功
                    $uinfo = $this->getRedis()->get($result[1][0]);
                    if (!empty($uinfo)) {  //已登录过
                        $this->sendOne($serv, $uinfo['fd'], self::RELOGIN, []);
                        $this->getRedis()->delete($uinfo['fd'], $result[1][0]);
                        \swoole_server_close($serv, $uinfo['fd']);
                    }

                    /**
                     * 加入到fd列表中
                     */
                    $this->getRedis()->add($result[1][0], $fd);
                    $this->getRedis()->addFd($fd, $result[1][0]);
                    $this->sendToChannel($serv, self::LOGIN_SUCC, $routeResult);
                } else {       //登录失败
                    $this->sendOne($serv, $fd, self::LOGIN_ERROR, array($routeResult, $result[1][0], $result[1][1]));
                }
                break;
            case self::HB:  //心跳处理
                $uid = $this->getRedis()->getUid($fd);
                $this->getRedis()->uphb($uid);
                return null;
                break;
            case self::CHAT:
                $toId = \intval($result[1][0]);
                $msg = \strip_tags($result[1][1]);
                $uid = $this->getRedis()->getUid($fd);
                if(empty($toId)) {  //公共聊天
                    $this->sendToChannel($serv, self::CHAT, array($uid, $msg, $toId));
                } else { //私聊
                    $toInfo = $this->getRedis()->get($toId);
                    if(!empty($toInfo)) {
                        $this->sendOne($serv, $toInfo['fd'], self::CHAT, array($uid, $msg, $toId));
                        $this->sendOne($serv, $fd, self::CHAT, array($uid, $msg, $toId));
                    }
                }
                break;
            case self::OLLIST:
                $routeResult = $this->_route(array(
                    'a'=>'chat/main',
                    'm'=>'online',
                ));
                if(!empty($routeResult)) {
                    $this->sendOne($serv, $fd, self::OLLIST, $routeResult);
                }
                break;

        }
    }

    /*
     * 想频道成员广播
     * */
    public function sendToChannel($server, $cmd, $data, $channel = 'ALL')
    {
        $list = $this->getRedis()->getChannelMember($channel);
        if (empty($list)) {
            echo "{$channel} empty==".PHP_EOL;
            return;
        }

        foreach ($list as $fd) {
            echo "send to {$fd}===".PHP_EOL;
            $this->sendOne($server, $fd, $cmd, $data);
        }
    }

    /*
     * 单独向用户广播
     * */
    public function sendOne($server, $fd, $cmd, $data)
    {
        if (empty($server) || empty($fd) || empty($cmd)) {
            return;
        }
        //echo "send {$fd} cmd: {$cmd}, len:".json_encode($data).PHP_EOL;
        $data = json_encode(array($cmd, $data));
        return \swoole_server_send($server, $fd, $data);
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

    public function executeController($param){



    }




}
