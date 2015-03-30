<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/27
 * Time: 22:58
 */
namespace src\dao\redis;

use components\BaseRedisDao;
class redisDao extends BaseRedisDao{

    public function bindChanel($channel, $key, $value){
        $this->hash_add($channel, $key, $value);
    }

    public function deletFromChannel($channel, $key){
        $this->hash_delete($channel, $key);
    }

    public function getChannelCountr($channel){
        return $this->hash_count($channel);
    }

    public function getChannelMember($channel, $key){
        return $this->hash_get($channel, $key);
    }

    public function bindUidToFd($fd, $uid, $time = 0){
        $this->set($fd, $uid, $time);
    }


    public function bindFdToUid($uid, $fd, $time = 0)
    {
        $this->set($uid, $fd, $time);
    }

    public function getUidByFd($fd){
        return $this->get($fd);
    }

    public function getFdByUid($uid){
        return $this->get($uid);
    }

    public function removeUser($uid, $fd){
        $this->delete(array($uid, $fd));
    }


}