<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2014/10/29
 * Time: 22:52
 */
namespace components;
class BaseDao extends \Ouno\Dao{
    public  $table = '';

    public function __construct(){
        parent::__construct();
    }

    public function count($where = ''){//通过
        $where = $where ?  ' WHERE ' . $where : '';
        $sql = "select count(*) as count from " . $this->table . $where;
        return $this->dao->queryRow($sql);
    }

}