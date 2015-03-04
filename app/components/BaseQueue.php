<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015/2/23
 * Time: 19:25
 */
namespace components;
class BaseQueue{

    /*
     * @var public $config
     * */
    public $config;

    /*
     * @var public 持有链接
     * */
    public $conn;

    public static $instance = array();


    /*
     * 构造函数，初始化队列及自动加载
     * */
    public function __construct(){
        define('AMQP_DEBUG', true);
        $this->config = array('HOSPT'=>'127.0.0.1', 'PORT'=>5672, 'USER'=>'guest', 'PASS'=>'guest');
        \Ouno\Ouno::registerAutoloader('this', 'qLoader');

    }


    /*
     * 自动加载ampq相关类
     * */
    function qloader($className){
        $class =strtolower($className);
        if(strncmp($class, 'phpamqplib', 10) === 0){
            $classFile =  PATH . '/app/extensions/' . $className .'.php';
            if(!isset(queue::$instance[$className])){
                include($classFile);
                queue::$instance[$className] = $className;
            }
        }
    }

    public function publish(){
        $queue = 'msg';
        $consumer_tag = 'crab';
        $exchange = 'router';
        $ch = $this->conn->channel();

        /*
            The following code is the same both in the consumer and the producer.
            In this way we are sure we always have a queue to consume from and an
                exchange where to publish messages.
        */

        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $ch->queue_declare($queue, false, true, false, false);

        /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */

        $ch->exchange_declare($exchange, 'direct', false, true, false);

        $ch->queue_bind($queue, $exchange);
        $argv = func_get_args();
        $msg_body = implode(' ', array_slice($argv, 1));
        //publish param $msg(AMQPMessage instance) ,param $exchange mq excahnge string param $routing_key for upon is $consumer_tag , param $mandatory 是否强制性
        $msg = new \PhpAmqpLib\Message\AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $ch->basic_publish($msg, $exchange);

    }



    public function process_message($msg)
    {
        echo "<br>n--------\n";
        echo $msg->body;
        echo "<br>\n--------\n<br>";

        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        // Send a message with the string "quit" to cancel the consumer.
        if ($msg->body === 'quit') {
            $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
        }
    }




    function shutdown($ch, $conn)
    {
        $ch->close();
        $conn->close();
    }



}