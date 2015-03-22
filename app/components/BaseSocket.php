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
        $handlerArray = array(
            'onTimer',
            'onWorkerStart',
            'onWorkerStop',
            'onTask',
            'onFinish',
        );
        foreach($handlerArray as $handler) {
            if(method_exists($this->client, $handler)) {
                $this->server->on(\str_replace('on', '', $handler), array($this->client, $handler));
            }
        }
        $this->server->start();
    }


    function shutdown()
    {
        return $this->server->shutdown();
    }
    function close($client_id)
    {
        return $this->server->close($client_id);
    }
    function addListener($host, $port, $type)
    {
        return $this->server->addlistener($host, $port, $type);
    }
    function send($client_id, $data)
    {
        return $this->server->send($client_id, $data);
    }


}