<?php
/**
 * httpserver 功能来源于swoole_sever区别fpm和apache运行在cli
 * swoole 一般会开启 一个master主进程，管理rector
 * fork一个manager进程
 * 然后根据设置worker_num 产生若干个worker
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/22
 * Time: 14:53
 */
//namespace bin;

use Ouno\Ouno;
class httpServer{

    private static $instance;
    public static $server;
    public static $request;
    public static $response;
    public static $wsfarme;
    public static $httpServer;
    public static $config = [];
    public static $cofnig_name= 'default';
    private $mimes = [];
    private $logger;
    private $webRoot;
    private $staticFile;

    public function __construct($run_path, $conf_name){
        $this->mimes = include('./mimes.php');
	$this->webRoot = $run_path;
	$config = include($run_path . DIRECTORY_SEPARATOR . "config/" . $conf_name . ".php");
        $config['IP'] = isset($config['IP']) ? $config['IP'] : '0.0.0.0';
        $config['PORT'] = isset($config['PORT']) ? $config['PORT'] : '9501';
        $config['WORK_NUM'] = isset($config['WORK_NUM']) ? $config['WORK_NUM'] : 4;
        $config['DEAMONIZE'] = isset($config['DAEMONIZE']) ? $config['DAEMONIZE'] : 1;
        $config['MAX_REQUEST'] = isset($config['MAX_REQUEST']) ? $config['MAX_REQUEST'] : 1000;
	$set = array(
		"work_num"=>$config['WORK_NUM'],
		"deamonize"=>$config['DEAMONIZE'],
		"max_request"=>$config['MAX_REQUEST'],	
	);
	self::$config;
        //swoole_websocket_server继承自swoole_http_server继承自swoole_server
        $http = new swoole_websocket_server($config['IP'], $config['PORT']);
        $http->set($set);
       // 服务器会自动将请求的GET、POST、COOKIE等数据设置到PHP的$_GET/$_POST/$_COOKIE等超全局变量中。
        $http->setGlobal(HTTP_GLOBAL_ALL, HTTP_GLOBAL_GET|HTTP_GLOBAL_POST);
        //每个注册进程开启回调(主要逻辑桥接)
        $http->on('WorkerStart', array($this, 'onWorkerStart'));
        $http->on('WorkerError', array($this, 'onWorkerError'));
        $http->on('WorkerStop', array($this, 'onWorkerStop'));
        $http->on('close', array($this, 'onClose'));
        $http->on('open',array($this, 'onOpen'));
        $http->on('message', function (swoole_websocket_server $server, $fd, $data, $opcode, $fin){
            echo "receive from {$fd}:{$data},opcode:{$opcode},fin:{$fin}\n";
            $server->push($fd, "this is websock server");
        });

        //处理请求
        $http->on('request', function ($request, $response) {
            httpServer::$request = $request;
            httpServer::$response = $response;
            $_SERVER['PATH_INFO'] = $request->server['path_info'];
            if ($_SERVER['PATH_INFO'] == '/') {
                if (!empty($this->defaultFiles)) {
                    foreach ($this->defaultFiles as $file) {
                        $staticFile = $this->getStaticFile(DIRECTORY_SEPARATOR . $file);
                        if (is_file($staticFile)) {
                            $response->end(file_get_contents($staticFile));
                            return;
                        }
                    }
                }
            }
		
            if($_SERVER['PATH_INFO'] == '/favicon.ico') {
                $response->header('Content-Type', $this->mimes['ico']);
                $response->end('');
                return;
            }

            $staticFile = $this->getStaticFile($_SERVER['PATH_INFO']);

            if (is_dir($staticFile)) { //是目录
                foreach ($this->defaultFiles as $file) {
                    if (is_file($staticFile . $file)) {
                        $response->header('Content-Type', 'text/html');
                        $response->end(file_get_contents($staticFile . $file));
                        return;
                    }
                }
            }

            $ext = pathinfo($_SERVER['PATH_INFO'], PATHINFO_EXTENSION);

            if (isset($this->mimes[$ext])) {  //非法的扩展名
                if (is_file($staticFile)) { //读取静态文件
                    $response->header('Content-Type', $this->mimes[$ext]);
                    $response->end(file_get_contents($staticFile));
                    return;
                } else {
                    $response->status(404);
                    $response->end('');
                    return;
                }
            }

		
            try{
                ob_start();
                $result = $this->Ouno->run($this->webRoot,'swoole');
                if (null == $result) {
                    $result = ob_get_contents();
                }
                ob_end_clean();
            }catch (Exception $e){
		$result = $e->getMessage();
		$response->end($result);
		//$result = $e->getTrace();	
		//$result = json_encode($e->getTrace());
		//$result = $e->getTrace();
		//var_dump($result);
            }
            $response->status(200);
            $response->end($result);
        });

        self::$httpServer = $http;
        self::$httpServer->start();
    }

    /*
     * 获取实例
     * */
    public static function getInstance($run_path, $conf_name)
    {
        if (!self::$instance) {
            self::$instance = new HttpServer($run_path, $conf_name);
        }
        return self::$instance;
    }

    /*
     * 回调的参数
     * */
    public function onClose($server, $client_id, $from_id){
        echo $from_id . "close";
    }
	
	public function onWorkerError(){
	
	}
	
	public function onWorkerStop(){
	
	}
	
	public function onOpen(){
	
	}

    /*
     * 开启worker
     * */
    public function onWorkerStart()
    {
        //这里require Ouno框架目录地址
        opcache_reset();
        require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Ouno' . DIRECTORY_SEPARATOR . 'Ouno.php';
        ///home/wwwroot/www.Ouno.com, 是应用的地址
        $this->Ouno = Ouno::getInstance();
        $params = func_get_args();
        echo "worker {$params[1]} start".PHP_EOL;
        $this->mimes = require 'mimes.php';
    }

    private function getStaticFile($file, $path = 'webroot')
    {
        return $this->webRoot . DIRECTORY_SEPARATOR . $path . $file;
    }



}
$config = array();
$runpath = dirname(__DIR__) . DIRECTORY_SEPARATOR . "app";  
$confname = "swoole";
$server = httpServer::getInstance($runpath, $confname);
