<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/29
 * Time: 14:35
 */
namespace components;
use Ouno\OFactory as OFactior;
use Ouno\OFactory;

class BaseServer{

    public function run($config){
        $sconfig = \Ouno\Ouno::config("SERVER");
        $server = OFactory::getInstance($sconfig['CLASS'], $sconfig['PARAM']);
        $cconfig = \Ouno\Ouno::config("CLIENT");
        $client = OFactory::getInstance($cconfig['CLASS'], $cconfig['PARAM']);
    }



}