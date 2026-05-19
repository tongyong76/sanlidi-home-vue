<?php 
class BrandAction extends BaseAction 
{
	public $mod;
	public $map;
	public function _initialize(){
		header("Content-type: text/html; charset=utf-8"); 
		$this->map['is_del'] = 0;
		$this->map['is_show'] = 1;
		$this->map['minprice'] = array('neq',0);
		$this->mod = M('goods');
		
		//载入导航
		$navMod = M('navigation');
		$navList = $navMod->where('is_del=0')->order('ordid desc')->select();
		$this->assign('navList',$navList);
		
		//顶部分类调用
		$lineCateList = $this->genTree5("goods_cate");
		$this->assign("lineCateList",$lineCateList);
		//$ress = sort($lineCateList[1]['son']);
		$arrr = my_mul_sort($lineCateList[2]['son'],'ordid');
		$this->assign("zbCateArr",my_mul_sort($lineCateList[1]['son'],'ordid'));
		$this->assign("gnCateArr",my_mul_sort($lineCateList[2]['son'],'ordid'));
		$this->assign("cjCateArr",my_mul_sort($lineCateList[3]['son'],'ordid'));
		$this->assign("tdCateArr",my_mul_sort($lineCateList[97]['son'],'ordid'));
		
		//搜索推广
		$adSearch = M('ad_search')->where('id=1')->find();
		$this->assign('search',$adSearch);	
		
		//广告调用
		$adList = $this->getAd();
		$this->assign('ad',$adList);
	} 
	
