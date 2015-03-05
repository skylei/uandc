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

    /*
     * 获取所有相册
     * @return array | null
     * */
    public function getAlbum(){
        return $this->getDao('album')->getAll();
    }

    /*
     * 通过相册Id获取1个相册
     * @param
     * @return array | null
     * */
    public function getAlbumById($_id){
        $_id = new \mongoId($_id);
        return $this->getDao('album')->getOne(array('_id'=>$_id));
    }

    /*
     * 创建默认相册
     * */
    public function createDefaultAlbum(){
        $data = array(
            "name"=>"default",
            "title"=>"默认",
            "create_time"=>time(),
            "update_time"=>time(),
            "logo"=>"/app/static/images/album/default/logo.png"
        );
        $dao = $this->getDao('album');
        $result = $dao->getOne(array('name'=>'default'));
        if(empty($result))
            return $dao->Ndb->insert($data);
        else
            return $result;

    }
}
