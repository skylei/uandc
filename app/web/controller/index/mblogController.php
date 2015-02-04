<?php
class mblogController extends BaseController {

	private  $data = array();
	public $initphp_list = array('run','micblog', 'timesort','search', 'reply','srctest', 'loadComment');
	private $_topicService = '';


	public function run(){
		echo time();
		$this->data['mblog'] = $this->_topicService->getAll();
		$this->view->assign('data', $this->data);
		$this->view->set_tpl('Home/mblog');
	}

	public function mblog(){
		$this->data['mblog'] = $this->_topicService->getAll();
		$this->view->assign('data', $this->data);
		$this->view->set_tpl('Home/mblog');
	}
	public function after(){
		$this->view->assign('data', $this->data);
		$this->view->display();
	}

	public function before(){
		$this->_topicService = $this->_getService('HomeMblog','Home');
		$this->data['baseUrl'] = '/?m=Home&c=topic&a=';
	}

}