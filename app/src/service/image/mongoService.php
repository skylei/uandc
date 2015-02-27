<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015/2/21
 * Time: 16:14
 */
namespace src\service\image;
class mongoService{

    /*
     * 获得获得dao
     * @return object
     * */
    public function getDao($daoName){
        return  \Ouno\Ouno::dao($daoName, 'mongo');
    }


    public function getNew(){
        return $this->getDao('image', 'mongo')->getNew();
    }

    public function getOne($query){
        return $this->getDao('image', 'mongo')->getOne($query);
    }

    public function getOneChunk($query){
        return $this->getDao('imageChunk', 'mongo')->getOne($query);
    }
}