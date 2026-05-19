<?php
//收客宝
class GameAction extends BaseAction {
	private $wx_appid = 'wx9f98139e202c7003';
	private $wx_appsecret = 'fc569bb8896f29b5269fe49169d0c77a';
	
    public function _initialize(){
		header('Content-type: text/html; charset=utf-8');
		$this->checkOpenid();//检查openid，存入SESSION
		//检查用户信息是否存在
		$userInfo = $this->getUserInfo();
		if(session('wx_openid') and ($this->checkUserExist() == 'add' or $this->checkUserExist() == 'update')){
			//echo "222";
			if($userInfo['subscribe'] == 1){
				//关注过的用户
				if($this->checkUserExist() == 'add'){
					$addData['wx_openid'] = $userInfo['openid'];
					$addData['wx_nickname'] = $userInfo['nickname'];
					$addData['wx_headimgurl'] = $userInfo['headimgurl'];
					$addData['wx_sex'] = $userInfo['sex'];
					$addData['last_time'] = time();
					$addData['is_subscribe'] = 1;
					M('wx_user')->add($addData);
					SESSION('wx_nickname',$addData['wx_nickname']);
					SESSION('wx_headimgurl',$addData['wx_headimgurl']);
				}
				if($this->checkUserExist() == 'update'){
					$updateData['wx_nickname'] = $userInfo['nickname'];
					$updateData['wx_headimgurl'] = $userInfo['headimgurl'];
					$updateData['wx_sex'] = $userInfo['sex'];
					$updateData['last_time'] = time();
					$updateData['is_subscribe'] = 1;
					M('wx_user')->where(array('wx_openid'=>session('wx_openid')))->save($updateData);
					SESSION('wx_nickname',$updateData['wx_nickname']);
					SESSION('wx_headimgurl',$updateData['wx_headimgurl']);
				}
			}else{
				//未关注的用户
				//echo "未关注";
				$state=str_replace("&","△",$_SERVER['REQUEST_URI']);
				$redirect_uri = urlencode(C('ulogin_url'));
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
				header("Location: $url");
			}
		}else{
			//更新关注状态
			$wx_openid = session('wx_openid');
			if($userInfo['subscribe'] == 1){
				$data['is_subscribe'] = 1;	
			}else{
				$data['is_subscribe'] = 0;
			}
			M('wx_user')->where(array('wx_openid'=>$wx_openid))->save($data);
		}
		
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//分享信息
		$share['title'] = '三三旅游节：全民游长白山1599起，独家包机包酒店';
		$share['desc'] = '9月9-10月10第二届三三旅游节震撼来袭：1站式旅行服务、整包4家酒店、独包5架飞机、打造8大爆款！';
		$share['link'] = 'https://mp.weixin.qq.com/s?__biz=MjM5MzE1OTQ4Mg==&mid=2650311310&idx=1&sn=0836b9f40504ce71b498168826402445&scene=1&srcid=0831sHhMXPtETcp6ZLLsPky4&pass_ticket=VOipT0NwylxIKRqHbzaN4KkaQTCoEdZPRo77ysaInOOBqOv7gNer7IM%2Fckn%2BiJ2M#rd';
		$share['icon'] = 'http://m.33ly.com/Uploads/logo.png';    //分享图标
		$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
	}
	
    /**
     * 检查是否绑定
     * 
     * @return boolean
     */
	private function checkBind(){
		return true;
	}
	
