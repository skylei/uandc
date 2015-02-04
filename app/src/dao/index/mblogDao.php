<?php
namespace src\dao\index;
class mblogDao extends BaseDao{
	public $collection = 'mblog';
	
 	public function getNew($query, $sort = array('time'=>-1), $skip = 0, $limit = 10, $fields = array()){
		$query = array('time'=>array('$lt'=>time()));
		return $this->Ndb->findAll($query, $sort, $skip, $limit, $fields);
	}
	
	/*public function getHot($query, $sort = array('clicknum'=>1), $skip = 0, $limit = 10, $fields = array()){
		$query = array('time'=>array('$lt'=>time()));
		return $this->Ndb->findAll($query, $sort = array(), $skip, $limit, $fields);
	} */
	
	
	public function group($key='', $initial='', $reduce='', $options= array()) {//通过
		
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