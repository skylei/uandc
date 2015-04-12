<?php 
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/30
 * Time: 16:05
 */
namespace components;
class smartyView extends \Ouno\Base{
	
	
	public $viewObj = '';


	public function __construct(){
		$smartyPath = \Ouno\Ouno::config('SMARTY_PATH');
        \Ouno\Ouno::import($smartyPath . '/Smarty.class.php');
//        Ouno::$_Classes['Smarty_Internal_Data'] = $smartyPath . '/sysplugins/smarty_internal_data.php';
        $this->viewObj = new \Smarty();
        $this->viewObj->error_reporting = E_ALL & ~E_NOTICE;
        $this->viewObj->compile_dir = APP_PATH . \Ouno\Ouno::config("SMARY_COMPILE");
        $this->viewObj->cache_dir =  APP_PATH . \Ouno\Ouno::config("SMARTY_CACHE_DIR");
        $this->viewObj->setTemplateDir(APP_PATH . \Ouno\Ouno::config('TEMPLATE_PATH'));
        $this->viewObj->left_delimiter = '<{'; // chenge it if you want other delimiter
        $this->viewObj->right_delimiter = '}>';
        $this->viewObj->caching = \Ouno\Ouno::config('SMARTY_CACHE');
        $this->viewObj->setCacheDir(\Ouno\Ouno::config('SMARTY_CACHE_DIR'));//@todo
        $this->viewObj->cache_lifetime = 150;
        $this->viewObj->force_compile = \Ouno\Ouno::config('DEBUG');
        \Ouno\Ouno::registerAutoloader('smartyAutoload');
	}
	
	 /*
     *
     * */
    public function assign($var, $value){
        $this->viewObj->assign($var, $value);
    }


    /*
     * 显示模版
     * @param $file string
     * @param $oupt boolean|string
     * */
    public function display($file, $output =true){
        if($output)
            $this->viewObj->display($file);
        else{
            return $this->viewObj->fetch($file);
        }
    }

    /*
     * 开启smarty缓存
     * */
    public function cleanCache(){
        $this->viewObj->cache->clearAll();
    }

}