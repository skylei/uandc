<?php
/*
 * userCenter
 * @author crab
 * @date 2014-10-31
 *
 * */
namespace web\controller\ucenter;
use components\Myconstant;

class uhomeController extends \components\BaseUcenterController{


	public function run_after(){
        $this->data['baseUrl'] = $this->getbaseUrl();
        $this->data['basePath'] = $this->getBasePath();
        $this->data['urls'] = $this->urlTogather();
		$this->assign('data', $this->data);
		$this->show();
	}

    public function  run(){
        parent::run();
        define('IS_INITPHP', true);
    }

    public function staticAction(){
        $this->setTpl('static');

    }

    public function indexAction(){
        $page = $this->_get('page', 0);
        $page = ( $page - 1 <= 0) ? 0 : $page - 1;
        $pagesize = 20;
        $offset = $page * $pagesize;
        $count = $this->getArtService()->artCount();
        $this->data['pageCount'] = ceil($count[0]['count'] / $pagesize);
        $url = $this->createUrl('/ucenter/uhome/index');
        $pager = new \extensions\library\pagerInit();
        $this->data['pageHtml'] = $pager->pager($count[0]['count'], $pagesize, $url);
        $this->data['delUrl'] = $this->createUrl('/ucenter/uhome/del');
        $this->data['art'] = $this->getArtService()->artList('', $offset, $pagesize);
        $this->data['comment'] = $this->getUService()->commentList(0,20);
        $this->setTpl('index');
    }



	public function mainAction(){
		//$this->data['count'] = $this->getArtService()->Count();
        $this->data['delUrl'] = $this->createUrl('/ucenter/uhome/del');
		$this->data['art'] = $this->getArtService()->artList(0, 20);
        $this->data['comment'] = $this->getUService()->commentList(0,20);
        $this->setTpl('main');
	}
	
	public function urlTogather(){
		$url['delArt'] = $this->createUrl('/ucenter/uhome/del');
        $url['addArt'] = $this->createUrl('/ucenter/uhome/addart');
		$url['uhome']  = $this->createUrl('/ucenter/uhome/main');
		$url['login']  = $this->createUrl('/ucenter/index/login');
        $url['edit']  = $this->createUrl('/ucenter/uhome/editArt');
		return $url;
	}
	
	public function myMblog(){
		$res = \Ouno\Ouno::dao('mblog', 'Home')->getNew($query = array());
		$this->data['mblog'] = \Ouno\Ouno::dao('mblog', 'Index')->db->findAll($query = array());
	}
	
	public function generalAction(){
		$time = (int) (time()-604800);
		$query = array('time'=>array('$lt'=>$time));
		$comments = $this->getDao('HomeComments', 'Home');//1
		$this->data['comCount'] = $comments->count($query);
		$this->data['comments'] = $comments->getAll(array('isread'=>1), array('time'=> -1), $skip = 0);
		$art = $this->getDao('Home', 'Home');//2 nosqlinit 使用了单例，不能乱了顺序
		$this->data['newArt'] = $art->getAll($query);
		$this->data['artCount'] = $art->count($query);
		$visitor = $this->getDao('visit', 'Common');
		$this->setTpl('Ucenter/index');
		$this->dump($this->data['newArt']);
	}


	public function artManage(){
		$art = $this->getDao('Home', 'Home');
		$this->data['artList'] = $art->getAll();
		$this->setTpl('artManage');
	}

	public function getDao($daoName, $group = 'Ucenter'){
		return \Ouno\Ouno::dao($daoName, $group);

	}

    public function wmdAction(){
        $this->setTpl('wmd');


    }
	public function modifyInfo(){

		$this->setTpl('ucenter/modifyInfo');
	}

