<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/29
 * Time: 14:35
 */
namespace components;
use \Ouno\OFactory as OFactory;

class BaseServer{

    public function run(){
        echo "server run\r\n";
        $sconfig = \Ouno\Ouno::config("SERVER");
        $server = OFactory::getInstance($sconfig['CLASS'], $sconfig['PARAM']);
        $cconfig = \Ouno\Ouno::config("CLIENT");
        $client = OFactory::getInstance($cconfig['CLASS'], $cconfig['PARAM']);
    }



}