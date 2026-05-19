<?php
//微信支付测试
class TestAction extends BaseAction {
    
	public function index(){
		//echo $this->isWeixin();
		if($this->isWeixin()){
			//微信浏览器
			vendor('Weixin.WxPay.pub.config');
			vendor('Weixin.WxPayPubHelper');
			
			$jsApi = new JsApi_pub();			
			$openid = 'oOrekjmmExAQ40gRUAzM8I_LrnO8';
			
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
			
			//var_dump($jsApiParameters);
			$this->display();
		}else{
			//非微信浏览器
		}
    }
	
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