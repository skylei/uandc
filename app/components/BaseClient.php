<?php
/**
 * swoole客户端的基类
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/21
 * Time: 14:12
 */
namespace components;
class BaseClient{

    public function __construct(){
        echo "this is BaseClient";
    }

    public function onStart(){
	echo "this is client start";
    }	

    public function OnConnect(){
	echo "this is onnect";
    }
    
    public function onReceive()
    {
	$params = func_get_args();
	var_dump($params);
    }
    
    public function onClose()
    {	   
	$params = func_get_args();
	var_dump($params);
     }  	

    public function onWorkerStart()
    {
        $params = func_get_args();
        $worker_id = $params[1];
        echo "WorkerStart[$worker_id]|pid=" . posix_getpid() . ".\n";
    }

    public function onWorkerStop()
    {
        $params = func_get_args();
        $worker_id = $params[1];
        echo "WorkerStop[$worker_id]|pid=" . posix_getpid() . ".\n";
    }

    public function onTask()
    {

    }

    public function onFinish()
    {
        
    }

    public function onShutdown(){
	$params = func_get_args();
	var_dump($params);	
    }
	




}
