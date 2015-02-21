<?php
class HomeTopicService extends Service {
	private $_dao = '';
	public  $TopicDao = '';

	public function __construct() {
		$this->_dao = InitPHP :: getDao('Common', 'Common');//获得CommonDao类的工厂
		$this->TopicDao = $this->_dao->fDao('HomeTopic', 'Home');

	}

	/*最帖子列表*/
	public function getAll($query, $sort = array('time'=> 1), $skip=0,$limit = 10){

		//$list['count']  = $this->HomeDao->getCounts();
		$list = $this->TopicDao->getAll($query, $sort, $skip, $limit);
		return $list;
	}



}



?>
