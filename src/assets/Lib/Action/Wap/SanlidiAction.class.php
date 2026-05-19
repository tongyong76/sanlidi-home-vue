<?php
class SanlidiAction extends Action {
	public $user;
	public function _initialize(){
		header('Content-type: text/html; charset=utf-8');
		
		$this->user['appid'] = 'wxbcf5260594fdf8d1';
		$this->user['appsecret'] = 'fe53d0dddd921af9efd3afcf08ff0b3a';
	}
	
	/**
	*微信接入
	*input：none
	*output：none
	*@gwj
	**/
	public function index(){
		//验证
		import("@.ORG.WeChat");
		
		define("TOKEN", "sanlidi");
		$wechat = new wechatCallbackapiTest();
		$wechat->valid();
		
		//初始化数据格式模版
		$textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>"; 
		$newsTpl = "<xml>
		    <ToUserName><![CDATA[%s]]></ToUserName>
		    <FromUserName><![CDATA[%s]]></FromUserName>
		    <CreateTime>%s</CreateTime>
		    <MsgType><![CDATA[%s]]></MsgType>
		    <ArticleCount>%s</ArticleCount>
		    <Articles>
		    <item>
		    <Title><![CDATA[%s]]></Title> 
		    <Description><![CDATA[%s]]></Description>
		    <PicUrl><![CDATA[%s]]></PicUrl>
		    <Url><![CDATA[%s]]></Url>
		    </item>
		    </Articles>
		    <FuncFlag>1</FuncFlag>
		    </xml> ";
		$musicTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<Music>
			<Title><![CDATA[%s]]></Title>
			<Description><![CDATA[%s]]></Description>
			<MusicUrl><![CDATA[%s]]></MusicUrl>
			<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
			</Music>
			<FuncFlag>0</FuncFlag>
			</xml>";		
	}
	
