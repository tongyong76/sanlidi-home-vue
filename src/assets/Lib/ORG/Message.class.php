<?php
/**
* 玄武短信接口
* author:steven
**/
class mess
{
	//帐号
	var $account = "ssly@ssly";
	//密码
	var $passwd  = "xw2495";
	//webservice
	var $url     = "http://211.147.239.62/Service/WebService.asmx?wsdl";
	//port
	var $port    = "";
	//type  方式,1为mos 2为200
	var $type    = "1";
	//扩展id
    var $subid   = "";
	
	//MT提交状态码  返回值
	var $response = array(
				"0" =>"成功",
				"-1"=>"账号无效",
				"-2"=>"参数：无效",
				"-3"=>"连接不上服务器",
				"-5"=>"无效的短信数据，号码格式不对",
				"-6"=>"用户名密码错误",
				"-7"=>"旧密码不正确",
				"-9"=>"资金账户不存在",
				"-11"=>"包号码数量超过最大限制",
				"-12"=>"余额不足",
				"-13"=>"账号没有发送权限",
				"-99"=>"系统内部错误",
				"-100"=>"其它错误"
				);
	static $single = null;

	function __construct($type = 1){
		if(!class_exists("SoapClient")){
			die("请开启Soap扩展");
			exit;
		}

		if(self::$single == null)
			self::$single = new SoapClient($this->url);
	} 

	//单一接口发送
	function _postSingle($mobile,$content){
		$params = array(
			'account'    => $this->account,
			'password'	 => $this->passwd,
			'mobile'	 => $mobile,
			'content'	 => $content,
			'subid'		 => '123456'
		);
		self::$single->PostSingle($params); 
		//$result = self::$single->PostSingle($params); 
		//return $result->PostSingleResult;
	}


	//批量发送
	function _post($mobiles,$content){
		//批量发送数组
		 $batchMobile = array();
		 if(is_array($mobiles)){
			foreach($mobiles as $val){
				$messageData = new messageData();
				$messageData->content = $content;
				$messageData->Phone = $val;
				$messageData->vipFlag = true;
				$messageData->customMsgID = "";
				$batchMobile[] = $messageData;
			}
			$mtpack = new mtpack();
			$mtpack->msgs  = $batchMobile;
			$params = array(
				'account'    => $this->account,
				'password'	 => $this->passwd,
				'mtpack'	 => $mtpack
			); 
			print_r(self::$single->Post($params));
		 }else
			$this->_postSingle($mobiles,$content);
	}


	//按组发送
	function _postgroup(){
		
	}

	//群发短信
	function _postmass($mobiles,$content){
		$params = array(
			'account'    => $this->account,
			'password'	 => $this->passwd,
			'mobiles'	 => $mobiles,
			'content'	 => $content,
			'subid'		 => ''
		);
		print_r(self::$single->PostMass($params));
	}
	
	//获取返回结果
	function _getResponse(){
		$params = array(
			'account'    => $this->account,
			'password'	 => $this->passwd,
			'PageSize'	 => 500
		);
		print_r(self::$single->GetResponse($params));
	}

	//获取发送报告
	function _getReport(){
		
	}

	
}


/**
*  批量发送类
**/
class mtpack{
	var $batchID	= "00000000-0000-0000-0000-000000000000";
	//var $batchID    = "";
	var $batchName	= "";
	var $sendType	= "0";
	var $msgType	= "1";
	var $msgs		= "";
	var $bizType	= 0;
	var $distinctFlag = true;	
	var $scheduleTime = "";
	//var $remark		  = "";
	//var $customNum	  = "";
	var $deadline	  = "";
	var $uuid		= "00000000-0000-0000-0000-000000000000";
}

//发送信息类
class messageData{
	var $content;
	var $Phone;
	var $vipFlag;
	var $customMsgID;
}


//$mess = new mess();
//$mess->_post(array("13826174825","18148922017"),"我的测试信息");
//exit;
//print_r($mess->_postSingle(13826174825,"移淘"));
//exit;
//$mess->_postmass(array("13826174825","18148922017"),"我的测试信息");
//$mess->_getResponse();