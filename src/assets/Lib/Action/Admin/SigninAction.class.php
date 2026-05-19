<?php
class SigninAction extends Action{
	
	//登陆界面
	
	//管理员登录
	public function index(){
		if($_POST['login']){
			if (!isset($_POST['user_name']) || !isset($_POST['password'])){
				$this->error('帐号或密码不能为空！');
			}
			
			$username=trim($_POST['user_name']);
			$password=trim($_POST['password']);
			$password=md5($password);
			$user=M('Admin');
			$where['username']=$username;
			$where['password']=$password;
			$where['is_del']=0;
			$result=$user->where($where)->find();
			if ($result){
				setcookie('id',$result['id'],time()+86400,'/');
				setcookie('username',$_POST['username'],time()+86400,'/');
				setcookie('loginname',$username,time()+86400,'/');
				setcookie('check',md5($_POST['username'].C('login_key')),time()+86400,'/');
				$data['last_time']=time();
				$result=$user->where($where)->save($data);
				$this->success('登录成功',U('Index/index'));
			}else{
				$this->error('帐号或密码错误');
			}
		}else{
			if ($_COOKIE['username']){
				header("Location: ?a=index&m=Index&g=Admin");
			}
			$this->display();
		}
	}
	
	//管理员登出
	public function logout(){
		if (isset($_COOKIE['check'])){
			setcookie('username','',time()-1,'/');
			setcookie('check','',time()-1,'/');
			$this->success('登出成功',U('Signin/index'));
		}else{
			$this->error('您并未登录',U('Signin/index'));
		}
	}
	
}