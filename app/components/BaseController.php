<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/30
 * Time: 16:05
 */
namespace components;
class BaseController extends \Ouno\Controller {

    protected $data = array();

    protected function _get($param, $default = ''){
        if(!empty($_GET[$param]))
            return addslashes($_GET[$param]);
        else
            return $default;
    }

    /*
     *
     * @param $param mix
     * @return mix
     * */
    protected function _post($param, $default = ''){
        if(is_array($param)){
            foreach($param as $key=>$val){
                if(!empty($_POST[$val]))
                   $data[$val] = addslashes($_POST[$val]);
            }
        }else{
            if(!empty($_POST[$param]))
                $data = addslashes($_POST[$param]);
        }
        return !empty($data) ? $data : $default;
    }

    /*
     * url专挑
     * */
    public function redirect($url = ''){
        if(!$url){
            $jump = "<script>";
            $jump .= "window.history.back(-1);";
            $jump .= "</script>";
        }else{
            $url = $this->createUrl($url);
            $jump = "<script>";
            $jump .= "window.location.href='$url';";
            $jump .= "</script>";
        }

        echo $jump;
    }

    /**
     * 返回ajax数据
     * @param mixed $data
     * @param boolean $isSucc
     * @param int $code
     */
    public function ajax_return($isSucc, $data = '', $code = 0){
        $callback = isset($_GET['callback']) ? $_GET['callback'] : '';
        $result = json_encode ( array ('status' => $isSucc, 'data' => $data , 'code' => $code) );
        if ($callback) {
            $result = $callback . '(' . $result . ')';
        }
        echo $result;
        exit;
    }

    /*
     * 获取index.php相对www目录的路径
     * */
    public function getBaseUrl(){
        return \Ouno\Ouno::config('BASEURL') . '/index.php';
    }

    /*
     * 获取根目录
     * */
    public function getBasePath(){
        return  \Ouno\Ouno::config('BASEURL');
    }



}