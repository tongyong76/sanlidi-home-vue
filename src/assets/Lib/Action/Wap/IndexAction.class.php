<?php
class IndexAction extends BaseAction {
    public function index(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		//banner
		
		//热推
		$hotList = M('goods')->where('is_del=0 and is_show=1 and minprice<>0')->order('ordid desc')->limit(10)->select();
		$this->assign('hotList',$hotList);
		
		//移动站首页幻灯
		$mBannerList = M('ad')->where('pid=7 and status=1')->select();
		$this->assign('mBannerList',$mBannerList);
		
		//去哪儿
		if($this->wxUserInfo['type_id'] == 1){
			$wx_openid = SESSION('wx_openid');
			$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			$share['title'] = '三三旅游，服务到家';
			$share['desc'] = '家离店不远，家门口接送，家人式服务';
			$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
			$share['icon'] = 'http://www.33ly.com/Uploads/logo.png';    //分享图标
			$share['wx_openid'] = $wx_openid;
			$this->assign('share',$share);
			$this->assign('is_sales',1);
		}else{
			
		}
		
		
		$this->display();
    }
	
	public function chujing(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$list = M('goods')->where('is_del=0 and is_show=1 and type_id=3 and minprice<>0')->order('ordid desc,add_time desc')->limit(6)->select();	
		//热门分类
		$hotCate = M('goods_cate')->where('pid=3 and is_del=0 and is_show=1')->order('ordid desc')->limit(4)->select();
		$this->assign('hotCate',$hotCate);
		
		//去哪儿
		if($this->wxUserInfo['type_id'] == 1){
			$wx_openid = SESSION('wx_openid');
			$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			$share['title'] = '三三旅游，服务到家';
			$share['desc'] = '家离店不远，家门口接送，家人式服务';
			$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
			$share['icon'] = 'http://www.33ly.com/Uploads/logo.png';    //分享图标
			$share['wx_openid'] = $wx_openid;
			$this->assign('share',$share);
			$this->assign('is_sales',1);
		}else{
			
		}	
		
		//广告
		$adInfo = M('ad')->where('cname = "ydqt3"')->find();
		$this->assign('adInfo',$adInfo);
		$this->assign('title','出境游');
		$this->assign('list',$list);
		$this->assign('type_id',3);
		$this->display();
	}
	
	public function guonei(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$list = M('goods')->where('is_del=0 and is_show=1 and type_id=2 and minprice<>0')->order('ordid desc,add_time desc')->limit(6)->select();
		//热门分类
		$hotCate = M('goods_cate')->where('pid=2 and is_del=0 and is_show=1')->order('ordid desc')->limit(4)->select();
		$this->assign('hotCate',$hotCate);
		
		//去哪儿
		if($this->wxUserInfo['type_id'] == 1){
			$wx_openid = SESSION('wx_openid');
			$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			$share['title'] = '三三旅游，服务到家';
			$share['desc'] = '家离店不远，家门口接送，家人式服务';
			$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
			$share['icon'] = 'http://www.33ly.com/Uploads/logo.png';    //分享图标
			$share['wx_openid'] = $wx_openid;
			$this->assign('share',$share);
			$this->assign('is_sales',1);
		}else{
			
		}
		
		
		//广告
		$adInfo = M('ad')->where('cname = "ydqt2"')->find();
		$this->assign('adInfo',$adInfo);
		$this->assign('title','国内游');
		$this->assign('list',$list);
		$this->assign('type_id',2);
		$this->display('chujing');
	}
	
	public function zhoubian(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$list = M('goods')->where('is_del=0 and is_show=1 and type_id=1 and minprice<>0')->order('ordid desc,add_time desc')->limit(6)->select();
		//热门分类
		$hotCate = M('goods_cate')->where('pid=1 and is_del=0 and is_show=1')->order('ordid desc')->limit(4)->select();
		$this->assign('hotCate',$hotCate);
		
		//去哪儿
		if($this->wxUserInfo['type_id'] == 1){
			$wx_openid = SESSION('wx_openid');
			$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			$share['title'] = '三三旅游，服务到家';
			$share['desc'] = '家离店不远，家门口接送，家人式服务';
			$share['link'] = 'http://m.33ly.com/?wid='.$wx_id;
			$share['icon'] = 'http://www.33ly.com/Uploads/logo.png';    //分享图标
			$share['wx_openid'] = $wx_openid;
			$this->assign('share',$share);
			$this->assign('is_sales',1);
		}else{
			
		}
		
		//广告
		$adInfo = M('ad')->where('cname = "ydqt1"')->find();
		$this->assign('adInfo',$adInfo);
		$this->assign('title','周边游');
		$this->assign('list',$list);
		$this->assign('type_id',1);
		$this->display('chujing');
	}
	
