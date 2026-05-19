<?php
//投票
class VoteAction extends BaseAction {
	public $wx_appid = 'wx9f98139e202c7003';
	public $wx_appsecret = 'fc569bb8896f29b5269fe49169d0c77a';
    
	//首页
	public function index(){
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//$wx_openid = 'oOrekjmmExAQ40gRUAzM8I_LrnO8';
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Vote/slogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		if(empty($wx_nickname)){
			$userInfo = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->find();
			if(!empty($userInfo) and $userInfo['exp_time']>time()){
				$wx_nickname = $userInfo['wx_nickname'];
				$wx_headimgurl = $userInfo['wx_headimgurl'];
 			}
		}
		if(empty($wx_nickname)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Vote/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		$id = $this->_param('id');
		if(empty($id) or $id == '请输入导游编号'){
			$this->assign('nav','index');
		}else{
			$map['id'] = $id;
			$this->assign('nav','search');
		}
		
		//导游库 daoyou
		$map['is_del'] = 0;
		$res = M('daoyou')->where($map)->order('vote desc')->select();
		if(empty($res)){
			$this->assign('nav','index');
			$res = M('daoyou')->where('is_del=0')->order('vote desc')->select();
		}
		$res2 = M('daoyou')->where('is_del=0')->order('id desc')->select();
		$dyList1 = json_encode($res);
		$dyList2 = json_encode($res2);
		$this->assign('dyList1',$dyList1);
		$this->assign('dyList2',$dyList2);
		//$this->assign('dyList',$res);
		//$this->assign('dyList2',$res2);
		$this->display();
    }
	
	//投票成功
	public function info(){
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		$wx_openid = SESSION('wx_openid');
		$wxInfo = M('daoyou_info')->where('wx_openid="'.$wx_openid.'"')->find();
		$this->assign('wxInfo',$wxInfo);
		
		$mod = M('goods');
		$data['is_del'] = 0;
		$data['is_show'] = 1;
		$data['minprice'] = array('neq',0);
		
		$data1 = $data;
		$data1['type_id'] = 1;
		$list1 = $mod->where($data1)->order('ordid desc')->limit(2)->select();
		$this->assign('list1',$list1);
		
		$data2 = $data;
		$data2['type_id'] = 2;
		$list2 = $mod->where($data2)->order('ordid desc')->limit(2)->select();
		$this->assign('list2',$list2);
		
		$data3 = $data;
		$data3['type_id'] = 3;
		$list3 = $mod->where($data3)->order('ordid desc')->limit(2)->select();
		$this->assign('list3',$list3);
		
		
		$this->display();
	}
	
	//活动规则页面
	public function rule(){
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);		
		
		$this->display();
	}
	
	//完善兑奖信息
	public function completeExchangeInfo(){
		$data['wx_openid'] = SESSION('wx_openid');
		$data['cname'] = $_REQUEST['cname'];
		$data['cphone'] = $_REQUEST['cphone'];
		$data['add_time'] = time();
		$wxInfo = M('daoyou_info')->where('wx_openid="'.$data['wx_openid'].'"')->find();
		if(!empty($wxInfo['wx_openid'])){
			M('daoyou_info')->where('wx_openid="'.$data['wx_openid'].'"')->save($data);
			$this->ajaxReturn('修改成功','success',1);
		}else{
			M('daoyou_info')->add($data);
			$this->ajaxReturn('提交成功','success',1);
		}		
	}
	
	//处理投票
	public function doVote(){//防止恶意投票
		// $data['daoyou_id'] = $_REQUEST['daoyou_id'];
		// //$data['wx_openid'] = $_REQUEST['wx_openid'];
		// $data['from_user'] = SESSION('wx_openid');
		// $now = M('daoyou_record')->where('from_user="'.$data['from_user'].'"')->count();
		// if($now >= 3){
			// $info = "每人最多只能投3票，感谢您的参与！";
			// $status = 0;
		// }else{
			// $res = M('daoyou_record')->where($data)->find();
			// if(!empty($res['record_id'])){
				// $info = "你已为TA投过票了，无法重复投票，感谢您的参与！";
				// $status = 0;
			// }else{
				// $data['add_time'] = time();
				// M('daoyou_record')->add($data);
				// M('daoyou')->where('id='.$data['daoyou_id'])->setInc('vote');
				// $info = "投票成功";
				// $status = 1;
			// }
		// }
		// $this->ajaxReturn('',$info,$status);
	}
	
	public function compInfo(){
		$wx_openid = SESSION('wx_openid');
		$info = M('daoyou_info')->where('wx_openid="'.$wx_openid.'"')->find();
		if(empty($info['cname'])){
			$this->ajaxReturn('','',0);
		}else{
			$this->ajaxReturn('','',1);
		}
	}
	
	//简单验证
	public function slogin(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state'];
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appid.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode(http_request($post_url));
		SESSION('wx_openid',$json->openid);
		$jump_url = "http://".$_SERVER['HTTP_HOST'].str_replace("△","&",$state);
		header("Location: $jump_url");
	}
	
	//获取用户信息并绑定
	public function ulogin(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state'];
		$userInfo = $this->checkUser($code);
		//存入数据库
		$data['wx_openid'] = $userInfo->openid;
		$data['wx_nickname'] = $userInfo->nickname;
		$data['wx_sex'] = $userInfo->sex;
		$data['wx_headimgurl'] = $userInfo->headimgurl;
		$data['exp_time'] = time() + 3600*24*7;
		$exist = M('wx_user')->where('wx_openid="'.$data['wx_openid'].'"')->find();
		if(empty($exist)){
			M('wx_user')->add($data);
		}else{
			M('wx_user')->where('wx_openid="'.$data['wx_openid'].'"')->save($data);
		}
		
		// //测试
		// $file_path = "Uploads/test.txt";
		// $fp = fopen($file_path,"a+");  //打开文件
		// $con1 = M('wx_user')->getlastsql();
		// fwrite($fp,$con1);
		
		SESSION('wx_openid',$data['wx_openid']);
		SESSION('wx_nickname',$data['wx_nickname']);
		SESSION('wx_headimgurl',$data['wx_headimgurl']);
		$jump_url = "http://".$_SERVER['HTTP_HOST'].str_replace("△","&",$state);
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