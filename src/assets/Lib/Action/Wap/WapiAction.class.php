<?php 
class WapiAction extends BaseAction 
{	
	private $wx_appid = 'wx9f98139e202c7003';
	private $wx_appsecret = 'fc569bb8896f29b5269fe49169d0c77a';
	
	/**
     * 微信简单验证
     */
	public function slogin(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state'];
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appid.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode(http_post($post_url),true);
		//SESSION(array('name'=>'wxopenid','expire'=>86400));
		SESSION('wx_openid',$json['openid']);
		$jump_url = "http://".$_SERVER['HTTP_HOST'].str_replace("△","&",$state);
		header("Location: $jump_url");
	}
	
	//通过snsapi_userinfo获取用户基本信息
	public function ulogin(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state'];
		$userInfo = $this->checkUser($code);
		//存入数据库
		$data['wx_openid'] = $userInfo->openid;
		$data['wx_nickname'] = $userInfo->nickname;
		$data['wx_sex'] = $userInfo->sex;
		$data['wx_headimgurl'] = $userInfo->headimgurl;
		$data['last_time'] = time();
		$data['is_subscribe'] = 0;
		$exist = M('UserWeixin')->where('wx_openid="'.$data['wx_openid'].'"')->find();
		if(empty($exist)){
			M('UserWeixin')->add($data);
		}else{
			M('UserWeixin')->where('wx_openid="'.$data['wx_openid'].'"')->save($data);
		}
		
		SESSION('wx_openid',$data['wx_openid']);
		SESSION('wx_nickname',$data['wx_nickname']);  //待用
		SESSION('wx_headimgurl',$data['wx_headimgurl']); //待用
		$jump_url = "http://".$_SERVER['HTTP_HOST'].str_replace("△","&",$state);
		header("Location: $jump_url");
	}
	
	//微信接口 snsapi_userinfo获取用户信息
	/**
     * snsapi_userinfo获取用户信息
	 *
     * @param string $code
     * @return array
     */
	public function checkUser($code){
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appid.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode(http_post($post_url));
		
		$post_url2 = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$json->access_token.'&openid='.$json->openid.'&lang=zh_CN';
		$json2 = json_decode(http_post($post_url2));
		return $json2;
	}
	
}
