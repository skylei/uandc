<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2014/10/29
 * Time: 22:52
 */
namespace components;
class BaseRabbitMQ{



    /*
     * @var $channel
     * */
    public $channel;
    /*
     * @var public 持有链接
     * */
    public $conn;

    public static $_instance = array();


    /*
     * 构造函数，初始化队列及自动加载
     * */
    public function __construct($config){
        define('AMQP_DEBUG', true);
        $this->config = array('HOSPT'=>'127.0.0.1', 'PORT'=>5672, 'USER'=>'guest', 'PASS'=>'guest');
        $this->connection($this->config);
        \Ouno\Ouno::registerAutoloader(__NAMESPACE__ . "\\BaseRabbitMQ::qLoader");

    }

    /*
     * 连接rabbitmq 函数
     * @param array $config
     * @return void
     * */
    public function connection($config){
        $this->conn = new \PhpAmqpLib\Connection\AMQPConnection($config['HOST'], $config['PORT'],
            $config['USERNAME'], $config['PASSWORD'], $config['OPTIONS']);
        $this->channel = $this->conn->channel();
    }

    /*
    * 获得单例
    * */
    public static  function getInstance(){
        if(self::$_instance == null)
            self::$_instance = new self(\Ouno\Ouno::config('MQ'));

        return self::$_instance;
    }

    /*
     * 自动加载ampq相关类
     * */
    static function qloader($className){
        $class =strtolower($className);
        if(strncmp($class, 'phpamqplib', 10) === 0){
            $classFile =  APP_PATH . '/app/extensions/' . $className .'.php';
            if(!isset(self::$instance[$className])){
                include($classFile);
                self::$instance[$className] = $className;
            }
        }
    }

    /*
     * @param array $param
     * @param string $queue
     * @param string $exchange
     * */
    public function publish($param, $queue, $exchange){
        $param = json_encode($param);
        $queue = strtolower($queue);
        /*
            The following code is the same both in the consumer and the producer.
            In this way we are sure we always have a queue to consume from and an
                exchange where to publish messages.
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $this->channel->queue_declare($queue, false, true, false, false);

        /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */
        $this->channel->exchange_declare($exchange, 'direct', false, true, false);

        $this->channel->queue_bind($queue, $exchange);
        /*
        publish param $msg(AMQPMessage instance) ,
        param $exchange mq
        excahnge string
        param $routing_key for upon is $consumer_tag ,
        param $mandatory 是否强制性
        */
        $msg = new \PhpAmqpLib\Message\AMQPMessage($param, array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $this->channel->basic_publish($msg, $exchange);

    }


    public function consumer($queue, $exchange = null, $no_ack = null){
        $queue = strtolower($queue);
        $exchange = $exchange ===null ? $queue : $exchange;
        $consumer_tag = $queue;
        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $this->channel->queue_declare($queue, false, true, false, false);

        /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */

        $this->channel->exchange_declare($exchange, 'direct', false, true, false);

        $this->channel->queue_bind($queue, $exchange);
        /*
            queue: Queue from where to get the messages
            consumer_tag: Consumer identifier
            no_local: Don't receive messages published by this consumer.
            no_ack: Tells the server if the consumer will acknowledge the messages.
            exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
            nowait:
            callback: A PHP Callback
        */
        $this->channel->basic_consume($queue, $consumer_tag, false, false, false, false, array($this, 'execute'));
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }

    }

    public function execute($msg){
        var_dump($msg);
        echo "\n----+++----\n";
        echo $msg->body;
        echo "\n----+++----\n";
        //确认答应
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        // Send a message with the string "quit" to cancel the consumer.
        if ($msg->body === 'quit') {
            $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
        }

    }

    public function afterExecute(){

    }


    /**
     * 正确处理完成
     */
    public function finish($msg){
        return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    public function quit($msg){
        return $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
    }



}
