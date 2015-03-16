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
        $query = array();
        $sort = array("create_time"=>-1);
        $skip = 0;
        $limit = 20;
        return $this->getDao('image', 'mongo')->getNew($query, $sort, $skip, $limit);
    }
    /*
     * 查询单个
     * */
    public function getOne($query){
        return $this->getDao('image', 'mongo')->getOne($query);
    }

    /*
     * 获取文件相关的chunk内容
     * */
    public function getOneChunk($query){
        return $this->getDao('imageChunk', 'mongo')->getOne($query);
    }

    /*
     * 获取所有相册
     * */
    public function getAllAlbums(){
        return $this->getDao('album', 'mongo')->getAll();
    }

    /*
     * 获取相册相关图片
     * @param string $name
     * @paramt int   $limit
     * @return array | null
     * */
    public function getAlbumImages($name, $limit){
        $query = array("album"=>$name);
        $sort = array('create_time'=> -1);
        $skip = 0;
        return $this->getDao('image', 'mongo')->getAll($query, $sort, $skip, $limit);
    }

    /*
     * 获取相关评论
     * @param array $query
     * @return array | false
     * */
    public function getComments($query){
        return $this->getDao('image', 'mongo')->getAll($query);
    }


}