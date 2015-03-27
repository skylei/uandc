<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/27
 * Time: 22:07
 */
namespace components;

use Ouno\Cache\Redis;

class BaseRedisDao{

    public static $instances;

    private static $configs;

    public $db;

    public function __construct($config){
        $this->db = new \Ouno\Cache\Redis($config);
        $this->db->select($config['DB']);

    }

    public static function getInstance($config){
        if(!(self::$instances[$config['DB']] instanceof self))
            self::$instances[$config['DB']] = new self($config);

        return self::$instances[$config['DB']];
    }

    public function set($key, $value, $timeOut = ''){
        return $this->db->set($key, $value, $timeOut);
    }

    public function get($key){
        return $this->db->get($key);
    }

    /*
     * 删除key，可以是一个，或者多个(数组形式传参)
     * @param mixed $keys
     * */
    public function delete($keys){
        $this->db->delete($keys);
    }

    public function hash_add($hash, $key, $value){
        return $this->db->hSet($hash, $key, $value);
    }


    public function hash_get($hash, $key){
        return $this->db->hGet($hash, $key);
    }

    public function hash_delete($hash, $key){
        return $this->db->hDel($hash, $key);

    }

    public function hash_get_all($hash){
        return $this->db->hGetAll($hash);
    }

    public function hash_count($hash){
        return $this->db->hLen($hash);
    }

    public function hash_keys($hash){
        return $this->db->hKeys($hash);
    }


}