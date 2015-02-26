<?php
namespace PhpAmqpLib;
define('PATH', dirname(__DIR__));

class queue{
	public $config =  array();
	public $conn;
	public static $instance = array();
	
	public function __construct(){
		spl_autoload_register(array('PhpAmqpLib\queue', 'qloader'));
		
		// $exchange = 'router';
		// $queue = 'msgs';
		$this->conn = new \PhpAmqpLib\Connection\AMQPConnection('127.0.0.1', '5672', 'guest', 'guest', '/');
		
		// $ch = $conn->channel();
		// /*
			// The following code is the same both in the consumer and the producer.
			// In this way we are sure we always have a queue to consume from and an
				// exchange where to publish messages.
		// */

		// /*
			// name: $queue
			// passive: false
			// durable: true // the queue will survive server restarts
			// exclusive: false // the queue can be accessed in other channels
			// auto_delete: false //the queue won't be deleted once the channel is closed.
		// */
		// $ch->queue_declare($queue, false, true, false, false);

		// /*
			// name: $exchange
			// type: direct
			// passive: false
			// durable: true // the exchange will survive server restarts
			// auto_delete: false //the exchange won't be deleted once the channel is closed.
		// */

		// $ch->exchange_declare($exchange, 'direct', false, true, false);

		// $ch->queue_bind($queue, $exchange);

		// $toSend = new \PhpAmqpLib\Message\AMQPMessage('test message', array('content_type' => 'text/plain', 'delivery_mode' => 2));
		// $ch->basic_publish($toSend, $exchange);

		// $msg = $ch->basic_get($queue);
		// var_dump($msg);
		// $ch->basic_ack($msg->delivery_info['delivery_tag']);
		// var_dump($msg->body);
		// $ch->close();
		// $conn->close();
	}
	
	function qloader($className){
		$class =strtolower($className);
		if(strncmp($class, 'phpamqplib', 10) === 0){
			$classFile =  PATH . '/' . $className .'.php';
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
	
	
	public function customer(){
		echo "this is customer";
		$ch = $this->conn->channel();
		$queue = 'msg';
		$consumer_tag = 'crab';
		$exchange = 'router';

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
		
		/*
			queue: Queue from where to get the messages
			consumer_tag: Consumer identifier
			no_local: Don't receive messages published by this consumer.
			no_ack: Tells the server if the consumer will acknowledge the messages.
			exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
			nowait:
			callback: A PHP Callback
		*/
		
		
		$ch->basic_consume($queue, $consumer_tag, false, false, false, false, array($this, 'process_message'));
		var_dump(count($ch->callbacks));
		while(count($ch->callbacks)) {
			$ch->wait();
		}
		
		
		
	
	}
	
	public function shutdown($ch){
		$this->conn->close();
		$ch->close();
	}
	

	
	/**
	 * @param \PhpAmqpLib\Message\AMQPMessage $msg
	 */
	public function process_message($msg)
	{	
		var_dump($msg);
		echo "\n----+++----\n";
		echo $msg->body;
		echo "\n----+++----\n";

		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

		// Send a message with the string "quit" to cancel the consumer.
		if ($msg->body === 'quit') {
			$msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
		}
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


$q = new \PhpAmqpLib\queue();
$rand = rand(0,100);

$q->publish($rand);
// $q->customer();

?>