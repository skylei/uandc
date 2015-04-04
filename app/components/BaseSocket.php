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
    public function __construct($config){

        $config = array("HOST"=> '0.0.0.0', 'PORT'=>8888);
        
        $this->server = new \swoole_websocket_server($config['HOST'], $config['PORT'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $this->server->set(array(
            'reactor_num' => empty($config['reactor_num']) ? 4 : $config['reactor_num'], //reactor thread num
            'worker_num' => empty($config['worker_num']) ? 4 : $config['worker_num'], //worker process num
            //'task_worker_num' => empty($config['task_worker_num']) ? 2 : $config['task_worker_num'], //task worker process num
            //'backlog' => empty($config['backlog']) ? 128 : $config['backlog'], //listen backlog));
            'max_request' => empty($config['max_request']) ? 100 : $config['max_request'],
            'max_conn' => empty($config['max_conn']) ? 1000 : $config['max_conn'],
            'dispatch_mode' => empty($config['dispatch_mode']) ? 2 : $config['dispatch_mode'],
            
            )
        );
	$this->config = $config;
    }

    /*
     *
     * */
    public function run(){
        $this->server->on('Start', array($this->client, 'onStart'));
        $this->server->on('Connect', array($this->client, 'onConnect'));
        $this->server->on('Receive', array($this->client, 'onReceive'));
        $this->server->on('Close', array($this->client, 'onClose'));
        $this->server->on('Shutdown', array($this->client, 'onShutdown'));
	$this->server->on("task" , array($this->client, "onTask"));
	$this->server->on("finish", array($this->client, "onFinish"));
	$this->server->on("message", array($this->client, "onMessage"));
	$this->server->on("open", array($this->client, "onOpen"));
	//$this->server->on("WorkerError", array($this->client, "onWorkerError"));

        $handlerArray = array(
            'onTimer',
            'onWorkerStart',
            'onWorkerStop',
            'onTask',
            'onFinish',
            'onWorkerError',
           // 'onManagerStart',
           // 'onManagerStop',
           // 'onPipeMessage'
        );
	/*
        foreach($handlerArray as $handler) {
            if(method_exists($this->client, $handler)) {
                $this->server->on(str_replace('on', '', $handler), array($this->client, $handler));
            }
        }
	*/
	
        $this->server->start();
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
     * @param string $HOST 静听ip或域名
     * @param int $PORT 监听端口
     * @ty
     * */
    function addListener($HOST, $PORT, $type)
    {
        return $this->server->addlistener($HOST, $PORT, $type);
    }











}
