<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/4/12
 * Time: 13:06
 */
namespace web\rest\api;
class indexController{

    public function getAction(){
        echo "get one action";
    }

    public function listAction(){
        echo "get list action";
    }

    public function addAction(){
        echo "this is post";
    }

    public function updateAction(){
        var_dump(\Ouno\Ouno::getInstance()->request->getRestParams());
        echo "this is put";
    }
}