	//创建菜单
	public function createMenu(){
		$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->user['appid'].'&secret='.$this->user['appsecret'];
		$json=json_decode($this->curlGet($url_get));
		if (!$json->errmsg){
			//return array('rt'=>true,'errorno'=>0);
		}else {
			$this->error('获取access_token发生错误：错误代码'.$json->errcode.',微信返回错误信息：'.$json->errmsg);
		}
		
		$data = '{"button":[';
		$class=M('wx_menu_sanlidi')->where(array('pid'=>0,'is_del'=>0,'status'=>1))->limit(3)->order('ordid desc')->select();
		$kcount=M('wx_menu_sanlidi')->where(array('pid'=>0,'is_del'=>0,'status'=>1))->limit(3)->order('ordid desc')->count();
		$k=1;
		foreach($class as $key=>$vo){
			//主菜单
			$data.='{"name":"'.$vo['name'].'",';
			$c=M('wx_menu_sanlidi')->where(array('pid'=>$vo['id'],'is_del'=>0,'status'=>1))->limit(5)->order('ordid desc')->select();
			$count=M('wx_menu_sanlidi')->where(array('pid'=>$vo['id'],'is_del'=>0,'status'=>1))->limit(5)->order('ordid desc')->count();
			//子菜单
			$vo['url']=str_replace(array('&amp;'),array('&'),$vo['url']);
			if($c!=false){
				$data.='"sub_button":[';
			}else{
				if(!$vo['url']){
					$data.='"type":"click","key":"'.$vo['keyword'].'"';
				}else {
					$data.='"type":"view","url":"'.$vo['url'].'"';
				}
			}
			$i=1;
			foreach($c as $voo){
				$voo['url']=str_replace(array('&amp;'),array('&'),$voo['url']);
				if($i==$count){
					if($voo['url']){
						$data.='{"type":"view","name":"'.$voo['name'].'","url":"'.$voo['url'].'"}';
					}else{
						$data.='{"type":"click","name":"'.$voo['name'].'","key":"'.$voo['keyword'].'"}';
					}
				}else{
					if($voo['url']){
						$data.='{"type":"view","name":"'.$voo['name'].'","url":"'.$voo['url'].'"},';
					}else{
						$data.='{"type":"click","name":"'.$voo['name'].'","key":"'.$voo['keyword'].'"},';
					}
				}
				$i++;
			}
			if($c!=false){
				$data.=']';
			}

			if($k==$kcount){
				$data.='}';
			}else{
				$data.='},';
			}
			$k++;
		}
		$data.=']}';
		
		file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$json->access_token);
		$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$json->access_token;
		$rt=$this->api_notice_increment($url,$data);
		if($rt['rt']==false){
			$this->error('操作失败,curl_error:'.$rt['errorno']);
		}else{
			$this->success('操作成功','http://mm.33ly.com/Sldwxmenu/index');
			//echo U('Weixin/index');
		}
		exit;
	}
	
	//简单验证
	public function slogin(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state']?$_REQUEST['state']:0;
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->user['appid'].'&secret='.$this->user['appsecret'].'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode($this->curlGet($post_url));
		
		//是否第一次
		$exist = M('wx_user')->where('wx_openid="'.$json->openid.'"')->find();
		if($exist){
			SESSION('wx_openid',$exist['wx_openid']);
			$url = "http://m.33ly.com/Weixin/tejia";
			header("Location: $url");
		}else{
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->user['appid']."&redirect_uri=http%3A%2F%2Fm.33ly.com%2FWeixin%2Fulogin&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
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
		SESSION('wx_openid',$exist['wx_openid']);
		$url = "http://m.33ly.com/Weixin/tejia";
		header("Location: $url");
	}
	
	//获奖名单
	public function prize(){
		$id = $_REQUEST['id'];
		$list = M('wx_win')->where('win_date='.$id.' and is_del=0')->select();
		foreach($list as $key=>$value){
			$list[$key]['son'] = M('wx_win_name')->where('wid='.$value['id'])->select();
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	//微信支付接口测试
	public function paytest(){
		vendor('Weixin.WxPay.pub.config');
		vendor('Weixin.WxPayPubHelper');
		//echo WxPayConf_pub::APPID;
		
		$jsApi = new JsApi_pub();
		// if (!isset($_GET['code']))
		// {
			// //触发微信返回code码
			// $url = $jsApi->createOauthUrlForCode(WxPayConf_pub::JS_API_CALL_URL);
			// Header("Location: $url"); 
		// }else
		// {
			// //获取code码，以获取openid
			// $code = $_GET['code'];
			// $jsApi->setCode($code);
			// $openid = $jsApi->getOpenId();
		// }
		$openid = 'oOrekjmmExAQ40gRUAzM8I_LrnO8';
		
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		$unifiedOrder->setParameter("openid","$openid");//商品描述
		$unifiedOrder->setParameter("body","贡献一分钱");//商品描述
		$timeStamp = time();
		$out_trade_no = WxPayConf_pub::APPID."$timeStamp";
		$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		$unifiedOrder->setParameter("total_fee","1");//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型		
		
		$prepay_id = $unifiedOrder->getPrepayId();
		$jsApi->setPrepayId($prepay_id);
		$jsApiParameters = $jsApi->getParameters();
		$this->assign('jsApiParameters',$jsApiParameters);

		$this->display();
	}
	
	/****公共函数部分开始****/
	function api_notice_increment($url, $data){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if ($js['errcode']=='0'){
				return array('rt'=>true,'errorno'=>0);
			}else {
				$this->error('发生错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
			}
		}
	}	
	
	public function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}
	
	public function checkUser($code){
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->user['appid'].'&secret='.$this->user['appsecret'].'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode($this->curlGet($post_url));
		
		//$reflesh_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->user['appid'].'&grant_type=refresh_token&refresh_token='.$json->refresh_token;
		//$json3 = json_decode($this->curlGet($reflesh_url));
		
		$post_url2 = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$json->access_token.'&openid='.$json->openid.'&lang=zh_CN';
		$json2 = json_decode($this->curlGet($post_url2));
		return $json2;
	}	

}