<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/29
 * Time: 18:23
 */
namespace Ouno\Core\DB;
class OunoMongo extends \Ouno\BaseComponent{
	
	protected $cursor = '';

    /*
     * 保存单利对象
     * @var static $_instance
     * */
    public static $_instance = '';
	public $db; //db mongodb对象数据库
	private $collection; //集合，相当于数据表 
	
	/**
	 * 初始化Mongo
	 * $config = array(
	 *      'POST' => ‘127.0.0.1' 服务器地址
	 *      'PORT'   => '27017' 端口地址
	 *      'OPTION' => array('connect' => true) 参数
	 *      'DBNAME'=> 'test' 数据库名称
	 *      'USERNAME'=> 'CRAB' 数据库用户名
	 *      'PASSWORD'=> '123456' 数据库密码
	 * )
	 */
	public function __construct($config = array()) {
		if ($config['PASSWORD'] == '') $config['PASSWORD'] = '123456';
		if ($config['USERNAME'] == '') $config['USERNAME'] = 'root';
		if ($config['HOST'] == '')  $config['HOST'] = '127.0.0.1';
		if ($config['PORT'] == '')  $config['PORT'] = '27017';
		if (!isset($config['OPTION'])) $config['OPTION'] = array('connect' => true);
		$server = 'mongodb://' . $config['HOST'] . ':' . $config['PORT'];
		$mongo = new \MongoClient($server, $config['OPTION']);
		if ($config['DBNAME'] == '') $config['DBNAME'] = 'test';
		$this->db = $mongo->selectDB($config['DBNAME']);
		if ($config['USERNAME'] != '' && $config['PASSWORD'] != '')
			$this->db->authenticate($config['USERNAME'], $config['PASSWORD']);
	}

    /*
     * 获得单例
     * */
    public static  function getInstance(){
        if(self::$_instance == null)
            self::$_instance = new self(\Ouno\Ouno::config('MONGO'));

        return self::$_instance;
    }
	
	/**
	 * 选择一个集合，相当于选择一个数据表
	 * @param string $collection 集合名称
     * @return mixed
	 */
	public  function collection($collection) {
		return $this->collection = $this->db->selectCollection($collection);
	}
	
	/**
	 * 新增数据
	 * @param array $data 需要新增的数据 例如：array('title' => '1000', 'username' => 'xcxx')
	 * @param array $option 参数
	 */
	public function insert($data, $option = array()) {
		return $this->collection->insert($data, $option);
	}
	
	/**
	 * 批量新增数据
 	 * @param array $data 需要新增的数据 例如：array(0=>array('title' => '1000', 'username' => 'xcxx'))
	 * @param array $option 参数
	 */
	public function batchInsert($data, $option = array()) {
		return $this->collection->batchInsert($data, $option);
	}
	
	/**
	 * 保存数据，如果已经存在在库中，则更新，不存在，则新增
 	 * @param array $data 需要新增的数据 例如：array(0=>array('title' => '1000', 'username' => 'xcxx'))
	 * @param array $option 参数
	 */
	public function save($data, $option = array()) {
		return $this->collection->save($data, $option);
	}
	
	/**
	 * 根据条件移除
 	 * @param array $query  条件 例如：array(('title' => '1000'))
	 * @param array $option 参数
     * @return array | true
	 */
	public function remove($query, $option = array()) {
		return $this->collection->remove($query, $option);
	}
	
	/**
	 * 根据条件更新数据
 	 * @param array $query  条件 例如：array(('title' => '1000'))
 	 * @param array $data   需要更新的数据 例如：array(0=>array('title' => '1000', 'username' => 'xcxx'))
	 * @param array $option 参数
	 */
	public function update($query, $data, $option = array()) {
		return $this->collection->update($query, $data, $option);
	}
	
	/**
	 * 根据条件查找一条数据
 	 * @param array $query  条件 例如：array(('title' => '1000'))
	 * @param array $fields 参数
	 */
	public function findOne($query = '', $fields = array()) {
		return $query ? $this->collection->findOne($query, $fields) : $this->collection->findOne();
	}
	
	/**
	 * 根据条件查找一条数据
 	 * @param array $query  条件 例如：array(('title' => '1000'))
	 * @param array $fields 参数
     * @return array |
	 */
	public function getOneById($_id, $fields = array()) {
		$_id = new mongoId($_id);
		return $this->collection->findOne(array('_id'=>$_id), $fields);
	}
	
	/**
	 * 根据条件查找多条数据
	 * @param array $query 查询条件
	 * @param array $sort  排序条件 array('age' => -1, 'username' => 1)
	 * @param int   $limit 页面
	 * @param int   $limit 查询到的数据条数
	 * @param array $fields 返回的字段
     * @return array
	 */
	public function findAll($query = '', $sort = array(), $skip = 0, $limit =2, $fields = array()) {
		$this->cursor = $query ? $this->collection->find($query, $fields) : $this->collection->find();
		if ($sort)   $this->cursor->sort($sort);
		if ($skip)  $this->cursor->skip($skip);
        if ($limit) $this->cursor->limit($limit);
		return iterator_to_array($this->cursor);
	}
	
