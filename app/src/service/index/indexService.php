<?php
/*
* 首页加载service
* @author weiyuhe123@163.com
*/
namespace src\service\index;

use components\Myconstant;
use \Ouno\Ouno as Ouno;
class indexService extends \Ouno\Service {


	/*最热门帖子*/
	public function HotArt(){
		//$query = array('time'=>array('$lt'=>time()));
		return Ouno::dao('article', 'index')->findAll();
	}

	/*
	 * 最热门帖子
	 * @param $query array 查询的条件数组
	 * @return array | false;
	 * */
	public function getOne($query = array()){
		return Ouno::dao('article', 'index')->getOne($query);
	}

	/*最新XX条贴子*/
	public function artList($cate, $offset, $pagesize){
        $where = '';
		if($cate) $where = "cate = '$cate'";
        $field = '*';
        $options = array(
            'order'=>array('create_time', 'value'=>'create_time', 'sort'=>'DESC'),
            'limit'=>array('offset'=>$offset, 'pagesize'=>$pagesize)
        );
		return Ouno::dao('article', 'index')->dao->findAll($where, $field, $options);
	}

    /*
     * 更新用户点击的数量
     * */
    public function updateArtClick($where, $data, $options = ''){
        return Ouno::dao('article', 'index')->dao->findOneModify($where, $data, $options);
    }

    /*
     * 获取文章的评论数
     * @param int $id
     * @param array | false
     * */
    public function artCommentNum($id){
       return  Ouno::dao('comment', 'index')->count("art_id = '$id'");
    }

    public function praiseComment($cid, $field){
        $data = array($field=> "$field + 1");
        $where = " where id = '$cid' ";
        return Ouno::dao('comment', 'index')->update($data, $where);
    }

    /*
     * 统计文章数
     * @param array $query
     * @return array | false
     * */
	public function count($query  = array()) {
		return Ouno::dao('article', 'index')->count($query);
	}

    public function getUsericon(){
        return Ouno::dao('usericon', 'index')->dao->findAll();
    }

    public function commentUnique($data){
        $where = " nickname ='{$data['user']}' and  email ='{$data['email']}'";
       return  Ouno::dao('comment', 'index')->dao->findAll($where);
    }


	/*
	* 获取推荐标签
	*

	public function HotTags(){
		$query = array();
		$hotTags = $this->_dao->fDao('IndexTags','index')->getHot();
		return $hotTags;
	}*/


	public function getGroup($data){
		$key = $data;
		$init = array('items'=>array());
		$reduce = "function (doc,prev){ prev.items.push(obj);}";
		$result = Ouno::dao('article', 'index')->group($key, $init, $reduce);
		return $result['retval'];
	}

	public function search($search){
		//if($data) $this->controller->redirect('/')
        $where = "title like '%$search%' or tags like '%$search%'";
        $res = Ouno::dao('article', 'index')->dao->findAll($where);
		return $res;
	}


    public function getTagList($tag){
        $tagStr = "tags like '%" . $tag . "%'";
        $tagStr .= 'ORDER BY click_num DESC LIMIT 0,15';
        $field = '*';
        return Ouno::dao('article', 'index')->dao->findAll($tagStr, $field);
    }

	public function category($key){
		//$key = array('cate'=>'wrisper');
		return Ouno::dao('article', 'index')->distinct($key);
	}

     /*
      * article count
      * */
    public function artCount($where = ''){
        return Ouno::dao('article', 'index')->count($where);
    }

	public function getDetail($id){
		$where = array('id'=>array('value'=>$id, 'operator'=> '='));
		return Ouno::dao('article', 'index')->dao->findOne($where);
	}

	public function mblogDao(){


	}

	public function insertComment($data){
		$data = array_filter($data);
		return Ouno::dao('comment', 'index')->dao->insert($data);
	}

    public function getComment($id){
        $condition['art_id'] = array('value'=>$id, 'operator'=>'=');
        return Ouno::dao('comment', 'index')->dao->findAll($condition);
    }

	public function updateComment($cid, $type){
        $data = array($type=>"$type + 1");
        $where = "where id='$cid'";
		return Ouno::dao('comment', 'index')->dao->update($data, $where);

	}

    /*
     * 检验im用户登录
     * @param array $data
     * @return array | false
     * */
    public function checkImUserLogin($data){
        $field = "*";
        return Ouno::dao('imuser', 'user')->dao->findOne($data, $field);
    }

    /*
     * 是否是登录用户
     * */
    public function isLogined(){
        if(!empty($_SESSION[Myconstant::IM_SESSIOM_ID]))
            $loginUser['uid'] = $_SESSION[Myconstant::IM_SESSIOM_ID];
        else if(!empty($_COOKIE[Myconstant::IM_COOKIE_ID]))
            $loginUser['uid'] = $_COOKIE[Myconstant::IM_COOKIE_ID];
        else
            return false;
        $condition = array("uid"=>array("value"=>$loginUser['uid'], 'operator'=> '='));
        return Ouno::dao('imuser', 'user')->dao->findOne($condition);
    }

    /*
     * 添加im用户
     * @param array $data
     * @return boolean
     * */
    public function addImUser($data){
        $condition = array(
            "username" => array("value"=> $data['username'], 'operator'=> "=" )
        );
        $exist = Ouno::dao("imuser", "user")->dao->findOne($condition);
        if($exist)
            return false;
        return Ouno::dao("imUser", "user")->dao->insert($data);
    }

    /*
    * 获取im用户
    * @param array $data
    * @return boolean | array
    * */
    public function getImUser($data){
        return Ouno::dao("imUser", "user")->dao->findOne($data);
    }

    /*
     * 获取im用户在线列表
     * */
    public function getOnlineList(){
        return Ouno::dao("redis", "redis")->getChannelAllMember("ALL");
    }

    /*
     * 获取所有im用户
     * */
    public function getAllImUser(){
       return  Ouno::dao("imUser", "user")->dao->findAll();
    }

}


?>