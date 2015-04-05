<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/4/4
 * Time: 10:22
 */
namespace src\service\redis;

use components\BaseTools;
use src\dao\redis\redisDao as redisDao;
class indexService{


    public function checkLoginUser($uid, $token){

        $check = BaseTools::getToken($uid);
        if($check !== $token)
            return false;


    }


    public function getRedisDao(){
        return new redisDao();
    }

    public function getImUserDao(){
        return \Ouno\Ouno::dao("redis", "imUser");
    }
}