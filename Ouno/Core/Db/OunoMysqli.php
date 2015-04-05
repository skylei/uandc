<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/29
 * Time: 18:23
 */
namespace Ouno\Core\Db;
class OunoMysqli  extends \Ouno\BaseComponent{

    /*
     * @var 主服务的连接
     * */
    public $linkw;

    /*
     * @var 从服务器的连接
     * */
    public $linkr;

    /*
     * @var singleton 对象
     * */
    public static $_instance;
    
    public $table;

    /*
     * @var 最后执行的sql
     * */
    public $lastSql;
    //todo
    public $errotStr;

    /*
     * @var 数据库配置
     * */
    public $config;

    /*
     * 初始化数据库连接
     * @param $config array
     * */
    public function __construct($config){
        $this->config = $config;
        $this->connectRouter();
    }

    /*
     * 连接路由，支持主从结构的读写分离
     * */
    public function connectRouter(){
        if(count($this->config) == 1){
            $link = $this->connectM($this->config);
            if($link)
                $this->linkr = $this->linkw;
				$this->linkr->autocommit(true);
        }else{
            $this->connectM($this->config);
            $this->connectS($this->config);
        }

    }

    /*
     * 获取单例singleton
     * */
    public static function getInstance($config){
        if(self::$_instance == null){
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }

    /*
     * 主mysql库连接
     * @parma array $config
     * @return boolean
     * */
    public function connectM($config){
        $this->linkw =  new \mysqli($config[0]['HOST'], $config[0]['USERNAME'], $config[0]['PASSWORD'], $config[0]['DB']);
        if($this->linkw->connect_errno)
            $this->error($this->linkw->connect_error);
        if($config[0]['AUTO_COMMIT'])
            $this->linkw->autocommit(true);
        $this->linkw->set_charset($config[0]['CHARSET']);
        return true;
    }

    /*
     * 从服务器连接
     * @apram array $config
     * @return boolean
     * */
    public function connectS($config){
        $i = mt_rand(1, count($config));
        $this->linkr = new \mysqli($config[$i]['HOST'], $config[$i]['USERNAME'], $config[$i]['PASSWORD'], $config[$i]['DB']);
        if($this->linkw->errno)
            return $this->error($this->linkw->connect_error);
        if($config[$i]['AUTO_COMMIT'])
            $this->linkr->autocommit(true);

        $this->linkr->set_charset($config[$i]['CHARSET']);
        return true;
    }

    /*
     * 查询并返回单条记录
     * */
    public function queryRow($sql, $mode = MYSQLI_ASSOC){
        $this->lastSql = $sql;
        $query = $this->linkr->query($this->lastSql);
        if($this->linkr->errno)
            return $this->error($this->linkw->error);
        return $query->fetch_array($mode);

    }

    public function getListInserId($mode = 1){
        return $mode  ? $this->linkw->insert_id : $this->linkr->insert_id;
    }

    /*
     * 查询并返回结果
     * @param string $sql
     * @param boolean $result
     * @param int $mode
     * */
    public function query($sql, $result = true, $mode = MYSQLI_ASSOC){
        $this->lastSql = $sql;
        $query = $this->linkr->query($this->lastSql);
        if($this->linkr->errno)
            return $this->error($this->linkw->error);
        return $this->fetchResult($query, $mode);
    }

    /*
     * 执行一条sql语句
     * @param string $sql
     * @param boolean $slave
	 * @return boolean
     * */
    public function execute($sql, $slave = false){//默认主服务器执行
        $this->lastSql = $sql;
        $link = $slave ? $this->linkr : $this->linkw;
        $query = $link->real_query($sql);
        if($link->errno)
            $this->error($link->error);
        return $query;
    }

    /*
     * 查询所有
     * @param string $where
     * @param string $field
     * @param $options
     * */
    public function findAll($where = '',  $field = '*', $options = ''){
        $this->lastSql = $this->parseParam($where, $field, $options);
        return $this->query($this->lastSql);
    }

    /*
     * 查询单个
     * @todo
     * */
    public function findOne($where, $field = '*', $options = '' ){
        $sql = "SELECT " . $field . " FROM ". $this->table . " %s %s limit 0,1";
        if(is_array($where))
            $where = $this->where($where);
        if(is_array($options))
            $options = $this->parseOptions($options);
        $sql = sprintf($sql, $where, $options);
        return $this->queryRow($sql);
    }

    /*
     * 单条插入
     * @param array $data 数据键值对
     * @return @todo
     * */
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

    /*
     * 批量插入
     * @param mixed $values
     * @param mixed $fileds
     * @return int
     * */
    public function insertMore( $values, $fields, $slave = false){
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

        $this->lastSql = sprintf($insertSql, $fieldStr, $this->table, rtrim($valueStr));
        $this->execute($this->lastSql, $slave);
        $link = $slave ? $this->linkr : $this->linkw;
        return $link->insert_id;

    }

    /*
     * 删除操作
     * @param mixed $where
     * @param boolean $slave
     * */
    public function delete($where, $slave = false){
        $where = $this->where($where);
        $this->lastSql = 'DELETE FROM ' . $this->table . ' WHERE ' .$where;
        return  $this->execute($this->lastSql, $slave);
    }

    /*
     * 更新操作
     * @param array $data 键值对
     * @param mixed $where
     * @return
     * */
    public function update($data, $where, $slave = false){
        $setStr = ' SET ';
        foreach($data as $key=>$val){
            $setStr .= ' '. $key . '=' . "$val ,";
        }

        if(is_array($where)) $where = $this->where($where);
        $this->lastSql = 'UPDATE ' . $this->table . ' ' . rtrim($setStr, ',') . ' ' . $where;
        return  $this->execute($this->lastSql, $slave);
    }

    /*
     * 记录mysql错误
     * @param string $string
     * @return void
     * */
    public function error($error){

        $type = $this->linkw ? 'marster' : 'slave';
        \Ouno\OunoLog::logSql($error,  $this->table, $type);
    }

    /*
     * 查到并修改
     * */
    public function findOneModify($where, $data, $options = ''){
        $result = $this->findOne($where, $filed = '*', $options);
        if($result)  $update = $this->update($data, $where);
        return  array('result'=>$result, 'update'=>$update);
    }

    /*
     * 开启事务
     * */
    public function trans_start($mode = 1){
        switch($mode){
            case "1" : $this->linkw->autocommit(false);
                break;
            case "2" : $this->linkr->autocommit(false);
                break;
            default :
                $this->linkw->autocommit(false);
                $this->linkr->autocommit(false);
        }
    }

    /*
     * 提交事务
     * */
    public function commit($mode = 0){
        switch($mode){
            case "1" : $this->linkw->commit();
                break;
            case "2" : $this->linkr->commit();
                break;
            default :
                $this->linkw->commit();
                $this->linkr->commit();
        }

    }

    public function rollback($mode){
        switch($mode){
            case "1" : $this->linkw->rollback();
                break;
            case "2" : $this->linkr->rollback();
                break;
            default :
                $this->linkw->rollback();
                $this->linkr->rollback();
        }
    }
	
	public function close($link){
		$link->close();
	}

    /*
     * 转意特殊字符
     * @param $link
     * */
    public function escape($link){
        return $link->real_escape_string();
    }

    public function getError(){

    }


    /*
     * 解析where条件
     * @param mixed $where
     * @return string
     * */
    public function where($where){
        $whereStr = ' WHERE ';
        if(is_array($where)){
            foreach($where as $key=>$val){
                $connector =  isset($val['connector']) ? $val['connector'] : '';
                $operator = isset($val['operator']) ? $val['operator'] : '';
                $whereStr .= $key . " " . $operator . " '" .$val['value'] . "' " . $connector .' ';
            }
        }else{
            $whereStr .= $where;
        }
        return $whereStr;

    }

    /*
     * 遍历结果返回数组
     * @param object $query
     * @return array | false
     * */
    public function fetchResult($query, $mode){
        if(!$query) return false;
        $data = array();
        while($row =  $query->fetch_array($mode)){
            $data[] = $row;
        }
        $this->free($query);
        return $data;
    }

    /*
     * 释放结果集
     * */
    public function free($query){
        $query->free();
    }

    /*
     * 解析sql 参数
     * @param string $param
     * @return string
     * */
    public function parseParam($param, $fields ,$options){
        $sql = "SELECT %s FROM ". $this->table . " %s %s";
        $where = '';
        if($param){
            $where = $this->where($param);
        }
        $extras = ' ';
        if(is_array($options)&& !empty($options)) {
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

    /*
     * 解析选项数组
     * @param array $options
     * */
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