<?php
/*
* 首页加载service
* @author weiyuhe123@163.com
*/
class HomeArtService extends Service {
	private $_dao = '';
	public  $HomeDao = '';

	public function __construct() {
		$this->_dao = InitPHP :: getDao('Common', 'Common');//获得CommonDao类的工厂
		$this->HomeDao = $this->_dao->fDao('Home', 'Home');

	}

	/*最帖子列表*/
	public function getAll($query, $sort = array('time'=> 1), $skip=0,$limit = 10){

		//$list['count']  = $this->HomeDao->getCounts();
		$list = $this->HomeDao->getAll($query, $sort, $skip, $limit);
		return $list;
	}

	/*最热门帖子*/
	public function HotArt($query, $sort = array('clicknum'=>1), $skip = 0, $limit = 10, $fields = array()){
		//$query = array('time'=>array('$lt'=>time()));
		return $this->HomeDao->getAll($query, $sort, $skip, $limit, $fields);
	}

	/*最热门帖子*/
	public function getOne($query){
		return $this->HomeDao->getOne($query);
	}

	/*最新XX条贴子*/
	public function NewArt($query, $sort = array('time'=> -1), $skip = 0, $limit = 10, $fields = array()){
		$query = array('time'=>array('$lt'=>time()));
		return $this->HomeDao->getAll($query, $sort, $skip, $limit, $fields);
	}

	public function count($query) {
		return $this->HomeDao->count($query);
	}



	public function getGroup($data){
		$key = $data;
		$init = array('items'=>array());
		$reduce = "function (doc,prev){ prev.items.push(obj);}";
		$result = $this->HomeDao->group($key, $init, $reduce);
		return $result['retval'];
	}

	public function search($search){
		//if($data) $this->controller->redirect('/')
		$preg =  new MongoRegex("/$search/i");
		$where = array ('$or' =>
					array (0 =>
						array ('title' => $preg ),
					1 =>
						array ('keyword' =>$preg ),
					2 =>
						array ('content' =>$preg ),
					)
				);
		return $this->HomeDao->getAll($where);
	}

	public function category($key){
		//$key = array('cate'=>'wrisper');
		return $this->HomeDao->distinct($key);
	}

	public function getDetail($id){
		$query = array('_id'=>new MongoId($id));
		$data = array('$inc'=>array('clicknum'=>1));
		return $this->HomeDao->findAndModify($query, $data);
	}
	public function commentDao(){
		return  $this->_dao->fDao('HomeComments', 'Home');
	}
	public function mblogDao(){


	}
	public function getComments($id){
		$_id = new mongoId($id);
		$comments = $this->commentDao();
		$query = array('artid'=>$_id);
		return $comments->getAll($query);
	}

	public function insertComment($data){
		$data = array_filter($data);
		$comments = $this->commentDao();
		return $comments->insert($data);
	}

	public function fAndMcomment($query, $data){
		$comments = $this->commentDao();
		return $comments->findAndModify($query, $data);

	}
	public function updateComment($query, $data){
		$comments = $this->commentDao();
		return $comments->update($query, $data);

	}

	public function getMblog(){
		$mblog = $this->mblogDao();
		return $mblog->getAll();

	}
	/*
	* 获取热门相册
	*
	*/
	public function hotTags($query) {
		$tags = $this->_dao->fDao('HomeTags', 'Home');
		$key = array('tags'=>$query);
		$init = array('items'=>array());
		$reduce = "function (doc,prev){

			prev.items.push(obj);}";
		//$result = $this->HomeDao->group($key, $init, $reduce);
		$hotTags = $tags->group($key, $init, $reduce);
		return $hotTags;
	}

	public function visitLog($info, $act){
		$query = array('info'=>$info, 'ip'=>'127.0.0.1','time'=>time(), 'visitnum'=>1, 'actNum'=>1, 'act'=>$act );
		$visit = $this->_dao->fDao('HomeVisit', 'Home');
		$update = array('$inc'=>array('visitnum'=>1, 'actNum'=>1));
		$result = $visit->findAndModify(array('info'=>$info, 'act'=>$act),$update);
		if(empty($result)){
			$visit->insert($query);
			$result = $visit->getOne($query);
		}
		return  $result;
	}


	/**
	 * 前置操作
	*/


	/**
	 * 分页类
	 * @param int $count
	 */
	public function page($count, $str = '') {
		$pager= $this->getLibrary('pager'); //分页加载
		$c   = $this->controller->get_gp('c');
		$a   = $this->controller->get_gp('a');
		$url = $this->getUrl($c, $a) . $str;
		$page_html = $pager->pager($count, $this->perpage, $url, true);
		if ($count == 0) $page_html = '';
		$this->view->assign('page', $page_html);
	}


















}


?>