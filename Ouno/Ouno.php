<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/30
 * Time: 16:05
 */
namespace Ouno;

/*
 * 基类，核心文件均继承自该类
 * */

use Ouno\Core\DB\OunoMysql;

class BaseComponent{

    /*
     * @var $_container array
     * */
    public $container = array();

    /*
     * @var $event user define event
     * */
    public $event = array();


//    public function __construct() {
//
//        $this->run();
//    }

    /*
     *默认运行方法，该方法保护
     * */
//    protected function run(){}
//
    public function __set($key, $value){
        $set = 'set' . $key;
        if (method_exists($this, $set)) {
            return $this->$set($value);
        }else if(strncasecmp($key,'on',2)===0 && method_exists($this,$key)){
            $key = strtolower($key);
            if(!isset($this->event[$key]))
                $this->event[$key] = new OunoList();
            return $this->event[$key]->set($key, $value);
        }
    }
//
    public function __get($key)
    {
        $get = 'get' . $key;
        if (method_exists($this, $get)) {
            return $this->$get;
        } else if (isset($this->container[$key])){
            return $this->container[$key];
        }else if(strncasecmp($key,'on',2)===0 && method_exists($this,$key)){
            return $this->event[$key];
        }
    }


//
//    public function __toStrings(){
//
//    }

    private function __clone(){}



}

/*
 *列表类，实现
 *
 * */
class OunoList implements \ArrayAccess, \Countable, \IteratorAggregate{

    /*
     * @var property $data
     * */
    protected $data = array();

    /*
     * 构造函数 给data属性赋值
     * @param $data array
     * */
    public function __construct($data = array()){
        $this->data = $data;
    }


    /**
     * 给data 设置键对应值
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * ArrayAccess 接口
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
    public function offsetUnset($offset)
    {
       unset($this->data[$offset]);
    }

    /**
     * Countable 的接口实现
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }


    /*
     * 添加到迭代器中
     * @param $key string
     * @param $value mix 如果 data 中不存在则返回默认
     * @return mixed
     * */
    public function get($key, $defaul = null){
        if($this->has($key)){
            $enable = is_object($this->data[$key]) && method_exists($this->data[$key], '_invoke');
            $enable ? $this->data[$key]($this) : $this->data[$key];
        }
        return $defaul;
    }

    /*
     *
     * */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function instance($key, $value)
    {
        $this->set($key, function ($config) use ($value) {
            static $object;
            if (null === $object) {
                $object = $value($config);
            }
            return $object;
        });
    }

}
/*
 * 核心类，主要文件
 *
 * */
class Ouno extends BaseComponent{

	public static $APP_PATH = 'app';
	
    private static $includePath = array();
    /*
     * @var array $_import
     * */
    public static $_import = array();

    /*
     * @var instance $_instance application object
     * */
    public static $_instance = null;

    /*
     * @var autoload classes
     * */
    public static $_classes = array();

    /*
     * @var array $_config
     * */
    public static $_config = array();


    public static function config($key, $value = ''){
        if($key && $value === ''){
            if(is_string($key))
                return isset(self::$_config[$key]) ? self::$_config[$key] : $value;
            else if (is_array($key))
                self::$_config = array_merge(self::$_config, $key);
        }else if($key && $value){
            self::$_config[$key] = $value;
            return self::$_config[$key];
        }
        return null;
    }
	
    /**
     * 获取单例
     * @param $config
     * @return object self
     */
    public static function getInstance(){
        if(!(self::$_instance['\\Ouno\\Ouno'] instanceof self)){
            self::$_instance['\\Ouno\\Ouno'] = new self();
        }
		
        return self::$_instance['\\Ouno\\Ouno'];
    }


