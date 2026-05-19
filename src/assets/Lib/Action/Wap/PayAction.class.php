<?php
class PayAction extends Action {
	public function _initialize() {
		header('Content-type: text/html; charset=utf-8');
		vendor('Alipay.Corefunction');
		vendor('Alipay.Rsafunction');
		vendor('Alipay.Md5function');
		vendor('Alipay.Mnotify');
		vendor('Alipay.Msubmit');	
	}
	
	public function doalipay(){		

		//传订单参数,并获取订单信息
		$id = $_REQUEST['id'];
		$mod = M('order');
		$orderInfo = $mod->where('id='.$id)->find();
		
		//参数
		$alipay_config = C('malipay_config');
		
		$format = "xml";
		$v = "2.0";
		$req_id = date('Ymdhis');
		
		$notify_url = "http://m.33ly.com/Pay/notifyurl";
		$call_back_url = "http://m.33ly.com/Pay/callbackurl";
		$merchant_url = "http://m.33ly.com/";
		//$notify_url = "http://192.168.30.67/33ly/Pay/notifyurl";
		//$call_back_url = "http://192.168.30.67/33ly/Pay/callbackurl";
		//$merchant_url = "http://192.168.30.67/33ly/";
		
		$seller_email = "fukuan@33ly.com";
		$out_trade_no = $orderInfo['ordsn'];
		$orderInfo['ordname'] = str_replace(" ", "", $orderInfo['ordname']);
		$orderInfo['ordname'] = str_replace("<", "", $orderInfo['ordname']);
		$subject = $orderInfo['ordname'];
		if($orderInfo['id'] == 2523){
			$total_fee = 0.01;
		}else{
			$total_fee = $orderInfo['ordprice'];
		}
		//$total_fee = 0.01;
			//根据上面参数拼接的请求数据
		$req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';
		
		//构造要请求的参数数组，无需改动
		$para_token = array(
			"service" => "alipay.wap.trade.create.direct",
			"format"	=> $format,
			"v"	=> $v,
			"partner" => trim($alipay_config['partner']),
			"req_id"	=> $req_id,
			"sec_id" => trim($alipay_config['sign_type']),			
			"req_data"	=> $req_data,
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($para_token);	

		//URLDECODE返回的信息
		$html_text = urldecode($html_text);

		//解析远程模拟提交后返回的信息
		$para_html_text = $alipaySubmit->parseResponse($html_text);

		//获取request_token
		$request_token = $para_html_text['request_token'];
		//echo 'token='.$request_token;
		
		$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';		
		$parameter = array(
			"service" => "alipay.wap.auth.authAndExecute",
			"partner" => trim($alipay_config['partner']),
			"sec_id" => trim($alipay_config['sign_type']),
			"format"	=> $format,
			"v"	=> $v,
			"req_id"	=> $req_id,
			"req_data"	=> $req_data,
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
		echo $html_text;
			
	}
	
	function notifyurl(){
		//通过C函数来读取配置项，赋值给$alipay_config
        $alipay_config=C('malipay_config');
		
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		//echo "1";

		//if($verify_result) {//验证成功
		if(1){
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//解析notify_data
			//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
			$doc = new DOMDocument();	
			if ($alipay_config['sign_type'] == 'MD5') {
				$doc->loadXML($_POST['notify_data']);
			}
			
			if ($alipay_config['sign_type'] == '0001') {
				$doc->loadXML($alipayNotify->decrypt($_POST['notify_data']));
			}
			
			if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
				//商户订单号
				$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
				//支付宝交易号
				$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
				//交易状态
				$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
				
				if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
					
					
					//判断状态
					$ordstatus=M('order')->where('ordsn="'.$out_trade_no.'"')->getField('ordstatus');
					if($ordstatus==2){
					
					}else{
						$data['payment_trade_no']      =$trade_no;
						$data['payment_trade_status']  =$trade_status."w1";
						$data['ordstatus']             =2;
						$data['is_edit']               =0;
						M('order')->where('ordsn="'.$out_trade_no.'"')->save($data);
						
						$orderId = M('order')->where('ordsn="'.$out_trade_no.'"')->getfield('id');
	
						$mod = M('order_modify');
						$modata['order_id'] = $orderId;
						$modata['admin_id'] = 888;
						$modata['reason'] = '用户通过移动支付宝支付';
						$modata['modify_type'] = '支付成功';
						$modata['modify_time'] = time();
						$mod->add($modata);
					}
					
					echo "success";		//请不要修改或删除
				}
			}

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			echo "fail";

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}
	
	public function callbackurl(){
		//通过C函数来读取配置项，赋值给$alipay_config
        $alipay_config=C('malipay_config');
	
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		
		if(1) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//商户订单号
			$out_trade_no = $_GET['out_trade_no'];
			$trade_no = $_GET['trade_no'];
			$trade_status = $_GET['trade_status'];
			$result = $_GET['result'];

			//判断状态
			$ordstatus=M('order')->where('ordsn="'.$out_trade_no.'"')->getField('ordstatus');
			if($ordstatus==2){
				$jumpUrl = U('User/profile');
				header("Location:".$jumpUrl);
			}else{
				$data['payment_trade_no']      =$trade_no;
				$data['payment_trade_status']  =$trade_status."w2";
				$data['ordstatus']             =2;
				$data['is_edit']               =0;
				M('order')->where('ordsn="'.$out_trade_no.'"')->save($data);
				
				$orderId = M('order')->where('ordsn="'.$out_trade_no.'"')->getfield('id');
	
				$mod = M('order_modify');
				$modata['order_id'] = $orderId;
				$modata['admin_id'] = 888;
				$modata['reason'] = '用户通过移动支付宝支付';
				$modata['modify_type'] = '支付成功';
				$modata['modify_time'] = time();
				$mod->add($modata);
				
				$jumpUrl = U('User/profile');
				header("Location:".$jumpUrl);
			}
			
			//M('order')->where('ordsn="'.$out_trade_no.'"')->setfield('ordstatus',3);

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			//echo "<div class='font-size:16px;'>支付成功&nbsp;&nbsp;<a href='http://m.33ly.com/'>返回首页</a></div>";
			//修改跳转到用户中心
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数
			echo "验证失败";
		}		
	}

	//打印输出数组信息
	function printf_info($data)
	{
		foreach($data as $key=>$value){
			echo "<font color='#00ff55;'>$key</font> : $value <br/>";
		}
	}
	
	//微信支付
	public function dowxpay(){
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
		
		$id = $_REQUEST['id'];
		//echo 'id='.$id.'<br>';
		//if(!id) $this->error('非法操作！');
		$orderInfo = M('order')->where('id='.$id)->find();
		
		//统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody($orderInfo['ordname']);
		$input->SetAttach('网站订单');
		$input->SetOut_trade_no(date("YmdHis").$orderInfo['ordsn']);
		$input->SetTotal_fee($orderInfo['ordprice'] * 100);
		//$input->SetTotal_fee(1);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 6000));
		$input->SetGoods_tag($orderInfo['gid']);
		$input->SetNotify_url("http://m.33ly.com/Pay/wxnotify");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		//var_dump($input);
		$order = WxPayApi::unifiedOrder($input);
		//$wxPayApi = new WxPayApi();
		//$order = $wxPayApi->unifiedOrder($input);
		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		//$this->printf_info($order);
		$jsApiParameters = $tools->GetJsApiParameters($order);
		//echo $jsApiParameters;
		$this->assign('jsApiParameters',$jsApiParameters);
		
		$this->display();	
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