	public function youlun(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		//甩仓
		$hotList = M('ship')->where('is_del=0 and is_show=1 and is_hot=1')->select();
		$this->assign('hotList',$hotList);
		
		//日韩、东南亚
		$nowTime = time();
		$rhList = M('ship')->where('cate_id=1 and is_del=0 and is_show=1 and minprice<>0 and start_time>'.$nowTime)->order('ordid desc,start_time')->limit(4)->select();
		$this->assign('rhList',$rhList);
		$dnyList = M('ship')->where('cate_id=2 and is_del=0 and is_show=1 and minprice<>0 and start_time>'.$nowTime)->order('ordid desc,start_time')->limit(4)->select();
		$this->assign('dnyList',$dnyList);
	
		$this->display();
	}
	
	public function tuandui(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$list = M('goods')->where('is_del=0 and is_show=1 and type_id=97 and minprice<>0')->order('ordid desc,add_time desc')->limit(6)->select();
		$this->assign('title','团队游');
		$this->assign('list',$list);
		$this->assign('type_id',97);
		$this->display('chujing');
	}
	
	public function ziyouxing(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$list = M('goods')->where('is_del=0 and is_show=1 and is_zyx=1 and minprice<>0')->order('ordid desc,add_time desc')->limit(6)->select();
		$this->assign('title','自由行');
		$this->assign('list',$list);
		$this->display();
	}
	
