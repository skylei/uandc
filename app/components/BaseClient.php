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

    

    public function onStart($server){
        $config = \Ouno\Ouno::config("SERVER");
	/*
        if(!empty($config['TIMER'])) {
            foreach ($config['TIMER'] as $time) {
                $server->addtimer($time);
            }
        }
	*/

	 echo "client start" . PHP_EOL;
    }	
   

    /**
     * bind fd to 0 user  
     */
    public function OnConnect($server, $fd, $from_id){
        echo "onconneect" . $fd . PHP_EOL;
	
        $this->getRedis()->bindFdToUid($fd, 0);

    }

    public function getRedis(){
        $config = \Ouno\Ouno::config("REDIS");
        return new redisDao();
    }


    /**
     * here should write into log
     *  
     */
    public function onOpen(swoole_websocket_server $server, $fd){
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
	echo "this is close" .PHP_EOL;
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
     * the headle client send message not the recevie action 
     */
    public function onMessage($server, $fd, $data, $opcode, $fin) {
    	echo "message from {$fd}:{$data},opcode:{$opcode},fin:{$fin}\n";
	$message = json_decode($data, true);
	if($message['type'] == 0){
	    $indexController = $this->getController("\\command\\index\\index");	
	    $loginUser = $indexController->checkLogin();
	    if(!$loginUser){
		$return = array(
		    "active" => "login",
		    "type"=> 1,
		    "message" => "user haven't login, please login",
		    "uid" => 0
   		);
		$this->server->push(json_encode($return));	
	    }else{
	        $return = array(
		    "active" => "online",
		    "type"=> 2,
		    "message" => "user haven't login, please login",
		    "uid" => $loginUser['userid'],
		    "to_uid" => 0	
   		);	
		$this->sendToChannel($return, $channel = "ALL");
	    }

	}else if($message['type'] == 1){
	    if($message['to_uid']){
		$to_fd = $this->getRedis->getFdByUid($message['to_uid']);
		$sendMessage = array(
		    "active"=> "sendOne",
		    "type" => 2,
		    "message"=> $message['message'],
		    "to_uid" => $message['to_uid'],
		    "uid" => $loginUser['userid']
		); 
		
		$this->sendOne($sendMessage);
	    }else{
		$sendMessage = array(
		    "active"=> "sendOne",
		    "type" => 2,
		    "message"=> $message['message'],
		    "to_uid" => $message['to_uid'],
		    "uid" => $loginUser['userid']
		);
	        $this->sendToChannel($sendMessage, $channel = "ALL");	
	    } 
	    
	   	
	}else if($message['type'] == 2){


	}else if($message['type'] == 3){

	}
    	$server->push($fd, $data);
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

   
    public function getController($class, $param = array()){
	return \Ouno\OFactory::getInstance($class, $param);
    }



}
