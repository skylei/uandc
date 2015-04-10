<?php
namespace src\dao\index;
class articleDao extends \components\BaseDao{
    public $table = 'crab_article';

    //按key去重
    public function distinct($key, $query=array()){
        $result = $this->dao->distinct($key, $query);
        return $result;
    }


}