    /*
     * 运行框架
     * */
    public function run($app_path, $config = 'default'){
        self::setAppPath($app_path);
        $config = include_once($app_path . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $config . ".php");
        self::config($config);
	    if(self::config('SESSION'))
        	session_start();
        Ouno::$_classes = array(
            "Ouno\\Core\\Db\\OunoMysql"=> __DIR__ . "/Core/Db/OunoMysql.php",
			"Ouno\\Core\\Db\\OunoMysqli"=> __DIR__ . "/Core/Db/OunoMysqli.php",
            "Ouno\\Core\\Db\\OunoMongo"=> __DIR__ . "/Core/Db/OunoMongo.php",
            "Ouno\\Core\\Db\\AbstractDb"=> __DIR__ . "/Core/Db/AbstractDb.php",
 	        "Ouno\\Cache\\Oredis"=> __DIR__ . "/Cache/Oredis.php",
        );

        //$this->init2Ehandle();
		if(self::config('EXCEPTION_HANDLE', true))
            set_exception_handler(array('\Ouno\Ouno', 'handleException'));
        if(self::config('ERROR_HANDLE', true))
            set_error_handler(array('\Ouno\Ouno','handleError'),error_reporting());

        spl_autoload_register(array('self', 'loader'));
        if(PHP_SAPI == 'cli'){
            global $argv;
            //@TODO
            $console = new Console($argv);
            $this->container = $console->container;
        }else {
            if (self::config('URI') == 'PATH'){
				$this->getRequest();
			}
            if(self::config('MODULE', true))
                $this->container['module'] = $_GET['m'] = isset($_GET['m']) ? $_GET['m'] : 'index';
            $this->container['controller'] = $_GET['c'] = isset($_GET['c']) ? $_GET['c'] : 'index';
            $this->container['action'] = $_GET['a'] = isset($_GET['a']) ? $_GET['a'] : 'index';
            //@todo
            $controllerNamespace = self::config('CONTROLER_NAMESPACE', '\\web\\controller');
			$this->container['controllerClass'] = $controllerNamespace . '\\' . $this->container['module']
                . '\\' .$this->container['controller'] .'Controller';
		}
        if(!class_exists( $this->container['controllerClass'])){
            if(Ouno::config('DEBUG'))
                throw new \Exception("controller " . $this->container['controllerClass'] . " inexistance");
            else
                OunoError::error404();
        }

        $controller = new $this->container['controllerClass'];
        self::$_instance[$this->container['controllerClass']] = $controller;
        $this->container['action'] =  $this->container['action'].'Action';
        if(!method_exists($controller,  $this->container['action']) ){
            OunoError::error403();
        }
		
		call_user_func(array($controller, $this->container['action']));
        if(method_exists($controller, 'run_after'))
            call_user_func(array($controller, 'run_after'));
    }

    /*
     * @param $callback 注册全局变量
     * */
    public static function registerAutoloader($callback){
        spl_autoload_unregister(array('self', 'loader'));
        spl_autoload_register($callback);
        spl_autoload_register(array('self', 'loader'));
    }

    public static function registerInstance($key, $instance){
        if(!isset(self::$_instance[$key]))
            self::$_instance[$key] = $instance;
    }

    /*
     * 销毁挂载实力
     * @param $key string
     * */
    public static function unregisterInstance($key){
        if(isset(self::$_instance[$key]))
            unset(self::$_instance[$key]);
    }

