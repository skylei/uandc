<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/29
 * Time: 18:23
 */
namespace Ouno\Core\DB;
class OunoMysql  extends \Ouno\BaseComponent{

    private static $db;
    public $linkw;
    public $linkr = null;
    public static $_instance;
    public $table;
    public $lastSql;
    public $errotStr;
    public $config;

    /*
     * 初始化数据库连接
     * @param $config array
     * */
    public function __construct($config){
        $this->config = $config;
        $this->connectRouter();
    }

    public function connectRouter(){
        if(count($this->config) == 1){
            $this->linkr = $this->connectM($this->config);
        }else{
            $this->connectM($this->config);
            $this->connectS($this->config);
        }
    }
    public static function getInstance($config){
        if(self::$_instance == null){
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }

    public function connectM($config){
        if($config[0]['PCONNECT']) {
            $this->linkw = mysql_connect($config[0]['HOST'], $config[0]['USERNAME'], $config[0]['PASSWORD'], 1);
        }else {
            $this->linkw = mysql_connect($config[0]['HOST'], $config[0]['USERNAME'], $config[0]['PASSWORD']);
        }
        mysql_query('SET NAMES ' . $config[0]['CHARSET'], $this->linkw);
        mysql_select_db ( $config[0]['DBNAME'] ,  $this->linkw) or die ( "Can not  use {$config[0]['DBNAME']} : '" . $this->error());
        return $this->linkw;
    }

    public function connectS($config){
        $i = mt_rand(1, count($config));
        if($config[$i]['PCONNECT'])
            $this->linkr = mysql_connect($config[$i]['HOST'], $config[$i]['USERNAME'], $config[$i]['PASSWORD'], 1);
        else
            $this->linkr = mysql_connect($config[$i]['HOST'], $config[$i]['USERNAME'], $config[$i]['PASSWORD']);
        mysql_query('SET NAMES ' . $config[0]['CHARSET'], $this->linkr);
        mysql_select_db ( $config[0]['DBNAME'] ,  $this->linkr) or die ( "Can not  use {$config[0]['DBNAME']} : '" . $this->error());
        return $this->linkr;
    }

    public function query($sql, $result = true, $all = true){
        $this->lastSql = $sql;
        $query = mysql_query($this->lastSql, $this->linkr);
        if($result)
            return $this->fetchResult($query, $all);
        else
            $this->error();
    }

    public function execute($sql, $slave = false){//默认主服务器执行
        $this->lastSql = $sql;
        $link = $slave ? $this->linkr : $this->linkw;
        $query = mysql_query($sql, $link);
        if($query){
            return mysql_affected_rows ($link);
        }else{
            $this->error();
        }
    }

    public function findAll($where = '',  $field = '*', $options = ''){
        $this->lastSql = $this->parseParam($where, $field, $options);
        return $this->query($this->lastSql);
    }

    public function findOne($where, $field = '*', $options = '' ){
        $sql = "SELECT " . $field . " FROM ". $this->table . " %s %s limit 0,1";
        if(is_array($where)) $where = $this->where($where);
        if(is_array($options)) $options = $this->parseOptions($options);
        $sql = sprintf($sql,$where, $options );
        return $this->query($sql, $all = true, $all = false);
    }



    public function insert($data){
        $insertSql = 'INSERT INTO %s (%s) VALUES( %s )';
        $fieldStr = '';
        $valueStr = '';
        foreach($data as $key=>$val){
            $fieldStr .= ' `' . $key . '`,';
            $valueStr .= "'$val',";
        }
        $this->lastSql = sprintf($insertSql, $this->table, rtrim($fieldStr, ','), rtrim($valueStr, ','));
        return $this->execute($this->lastSql, false);

    }

    public function insertMore( $values, $fields){
        $insertSql = 'INSERT INOT %s (%s) VALUES %s ';
        $valueStr = '';
        if(is_array($values)){
            foreach($fields as $key=> $field) {
                $valueStr .= '(' . implode(',', $values) . '),';
            }
        }else{
            $valueStr = $values;
        }
        if(is_array($fields)){
            $fieldStr = explode(',', $fields);
        }else{
            $fieldStr = $fields;
        }
        $insertSql = sprintf($insertSql, $fieldStr, $this->table, rtrim($valueStr));
        $this->query($insertSql, false);
        return mysql_insert_id();

    }

    public function delete($where){
        $where = $this->where($where);
        $this->lastSql = 'DELETE FROM ' . $this->table . ' WHERE ' .$where;
        return  $this->execute($this->lastSql, false);
    }

    public function update($data, $where){
        $setStr = ' SET ';
        foreach($data as $key=>$val){
            $setStr .= ' '. $key . '=' . "$val ,";
        }
        if(is_array($where)) $where = $this->where($where);
        $this->lastSql = 'UPDATE ' . $this->table . ' ' . rtrim($setStr, ',') . ' ' . $where;
        return  $this->execute($this->lastSql);
    }

    public function error(){

        $type = $this->linkw ? 'marster' : 'slave';
        \Ouno\OunoLog::logSql(mysql_error(), $this->table, $type);
    }

    public function findOneModify($where, $data, $options = ''){
        $result = $this->findOne($where, $filed = '*', $options);
        if($result)  $update = $this->update($data, $where);
        return  array('result'=>$result, 'update'=>$update);
    }

    public function trans_start(){}

    public function tracs_commit(){}

    public function escape($string){
        return mysql_escape_string($string);
    }

    public function getError(){}

    public function where($where){
        $whereStr = ' WHERE ';
        if(is_array($where)){
            foreach($where as $key=>$val){
                $connector =  isset($val['connector']) ? $val['connector'] : '';
                $operator = isset($val['operator']) ? $val['operator'] : '';
                $whereStr .= $key . " " . $operator . " " . "'" .$val['value'] . "'" . " " . $connector .' ';
            }
        }else{
            $whereStr .= $where;
        }
        return $whereStr;

    }

    public function fetchResult($query, $all = true){
        if(!$query) return false;
        $data = array();
        while($row = mysql_fetch_assoc($query )){
            if($all == false){
                $data = $row;
                break;
            }else{
                $data[] = $row;
            }
        }
        mysql_free_result($query);
        return $data;
    }

    public function parseParam($param, $fields ,$options){
        $sql = "SELECT %s FROM ". $this->table . " %s %s";
        $where = '';
        if($param){
            $where = $this->where($param);
        }
        $extras = ' ';
        if(!empty($options) && is_array($options)) {
            $extras .= $this->parseOptions($options);
        }else{
            $extras .= $options;
        }
        $fieldStr = ' ';
        if(is_array($fields))
            $fieldStr = implode(',', $fields);
        else
            $fieldStr .= $fields;
        return sprintf($sql, $fieldStr, $where, $extras);
    }

    public function parseOptions($options){
        $extras = ' ';
        foreach ($options as $key => $val) {
            $key = strtolower($key);
            if ($key == 'group') $extras .= $key . ' BY ' . $val['value'] . ' ';
            if ($key == 'order') $extras .= $key . ' BY ' . $val['value'] . ' ' . $val['sort'] . ' ';
            if ($key == 'limit'){
                $extras .= $key . ' ';
                $extras .= isset($val['offset']) ? $val['offset'] : 0 ;
                $extras .= ',';
                $extras .= isset($val['pagesize']) ? $val['pagesize'] : 20;
            }
        }
        return $extras;
    }



}