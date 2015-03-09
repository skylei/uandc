<?php
/**
 * picture-show controller
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/2/27
 * Time: 21:23
 */
namespace web\controller\image;
class imageController extends \components\BaseController {

    public function run(){

    }

    /*
     * 显示上传图片模版
     * */
    public function indexAction(){
        $basepath = $this->getbasePath();
        $iservice = new \src\service\image\mongoService();
        $albums = $iservice->getAllAlbums();
        $images = array();
        if($albums){
            foreach($albums as $key=>$val)
            {
                $temp = $iservice->getAlbumImages($val['name'], $limit = 4);
                $images[] = $temp;
            }
        }

        $this->assign('basePath', $basepath);
        $this->assign('images',$images);
        $data['basePath'] = $this->getBasePath();
        $this->assign('data', $data);
        $this->setTpl('image');
        $this->show();
    }

    /*
     * 获取图片列表，按照时间倒序，显示缩略图缓存
     *
     * */
    public function pictureListAction(){
        $page = $this->_get('page');
        $pageSize = 30;
        $iservice = new \src\service\image\mongoService();
        $imageList = $iservice->getnew();
        $domain = \Ouno\Ouno::$_config['BASEURL'];
        foreach($imageList as $key=>&$val){
            $val['img_path'] = $domain . '/' . $val['filename'];
            if(!is_file($val['img_path']))
                $val['img_path'] = BaseTools::createThumb($val['img_path']);
        }



    }

    /*
     * 显示图片
     * */
    public function showAction(){
        $id = $this->_get('id');
        $result = $this->getIService()->getOneChunk(array('files_id'=>new \mongoId($id)));
        header ( 'Content-Type: image/jpeg' );
        echo $result['data']->bin;
        exit;
    }

    /*
     * 获取推荐图片
     * */
    private function getRecommend(){


    }

    private function getIService(){
        return new \src\service\image\mongoService();
    }
}