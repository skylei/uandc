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
     * 构造函数，初始化队列及自动加载
     * */
    public function __construct(){
        define('AMQP_DEBUG', true);
        $this->config = array('HOSPT'=>'127.0.0.1', 'PORT'=>5672, 'USER'=>'guest', 'PASS'=>'guest');
        \Ouno\Ouno::registerAutoloader('this', 'mqLoader');

    }

    public function run(){
        $conn = new \PhpAmqpLib\Connection\AMQPConnection('127.0.0.1', '5672', 'guest', 'guest', '/');
        $exchange = 'router';
        $queue = 'msgs';
        $consumer_tag = 'consumer';
        $ch = $conn->channel();
        $ch->queue_declare($queue, false, true, false, false);
        $ch->exchange_declare($exchange, 'direct', false, true, false);
        $ch->queue_bind($queue, $exchange);
        $msg_body = 'nihao 234 asdfa adfasd 23 quit';
        $msg = new \PhpAmqpLib\Message\AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
        //$ch->basic_publish($msg, $exchange);
        $msg = $ch->basic_get($queue);

        $ch->basic_ack('consumer');

        var_dump($msg->body);
        // Loop as long as the channel has callbacks registered
//        while (count($ch->callbacks)) {
//            $ch->wait(10);
//        }
    }

    /*
     * 自动加载ampq相关类
     * */
    public function mqLoader($className){
        $class =strtolower($className);
        if(strncmp($class, 'phpamqplib', 10) === 0){
            $classFile = '/extensions/' . $className .'.php';
            \Ouno\Ouno::import($classFile);
        }
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