	public function addArtAction(){
        if($_POST){
            $data['user_id'] = '1';
            $data['update_time'] = date('Y-m-d H:i:s', time());
            $data['type'] =  $this->_post('type');
            $data['content'] = $this->_post('content');
            $data['title']  = $this->_post('title');
            $data['author']  = $this->_post('author');
            $data['is_show'] = 1;
            $tag  = $this->_post('tag');
            $data['tags'] = str_replace(array(',' , '，' ,'；', ';', '/'), ' ', $tag);
            $data['cate']  = $this->_post('cate');
            $newcate = $this->_post('newcate');
            $data['from_url']  = $this->_post('from');
            if(empty($data['content']))  $this->ajax_return(false, "content unable null");
            $time = $this->_post('time');
            $data['create_time'] 	= !empty($time) ? strtotime($this->_post('time')) : time();
            $data['author'] = $data['author'] ? $data['author'] : '螃蟹在晨跑';
            $data['cate'] 	= $data['cate'] ? $data['cate'] : 'code';
            if($data['cate'] == 'add' && $data['newcate']){
                $this->getUService()->addNewCate(array('cate'=> $newcate));
                $data['cate'] = $newcate;
            }
            var_dump($_POST['content']);
            $res = \Ouno\Ouno::dao('article', 'index')->db->insert($data);
            if($res)
                $this->ajax_return(true,'success');
            else
                $this->ajax_return(false,'false');
        }

        $this->data['cate'] = $this->getUService()->getCateList();
        $this->data['submitUrl'] = $this->createUrl('/ucenter/uhome/addArt');
		$this->setTpl('addArt');
	}

    public function editArtAction(){
        $id = $this->_get('id');
        $this->data['art'] = $this->getArtService()->getDetail($id);
        $this->setTpl('editArt');
    }

	public function addmblogAction(){
		$this->setTpl('addMblog');
	}


    public function imgUploadAction(){
        if(!isset($_FILES['art_img'])) return false;
        $temp = $_FILES['art_img'];
        $check = \components\BaseTools::allow_img_type($temp['type']);
        if(!$check)
           $this->ajax_return(false, 'image type is incorrect!', 400);
        if($temp['error'] != 0)
            $this->ajax_return(flase, 'image upload has some wrong!');
        $filename = Myconstant::IMG_PREFIX . time(). mt_rand(1000, 9999);
        $postfix = substr($temp['type'], strrpos($temp['type'],'/') + 1);
        $file = '/static/images/ablum/default/' . $filename . '.'. $postfix;
        $mservice = new \src\service\ucenter\mongoService();
        $gfs = $mservice->getDao('image')->getGridFS();
        $mid = $gfs->storeFile($temp['tmp_name'], array('filename'=> $file, 'create_time'=>new \MongoDate ()));
        if(is_object($mid) && isset($mid->{'$id'})) {
            $id = $mid->{'$id'};
        }else{
            $this->ajax_return(false, 'add mongo fail');
        }
        $truePath = APP_PATH . $file;
        $showPath = 'http://www.uand.cn/index.php/image/pic/show/id/' . $id;
        //保险起见保留一张原图
        move_uploaded_file($temp['tmp_name'], $truePath);

      $return = array('success'=>true, 'msg'=>'upload success!', 'file_path'=>$showPath);
//        $return = array('success'=>true, 'msg'=>'upload success!', 'file_path'=>$truePath);
        echo json_encode($return);
        exit;

    }

    public function uploadAction(){
//        $mservice = new \src\service\ucenter\mongoService();
//        $dao = $mservice->getDao('image');
//        $gfs = $mservice->getDao('image')->getGridFS();
//        $return = array('success'=>true, 'msg'=>'upload success!', 'file_path'=>'/app/static/images/site/cat.png');
//        echo json_encode($return);
//        exit;
//        $file = 'd:\web\www\uandc\app\static\images\site\m.jpg';
//        $gfs->storeFile($file, array('name'=>'test', 'create_time'=>new \MongoDate () ));
//
//        exit;
        // $this->setTpl('upload');
        $this->setTpl('upload');

    }

    public function PicsUploadAction(){



        $this->setTpl('picsUpload');
    }

	public function delAction(){
		$id = $this->_get('id');
		$type = $this->_get('type');
        $res = \Ouno\Ouno::dao('article', 'Index')->db->delte(array('id'=>$id));
		if(!empty($res)){
			$this->ajax_return(true,'success');
		}else{
			$this->ajax_return(false,'fail');
		}
	}
	
	public function getArtService(){
        return \Ouno\Ouno::service('index', 'Index');
	}

    public function getCommService(){
        return \Ouno\Ouno::service('index', 'Index');
    }
	
	private function getUService(){
		return \Ouno\Ouno::service('ucenter', 'Ucenter');
	}
}