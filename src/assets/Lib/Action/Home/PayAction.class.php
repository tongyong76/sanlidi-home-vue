<?php
class PayAction extends Action {
	public function _initialize(){
		vendor('Alipay.Corefunction');
		vendor('Alipay.Md5function');
		vendor('Alipay.Notify');
		vendor('Alipay.Submit');
	}
	
	public function index(){
		$this->display();
	}
	
    public function doalipay(){
		header('Content-type: text/html; charset=utf-8');
		//传订单参数,并获取订单信息
		$id = $_REQUEST['id'];
		session('newOrderId',$id);
		$mod = M('order');
		$orderInfo = $mod->where('id='.$id)->find();
	
		$alipay_config = C('alipay_config');
		$payment_type = "1";
		$notify_url = C('alipay.notify_url');
		$return_url = C('alipay.return_url');
		$seller_email = C('alipay.seller_email');
		$out_trade_no = $orderInfo['ordsn'];
		$subject = $orderInfo['ordname'];
		$total_fee = $orderInfo['ordprice'];
		//$total_fee = 0.01;
		$body = $orderInfo['ordname'];
		$show_url = 'http://www.33ly.com/member/';
		$anti_phishing_key = "";
		$exter_invoke_ip = get_client_ip();
		
		$parameter = array(
			"service" => "create_direct_pay_by_user",
			"partner" => trim($alipay_config['partner']),
			"payment_type"    => $payment_type,
			"notify_url"    => $notify_url,
			"return_url"    => $return_url,
			"seller_email"    => $seller_email,
			"out_trade_no"    => $out_trade_no,
			"subject"    => $subject,
			"total_fee"    => $total_fee,
			"body"            => $body,
			"show_url"    => $show_url,
			"anti_phishing_key"    => $anti_phishing_key,
			"exter_invoke_ip"    => $exter_invoke_ip,
			"_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );
		
		//建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
		//$alipaySubmit->buildRequestForm($parameter,"post", "确认");
        echo $html_text;	
	}
	
	//支付测试用例
    public function doalipayTest(){
		header('Content-type: text/html; charset=utf-8');
		//传订单参数,并获取订单信息
		$id = $_REQUEST['id'];
		$mod = M('order');
		$orderInfo = $mod->where('id='.$id)->find();
	
		$alipay_config = C('alipay_config');
		$payment_type = "1";
		$notify_url = C('alipay.notify_url');
		$return_url = C('alipay.return_url');
		$seller_email = C('alipay.seller_email');
		$out_trade_no = $orderInfo['ordsn'];
		$subject = $orderInfo['ordname'];
		//$total_fee = $orderInfo['ordprice'];
		$total_fee = 0.01;
		$body = $orderInfo['ordname'];
		$show_url = 'http://www.33ly.com/member/';
		$anti_phishing_key = "";
		$exter_invoke_ip = get_client_ip();
		
		$parameter = array(
			"service" => "create_direct_pay_by_user",
			"partner" => trim($alipay_config['partner']),
			"payment_type"    => $payment_type,
			"notify_url"    => $notify_url,
			"return_url"    => $return_url,
			"seller_email"    => $seller_email,
			"out_trade_no"    => $out_trade_no,
			"subject"    => $subject,
			"total_fee"    => $total_fee,
			"body"            => $body,
			"show_url"    => $show_url,
			"anti_phishing_key"    => $anti_phishing_key,
			"exter_invoke_ip"    => $exter_invoke_ip,
			"_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );
		
		//建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
		//$alipaySubmit->buildRequestForm($parameter,"post", "确认");
        echo $html_text;	
	}
	
    /******************************
    服务器异步通知页面方法
    其实这里就是将notify_url.php文件中的代码复制过来进行处理
    *******************************/
	function notifyurl(){
		header('Content-type: text/html; charset=utf-8');
		//通过C函数来读取配置项，赋值给$alipay_config
        $alipay_config=C('alipay_config');
		
		//计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
		
		if($verify_result) {
			//验证成功
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			$out_trade_no   = $_POST['out_trade_no'];      //商户订单号
			$trade_no       = $_POST['trade_no'];          //支付宝交易号
			$trade_status   = $_POST['trade_status'];      //交易状态
			$total_fee      = $_POST['total_fee'];         //交易金额
			$notify_id      = $_POST['notify_id'];         //通知校验ID。
			$notify_time    = $_POST['notify_time'];       //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
			$buyer_email    = $_POST['buyer_email'];       //买家支付宝帐号；
			$parameter = array(
				"out_trade_no"     => $out_trade_no, //商户订单编号；
				"trade_no"     => $trade_no,     //支付宝交易号；
				"total_fee"     => $total_fee,    //交易金额；
				"trade_status"     => $trade_status, //交易状态
				"notify_id"     => $notify_id,    //通知校验ID。
				"notify_time"   => $notify_time,  //通知的发送时间。
				"buyer_email"   => $buyer_email,  //买家支付宝帐号；
			);
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
                       //
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				if(!checkorderstatus($out_trade_no)){
					orderhandle($parameter);
							   //进行订单处理，并传送从支付宝返回的参数；
				}
            }
            echo "success";        //请不要修改或删除
        }else{
            //验证失败
            echo "fail";
		}   
	}

	/*
    页面跳转处理方法；
    这里其实就是将return_url.php这个文件中的代码复制过来，进行处理； 
    */
	function returnurl(){
		header('Content-type: text/html; charset=utf-8');
		$alipay_config=C('alipay_config');
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if(1) {
			$out_trade_no   = $_GET['out_trade_no'];      //商户订单号
			$trade_no       = $_GET['trade_no'];          //支付宝交易号
			$trade_status   = $_GET['trade_status'];      //交易状态
			$total_fee      = $_GET['total_fee'];         //交易金额
			$notify_id      = $_GET['notify_id'];         //通知校验ID。
			$notify_time    = $_GET['notify_time'];       //通知的发送时间。
			$buyer_email    = $_GET['buyer_email'];       //买家支付宝帐号；
			$parameter = array(
				"out_trade_no"     => $out_trade_no,      //商户订单编号；
				"trade_no"     => $trade_no,          //支付宝交易号；
				"total_fee"      => $total_fee,         //交易金额；
				"trade_status"     => $trade_status,      //交易状态
				"notify_id"      => $notify_id,         //通知校验ID。
				"notify_time"    => $notify_time,       //通知的发送时间。
				"buyer_email"    => $buyer_email,       //买家支付宝帐号
			);
			if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				if(!checkorderstatus($out_trade_no)){
					orderhandle($parameter);  //进行订单处理，并传送从支付宝返回的参数；
				}
				//logResult('url:'.C('alipay.successpage'));
				//$this->redirect(C('alipay.successpage'));//跳转到配置项中配置的支付成功页
				header("Location: ".C('alipay.successpage')); 
			}else{
				echo "trade_status=".$_GET['trade_status'];
				//$this->redirect(C('alipay.errorpage'));//跳转到配置项中配置的支付失败页面；
				header("Location: ".C('alipay.errorpage')); 
			}
		}else{
			echo "支付失败！";
		}	
	}
	
}