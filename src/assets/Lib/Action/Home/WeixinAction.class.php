<?php 
class WeixinAction extends BaseAction 
{	
	public $user;
	public $funList;
	public $menuList;
	public function _initialize() {
		header("Content-Type: text/html;charset=utf-8");
		$this->user['appid'] = 'wx9f98139e202c7003';
		$this->user['appsecret'] = 'fc569bb8896f29b5269fe49169d0c77a';
		//$this->user['appid'] = 'wx30eb7ad9b96dd34a';
		//$this->user['appsecret'] = 'a453e45b87ddef8d098b0d681413c9e8';
		//$this->user['appid'] = 'wxe963da6c394aff5a';
		//$this->user['appsecret'] = '94df5165d4450e90a747db8d3795ba95';
		
		$fun = M('wx_auto')->where('is_del=0 and status=1')->order('ordid desc')->select();
		foreach($fun as $key=>$value){
			$funList[$key][0] = $value['keyword'];
			$funList[$key][1] = $value['cname'];
			$funList[$key][2] = 'http://www.33ly.com'.$value['cimg'];
			$funList[$key][3] = $value['curl'];
			$funList[$key][4] = $value['cdes'];
		}
		$this->funList = $funList;

		$menu = M('wx_menu')->where('is_del=0 and status=1')->order('ordid desc')->select();
		foreach($menu as $key=>$value){
			$menu[$key]['son'] = M('wx_content')->where('mid='.$value['id'].' and is_del=0 and status=1')->order('ordid desc,id desc')->limit(5)->select();
		}
		//var_dump($menu[4]);
		$this->menuList = $menu;
		//var_dump($this->menuList);
	}
	
	//获取ACCESS_TOKEN
	public function getAccessToken(){
		$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->user['appid'].'&secret='.$this->user['appsecret'];
		$json=json_decode($this->curlGet($url_get));
		if (!$json->errmsg){
			//return array('rt'=>true,'errorno'=>0);
		}else {
			$this->error('获取access_token发生错误：错误代码'.$json->errcode.',微信返回错误信息：'.$json->errmsg);
		}
		return $json->access_token;
	}
	
	public function autoGroup(){
		$access_token = $this->getAccessToken();		
		$url_get = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=".$access_token;
		$res = json_decode($this->curlGet($url_get));
		var_dump($res);
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
		
		//获取菜单拼接
		// $menu = M('wx_menu')->field('id,type,name')->where('floor=1 and is_del=0 and status=1')->select();
		// foreach($menu as $key=>$value){
			// $button[$key]['name'] = $value['name'];
			// $list = M('wx_menu')->field('id,type,name')->where('pid='.$value['id'].' and is_del=0 and status=1')->select();
			// foreach($list as $skey=>$svalue){
				// $button[$key]['sub_button'][$skey]['type'] = $svalue['type'];
				// $button[$key]['sub_button'][$skey]['name'] = $svalue['name'];
				// $button[$key]['sub_button'][$skey]['key'] = $key."_".$svalue['id'];
			// }
		// }
		// $menudata = '{ "button":'.json_encode($button).'}';

		
		$data = '{"button":[';
		$class=M('wx_menu')->where(array('pid'=>0,'is_del'=>0,'status'=>1))->limit(3)->order('ordid desc')->select();
		$kcount=M('wx_menu')->where(array('pid'=>0,'is_del'=>0,'status'=>1))->limit(3)->order('ordid desc')->count();
		$k=1;
		foreach($class as $key=>$vo){
			//主菜单
			$data.='{"name":"'.$vo['name'].'",';
			$c=M('wx_menu')->where(array('pid'=>$vo['id'],'is_del'=>0,'status'=>1))->limit(5)->order('ordid desc')->select();
			$count=M('wx_menu')->where(array('pid'=>$vo['id'],'is_del'=>0,'status'=>1))->limit(5)->order('ordid desc')->count();
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
			$this->success('操作成功','http://mm.33ly.com/Wxmenu/index');
			//echo U('Weixin/index');
		}
		exit;
	}
	
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

