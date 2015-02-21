<?php
class HomeMblogService extends Service {
	private $_dao = '';
	public  $mblogDao = '';

	public function __construct() {
		$this->_dao = InitPHP :: getDao('Common', 'Common');//获得CommonDao类的工厂
		$this->mblogDao = $this->_dao->fDao('HomeMblog', 'Home');

	}

	/*最帖子列表*/
	public function getAll($query, $sort = array('time'=> 1), $skip=0,$limit = 10){

		//$list['count']  = $this->HomeDao->getCounts();
		$list = $this->mblogDao->getAll($query, $sort, $skip, $limit);
		return $list;
	}



}



?>
