<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/3/5
 * Time: 21:35
 */
namespace web\controller\ucenter;
use components\Myconstant;

class imageController extends \components\BaseUcenterController
{

    /*
     * 显示上传图片模版
     * */
    public function indexAction(){
        $data['basePath'] = $this->getBasePath();
        $this->assign('data', $data);
        $this->setTpl('index');
        $this->show();
    }

    /*
     * 处理上传图片
     * */
    public function uploadAction(){
        if(!isset($_FILES['fileList'])) return false;
        $temp = $_FILES['fileList'];
        $mservice = new \src\service\ucenter\mongoService();
        $albumId = $this->_post("album", '');
        $album = array();
        if(!$albumId){
            //创建默认相册
            $result = $mservice->createDefaultAlbum();
            var_dump($result);
            $album['name'] = isset($result['name']) ? $result['name'] : 'default';
        }else{
            $album = $mservice->getalbumById($albumId);
            $album['name'] =  !empty($album['name']) ? $album['name'] : 'default';
        }

        $check = \components\BaseTools::allow_img_type($temp['type']);
        if(!$check)
            $this->ajax_return(false, 'image type is incorrect!', 400);
        if($temp['error'] != 0)
            $this->ajax_return(flase, 'image upload has some wrong!');
        $preFilename = Myconstant::IMG_PREFIX . time(). mt_rand(1000, 9999);
        $path = '/static/images/ablum/' . $album['name'] . '/' ;
        $basePath = APP_PATH . $path;
        $thumbPath = $basePath . 'thumb/';
        if(!is_dir($basePath))
            @mkdir($basePath, 0777);
        if(!is_dir($thumbPath))
            @mkdir($thumbPath, 0777);
        $postfix = substr($temp['type'], strrpos($temp['type'],'/') + 1);
        $filename =  $preFilename . '.'. $postfix;
        $prevThumbFilename = $preFilename . '_thumb';
        $thumbPostfix = ($postfix == 'jpeg') ? '.jpg' : '.' . $postfix;
        $thumbFilename =  $prevThumbFilename . $thumbPostfix;
        $file = $basePath . $filename;
        $thumbFile = $thumbPath . $thumbFilename;
        $storeInfo = array(
            'filename'=> $path . $filename,
            'thumbfile'=> $path . 'thumb/' . $thumbFilename,
            'create_time'=> time(),
            'update_time'=> new \MongoDate ()
        );
        $storeInfo['album'] = $album['name'];
        $gfs = $mservice->getDao('image')->getGridFS();
        $mid = $gfs->storeFile($temp['tmp_name'], $storeInfo);
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

        $truePath = $file;
        $showPath = 'http://www.uandc.cn/index.php/image/pic/show/id/' . $id;
        //保险起见保留一张原图
        move_uploaded_file($temp['tmp_name'], $truePath);
        \Ouno\Ouno::import("/extensions/Library/image.init.php");
        $image = new \imageInit();
        $result = $image->make_thumb($truePath, $thumbPath . $prevThumbFilename,$width = 200,$height = 200);
        var_dump($result);

        $return = array('file_path'=>$showPath);
        $truePath = APP_PATH . $file;
        $showPath = 'http://www.uandc.cn/index.php/image/pic/show/id/' . $id;
        //保险起见保留一张原图
        move_uploaded_file($temp['tmp_name'], $truePath);

        $return = array('success'=>true, 'msg'=>'upload success!', 'file_path'=>$showPath);
//        $return = array('success'=>true, 'msg'=>'upload success!', 'file_path'=>$truePath);
        echo json_encode($return);
        exit;

    }

    public function testAction(){
        $this->setTpl('upload');
        $this->show();
    }

    public function testUploadAction(){
        if(!isset($_FILES['fileList'])) return false;
        $temp = $_FILES['fileList'];
        \Ouno\Ouno::import("/extensions/Library/image.init.php");
        $image = new \imageInit();
        var_dump($image);
        $result = $image->make_thumb($temp['tmp_name'], $width=200,$height=200);
    }

    /*
     * ajax 获取相册
     * */
    public function albumAction(){
        $mservice = new \src\service\ucenter\mongoService();
        $albums = $mservice->getAlbum();
        $html = '<select name="album" class="upload-album">';
        if($albums){
            foreach($albums as $key=>$val){
                $html .= '<option value="'. $key .'">' . $val['name']. '</option>';
            }
        }else{
            $html .= '<option value="">defalut</option>';
        }

        $html .= '</select>';
        echo $html;
        exit;
    }
}