    /*
     * @desc 自动加载类，依赖于配置文件
     * @param $className 加载的类名，文件名需和类名一致
     * @retrun include file;
     * */
    public static function loader($className){
        if(isset(self::$_classes[$className]) ){
            if(!isset(self::$_import[$className]) && file_exists(self::$_classes[$className]))
                include(self::$_classes[$className]);
        }else{
            if (strpos($className, '\\') !== false) {
                $classFile = self::$APP_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, ltrim($className, '\\')) . '.php';
		if (is_file($classFile) && !isset(self::$_import[$className]))
                    include($classFile);

            }else{//路径
                self::$includePath = Ouno::config('AUTO_LOAD_PATH');//array
                $include = '';
                foreach (self::$includePath as $name => $path) {
                    $include .= $path . PATH_SEPARATOR ;
                }

                set_include_path($include . get_include_path());
                include($className . '.php');
            }
        }
    }

	public static function setAppPath($app_path){
		self::$APP_PATH = $app_path;
	}

    public function getController($controller){
        if(isset(self::$_instance[$controller]))
            return self::$_instance[$controller];
        if(PHP_SAPI == 'cli')
            $controller = self::config("COMMAND_NAMESPACE") . '\\' . $controller;
        else
            $controller = self::config("CONTROLLER_NAMESPACE") .'\\' . $controller;

        if(!class_exists($controller))
            if(Ouno::config('DEBUG'))
                throw new \Exception("controller " . $this->container['controllerClass'] . " inexistance");
            else
                OunoError::error404();

        self::$_instance[$controller] = new $controller;
        return self::$_instance[$controller];
    }
	
	public static function getAppPath(){
		return self::$APP_PATH;
	}
	
	public static function addConfig($path){
		$newConfig = self::import($path);
		Ouno::config($newConfig);
	}
	
    /*
     * include加载，如果存在则加载
     * */
    public static function import($file){
        $file = self::$APP_PATH . $file;
        if(file_exists($file) && !in_array($file, self::$_import)){
            self::$_import[] = $file;
            include $file;
        }else{
            throw new \Exception("$file inexistance when Ouno::import");
        }

    }

    /*
     *初始化错误处理
     * */
    public function init2Ehandle(){
        if(self::$_config['EXCEPTION_HANDLE'])
            set_exception_handler(array('\Ouno\Ouno', 'handleException'));
        if(self::$_config['ERROR_HANDLE'])
            set_error_handler(array('\Ouno\Ouno','handleError'),error_reporting());
    }

    /*
    *设置PHP错误处理函数,写入日志文件
    */
    public static function handleError($errorCode, $msg = '', $errorFile = 'unkwon', $errorLine = 0){
        restore_error_handler();
        if($errorCode & error_reporting()){
            $error = isset( OunoLog::$_errorCode[$errorCode]) ?  OunoLog::$_errorCode[$errorCode] : 'other';
            $message = "#[ " .$error ."]";
            $message .= "#date : " . date('Y-m-d H:i:s', time());
            $message .= " #message:". $msg . " #file: ". $errorFile . "#line: ". $errorLine;
            $trace = debug_backtrace();
            $traceStr = '#(strace)';
            foreach(array_slice($trace, 1, Ouno::config('ERROR_LEVE', 5) ) as $key=>$val){
                $traceStr .= "#";
                $traceStr .= isset($val['file']) ?  ' file : ' .$val['file']  : '';
                $traceStr .= isset($val['class']) ?  ' ' .$val['class'] . '::' : '';
                $traceStr .= isset($val['function']) ? ' ' .$val['function'] . '()' : '';
                $traceStr .= isset($val['line']) ?  'line( '. $val['line'] .')' : '';
            }
            $message .=$traceStr;
            if(Ouno::config('ERROR_DISPLAY')) self::displayException($message);
            OunoLog::log($message);
        }

    }

    /*
    *设置异常处理函数,写入日志文件
    */
    public static function handleException($exception){
        restore_exception_handler();
        $message = "#Exception : " . $exception->__toString();
        $message .= '#date : ' .date('Y-m-d H:i:s', time());
        $class = get_class($exception);
        $message .='#exception : ' .$class;
        $message .= '#(trace)' . $exception->getTraceAsString();
        if(Ouno::config('EXCEPTION_DISPLAY')) self::displayException($message);
        OunoLog::log($class, $message);
    }

    /*
     * 打印错误
     * */
    public static function displayException($message){
        if(PHP_SAPI == 'cli')
            echo str_replace('#', "\n\r#", $message);
        else
            echo str_replace('#', '<br/>#', $message);
    }

    /*
     * @desc 获取dao 实例
     * @param $daoName string
     * @param $group string
     * @return instance
     * @throw dao inexistance
     * */
    public static function dao($daoName, $group = ''){
        $dao = Ouno::config('DAO_NAMESPACE', '\\src\\dao');
        if($group)
            $dao .= '\\' . $group;
        $dao .= '\\' . $daoName . 'Dao';
        if(class_exists($dao))
            return new $dao;
        else
            throw new \Exception("daogroup : $group daoname $dao inexistance");
    }


    /*
     * @desc 获取 service 实例
     * @param $serviceName string
     * @param $group string
     * @return instance
     * @throw service inexistance
     * */
    public static  function service($serviceName, $group = ''){
        $service = Ouno::config('SERVICE_NAMESPACE');
        if($group)
            $service .= '\\' . $group;
        $service .= '\\' . $serviceName . 'Service';
        if(class_exists($service))
            return new $service;
        else
            throw new \Exception("service group : [$group] service name [$service] inexistance");
    }

    /**
     * 路由分发，获取Uri数据参数
     * 1. 对Service变量中的uri进行过滤
     * 2. 配合全局站点url处理request
     * @return string
     */
    private function getRequest() {
        $filter_param = array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e');
        $request = array();
        if(isset($_SERVER['PATH_INFO'])) {
            $path = str_replace($filter_param, '', $_SERVER['PATH_INFO']);
            $request = explode('/', trim($path, '/'));
        }

        $i = -1;
        if(self::config('MODULE')) {
            $_GET['m'] = !empty($request[++$i]) ? $request[$i] : 'index';
            unset($request[$i]);
        }

        $_GET['c'] = !empty($request[++$i]) ? $request[$i] : 'index';
        unset($request[$i]);
        $_GET['a'] = !empty($request[++$i]) ? $request[$i] : 'index';
        unset($request[$i]);

        if(count($request) >= 2){
            foreach($request as $k=>$val){
                if($k % 2 == 0)
                    $key[] = $val;
                else
                    $value[] = $val;
            }

            if(count($key) !== count($value))
                $value[] = '';

            if(count($key) === count($value)){
                $get = array_combine($value, $key);
                if (!empty($get)){
                    foreach ($get as $key => $value) $_GET[$key] = $value;
                }
            }
        }
    }

    public static function error($message){
        echo $message;exit;
        OunoLog::UserLog($message);
    }
}

