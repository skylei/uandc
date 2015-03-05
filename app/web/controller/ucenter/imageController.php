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
        $showPath = 'http://www.uandc.cn/index.php/image/pic/show/id/' . $id;
        //保险起见保留一张原图
        move_uploaded_file($temp['tmp_name'], $truePath);

        $return = array('success'=>true, 'msg'=>'upload success!', 'file_path'=>$showPath);
//        $return = array('success'=>true, 'msg'=>'upload success!', 'file_path'=>$truePath);
        echo json_encode($return);
        exit;

    }

}