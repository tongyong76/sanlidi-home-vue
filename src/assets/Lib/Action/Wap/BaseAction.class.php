<?php
//Wap公共部分
class BaseAction extends Action {
	private $wx_appid = 'wx9f98139e202c7003';
	private $wx_appsecret = 'fc569bb8896f29b5269fe49169d0c77a';	
	
	public function _initialize() {
		header("Content-Type:text/html; charset=UTF-8");  //编码
		
		import("@.ORG.BaiduHmWap");
		$_hmt = new _HMT("45988a729df28f554d96a5b9932b17e1");
		$_hmtPixel = $_hmt->trackPageView();
		$this->assign('BdWap',$_hmtPixel);
		
		//获取来源		
		if($_REQUEST['from']) session('ordacc',$_REQUEST['from']);
		if(!session('ordacc')){
			$url = $_SERVER['HTTP_REFERER'];
			$search = "/^(https?:\/\/)?([^\/]+)/i";
			preg_match($search,$url,$arr);
			if($arr[2]){
				$ordacc = $arr[2];
				session('ordacc',$ordacc);
			}			
		}
		
		//如果是微信浏览器则获取openid;
		if($this->isWeixin()){
			//我是谁
			$this->checkOpenid();
			$wx_openid = session('wx_openid')?session('wx_openid'):0;
			if(!empty($wx_openid)){
				$wxUserInfo = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->find();
				$this->assign('wxUserInfo',$wxUserInfo);
			}
			//根据wx_openid获取UID
			$uid = M('user')->where(array('wx_openid'=>session('wx_openid')))->getfield('id');
				//绑定帐号
			// if(empty($uid)){
				// $bindurl = U('Loginin/bind');
				// header('location:'.$bindurl);
			// }
			$this->assign('is_weixin',1);
		}else{
			$uid=$_COOKIE['wuid']?$_COOKIE['wuid']:0;					
		}
		
		//分享人信息 从哪来
		$wid = $_REQUEST['wid']?$_REQUEST['wid']:0;
		if(!empty($wid)){
			$shareInfo = M('wx_user')->where('wx_id='.$wid)->find();
			session('shareInfo',$shareInfo);
		}else{
			$shareInfo = session('shareInfo')?session('shareInfo'):'';
		}
		$this->assign('shareInfo',$shareInfo);
		
		$this->assign('is_sales',0);  //预设is_sales=0
		
		//判断是否登录
		$userMod=M("User");		
		if($uid){
			$userInfo=$userMod->where("id=$uid and is_del=0")->find();
			if(!$userInfo){
				setCookie('wuid',null,time()-1,'/');
				$url=C('site_domain');
				header('location:'.$url);
			}
			$this->assign("uid",$uid);
			if($userInfo['nickname']){
				$this->assign("uname",$userInfo['nickname']);
			}else{
				$this->assign("uname",substr($userInfo['phone'],0,3).'****'.substr($userInfo['phone'],7));
			}
			$this->assign("userInfo",$userInfo);
		}else{
			$this->assign("uid",0);
		}

		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
	}
	
	//短信
	public function smsxwkj($phone,$data){
		//include("postmsg.php");	
		import("@.ORG.Message");
		$mobile = $phone;
		$content = $data;
		$mess = new mess();
		$mess->_postSingle($mobile,$content);
		//$mess->_getResponse();
	}
	
	public function verify(){
		import('ORG.Util.Image');
		Image::buildImageVerify ();
		//$this->assign('verify',session('verify'));
		//echo session('verify');
	}

	//判断是否微信浏览器
	public function isWeixin(){
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
	
	//获取openid
	private function checkOpenid(){
		$wx_openid = session('wx_openid');		
        if(empty($wx_openid)){
            $state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
	}
	
}