	public function getMoreFree(){
		$mod = M('goods');
		$sid = $_REQUEST['sid'];
		$num = 3;
		$list = $mod->where('is_zyx=1 and is_del=0 and is_show=1 and minprice<>0')->order('ordid desc,add_time desc')->limit($sid,$num)->select();
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),45);
		}
		if($list){
			$this->assign('list', $list);
			$data['list'] = $this->fetch('ajax_tour_list');
			$data['sid'] = $sid + $num;
			$this->ajaxReturn($data,'',1);
		}else{
			$this->ajaxReturn('','',0);
		}
	}
	
	public function getMore(){
		$mod = M('goods');
		$tid = $_REQUEST['tid']?$_REQUEST['tid']:0;
		$sid = $_REQUEST['sid'];
		
		if($tid){
			$num = 3;
			$list = $mod->where('type_id='.$tid.' and is_del=0 and is_show=1 and minprice<>0')->order('ordid desc,add_time desc')->limit($sid,$num)->select();
			foreach($list as $key=>$value){
				$list[$key]['info'] = msubstr(strip_tags($value['info']),45);
			}
			if($list){
				$this->assign('list', $list);
				$data['list'] = $this->fetch('ajax_tour_list');
				$data['sid'] = $sid + $num;
				$this->ajaxReturn($data,'',1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}else{
			$num = 5;
			$list = $mod->where('is_del=0 and is_show=1 and minprice<>0')->order('ordid desc')->limit($sid,$num)->select();
			if($list and $sid<31){
				$this->assign('hotList', $list);
				$data['list'] = $this->fetch('ajax_hot_list');
				$data['sid'] = $sid + $num;
				$this->ajaxReturn($data,'',1);
			}else{
				$this->ajaxReturn('','',0);
			}		
		}
	}
	
	public function qianzheng(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$mod = M('visa');
		$cateMod = M('visa_cate');
		//热推
		$hotList = $mod
		->field('33_visa.id,33_visa.name,33_visa.price,c.imgurl')
		->join('33_visa_cate as c ON 33_visa.cate_id = c.id')
		->where('33_visa.is_del=0 and 33_visa.is_hot=1')
		->limit(8)
		->select();
		$this->assign('hotList',$hotList);
		
		//分类
		$cateList = $cateMod->where('floor=1 and is_del=0')->select();
		foreach($cateList as $key=>$value){
			$res = $cateMod->where('pid='.$value['id'].' and is_del=0')->select();
			$cateList[$key]['son'] = $res;
		}
		$this->assign('cateList',$cateList);
		
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
	
	//深度苏州
	public function suzhou(){
		//苏州热推 type_id=326
		$hotList = M('goods')->where('type_id=326 and is_del=0 and minprice<>0')->order('ordid desc,add_time desc')->limit(5)->select();
		$this->assign('hotList',$hotList);
		
		$this->display();
    }
	
	//电商精选
	public function jingxuan(){
		$cateList[1] = array('CateName'=>'超值热卖','CateUrl'=>'/jingxuan/chaozhi/');
		$cateList[2] = array('CateName'=>'夏日漂流','CateUrl'=>'/jingxuan/piaoliu/');
		$cateList[3] = array('CateName'=>'每日特价','CateUrl'=>'/jingxuan/tejia/');
		$cateList[4] = array('CateName'=>'自组团','CateUrl'=>'/jingxuan/zizhutuan/');
		$cateList[5] = array('CateName'=>'大西北','CateUrl'=>'/jingxuan/xibei/');
		//$cateList[99] = array('CateName'=>'ALL','CateUrl'=>'/jingxuan/');
		$this->assign('cateList',$cateList);
		$cateName = $_REQUEST['cate'];
		//$map['is_del'] = 0;
		//$map['is_show'] = 1;
		//$map['minprice'] = array('neq',0);
		switch($cateName){
			case 'chaozhi':
				$tagName = '超值热卖';
				$this->assign('cateId',1);
				break;
			case 'piaoliu':
				$tagName = '夏日漂流';
				$this->assign('cateId',2);
				break;
			case 'tejia':
				$tagName = '每日特价';
				$this->assign('cateId',3);
				break;
			case 'zizhutuan':
				$tagName = '自组团';
				$this->assign('cateId',4);
				break;
			case 'xibei':
				$tagName = '大西北';
				$this->assign('cateId',5);
				break;
			default:
				$tagName = '超值热卖';
				$this->assign('cateId',1);
				break;
		}
		
		//$goodsList = M('goods')->where($map)->select();
		$goodsList = M('tag as t')
		->field('g.id,g.name,g.subname,g.imgurl,g.days,g.minprice,g.info')
		->join('33_goods_tag as gt on gt.tag_id=t.id')
		->join('33_goods as g on g.id=gt.goods_id')
		->where('t.name="'.$tagName.'" and g.is_del=0 and g.minprice<>0 and g.is_show=1')
		->select();

		foreach($goodsList as $key=>$value){
			switch($value['type_id']){
				case 1:
					$goodsList[$key]['cateType'] = 'zhoubian';
					break;
				case 2:
					$goodsList[$key]['cateType'] = 'guonei';
					break;
				case 3:
					$goodsList[$key]['cateType'] = 'chujing';
					break;
			}
		}
		$this->assign('goodsList',$goodsList);
		$this->display();
	}
	
	public function search(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$keyword = $_REQUEST['SearchKey'];
		$this->assign('keyword',$keyword);
		$this->assign('key');
		$mod = M('goods');
		$map['name|subname|seo_title'] = array('like','%'.$keyword.'%');
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		$map['type_id'] = array('neq',97);
		$list = $mod->where($map)->order('ordid desc,add_time desc')->limit(6)->select();
		$this->assign('title','搜索"'.$keyword.'"的结果');
		$this->assign('list',$list);
		$this->display();
	}
	
	public function getMoreSearch(){
		$mod = M('goods');
		$keyword = $_REQUEST['keyword']?$_REQUEST['keyword']:0;
		$sid = $_REQUEST['sid'];
		
		//参数
		$num = 5;
		$map['name|subname|seo_title'] = array('like','%'.$keyword.'%');
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		$map['type_id'] = array('neq',97);
		$list = $mod->where($map)->order('ordid desc,add_time desc')->limit(6)->select();
		if($list){
			$this->assign('list', $list);
			$data['list'] = $this->fetch('ajax_tour_list');
			$data['sid'] = $sid + $num;
			$this->ajaxReturn($data,'',1);
		}else{
			$this->ajaxReturn('','',0);
		}
	}
	
	public function tachun(){
		$this->display();
	}
	
}