/*
 * cli模式下的命令基类
 *
 * */
class Console extends BaseComponent{
        
     /**
      * @var $view 
      */
      public $controller;

    /*
     * @var $container
     * */
    public $container = [];
		
    /**
     * 构造函数，初始化视图实例，调用运行mode@TODO
     */
    public function __construct($argv){
        if($mode = Ouno::config('RUN_MODE'))
        {
            $param = isset($mode['PARAM']) ? $mode['PARAM'] : '';
            $runInstance = OFactory::getInstance($mode['CLASS'], $param);
            if(method_exists($runInstance, 'run'))
                $runInstance->run();
        }

        if(Ouno::config('MODULE', true))
            $this->container['module'] = isset($argv[1]) ? $argv[1] : 'index';
        $this->container['controller'] = isset($argv[2]) ? $argv[2] : 'index';
        $this->container['action'] = isset($argv[3]) ? $argv[3] : 'index';
        $controllerClass =  Ouno::config('COMMAND_NAMESPACE', '\\command') . '\\' . $this->container['module']
            . '\\' .$this->container['controller'] .'Controller';
        $this->container['controllerClass'] = $controllerClass;

    }

    /*
     * 默认前置方法
     * */
    public function run(){

    }
}


/*
 * @desc 控制器类，所有控制器均继承自该类
 * */
class Controller extends BaseComponent{
    /**
     * 视图实例
     * @var $_view
     */
    public $_view;
    /*
     * @var $tpl 模板文件属性
     * */
    protected $tpl = '';
    /*
     * @var $display 是否显示模板属性，true则直接输出，fasle则返回
     * */
    public $display = true;

    public $baseUrl = '';

    /**
     * 构造函数，初始化视图实例，调用
     */
    public function __construct(){
        if(PHP_SAPI != 'cli' || Ouno::config('ENABLE_VIEW')) {
            //模板引擎用户可自定义模板引擎
            if (!Ouno::config('VIEW')) {
                $this->_view = OunoView::getInstance();
            } else {
                $view = Ouno::config('VIEW');
                $this->_view = new $view;
            }

            $this->baseUrl = Ouno::config('BASEURL');
        }

        $this->run();
    }

    public function run(){}

    /*
     * 后置运行方法，默认加载，可自行重写改方法
     * */
    public function run_after(){}

    /*
     * 创建url
     * @param string $url
     * @param array $param
     * @param string $type url or path
     * */
    public function createUrl($url = '',$param = array(),  $type = 'url'){
        $baseUrl = explode('index.php', $_SERVER['PHP_SELF']);
        $newUrl = $this->baseUrl;
        $paramStr = '';
        if($type == 'url'){
            $newUrl .= $baseUrl[0];
			$newUrl .= Ouno::config('REWRITE') ? '' : 'index.php';
        }else {
            $newUrl .= rtrim($baseUrl[0], '/');
        }
		
        if(!empty($param) && is_array($param)){
            foreach($param as $key => $val){
                $paramStr .= '/' . $key . '/' . $val;
            }
        }
		
        $newUrl .= rtrim($url, '/') . $paramStr;
        $newUrl =  rtrim($newUrl, '/');
        return $newUrl;
    }

