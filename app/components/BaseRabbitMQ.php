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
        \Ouno\Ouno::registerAutoloader(__NAMESPACE__ . "\\BaseRabbitMQ::qLoader");
        $this->config = $config;
        $this->connection($this->config);

    }

    /*
     * 连接rabbitmq 函数
     * @param array $config
     * @return void
     * */
    public function connection($config){
        $this->conn = new \PhpAmqpLib\Connection\AMQPConnection($config['HOST'], $config['PORT'],
            $config['USER'], $config['PASSWORD'], $config['VHOST']);
        $this->channel = $this->conn->channel();
    }

    /*
    * 获得单例
    * */
       /* public static  function getInstance($config){
            if(self::$_instance == null)
                self::$_instance = new self(\Ouno\Ouno::config('MQ'));

            return self::$_instance;
        }*/

    /*
     * 自动加载ampq相关类
     * */
    public static function qloader($className){
        $class =strtolower($className);
        echo $class  . "\r\n";
        if(strncmp($class, 'phpamqplib', 10) === 0){
            $classFile =  \Ouno\Ouno::$APP_PATH . '/extensions/' . $className .'.php';
            if(!isset(self::$_instance[$className])){
                include($classFile);
                self::$_instance[$className] = $className;
            }
        }
    }

    /*
     * @param array $data
     * @param string $queue
     * @param string $exchange
     * */
    public function publish($queue, $exchange, $data){
        $data = json_encode($data);
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
        $msg = new \PhpAmqpLib\Message\AMQPMessage($data, array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $this->channel->basic_publish($msg, $exchange);

    }


    /*
     * 消息出口
     * @param string $queue 队列名
     * @param string $exchang 交换机名
     * @param boolean $no_ack 确保message被consumer“成功”处理了。这里“成功”的意思是（在设置 no_ack=false的情况下只要consumer手动应答 Basic.Ack就算其“成功”处理了。
     * */
    public function consumer($queue, $exchange = null, $no_ack = null){
        echo $queue . "\r\n";
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
        //声明队列
        $this->channel->queue_declare($queue, false, true, false, false);

        /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts //是否持久化
            auto_delete: false //the exchange won't be deleted once the channel is closed. 频道关闭后是否自动删除队列
        */
        //声明交换机
        $this->channel->exchange_declare($exchange, 'direct', false, true, false);
        //绑定交换机和队列
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
        //队列元素出列，最后是回调函数
        $this->channel->basic_consume($queue, $consumer_tag, false, false, false, false, array($this, 'execute'));
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }

    }

    /*
     * 出队列时消息回调
     * @param object $message
     * */
    public function execute($msg){
        var_dump($msg);
        echo "\n----+++----\n";
        echo $msg->body;
        echo "\n----+++----\n";
        //确认答应
        $this->finish($msg);

        // 发送消息的字符串“quit” 告知 消费者 取消出队列.
        if ($msg->body === 'quit') {
            $this->quit($msg);
        }

    }

    public function afterExecute(){

    }


    /**
     * 正确处理完成
     * @param object $msg
     */
    public function finish($msg){
        return $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    public function quit($msg){
        return $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
    }

    public function close(){
        $this->conn->close();
        $this->channel->close();
    }

}
