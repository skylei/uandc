<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/26
 * Time: 22:17
 */
namespace Ouno\Cache;

class Oredis{
    /*
     * @var $instance  持有redis实例
     * */
    public $redis;

    /*
     * @var $config static redis配置
     * */
    private static $config;

    public function __construct($config)
    {
        $redis = new \redis();
        if (isset($config['PCONNECT'])) {
            $redis->pconnect($config['HOST'], $config['PORT'], $config['TIMEOUT']);
        } else {
            $redis->connect($config['HOST'], $config['PORT'], $config['TIMEOUT']);
        }

        $redis->setOption(\redis::OPT_SERIALIZER, \redis::SERIALIZER_NONE);
	$redis->select(1);
        $this->redis = $redis;
        self::$config = $config;
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

    //@todo
    public function close(){
        return $this->redis->close();
    }
    
    public function selectdb($db){
	$this->redis->select($db);
    }		
	
}
