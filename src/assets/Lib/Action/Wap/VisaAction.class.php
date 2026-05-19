<?php
class VisaAction extends BaseAction {
	public function index(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$pinyin = $_REQUEST['pinyin'];
		//$id = $_REQUEST['id'];
		$cateInfo = M('visa_cate')->where('pinyin="'.$pinyin.'"')->find();
		$this->assign('cateInfo',$cateInfo);
		$visaList = M('visa')->where('cate_id='.$cateInfo['id'])->select();
		$this->assign('visaList',$visaList);
		
		//去哪儿
		if($this->wxUserInfo['type_id'] == 1){
			$wx_openid = SESSION('wx_openid');
			$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			$share['title'] = '三三旅游签证服务';
			$share['desc'] = '家离店不远，家门口接送，家人式服务';
			$share['link'] = 'http://m.33ly.com/Index/qianzheng.html?wid='.$wx_id;
			$share['icon'] = 'http://www.33ly.com/Uploads/logo.png';    //分享图标
			$share['wx_openid'] = $wx_openid;
			$this->assign('share',$share);
			$this->assign('is_sales',1);
		}else{
			
		}	
		
        $this->display();
    }

    public function detail(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$id = $_REQUEST['id'];
		$mod = M('visa');
		$cateMod = M('visa_cate');
		$info = $mod->where('id='.$id)->find();
		$this->assign('visaInfo',$info);
		$cateInfo = $cateMod->where('id='.$info['cate_id'])->find();
		$this->assign('cateInfo',$cateInfo);
		
		//去哪儿
		if($this->wxUserInfo['type_id'] == 1){
			$wx_openid = SESSION('wx_openid');
			$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			$share['title'] = $info['name'];
			$share['desc'] = '家离店不远，家门口接送，家人式服务';
			$share['link'] = 'http://m.33ly.com/Visa/detail/id/'.$id.'.html?wid='.$wx_id;
			$share['icon'] = 'http://www.33ly.com/Uploads/logo.png';    //分享图标
			$share['wx_openid'] = $wx_openid;
			$this->assign('share',$share);
			$this->assign('is_sales',1);
		}else{
			
		}		
		
		$this->display();
    }
}