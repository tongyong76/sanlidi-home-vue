<?php 
class UserAction extends BaseAction 
{
    public function index(){
		
	}
	
	public function myorder(){
		$ordList = M('order')->where('((ordstatus<3 and ordstatus>=0) or ordstatus=4 or ordstatus=5) and uid='.$this->uid)->order('add_time desc')->select();
		$this->assign('ordList',$ordList);
		import("@.ORG.Page");
		$count = M('order')->where('(ordstatus=3 or ordstatus<0) and uid='.$this->uid)->count();
		$page=new Page($count,5);
		$page->setConfig('first','首页');
		$page->setConfig('last','末页');
		$page->setConfig('theme','%first% %upPage% %prePage%  %linkPage%  %nextPage% %downPage% %end%');
		$show=$page->show();
		$uordList = M('order')->where('(ordstatus=3 or ordstatus<0) and uid='.$this->uid)->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('uordList',$uordList);
		$this->assign('page',$show);
		$this->assign('rNav','wddd');
		
		$this->assign('likeThis',$this->likeThis());
		
		$this->display();
	}
	
	public function profile(){
        if($_POST['submit']){
			$mod = M('user');
            $data = $mod->create();
			$data['birthday'] = implode('-',$data['birthday']);
			//var_dump($data);
            if (false !== M('user')->where(array('id'=>$this->nav_user_info['id']))->save($data)) {
                //$msg = array('status'=>1, 'info'=>L('edit_success'));
				$this->success('修改成功！','__ROOT__/member/info.html?type=done');
//            }else{
                //$msg = array('status'=>0, 'info'=>L('edit_failed'));
            }  
            //$this->assign('msg', $msg);
            //$this->success($msg['info']);
        }else{
			//初始化年月日
			$type = $_REQUEST['type']?$_REQUEST['type']:0;
			if($type) $this->assign('type',$type);
			$nowyear = date('Y',time());
			for($i=1940;$i<=$nowyear;$i++){
				$byear[]=$i;
			}			
			for($i=1;$i<=12;$i++){
				$bmonth[]=$i;
			}
			for($i=1;$i<=31;$i++){
				$bday[]=$i;
			}
			$this->assign('byear',$byear);
			$this->assign('bday',$bday);
			$this->assign('bmonth',$bmonth);
			$this->assign('rNav','jbxx');
			$this->assign('likeThis',$this->likeThis());
			$this->display();
		}
    }
	
	public function password() {          
		$this->assign('userInfo', $this->nav_user_info);
		$this->assign('rNav','grxx');
		$this->assign('likeThis',$this->likeThis());
		$this->display();
    }
	
	public function passwordAct(){
		$uid = $this->nav_user_info['id'];
		$pw = $_REQUEST['pw'];
		$npw = $_REQUEST['npw'];
		$repw = $_REQUEST['repw'];
		if(!$npw) $this->ajaxReturn('','请填写新密码！',0);
		if($npw <> $repw) $this->ajaxReturn('','两次密码不相同！',0);
		$passlen = strlen($pw);
		if ($passlen < 6 || $passlen > 24) {
			$this->ajaxReturn('','密码长度必须在6-24个字符！',0);
        }
		$map['id'] = $uid;
		$map['password'] = md5($pw);
		$data['password'] = md5($npw);
		$res = M('user')->where($map)->save($data);
		if($res){
			$this->ajaxReturn($res,'修改成功',1);
		}else{
			$this->ajaxReturn('','修改失败',0);
		}
		
        //$oldpassword = $this->_post('oldpassword','trim');
	}
	
