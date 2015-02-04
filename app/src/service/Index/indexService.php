<?php
/*
* 首页加载service
* @author weiyuhe123@163.com
*/
namespace src\service\index;
class indexService extends \Ouno\Service {


	/*最热门帖子*/
	public function HotArt(){
		//$query = array('time'=>array('$lt'=>time()));
		return \Ouno\Ouno::dao('article', 'index')->findAll();
	}

	/*
	 * 最热门帖子
	 * @param $query array 查询的条件数组
	 * @return array | false;
	 * */
	public function getOne($query = array()){
		return \Ouno\Ouno::dao('article', 'index')->getOne($query);
	}

	/*最新XX条贴子*/
	public function artList($cate, $offset, $pagesize){
        $where = '';
		if($cate) $where = "cate = '$cate'";
        $field = '*';
        $options = array('order'=>array('create_time', 'value'=>'create_time', 'sort'=>'DESC'), 'limit'=>array('offset'=>$offset, 'pagesize'=>$pagesize));
		return \Ouno\Ouno::dao('article', 'index')->db->findAll($where, $field, $options);
	}

    public function updateArtClick($where, $data, $options = ''){
        return \Ouno\Ouno::dao('article', 'index')->db->findOneModify($where, $data, $options);
    }
    public function artCommentNum($id){
       return  \Ouno\Ouno::dao('comment', 'index')->count(" where art_id = '$id'");
    }

    public function praiseComment($cid, $field){
        $data = array($field=> "$field + 1");
        $where = " where id = '$cid' ";
        return \Ouno\Ouno::dao('comment', 'index')->update($data, $where);
    }

	public function count($query  = array()) {
		return \Ouno\Ouno::dao('article', 'index')->count($query);
	}

    public function getUsericon(){
        return \Ouno\Ouno::dao('usericon', 'index')->db->findAll();
    }

    public function commentUnique($data){
        $where = " nickname ='{$data['user']}' and  email ='{$data['email']}'";
       return  \Ouno\Ouno::dao('comment', 'index')->db->findAll($where);
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
		$result = \Ouno\Ouno::dao('article', 'index')->group($key, $init, $reduce);
		return $result['retval'];
	}

	public function search($search){
		//if($data) $this->controller->redirect('/')
        $where = "title like '%$search%' or tags like '%$search%'";
        $res = \Ouno\Ouno::dao('article', 'index')->db->findAll($where);
		return $res;
	}


    public function getTagList($tag){
        $tagStr = "tags like '%" . $tag . "%'";
        $tagStr .= 'ORDER BY click_num DESC LIMIT 0,15';
        $field = '*';
        return \Ouno\Ouno::dao('article', 'index')->db->findAll($tagStr, $field);
    }

	public function category($key){
		//$key = array('cate'=>'wrisper');
		return \Ouno\Ouno::dao('article', 'index')->distinct($key);
	}

     /*
      * article count
      * */
    public function artCount($where = ''){
        return \Ouno\Ouno::dao('article', 'index')->count($where);
    }

	public function getDetail($id){
		$where = array('id'=>array('value'=>$id, 'operator'=> '='));
		return \Ouno\Ouno::dao('article', 'index')->db->findOne($where);
	}

	public function mblogDao(){


	}

	public function insertComment($data){
		$data = array_filter($data);
		return \Ouno\Ouno::dao('comment', 'index')->db->insert($data);
	}

    public function getComment($id){
        return \Ouno\Ouno::dao('comment', 'index')->db->findAll("art_id =$id ");
    }

	public function updateComment($cid, $type){
        $data = array($type=>"$type + 1");
        $where = "where id='$cid'";
		return \Ouno\Ouno::dao('comment', 'index')->db->update($data, $where);

	}

	public function getMblog(){
		return \Ouno\Ouno::dao('Mblog', 'index')->findAll();

	}


	public function visitLog($info, $act = 1){
		$data = array('info'=>$info['info'], 'ip'=>$info['ip'], 'create_time'=>time(), 'visit_num'=>1,
                        'user_agent'=>$info['user_agent'], 'act'=>$act, 'update'=>date('Y-m-d H:i:s', time()));
        $where = array(
            'ip'=> array('value'=>$info['ip'], 'operator'=> '=', 'connector'=>'and'),
            'info'=> array('value'=>$info['info'],'operator'=>'='),
        );
        $res = \Ouno\Ouno::dao('visit', 'index')->db->findOne($where, $field = 'create_time', 'order by create_time desc ');
        if(!$res) return false;
        if(time() - $res['create_time'] > 30)
            \Ouno\Ouno::dao('visit', 'index')->db->insert($data);
        $keyword = addslashes($info['keyword']);
        $where = "info like '%$keyword%'";
        $count = \Ouno\Ouno::dao('visit', 'index')->count($where);
        $data['count'] = $count['0']['count'];
		return  $data;
    }


	/**
	 * 分页类
	 * @param int $count
	 */
	public function page($count, $str = '') {
		
	}

	public function mapreduce(){
		return \Ouno\Ouno::dao('article', 'index')->mapreduce();

	}






}


?>