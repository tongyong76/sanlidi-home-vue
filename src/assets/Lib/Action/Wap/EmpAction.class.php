<?php
class EmpAction extends Action {
	public function login(){
		header("Content-Type:text/html; charset=UTF-8");
		if ($_POST['submit']){
			$mod = M('admin');
			$data = $mod->create();
			if(preg_match("/1[0-9]{1}\d{9}$/",$data['username'])){  
				$map['phone'] = $_REQUEST['username'];
			}else{  
				$map['username'] = $_REQUEST['username'];
			}  
			$map['password'] = md5($_REQUEST['password']);
			$info = $mod->join('33_admin_emp as e on e.uid=33_admin.id')->where($map)->find();
			if($info){
				setcookie ( 'id', $info ['id'], time () + 3600 * 24 * 7, '/' );
				$this->success('登录成功',U('Emp/info'));
			}else{
				$this->error('用户名密码错误！');
			}
		}else{
			$this->display();
		}		
	}
	
	public function info(){		
		header("Content-Type:text/html; charset=UTF-8");
		$uid=$_COOKIE['id'];
		if ($_POST['submit']){
			$data = M('admin_emp')->create();
			$password = $_REQUEST['password'];
			if($password){
				$password = md5($password);
				M('admin')->where('id='.$uid)->setfield('password',$password);
			}
			$where['uid'] = $uid;
			M('admin_emp')->where($where)->save($data);
			$this->success('修改成功',U('Emp/info'));
		}else{			
			if($uid){
				$info = M('admin')->join('33_admin_emp as e on e.uid=33_admin.id')->where('id='.$uid)->find();
				$info['linkurl'] = "http://m.33ly.com/emp/".$info['username']."/";
				$info['erweimaurl'] = $this->erweima($info['linkurl'],$info['username']);
				$this->assign('info',$info);
			}else{
				$this->error('非法操作',U('Emp/login'));
			}
			$this->display();
		}
	}
	
	public function user(){
		$mod = M('admin');
		$username = $_REQUEST['username'];
		if($username){
			$info = $mod->join('33_admin_emp as e on e.uid = 33_admin.id')->where('username="'.$username.'"')->find();
		}else{
			$id = $_REQUEST['id'];
			$info = $mod->join('33_admin_emp as e on e.uid = 33_admin.id')->where('id='.$id)->find();
		}		
		session('empInfo',$info);
		$this->assign('empInfo',$info);
		$this->assign('userpage',1);
		
		//获取模块
		if($info['expert']){
			$dataExpert = explode('|',$info['expert']);
			foreach($dataExpert as $key=>$value){
				$expert[$key]['id'] = $value;
				switch($value){
					case 1:
						$expert[$key]['url'] = "http://m.33ly.com/zhoubian/";
						break;
					case 2:
						$expert[$key]['url'] = "http://m.33ly.com/guonei/";
						break;
					case 3:
						$expert[$key]['url'] = "http://m.33ly.com/chujing/";
						break;
					case 4:
						$expert[$key]['url'] = "http://m.33ly.com/youlun/";
						break;
					case 5:
						$expert[$key]['url'] = "http://m.33ly.com/qianzheng/";
						break;
					case 6:
						$expert[$key]['url'] = "http://m.33ly.com/tuandui/";
						break;
				}
				
			}
			$this->assign('expert',$expert);
		}
		
		//热推
		$dataLines = explode('|',$info['lines']);
		$mapLines['id'] = array('in',$dataLines);
		$hotList = M('goods')->where($mapLines)->select();
		if(!$hotList){
			$hotList = M('goods')->where('is_del=0 and is_hot=1 and minprice<>0')->order('ordid desc,add_time desc')->limit(5)->select();
		}
		$this->assign('hotList',$hotList);
		$this->display();
	}
	
	//微信分享内容测试
	public function user2(){
		$mod = M('admin');
		$username = $_REQUEST['username'];
		if($username){
			$info = $mod->join('33_admin_emp as e on e.uid = 33_admin.id')->where('username="'.$username.'"')->find();
		}else{
			$id = $_REQUEST['id'];
			$info = $mod->join('33_admin_emp as e on e.uid = 33_admin.id')->where('id='.$id)->find();
		}		
		session('empInfo',$info);
		$this->assign('empInfo',$info);
		$this->assign('userpage',1);
		
		//获取模块
		$data = explode(',',$info['expert']);
		foreach($data as $key=>$value){
			$expert[$key]['id'] = $value;
			switch($value){
				case 1:
					$expert[$key]['url'] = "http://m.33ly.com/zhoubian/";
					break;
				case 2:
					$expert[$key]['url'] = "http://m.33ly.com/guonei/";
					break;
				case 3:
					$expert[$key]['url'] = "http://m.33ly.com/chujing/";
					break;
				case 4:
					$expert[$key]['url'] = "http://m.33ly.com/youlun/";
					break;
			}
			
		}
		$this->assign('expert',$expert);
		
		//热推
		$hotList = M('goods')->where('is_del=0 and is_hot=1 and minprice<>0')->order('ordid desc,add_time desc')->limit(5)->select();
		$this->assign('hotList',$hotList);
		
		$this->display();
	}
	
	public function register(){
		$this->display();
	}
	
	public function erweima($url,$sn){
		$imgurl = 'Uploads/erweima/'.$sn.'.png';
		
		if(!file_exists($imgurl)){
			vendor("phpqrcode.phpqrcode");
			$data = $url;
			$level = 'H';
			$size = 10;
			QRcode::png($data, 'ewm.png', $level, $size,1);
			$logo = "qrlogo2.gif";
			$qr = 'ewm.png';
			
			if($logo !== FALSE){
				$qr = imagecreatefromstring(file_get_contents($qr));			
				$logo = imagecreatefromstring(file_get_contents($logo));
				$qr_width = imagesx($qr);
				$qr_height = imagesy($qr);
				$logo_width = imagesx($logo);
				$logo_height = imagesy($logo);
				$logo_qr_width = 110;
				$scale = $logo_width / $logo_qr_width;
				$logo_qr_height = $logo_height / $scale;
				//echo $logo_qr_height;
				$from_width = ($qr_width - $logo_qr_width) / 2;
				imagecopyresampled($qr, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height,$logo_width,$logo_height);
				//var_dump($qr);
			}			
			imagepng($qr,$imgurl);			
		}
		return $imgurl;
	}
	
}