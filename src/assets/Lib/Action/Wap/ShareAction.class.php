<?php
//收客宝
class ShareAction extends BaseAction {
	public $wx_appid = 'wx9f98139e202c7003';
	public $wx_appsecret = 'fc569bb8896f29b5269fe49169d0c77a';
    
	//首页
	public function index(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		//整站分享，放在微信信息下面
		$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		$share['title'] = '三三旅游网——苏州旅行社直营第一品牌';
		$share['desc'] = '三三旅游，旅途无忧';
		$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
		$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].'/Uploads/logo.png';    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//广告幻灯
		
		//热门产品推荐  参数  1.是否参与返利   2.是否热推排序   3.ordid排序
		//$map['is_share'] = 1;
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['is_share'] = 1;
		$map['minprice'] = array('neq',0);
		$order['is_hot'] = 'desc';
		$order['ordid'] = 'desc';
		$list = M('goods')->field('id,name,subname,minprice,share_price,imgurl')->where($map)->order($order)->limit(10)->select();
		$this->assign('list',$list);
		
		//是否有能力
		$is_handle = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('type_id');
		$this->assign('is_handle',$is_handle);
		
		//快捷按钮（备选）
		
		$this->assign('nav','nav_1');
		$this->display();
    }
	
	//挑选产品
	public function chooseProduct(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		//整站分享，放在微信信息下面
		$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		$share['title'] = '三三旅游网——苏州旅行社直营第一品牌';
		$share['desc'] = '三三旅游，旅途无忧';
		$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
		$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].'/Uploads/logo.png';    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//是否有能力
		$is_handle = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('type_id');
		$this->assign('is_handle',$is_handle);
		
		//产品集  根据分类ID获取
		$map['is_del'] = 0;
		if($is_handle){		//如果具有业务处理能力，则显示全部商品
			$map['is_share'] = 0;  
		}else{
			$map['is_share'] = 1;  
		}
		$map['is_show'] = 1;		 
		$map['minprice'] = array('neq',0);	
		$order['is_hot'] = 'desc';
		$order['ordid'] = 'desc';
		
		$map1 = $map;
		$map1['type_id'] = 1;
		$list1 = M('goods')->field('id,name,subname,minprice,share_price,imgurl')->where($map1)->order($order)->limit(10)->select();
		$this->assign('list1',$list1);
		$map2 = $map;
		$map2['type_id'] = 2;
		$list2 = M('goods')->field('id,name,subname,minprice,share_price,imgurl')->where($map2)->order($order)->limit(10)->select();
		$this->assign('list2',$list2);
		$map3 = $map;
		$map3['type_id'] = 3;
		$list3 = M('goods')->field('id,name,subname,minprice,share_price,imgurl')->where($map3)->order($order)->limit(10)->select();
		$this->assign('list3',$list3);
		
		//分享特定产品（前端）
		
		//上架到个人首页（业务员A,AJAX更新关联,前端）
		
		$this->assign('nav','nav_2');
		$this->display();
    }
	
	
	//搜索产品，兼顾分类
	public function searchProduct(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}		
		
		//整站分享，放在微信信息下面
		$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		$share['title'] = '三三旅游网——苏州旅行社直营第一品牌';
		$share['desc'] = '三三旅游，旅途无忧';
		$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
		$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].'/Uploads/logo.png';    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//搜索结果集  根据关键词
		if($_REQUEST['type_id']){
			$map['type_id'] = $_REQUEST['type_id'];
			$this->assign('type_id',$map['type_id']);
		}
		if($_REQUEST['keyword']){
			$map['name|subname'] = array('like','%'.$_REQUEST['keyword'].'%');
			$this->assign('keyword',$_REQUEST['keyword']);
			unset($map['type_id']);
			$this->assign('type_id',0);
		}
		
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['is_share'] = 1;   
		$map['minprice'] = array('neq',0);	
		$order['is_hot'] = 'desc';
		$order['ordid'] = 'desc';
		$list = M('goods')->field('id,name,subname,minprice,share_price,imgurl')->where($map)->order($order)->limit(10)->select();
		$this->assign('list',$list);
		
		//返回挑产品页入口
		
		$this->assign('nav','nav_2');
		$this->display();
    }
	
	//产品详情
	public function productDetail(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//输入参数
		$id = $this->_param('id');	//产品标识
		$info = M('goods')->where("id=$id")->find();
		$info['dep'] = $this->getDeparture($info['id'],$info['sign_up']);
		$info['dep'] = $info['dep']?$info['dep']:0;
		$this->assign('info',$info);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		
		//单独分享，分享信息
		$share['title'] = '<'.$info['name'].'>'.$info['subname'];
		$share['desc'] = '<'.$info['name'].'>'.$info['subname'];
		$share['link'] = 'http://'.$_SERVER['HTTP_HOST']."/tour/xianlu-".$info['id'].".html?wid=".$wx_id;
		$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].$info['imgurl'];    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//获取行程
		$trip = M('trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
		foreach($trip as $key=>$value){
			$trip[$key]['dinner'] = json_decode($value['dinner']);
			$scene = explode(',',$value['scene']);
			foreach($scene as $skey=>$svalue){
				if($svalue && $skey<3){
					$res = M('scenic')->where('name="'.$svalue.'"')->find();
					$trip[$key]['scenic'][$skey] = $res;
				}
			}
		}
		
		$this->assign('trip',$trip);
		$this->assign('nav','nav_2');
		$this->display();
	}
	
	//更新分享记录
	public function saveShareOutRecord(){
		
		$data['wx_openid']=isset($_POST['wx_openid'])?$_POST['wx_openid']:'';
		$data['share_status']=isset($_POST['share_status'])?$_POST['share_status']:1;
		$data['share_type']=isset($_POST['share_type'])?$_POST['share_type']:1;
		$data['link_url']=isset($_POST['link_url'])?$_POST['link_url']:'';
		$data['add_time'] = time();
		M('wx_share_out')->add($data);
		
	}
	
	//我的订单/我的分享
	public function myOrder(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');	
		$wx_headimgurl = SESSION('wx_headimgurl');
		$this->assign('wx_nickname',$wx_nickname);
		$this->assign('wx_headimgurl',$wx_headimgurl);
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		if(empty($wx_nickname)){
			$userInfo = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->find();
			if(!empty($userInfo) and $userInfo['exp_time']>time()){
				$wx_nickname = $userInfo['wx_nickname'];
				$wx_headimgurl = $userInfo['wx_headimgurl'];
				$this->assign('wx_nickname',$wx_nickname);
				$this->assign('wx_headimgurl',$wx_headimgurl);
 			}
		}
		if(empty($wx_nickname)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}		
		
		//定单集   （A可点入查看订单详情，B不行，只显示结果）
		$wx_openid = SESSION('wx_openid');
		$orderList = M('order')->where('ordshare="'.$wx_openid.'"')->order('add_time desc')->select();
		$this->assign('orderList',$orderList);
		
		//是否有能力
		$is_handle = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('type_id');
		$this->assign('is_handle',$is_handle);
		
		// //总金额
		// $res = M('order as o')->field('sum(g.share_price*(o.adult_num + o.child_num)) as total_income')->join('33_goods as g on g.id=o.gid')->where('o.ordshare="'.$wx_openid.'" and ordstatus=3')->find();
		// $total_income = $res['total_income'];
		// $this->assign('total_income',$total_income);
		
		//$this->assign('nav','nav_3');
		$this->display();
    }
	
	//订单详情
	public function orderDetail(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		$wx_nickname = SESSION('wx_nickname');
		$this->assign('wx_nickname',$wx_nickname);
		$wx_headimgurl = SESSION('wx_headimgurl');
		$this->assign('wx_headimgurl',$wx_headimgurl);
		
		//总金额
		$wx_openid = SESSION('wx_openid');
		$res = M('order as o')->field('sum(g.share_price*(o.adult_num + o.child_num)) as total_income')->join('33_goods as g on g.id=o.gid')->where('o.ordshare="'.$wx_openid.'" and ordstatus=3')->find();
		$total_income = $res['total_income'];
		$this->assign('total_income',$total_income);
		
		//整站分享，放在微信信息下面
		$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		$share['title'] = '三三旅游网——苏州旅行社直营第一品牌';
		$share['desc'] = '三三旅游，旅途无忧';
		$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
		$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].'/Uploads/logo.png';    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//订单详细信息
		$id = $_REQUEST['id'];
		$orderInfo = M('order')->field('*,33_order.id as id')->join('33_user as u on u.id=33_order.uid')->where('33_order.id='.$id)->find();
		switch($orderInfo['ordstatus']){
			case 0:
				$orderInfo['status_name'] = '等待确认';
				break;
			case 1:
				$orderInfo['status_name'] = '等待支付';
				break;
			case 2:
				$orderInfo['status_name'] = '待发出团单';
				break;
			case 3:
				$orderInfo['status_name'] = '交易完成';
				break;
			case 5:
				$orderInfo['status_name'] = '沟通进行中';
				break;
			default:
				$orderInfo['status_name'] = '沟通进行中';
				break;
		}
		$this->assign('orderInfo',$orderInfo);
		$this->display();		
		
		//跟踪导入记录
		
		//价格修改权限（待定）
		
		//订单状态修改

    }
	
	//订单状态修改
	public function orderHandel(){
		$wx_openid = SESSION('wx_openid');
		$orderId = $_REQUEST['orderId'];
		$orderStatus = $_REQUEST['orderStatus'];
		$map['id'] = $orderId;
		$mod=M('order');
		switch($orderStatus){
			case 911:
				//1订单状态（-2后台取消）
				$dataOrder['ordstatus'] = -2;
				$dataOrder['is_edit'] = 0;
				$dataOrder['clsrz'] = $_REQUEST['clsrz'];
				$mod->where($map)->save($dataOrder);
				//2操作记录
				$dataModify['modify_type'] = '后台取消';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = '业务员操作';
				$dataModify['wx_openid'] = $wx_openid;
				$dataModify['order_id'] = $orderId;
				M('order_modify')->add($dataModify);
				break;
			case 910:
				//1订单状态（3交易完成）
				$dataOrder['ordstatus'] = 3;
				$dataOrder['is_edit'] = 0;
				$mod->where($map)->save($dataOrder);				
				//2操作记录
				$dataModify['modify_type'] = '支付完成';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = '业务员操作';
				$dataModify['wx_openid'] = $wx_openid;
				$dataModify['order_id'] = $orderId;
				M('order_modify')->add($dataModify);
				break;
			case 912:
				//上传出团通知书，暂无
				break;
			case 1:
				//1修改支付方式
				$dataOrder['ordpay'] = $ordpay;
				//2订单状态（1等待支付）
				$dataOrder['ordstatus'] = 1;
				$dataOrder['is_edit'] = 0;
				$mod->where($map)->save($dataOrder);
				//2操作记录
				$dataModify['modify_type'] = '等待支付';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = '业务员操作';
				$dataModify['wx_openid'] = $wx_openid;
				$dataModify['order_id'] = $orderId;
				M('order_modify')->add($dataModify);
				//发短信给客户
				//短信…………
				$orderInfo = $mod->where($map)->find();
				$msgPhone = $orderInfo['cphone'];
				$msgData = "您的订单".$orderInfo['ordname']."可以支付了，请登录后台进行支付http://m.33ly.com/uc";
				sendmessage($msgPhone,$msgData);
				break;
			case 3:
				//暂无
				break;
			case 909:
				//1修改支付方式
				$dataOrder['ordpay'] = 0;
				//2订单状态（4转门店跟进）
				$dataOrder['ordstatus'] = 4;
				$dataOrder['is_edit'] = 0;
				$mod->where($map)->save($dataOrder);
				//2操作记录
				$dataModify['modify_type'] = '转门店跟进';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = '业务员操作';
				$dataModify['wx_openid'] = $wx_openid;
				$dataModify['order_id'] = $orderId;
				M('order_modify')->add($dataModify);
				break;
			default:
				$dataOrder['ordstatus'] = 5;
				$mod->where($map)->save($dataOrder);
				$dataModify['modify_type'] = '沟通记录';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = $reason;
				$dataModify['admin_id'] = $this->uid;
				$dataModify['order_id'] = $orderId;
				M('order_modify')->add($dataModify);
				break;
		}
		//缺返回状态
		$this->ajaxReturn(M('order_modify')->getlastsql(),0,1);
		
	}
	
	//我的分享
	public function myShare(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		//整站分享，放在微信信息下面
		$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		$share['title'] = '三三旅游网——苏州旅行社直营第一品牌';
		$share['desc'] = '三三旅游，旅途无忧';
		$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
		$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].'/Uploads/logo.png';    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//分享记录
		$wx_openid = SESSION('wx_openid');
		$shareList = M('wx_sharerecord')->field('order_url,count(order_url) as num')->where('share_user="'.$wx_openid.'"')->group('order_url')->select();
		foreach($shareList as $key=>$value){
			preg_match_all("/\/id\/(.*?)\.html/",$value['order_url'],$matches);
			$gid = $matches[1][0];
			$gInfo = M('goods')->where('id='.$gid)->find();
			$shareList[$key]['gname'] = "<".$gInfo['name'].">".$gInfo['subname'];
		}
		$this->assign('shareList',$shareList);
		
		//总金额
		$res = M('order as o')->field('sum(g.share_price*(o.adult_num + o.child_num)) as total_income')->join('33_goods as g on g.id=o.gid')->where('o.ordshare="'.$wx_openid.'" and ordstatus=3')->find();
		$total_income = $res['total_income'];
		$this->assign('total_income',$total_income);
		
		//var_dump($shareList);
		$this->assign('nav','nav_4');
		$this->display();
    }
	
	//我的佣金
	public function myIncome(){
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		//整站分享，放在微信信息下面
		$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		$share['title'] = '三三旅游网——苏州旅行社直营第一品牌';
		$share['desc'] = '三三旅游，旅途无忧';
		$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
		$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].'/Uploads/logo.png';    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//分享成功的佣金记录
		$wx_openid = SESSION('wx_openid');
		$incomeList = M('order as o')->field('o.ordsn,o.add_time,g.share_price')->join('33_goods as g on g.id=o.gid')->where('o.ordshare="'.$wx_openid.'"')->order('add_time desc')->select();
		$this->assign('incomeList',$incomeList);
		
		//总金额
		$res = M('order as o')->field('sum(g.share_price*(o.adult_num + o.child_num)) as total_income')->join('33_goods as g on g.id=o.gid')->where('o.ordshare="'.$wx_openid.'" and ordstatus=3')->find();
		$total_income = $res['total_income'];
		$this->assign('total_income',$total_income);
		
		$this->assign('nav','nav_4');
		$this->display();
    }
	
	//个人首页
	public function myHomepage(){
		//布局 无业务逻辑
		
		$this->display();
    }
	
	//个人中心
	public function myCenter(){	
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		//整站分享，放在微信信息下面
		$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		$share['title'] = '三三旅游网——苏州旅行社直营第一品牌';
		$share['desc'] = '三三旅游，旅途无忧';
		$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
		$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].'/Uploads/logo.png';    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//总金额
		$res = M('order as o')->field('sum(g.share_price*(o.adult_num + o.child_num)) as total_income')->join('33_goods as g on g.id=o.gid')->where('o.ordshare="'.$wx_openid.'" and ordstatus=3')->find();
		$total_income = $res['total_income'];
		$this->assign('total_income',$total_income);
	
		$this->assign('nav','nav_4');
		$this->display();
	}
	
	//个人信息
	public function myProfile(){
		//用户信息   根据ID获取
		
		//修改保存用户信息
		
		//基于微信用户信息
		
		$this->display();
    }
	
	//延迟加载
	public function getMore(){
		$mod = M('goods');
		$sid = $_REQUEST['sid'];
		$type_id = $_REQUEST['type_id']?$_REQUEST['type_id']:0;
		$keyword = $_REQUEST['keyword']?$_REQUEST['keyword']:0;
	
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['is_share'] = 1;     //需要修改
		if($type_id){
			$map['type_id'] = $type_id;
		}
		if($keyword){
			$map['name|subname'] = array('like','%'.$keyword.'%');
		}
		$map['minprice'] = array('neq',0);
		$order['is_hot'] = 'desc';
		$order['ordid'] = 'desc';
		$num = 5;
		$list = M('goods')->field('id,name,subname,minprice,share_price,imgurl')->where($map)->order($order)->limit($sid,$num)->select();
		if($list){
			$this->assign('list',$list);
			$data['list'] = $this->fetch('ajax_share_list');
			$data['sid'] = $sid + $num;
			$this->ajaxReturn($data,'',1);
		}else{
			$this->ajaxReturn(M('goods')->getlastsql(),$_REQUEST['type_id'],0);
		}
	}
	
	//简单验证
	public function slogin(){
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state'];
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appid.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode(http_request($post_url));
		//SESSION(array('name'=>'wx_openid','expire'=>86400));
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
		
		SESSION(array('name'=>'wx_openid','expire'=>86400));
		SESSION('wx_openid',$data['wx_openid']);
		SESSION(array('name'=>'wx_nickname','expire'=>86400));
		SESSION('wx_nickname',$data['wx_nickname']);
		SESSION(array('name'=>'wx_headimgurl','expire'=>86400));
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
	
	/**
     * 获取最近行程
     * @access public
     * @param integer $id 线路id
     * @return query
     */
	public function getDeparture($id,$sday){
		$nowtime = strtotime(date(Ymd));
		$exptime = $nowtime + 3600*24*$sday;
		$query ='';
		$mod = M('departure_time');
		$list = $mod->where('pid='.$id.' and departure_time>='.$exptime.' and is_del=0')->order('departure_time')->limit(7)->select();
		//$this->assign('firstDep',date('Y-m-d',$list[0]['departure_time']));
		foreach($list as $key=>$value){
			if($key==0) $query .= date('n/d',$value['departure_time']);
			if($key<5 and $key>0) $query .= '，'.date('n/d',$value['departure_time']);
			if($key==5) $query .= '...';
		}
		return $query;
		//return date('Ymd',$exptime);
	}
	
	//收集收客宝信息
	public function collectUserInfo(){
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		if(IS_POST){
			$data = M('wx_user')->create();
			$exist = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			if(empty($exist)){
				$data['wx_openid'] = $wx_openid;
				M('wx_user')->add($data);
				$this->success('提交成功',U('Share/collectUserInfo'));
			}else{
				M('wx_user')->where('wx_openid="'.$wx_openid.'"')->save($data);
				$this->success('更新成功',U('Share/collectUserInfo'));
			}
		}else{
			$info = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->find();
			$shop = C('shop');
			$this->assign('info',$info);
			$this->assign('shop',$shop);
			$this->display();
		}
		
	}
	
	//微信单活动
	public function pgone(){
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$wx_nickname = SESSION('wx_nickname');
		$wx_headimgurl = SESSION('wx_headimgurl');
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
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
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		
		if(IS_POST){
			$data = M('wx_pgone')->create();
			$exist = M('wx_pgone')->where('wx_openid="'.$wx_openid.'"')->getfield('pg_id');
			if(empty($exist)){
				$data['wx_openid'] = $wx_openid;
				M('wx_pgone')->add($data);
				$this->success('提交成功',U('Share/pgone'));
			}		
		}else{
			$pg_id = M('wx_pgone')->where('wx_openid="'.$wx_openid.'"')->getfield('pg_id');
			$pg_shop = M('wx_pgone')->where('wx_openid="'.$wx_openid.'"')->getfield('shop');
			$this->assign('pg_id',sprintf('%04d',$pg_id));
			$shop = C('shop');
			$this->assign('pg_shop',$shop[$pg_shop]);
			$this->assign('shop',$shop);
			$this->display();
		}
		
	}
	
	//微信活动结果
	public function pgone_result(){
		// $list = M('wx_pgone')->select();
		// $shop = C('shop');
		// echo '姓名-电话-领取门店<br>';
		// foreach($list as $key=>$value){
			// echo $value['pg_id'].'-'.$value['name'].'-'.$value['phone'].'-'.$shop[$value['shop']].'<br>';
		// }
		echo "result";
	}
	
	//微信关注页
	public function subscribe(){
		
	}
	
	//临时处理
	public function dosth(){
		M('wx_user')->where('wx_id=33')->setfield('real_email','2355933328@qq.com');
	}
	
	//业务员注册
	public function sreg(){
		//获取微信信息
		$wx_openid = SESSION('wx_openid');
		$userInfo = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->find();
		var_dump($userInfo);
		$wx_nickname = $userInfo['wx_nickname'];
		$wx_headimgurl = $userInfo['wx_headimgurl'];
		//if($gameInfo['wx_openid'] == $wx_openid) header("Location: http://m.33ly.com/Game/index");
		if(empty($wx_openid)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/slogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
			header("Location: $url");
		}
		if(empty($wx_nickname)){
			$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode('http://m.33ly.com/Share/ulogin');
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
			header("Location: $url");
		}		
	}

}