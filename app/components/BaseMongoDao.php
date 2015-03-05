<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2014/10/29
 * Time: 22:52
 */
namespace components;
class BaseMongoDao {
    public  $collection = '';
    public $Ndb;

    public function __construct(){
        $this->Ndb =  \Ouno\Core\Db\OunoMongo::getInstance(\Ouno\Ouno::config('MONGO'));
        $this->Ndb->collection($this->collection);
    }

    /*
     * 获取mongo文件对象
     * */
    public function getGridFS($prefix = 'crab_img'){
        return $this->Ndb->db->getGridFS($prefix);
    }

    /*
     *
     * @return object 文档集合对象
     * */
    public function getCollection(){
        return \Ouno\Ouno::dao('user', 'ucenter')->Ndb->collection($this->collection);
    }

    public function getAll($query = array()){
        return $this->Ndb->findAll($query);
    }

}