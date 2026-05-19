<?php
class OrderAction extends BaseAction {
    public function confirm(){
		$type= $_REQUEST['type']?$_REQUEST['type']:0;
		$this->assign('type',$type);
		if(!$type){			//普通线路
			$tid = $_REQUEST['tid'];
			$an = $_REQUEST['an']?$_REQUEST['an']:1;
			$cn = $_REQUEST['cn']?$_REQUEST['cn']:0;
			//tid不存在或者未登录=>非法操作
			$exist = M('departure_time')->where('id='.$tid)->find();
			if($exist and 1){
				$goodsInfo = M('goods')->field('id,days,name,type_id,subname,type_id,is_zyx')->where('id='.$exist['pid'])->find();
				$exist['type_id'] = $goodsInfo['type_id'];
				$exist['gid'] = $goodsInfo['id'];
				$exist['name'] = $goodsInfo['name'];
				$exist['subname'] = $goodsInfo['subname'];
				$exist['days'] = $goodsInfo['days'];
				$exist['ywx'] = $goodsInfo['days'] * 5;  //意外险
				$exist['an'] = $an;
				$exist['cn'] = $cn;
				$this->assign('goodsInfo',$goodsInfo);
				$this->assign('tourInfo',$exist);
			}else{
				$this->error('非法操作','__ROOT__');
			}
		}
		
		if($type==1){		//邮轮		
			$gid = $_REQUEST['gid'];
			$shipInfo = M('ship')->where('id='.$gid)->find();
			if($shipInfo and 1){
				$exist['departure_time'] = $shipInfo['start_time'];
				$exist['gid'] = $shipInfo['id'];
				$exist['name'] = $shipInfo['name'];
				$exist['days'] = $shipInfo['days'];
				$exist['ywx'] = $shipInfo['days'] * 5;  //意外险
				
				//query传递信息
				$query = str_replace('*','/',$_REQUEST['query']);
				$query = str_replace('_','+',$query);
				$query = base64_decode($query);
				$query = explode('|',$query);
				$exist['subname'] = $query[0];
				$exist['an'] = $query[1];
				$exist['cn'] = $query[2];
				$exist['rn'] = $query[3];
				$exist['price'] = 0;
				$exist['child_price'] = 0;
				$exist['totalMoneyAll'] = $query[4];
				$this->assign('tourInfo',$exist);
				//var_dump($exist);
			}else{
				$this->error('非法操作','__ROOT__');
			}
		}
		$this->display();
    }
	
