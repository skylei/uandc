<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2014/11/16
 * Time: 0:52
 */
namespace src\service\ucenter;
class ucenterService extends \components\BaseService{

    public function commentList($offset, $pagesize){
        $where = '';
        $field = '*';
        $options = array('order'=>array('create_time', 'value'=>'create_time', 'sort'=>'DESC'), 'limit'=>array('offset'=>$offset, 'pagesize'=>$pagesize));
        return  \Ouno\Ouno::dao('comment', 'ucenter')->db->findAll($where, $field, $options);

    }


    public function getUser($username, $password, $remember, $ip){
        $where = "where username = '$username'";
        $res = \Ouno\Ouno::dao('user', 'ucenter')->db->findOne($where);
        $update = array('ip'=> $ip, 'last_login'=>time());
        if($res){
            $user = $this->getPassword($res, $password);
            if($user){
                $res = \Ouno\Ouno::dao('user', 'ucenter')->db->update($update, $where);
                $_SESSION['username'] = serialize($res);
                if($remember){
                    setcookie( 'username', serialize($username) ,  time ()+ 86400);
                }
                return $user;
            }
        }
        return false;
    }

    public function getPassword($user, $password){
        $password = md5($password .$user['pwd_hash']);
        $where = "where username = '{$user['username']}'";
        $where .= " AND password = '$password'";
        $update['ip'] = '';
        $loginUser = \Ouno\Ouno::dao('user', 'ucenter')->db->findOne($where);
        return $loginUser;
    }

    public function checkLoginStatus(){
        $sname = !empty($_SESSION['username']) ? true : false;
        if($sname){
            return $sname;
        }else{
            $cname = isset($_COOKIE['username']) ? unserialize($_COOKIE['username']) : false;
            if($cname){
                $ip = '';
                $where = "where username = '{$cname['username']}'";
                return  \Ouno\Ouno::dao('user', 'ucenter')->db->findOne($where);
            }else{
                return false;
            }
        }
    }

    public function getCateList(){
        $res = \Ouno\Ouno::dao('cate', 'ucenter')->db->findAll('pid = 0', 'DISTINCT cate', 'order by create_time');
        return $res;
    }

    public function addNewCate($data){
        $data['create_time'] = time();
        $data['update'] = date('Y-m-d H:i:s', time());
         return  \Ouno\Ouno::dao('cate', 'ucenter')->db->insert($data);
    }




}