	//获取openid
	private function checkOpenid(){
		$wx_openid = session('wx_openid');
        if(empty($wx_openid)){
            $state=str_replace("&","△",$_SERVER['REQUEST_URI']);
			$redirect_uri = urlencode(C('slogin_url'));
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$state."#wechat_redirect";
			header("Location: $url");
		}else{
			//echo $wx_openid;
		}
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
	
	//检查用户信息是否存在
	private function checkUserExist(){
		$map['wx_openid'] = session('wx_openid');
		$model = M('wx_user');
		$wxUserInfo = $model->where($map)->find();
		if(empty($wxUserInfo['wx_openid'])){
			return 'add';
		}else{
			if(($wxUserInfo['last_time']+3600*24*7) < time()){
				return 'update';
			}else{
				SESSION('wx_nickname',$wxUserInfo['wx_nickname']);
				SESSION('wx_headimgurl',$wxUserInfo['wx_headimgurl']);
				return 'keep';
			}
		}
	}
	
    /**
     * 已关注用户获取用户基本信息
     *
     * @return array 
     */
	private function getUserInfo(){
		$wx_openid = session('wx_openid');
		$access_token = $this->getAccessToken();
		$post_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$wx_openid.'&lang=zh_CN';
		$json=json_decode(http_post($post_url),true);
		return $json;
	}
	
	//获取access_token
	private function getAccessToken(){
		$post_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wx_appid.'&secret='.$this->wx_appsecret;
		$json=json_decode(http_post($post_url));
		return $json->access_token;
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
		
		//定单集   （A可点入查看订单详情，B不行，只显示结果）
		$wx_openid = SESSION('wx_openid');
		$orderList = M('order')->where('ordshare="'.$wx_openid.'"')->order('add_time desc')->select();
		$this->assign('orderList',$orderList);
		
		//是否有能力
		$is_handle = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('type_id');
		$this->assign('is_handle',$is_handle);
		
		//总金额
		$res = M('order as o')->field('sum(g.share_price*(o.adult_num + o.child_num)) as total_income')->join('33_goods as g on g.id=o.gid')->where('o.ordshare="'.$wx_openid.'" and ordstatus=3')->find();
		$total_income = $res['total_income'];
		$this->assign('total_income',$total_income);
		
		$this->assign('nav','nav_3');
		$this->display();
    }
	
	//旅游节活动首页+分享页
	public function index(){
		$content = '<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: justify; text-decoration: inherit; color: rgb(150, 150, 150); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　9月9-10月10第2届三三旅游节震撼来袭：１站式旅行服务、整包４家酒店、独包５架飞机、打造8大爆款~
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<p></p>
<section style="border: 0px; box-sizing: border-box; width: 100%; margin: 0.8em 0px 0.2em; clear: both; padding: 0px;" class="tn-Powered-by-XIUMI">
    <img style="box-sizing: border-box; width: 100%; height: auto !important;" src="http://mm.33ly.com/Public/ueditor/php/upload/64511472550571.jpg" class="tn-Powered-by-XIUMI"/>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 1em; font-family: inherit; text-align: justify; text-decoration: inherit; color: inherit; box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        <br class="tn-Powered-by-XIUMI" style="box-sizing: border-box;"/>
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: justify; text-decoration: inherit; color: rgb(150, 150, 150); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　除了有给力的爆款产品外，9月9-10月10旅游节期间我们还有精彩的“抢楼看世界”微信活动，参与活动即有机会赢取免费游哦～
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin: 0.8em 0px 0.5em; overflow: hidden; padding: 0px; box-sizing: border-box !important;" class="tn-Powered-by-XIUMI">
    <section style="display: inline-block; font-size: 1em; font-family: inherit; text-decoration: inherit; color: rgb(255, 255, 255); border-color: rgb(166, 91, 203); box-sizing: border-box;" class="tn-Powered-by-XIUMI">
        <section style="height: 2em; display: inline-block; padding: 0.3em 0.5em; text-align: center; font-size: 1em; line-height: 1.4; vertical-align: top; font-family: inherit; box-sizing: border-box !important; background-color: rgb(166, 91, 203);" class="tn-Powered-by-XIUMI">
            <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
                奖项设置
            </section>
        </section>
        <section style="display: inline-block; height: 2em; width: 0.5em; vertical-align: top; border-left-width: 0.5em; border-left-style: solid; border-left-color: rgb(166, 91, 203); font-size: 1em; box-sizing: border-box !important; border-top-width: 1em !important; border-top-style: solid !important; border-top-color: transparent !important; border-bottom-width: 1em !important; border-bottom-style: solid !important; border-bottom-color: transparent !important;" class="tn-Powered-by-XIUMI"></section>
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; box-sizing: border-box; width: 100%; margin: 0.8em 0px 0.2em; clear: both; padding: 0px;" class="tn-Powered-by-XIUMI">
    <img style="box-sizing: border-box; width: 100%; height: auto !important;" src="http://mm.33ly.com/Public/ueditor/php/upload/34501472550572.jpg" class="tn-Powered-by-XIUMI"/>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; box-sizing: border-box; width: 100%; margin: 0.8em 0px 0.2em; clear: both; padding: 0px;" class="tn-Powered-by-XIUMI">
    <img style="box-sizing: border-box; width: 100%; height: auto !important;" src="http://mm.33ly.com/Public/ueditor/php/upload/60221472550572.jpg" class="tn-Powered-by-XIUMI"/>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin: 0.8em 0px 0.5em; overflow: hidden; padding: 0px; box-sizing: border-box !important;" class="tn-Powered-by-XIUMI">
    <section style="display: inline-block; font-size: 1em; font-family: inherit; text-decoration: inherit; color: rgb(255, 255, 255); border-color: rgb(166, 91, 203); box-sizing: border-box;" class="tn-Powered-by-XIUMI">
        <section style="height: 2em; display: inline-block; padding: 0.3em 0.5em; text-align: center; font-size: 1em; line-height: 1.4; vertical-align: top; font-family: inherit; box-sizing: border-box !important; background-color: rgb(166, 91, 203);" class="tn-Powered-by-XIUMI">
            <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
                参与方式
            </section>
        </section>
        <section style="display: inline-block; height: 2em; width: 0.5em; vertical-align: top; border-left-width: 0.5em; border-left-style: solid; border-left-color: rgb(166, 91, 203); font-size: 1em; box-sizing: border-box !important; border-top-width: 1em !important; border-top-style: solid !important; border-top-color: transparent !important; border-bottom-width: 1em !important; border-bottom-style: solid !important; border-bottom-color: transparent !important;" class="tn-Powered-by-XIUMI"></section>
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: justify; text-decoration: inherit; color: rgb(150, 150, 150); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　关注三三旅游官方微信（微信号：sz-ssly），回复“我要抢楼”，按照提示完成相关操作，即可参与！
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: justify; text-decoration: inherit; color: rgb(150, 150, 150); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　首次成功参与的小伙伴，都会抢到一个唯一的楼层号码以及20元代金券1张（11/30前在三三购买任意服务均可抵用，不与旅游节优惠同时享用）
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: justify; text-decoration: inherit; color: rgb(150, 150, 150); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　回复“我要查询”即可查询已获得的代金券以及成功抢占的楼层号码。
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 1em; font-family: inherit; text-align: justify; text-decoration: inherit; color: inherit; box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        <br class="tn-Powered-by-XIUMI" style="box-sizing: border-box;"/>
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: justify; text-decoration: inherit; color: rgb(150, 150, 150); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　偷偷告诉你哦：召唤更多小伙伴一起参与，还可抢占更多楼层，赢取免费游的机会更大~
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin: 0.8em 0px 0.5em; overflow: hidden; padding: 0px; box-sizing: border-box !important;" class="tn-Powered-by-XIUMI">
    <section style="display: inline-block; font-size: 100%; font-family: inherit; text-decoration: inherit; color: rgb(255, 255, 255); border-color: rgb(166, 91, 203); box-sizing: border-box;" class="tn-Powered-by-XIUMI">
        <section style="height: 2em; display: inline-block; padding: 0.3em 0.5em; text-align: center; font-size: 100%; line-height: 1.4; vertical-align: top; font-family: inherit; box-sizing: border-box !important; background-color: rgb(166, 91, 203);" class="tn-Powered-by-XIUMI">
            <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
                中奖规则
            </section>
        </section>
        <section style="display: inline-block; height: 2em; width: 0.5em; vertical-align: top; border-left-width: 0.5em; border-left-style: solid; border-left-color: rgb(166, 91, 203); font-size: 100%; box-sizing: border-box !important; border-top-width: 1em !important; border-top-style: solid !important; border-top-color: transparent !important; border-bottom-width: 1em !important; border-bottom-style: solid !important; border-bottom-color: transparent !important;" class="tn-Powered-by-XIUMI"></section>
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: justify; text-decoration: inherit; color: rgb(150, 150, 150); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　1、活动期间9/12、9/19、9/26、10/03为抽奖日，每个抽奖日产生6个幸运号码
    </section>
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　2、6个幸运号码为抽奖日当天15:00时总参与人次乘以指定比例（11%、22%、33%、44%、55%、66%）再四舍五入获得。如抽奖日
    </section>
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　3、如抽奖日当天参与人次为12345，12345*11%=1357.95，四舍五入后得道的数值就是1358，那么第一个幸运号码就是1358。其他幸运号码以此类推。
    </section>
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　4、为保证公平，每个号码只有一次中奖机会，活动期间不重复中奖。
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin: 0.8em 0px 0.5em; overflow: hidden; padding: 0px; box-sizing: border-box !important;" class="tn-Powered-by-XIUMI">
    <section style="display: inline-block; font-size: 100%; font-family: inherit; text-decoration: inherit; color: rgb(255, 255, 255); border-color: rgb(166, 91, 203); box-sizing: border-box;" class="tn-Powered-by-XIUMI">
        <section style="height: 2em; display: inline-block; padding: 0.3em 0.5em; text-align: center; font-size: 100%; line-height: 1.4; vertical-align: top; font-family: inherit; box-sizing: border-box !important; background-color: rgb(166, 91, 203);" class="tn-Powered-by-XIUMI">
            <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
                中奖名单公布
            </section>
        </section>
        <section style="display: inline-block; height: 2em; width: 0.5em; vertical-align: top; border-left-width: 0.5em; border-left-style: solid; border-left-color: rgb(166, 91, 203); font-size: 100%; box-sizing: border-box !important; border-top-width: 1em !important; border-top-style: solid !important; border-top-color: transparent !important; border-bottom-width: 1em !important; border-bottom-style: solid !important; border-bottom-color: transparent !important;" class="tn-Powered-by-XIUMI"></section>
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: justify; text-decoration: inherit; color: rgb(150, 150, 150); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        　　9/13、9/19、9/26、10/03将通过微信公众号公布中奖名单，工作人员也将及时与获奖者取得联系，安排兑奖事宜。
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 1em; font-family: inherit; text-align: justify; text-decoration: inherit; color: inherit; box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        <br class="tn-Powered-by-XIUMI" style="box-sizing: border-box;"/>
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; margin-top: 0px; margin-bottom: 0px; clear: both; font-size: 87.5%; font-family: inherit; text-align: center; text-decoration: inherit; color: rgb(166, 91, 203); box-sizing: border-box; padding: 0px;" class="tn-Powered-by-XIUMI">
    <section class="tn-Powered-by-XIUMI" style="box-sizing: border-box;">
        点击下图，了解更多旅游节爆款线路
    </section>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<section style="border: 0px; box-sizing: border-box; width: 100%; margin: 0.8em 0px 0.2em; clear: both; padding: 0px;" class="tn-Powered-by-XIUMI">
    <img style="box-sizing: border-box; width: 100%; height: auto !important;" src="http://mm.33ly.com/Public/ueditor/php/upload/27871472550572.gif" class="tn-Powered-by-XIUMI"/>
    <section style="width: 0px; height: 0px; clear: both;"></section>
</section>
<p></p>
<p>
    <br/>
</p>';
		$this->assign('content',$content);
		$this->display();
	}
	
	//参与页
	public function join(){
		$wx_openid = SESSION('wx_openid');
		$model = M('wx_game_info');
		if(IS_POST){
			$data = $model->create();
			$exist = $model->where('wx_openid="'.$wx_openid.'"')->getfield('game_id');
			if(empty($exist)){
				//插入wx_game_info
				$data['wx_openid'] = $wx_openid;
				$model->add($data);
				
				//插入wx_game_floor
				$floorData['wx_openid'] = $wx_openid;
				$floorData['add_time'] = time();
				$floorData['reason'] = 'new';
				$exist2 = M('wx_game_floor')->where(array('wx_openid'=>$wx_openid,'reason'=>'new'))->getfield('floor_id');
				if(empty($exist2)){
					M('wx_game_floor')->add($floorData);
				}
				
				//更新wx_user
				$userData['real_name'] = $data['name'];
				$userData['real_phone'] = $data['phone'];
				$userData['last_time'] = time();
				M('wx_user')->where('wx_openid="'.$wx_openid.'"')->save($userData);
				$this->success('抢楼成功',U('Game/result'));
			}else{
				//更新wx_game_info
				$model->where('wx_openid="'.$wx_openid.'"')->save($data);
				
				//更新wx_user
				$userData['real_name'] = $data['name'];
				$userData['real_phone'] = $data['phone'];
				$userData['last_time'] = time();
				M('wx_user')->where('wx_openid="'.$wx_openid.'"')->save($userData);
				$this->success('更新成功',U('Game/join'));
			}
		}else{
			$info = $model->where('wx_openid="'.$wx_openid.'"')->find();
			$this->assign('info',$info);
			$shop = C('shop');
			$this->assign('shop',$shop);
			$this->display();
		}
		
		//提交后获得号码+代金券
		
	}
	
	//占楼
	public function getFloor(){
		$data['wx_openid'] = SESSION('wx_openid');
		$data['reason'] = 'share'.date('md',time());
		$data['add_time'] = time();
		$exist = M('wx_game_floor')->where(array('wx_openid'=>$data['wx_openid'],'reason'=>$data['reason']))->getfield('floor_id');
		if(empty($exist)){
			$new = M('wx_game_floor')->add($data);
			$sum = M('wx_game_floor')->count();
			$this->ajaxReturn($new,$sum,1);
		}else{
			$this->ajaxReturn('每天只有第一次分享才能获得新楼层，记得明天再来哦！','',2);
		}
		
	}
	
	public function update(){
		$wx_openid = SESSION('wx_openid');
		$model = M('wx_game_info');
		$data['name'] = $_REQUEST['name'];
		$data['phone'] = $_REQUEST['phone'];
		$data['shop'] = $_REQUEST['shop'];
		$exist = $model->where('wx_openid="'.$wx_openid.'"')->getfield('game_id');
		if(empty($exist)){
			//插入wx_game_info
			$data['wx_openid'] = $wx_openid;
			$model->add($data);
			
			//插入wx_game_floor
			$floorData['wx_openid'] = $wx_openid;
			$floorData['add_time'] = time();
			$floorData['reason'] = 'new';
			$exist2 = M('wx_game_floor')->where(array('wx_openid'=>$wx_openid,'reason'=>'new'))->getfield('floor_id');
			if(empty($exist2)){
				M('wx_game_floor')->add($floorData);
			}
			
			//更新wx_user
			$userData['real_name'] = $data['name'];
			$userData['real_phone'] = $data['phone'];
			$userData['last_time'] = time();
			M('wx_user')->where('wx_openid="'.$wx_openid.'"')->save($userData);
			$this->ajaxReturn('add','抢楼成功',1);
		}else{
			//更新wx_game_info
			$model->where('wx_openid="'.$wx_openid.'"')->save($data);
			
			//更新wx_user
			$userData['real_name'] = $data['name'];
			$userData['real_phone'] = $data['phone'];
			$userData['last_time'] = time();
			M('wx_user')->where('wx_openid="'.$wx_openid.'"')->save($userData);
			$this->ajaxReturn('edit','更新信息成功',1);
		}		
	}
	
	//结果页+奖券页
	public function result(){
		//判定是否绑定
		if($this->checkSubscribe()){
			$wx_openid = SESSION('wx_openid');
			//楼层
			$model = M('wx_game_floor');
			$res['sum'] = $model->count();
			$res['floor'] = $model->where(array('wx_openid'=>$wx_openid))->select();
			$res['count'] = count($res['floor']);
			//代金券
			$res['ticket'] = M('wx_game_info')->where(array('wx_openid'=>$wx_openid))->find();
			$this->assign('res',$res);
			
			$this->display();
			
		}else{
			$url = U('Game/subscribe');
			header("Location: $url");
		}
	}
	
	
	//绑定页
	public function subscribe(){
		$this->display();
	}
	
	//检查绑定
	private function checkSubscribe(){
		$wx_openid = SESSION('wx_openid');
		$model = M('wx_user');
		$is_subscribe = $model->where(array('wx_openid'=>$wx_openid))->getfield('is_subscribe');
		if(empty($is_subscribe)){
			return false;
		}else{
			return true;
		}
	}

}