	public function confirmAct(){
		
		//获取订单信息
		$orderMod = M('order');
		$data = $orderMod->create();
		if($data['gid']){
			$gid = $_REQUEST['gid'];		
				
			$is_direct = M('goods')->where('id='.$gid)->getfield('is_direct'); //是否二次审核
			//$is_direct=0; //默认都需要审核
			
			$data['ordsn'] = 'SN'.date('YmdHis');
			$data['uid'] = $this->uid;
			$data['add_time'] = time();	
			$data['ordacc'] = session('ordacc');				
			
			//判断联系人信息
			$who = $_REQUEST['who'];  //0未登录 1本人  2其他人
			
			if(!$who){	//未登录的情况			
				$exist2 = M('user')->where('phone='.$data['cphone'].' and (status=0 or status=1 or status=2) and is_del=0')->getfield('id'); //查找是否存在
				if(!$exist2){
					$addData['phone'] = $data['cphone'];
					$addData['realname'] = $data['cname'];		
					$addData['email'] = $data['cmail'];		
					$randStr = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
					$rand = substr($randStr,0,6);
					$addData['password'] = md5($rand);
					$addData['status'] = 2;
					$addData ['reg_ip'] = getClientIp();
					$addData ['reg_time'] = time();
					$newId2 = M('user')->add($addData);	
					//发送短信提醒
					//echo "短信已发送,密码为".$rand;
					$msgdata = "您已成为三三旅游的注册会员，帐号为您的手机号码，密码为".$rand."，感谢您支持三三旅游！";
					$msgphone= $data['cphone'];
					$reslut = $this->smsxwkj($msgphone,$msgdata);
					//。。。。。
					
					//自动登录
					//更新用户最后登录的IP和时间
					$user ['last_ip'] = getClientIp ();
					$user ['last_time'] = time ();
					M('user')->where ( "id=$newId2 and is_del=0" )->save ( $user );
					//更新cookies
					$cookie = 1;
					if ($cookie == true) {
						setcookie ( 'id', $newId2, time () + 3600 * 24 * 7, '/' );
						setcookie ( 'username', $addData['realname'], time () + 3600 * 24 * 7, '/' );
					} else {
						setcookie ( 'id', $newId2, '/' );
						setcookie ( 'username', $addData['realname'], '/' );
					}
				}else{
					$this->error('非法操作');
				}
			}elseif($who == 1){ //本人则更新信息
				if($data['cname']) $newData['realname'] = $data['cname'];
				if($data['cmail']) $newData['email'] = $data['cmail'];
				M('user')->where('id='.$data['uid'])->save($newData);
			}
			if($newId2) $data['uid'] = $newId2;
			$newId = $orderMod->add($data);
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
			$mailto1 = '2355677401@qq.com';		//汤
			//$mailto2 = 'huangmengjie@33ly.com';		//黄
			//$mailto3 = '985748968@qq.com';   //宗
			$mailto3 = '475810549@qq.com';   //沈
			
			$mailtitle = '新订单！'.$userInfo['realname'].'|'.$userInfo['phone'];//标题
			$mailmessage = $this->fetch('Public:orderMailForm');//邮件正文
			$mailfrom = '三三旅游后台订单';//发件方名称
			SendMail($mailto,$mailtitle,$mailmessage,$mailfrom);
			SendMail($mailto1,$mailtitle,$mailmessage,$mailfrom);
			//SendMail($mailto2,$mailtitle,$mailmessage,$mailfrom);
			SendMail($mailto3,$mailtitle,$mailmessage,$mailfrom);
			
			//session结算信息
			
			$info['type'] = $data['g_type']?$data['g_type']:0;
			$info['ordname'] = $data['ordname'];
			$info['adult_num'] = $data['adult_num'];
			$info['adult_price'] = $_REQUEST['adult_price'];
			$info['child_num'] = $data['child_num'];
			$info['child_price'] = $_REQUEST['child_price'];
			$info['totel_price'] = $data['ordprice'];
			$info['safe'] = $_REQUEST['safe'];
			session('account',$info);
			
			//echo $is_direct.'2';
			if($is_direct){
				//跳转支付宝页面
				M('order')->where('id='.$newId)->setField('ordstatus',1);
				M('order')->where('id='.$newId)->setField('ordpay',1);
				//echo $data["ordpay"];
				//switch($data["ordpay"]){
				switch(1){
					case 1:
						//跳转支付宝
						$this->redirect('Order/pay',array('payType'=>'zfb','orderid'=>$newId));
						break;
					case 2:
						//门店支付
						$this->redirect('Order/pay',array('payType'=>'mdzf'));
						break;
					case 3:
						//银行转账
						$this->redirect('Order/pay',array('payType'=>'yhzz'));
						break;
					default:
						break;
				};
			}else{
				//等待确认页面
	//			$this->redirect('Order/examine',array('oid'=>$newId));
				$this->redirect('Order/card',array('oid'=>$newId));
			}
		}
		//echo $gid;		
	}
	
	public function card(){
		//订单信息
		$res = session('account');
		$this->assign('account',$res);
		
		$oid = $_REQUEST['oid'];
		
		//可能的出行人信息
		$infoList1 = M('user_card')->where('oid='.$oid.' and card_type=1')->order('card_ordid')->select();
		$infoList2 = M('user_card')->where('oid='.$oid.' and card_type=2')->order('card_ordid')->select();
		//var_dump($infoList);
		$this->assign('infoList1',$infoList1);
		$this->assign('infoList2',$infoList2);
		
		$mod = M('order as o');
		$userInfo = $mod->join('33_user as u on u.id=o.uid')->where('o.id ='.$oid)->find();
		$this->assign('userInfo',$userInfo);
		//var_dump($userInfo);
		$this->assign('oid',$oid);
		
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
		$this->redirect('Order/examine',array('oid'=>$newId));
	}
	
	public function detail(){
		$id = $_REQUEST['id'];
		$visaInfo = M('visa')->where('id='.$id)->find();
		$this->assign('visaInfo',$visaInfo);
		$cateInfo = M('visa_cate')->where('id='.$visaInfo['cate_id'])->find();
		$this->assign('cateInfo',$cateInfo);
		$this->display();
	}
	
