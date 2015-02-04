<?php
class artController extends BaseController {

	public $initphp_list = array('run','readmore', 'timesort','search', 'reply','srctest', 'loadComment');
	protected $_artService = '';

	protected $data = array();//存放抛向模板的变量,避免多次使用$this->view->assign();

	public function run() {
		$id = $this->controller->get_gp('id');
		$id = new mongoId($id);
		$this->data['detail'] = $this->_artService->getDetail($id);
		$this->data['comments'] = $this->_artService->getComments($id);
		$this->data['replyUrl'] = $this->getUrl('Home', 'index', 'reply');
		$this->view->assign('data', $this->data);
		$this->view->set_tpl('Home/detail');
	}

	public function after(){
		$this->view->assign('data', $this->data);
		$this->view->display();
	}

	public function search(){
		$this->data['keyword'] = $this->controller->get_gp('search');
		if(empty($this->data['keyword'])) $this->controller->redirect($this->getUrl('Home', 'index', 'run'));

		$this->data['search'] = $this->_artService->search($this->data['keyword']);
		if(empty($this->data['search'])) $this->controller->redirect($this->getUrl('Home', 'index', 'run'));
		$this->data['visitInfo'] = $this->_artService->visitLog($this->data['keyword'], 'search');
		$this->dump($this->data['visitInfo']);
		$this->view->set_tpl('Home/search');

	}

	public function loadComment(){
		$comments = $this->_artService->commentDao();
		$id = $this->controller->get_gp('_id');
		$_id = new mongoId('536e2699bd0383dc08000029');
		$query = array('artid'=>$_id);
		$data = $comments->getAll($query);
		if(!empty($data)){
			foreach($data as $info){
				$html = <<<EOF
<div class="context">
<div class="avatar"><img src="../../thumb/usericon.jpg"/></div>
<div class="textwrap" style="background:#fff;">
<hr/>
<p style="padding:5px">{$info['content']}</p>
</div>
<div class="more">
<span class="icon">L</span><span1>{$info['nickName']}</span1>
<a href="javascript:void(0);" class="reply" id="{$info['_id']}" onclick="replyTo()" ><span class="icon">O</span><span1>回复</span1></a>
<a href="javascript:void(0);" id="zan" title="赞一下" value="6"><span1><span class="icon">1</span<!--good-->6</span1></a>
<a href="javascript:void(0);" id="zan2" title="黑一下" value="5"><span1><span class="icon">2</span><!--bad-->5</span1></a>
</div>
</div>
EOF;
				$reHtml .= $html;
			}
		}
		echo $reHtml;
		exit(0);
	}

	public function reply(){
		
		$id = $this->controller->get_gp('id');
		$data['to'] = $this->controller->get_gp('to');
		$data['artid'] = new mongoId($id);
		$data['nickName'] = $this->controller->get_gp('nickName');
		$data['email'] = $this->controller->get_gp('email');
		$data['content'] = $this->controller->get_gp('content');
		$verify = $this->controller->get_gp('verify');
		//$insert = $this->_artService->insertComment($data);
		//$htmlFile = $this->controller->get_gp('artUrl');
		$data['url'] = 'static/data/art/14009166771391.html';
		$html = file_get_contents($data['url']);
		$rehtml = <<<EOF
<div class='context'>
<div class='avatar'><img src='static/thumb/usericon.jpg'/></div>
<div class='textwrap' style='background:#fff;'>
<hr/>
<p style='padding:5px'>{$data['content']}</p>
</div>
<div class='more'>
<span class='icon'>L</span><span1>{$data['nickName']}</span1>
<a href='javascript:void(0);' class='reply' id='<{$data['artid']}>' ><span class='icon'>O</span><span1>回复</span1></a>
<a href='#' title='赞一下'><span1><span class='icon'>1</span>10</span1></a>
<a href='#'  title='黑一下'><span1><span class='icon'>2</span></span1></a>
</div>
</div>
<!--side-->
EOF;
		$pattern = "/<\!--side-->/";
		$newHtml = preg_replace($pattern, $rehtml, $html);
		file_put_contents($data['url'], $newHtml);
		$this->dump($newHtml);
	}

	public function srctest(){

	}

	public function good(){
		$_id = $this->controller->get_gp('_id');
		$_id = new mongoId($_id);
		$query = array('_id', $_id);
		$data = array('$inc',array('good'=>1));
		$result = $this->_artService->updateComment($query, $data);
		echo true;
		exit();
	}

	/**
	 * 公用URL组装函数
	 * @param string $c
	 * @param string $a
	 */
	public function getUrl($m, $c, $a) {
		$config = InitPHP::getConfig();
		$url = InitPHP::url($m . '|' . $c . '|' . $a);
		return $url;

	}
	public function before() {
		$this->_artService = $this->_getService('HomeArt','Home');
		$this->data['baseUrl'] = '/?m=Home&c=index&a=';
		/* parent::before();
		$this->view->assign('userRun', $this->getUrl('adminuser', 'run'));
		$this->view->assign('userDel', $this->getUrl('adminuser', 'del')); */
	}



}




?>