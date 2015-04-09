<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015/3/9
 * Time: 21:16
 */
namespace command\index;
use PhpAmqpLib\Exception\AMQPException as AMQPExcepton;
use \src\dao\redis\redisDao as rdao,
    \components\BaseController;


class indexController extends BaseController {

    public function indexAction()
    {
        $config =  array('HOST'=>'127.0.0.1', 'PORT'=> 5672, 'USER'=>'guest', 'PASSWORD'=>'guest', 'VHOST'=>'/');
        try{
            $mq = new \components\BaseRabbitMQ($config);
//            $mq->publish('crab', 'crab_exchange', array("hellow"=>'crab'));
            $mq->consumer('crab', 'crab_exchange');
        }catch(\Exception $e){
            echo $e->getFile() . "\r\n";
            echo $e->getLine() ."\r\n";
            echo $e->getMessage() . "\r\n";
        }



    }

    public function publishAction(){
        $config =  array('HOST'=>'127.0.0.1', 'PORT'=> 5672, 'USER'=>'guest', 'PASSWORD'=>'guest', 'VHOST'=>'/');
        try{
            $mq = new \components\BaseRabbitMQ($config);
            $mq->publish($queue = 'crab', $exchange = 'crab_exchange', array("hellow"=>'crab' . mt_rand(0,10000)));
        }catch(\Exception $e){
            echo $e->getFile() . "\r\n";
            echo $e->getLine() ."\r\n";
            echo $e->getMessage() . "\r\n";
        }

    }
}