	public function pay(){
		$payType = $_REQUEST['payType'];
		if($payType == 'zfb'){
			$this->assign('orderid',$_REQUEST['orderid']);
		}
		$this->assign('payType',$payType);
		$res = session('account');
		$this->assign('account',$res);
		$this->display();
	}
	
	public function examine(){
		//通过SESSION获取订单信息
		$res = session('account');
		$this->assign('account',$res);
		
		//根据时间来判断提示信息
		//周一-周日 9：00-17：00为工作时间
		$nowHour = date('H',time());
		$nowMinute = date('i',time());
		if(9<=$nowHour and $nowHour<=16){
			$this->assign('alertMsg','客服会在15分钟内与您取得联系');
		}elseif($nowHour<18 and $nowMinute<15){
			$this->assign('alertMsg','客服会在15分钟内与您取得联系');
		}elseif(0<$nowHour and $nowHour<9){
			$this->assign('alertMsg','客服会在9点上班后与您取得联系');
		}else{
			$this->assign('alertMsg','客服会在次日9点以后与您取得联系');
		}
		
		$this->display();
	}
	
	public function success(){
		$orderId = session('newOrderId');
		$orderInfo = M('order')->field('nickname,realname,cname,phone,email,ordname,ordstart')->join('33_user as u on u.id=33_order.uid')->where('33_order.id='.$orderId)->find();
		$today = $dtime = strtotime(date('Y-m-d',time()));
		$expTime = strtotime(date('Y-m-d',$orderInfo['ordstart']));
		if(($expTime - 3600*24*2)>$today){
			$orderInfo['exptime'] = ($expTime - 3600*24*2);
		}elseif(($expTime - 3600*24*1)>$today){
			$orderInfo['exptime'] = ($expTime - 3600*24*1);
		}else{
			$orderInfo['exptime'] = '今晚';
		}
		$this->assign('orderInfo',$orderInfo);
		//$res = session('account');
		//$this->assign('account',$res);
		$this->display();
	}
	
	public function cancel(){
		$mod = M('order_modify');
		$data['order_id'] = $_REQUEST['order_id'];
		$data['reason'] = $_REQUEST['reason'];
		$data['admin_id'] = 888;
		$data['modify_type'] = '用户取消';
		$data['modify_time'] = time();
		M('order')->where('id='.$data['order_id'])->setfield('ordstatus','-1');
		$mod->add($data);
		//$this->success('取消订单成功',U('User/myorder'));
		//封装数据
		$ordList = M('order')->where('(ordstatus<3 and ordstatus>=0) and uid='.$this->uid)->order('add_time desc')->select();
		$res = '';
		foreach($ordList as $key=>$value){
			if($key){
				$res .= '<tr>';
			}else{
				$res .= '<tr class="nobar">';
			}
			$res .= '<td>'.$value['ordsn'].'</td>';
			$res .= '<td>'.date("Y-m-d",$value['add_time']).'</td>';
			$res .= '<td>'.$value['ordname'].'</td>';
			$res .= '<td>'.date("Y-m-d",$value['ordstart']).'</td>';
			$res .= '<td>￥'.$value['ordprice'].'</td>';
			$res .= '<td>'.getNameById($value['ordstatus'],order_status).'</td>';
			$res .= '<td>';
			if($value['ordstatus'] == 1){
				$res .= '<a href="javascript:void(0);" onclick="payByAlipay('.$value['id'].');" class="oBtn pay">付款</a>';
				$res .= '<a href="javascript:void(0);" onclick="cancelOrder('.$value['id'].');" class="oBtn cancel">取消</a>';
			}else{
				$res .= '<a href="javascript:void(0);" onclick="cancelOrder('.$value['id'].');" class="oBtn cancel middle">取消</a>';
			}
			$res .= '</td>';
		}
		$this->ajaxReturn($res,'取消订单成功',1);
	}
	
	public function hetong(){
		$ordid = $_REQUEST['ordid'];
		$typeid = M('order')->join('33_goods as g on g.id=33_order.gid')->getfield('type_id');
		$res = M('order')->getlastsql();
		switch($typeid){
			case 1:
				$data = $this->fetch('Public:hetong1');
				break;
			case 2:
				$data = $this->fetch('Public:hetong1');
				break;
			case 3:
				$data = $this->fetch('Public:hetong1');
				break;
		}
		$this->ajaxReturn($data,0,1);
	}
}