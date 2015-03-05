<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/30
 * Time: 16:05
 */
namespace web\controller\index;

class indexController extends \components\BaseController {


	protected $data = array();//存放抛向模板的变量,避免多次使用$this->view->assign();
	
    protected function  run(){
        define('IS_INITPHP', true);
        $this->data['basePath'] = $this->getBasePath();
        $this->data['baseUrl'] = $this->getBaseUrl();
    }

    /*
     * @desc 首页
     * */
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
        $cate = $cate ? "cate = ' $cate '" : '';
        $count = $this->getIndexService()->artCount($cate);
        $this->data['pageCount'] = ceil($count[0]['count'] / $pagesize);
        $url = $this->createUrl('/index/index/index');
        $pager = new \extensions\Library\pagerInit();
        $this->data['pageHtml'] = $pager->pager($count[0]['count'], $pagesize, $url);


        $imgService = new \src\service\image\mongoService();
        $this->data['image'] = $imgService->getNew();
        $this->assign('data', $this->data);
        $this->display = true;
        $this->setTpl('index');
    }

    public function testAction()
    {
        $res = \Ouno\Ouno::service('search', 'search')->getArt('兔子');
        var_dump($res);
        exit;
    }

    public function viewAction(){
        $a = array(1=>'aaaa',2=>'dfddd',3=>'ssss',4=>'dddddd',5=>'aaa');
        $this->_view->assign('a', $a);
        $this->_view->display('view');
        $this->_view->createStaticFile('view');
    }



	public function mblogAction(){
        $this->data['artList'] = $this->getIndexService()->artList($offset = 0, $pagesize= 10);
        $this->assign('data', $this->data);
        $this->setTpl('mblog');
    }

    /*
     * 后置控制器
     * */
    public function run_after(){
        $this->assign('data', $this->data);
        if($this->display) $this->show();
    }

	public function detailAction(){
		$id =$this->_get('id');
        $detail = $this->getIndexService()->getDetail($id);
        $this->data['submitUrl'] = $this->createUrl('/index/index/artComment');
        $where = "where id = '$id'";
        $data = array('click_num'=>'click_num + 1');//原子操作
        $detail = $this->getIndexService()->updateArtClick($where,$data );
        $tags = $detail['result'];
        $tagList= $this->getIndexService()->getTagList($tags['tags']);
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
        $this->data['detail'] = $detail['result'];
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

    public function cliAction(){
        $detail = $this->getIndexService()->getDetail($id = 1);
    }

	public function searchAction(){
        $keyword = $this->_get('search', '');
        if (!$keyword) $this->redirect('');
        $searchService = new \src\service\search\searchService();
        $result = $searchService->getArt($keyword);
        if(!$result) exit;
        $this->assign('result', $result);
        $this->setTpl('search');
    }

    public function artCommentAction(){
        $data['replyto'] = $this->_post('replyto');
        $data['art_id'] = $this->_post('artId');
        $data['content'] = $this->_post('content');
        $data['email'] = $this->_post('email');
        $data['nickname'] = $this->_post('nickname');
        $data['ip'] = ip2long($_SERVER['REMOTE_ADDR']);
        $userIcon = $this->getIndexService()->getUsericon();
        $iconIndex = mt_rand(0, count($userIcon)-1);
        $data['user_icon'] = $userIcon[$iconIndex]['img_path'];
        $data['content'] = $this->_post('content');
        $data['create_time'] = time();
        $data['update'] = date('Y-m-d H:i:s', time());
        $res = $this->getIndexService()->insertComment($data);
        $this->display = false;
        $this->redirect('/index/index/detail/id/' . $data['art_id']);
    }

    public function checkCommentAction(){
        $this->display = false;
        $data['user'] = $this->_get('user');
        $data['email'] = $this->_get('email');
        $res = $this->getIndexService()->commentUnique($data);
        if(!empty($res)) {
            $this->ajax_return(false, 'user is already existed');
        }else{
            $this->ajax_return(true, 'true');
        }
    }

    public function updatecommentAction(){
        $this->display =false;
        $cid = $this->_get('cid');
        $type = $this->_get("type");
        $res = $this->getIndexService()->updateComment($cid, $type);
        if($res){
            $this->ajax_return(true);
        }else{
            $this->ajax_return(false);
        }

    }

    private function getIndexService(){
        return new \src\service\index\indexService();
    }



















}