	//自组团
    public function zizhutuan(){
		$this->map['brand_id|switch'] = array(array('in',array(1,2,3,4)),array('like','%1%'),'_multi'=>true);
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('goods')->where($this->map)->order('ordid desc,add_time desc')->select();
		$count_num = M('goods')->where($this->map)->order('ordid desc,add_time desc')->count();
		$zzt_num = 0;
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);			
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 2) or ($value['brand_id'] == 3) or ($value['brand_id'] == 1)){
				$list[$key]['is_zzt'] = 1;
				$zzt_num++;
			}
			switch($value['type_id']){
				case 1:
					$list[$key]['cateType'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['cateType'] = 'guonei';
					break;
				case 3:
					$list[$key]['cateType'] = 'chujing';
					break;
			}
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '自组团';
		$this->assign('link',$link);
		$this->display('index');	
	}

	//团游
	public function tuanyou(){
		$this->map['brand_id'] = 1;
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('goods')->where($this->map)->order('ordid desc,add_time desc')->select();
		$count_num = M('goods')->where($this->map)->order('ordid desc,add_time desc')->count();
		$zzt_num = 0;
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 1)){
				$list[$key]['is_zzt'] = 1;
				$zzt_num++;
			}
			switch($value['type_id']){
				case 1:
					$list[$key]['cateType'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['cateType'] = 'guonei';
					break;
				case 3:
					$list[$key]['cateType'] = 'chujing';
					break;
			}
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '团游';
		$this->assign('link',$link);
		$this->display('index');	
	}
	
	//奢享
	public function shexiang(){
		$this->map['series_id'] = array('like','%4%');
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('goods')->where($this->map)->order('ordid desc,add_time desc')->select();
		$count_num = M('goods')->where($this->map)->order('ordid desc,add_time desc')->count();
		$zzt_num = 0;
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 1)){
				$list[$key]['is_zzt'] = 1;
				$zzt_num++;
			}
			switch($value['type_id']){
				case 1:
					$list[$key]['cateType'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['cateType'] = 'guonei';
					break;
				case 3:
					$list[$key]['cateType'] = 'chujing';
					break;
			}
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '奢享';
		$this->assign('link',$link);
		$this->display('index');		
	}
	
	//尊享
	public function zunxiang(){
		$this->map['series_id'] = array('like','%3%');
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('goods')->where($this->map)->order('ordid desc,add_time desc')->select();
		$count_num = M('goods')->where($this->map)->order('ordid desc,add_time desc')->count();
		$zzt_num = 0;
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 1)){
				$list[$key]['is_zzt'] = 1;
				$zzt_num++;
			}
			switch($value['type_id']){
				case 1:
					$list[$key]['cateType'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['cateType'] = 'guonei';
					break;
				case 3:
					$list[$key]['cateType'] = 'chujing';
					break;
			}
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '尊享';
		$this->assign('link',$link);
		$this->display('index');		
	}
	
	//畅享
	public function changxiang(){
		$this->map['series_id'] = array('like','%1%');
		//ajax分页
		import("@.ORG.Page");
		$list = M('goods')->where($this->map)->order('ordid desc,add_time desc')->select();
		$count_num = M('goods')->where($this->map)->order('ordid desc,add_time desc')->count();
		$zzt_num = 0;
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 1)){
				$list[$key]['is_zzt'] = 1;
				$zzt_num++;
			}
			switch($value['type_id']){
				case 1:
					$list[$key]['cateType'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['cateType'] = 'guonei';
					break;
				case 3:
					$list[$key]['cateType'] = 'chujing';
					break;
			}
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '畅享';
		$this->assign('link',$link);
		$this->display('index');		
	}
	
	//嘻哈亲子
	public function xihaqinzi(){
		//$this->map['brand_id'] = 4;
		//单独条件
		$qxmap['is_del'] = 0;
		$qxmap['is_show'] = 1;
		$qxmap['minprice'] = array('neq',0);
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('qinzi')->where($qxmap)->order('ordid desc,add_time desc')->select();
		$count_num = M('qinzi')->where($qxmap)->order('ordid desc,add_time desc')->count();
		$zzt_num = 0;
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			//最近行程
			$nowtime = strtotime(date(Ymd));
			$exptime = $nowtime + 3600*24*1;
			$length = 6;
			$query ='';
			$mod = M('qinzi_departure');
			$deplist = $mod->where('pid='.$value['id'].' and departure_time>='.$exptime.' and is_del=0')->order('departure_time')->limit(7)->select();
			foreach($deplist as $dkey=>$dvalue){
				if($dkey==0) $query .= date('n/d',$dvalue['departure_time']);
				if($dkey<$length and $dkey>0) $query .= '，'.date('n/d',$dvalue['departure_time']);
				if($dkey==$length ) $query .= '...';
			}
			$list[$key]['dep'] = $query;
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:xhajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '嘻哈亲子';
		$this->assign('link',$link);
		$this->display();		
	}
	
	//沪上邮轮
	public function shuaicang(){
		//$this->map['is_hot'] = 1;
		$this->map['start_time'] = array('gt',time());
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('ship')->where($this->map)->order('ordid desc,add_time desc')->select();
		$count_num = M('ship')->where($this->map)->order('ordid desc,add_time desc')->count();
		$zzt_num = 0;
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			$list[$key]['cateType'] = 'youlun';
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '沪上邮轮';
		$this->assign('link',$link);
		$this->display('index');	
	}
	
	//专享
	public function zhuanxiang(){
		$this->map['series_id'] = array('like','%2%');
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('goods')->where($this->map)->order('ordid desc,add_time desc')->select();
		$count_num = M('goods')->where($this->map)->order('ordid desc,add_time desc')->count();
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 1)){
				$list[$key]['is_zzt'] = 1;
				$zzt_num++;
			}
			switch($value['type_id']){
				case 1:
					$list[$key]['cateType'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['cateType'] = 'guonei';
					break;
				case 3:
					$list[$key]['cateType'] = 'chujing';
					break;
			}
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '专享';
		$this->assign('link',$link);
		$this->display('index');	
	}
	
	//周末地图
	public function zhoumoditu(){
		$this->map['brand_id'] = 3;
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('goods')->where($this->map)->order('ordid desc,add_time desc')->select();
		$count_num = M('goods')->where($this->map)->order('ordid desc,add_time desc')->count();
		$zzt_num = 0;
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 1)){
				$list[$key]['is_zzt'] = 1;
				$zzt_num++;
			}
			switch($value['type_id']){
				case 1:
					$list[$key]['cateType'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['cateType'] = 'guonei';
					break;
				case 3:
					$list[$key]['cateType'] = 'chujing';
					break;
			}
		}
		$this->assign('zzt_num',$zzt_num);
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Brand:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		$link['name'] = '周末地图';
		$this->assign('link',$link);
		$this->display('index');		
	}
	
	public function page($param){
		extract($param);
		import("@.ORG.Page");
		//总记录数
		$flag = is_string($result);
		$listvar = $listvar ? $listvar : 'list';
		$listRows = $listRows? $listRows : 10;
		if ($flag)
			$totalRows = M()->table($result . ' a')->count();
		else
			$totalRows = ($result) ? count($result) : 1;
		//创建分页对象
		if ($target && $pagesId)
			$p = new Page($totalRows, $listRows, $parameter, $url,$target, $pagesId);
		else
			$p = new Page($totalRows, $listRows, $parameter,$url);
		//抽取数据
		if ($flag) {
			$result .= " LIMIT {$p->firstRow},{$p->listRows}";
			$voList = M()->query($result);
		} else {
			$voList = array_slice($result, $p->firstRow, $p->listRows);
		}
		$pages = C('PAGE');//要ajax分页配置PAGE中必须theme带%ajax%，其他字符串替换统一在配置文件中设置，
		//可以使用该方法前用C临时改变配置
		foreach ($pages as $key => $value) {
			$p->setConfig($key, $value); // 'theme'=>'%upPage% %linkPage% %downPage% %ajax%'; 要带 %ajax%
		}
		//分页显示
		$page = $p->show();
		//模板赋值
		$this->assign($listvar, $voList);
		$this->assign("page", $page);
		if ($this->isAjax()) {//判断ajax请求
			layout(false);
			$template = (!$template) ? 'ajaxlist' : $template;
			exit($this->fetch($template));
		}
		return $voList;
	}
}