	public function register(){
		if ($_POST['submit']){			
			if(session('verify') != md5($_POST['captcha'])) {
				$this->error('验证码错误！');
			}
			$userMod = M('user');
			$data=$userMod->create();
			$data ['password'] = md5 ( $data ['password'] );
			$data ['reg_ip'] = getClientIp();
			$data ['reg_time'] = time();
			$id = $userMod->where('phone='.$data['phone'])->getfield('id');
			if($id){
				$userMod->where('id='.$id)->save($data);
			}else{
				$id = $userMod->add($data);
			}
			
			if($id){
				//setcookie ( 'id', $result, time () + 3600 * 24 * 7, '/' );
				//setcookie ( 'name', $_POST ['uname'], time () + 3600 * 24 * 7, '/' );
				//$this->success('',U('User/step2'));
				//header("Location: ?a=checkverify&m=User&g=Home&uid=$newId");
				//header("Location: $url"); 
				$this->redirect('User/step2', array('uid'=>$id,'phone'=>$data['phone'])) ;
			}
		}else{
			$jumpUrl = $_SERVER['HTTP_REFERER'];
			if($url) session('loginJumpUrl',$jumpUrl);
			$this->display();
		}
	}
	
	// 验证码
	public function verify(){
		import('ORG.Util.Image');
		Image::buildImageVerify ();
		//$this->assign('verify',session('verify'));
		//echo session('verify');
	}	
	public function verify_test() {
		header ( 'Content-type:text/html;charset=utf-8' );
		session_start ();
		$verify = md5 ( $_POST ['verification'] );
		if ($_SESSION ['verify'] == $verify) {
			echo "true";
		} else {
			echo "false";
		}
	}
	public function check_phone_exist() {
		$user_mod = M ( "User" );
		$phone = $_POST ['phone'];
		$exist = $user_mod->where ( "phone='$phone' and is_del=0 and (status=0 or status=1 or status=2)" )->getField ( "id" );
		if ($exist) {
			echo "phoneExist"; 
		}
	}
	
	public function step2(){
		if ($_POST['submit']){
			//提交并验证验证码
			$verify = $_REQUEST['verify'];
			//echo $verify;
			if($verify == $_SESSION['mobile_code']){
			//if(1){
				$uid = $_REQUEST['uid'];
				$user_info = M('user')->where('id='.$uid)->find();
				//var_dump($user_info);
				M('user')->where('id='.$uid)->setField('status',1);
				//成功则改变帐号状态并直接登录
				setcookie ( 'id', $user_info ['id'], time () + 3600 * 24 * 7, '/' );
				setcookie ( 'username', $user_info ['username'], time () + 3600 * 24 * 7, '/' );
				$this->redirect('User/step3') ;
			}else{
				$this->error('验证码错误');
			}			
		}else{
			$uid = $_REQUEST['uid'];
			$this->assign('uid',$uid);
			$phone = $_REQUEST['phone'];
			session('phoneNum',$phone);
			//发送验证码
			//测试阶段模拟$mobile_code = 6666;
			//$_SESSION['mobile_code'] = 6666;
			$this->sendCode();
			
			$this->assign('phone',substr($phone,0,3).'****'.substr($phone,7));
			$this->display();		
		}
	}
	
	public function step3(){
		echo $uid;
		$this->assign('jumpUrl','http://www.33ly.com');
		$this->assign('waitSecond',2);
		$this->display();
	}
	
	public function reg(){
		if($_POST){
			//echo '<pre>';print_r($_POST);print_r($_SESSION);
			if($_POST['mobile']!=$_SESSION['mobile'] or $_POST['mobile_code']!=$_SESSION['mobile_code'] or empty($_POST['mobile']) or empty($_POST['mobile_code'])){
				exit('手机验证码输入错误。');	
			}else{
				$_SESSION['mobile'] = '';
				$_SESSION['mobile_code'] = '';	
				exit('注册成功。');	
			}
		}else{
			$_SESSION['send_code'] = random(6,1);
			$this->assign('send_code',$_SESSION['send_code']);
			$this->display();
		}	
	}
	
