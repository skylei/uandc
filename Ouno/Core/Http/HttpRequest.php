<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/4/12
 * Time: 15:23
 */
namespace Ouno\Core\Http;
class HttpRequest{

    public $parma = [];

    public $method = [];

    public function getHttpMethod(){
        return $this->method;
    }

    public function setMethod(){
        $argv = func_get_args();
        $this->method = array_merge($this->method, $argv);
    }

    public function getPost($key, $default = ''){
        if($this->param['post'][$key])
            return $this->param['post'][$key];
    }

    public function getDelete($name,$default = null){
        if($this->param['delete']===null)
            $this->param['delete'] = $this->getRestParams();
        return isset($this->param['delete'][$name]) ?$this->param['delete'][$name] : $default;
    }

    public function getPut($name,$default = null){
        if($this->param['put']===null)
            $this->param['put'] = $this->getRestParams();
        return isset($this->param['delete'][$name]) ? $this->param['put'][$name] : $default;
    }

    /**
     * Returns rest request parameters.
     * @return array the request parameters
     */
    protected function getRestParams(){

        $result = [];
        $httpMethod = array('get', 'put', 'delete', 'put', 'patch', 'options');
        if(!isset($_SERVER['REQUEST_METHOD']) || !in_array(strtolower($_SERVER['REQUEST_METHOD']), $httpMethod))
            return $result;
        if(function_exists('mb_parse_str'))
            mb_parse_str(file_get_contents('php://input'), $result);
        else
            parse_str(file_get_contents('php://input'), $result);
        return $result;
    }

    /*
     * rest 请求
     * */
    public function parseRest(){
        $pathInfo = trim($_SERVER['PATH_INFO'], '/');
        echo $pathInfo;
        $pattern = '/^([^\/]+)\/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\/[a-zA-Z0-9_\x7f-\xff]*)*$/';
        preg_match($pattern, $pathInfo, $matches);
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        var_dump($matches);
        $module =$_GET['m'] = $matches[1];
        $controller = $_GET['c'] = $matches[2];
        $id = $_GET['id'] = isset($matches[3]) ? ltrim($matches[3]) : '';
        $allowApi = \Ouno\Ouno::config("REST_API");
        if(!in_array($module, $allowApi))
            echo "403 forbidden";
        $restNamespace = \Ouno\Ouno::config("REST_CONTROLLER");
        $class = $restNamespace . '\\' . $module . '\\' . $controller . 'Controller';
        $instance = OFactory::getInstance($class);
        if(method_exists($instance, 'run'))
            $instance->run();
        if($method == 'get' && $id)
            $instance->getAction();
        else if($method == 'get' && !$id)
            $instance->listAction();
        else if($method == 'post')
            $instance->addAction();
        else if($method == 'delete' && $id)
            $instance->removeAction();
        else if($method == 'put' && $id)
            $instance->updateAction();
        if(method_exists($instance, 'run_after'))
            $instance->run_after();

    }

    /**
     * 路由分发，获取Uri数据参数
     * 1. 对Service变量中的uri进行过滤
     * 2. 配合全局站点url处理request
     * @return string
     */
    public function parsePathRequest() {
        $filter_param = array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e');
        $request = array();
        if(isset($_SERVER['PATH_INFO'])) {
            $path = str_replace($filter_param, '', $_SERVER['PATH_INFO']);
            $request = explode('/', trim($path, '/'));
        }

        $i = -1;
        if(\Ouno\Ouno::config('MODULE')) {
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

}