<?php 
class indexController extends BaseController{

	public function indexAction(){
		$this->assign('test', 'sdxkxkkkkkkk');
		$this->setTpl('index');
	}

	public function run_after(){
        $this->display();
	}

    private function getHomeService(){
        return Ouno::service('home', 'Home');
    }
	


















}
?>
