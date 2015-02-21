<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015/2/21
 * Time: 16:14
 */
namespace src\service\ucenter;
class mongoService{

    /*
     * 获得获得dao
     * @return object
     * */
    public function getDao($daoName){
        return  \Ouno\Ouno::dao($daoName, 'mongo');
    }


    public function getOne(){
        return $this->getDao('image')->getNew();
    }

}