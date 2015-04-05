<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/30
 * Time: 16:05
 */
namespace web\controller\index;

use components\BaseTools;
use components\Myconstant;
use \src\service\index\indexService as indexService;
class indexController extends \components\BaseController {


	protected $data = array();//存放抛向模板的变量,避免多次使用$this->view->assign();
	
    public function  run(){
        define('IS_INITPHP', true);
        $this->data['basePath'] = $this->getBasePath();
        $this->data['baseUrl'] = $this->getBaseUrl();
        $this->data['imUser'] = $this->getIndexService()->isLogined();
    }


    /*
     * 后置控制器
     * */
    public function run_after(){
        if($this->display)
            $this->show();
    }


    /*
     * @desc 首页
     * */
	public function indexAction()
    {
        $data = $this->data;
        $page = $this->_get('page', 1);
        $pagesize = 20;
        $offset = ($page - 1) * $pagesize;
        $cate = $this->_get('cate');
        $data['artList'] = $this->getIndexService()->artList($cate, $offset, $pagesize);
        $data['detailUrl'] = $this->createUrl('/index/index/detail');
        $data['searchUrl'] = $this->createUrl('/index/index/search');
        $cate = $cate ? "cate = ' $cate '" : '';
        $count = $this->getIndexService()->artCount($cate);
        $data['pageCount'] = ceil($count['count'] / $pagesize);
        $url = $this->createUrl('/index/index/index');
        $pager = new \extensions\Library\pagerInit();
        $data['pageHtml'] = $pager->pager($count['count'], $pagesize, $url);
        $imgService = new \src\service\image\mongoService();
        $data['image'] = $imgService->getNew();
        $this->assign('data', $data);

//        $onlineList = $this->getIndexService()->getOnlineList();
        $onlineList = $this->getIndexService()->getAllImUser();
        foreach($onlineList as $uid=>$fd){

        }
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



	public function detailAction(){
		$id =$this->_get('id');
        $detail = $this->getIndexService()->getDetail($id);
        $data['submitUrl'] = $this->createUrl('/index/index/artComment');
        $where = "where id = '$id'";
        $data = array('click_num'=>'click_num + 1');//原子操作
        $detail = $this->getIndexService()->updateArtClick($where,$data );
        $tags = $detail['result'];
        $tagList= $this->getIndexService()->getTagList($tags['tags']);
        $data['comment'] = $this->getIndexService()->getComment($id);
        $count = $this->getIndexService()->artCommentNum($id);
        $data['commentCount'] = $count['count'];
        $tags = array();
        foreach($tagList as $key=>$val){
            $list = explode(' ', $val['tags']);
            foreach($list as $k=>$v){
                if(!in_array($v, $tags)) $tags[] = $v;
            }
        }

        $data['tagList'] = $tags;
        $data['detail'] = $detail['result'];
        $this->setTpl('detail');
	}

    public function tagListAction(){

        $tag = $this->_get('tag');
        $len = strlen($tag);
        if($len < 1 || $len > 50) $this->ajax_return(false, 'word is too simple or too complex ');
        $tagList = $this->getIndexService()->getTagList($tag);
        //if($tagList) $tagList = ''
        $data['artList'] =$tagList;
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
        return new indexService();
    }

    public function imUserLoginAction(){
        $this->display = false;
        $username = $this->_get('username');
        $password = $this->_get('password');
        if(!$username || !$password)
            $this->ajax_return(false, 'username or password incorrect');
        $data = array(
            "username"=> array("value"=>$username, "operator"=> '=', 'connector'=> 'AND'),
            "password"=> array("value"=>md5($password), "operator"=> '=')
        );
        $userInfo =  $this->getIndexService()->checkImUserLogin($data);
        if(empty($userInfo))
            $this->ajax_return(false);
        $this->setTpl("chatroom");
        $html = $this->show();
        $_SESSION[Myconstant::IM_SESSIOM_ID] = $userInfo['uid'];
        $_COOKIE[Myconstant::IM_COOKIE_ID] = $userInfo['uid'];
        $userInfo['chatroom'] = $html;
        $this->ajax_return(true, $userInfo);
    }

    /*
     * im用户注册
     * */
    public function checkImUserRegisterAction(){

        $data['username'] = $this->_get('username');
        $data['password'] = md5($this->_get('password'));
        if(!$data['username'] || !$data['password'])
            $this->ajax_return(false, 'username or password incorrect');
        $data['ip'] = 123;
        $time = time();
        $data['create_time'] = $time;
        $data['update_time'] = date("Y-m-d H:i:s", $time);
        $data['uid'] = uniqid('crab');
        $data['avatar'] = 'http://www.uandc.cn/app/static/images/usericon/crab.jpg';
        $data['token'] = BaseTools::getToken($data['uid']);
        $res =  $this->getIndexService()->addImuser($data);
        if(!$res)
            $this->ajax_return(false, 'register fail');

        $condition = array(
            "username"=>array("value"=> $data['username'], "operator" => '=', 'connector'=> 'AND'),
            'password' => array("value"=> $data['password'], 'operator'=> '='),
        );

        $fields = "uid, avatar,username,token";
        $userInfo = $this->getIndexService()->getImUser($condition, $fields);
        $this->display = false;
        $this->setTpl("chatroom");
        $html = $this->show();
        $_SESSION[Myconstant::IM_SESSIOM_ID] = $userInfo['uid'];
        $_COOKIE[Myconstant::IM_COOKIE_ID] = $userInfo['uid'];
        $userInfo['chatroom'] = $html;
        $this->ajax_return(true, $userInfo);
    }


    /*
     * im用户下线
     * */
    public function offlineAction(){
        if(isset($_COOKIE[Myconstant::IM_COOKIE_ID]))
            unset($_COOKIE[Myconstant::IM_COOKIE_ID]);
        if(isset($_SESSION[Myconstant::IM_SESSIOM_ID]))
            unset($_SESSION[Myconstant::IM_SESSIOM_ID]);
        $this->ajax_return(true);
    }




}
