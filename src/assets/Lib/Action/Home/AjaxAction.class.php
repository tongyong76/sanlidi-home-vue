<?php
class AjaxAction extends BaseAction {
	//Admin-Ad-Search
    public function adSearch(){
		$field = $_REQUEST['field'];
		$val = $_REQUEST['val'];
		$mod = M('ad_search');
		$mod->where('id=1')->setField($field,$val);
		$this->ajaxReturn('','',1);
	}
	
	 public function word(){
		$field = $_REQUEST['field'];
		$val = $_REQUEST['val'];
		$id = $_REQUEST['id'];
		$mod = M('ad');
		$mod->where('id='.$id)->setField($field,$val);
		$this->ajaxReturn('','',1);
	}
	
	//搜索筛选框
	public function searchNav(){
		$keyword = trim($_REQUEST['keyword']);
		$days = $_REQUEST['days'];
		$price = $_REQUEST['price'];
		
		//获取搜索条件
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['type_id'] = array('neq',97);
		
		if($days) $map['days'] = $days;
		$map['name|subname'] = array('like','%'.$keyword.'%');
		switch($price){
			case 1:
				$map['minprice'] = array('between','1,1000');
				break;
			case 2:
				$map['minprice'] = array('between','1000,2000');
				break;
			case 3:
				$map['minprice'] = array('between','2000,5000');
				break;
			case 4:
				$map['minprice'] = array('egt',5000);
				break;
			default:
				break;
		}
		$mod = M('goods');
		//ajax分页
		import("@.ORG.Page");
		$list = $mod->where($map)->select();
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			switch($value['type_id']){
				case 1:
					$list[$key]['mtype'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['mtype'] = 'guonei';
					break;
				case 3:
					$list[$key]['mtype'] = 'chujing';
					break;
			}
		}
		$parameter = 'keyword='.$keyword.'&days='.$days.'&price='.$price;
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			'parameter'=>$parameter,//url分页后继续带的参数
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Index:ajaxlist',//ajax更新模板
		);
		echo $html = $this->page($param);
		
	}
	
	//搜索筛选框
	public function tourIndex(){
		$pinyin = $_REQUEST['pinyin'];
		$mod = M('goods');
		$cateMod = M('goods_cate');
		
		$cateInfo = $cateMod->where('pinyin="'.$pinyin.'" and is_auto=0 and is_del=0')->find();
		$cid = $cateInfo['id'];
		
		$days = $_REQUEST['days'];
		$price = $_REQUEST['price'];
		
		$floor = $cateInfo['floor'];
		if($floor == 2){
			$typeId = $cateMod->where('id="'.$cid.'"')->getfield('pid');
			$cateList = $cateMod->where('pid='.$cid.' and is_del=0')->getfield('id',true);
			if($cateList){
				$catemap['cate_id'] = array('in',$cateList);
				$goods_ids = M('goods_cate_rela')->where($catemap)->group('goods_id')->getfield('goods_id',true);
				$map['id'] = array('in',$goods_ids);
			}else{
				$catemap['cate_id'] = $cid;
				$goods_ids = M('goods_cate_rela')->where($catemap)->getfield('goods_id',true);
				$map['id'] = array('in',$goods_ids);
			}	
		}
		if($floor == 3){
			$typeId = $cateMod->where('id="'.$cateInfo['pid'].'"')->getfield('pid');
			$catemap['cate_id'] = $cateInfo['id'];
			$goods_ids = M('goods_cate_rela')->where($catemap)->getfield('goods_id',true);
			$map['id'] = array('in',$goods_ids);
		}
		//获取搜索条件
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		
		if($days) $map['days'] = $days;
		if($typeId == 1){
			$this->assign('cateType','zhoubian');
			switch($price){
				case 1:
					$map['minprice'] = array('between','1,100');
					break;
				case 2:
					$map['minprice'] = array('between','100,300');
					break;
				case 3:
					$map['minprice'] = array('between','300,500');
					break;
				case 4:
					$map['minprice'] = array('egt',500);
					break;
				default:
					break;
			}
		}
		if($typeId == 2){
			$this->assign('cateType','guonei');
			switch($price){
				case 1:
					$map['minprice'] = array('between','1,1000');
					break;
				case 2:
					$map['minprice'] = array('between','1000,2000');
					break;
				case 3:
					$map['minprice'] = array('between','2000,5000');
					break;
				case 4:
					$map['minprice'] = array('egt',5000);
					break;
				default:
					break;
			}			
		}
		
		if($typeId == 3){
			$this->assign('cateType','chujing');
			switch($price){
				case 1:
					$map['minprice'] = array('between','1,1000');
					break;
				case 2:
					$map['minprice'] = array('between','1000,2000');
					break;
				case 3:
					$map['minprice'] = array('between','2000,5000');
					break;
				case 4:
					$map['minprice'] = array('egt',5000);
					break;
				default:
					break;
			}			
		}
		
		//ajax分页
		import("@.ORG.Page");
		$list = $mod->where($map)->select();
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
			$list[$key]['is_zzt'] = stripos($value['switch'],'1');
		}
		$parameter = 'pinyin='.$pinyin.'&days='.$days.'&price='.$price;
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			'parameter'=>$parameter,//url分页后继续带的参数
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Tour:ajaxlist',//ajax更新模板
		);
		echo $html = $this->page($param);
	}
	
	//自由行AJAX
	public function free(){
		$mod = M('goods as g');
		$days = $_REQUEST['days'];
		$cate = $_REQUEST['cate'];
				
		//通用条件
		if($days){
			$map['g.days'] = $days;
		}
		if($cate){
			$map['g.cate_id'] = $cate;
		}
		// if($starttime){
			// $timeArr = getMonthTimeStamp($starttime,1);
			// $map['g.starttime'] = array('between',$timeArr['start'],$timeArr['end']);
		// }
		if($_REQUEST['type_id']){
			$map['type_id'] = trim($_REQUEST['type_id']);
		}
		$map['g.is_del'] = 0;
		$map['g.is_show'] = 1;
		$map['g.minprice'] = array('neq',0);
		$map['g.is_zyx'] = 1;
		
		import("@.ORG.Page");
		$list = $mod->where($map)->order('g.ordid desc,g.add_time desc')->select();
		$count_num = $mod->where($map)->order('g.ordid desc,g.add_time desc')->count();
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
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
			}
		}
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Tour:ajaxfreelist',//ajax更新模板
		);
		echo $html = $this->page($param);
	}
	
	//搜索筛选框
	public function shipIndex(){
		//$cid = trim($_REQUEST['cid']);
		//限定月份开始
		$s = $_REQUEST['s'];
		$nowYear = date('Y',$s);//当前月份
		$nowMonth = date('m',$s);//当前月份
		if($nowMonth == 12){
			$endTime = strtotime(($nowYear+1).'-01');
		}else{;
			$endTime = strtotime($nowYear.'-'.($nowMonth+1));
		}
		$startTime = $s;
		//限定月份结束
		
		$days = $_REQUEST['days'];
		$price = $_REQUEST['price'];
		
		$mod = M('ship');
		$cateMod = M('ship_cate');
		$cateInfo = $cateMod->where('id='.$cid)->find();		
		
		//获取搜索条件
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		$nowTime = time();
		//$map['start_time'] = array('gt',$nowTime);
		$map['start_time'] = array('between',array($startTime,$endTime));
		if($cid) $map['cate_id'] = $cid;
		
		if($days) $map['days'] = $days;
		switch($price){
			case 1:
				$map['minprice'] = array('between','1,2000');
				break;
			case 2:
				$map['minprice'] = array('between','2000,5000');
				break;
			case 3:
				$map['minprice'] = array('egt',5000);
				break;
			default:
				break;
		}
		
		//ajax分页
		import("@.ORG.Page");
		$list = $mod->where($map)->select();
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
		}
		$parameter = 'cid='.$cid.'&days='.$days.'&price='.$price;
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			'parameter'=>$parameter,//url分页后继续带的参数
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Ship:ajaxlist',//ajax更新模板
		);
		echo $html = $this->page($param);		
	}
	
	public function page($param) {
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