<?php
namespace components;
class BaseTools {

    /*
     * 上传图片允许类型
     * @type string $type image type
     * @return boolen
     * */
	public static function allow_img_type($type){
        $alist = array('jpg', 'jpeg', 'gif', 'png');
        if(!in_array($type, $alist))
            return false;
        return true;
    }

}