    /*
     * 向视图抛掷变量
     * @param string $var 变量名
     * @param mix $value
     * */
    public function assign($var, $value){

        $this->_view->assign($var, $value);
    }

    public function clearSmartyCache(){
        $this->_view->cleanCache();
    }

    /*
     * 设置渲染模版
     * @param string $file
     * */
    public function setTpl($file){//false返回不输出
        if(Ouno::config('MODULE'))
            $file = $_GET['m'] . DIRECTORY_SEPARATOR . $_GET['c'] .DIRECTORY_SEPARATOR . $file . Ouno::config('VIEW_POSTFIX');
        else
            $file =  $_GET['c'] .DIRECTORY_SEPARATOR . $file . '.' . Ouno::config('VIEW_POSTFIX');
        $realFile = Ouno::$APP_PATH . Ouno::config('TEMPLATE_PATH'). DIRECTORY_SEPARATOR . $file;
        if(!file_exists($realFile))
            Ouno::error($realFile . 'template not exist');
        $this->tpl = $file;
    }

    /*
     * 向浏览器渲染模版
     * */
    public function show(){
        if($this->display) {
            $this->_view->display($this->tpl, $this->display);
        }else{
            return $this->_view->display($this->tpl, $this->display);

        }

    }

}

/*
 * @desc Ouno 默认模板引擎，采用原生php
 * */
class OunoView extends BaseComponent{


    /*
     * @var $data
     * */
    protected $data =array();

    /*
     * @var $tpl 模板文件
     * */
    public $tpl;

    /*
     * @var $show 是否输出模板
     * */
    protected $show = true;

    /**
     * @var string
     */
    public $fileExtension = '.php'; // or ".php" if you like

    /*
     *
     * */
    public function __construct(){
        $this->fileExtension = Ouno::config('VIEW_POSTFIX');
    }

    public static function getInstance(){
        if(!( Ouno::$_instance['OunoView'] instanceof self)){
            Ouno::$_instance['OunoView'] = new self();
        }
        return Ouno::$_instance['OunoView'];
    }

    /*
     * 向模板抛掷变量
     * @param $var string 变量名
     * @param $value mixed 变量值
     * @return void
     * */
    public function assign($var, $value){
        $this->data[$var] = $value;
    }


    /*
     * 显示模板
     * @param $file string
     * */
    public function display($file){
        $this->fetch($file);
        extract($this->data);
        include_once($this->tpl);
    }

    /*
     * 生成静态html文件
     * @param $file string 模板名
     * */
    public function createStaticFile($file){
        ob_start();
        $this->display($file);
        $template = ob_get_contents();
        ob_end_clean();#清空缓存
        $file = Ouno::$APP_PATH . Ouno::$_config['VIEW_STATIC_PATH'] . '/' . $file . $this->fileExtension;
        file_put_contents($file, $template);
    }

    /*
     * 在模板文件中include其他部分文件
     * */
    public function includeTpl(){

    }

    /*
     * layout
     * */
    public function layOut($name){
// $layOutPath = Ouno::$_config['VIEW_LAYOUT_PATH'];
    }

    /*
     * 将模板内容返回，相对于display的直接输出
     * @param $file string
     * */
    public function fetch($file){
        $this->tpl = Ouno::$APP_PATH . rtrim(Ouno::config('TEMPLATE_PATH', '/web/template'), '/') 
			. '/' . $file . $this->fileExtension;
        $template = str_replace(array("\n\r", "\t", " "), '', file_get_contents($this->tpl));
        return $template;
    }



    public function setTpl($tplName){
        $this->tpl = Ouno::config('TEMPLATE_PATH') . $tplName;
    }


}

/*
 * 数据库操作接口
 * */
class Dao extends  BaseComponent
{

    public $db = null;
    public $table = '';
    public $is_slice = false;

    public function __construct()
    {
        $driver = Ouno::config('DB_DRIVER', 'OunoMysqli');
        $namespace = 'Ouno\\Core\\Db\\';
        $daoClass = $namespace . $driver;
        $this->db = new $daoClass(Ouno::config('DB'));
        $this->db->table = $this->table;
    }

}


class Service extends BaseComponent{
    protected $service;

