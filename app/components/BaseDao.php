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
        if($this->table != '')
            $this->db->table = $this->table;
    }

    public function count($where = ''){//通过
        $where = $where ?  $where : '';
        $sql = "select count(*) as count from " . $this->table . $where;
        return $this->db->queryRow($sql);
    }

}