	/**
	 * 数据统计
	 * @param array $query 统计条件
	 */
	public function count($query = array()) {
		return $this->collection->count($query);
	}

	/**
	 * 错误信息
	 */
	public function error() {
		return $this->db->lastError();
	}
	
	/**
	 * 获取集合对象
	 */
	public function getCollection() {
		return $this->collection;
	}
	
	/**
	 * 获取DB对象
	 */
	public function getDb() {
		return $this->db;
	}
	
	/**
	 * 释放结果游标
	 */
	public function free(){
		$this->cursor = null;
	}
	
	
	/*
	 * 分组
	 * @param array $key  按$key进行分组,形式：array('init'=>1)
	 * @param array $init  初始文档
	 * @param string $reduce js函数function (obj, prev)  obj指当前文档(对应当前collection),prev 初始文档(对应$init)
	 * @param array $options 更多选项，如查询条件...
	*/
	public function group($key, $init, $reduce, $options = array()){
		return $this->collection->group($key, $init, $reduce, $options);
	}
	
	 /**
     * 执行命令 mongo 所有CURD 都可以通过command来实现
     * @param $cmd array 指令 example： array("distinct" => "collectionName", "key" => "age") 按键名age去重复查找
     * @return unkwon
	 * 
     */
    public function command($cmd){
        return $this->db->command($cmd);
		
    }
	
	 /**
     * 查找并更改
     * @ param array 查询条件
	 * @ parm array 更新的数据
     * @ param array $field  返回的字段 
     * @ return array 返回的结果集
     */
	public function findAndModify($query, $data = array(), $options = array()){
		return $this->collection->findAndModify($query, $data, $options);
	
	}
	
	/**
	 * mapreduce 方法
	 * @param string  $map  js function 映射函数
	 * @param string $reduce js function 统计处理函数
	 * @param string outputCollection 统计结果存放集合
	 * @param array  $query 过滤条件 如：array('uid'=>123)
     * @param array  $sort 排序
     * @param number $limit 限制的目标记录数
     * @param bool   $keeptemp 是否保留临时集合
     * @param string $finalize 最终处理函数 (对reduce返回结果进行最终整理后存入结果集合)
     * @param string $scope 向 map、reduce、finalize 导入外部js变量
     * @param bool   $jsMode 是否减少执行过程中BSON和JS的转换，默认true(注：false时 BSON-->JS-->map-->BSON-->JS-->reduce-->BSON,可处理非常大的mapreduce,//true时BSON-->js-->map-->reduce-->BSON)
     * @param bool   $verbose 是否产生更加详细的服务器日志
     * @return mixed
	 */
	
	public function mapReduce($map, $reduce, $outputCollection = '',$query = null, $sort = null, $limit = null, $keeptemp = true, $finalize = null, $scope = null, $jsMode = true, $verbose = true){
		if(empty($map) || empty($reduce)) return false;
		if(empty($outputCollection)) $outputCollection = 'tmp_mr_res_'.$this->collection->getName();
		$cmd = array('mapreduce'=>$this->collection->getName(),
					'map'=>$map,
					'reduce'=>$reduce,
					'out'=>$outputCollection);
	    if(!empty($query) && is_array($query)){
			$cmd['query'] = $query;
		}
		if(!empty($sort) && is_array($sort)){
			$cmd['sort'] = $query;
		}
		if(!empty($limit) && is_int($limit) && $limit>0){
			$cmd['limit'] = $limit;
		}
		if(!empty($keeptemp) && is_bool($keeptemp)){
			$cmd['keeptemp'] = $keeptemp;
		}
		if(!empty($finalize)){
			$finalize = new Mongocode($finalize);
			$cmd['finalize'] = $finalize;
		}
		if(!empty($scope)){
			$cmd['scope'] = $scope;
		}
		if(!empty($jsMode) && is_bool($jsMode)){
			$cmd['jsMode'] = $jsMode;
		}
		if(!empty($verbose) && is_bool($verbose)){
			$cmd['verbose'] = $verbose;
		}
		$cmdResult = $this->command($cmd);
		if($cmdResult['ok'] == 1){
			$this->selectCollection($outputCollection);
			$result = $this->find(); 	
		}else{
			return false;
		}
		if(isset($keeptemp) && $keeptemp==false) $this->mongo->$dbname->dropCollection($out);//删除集合
		return $result;
	}
	
	
	/**
	 * 按$key去重复
	 *	@param string 键名
     * 	@param array $query 查询条件
     *  @return array | false
	 */
	public function distinct($key, $query = array()){
		return $this->collection->distinct($key, $query);
	}

    /*
     * 执行js代码
     * @return unkwon
     * */
    public function execute($code , $args = array()){
        $code = 'return ' . $code;
        return $this->db->execute($code, $args);
    }

	
}