<?php
/**
 * Created by IntelliJ IDEA.
 * User: crab
 * Date: 2014/10/30
 * Time: 16:05
 */
namespace components;
class BaseUcenterController extends BaseController{
    protected $data = array();


    public function run(){
        $logined = \Ouno\Ouno::service('ucenter', 'ucenter')->checkLoginStatus();
        if($logined == false){
            $this->redirect('/ucenter/index/login');
        }
    }



}