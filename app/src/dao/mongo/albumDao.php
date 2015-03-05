<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2014/11/16
 * Time: 0:52
 */
namespace src\dao\mongo;
class albumDao extends \components\BaseMongoDao{
	public $collection = 'album';

    /*
     * 获取最新的图片
     * @param array $query
     * @param array $sort
     * @param int $skip
     * @param int $limit
     * @param array $fields
     * @return array | false
     * */
 	public function getAlbum($query = array(), $sort = array('create_time'=>-1), $skip = 0, $limit = 10, $fields = array()){
		return $this->Ndb->findAll($query, $sort, $skip, $limit, $fields);
	}
	
	public function getHot($query, $sort = array('clicknum'=>1), $skip = 0, $limit = 10, $fields = array()){
		$query = array('time'=>array('$lt'=>time()));
		return $this->Ndb->findAll($query, $sort = array(), $skip, $limit, $fields);
	}
	
	public function save($data){
		return $this->getGridFS()->storeFile($data['file'], $data['info']);
	}
	
	public function getOne($query){
        return $this->Ndb->findOne($query);
	}

    public function getAll($query = array(),$sort = array('create_time'=>-1) ){
        return $this->Ndb->findAll($query, $sort);
    }
	
	public function getBinaryData($mid){
		
	
	}
	
	/*
	 * 获得分组
	 * @param $key string
	 * @param $initial string
	 * @param $reduce
	 * */
	public function group($key='', $initial ='', $reduce ='', $options = array()) {//通过
		
		return $this->Ndb->group($key, $initial, $reduce, $options);
		
	} 
	
	public function findAllAndModify($query = array(), $data, $options = array()){//通过
		return $this->Ndb->findAllAndModify($query, $data, $options);
	}
	
	//按key去重
	public function distinct($key, $query=array()){
		$result = $this->Ndb->distinct($key, $query);
		return $result;
	}
	











}
?>