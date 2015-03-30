<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015/3/9
 * Time: 21:16
 */
namespace command\index;
use \src\dao\redis\redisDao as rdao,
    \components\BaseController;


class indexController extends BaseController {

    public function indexAction(){
        //$mq = new \components\BaseRabbitMQ(array());
        //var_dump($mq);
	echo "this is test echo ";
	$config = array(
	    "DB"=>1,	
	    "HOST"=>"127.0.0.0",
	    "PORT"=>"6379",
	    "TIMEOUT"=>"5",	
	);
	//$redis = new \redis();
	//$redis->connect("127.0.0.1", "6379");
	//$redis->select(2);
	return
	$rdao = new rdao($config); 
	$rdao->set("k1", "11");	
	echo $rdao->get('k1');
    }
}