    protected function run(){

    }

}


/**
 * 异常类
 */
class OunoException extends \Exception{



}


/*
 * @desc Ouno日志类
 * */
class OunoLog extends BaseComponent{

    /*
     * @var $autoFlush 文件大于改属性值时自动清空文件
     * */
    public static $autoFlush = 10000;

    private static  $_basePath ;
    public static $_errorCode = array(E_ERROR=> 'E_ERROR', E_WARNING=>'E_WARNING', E_PARSE =>'E_PARSE', E_NOTICE=>'E_NOTICE',
        E_CORE_ERROR=>'E_CORE_ERROR', E_CORE_WARNING=>'E_CORE_WARNING',E_COMPILE_WARNING=>'E_COMPILE_WARNING'
    );

    /*
     * @desc 记录系统错
     * @param $msg 信息
     * */
    public static function log($msg = ''){
        $filename = date('Y-m-d', time()). 'runOuno.log';
        if(!self::$_basePath) 
			self::$_basePath = Ouno::$APP_PATH . Ouno::config('LOG_PATH', '/runtime/log');
		if(!is_dir(self::$_basePath))
			mkdir(self::$_basePath, 0777, true);
		$file = self::$_basePath . '/' .$filename;
        if(file_exists($file) && filesize($file) > self::$autoFlush)
            system("echo '' > " . $file);
        file_put_contents($file, $msg, FILE_APPEND);
    }

    /*
     * @desc 记录sql错误
     * @param $message 错误信息
     * @param $table 数据表
     * @type $type 主从服务器
     * @return void
     * */
    public static function logSql($message, $table, $type){
        $errorStr = "[ SQL_ERROR ]";
        $errorStr .= "# table :" .$table;
        $errorStr .= "# type : $type";
        $errorStr .= '# message : '. $message;
        $trace = debug_backtrace();
        $traceStr = '#(trace)';
        foreach(array_slice($trace, 1, Ouno::config('ERROR_LEVE', 5) ) as $key=>$val){
            $traceStr .= '#';
            $traceStr .= isset($val['file']) ?  ' file : ' .$val['file']  : '';
            $traceStr .= isset($val['class']) ?  ' ' .$val['class'] . '::' : '';
            $traceStr .= isset($val['function']) ? ' ' .$val['function'] . '() ' : '';
            $traceStr .= isset($val['line']) ?  ' line( '. $val['line'] .')' : '';
        }
        $errorStr .= $traceStr;
        self::log($errorStr);
        if(Ouno::config('DEBUG')){
            $lf = (PHP_SAPI == 'cli') ? "\n\r#" : '<br />#';
            echo str_replace('#', $lf, $errorStr);
        }
        exit;
    }

    /*
     * @desc 记录用户行为信息日志
     * */
    public static function userLog($msg){
        $logStr = "[USER_LOG]";
        $logStr .= "#mssage : $msg";
        $logStr .=  (PHP_SAPI == 'cli') ? "\n\r#" : '<br />#';
    }


}

/*
 * 错误处理页面
 * */
class OunoError{

    public static function error404(){
        if(PHP_SAPI == 'cli')
            echo "404 ont found page\n\r";
        else
            include('/Ouno/Tpl/404.html');
        exit;
    }

    public static function error403(){
        if(PHP_SAPI == 'cli')
            echo "403 forbbiden \n\r";
        else
            include('/Ouno/Tpl/403.html');
        exit;
    }

}

/*
 * 框架工厂
 * */
class OFactory{

    private static $instances = [];

    /*
     * 产生实例的工厂方法
     * @param string $class 类名
     * @param string $function 方法名
     * @param array $param
     * @return unknow
     * */
    public static function getInstance($class, $param = array()){
        echo $class . "\r\n";
        if(!empty($param)){
            if(isset(self::$instances[$class]) && self::$instances[$class]['param'] == $param)
                return self::$instances[$class]['object'];
            self::$instances[$class]['object'] =  new $class($param);
            self::$instances[$class]['param'] = $param;
            return self::$instances[$class]['object'];
        }else{
            if(isset(self::$instances[$class]['object']))
                return self::$instances[$class]['object'];
            self::$instances[$class]['object'] = new $class();
            self::$instances[$class]['param'] = $param;
            return self::$instances[$class]['object'];
        }

        return false;

    }
}
