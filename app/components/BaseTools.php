<?php
namespace components;
class BaseTools {

    /*
     * 上传图片允许类型
     * @type string $type image type
     * @return boolen
     * */
	public static function allow_img_type($type){
        $type = substr($type, strrpos($type, '/') + 1);
        $alist = array('jpg', 'jpeg', 'gif', 'png');
        if(!in_array($type, $alist))
            return false;
        return true;
    }

    /**
     * 获取客户端IP
     * @return unknown_type
     */
    public static function getIP()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return $ip;
    }

}






