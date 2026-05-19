<?php
class UserAction extends BaseAction {
	
	public function login(){
		//if(SESSION('wx_openid')){   //特价
		if(0){
			$requesturl = U('Weixin/tips');
			$this->assign('title','帐号绑定'); 
		}else{
			$requesturl = $_REQUEST['requesturl'];
			$this->assign('title','用户登录');
		}
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
				$this->ajaxReturn($requesturl,'登录成功',1);  //原提示绑定成功
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
				$this->ajaxReturn(0,'您的手机号已被注册，请直接登录或选择忘记密码！',0);
			}else{
				
				if(session('wx_openid')){
					$regData['wx_openid'] = session('wx_openid');
				}
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
		$jumpUrl = $_SERVER['HTTP_REFERER'];
		if($url) session('loginJumpUrl',$jumpUrl);
		$this->assign('title','新用户注册');
		$this->display();
	}
	
	public function profile(){
		$this->checkLogin();
		$this->assign('title','个人中心');
		$info = M('user')->where('id='.$this->uid.' and is_del=0')->find();
		$this->assign('info',$info);
		
		$this->display();
	}
	
	public function info(){
		$this->checkLogin();
		$this->assign('title','个人信息');
		$info = M('user')->where('id='.$this->uid)->find();
		$this->assign('info',$info);
		
		$this->display();
	}
	
	public function password(){
		$this->checkLogin();
		$this->assign('title','修改密码');
		$this->assign('info',$info);
		
		$this->display();
	}
	
	public function passwordAct(){
		$mod = M('user');
		$old_password = md5($_REQUEST['old_password']);
		$new_password = md5($_REQUEST['new_password']);
		
		//验证旧密码是否正确
		$is_exist = M('user')->where('id='.$this->uid.' and password="'.$old_password.'"')->getfield('id');
		if($is_exist){
			$data['password'] = $new_password;
			M('user')->where('id='.$is_exist)->save($data);
			//修改成功后清空登录信息
			setCookie('wuid',null,time()-1,'/');
			$jumpUrl = U('User/login');
			$this->ajaxReturn($jumpUrl,'密码修改成功，您需要重新登录！',1);
		}else{
			$this->ajaxReturn(0,'原密码不正确！',0);
		}
		
	}
	
	public function infoAct(){
		$mod = M('user');
		$data['nickname'] = $_REQUEST['user_nickname'];
		$data['realname'] = $_REQUEST['user_true_name'];
		$data['sex'] = $_REQUEST['sex'];
		$data['email'] = $_REQUEST['email'];
		 M('user')->where('id='.$this->uid)->save($data);
		
		$this->ajaxReturn($data,'恭喜您！用户信息更新成功！',1);
	}
	
	public function order(){
		//是否微信跳转过来

		// $user_agent = $_SERVER['HTTP_USER_AGENT'];
// //		echo $user_agent;
		// if(strpos($user_agent, 'MicroMessenger') == true){
			// //直接登录
			// session('wxuid',$this->uid);
			// echo $this->uid;
			// echo 1;
		// }else{
			// $uid = session('wxuid');
			// echo $uid;
			// echo 2;
			// if($uid){
				// setcookie ( 'wuid', $uid, time () + 3600 * 24 * 7, '/' );
			// }else{
				// //$this->checkLogin();//跳转登录
			// }
		// }
		$this->checkLogin();
		
		//获取订单，初始化10个
		$mod = M('order');
		$uid = $this->uid;
		$list = $mod->where('uid='.$uid)->order('add_time desc')->limit(10)->select();
		$this->assign('list',$list);
		
		$this->assign('title','我的订单');
		$this->display();
	}
	
	public function getMoreOrders(){
		$mod = M('order');
		$uid = $this->uid;
		$sid = $_REQUEST['sid'];
		$num = 6;
		$list = $mod->where('uid='.$uid)->order('add_time desc')->limit($sid,$num)->select();
		if($list){
			$this->assign('list', $list);
			$data['list'] = $this->fetch('ajax_order_list');
			$data['sid'] = $sid + $num;
			$this->ajaxReturn($data,'',1);
		}else{
			$this->ajaxReturn('','',0);
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
			if(session('wx_openid')){
				$data['wx_openid'] = session('wx_openid');
			}
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
	
	public function checkLogin(){
		if(session('wx_openid')){
			//微信
			if($this->uid){
				return true;
			}else{
				$jumpUrl = U('Loginin/bind');
				header("Location:".$jumpUrl);
			}
		}else{
			//普通浏览器
			if($this->uid){
				return true;
			}else{
				$jumpUrl = U('User/login');
				header("Location:".$jumpUrl);
			}
		}
	}
}