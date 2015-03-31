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

    /*
     * 绑定用户到fd上，由于fd是整数，不能直接设为key
     * @param int $fd
     * @param int uid
     * @param int time 过期时间
     * */
    public function bindUidToFd($fd, $uid, $time = 0){
        $this->set($this->getFdKey($fd), $uid, $time);
    }


    public function bindFdToUid($uid, $fd, $time = 0)
    {
        $this->set($uid, $this->getFdKey($fd), $time);
    }

    public function getUidByFd($fd){
        return $this->get($this->getFdKey($fd));
    }

    /*
     * 给连接的fd绑定0用户
     * @param int $fd
     * @param int $uid
     * */
    public function addFd($fd, $uid = 0){
        $this->set($this->getFdKey($fd), $uid);
    }

    public function getFdByUid($uid){
        return $this->get($uid);
    }

    public function removeUser($uid, $fd){
        $this->delete(array($uid, $this->getFdKey($fd)));
    }

    public function getFdKey($fd, $prefix = "swoolefd_"){
        return $prefix . $fd;
    }


}