<?php
class TourAction extends BaseAction {
	//分类页
	public function category(){
		$id = $_REQUEST['id'];
		$this->assign('id',$id);
		//判断层级
		$cateInfo = M('goods_cate')->where('id='.$id)->find();

		// $hotCate = M('goods_cate')->where('pid='.$id.' and is_del=0')->order('ordid desc')->limit(4)->select();
		// $this->assign('hotCate',$hotCate);
		if($cateInfo['floor'] == 2 and $cateInfo['is_end'] == 0){
			$cateList = M('goods_cate')->where('pid='.$id.' and is_del=0')->order('ordid desc')->getfield('id',true);
			$smap['cate_id'] = array('in',$cateList);
			$goods_ids = M('goods_cate_rela')->where($smap)->group('goods_id')->getfield('goods_id',true);
			unset($smap);
		}
		if($cateInfo['floor'] == 3 or ($cateInfo['floor'] == 2 and $cateInfo['is_end'] == 1)){
			$goods_ids = M('goods_cate_rela')->where('cate_id="'.$id.'"')->getfield('goods_id',true);
		}
		$map['id'] = array('in',$goods_ids);


		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		$list = M('goods')->where($map)->order('ordid desc,add_time desc')->limit(6)->select();
		
		$this->assign('list',$list);
		$this->assign('title',$cateInfo['name']);
		$this->display();
	}
	
	public function getMore(){
		$mod = M('goods');
		$id = $_REQUEST['id'];
		$sid = $_REQUEST['sid'];
		
		//判断层级
		$cateInfo = M('goods_cate')->where('id='.$id)->find();
		switch($cateInfo['floor']){
			case 2:
				$hotCate = M('goods_cate')->where('pid='.$id.' and is_del=0')->order('ordid desc')->limit(4)->select();
				$this->assign('hotCate',$hotCate);
				$cateArr = M('goods_cate')->where('pid='.$id.' and is_del=0')->order('ordid desc')->getField('id',true);
				$map['cate_id'] = array('in',$cateArr);
				break;
			case 3:
				$hotCate = M('goods_cate')->where('pid='.$cateInfo['pid'].' and is_del=0')->order('ordid desc')->limit(4)->select();
				$this->assign('hotCate',$hotCate);
				$map['cate_id'] = $id;
				break;
			default:
				break;
		}
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		$num = 5;
		$list = M('goods')->where($map)->order('ordid desc,add_time desc')->limit($sid,$num)->select();
		if($list){
			$this->assign('list',$list);
			$data['list'] = $this->fetch('ajax_tour_list');
			$data['sid'] = $sid + $num;
			$this->ajaxReturn($data,'',1);
		}else{
			$this->ajaxReturn('','',0);
		}
	}
	
