<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/4/7
 * Time: 21:22
 */
namespace components;

use BaseQueue;
class BaseRQ extends BaseQueue{

    /*
    * @var public $config
    * */
    public $config;

    public $redis;

    public function __construct($config){
        $this->redis = new \redis();
        if($config['PCONNECT'])
            $this->redis->pconnect($config['HOST'], $config['PORT']);
        else
            $this->redis->connect($config['HOST'], $config['PORT']);

        $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
    }

    /*
     * 消息生产方法
     * */
    public function pulish($channel, $data){
        return $this->redis->rPush($channel, $data);
    }

    public function customer($channel){
        $msg = $this->redis->lPop($channel);
        $this->execute($msg);
    }


    /*
     * execute
     * */
    public function execute($msg){

    }

    public function afterExecute(){

    }



}