
<?php
class picController extends BaseController {

	protected $data = array();//存放抛向模板的变量,避免多次使用$this->view->assign();
	
    protected function  run(){
        define('IS_INITPHP', true);
        $this->data['basePath'] = '/uandc';
        $this->data['baseUrl'] = $this->getBaseUrl();
    }
	public function indexAction()
    {
        $page = $this->_get('page', 0);
        $page = ( $page - 1 <= 0) ? 0 : $page - 1;
        $pagesize = 20;
        $offset = $page * $pagesize;
        $cate = $this->_get('cate');
        $this->data['artList'] = $this->getIndexService()->artList($cate, $offset, $pagesize);
        $this->data['detailUrl'] = $this->createUrl('/index/index/detail');
        $this->data['searchUrl'] = $this->createUrl('/index/index/search');
        $count = $this->getIndexService()->artCount();
        $this->data['pageCount'] = ceil($count[0]['count'] / $pagesize);
        $url = $this->createUrl('/index/index/index');
        $pager = new pagerInit();
        $this->data['pageHtml'] = $pager->pager($count[0]['count'], $pagesize, $url);
        $this->assign('data', $this->data);
        $this->setTpl('pic');
    }
	
	public function mblogAction(){
        $this->data['artList'] = $this->getIndexService()->artList($offset = 0, $pagesize= 10);
        $this->assign('data', $this->data);
        $this->setTpl('mblog');
    }

    public function run_after(){
        $this->assign('data', $this->data);
        if($this->display) $this->show();
    }
	
	public function detailAction(){
		$id =$this->_get('id');
        $detail = $this->getIndexService()->getDetail($id);

        $where = "where id = '$id'";
        $data = array('click_num'=>'click_num + 1');//原子操作
        $detail = $this->getIndexService()->updateArtClick($where,$data );
        $tagList= $this->getIndexService()->getTagList($detail['tags']);
        $this->data['comment'] = $this->getIndexService()->getComment($id);
        $count = $this->getIndexService()->artCommentNum($id);
        $this->data['commentCount'] = $count[0]['count'];
        $tags = array();
        foreach($tagList as $key=>$val){
            $list = explode(' ', $val['tags']);
            foreach($list as $k=>$v){
                if(!in_array($v, $tags)) $tags[] = $v;
            }
        }
        $this->data['tagList'] = $tags;
        $this->data['detail'] = $detail;
        $this->setTpl('detail');
	}

    public function tagListAction(){

        $tag = $this->_get('tag');
        $len = strlen($tag);
        if($len < 1 || $len > 50) $this->ajax_return(false, 'word is too simple or too complex ');
        $tagList = $this->getIndexService()->getTagList($tag);
        //if($tagList) $tagList = ''
        $this->data['artList'] =$tagList;
        $this->setTpl('index');
    }

    public function praiseOrNot($cid){
        $praise = $this->_get('praise');
        $field = $praise ? 'praise' : 'unlike';
        $this->getIndexService()->praiseComment($cid, $field);
    }

	public function searchAction(){
        $this->data['keyword'] = $this->_get('search');

        if (empty($this->data['keyword'])) $this->redirect($this->createUrl('Home', 'index', 'run'));
        $this->data['artList'] = $this->getIndexService()->search($this->data['keyword']);
        //$this->data['visitLog'] = $this->getIndexService()->visitLog($this->data['keyword']);
        $this->assign('data', $this->data);
        $this->setTpl('search');
    }

    public function artCommentAction(){
        $data['art_id'] = $this->_post('artId');
        $data['content'] = $this->_post('content');
        $data['email'] = $this->_post('email');
        $data['nickname'] = $this->_post('nickname');
        $data['ip'] = ip2long($_SERVER['REMOTE_ADDR']);
        $data['user_icon'] = '/app/static/images/usericon/crab.jpg';
        $data['content'] = $this->_post('content');
        $data['create_time'] = time();
        $data['update'] = date('Y-m-d H:i:s', time());
        $res = $this->getIndexService()->insertComment($data);
        $this->display = false;
        $this->redirect('');
    }

    private function getIndexService(){
        return Ouno::service('index', 'Index');
    }



















}
