<?php
/*
 * userCenter
 * @author crab
 * @date 2014-10-31
 *
 * */
 namespace web\controller\ucenter;
class indexController extends \components\BaseUcenterController {
	protected $data =array();


	public function run_after(){
		$this->data['basePath'] = '';
        $this->data['baseUrl'] = $this->getBaseUrl();
		$this->assign('data', $this->data);
		$this->show();
	}

    public function run(){
        $this->data['loginUrl'] = $this->createUrl('/ucenter/index/checkLogin');
    }

    /* 	public function userState() {//通过
            $userInfo = $this->getUService()->checkUserState();
            return $userInfo;
        }  */
	

	public function loginAction(){ //通过
		$this->setTpl('login');
	}


	public function checkLoginAction(){ //待改进
		$username = $this->_post('username');
		$password = $this->_post('password');
        $remember = $this->_post('remember', false);
		$ip = $this->getIp();
		$check = $this->getUService()->getUser($username,$password, $remember, $ip);
		if ($check) {
			$this->redirect('/ucenter/uhome/index');
		}else{
            $this->redirect('/ucenter/index/login');
		}
	}
	

	public function checkRegister(){
		$data['username'] = $this->_post('username');
		$data['password'] = $this->_post('password');
		$data['email'] = $this->_post('email');
		$data['nickname'] = $this->_post('nickname');
		$verify = md5($this->_post('verify'));
		if(!$data['username'] || !$data['password'] || !$data['email'] || !$data['nickname'] || !$verify)	return false;
		if($this->getLibrary('code')->checkCode($verify, 'users') == false)	return false;
		$data['ip'] = $this->get_ip();
		$data['hash'] = mt_rand(713, 2014);
		$userService = $this->_getService('Users', 'Users');	
		if($userService->addUser($data)){
			$this->redirect($this->getUrl('Users', 'uhome', 'run'), 3, '恭喜你成为我们的一员');
			$this->register_global('userInfo', $data);
		}else{
			$this->error('呵呵，哪里出错了额~！');
		}
	}



    public function getIp(){
        return ip2long('127.0.0.1');
    }

    private function getUService(){
        return new \src\service\ucenter\ucenterService();
    }
	
}