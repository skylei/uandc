<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/30
 * Time: 16:05
 */
namespace Ouno22;
/*
 * 获取和设置配置参数 支持批量定义
 * 如果$key是关联型数组，则会按K-V的形式写入配置
 * 如果$key是数字索引数组，则返回对应的配置数组
 * @param string|array $key 配置变量
 * @param array|null $value 配置值
 * @return array|null
 **/
function C($key, $value = null) {
	static $_config = array() ;
    $args = func_num_args();
	if($args == 0){
		return $_config;
    }elseif($args == 1){
        if(is_string($key)){  //如果传入的key是字符串
            return isset($_config[$key])?$_config[$key]:null;
        }
        if(is_array($key)){
            if(array_keys($key) !== range(0, count($key) - 1)){  //如果传入的key是关联数组
                $_config = array_merge($_config, $key);
            }else{
                $ret = array();
                foreach ($key as $k) {
                    $ret[$k] = isset($_config[$k])?$_config[$k]:null;
                }
                return $ret;
            }
        }
    }else{
        if(is_string($key)){
            $_config[$key] = $value;
        }else{
            halt('传入参数不正确');
        }
    }
    return null;
}


/*
 * 基类，核心文件均继承自该类
 * */
class BaseComponent{

	public function __construct() {
        global $Ouno_conf;
        C($Ouno_conf);
        $this->run();
    }

    /*
     *默认运行方法，该方法保护
     * */
    protected function run(){}

	/*public function __set($name, $value = ''){

	}

	public function __get($name){
	    if(isset($this->$name))
            return $this->$name;
	}*/

	public function __toStrings(){

	}

	private function __clone(){}



}
/*
 * 核心类，主要文件
 *
 * */
class Ouno extends BaseComponent{

    private static $includePath = array();
    public static $_import = array();
    private static $_instance = null;
    public static $_Classes = array();


    /*
     * 构造函数，初始化配置参数
     * */
    public function __construct(){
		parent::__construct();
	}