	public function sms(){
		$target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
		$mobile = $_POST['mobile'];
		$send_code = $_POST['send_code'];
		$mobile_code = random(4,1);

		//防短信轰炸机
		if(empty($mobile)){
			exit('手机号码不能为空');
		}
		if(empty($_SESSION['send_code']) or $send_code!=$_SESSION['send_code']){
			//防短信轰炸机
			exit('非法请求');
		}
		$post_data = "account=cf_szss&password=szss123&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。");
		$gets =  xml_to_array(pp($post_data,$target));
		if($gets['SubmitResult']['code']==2){
			$_SESSION['mobile'] = $mobile;
			$_SESSION['mobile_code'] = $mobile_code;
		}
		echo $gets['SubmitResult']['msg'];
	}
	
	public function smscjtz($phone,$data){
		$mobile = $_POST['mobile'];
		$mobile_code = random(4,1);
		import("@.ORG.Nusoap");
		$client = new nusoap_client('http://www.jianzhou.sh.cn/JianzhouSMSWSServer/services/BusinessService?wsdl', true);
		$client->soap_defencoding = 'utf-8';
		$client->decode_utf8      = false;
		$client->xml_encoding     = 'utf-8';
		$err = $client->getError();
		if ($err) {
			$this->error('验证码错误');
		}
		$params = array(
			'account' => 'jzyy900',
			'password' => 'jiajia',
			'destmobile' =>  $phone,
			'msgText' => $data."【城建投资】",
		);
		$result = $client->call('sendBatchMessage', $params, 'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/services/BusinessService');
		if ($client->fault) {
			//echo '<h2>Fault (This is expected)</h2><pre>'; print_r($result); echo '</pre>';
			return 1;
		} else {
			$err = $client->getError();
			if ($err) {
				//echo '<h2>Error</h2><pre>' . $err . '</pre>';
				return 2;
			} else {
				//echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
				return 0;
			}
		}
		
	}
	
	public function login(){
		session_start ();
		if ($_POST['submit']){
			$requesturl =$_REQUEST['requesturl'];
			$userMod = M('user');
			$phone = $_POST ['phone'];
			if($_POST ['password']){
				$password = md5 ( $_POST ['password'] );
			}else{
				$password = '';
			}
			$user_info = $userMod->where ( "phone='$phone' and is_del=0 and password='$password'" )->find ();	
			$cookie = 1;		
			if ($user_info) {
				$id = $user_info['id'];
				//更新用户最后登录的IP和时间
				$user ['last_ip'] = getClientIp ();
				$user ['last_time'] = time ();
				$userMod->where ( "id=$id and is_del=0" )->save ( $user );
				//更新cookies
				if ($cookie == true) {
					setcookie ( 'id', $user_info ['id'], time () + 3600 * 24 * 7, '/' );
					setcookie ( 'username', $user_info ['username'], time () + 3600 * 24 * 7, '/' );
				} else {
					setcookie ( 'id', $user_info ['id'], '/' );
					setcookie ( 'username', $user_info ['username'], '/' );
				}

//强制完善个人信息
//				if($user_info ['username']){
//					//$uname = $user_info ['username'];
//					$this->success('欢迎您回来！'.$uname,'__ROOT__/');
//				}else{
//					$uname = '请完善个人信息';
//					$this->success('欢迎您回来！'.$uname,U('User/profile'));
//				}
//不强制完善个人信息
				
				$this->success('欢迎您回来！'.$uname,urldecode($requesturl));
			}else{
				$this->error('密码错误！',__ROOT__.'/login.html?requesturl='.urlencode($requesturl));
			}
			
		}else{
			if ($_SESSION ['count']) {
				unset ( $_SESSION ['count'] );
			}
			$this->display();
		}
	}
	
	public function logout(){
		setcookie ( 'id', null, time () - 1, '/' );
		setcookie ( 'username', null, time () - 1, '/' );
		//$this->success('注销成功！','__ROOT__');
		$this->redirect('/');
	}
	
	public function findpwd(){
		$userMod = M('user');
		if ($_POST['fpBtn']){
			if(session('verify') != md5($_POST['captcha'])) {
				$this->error('验证码错误！');
			}
			$phone = $_REQUEST['phone'];
			session('phoneNum',$phone);
			$this->assign('phone',substr($phone,0,3).'****'.substr($phone,7));
			//发送验证码
			$this->sendCode();
			$this->display('reset_pwd');
		}elseif($_POST['cpBtn']){
			//校验短信验证码
			$verify = $_REQUEST['verify'];
			$password = $_REQUEST['password'];
			//if($verify == $_SESSION['mobile_code']){
			if($verify == $_SESSION['mobile_code']){
				$data['password'] = MD5($password);
				M('user')->where('phone='.session('phoneNum'))->save($data);
				//echo M('user')->getlastsql();
				$user_info = $userMod->where ( "phone=".session('phoneNum')." and is_del=0" )->find ();	
				$cookie = 1;
				if ($cookie == true) {
					setcookie ( 'id', $user_info ['id'], time () + 3600 * 24 * 7, '/' );
					setcookie ( 'username', $user_info ['username'], time () + 3600 * 24 * 7, '/' );
				} else {
					setcookie ( 'id', $user_info ['id'], '/' );
					setcookie ( 'username', $user_info ['username'], '/' );
				}
				$this->success('修改成功','http://www.33ly.com');
			}else{
				$this->error('验证码错误，请重新填写',U('User/findpwd'));
			}			
		}else{
			$this->display();
		}
	}
	
	public function sendCode(){
		$phone = session('phoneNum');
		$mobile_code = random(4,1);
		$data = "您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。24小时旅游通0512-66667777";
		$_SESSION['mobile_code'] = $mobile_code;
		$reslut = $this->smsxwkj($phone,$data);
		//if($reslut) $this->error('发送错误！');
	}
	

	
	public function favsAdd(){
		$uid=$this->_get('uid','intval');
		$id=$this->_get('id','intval');
		//ajaxReturn(data,info,status)
		//$this->ajaxReturn('已经收藏过了'.$uid."aaa".$id,'已经添加了',0);
		if(M("goods_favs")->where(array('goods_id'=>$id,'uid'=>$uid))->find()){
            $this->ajaxReturn('已经投过票了','已经投过票了',0);
        }else{
			M("goods_favs")->add(array(
                'goods_id'=>$id,
                'uid'=>$uid,
                'add_time'=>time(),
                'ip'=>$_SERVER["REMOTE_ADDR"]
            ));
			D("goods")->where(array('id'=>$id))->setInc('favs');
			$this->ajaxReturn(D("goods")->where(array('id'=>$id))->getField('favs'),'添加成功',1);
		}
	}
	
	public function follow(){
		$uid=$this->_get('uid','intval');
		$id=$this->_get('id','intval');
		//ajaxReturn(data,info,status)
		//$this->ajaxReturn('已经收藏过了'.$uid."aaa".$id,'已经添加了',0);
		if(M("goods_follow")->where(array('goods_id'=>$id,'uid'=>$uid))->find()){
            $this->ajaxReturn('已关注','已关注',0);
        }else{
			M("goods_follow")->add(array(
                'goods_id'=>$id,
                'uid'=>$uid,
                'add_time'=>time(),
                'ip'=>$_SERVER["REMOTE_ADDR"]
            ));
			D("goods")->where(array('id'=>$id))->setInc('follow');
			$this->ajaxReturn(D("goods")->where(array('id'=>$id))->getField('follow'),'添加成功',1);
		}
	}
	
	public function img() {
		$this->assign('userInfo', $this->nav_user_info);
		$this->assign('rNav','wdtx');
		$this->assign('likeThis',$this->likeThis());
		$this->display ();
	}
	
	public function record(){
		
		$recordList = M('goods_record as gr')
		->join('33_goods as g on g.id=gr.goods_id')
		->field('gr.record_id,g.id as goods_id,gr.add_time,g.sn,g.sign_up,g.name,g.subname,g.minprice,g.imgurl')
		->where('gr.user_id='.cookie('id'))->order('gr.add_time desc')->select();
		foreach($recordList as $key=>$value){
			$recordList[$key]['dep'] = $this->getDeparture($value['goods_id'],$value['sign_up']);
		}
		$this->assign('recordList',$recordList);
		$this->assign('rNav','lljl');
		$this->assign('likeThis',$this->likeThis());
		$this->display ();
	}
	
	public function upfile() {
		$uid = $this->uid;
		$path = "./Uploads/avatar/";
       
		$file_src = "src.png"; 
		$filenameLast = "last.png"; 
		//$filename48 = "2.png"; 
		//$filename20 = "3.png";
		$filename162 = "user162_".$uid.".jpg";

		$src=base64_decode($_POST['pic']);
		$pic1=base64_decode($_POST['pic1']);
		$pic2=base64_decode($_POST['pic2']);
		$pic3=base64_decode($_POST['pic3']);

		if($src) {
		file_put_contents($file_src,$src);
		}

		file_put_contents($path.$filenameLast,$pic1);
		//file_put_contents($path.$filename48,$pic2);
		//file_put_contents($path.$filename20,$pic3);
		file_put_contents($path.$filename162,$pic1);
		$data['imgurl'] = $filename162;
		M('user')->where('id='.$uid)->save($data);
		$rs['status'] = 1;

		echo json_encode($rs);
    }
	
	public function score(){
		$uid=$this->_get('uid','intval');
		$id=$this->_get('id','intval');
		$score=$this->_get('score','intval');
		if(M("goods_score")->where(array('goods_id'=>$id,'uid'=>$uid,'score'=>$score))->find()){
            $this->ajaxReturn('已关注','已关注',0);
        }else{
			M("goods_score")->add(array(
                'goods_id'=>$id,
                'uid'=>$uid,
				'score'=>$score,
                'add_time'=>time(),
                'ip'=>$_SERVER["REMOTE_ADDR"]
            ));
			D("goods")->where(array('id'=>$id))->setInc('score');
			$data['score'] = $score;
			$data['num'] = M('goodsScore')->where(array('goods_id'=>$id))->count();
			$totelScore = M('goodsScore')->where(array('goods_id'=>$id))->select();
			$totelScoreAll = '';
			foreach($totelScore as $key=>$value){
				$totelScoreAll = $totelScoreAll + $value['score'];
			}
			$data['avg'] = round((80*20+$totelScoreAll*20)/($data['num']+20));
			$this->ajaxReturn($data,'添加成功',1);
		}
	}
	
	public function mytg(){
		$articleMod = M('article');
		import("@.ORG.Page");
		$count=$articleMod->where('uid='.$this->uid)->count();
		$page=new Page($count,10);
		$show=$page->show();
		$list = $articleMod->where('uid='.$this->uid)->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->assign('rNav','wytg');
		$this->display();
	}
	
	public function tg(){
		$id=$this->_get('id','intval');
		
		if ($_POST['submit']){
			$article=M('Article');
			$data['title'] = $_REQUEST['title'];
			$data['source'] = $_REQUEST['source'];
			$data['info'] = $_REQUEST['info'];
			$data['status'] = 0;
			if(!$data['title']) $this->error('修改失败');
			if($data['source']){
				$data['source'] = $_REQUEST['surl'];
			}
			$article->where('id='.$id)->save($data);
			$this->success('修改成功','__ROOT__/mytg.html');
		}else{
			$this->assign('modify',1);
			$info = M('article')->where(array('id'=>$id,'uid'=>$this->uid))->find();
			if($info){
				$this->assign('info',$info);
				$this->display('Article:submit');
			}else{
				$this->error('非法参数');
			}
		}
		
	}
	
	//个人中心猜你喜欢
	public function likeThis(){
		$mod = M('goods');
		$map['is_del'] = 0;
		$map['is_hot'] = 1;
		$map['minprice'] = array('neq',0);
		$list = $mod->where($map)->order('add_time desc')->limit(12)->select();
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),45);
			switch($value['type_id']){
				case 1:
					$list[$key]['type'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['type'] = 'guonei';
					break;
				case 3:
					$list[$key]['type'] = 'chujing';
					break;
				case 97:
					$list[$key]['type'] = 'group';
					break;
				case 326:
					$list[$key]['type'] = 'suzhou';
					break;
			}
		}
		return $list;
	}
	
}
