<?php
class LogininAction extends Action {
	
	public function _initialize() {
		header("Content-Type:text/html; charset=UTF-8");  //编码
		
		import("@.ORG.BaiduHmWap");
		$_hmt = new _HMT("45988a729df28f554d96a5b9932b17e1");
		$_hmtPixel = $_hmt->trackPageView();
		$this->assign('BdWap',$_hmtPixel);		
		
	}	
	
	public function bind(){
		//if(SESSION('wx_openid')){   //特价
		$requesturl = U('User/profile');
		$this->assign('title','帐号绑定'); 
		$this->assign('requesturl',$requesturl);
		
		$this->display();
	}
	
	public function loginAct(){
		$mod = M('user');
		$requesturl = $_REQUEST['requesturl']?$_REQUEST['requesturl']:__GROUP__.'/';
		$map['password'] = md5($_REQUEST['password']);
		$map['phone'] = $_REQUEST['phone'];
		$info = $mod->where($map)->find();
		if($info){
			setcookie ( 'wuid', $info ['id'], time () + 3600 * 24 * 7, '/' );
			if(SESSION('wx_openid')){//在微信浏览器中进行登录时进行微信绑定
				$mod->where('id="'.$info['id'].'"')->setfield('wx_openid',SESSION('wx_openid'));
				$this->ajaxReturn($requesturl,'绑定成功',1);  //原提示绑定成功
			}else{
				$this->ajaxReturn($requesturl,'登录成功',1);
			}
						
		}else{
			$this->ajaxReturn(0,'用户名密码错误',0);
		}
	}
	
	public function regsteptwo(){
		
		
		//生成验证码并发送
		if(session('shortMessage')){
			$regData['phone'] = $_REQUEST['user_number'];
			$regData['password'] = MD5($_REQUEST['user_password']);
			$regData['reg_ip'] = $regData['last_ip'] = getClientIp();
			$regData['reg_time'] = $regData['last_time'] = time();
			session('regData',$regData);
		
			session('shortMessage',0);
			$phone = $regData['phone'];
			$mobile_code = random(4,1);
			$data = "您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。24小时旅游通0512-66667777";
			$_SESSION['mobile_code'] = $mobile_code;
			$reslut = $this->smsxwkj($phone,$data);
				
		}
		
		$this->assign('title','验证手机');
		$this->display();
	}
	
	public function regsteptwoAct(){
		$auth_code = $_REQUEST['phone_auth_code'];
		if(session('mobile_code') != $auth_code) {
		//if(0){
			//验证码错误，0
			$this->ajaxReturn(0,'验证码错误',0);
		}else{
			//正确则插入数据
			$regData = session('regData');
			//先判断手机号是否存在
			$is_exist = M('user')->where('phone='.$regData['phone'])->find();
			if($is_exist){
				$this->ajaxReturn(0,'您的手机号不能注册',0);
			}else{
				$wuid = M('user')->where('phone='.$phone)->add($regData);
				$cookie = 1;
				if ($cookie == true) {
					setcookie ( 'wuid', $wuid, time () + 3600 * 24 * 7, '/' );
				} else {
					setcookie ( 'wuid', $wuid, '/' );
				}
				$jumpUrl = U('User/regsuccess');
				$this->ajaxReturn($jumpUrl,'注册成功',1);
			}
		}
	}
	
	public function regsuccess(){
		$this->assign('title','注册成功');
		$this->display();
	}
	
	public function register(){
		if ($_POST['submit']){
			// if(session('verify') != md5($_POST['captcha'])) {
				// $this->error('验证码错误！');
			// }
			$userMod = M('user');
			$data=$userMod->create();
			$data ['password'] = md5 ( $data ['password'] );
			$data ['reg_ip'] = getClientIp();
			$data ['reg_time'] = time();
//			var_dump($data);
			$id = $userMod->where('phone='.$data['phone'])->getfield('id');
			// if($id){
				// $userMod->where('id='.$id)->save($data);
			// }else{
				// $id = $userMod->add($data);
			// }
			
			if($id){
				//setcookie ( 'id', $result, time () + 3600 * 24 * 7, '/' );
				//setcookie ( 'name', $_POST ['uname'], time () + 3600 * 24 * 7, '/' );
				//$this->success('',U('User/step2'));
				//header("Location: ?a=checkverify&m=User&g=Home&uid=$newId");
				//header("Location: $url"); 
				$this->redirect('User/step2', array('uid'=>$id,'phone'=>$data['phone'])) ;
			}
		}else{
			$jumpUrl = $_SERVER['HTTP_REFERER'];
			if($url) session('loginJumpUrl',$jumpUrl);
			$this->assign('title','新用户注册');
			$this->display();
		}
	}
	
	public function logout(){
		setcookie ( 'wuid', null, time () - 1, '/' );
		//$this->success('注销成功！','__ROOT__');
		$jumpUrl = __GROUP__.'/';
		header("location: ".$jumpUrl);
	}
	
	public function findpwd(){
		if ($_POST['submit']){
		}else{
			$this->assign('title','找回密码');
			$this->display();
		}
	}
	
	public function changepwd(){

		//生成验证码并发送
		if(session('shortMessage')){
			session('shortMessage',0);
			$phone = $_REQUEST['phone'];
			$this->assign('phone',$phone);
			$mobile_code = random(4,1);
			$data = "您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。24小时旅游通0512-66667777";
			$_SESSION['mobile_code'] = $mobile_code;
			$reslut = $this->smsxwkj($phone,$data);
		}
		
		$this->assign('title','重置密码');
		$this->display();

	}
	
	public function changepwdAct(){
		$phone = $_REQUEST['phone'];
		$auth_code = $_REQUEST['auth_code'];
		$new_password = $_REQUEST['new_password'];
		if(session('mobile_code') != $auth_code) {
			//验证码错误，0
			$this->ajaxReturn(0,'验证码错误',0);
		}else{
			$data['password'] = MD5($new_password);
			M('user')->where('phone='.$phone)->save($data);
			$wuid = M('user')->where('phone='.$phone)->getfield('id');
			$cookie = 1;
			if ($cookie == true) {
				setcookie ( 'wuid', $wuid, time () + 3600 * 24 * 7, '/' );
			} else {
				setcookie ( 'wuid', $wuid, '/' );
			}
			$jumpUrl = U('User/profile');
			$this->ajaxReturn($jumpUrl,'修改密码成功',1);
		}
	}
	
	// 验证码
	public function verify(){
		import('ORG.Util.Image');
		Image::buildImageVerify ();
		//$this->assign('verify',session('verify'));
		//echo session('verify');
	}	
	public function verify_test() {
		header ( 'Content-type:text/html;charset=utf-8' );
		session_start ();
		$verify = md5 ( $_POST ['verification'] );
		if ($_SESSION ['verify'] == $verify) {
			//设置TOKEN,避免刷新发送短信
			SESSION('shortMessage',1);
			
			$jumpUrl = U('User/changepwd');
			$jumpUrl = str_replace('.html', '', $jumpUrl);
			$this->ajaxReturn($jumpUrl,0,1);
		} else {
			$this->ajaxReturn(0,0,0);
		}
	}
	
	public function verify_reg(){
		header ( 'Content-type:text/html;charset=utf-8' );
		session_start ();
		$verify = md5 ( $_POST ['verification'] );
		if ($_SESSION ['verify'] == $verify) {
			//设置TOKEN,避免刷新发送短信
			SESSION('shortMessage',1);
			echo 'true';
		} else {
			echo 'false';
		}
	}
}