    /**
     * 获取单例
     * @return object self
     */
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }


	/*
	 * 运行框架
	 * */
    public function run(){
        session_start();
        spl_autoload_register(array('Ouno', 'loader'));
        $this->init2Ehandle();
        if(C('URI') == 'PATH') $this->getRequest();
        $this->m = $_GET['m'] = (C('MODULE') && !empty($_GET['m'])) ? $_GET['m'] : 'index';
        $this->c = $_GET['c'] = (!empty($_GET['c'])) ? $_GET['c'] : 'index';
        $this->a = $_GET['a'] = (!empty($_GET['a'])) ? $_GET['a'] : 'index';

        $controllerFile =  C('CONTROLLER_PATH') . DIRECTORY_SEPARATOR . $this->m . DIRECTORY_SEPARATOR .$this->c .'Controller.php';
        self::import($controllerFile);
        $controller = $this->c . 'Controller';
        if(!class_exists($this->c.'Controller')){
            OunoLog::userLog('controller inexistence');
        }
        $controller = new $controller;
        $action = $this->a.'Action';

        if(!method_exists($controller, $action) ){
            OunoLog::userLog('controller inexistence');
        }
        call_user_func(array($controller, $action));
        if(method_exists($controller, 'run_after'))
            $controller->run_after();
	}

    /*
     * @param $callback 注册全局变量
     * */
    public static function registerAutoloader($callback){
        spl_autoload_unregister(array('\Ouno\Ouno', 'loader'));
        spl_autoload_register($callback);
        spl_autoload_register(array('\Ouno\Ouno', 'loader'));
    }

    /*
     * @desc 自动加载类，依赖于配置文件
     * @param $className 加载的类名，文件名需和类名一致
     * @retrun include file;
     * */
    public static function loader($className){

        if(isset(self::$_Classes[$className]) && is_file( self::$_Classes[$className])){
           include(self::$_Classes[$className]);
        }else{
            if (strpos($className, '\\') === true) {
                $classFile = APP_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, ltrim($className, '\\'));
                echo $classFile;exit;
                if (is_file($classFile)) include($classFile);
            }else{//路径
                self::$includePath = C('Autoload_Path');//array
                $include = '';
                foreach (self::$includePath as $name => $path) {
                    $include .= $path . PATH_SEPARATOR ;
                }
                set_include_path($include . get_include_path());
                include($className . '.php');
            }
        }
    }

    /*
     * include加载，如果存在则加载
     * */
    public static function import($file){
        $file = APP_PATH . $file;
        if(file_exists($file) && !in_array($file, self::$_import)){
            self::$_import[] = $file;
            include $file;
        }elseif(!in_array($file, self::$_import)){
//            Ouno::error("$file inexistance when Ouno::import");
            throw new Exception("$file inexistance when Ouno::import");
        }

    }

    /*
     *初始化错误处理
     * */
    public function init2Ehandle(){
        if(C('EXCEPTION_HANDLE'))
            set_exception_handler(array('Ouno', 'handleException'));
        if(C('ERROR_HANDLE'))
            set_error_handler(array($this,'handleError'),error_reporting());
    }

    /*
    *设置PHP错误处理函数,写入日志文件
    */
    public function handleError($errorCode, $msg = '', $errorFile = 'unkwon', $errorLine = 0){
        restore_error_handler();
        if($errorCode & error_reporting()){
            $message = "#[ " . OunoLog::$_errorCode[$errorCode] ."]";
            $message .= "#date : " . date('Y-m-d H:i:s', time());
            $message .= " #message:". $msg . " #file: ". $errorFile . "#line: ". $errorLine;
            $trace = debug_backtrace();
            //echo "<pre>"; var_dump($trace) ;echo "</pre>";
            $traceStr = '#(strace)';
            foreach(array_slice($trace, 1, C('ERROR_LEVE', 5) ) as $key=>$val){
                $traceStr .= '#';
                $traceStr .= isset($val['file']) ?  ' file : ' .$val['file']  : '';
                $traceStr .= isset($val['class']) ?  ' ' .$val['class'] . '::' : '';
                $traceStr .= isset($val['function']) ? ' ' .$val['function'] . '()' : '';
                $traceStr .= isset($val['line']) ?  'line( '. $val['line'] .')' : '';
            }
            $message .=$traceStr;
            if(C('ERROR_DISPLAY')) self::displayException($message);
            OunoLog::log($message);
        }

    }

    /*
    *设置异常处理函数,写入日志文件
    */
    public static function handleException($exception){
        restore_exception_handler();
        $message = 'Exception : ' . $exception->__toString();
        $message .= '#date : ' .date('Y-m-d H:i:s', time());
        $class = get_class($exception);
        $message .='#exception : ' .$class;
        $message .= '#(trace)' . $exception->getTraceAsString();
        if(C('EXCEPTION_DISPLAY')) self::displayException($message);
        OunoLog::log($class, $message);
    }

    /*
     * 打印错误
     * */
    public static function displayException($message){
        if(php_sapi_name() == 'cli')
            echo str_replace('#', '\n#', $message);
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
        $path = C('DAO_PATH');
        if($group)
            $path = $path . DIRECTORY_SEPARATOR .$group . DIRECTORY_SEPARATOR . $daoName .'Dao.php';
        else
            $path = $path . DIRECTORY_SEPARATOR . $daoName .'Dao.php';
        Ouno::import($path);
        $dao = $daoName .'Dao';
        if(class_exists($dao))
            return new $dao;
        else
            throw new Exception('dao inexistance');
    }


    /*
     * @desc 获取 service 实例
     * @param $serviceName string
     * @param $group string
     * @return instance
     * @throw service inexistance
     * */
    public static  function service($serviceName, $group = ''){

        $path =  C('SERVICE_PATH');

        if($group)
            $path = $path . '/' .$group . '/' . $serviceName .'Service.php';
        else
            $path = $path . '/' . $serviceName .'Service.php';
        Ouno::import($path);
        $service = $serviceName .'Service';
        if(class_exists($service))
            return new $service;
        else
            throw new Exception('service inexistance');
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
        if(C('MODULE')) {
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
            if(count($key) !== count($value)) $value[] = '';
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
     * 构造函数，初始化视图实例，调用hook
     */
    public function __construct(){
        parent::__construct();
        //模板引擎用户可自定义模板引擎
        if(C('VIEW') == 'DEFAULT'){
            $this->_view = new OunoView();
        }else{
            $view = C('VIEW');
            $this->_view = new $view;
        }
        $this->baseUrl = C('BASEURL');
    }

    /*
     * 后置运行方法，默认加载，可自行重写改方法
     * */
	public function run_after(){}

    /*
     * 创建url
     *
     * */
    public function createUrl($url = '',$param = array(),  $type = 'url'){
        $baseUrl = explode('index.php', $_SERVER['PHP_SELF']);
        $newUrl =  C('BASEURL');
        $paramStr = '';
        if($type == 'url'){
            $newUrl .= $baseUrl[0] . 'index.php';
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

	public function assign($tpl, $value){

        $this->_view->assign($tpl, $value);
	}

    public function clearSmartyCache(){
        $this->_view->cleanCache();
    }

	public function setTpl($file){//false返回不输出
        if(C('MODULE'))
            $file = $_GET['m'] . DIRECTORY_SEPARATOR . $file . '.' . C('VIEW_POSTFIX');
        else
            $file = $file . '.' . C('VIEW_POSTFIX');
        if(!file_exists(APP_PATH . C('TEMPLATE_PATH'). DIRECTORY_SEPARATOR . $file))
            Ouno::error('template not exist');
		$this->tpl = $file;
	}

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
     * @var viewobj
     * */
    public $viewObj = '';

    /**
     * @var string
     */
    public $fileExtension = '.html'; // or ".php" if you like

    public function __construct(){
        $this->viewObj = self;
    }

    /*
     *
     * */
    public function assign($var, $value){

    }


    /*
     * 显示模板
     * @param $file string
     * @param $oupt boolean 是否输出
     * */
    public function display($file, $output =true){
        if($output)
            $this->viewObj->display($file);
        else{
            return $this->viewObj->fetch($file);
        }
    }

    /*
     * 清除smarty 缓存
     * */
    public function cleanCache(){
        $this->viewObj->cache->clearAll();
    }


}

class Dao extends DB{



    /*protected function run(){
        $driver = C('NDB_DRIVER');
        return  $this->db = $driver::getInstance(C('NDB'));
    }*/
}

class Service extends BaseComponent{
    protected $service;

    protected function run(){

    }

}

class DB extends  BaseComponent{

    public  $db = null;
    public $table = '';
	public $is_slice = false;
    protected function run(){
        $driver = C('DB_DRIVER');
        $this->db = new $driver( C('DB'));
        $this->db->table = $this->table;
    }

	public function mapTable(){

	}
}

/**
 * 异常类
 */
class OunoException extends ErrorException{



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
        if(!self::$_basePath) self::$_basePath = APP_PATH . C('LOG_PATH') . '/' .$filename;
        if(filesize(self::$_basePath) > self::$autoFlush)
            system("echo '' > " . self::$_basePath);
		file_put_contents(self::$_basePath, $msg, FILE_APPEND);
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
        foreach(array_slice($trace, 1, C('ERROR_LEVE', 5) ) as $key=>$val){
            $traceStr .= '#';
            $traceStr .= isset($val['file']) ?  ' file : ' .$val['file']  : '';
            $traceStr .= isset($val['class']) ?  ' ' .$val['class'] . '::' : '';
            $traceStr .= isset($val['function']) ? ' ' .$val['function'] . '() ' : '';
            $traceStr .= isset($val['line']) ?  ' line( '. $val['line'] .')' : '';
        }
        $errorStr .= $traceStr;
        self::log($errorStr);
        if(C('DEBUG')){
            $lf = (php_sapi_name() == 'cli') ? '\n#' : '<br />#';
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
        $logStr .=  (php_sapi_name() == 'cli') ? '\n#' : '<br />#';
    }


}









