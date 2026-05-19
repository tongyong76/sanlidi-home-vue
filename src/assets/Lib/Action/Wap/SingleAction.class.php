<?php
//临时独立页面
class SingleAction extends BaseAction {
	public $wx_appid = 'wx9f98139e202c7003';
	public $wx_appsecret = 'fc569bb8896f29b5269fe49169d0c77a';
	
	public function order(){
		$data['gid'] = $_REQUEST['gid'];
		$data['ordstart'] = strtotime($_REQUEST['ordstart']);
		$data['ordname'] = $_REQUEST['ordname'];
		$data['adult_num'] = $_REQUEST['adult_num'];
		$data['ordprice'] = $_REQUEST['ordprice'];
		$data['ordplace'] = $_REQUEST['ordplace'];
		$data['wx_openid'] = $_REQUEST['wx_openid'];
		$data['cname'] = trim($_REQUEST['cname']);
		$data['cphone'] = trim($_REQUEST['cphone']);
		
		$data['ordstatus'] = 1;
		$data['ordsn'] = 'SN'.date('YmdHis');			
		$data['add_time'] = time();
		$data['ordfrom'] = 1;
		$data['ordsrc'] = "苏州";
		$data['ordacc'] = "qian";
		if(!$data['cname']){
			$wxInfo = M('wx_user')->where('wx_openid="'.$data['wx_openid'].'"')->find();
			$data['cname'] = $wxInfo['wx_nickname']."(微信)";
		}		
		
		//插入订单
		$newOrderId = M('order')->add($data);
		
		//发送通知邮件
		
		$this->ajaxReturn($newOrderId,'',1);
	}
	
	public function ktlist(){
		$this->display();
    }
	
	public function ktqjhj(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);

		//获取微信信息
		//$wx_openid = 'oOrekjmmExAQ40gRUAzM8I_LrnO8';
		$wx_openid = SESSION('wx_openid');
		$this->assign('wx_openid',$wx_openid);
		$wx_nickname = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_nickname');		
		if(!$wx_openid or !$wx_nickname){
			$redirect_uri = urlencode('http://m.33ly.com/Single/slogin');
			$state = base64_encode('http://m.33ly.com/Single/ktqjhj');
			//$state = 0;
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
			header("Location: $url");
		}	
		$this->display();
	}
	
	public function ktgt(){
		//获取微信信息
		//$wx_openid = 'oOrekjmmExAQ40gRUAzM8I_LrnO8';
		$wx_openid = SESSION('wx_openid');
		$this->assign('wx_openid',$wx_openid);
		$wx_nickname = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_nickname');		
		if(!$wx_openid or !$wx_nickname){
			$redirect_uri = urlencode('http://m.33ly.com/Single/slogin');
			$state = base64_encode('http://m.33ly.com/Single/ktgt');
			//$state = 0;
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
			header("Location: $url");
		}		
		$this->display();
	}
	
	public function ktzyx(){
		//获取微信信息
		//$wx_openid = 'oOrekjmmExAQ40gRUAzM8I_LrnO8';
		$wx_openid = SESSION('wx_openid');
		$this->assign('wx_openid',$wx_openid);
		$wx_nickname = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_nickname');		
		if(!$wx_openid or !$wx_nickname){
			$redirect_uri = urlencode('http://m.33ly.com/Single/slogin');
			$state = base64_encode('http://m.33ly.com/Single/ktzyx');
			//$state = 0;
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
			header("Location: $url");
		}	
		$this->display();
	}
	
	//简单验证
	public function slogin(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state']?$_REQUEST['state']:0;
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appid.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode(http_request($post_url));
		
		//是否第一次
		$exist = M('wx_user')->where('wx_openid="'.$json->openid.'"')->find();
		if($exist){
			SESSION('wx_openid',$exist['wx_openid']);
			$jump_url = base64_decode($state);
			header("Location: $jump_url");
		}else{
			$redirect_uri = urlencode('http://m.33ly.com/Single/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
	}
	
	//获取用户信息并绑定
	public function ulogin(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state']?$_REQUEST['state']:0;
		$userInfo = $this->checkUser($code);
		//存入数据库
		$data['wx_openid'] = $userInfo->openid;
		$data['wx_nickname'] = $userInfo->nickname;
		$data['wx_sex'] = $userInfo->sex;
		$data['wx_headimgurl'] = $userInfo->headimgurl;
		M('wx_user')->add($data);
		SESSION('wx_openid',$data['wx_openid']);
		$jump_url = base64_decode($state);
		header("Location: $jump_url");
	}
	
	
	//微信接口 获取用户信息
	public function checkUser($code){
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appid.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode(http_request($post_url));
		
		$post_url2 = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$json->access_token.'&openid='.$json->openid.'&lang=zh_CN';
		$json2 = json_decode(http_request($post_url2));
		return $json2;
	}
	
	
	//判断是否微信浏览器
	private function isWeixin(){
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			// 非微信浏览器禁止浏览
			return 0;
		} else {
			// 微信浏览器，允许访问
			// echo "MicroMessenger";
			// 获取版本号
			preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
			return '<br>Version:'.$matches[2];
			//return 1;
		}		
	}
}