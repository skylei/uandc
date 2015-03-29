<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015/3/21
 * Time: 14:12
 */
namespace components;

class BaseSocket{

    /*
     * @var $server Swooleserver
     * */
    public $server;

    /*
     * @var $client swoole_client
     * */
    public $client;

    /*
     * @var $config swoole config
     * */
    public $config = array();
    /*
     * 构造函数实例swoole_server
     * 并执行run　方法
     * @param arrat $config
     * */
    public function __contruct($config){
        echo "this is BaseSocket";
        return;
        $this->config = $config;
        $this->server = new \swoole_server($config['host'], $config['port'], $config['work_mode']);
        $this->server->set(array(
            'reactor_num' => empty($config['reactor_num']) ? 2 : $config['reactor_num'], //reactor thread num
            'worker_num' => empty($config['worker_num']) ? 2 : $config['worker_num'], //worker process num
            'task_worker_num' => empty($config['task_worker_num']) ? 2 : $config['task_worker_num'], //task worker process num
            'backlog' => empty($config['backlog']) ? 128 : $config['backlog'], //listen backlog));
            'max_request' => empty($config['max_request']) ? 1000 : $config['max_request'],
            'max_conn' => empty($config['max_conn']) ? 100000 : $config['max_conn'],
            'dispatch_mode' => empty($config['dispatch_mode']) ? 2 : $config['dispatch_mode'],
            'log_file' => empty($config['log_file']) ? '/tmp/swoole.log' : $config['log_file'],
            'daemonize' => empty($config['daemonize']) ? 0 : 1,
            )
        );
        $this->run();
    }

    /*
     *
     * */
    public function run(){
        $this->server->on('Start', array($this->client, 'onStart'));
        $this->server->on('Connect', array($this->client, 'onConnect'));
        $this->server->on('Receive', array($this->client, 'onReceive'));
        $this->server->on('Close', array($this->client, 'onClose'));
        $this->serv->on('Start', array($this->client, 'onStart'));
        $this->serv->on('Connect', array($this->client, 'onConnect'));
        $this->serv->on('Receive', array($this->client, 'onReceive'));
        $this->serv->on('Close', array($this->client, 'onClose'));
        $this->serv->on('Shutdown', array($this->client, 'onShutdown'));
        $handlerArray = array(
            'onTimer',
            'onWorkerStart',
            'onWorkerStop',
            'onTask',
            'onFinish',
            'onWorkerError',
            'onManagerStart',
            'onManagerStop',
            'onPipeMessage'
        );
        foreach($handlerArray as $handler) {
            if(method_exists($this->client, $handler)) {
                $this->serv->on(str_replace('on', '', $handler), array($this->client, $handler));
            }
        }
        $this->serv->start();
    }

    /*
     * 设置server的client
     * @param instance $client
     * @return true
     * */
    public function setClient($client)
    {
        $this->client = $client;
        return true;
    }

    /*
     * 关闭服务
     * */
    function shutdown()
    {
        return $this->server->shutdown();
    }

    /*
     * 回收
     * */
    function close($client_id)
    {
        return $this->server->close($client_id);
    }

    /*
     * 添加监听
     * @param string $host 静听ip或域名
     * @param int $port 监听端口
     * @ty
     * */
    function addListener($host, $port, $type)
    {
        return $this->server->addlistener($host, $port, $type);
    }


    function send($client_id, $data)
    {
        return $this->server->send($client_id, $data);
    }

    public function getConnection()
    {


    }

    public function onStart()
    {

        $params = func_get_args();
        $serv = $params[0];
        echo 'server start, swoole version: ' . SWOOLE_VERSION . PHP_EOL;
        $times = ZConfig::getField('socket', 'times');

        //设置定时器
        if(!empty($times)) {
            foreach ($times as $time) {
                $serv->addtimer($time);
            }
        }
    }


    /*
     * 收到用户发来消息
     * */
    public function onReceive()
    {
        $params = func_get_args();
        $_data = $params[3];
        $serv = $params[0];
        $fd = $params[1];
//        echo "from {$fd}: get data: {$_data}".PHP_EOL;
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
                    $uinfo = $this->getConnection()->get($result[1][0]);
                    if (!empty($uinfo)) {  //已登录过
                        $this->sendOne($serv, $uinfo['fd'], self::RELOGIN, []);
                        $this->getConnection()->delete($uinfo['fd'], $result[1][0]);
                        \swoole_server_close($serv, $uinfo['fd']);
                    }

                    /**
                     * 加入到fd列表中
                     */
                    $this->getConnection()->add($result[1][0], $fd);
                    $this->getConnection()->addFd($fd, $result[1][0]);
                    $this->sendToChannel($serv, self::LOGIN_SUCC, $routeResult);
                } else {       //登录失败
                    $this->sendOne($serv, $fd, self::LOGIN_ERROR, array($routeResult, $result[1][0], $result[1][1]));
                }
                break;
            case self::HB:  //心跳处理
                $uid = $this->getConnection()->getUid($fd);
                $this->getConnection()->uphb($uid);
                return null;
                break;
            case self::CHAT:
                $toId = \intval($result[1][0]);
                $msg = \strip_tags($result[1][1]);
                $uid = $this->getConnection()->getUid($fd);
                if(empty($toId)) {  //公共聊天
                    $this->sendToChannel($serv, self::CHAT, array($uid, $msg, $toId));
                } else { //私聊
                    $toInfo = $this->getConnection()->get($toId);
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
    public function sendToChannel($serv, $cmd, $data, $channel = 'ALL')
    {
        $list = $this->getConnection()->getChannel($channel);
        if (empty($list)) {
//            echo "{$channel} empty==".PHP_EOL;
            return;
        }

        foreach ($list as $fd) {
//            echo "send to {$fd}===".PHP_EOL;
            $this->sendOne($serv, $fd, $cmd, $data);
        }
    }

    /*
     * 单独向用户广播
     * */
    public function sendOne($serv, $fd, $cmd, $data)
    {
        if (empty($serv) || empty($fd) || empty($cmd)) {
            return;
        }
        //echo "send {$fd} cmd: {$cmd}, len:".json_encode($data).PHP_EOL;
        $data = json_encode(array($cmd, $data));
        return \swoole_server_send($serv, $fd, $data);
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


}