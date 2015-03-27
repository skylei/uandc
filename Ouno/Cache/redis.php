<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/26
 * Time: 22:17
 */
namespace Ouno\Cache;
class Redis{
    /*
     * @var $instance  持有redis实例
     * */
    private $redis;

    /*
     * @var $config static redis配置
     * */
    private $config;

    public function __construct($config)
    {
        $redis = new \Redis();
        if ($config['PCONNECT']) {
            $redis->pconnect($config['HOST'], $config['PROT'], $config['TIMEOUT']);
        } else {
            $redis->connect($config['HOST'], $config['PROT'], $config['TIMEOUT']);
        }
        $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
        $this->reids = $redis;
        self::$config = $config;
        return  $this->reids;
    }


    /**
     * 设置值
     * @param string $key 键名
     * @param string $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function set($key, $value, $timeOut = 0) {
        $result = $this->redis->set($key, $value);
        if ($timeOut > 0) $this->redis->EXPIRE($key, $timeOut);
        return $result;
    }

    /*
     * 同时设置多个值
     * @param array $data
     * */
    public function mult_set(array $data){

    }

    public function append($key, $value){
        $this->redis->APPEND($key, $value);
    }

    public function get($key){
        return $this->redis->get($key);
    }

    /*
     *
     * */
    public function mult_get($keyArr)
    {

    }


    /*
    * 删除key，可以是一个，或者多个(数组形式传参)
    * @param mixed $keys
    * */
    public function delete($keys){
        $this->redis->delete($keys);
    }

    public function close(){

    }

}