	function curlGet($url){
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
	
    public function index(){
		//验证
		//import("@.ORG.WeChat");
		
		//define("TOKEN", "33ly");
		//$wechat = new wechatCallbackapiTest();
		//$wechat->valid();
			  
		header("Content-Type: text/html;charset=utf-8");
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
					 
		//获取微信发送数据
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
			
		//获取菜单拼接
		// $menu = M('menu')->field('id,type,name')->where('floor=1 and is_del=0')->select();
		// foreach($menu as $key=>$value){
			// $button[$key]['name'] = $value['name'];
			// $list = M('menu')->field('id,type,name')->where('pid='.$value['id'].' and is_del=0')->select();
			// foreach($list as $skey=>$svalue){
				// $button[$key]['sub_button'][$skey]['type'] = $svalue['type'];
				// $button[$key]['sub_button'][$skey]['name'] = $svalue['name'];
				// $button[$key]['sub_button'][$skey]['key'] = $key."_".$svalue['id'];
			// }
		// }
		// $menudata = '{ "button":'.json_encode($button).'}';
			
		//返回回复数据
		if (!empty($postStr)){
				  
			//解析数据
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			//发送消息方ID
			$fromUsername = $postObj->FromUserName;
			//接收消息方ID
			$toUsername = $postObj->ToUserName;
			//消息类型
			$form_MsgType = $postObj->MsgType;
			
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->user['appid'].'&secret='.$this->user['appsecret'];
			$json=json_decode($this->curlGet($url_get));
			$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$json->access_token;
						  
			//文字消息
			if($form_MsgType=="text")
			{       
				$form_Content = trim($postObj->Content);
				//如果发送内容不是空白回复用户内容
				if(!empty($form_Content) or $form_Content == 0)
				{   
					// //大转盘
					$prefix='http://www.33ly.com/pcms/index.php?g=Wap&m=Lottery&a=index&type=1&token=trawwr1395885281&id=4&wecha_id=';
					$suffix='&id=35';
					//回复图文
					if($form_Content=="33"){
						// $resultStr="<xml>\n
						// <ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
						// <FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
						// <CreateTime>".time()."</CreateTime>\n
						// <MsgType><![CDATA[news]]></MsgType>\n
						// <ArticleCount>1</ArticleCount>\n
						// <Articles>\n";  
						// //foreach($autoInfo as $key=>$value){
							// $resultStr.="<item>\n";
							// //if(!$value['name']){
								// $resultStr.="<Title><![CDATA[都半价啦！三三最大活动，赶紧约起来]]></Title> \n";
							// //}elseif(!$value['subname']){
							// //	$resultStr.="<Title><![CDATA[今年春游去哪儿？当然是跟着《爸爸去哪儿》的脚步去建德新叶古村咯！]]></Title> \n";
							// //}else{
							// //	$resultStr.="<Title><![CDATA[<".$value['name'].">".$value['subname']."]]></Title> \n";
							// //}
							// $resultStr.="<Description><![CDATA[今年春游去哪儿？当然是跟着《爸爸去哪儿》的脚步去建德新叶古村咯！]]></Description>\n
							// <PicUrl><![CDATA[http://www.33ly.com/Uploads/goods/s_55015a9802dfb.jpg]]></PicUrl>\n
							// <Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MjM5MzE1OTQ4Mg==&mid=203912576&idx=1&sn=14a31692a697a963b3b10c3352c34097#rd]]></Url>\n
							// </item>\n";
						// //}
						// $resultStr.="</Articles>\n
						// <FuncFlag>0</FuncFlag>\n
						// </xml>";                
						// echo $resultStr;
						// exit;
						
						// //大转盘游戏
						$resultStr="<xml>\n
							<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
							<FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
							<CreateTime>".time()."</CreateTime>\n
							<MsgType><![CDATA[news]]></MsgType>\n
							<ArticleCount>1</ArticleCount>\n
							<Articles>\n";                  
						//大转盘详情数组  
						$return_arr=array(
							array(
								"幸运大转盘活动开始啦",
								"http://www.33ly.com/Uploads/pigcms/activity-lottery-start.jpg",
								$prefix.$fromUsername.$suffix
							),						  
						);
						  
						//数组循环转化
						foreach($return_arr as $value)
						{
							$resultStr.="<item>\n
							<Title><![CDATA[".$value[0]."]]></Title> \n
							<Description><![CDATA[]]></Description>\n
							<PicUrl><![CDATA[".$value[1]."]]></PicUrl>\n
							<Url><![CDATA[".$value[2]."]]></Url>\n
							</item>\n";
						}
						$resultStr.="</Articles>\n
							<FuncFlag>0</FuncFlag>\n
							</xml>";                
						echo $resultStr;
						exit;
					}
					
					//旅游节20160831
					if($form_Content == '旅游节'){
						$msgType = "text";
						//$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "亲来晚了哦，活动已结束");
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "<a href='https://mp.weixin.qq.com/s?__biz=MjM5MzE1OTQ4Mg==&mid=2650311310&idx=1&sn=0836b9f40504ce71b498168826402445&scene=1&srcid=0831sHhMXPtETcp6ZLLsPky4&pass_ticket=VOipT0NwylxIKRqHbzaN4KkaQTCoEdZPRo77ysaInOOBqOv7gNer7IM%2Fckn%2BiJ2M#rd'>点击这里</a>");
						echo $resultStr;
						exit;
					}
					// if($form_Content == '我要抢楼'){
						// $msgType = "text";
						// //$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "亲来晚了哦，活动已结束");
						// $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "<a href='http://m.33ly.com/Game/index'>点我开抢</a>");
						// echo $resultStr;
						// exit;
					// }
					// if($form_Content == '我要查询'){
						// $msgType = "text";
						// //$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "亲来晚了哦，活动已结束");
						// $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "<a href='http://m.33ly.com/Game/result'>点击这里</a>");
						// echo $resultStr;
						// exit;
					// }
					//销券
					// if(strstr($form_Content,"优惠券**")){
						// $res = (int)str_replace('优惠券**','',strstr($form_Content,"优惠券**"));
						// $res_res = M('wx_game_info')->where(array('game_id'=>$res))->find();
						// if(empty($res_res['wx_openid'])){
							// $res_str = "优惠券无效，优惠券不存在！";
						// }else{
							// if($res_res['status']){
								// $res_str = "优惠券无效，已被使用！";
							// }else{
								// $res_str = "优惠券有效，销券成功！";
								// M('wx_game_info')->where(array('game_id'=>$res))->setfield('status',1);
							// }
							
						// }
						
						// $msgType = "text";
						// $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $res_str);
						// echo $resultStr;
						// exit;
					// }

					if(is_numeric($form_Content)){ //纯数字
						if($form_Content == 0){
							$resultStr="<xml>\n
							<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
							<FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
							<CreateTime>".time()."</CreateTime>\n
							<MsgType><![CDATA[news]]></MsgType>\n
							<ArticleCount>1</ArticleCount>\n
							<Articles>\n";                  

								$resultStr.="<item>\n
								<Title><![CDATA[旅游反馈表]]></Title> \n
								<Description><![CDATA[]]></Description>\n
								<PicUrl><![CDATA[http://www.33ly.com/Public/images/yjd3.jpg]]></PicUrl>\n
								<Url><![CDATA[http://www.formtalk.net/pub.do?f=13CDB2B6211E6A87F7FA4B38F316C6FCE569A44124A8FFBA5697058C98477B32]]></Url>\n
								</item>\n";

							$resultStr.="</Articles>\n
							<FuncFlag>0</FuncFlag>\n
							</xml>";   
							echo $resultStr;
							exit;
						}else{
							//门店归类
							$dd = preg_replace('/^0+/','',$form_Content);
							switch($dd){
								case 1:
									$to_groupid = 129;
									$to_name = '市区形象店';
									$to_tel = '0512-65163333';
									break;
								case 2:
									$to_groupid = 130;
									$to_name = '昆山世茂店';
									$to_tel = '0512-55115533';
									break;
								case 3:
									$to_groupid = 131;
									$to_name = '新区泉屋店';
									$to_tel = '0512-68666633';
									break;
								case 4:
									$to_groupid = 132;
									$to_name = '太仓华旭店';
									$to_tel = '0512-53535333';
									break;
								case 5:
									$to_groupid = 133;
									$to_name = '常熟旗舰店';
									$to_tel = '0512-52225533';
									break;
								case 6:
									$to_groupid = 134;
									$to_name = '港城购物店';
									$to_tel = '0512-55395533';
									break;
								case 7:
									$to_groupid = 135;
									$to_name = '园区圆融店';
									$to_tel = '0512-69155333';
									break;
								case 8:
									$to_groupid = 136;
									$to_name = '吴江正翔店';
									$to_tel = '0512-63431333';
									break;
								case 9:
									$to_groupid = 137;
									$to_name = '吴中丽丰店';
									$to_tel = '0512-65165533';
									break;
								case 10:
									$to_groupid = 138;
									$to_name = '昆山形象店';
									$to_tel = '0512-55151333';
									break;
								case 11:
									$to_groupid = 139;
									$to_name = '常熟欧尚店';
									$to_tel = '0512-52935333';
									break;
								case 12:
									$to_groupid = 140;
									$to_name = '吴中永旺店';
									$to_tel = '0512-65199333';
									break;
								case 13:
									$to_groupid = 141;
									$to_name = '相城繁花店';
									$to_tel = '0512-65232333';
									break;
								case 14:
									$to_groupid = 142;
									$to_name = '园区永旺店';
									$to_tel = '0512-65731333';
									break;
								case 15:
									$to_groupid = 143;
									$to_name = '园区万科店';
									$to_tel = '0512-62961333';
									break;
								case 16:
									$to_groupid = 144;
									$to_name = '桐泾商务店';
									$to_tel = '0512-65311333';
									break;
								case 17:
									$to_groupid = 145;
									$to_name = '港城吾悦店';
									$to_tel = '0512-56885933';
									break;
								case 18:
									$to_groupid = 146;
									$to_name = '吴中万达店';
									$to_tel = '0512-69131333';
									break;
								default:
									exit;
							}
							$post_group_url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=".$json->access_token;
							$groupData = '{"openid":"'.$fromUsername.'","to_groupid":'.$to_groupid.'}';
							
							$groupRt = $this->api_notice_increment($post_group_url,$groupData);
							if($groupRt['rt']==false){
								$this->error('操作失败,curl_error:'.$rt['errorno']);
							}else{
								$msgType = "text";
								if($to_tel){
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, '恭喜您已成为三三旅游'.$to_name.'的会员！门店电话：<a href="tel:'.$to_tel.'">'.$to_tel.'</a>');
								}else{
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, '恭喜您已成为三三旅游'.$to_name.'的会员！');
								}
								echo $resultStr;
								exit;
							}
						}
					}	
						 
					//}else{
					//定向匹配
					//模糊匹配
					$funList = $this->funList;
					//$funList = json_decode($funList);		
					foreach($funList as $j)
					{
						if($form_Content==$j[0])
						{
							$resultStr="<xml>\n
							<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
							<FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
							<CreateTime>".time()."</CreateTime>\n
							<MsgType><![CDATA[news]]></MsgType>\n
							<ArticleCount>1</ArticleCount>\n
							<Articles>\n";                  
							//数组详情
							$return_arr=array(
								array(
									$j[1],
									$j[2],
									$j[3],
									$j[4]
								),
							);
				  
							//数组循环转化
							foreach($return_arr as $value)
							{
								$resultStr.="<item>\n
								<Title><![CDATA[".$value[0]."]]></Title> \n
								<Description><![CDATA[".$value[3]."]]></Description>\n
								<PicUrl><![CDATA[".$value[1]."]]></PicUrl>\n
								<Url><![CDATA[".$value[2]."]]></Url>\n
								</item>\n";
							 }
							$resultStr.="</Articles>\n
							<FuncFlag>0</FuncFlag>\n
							</xml>";   
							echo $resultStr;
							exit;
						}
					}
					$autoInfo = M('goods')->where('is_del=0 and is_show=1 and minprice<>0 and (name like "%'.$form_Content.'%" or subname like "%'.$form_Content.'%") and type_id<>97')->order('ordid desc')->limit(5)->select();
					foreach($autoInfo as $key=>$value){
						$autoInfo[$key]['info'] = msubstr(strip_tags($value['info']),45);
					}
					$count = count($autoInfo);
					if($autoInfo){
						$resultStr="<xml>\n
						<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
						<FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
						<CreateTime>".time()."</CreateTime>\n
						<MsgType><![CDATA[news]]></MsgType>\n
						<ArticleCount>".$count."</ArticleCount>\n
						<Articles>\n";  
						foreach($autoInfo as $key=>$value){
							$resultStr.="<item>\n";
							if(!$value['name']){
								$resultStr.="<Title><![CDATA[".$value['subname']."]]></Title> \n";
							}elseif(!$value['subname']){
								$resultStr.="<Title><![CDATA[<".$value['name'].">]]></Title> \n";
							}else{
								$resultStr.="<Title><![CDATA[<".$value['name'].">".$value['subname']."]]></Title> \n";
							}
							$resultStr.="<Description><![CDATA[".$value['info']."]]></Description>\n
							<PicUrl><![CDATA[http://www.33ly.com".$value['imgurl']."]]></PicUrl>\n
							<Url><![CDATA[http://m.33ly.com/Tour/detail/id/".$value['id']."]]></Url>\n
							</item>\n";
						}
						$resultStr.="</Articles>\n
						<FuncFlag>0</FuncFlag>\n
						</xml>";                
						echo $resultStr;
						exit;
					}
					
					$nowHour = date('H');
					if($nowHour<9 || $nowHour>18){
						$return_str='亲，客服MM今日工作已结束，将在下一工作日及时回复。【近期特惠】哈尔滨雪乡长白山全景畅享6天3399起；柬埔寨吴哥专享6天3899起……24小时旅游通：6666 7777。';
						$msgType = "text";
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $return_str);
						echo $resultStr;
						exit; 
					}
					
					// $msgType = "transfer_customer_service";
					// $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "wonderful176");
					// echo $resultStr;
					// exit;
					//}
				}
				//否则提示输入
				else
				{
					$msgType = "text";
					$msgTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						</xml>"; 
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType);
					echo $resultStr;
					exit;                                   
				}          
			}
			//消息事件类型为图片
			if($form_MsgType == "image")
            {                	
              $return_str='图片已收到，感谢您的参与';
              $msgType = "text";
              $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $return_str);
              echo $resultStr;
              exit;                                                  
            }
					  
			//事件消息
			if($form_MsgType=="event")
			{
				//获取事件类型
				$form_Event = $postObj->Event;

				//订阅事件
				if($form_Event=="subscribe")
				{
					
					// $return_str="亲，感谢关注三三旅游！05/29园区永旺邮轮馆&06/08昆山世茂店，双店开业，15店同庆！24小时旅游通66667777！";
					// //$return_str = "哇噻，离中奖又近了一步！三三旅游节火热进行中！\n回复<a href='http://m.33ly.com/Game/index'>“我要抢楼”</a>参与活动赢取免费游。\n回复<a href='http://m.33ly.com/Game/result'>“我要查询”</a>查询代金券及所占楼层。\n回复<a href='https://mp.weixin.qq.com/s?__biz=MjM5MzE1OTQ4Mg==&mid=2650311310&idx=1&sn=0836b9f40504ce71b498168826402445&scene=1&srcid=0831sHhMXPtETcp6ZLLsPky4&pass_ticket=VOipT0NwylxIKRqHbzaN4KkaQTCoEdZPRo77ysaInOOBqOv7gNer7IM%2Fckn%2BiJ2M#rd'>“旅游节”</a>了解旅游节爆款线路。";
					// $msgType = "text";
					// $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $return_str);
					// echo $resultStr;
					// exit;        
									
					// $wx_id_exist = M('wx_user')->where('wx_openid="'.$fromUsername.'"')->getfield('wx_id');
					// if(empty($wx_id_exist)){
						// $url_get = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$json->access_token.'&openid='.$fromUsername.'&lang=zh_CN';
						// $wxInfoRes = json_decode($this->curlGet($url_get));
						// $wxData['wx_openid'] = $fromUsername;
						// $wxData['wx_headimgurl'] = $wxInfoRes->headimgurl;
						// $wxData['wx_sex'] = $wxInfoRes->sex;
						// $wxData['wx_nickname'] = $wxInfoRes->nickname;
						// $wxData['exp_time'] = time()+3600*24*30;
						// M('wx_user')->add($wxData);
					// }
					
					//图文回复
					$gzList = M('wx_content')->where('is_del=0 and status=1 and mid=99')->order('ordid desc')->select();
					$count = count($gzList);
					$resultStr="<xml>\n
					<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
					<FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
					<CreateTime>".time()."</CreateTime>\n
					<MsgType><![CDATA[news]]></MsgType>\n
					<ArticleCount>".$count."</ArticleCount>\n
					<Articles>\n";                  

					//数组循环转化
					foreach($gzList as $key=>$value)
					{
						$resultStr.="<item>\n
						<Title><![CDATA[".$value['cname']."]]></Title> \n
						<Description><![CDATA[]]></Description>\n
						<PicUrl><![CDATA[http://www.33ly.com".$value['cimg']."]]></PicUrl>\n
						<Url><![CDATA[".$value['curl']."]]></Url>\n
						</item>\n";
					}
					$resultStr.="</Articles>\n
					<FuncFlag>0</FuncFlag>\n
					</xml>";                
					echo $resultStr;
					exit;
				}
				elseif($form_Event=='CLICK') 
				{
					$menuList = $this->menuList;
					//var_dump($menuList);
					//点击事件  
					$EventKey = $postObj->EventKey;//菜单的自定义的key值，可以根据此值判断用户点击了什么内容，从而推送不同信息  	

					foreach($menuList as $key=>$value){
						if($EventKey==$value['keyword'])
						{
							$count = count($value['son']);
							$resultStr="<xml>\n
							<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
							<FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
							<CreateTime>".time()."</CreateTime>\n
							<MsgType><![CDATA[news]]></MsgType>\n
							<ArticleCount>".$count."</ArticleCount>\n
							<Articles>\n";                  

							//数组循环转化
							foreach($value['son'] as $skey=>$svalue)
							{
								$resultStr.="<item>\n
								<Title><![CDATA[".$svalue['cname']."]]></Title> \n
								<Description><![CDATA[]]></Description>\n
								<PicUrl><![CDATA[http://www.33ly.com".$svalue['cimg']."]]></PicUrl>\n
								<Url><![CDATA[".$svalue['curl']."]]></Url>\n
								</item>\n";
							}
							$resultStr.="</Articles>\n
							<FuncFlag>0</FuncFlag>\n
							</xml>";                
							echo $resultStr;
							exit;
						} 
					}					      
				} 
			}
				  
		}
		else 
		{
			echo "nothing input";
			exit;
		}
    }
	
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