    public function detail(){
		$id = $_REQUEST['id'];
		//获取seseion
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$mod = M('goods as g');
		
		$info = $mod->join('33_goods_detail as gd on gd.goods_id=g.id')
		->where('g.id='.$id)->find();
		
		$info['dep'] = $this->getDeparture($info['id'],$info['sign_up']);
		//$info['dep'] = $this->getDeparture($info['id'],1);
		$info['dep'] = $info['dep']?$info['dep']:0;
		//$info['imgurl'] = image(__ROOT__.$info['imgurl'],320,200,1);
		
		switch($info['type_id']){
			case 1:
				$info['tt'] = "周边游";
				break;
			case 2:
				$info['tt'] = "国内游";
				break;
			case 3:
				$info['tt'] = "出境游";
				break;
		}
		$this->assign('info',$info);
		
		//获取行程
		$trip = M('trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
		foreach($trip as $key=>$value){
			$trip[$key]['dinner'] = json_decode($value['dinner']);
			$scene = explode(',',$value['scene']);
			foreach($scene as $skey=>$svalue){
				if($svalue && $skey<3){
					$res = M('scenic')->where('name="'.$svalue.'" and is_del=0')->find();
					$trip[$key]['scenic'][$skey] = $res;
				}
			}
			//获取景点
			//$trip[$key]['scene'] = explode(',',$value['scene']);
		}
		$this->assign('trip',$trip);
		
		// 收客宝分享记录2016-10-26
		// //分享信息
		// $wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
		// $wid = $_REQUEST['wid'];  //分享人标识
		
		// $shareInfo = M('wx_user')->where('wx_id='.$wid)->find();
		
		// if(!empty($shareInfo['wx_openid'])){
			// SESSION('share_user',$shareInfo['wx_openid']);
			// $recordmap['share_user'] = $shareInfo['wx_openid'];
			// $recordmap['order_user'] = $wx_openid;
			// $recordmap['order_url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			// $recordmap['status'] = 1;
			// $recordmap['add_time'] = time();
			// M('wx_sharerecord')->add($recordmap);
		// }else{
			// $shareInfo = M('wx_user')->where('wx_openid="'.session('share_user').'"')->find();
		// }
		// $this->assign('shareInfo',$shareInfo);
		
		//去哪儿
		if($this->wxUserInfo['type_id'] == 1){
			$wx_openid = SESSION('wx_openid');
			$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			$share['title'] = '<'.$info['name'].'>'.$info['subname'];
			$share['desc'] = '<'.$info['name'].'>'.$info['subname'];
			$share['link'] = 'http://'.$_SERVER['HTTP_HOST']."/tour/xianlu-".$info['id'].".html?wid=".$wx_id;
			$share['icon'] = 'http://'.$_SERVER['HTTP_HOST'].$info['imgurl'];    //分享图标
			$share['wx_openid'] = $wx_openid;
			$this->assign('share',$share);
			$this->assign('is_sales',1);
		}else{
			
		}
		
		
		
		$this->display();
    }
	
    // public function detail2(){
		// $id = $_REQUEST['id'];
		// //获取seseion
		// if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		// $mod = M('goods');
		
		// $info = $mod->where('id='.$id)->find();
		// //$info['dep'] = $this->getDeparture($info['id'],$info['sign_up']);
		// $info['dep'] = $this->getDeparture($info['id'],1);
		// $info['dep'] = $info['dep']?$info['dep']:0;
		// //$info['imgurl'] = image(__ROOT__.$info['imgurl'],320,200,1);
		
		// switch($info['type_id']){
			// case 1:
				// $info['tt'] = "周边游";
				// break;
			// case 2:
				// $info['tt'] = "国内游";
				// break;
			// case 3:
				// $info['tt'] = "出境游";
				// break;
		// }
		// $this->assign('info',$info);
		
		// //获取行程
		// $trip = M('trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
		// foreach($trip as $key=>$value){
			// $trip[$key]['dinner'] = json_decode($value['dinner']);
			// $scene = explode(',',$value['scene']);
			// foreach($scene as $skey=>$svalue){
				// if($svalue && $skey<3){
					// $res = M('scenic')->where('name="'.$svalue.'"')->find();
					// $trip[$key]['scenic'][$skey] = $res;
				// }
			// }
			// //获取景点
			// //$trip[$key]['scene'] = explode(',',$value['scene']);
		// }
		// $this->assign('trip',$trip);
		
		// $this->display();
    // }
	
	public function order(){
		if($_POST['submit']){
			$orderMod = M('order');
			$data = $orderMod->create();
			$gid = $_REQUEST['gid'];
			$is_direct = M('goods')->where('id='.$gid)->getfield('is_direct');
			if($is_direct) $data['ordstatus'] = 1;
			$data['ordsn'] = 'SN'.date('YmdHis');			
			$data['add_time'] = time();
			$data['ordfrom'] = 1;
			$data['ordsrc'] = "苏州";
			$data['ordacc'] = session('ordacc');
			$shareInfo = session('shareInfo');
			$data['ordshare'] = $shareInfo['wx_openid'];	
			
			//是否登录$this->uid
			if($this->uid){
				$data['uid'] = $this->uid;
			}else{
				//判断电话号码是否已存在
				$is_exist = M('user')->where('phone="'.$data['cphone'].'" and (status=0 or status=1 or status=2) and is_del=0')->getfield('id'); //查找是否存在
				if($is_exist){  //存在，跳转到登录页面
					session('ordDate',$data);  //保存订单信息
					$jumpUrl = urlencode($_SERVER['HTTP_REFERER']);
					header("location: ".__GROUP__."/User/login.html?requesturl=".$jumpUrl);
				
				}else{  //不存在，直接注册,并发送短信
					$addData['phone'] = $data['cphone'];
					$addData['realname'] = $data['cname'];		
					//$randStr = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
					//$rand = substr($randStr,0,6);
					//$addData['password'] = md5($rand);
					$addData['password'] = md5('66667777');
					$addData['status'] = 2;
					$addData ['reg_ip'] = getClientIp();
					$addData ['reg_time'] = time();
					$addData ['last_ip'] = getClientIp ();
					$addData ['last_time'] = time ();
					$newId2 = M('user')->add($addData);
					//发送短信提醒
					$msgdata = "您已成为三三旅游的注册会员，帐号为您的手机号码，默认密码为66667777，请您登录后尽快修改，感谢您对三三旅游的支持！";
					$msgphone= $data['cphone'];
					$reslut = $this->smsxwkj($msgphone,$msgdata);
					
					//自动用新行号登录
					$cookie = 1;
					if ($cookie == true) {
						setcookie ( 'wuid', $newId2, time () + 3600 * 24 * 7, '/' );
					} else {
						setcookie ( 'wuid', $newId2, '/' );
					}
					
					//获取用户ID
					$data['uid'] = $newId2;
				}

			}
			
			if($data['uid']){ //更新UID  插入订单数据到数据库
				$newOrderId = $orderMod->add($data);
				//echo $orderMod->getlastsql();
				
				//预定成功发送通知邮件到客服
				$userInfo = M('user')->where('id='.$data['uid'])->find();
				$this->assign('orderMailDetail', array(
					'ordsn'		=>	$data['ordsn'],  									//订单编号
					'add_time'	=>	$data['add_time'],									//下单时间
					'ordstart'	=>	$data['ordstart'],									//出游时间
					'ordname'	=>  $data['ordname'],									//出游线路
					'ordnum'	=>	$data['adult_num'].'大'.$data['child_num'].'小',	//出行人数
					'ordprice'	=>	$data['ordprice'],									//订单总价
					'cname'		=>	$data['cname'],										//下单人姓名
					'cphone'	=>	$data['cphone'],									//下单人电话
					'cinfo'		=>	$data['cinfo'],								    	//备注
				));
				
				$mailto = '76597304@qq.com';//客服邮箱			
				$mailtitle = '新订单！'.$userInfo['realname'].'|'.$userInfo['phone'];//标题
				$mailmessage = $this->fetch('Public:orderMailForm');//邮件正文
				$mailfrom = '三三旅游后台订单';//发件方名称
				SendMail($mailto,$mailtitle,$mailmessage,$mailfrom);
				
				if(!empty($data['ordshare'])){
					$mailto4 = M('wx_user')->where('wx_openid="'.$data['ordshare'].'"')->getField('real_email');
					SendMail($mailto4,$mailtitle,$mailmessage,$mailfrom);
				}else{
					$mailto1 = '2355677401@qq.com';
					$mailto3 = '475810549@qq.com';   //沈
					SendMail($mailto1,$mailtitle,$mailmessage,$mailfrom);
					SendMail($mailto3,$mailtitle,$mailmessage,$mailfrom);
				}
				
				//$this->success('预定成功，即将跳转到支付宝进行支付',U('Pay/doalipay',array('id'=>$newOrderId)));
				//if($is_direct){
					//$jumpUrl = U('Pay/doalipay',array('id'=>$newOrderId));
					//中间加一个支付选择页面，
				//	$jumpUrl = U('Tour/payment',array('id'=>$newOrderId));
				//}else{
//					$jumpUrl = U('Tour/examine');
				//直接支付改到完善出行人信息以后执行
				$jumpUrl = U('Tour/card',array('oid'=>$newOrderId));    //转到出行人信息填写
				//}
				//
				
				header("location: ".$jumpUrl);
			}
			
			
		}else{
			$gid = $_REQUEST['gid'];
			//订单记录session  //太复杂，暂缓实现
			// $sdata = session('ordDate');
			// if($sdata['gid'] == $gid){ 
				// $this->assign('sdata',$sdata);
			// }
			
			$info = M('goods')->where('id='.$gid)->find();
			$nowtime = time()+3600*24*($info['sign_up'] - 1);
			$dInfo = M('departure_time')->where('pid='.$gid.' and departure_time>'.$nowtime.' and is_del=0')->order('departure_time asc')->select();
			$this->assign('gid',$gid);
			$this->assign('info',$info);
			$this->assign('dInfo',$dInfo);
			$this->display();
		}
	}
	
	//选择支付方式页面
	public function payment(){
		// //微信SDK  用来控制显示
		// $user['appid'] = 'wx9f98139e202c7003';
		// $user['appsecret'] = 'fc569bb8896f29b5269fe49169d0c77a';
		// vendor('Weixin.jssdk');
		// $jssdk = new JSSDK($user['appid'],$user['appsecret']);
		// $signPackage = $jssdk->GetSignPackage();
		// $this->assign('signPackage',$signPackage);
		
		//订单信息
		$id = $_REQUEST['id'];
		$orderInfo = M('order')->where('id='.$id)->find();
		$this->assign('orderInfo',$orderInfo);
		$is_weixin = 0;
		
		if($this->isWeixin()){//只有微信才执行
			//微信支付
			ini_set('date.timezone','Asia/Shanghai');
			vendor('Wxpay.Exception');
			vendor('Wxpay.Config');
			vendor('Wxpay.Data');
			vendor('Wxpay.Api');
			vendor('Wxpay.JsApiPay');
		
			//获取用户openid
			$tools = new JsApiPay();
			$openid = session('wx_openid');
			$state = $id;
			if(!$openid){
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx9f98139e202c7003&redirect_uri=http%3A%2F%2Fm.33ly.com%2FTour%2Fslogin&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
				header("Location: $url");
			}
			
			//统一下单
			$input = new WxPayUnifiedOrder();
			$input->SetBody($orderInfo['ordname']);
			$input->SetAttach('网站订单');
			$input->SetOut_trade_no(date("YmdHis").$orderInfo['ordsn']);
			if($orderInfo['id'] == 2523){
				$input->SetTotal_fee($orderInfo['ordprice']);  //专供1分钱测试
			}else{
				$input->SetTotal_fee($orderInfo['ordprice'] * 100);
			}		
			//$input->SetTotal_fee(1);
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 6000));
			$input->SetGoods_tag($orderInfo['gid']);
			$input->SetNotify_url("http://m.33ly.com/Tour/wxnotify");
			$input->SetTrade_type("JSAPI");
			$input->SetOpenid($openid);
			//var_dump($input);
			$order = WxPayApi::unifiedOrder($input);
			//$wxPayApi = new WxPayApi();
			//$order = $wxPayApi->unifiedOrder($input);
			//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
			//$this->printf_info($order);
			$jsApiParameters = $tools->GetJsApiParameters($order);
			//echo $jsApiParameters;
			$this->assign('jsApiParameters',$jsApiParameters);
			
			$is_weixin = 1;
		}
		$this->assign('is_weixin',$is_weixin);
		$this->display();
	}
	
	//用户微信取消支付变更状态
	public function wxcancel(){
		$data['order_id'] = $_REQUEST['order_id'];
		$data['admin_id'] = 888;
		$data['reason'] = '用户发起微信支付后取消';
		$data['modify_type'] = '取消支付';
		$data['modify_time'] = time();
		M('order_modify')->add($data);
	}
	
	//出行人信息
	public function card(){
		$oid = $_REQUEST['oid'];
		$this->assign('oid',$oid);
		
		//可能的出行人信息
		$infoList1 = M('user_card')->where('oid='.$oid.' and card_type=1')->order('oid')->select();
		$infoList2 = M('user_card')->where('oid='.$oid.' and card_type=2')->order('oid')->select();
		//var_dump($infoList);
		$this->assign('infoList1',$infoList1);
		$this->assign('infoList2',$infoList2);
		
		$userInfo = M('order')->where('id ='.$oid)->find();
		$is_zyx = M('goods')->where(array('id'=>$userInfo['gid']))->getField('is_zyx');
		if($is_zyx){
			$userInfo['child_num'] = $userInfo['adult_num'] * 1;
			$userInfo['adult_num'] = $userInfo['adult_num'] * 2;
		}
		$this->assign('is_zyx',$is_zyx);
		$this->assign('userInfo',$userInfo);			
		
		$this->display();
	}
	
	public function cardAct(){
		$oid = $_REQUEST['oid'];
		
		//数据
		$count = count($_REQUEST['card_type']);
		if($count){
			for($i=0;$i<$count;$i++){
				$data[$i]['oid'] = $oid;
				$data[$i]['card_type'] = $_REQUEST['card_type'][$i];
				$data[$i]['card_name'] = $_REQUEST['card_name'][$i];
				$data[$i]['card_number'] = $_REQUEST['card_number'][$i];
				$data[$i]['card_phone'] = $_REQUEST['card_phone'][$i];
				$data[$i]['card_ordid'] = $_REQUEST['card_ordid'][$i];
			}
			$mod = M('user_card');
			$mod->where('oid='.$oid)->delete();
			$mod->addAll($data);
		}
		
		$is_direct = M('order as o')->join('33_goods as g on g.id=o.gid')->where('o.id='.$oid)->getfield('g.is_direct');
		if($is_direct){
			$jumpUrl = U('Tour/payment',array('id'=>$oid));
			header("location: ".$jumpUrl);
		}else{
			$this->redirect('Tour/examine');
		}
		
	}
	
	//从PC端移植过来
	public function getDate(){
		$id = $_REQUEST['id'];
		$updays = M('goods')->where('id='.$id)->getfield('sign_up');
		$month = $_REQUEST['month'];
		$mod = $_REQUEST['mod'];
		$year = $_REQUEST['year'];
		$starttime = strtotime($year.'-'.$month);
		if($month == 12){
			$endtime = strtotime(($year+1).'-1');
		}else{
			$endtime = strtotime($year.'-'.($month+1));
		}
		if(time() > $starttime){
			$starttime = time() + ($updays - 1) * 86400;
			//$starttime = time();
		}
		$map['is_del'] = 0;
		$map['pid'] = $id;
		$map['departure_time'] = array('between',array($starttime,$endtime-1));		
		$list = M($mod)->where($map)->order('departure_time')->select();
		//echo M($mod)->getlastsql();
		foreach($list as $key=>$value){
			$list2[date('j',$value['departure_time'])] = $value;
		}
		$json = $list?json_encode($list2):0;
		//echo $json;
		$this->ajaxReturn($json,'ok',1);
	}
	
	public function examine(){
		$this->assign('title','预定成功');
		
		//根据时间来判断提示信息
		//周一-周日 9：00-17：30为工作时间
		$nowHour = date('H',time());
		$nowMinute = date('i',time());
		if(9<=$nowHour and $nowHour<17){
			$this->assign('alertMsg','客服会在15分钟内与您取得联系');
		}elseif($nowHour>=17 and $nowHour<18 and $nowMinute<15){
			$this->assign('alertMsg','客服会在15分钟内与您取得联系');
		}elseif(0<$nowHour and $nowHour<9){
			$this->assign('alertMsg','客服会在9点上班后与您取得联系');
		}else{
			$this->assign('alertMsg','客服会在次日9点以后与您取得联系');
		}
		
		$shareInfo = M('wx_user')->where('wx_openid="'.session('share_user').'"')->find();
		$this->assign('shareInfo',$shareInfo);
		
		$this->display();
	}
	
	//old
	// public function getDeparture($id,$sday){
		// $nowtime = strtotime(date(Ymd));
		// $exptime = $nowtime + 3600*24*$sday;
		// $query ='';
		// $mod = M('departure_time');
		// $list = $mod->where('pid='.$id.' and departure_time>'.$exptime.' and is_del=0')->order('departure_time')->limit(3)->select();
		// foreach($list as $key=>$value){
			// if($key==0) $query .= date('Y-m-d',$value['departure_time']);
			// if($key==1) $query .= '/'.date('Y-m-d',$value['departure_time']);
			// if($key==2) $query .= '等';
		// }
		// return $query;
		// //return date('Ymd',$exptime);
	// }
	
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
	
	public function search(){
		//热门分类
		$hotCate = M('goods_cate')->where('floor=2 and is_del=0 and is_show=1')->order('ordid desc')->limit(8)->select();
		$this->assign('hotCate',$hotCate);
		
		foreach(range('A','Z') as $v){
			$res[$v] = null;
		}
		$map['floor'] = 3;
		$map['is_del'] = 0;
		$res1 = M('goods_cate')->where('pid = 97')->getfield('id',true);
		$tuan_arr = "(";
		foreach($res1 as $key=>$value){
			if($key == 0){
				$tuan_arr .= $value;
			}else{
				$tuan_arr .= ','.$value;
			}
		}
		$tuan_arr .= ")";		
		$map['pid'] = array('not in',$tuan_arr);
		$list = M('goods_cate')->where($map)->order('ordid desc')->select();
		foreach($list as $key=>$value){
			$key = strtoupper(substr($value['pinyin'],0,1));
			$res[$key][] = $value;			
		}
		$this->assign('res',$res);
		$this->display();
	}
	
	//打印输出数组信息
	function printf_info($data)
	{
		foreach($data as $key=>$value){
			echo "<font color='#00ff55;'>$key</font> : $value <br/>";
		}
	}
	
	public function wxtest(){
		ini_set('date.timezone','Asia/Shanghai');
		vendor('Wxpay.Exception');
		vendor('Wxpay.Config');
		vendor('Wxpay.Data');
		vendor('Wxpay.Api');
		vendor('Wxpay.JsApiPay');
		
		//获取用户openid
		$tools = new JsApiPay();
		$openId = $tools->GetOpenid();
		//echo $openId;
		
		//统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody("test");
		$input->SetAttach("test");
		$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
		$input->SetTotal_fee("1");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = WxPayApi::unifiedOrder($input);
		$wxPayApi = new WxPayApi();
		$order = $wxPayApi->unifiedOrder($input);
		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		//$this->printf_info($order);
		$jsApiParameters = $tools->GetJsApiParameters($order);
		$this->assign('jsApiParameters',$jsApiParameters);
		
		$this->display();
	}
	
	//公共函数
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
	
	//简单验证
	public function slogin(){
		$appid = 'wx9f98139e202c7003';
		$appsecret = 'fc569bb8896f29b5269fe49169d0c77a';
		$code = $_REQUEST['code'];
		$state = $_REQUEST['state']?$_REQUEST['state']:0;
		$post_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
		$json=json_decode($this->curlGet($post_url));
		
		$now = time();
		$openid = $json->openid;
		session('wx_openid',$openid);
		$url = "http://m.33ly.com/Tour/payment/id/".$state;
		header("Location: $url");
	}

	//微信回传信息
	public function wxnotify(){
		
	    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	    $this->logger($postStr);
		
		//订单状态修改
		$xmlArray = (array)simplexml_load_string($postStr);
		$ordsn = substr($xmlArray['out_trade_no'],14);
		
		//$this->logger("ordsn:".$ordsn);
		//$this->logger("status:".$xmlArray['result_code']);
		
		if($xmlArray['result_code'] == 'SUCCESS'){
			M('order')->where('ordsn="'.$ordsn.'"')->setField('ordstatus',3);
		}
		
		//操作记录修改
		$modifyData['order_id'] = M('order')->where('ordsn="'.$ordsn.'"')->getField('id');
		$modifyData['admin_id'] = 888;//系统回调操作
		$modifyData['reason'] = '用户通过微信支付';
		$modifyData['modify_type'] = '支付成功';
		$modifyData['modify_time'] = time();
		M('order_modify')->add($modifyData);
		
	 
	    if (isset($_GET)){
		    echo "success";
	    }
		
	}
	
    //日志记录  解析XML
    public function logger($log_content){
        $max_size = 100000;
        $log_filename = "log.xml";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
    }

}