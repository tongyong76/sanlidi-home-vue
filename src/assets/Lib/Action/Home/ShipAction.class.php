<?php 
class ShipAction extends BaseAction 
{
	//列表页 按月份
    public function index(){
		$s = $_REQUEST['s'];
		//$s = 1438358400 ;
		$this->assign('s',$s);
		$nowYear = date('Y',$s);//当前月份
		$nowMonth = date('m',$s);//当前月份
		if($nowMonth == 12){
			$endTime = strtotime(($nowYear+1).'-01');
		}else{;
			$endTime = strtotime($nowYear.'-'.($nowMonth+1));
		}
		$startTime = $s;
		$mod = M('ship');
		$cateMod = M('ship_cate');
		
		//拼音分类
		// if($pinyin){
			// $info = $cateMod->where('pinyin="'.$pinyin.'"')->find();
			// $cid = $info['id'];
		// }

		//echo $cid;
		//$this->assign('cid',$cid);
		
		//获取航线
		// $cateList = $cateMod->where('floor=1 and is_del=0')->select();
		// $this->assign('cateList',$cateList);
		//获取月份
		$nowTime = time();
		$q = M('ship')->where('is_show=1 and is_del=0 and minprice <>0 and start_time > '.$nowTime)->order('start_time')->find();
		$sYear = date('Y',$q['start_time']);//当前月份
		$sMonth = date('m',$q['start_time']);//当前月份
		$sTime = strtotime($sYear.'-'.$sMonth);
		for($i=0;$i<12;$i++){
			if(($sMonth+$i)>12){
				$newYear = $sYear + 1;
				$newMonth = $sMonth+$i-12;
			}else{
				$newYear = $sYear;
				$newMonth = $sMonth + $i;
			}
			$key = strtotime($newYear.'-'.$newMonth);
			$dStartTime = $key;
			if($newMonth + 1 >12){
				$newYear = $newYear + 1;
				$newMonth = 1;
			}
			$dEndTime = strtotime($newYear.'-'.($newMonth+1));
			$dateList[$key]['son'] = M('ship')->where('is_show=1 and is_del=0 and minprice <>0 and start_time >= '.$dStartTime.' and start_time < '.$dEndTime)->order('ordid desc,start_time')->count();
		}
		$this->assign('dateList',$dateList);
			
		$where = 'is_del=0 and is_show=1 and minprice<>0 and start_time >= '.$startTime.' and start_time <'.$endTime;
		
		//筛选条件
		//if (isset($_REQUEST['cid']) && trim($_REQUEST['cid'])) {
		if($cid){
			$where .= " AND cate_id = $cid ";
			$this->assign('cid', $cid);
		}
		if (isset($_REQUEST['days']) && trim($_REQUEST['days'])) {
			$days = trim($_REQUEST['days']);
			//$where .= " AND days = ".$days;
			$this->assign('days', $days);
		}
		if (isset($_REQUEST['minprice']) && trim($_REQUEST['minprice'])) {
			$minprice = trim($_REQUEST['minprice']);
			$where .= " AND minprice>='".$minprice."'";
			$this->assign('minprice', $minprice);
		}
		if (isset($_REQUEST['maxprice']) && trim($_REQUEST['maxprice'])) {
			$maxprice = trim($_REQUEST['maxprice']);
			$where .= " AND minprice<'".$maxprice."'";
			$this->assign('maxprice', $maxprice);
		}
		
		//获取日期
		$dayList = $mod->field('days')->where($where)->group('days')->select();
		$this->assign('dayList',$dayList);
		

		//ajax分页
		import("@.ORG.Page");
		$list = $mod->where($where)->order('ordid desc,start_time')->select();
		$count_num = $mod->where($where)->order('ordid desc,start_time')->count();
		$this->assign('count_num',$count_num);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			$list[$key]['bimg'] = M('ship_boat')->where('id='.$value['boat_id'])->getfield('imgurl');
			$list[$key]['binfo'] = M('ship_boat')->where('id='.$value['boat_id'])->getfield('info');
		}
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Ship:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		
		
		$this->display();
	}	
	
	public function boat(){
		
		//邮轮信息
		$id = $_REQUEST['id']?$_REQUEST['id']:1;
		$boatInfo = M('ship_boat as b')->join('33_ship_company as c on b.company_id=c.id')->field('b.*,c.imgurl as cimg,c.name as cname,c.ename as cename')->where('b.id='.$id)->find();
		$this->assign('boatInfo',$boatInfo);
		
		//邮轮全部行程
		$map['s.is_del'] = 0;
		$map['s.is_show'] = 1;
		$map['s.boat_id'] = $id;
		//ajax分页
		import("@.ORG.Page");
		$list = M('ship as s')->field('s.*,b.imgurl as bimg,b.info as binfo')->join('33_ship_boat as b on b.id=s.boat_id')->where($map)->order('ordid desc,start_time')->select();
		$count_num = M('ship as s')->join('33_ship_boat as b on b.id=s.boat_id')->where($map)->count();
		$this->assign('count_num',$count_num);
			$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Ship:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		
		//相关推荐
		$nowtime = time();
		$salelist = M('ship as s')->field('s.*,b.imgurl as bimg')->join('33_ship_boat as b on b.id=s.boat_id')->where('s.is_show=1 and s.is_hot=1 and s.is_del=0 and s.minprice <>0 and s.start_time > '.$nowtime)->order('s.start_time')->limit(4)->select();
		$this->assign('salelist',$salelist);
		
		$this->display();
	}
	
	public function detail(){
		$id = $_REQUEST['id'];
		$mod = M('ship');
			
		$info = $mod->where('id='.$id)->find();
		if(!$info){
			$this->error('非法操作！','/');
		}
		$info['dep'] = $this->getShipDeparture($info['id'],$info['sign_up']);
		$info['service'] = json_decode($info['service']);
		$info['seo_desc'] = msubstr(strip_tags($info['info']),45);
		$info['end_time'] = $info['start_time'] + 3600*24*($info['days']-1);
		
		//航线信息 ，暂时取消
		// $cateInfo = M('ship_cate')->where('id='.$info['cate_id'])->find();
		// $floor = $cateInfo['floor'];
		// $info['pinyin'] = $cateInfo['pinyin'];
		$this->assign('info',$info);
		
		//船体信息
		$boatInfo = M('ship_boat as b')
			->join('33_ship_company as c on c.id=b.company_id')
			->field('*,b.id as id,b.name as name,c.name as cname,b.imgurl as imgurl')
			->where('b.id='.$info['boat_id'])
			->find();
		$this->assign('boatInfo',$boatInfo);
		
		//舱位信息
		$roomList  = M('ship_room')
			->where('ship_id='.$info['id'].' and status=1 and is_del=0')
			->order('ordid desc')
			->select();
		$this->assign('roomList',$roomList);
		
		//获取行程
		$trip = M('ship_trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
		foreach($trip as $key=>$value){
			$trip[$key]['dinner'] = json_decode($value['dinner']);
			$scene = explode(',',$value['scene']);
			foreach($scene as $skey=>$svalue){
				if($svalue && $skey<3){
					$res = M('scenic')->where('name="'.$svalue.'" and is_del=0')->find();
					$trip[$key]['scenic'][$skey] = $res;
				}
			}
		}
		$this->assign('trip',$trip);
		//var_dump($trip);
		
		//相关线路 大分类下其他线路or同关键词线路or同分类
		$rList = $mod->where('cate_id='.$info['cate_id'].' and is_del=0')->order('ordid desc,add_time desc')->limit(4)->select();
		$this->assign('relative',$rList);
		
		//生成二维码
		$curl =  "http://".$_SERVER ['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$ewmurl = $this->erweima($curl,$info['sn']);
		$this->assign('ewmurl',$ewmurl);
		
		$this->display();
	}
	
	public function detail2(){
		$id = $_REQUEST['id'];
		$mod = M('ship');
			
		$info = $mod->where('id='.$id)->find();
		if(!$info){
			$this->error('非法操作！','/');
		}
		$info['dep'] = $this->getShipDeparture($info['id'],$info['sign_up']);
		$info['service'] = json_decode($info['service']);
		$info['seo_desc'] = msubstr(strip_tags($info['info']),45);
		$info['end_time'] = $info['start_time'] + 3600*24*$info['days'];
		
		//航线信息 ，暂时取消
		// $cateInfo = M('ship_cate')->where('id='.$info['cate_id'])->find();
		// $floor = $cateInfo['floor'];
		// $info['pinyin'] = $cateInfo['pinyin'];
		$this->assign('info',$info);
		
		//船体信息
		$boatInfo = M('ship_boat as b')
			->join('33_ship_company as c on c.id=b.company_id')
			->field('*,b.name as name,c.name as cname,b.imgurl as imgurl')
			->where('b.id='.$info['boat_id'])
			->find();
		$this->assign('boatInfo',$boatInfo);
		
		//舱位信息
		$roomList  = M('ship_room')
			->where('ship_id='.$info['id'].' and status=1 and is_del=0')
			->order('ordid desc')
			->select();
		$this->assign('roomList',$roomList);
		
		//获取行程
		$trip = M('ship_trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
		foreach($trip as $key=>$value){
			$trip[$key]['dinner'] = json_decode($value['dinner']);
			$scene = explode(',',$value['scene']);
			foreach($scene as $skey=>$svalue){
				if($svalue && $skey<3){
					$res = M('scenic')->where('name="'.$svalue.'" and is_del=0')->find();
					$trip[$key]['scenic'][$skey] = $res;
				}
			}
		}
		$this->assign('trip',$trip);
		//var_dump($trip);
		
		//相关线路 大分类下其他线路or同关键词线路or同分类
		$rList = $mod->where('cate_id='.$info['cate_id'].' and is_del=0')->order('ordid desc,add_time desc')->limit(4)->select();
		$this->assign('relative',$rList);
		
		//生成二维码
		$curl =  "http://".$_SERVER ['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$ewmurl = $this->erweima($curl,$info['sn']);
		$this->assign('ewmurl',$ewmurl);
		
		$this->display();
	}
	
	public function getShipDeparture($id,$sday){
		$nowtime = strtotime(date(Ymd));
		$exptime = $nowtime + 3600*24*$sday;
		$query ='';
		$mod = M('ship_departure');
		$list = $mod->where('pid='.$id.' and departure_time>'.$exptime.' and is_del=0')->order('departure_time')->limit(3)->select();
		foreach($list as $key=>$value){
			if($key==0) $query .= date('Y-m-d',$value['departure_time']);
			if($key==1) $query .= '/'.date('Y-m-d',$value['departure_time']);
			if($key==2) $query .= '...';
		}
		return $query;
		//return date('Ymd',$exptime);
	}
	
	public function getDate(){
		$id = $_REQUEST['id'];
		$month = $_REQUEST['month'];
		$year = $_REQUEST['year'];
		$starttime = strtotime($year.'-'.$month);
		$endtime = strtotime($year.'-'.($month+1));
		if(time() > $starttime){
			$starttime = time();
		}
		$map['pid'] = $id;
		$map['departure_time'] = array('between',array($starttime,$endtime));		
		$list = M('departure_time')->where($map)->select();
		foreach($list as $key=>$value){
			$list2[date('d',$value['departure_time'])] = $value;
		}
		$json = $list?json_encode($list2):0;
		$this->ajaxReturn($json,'ok',1);
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
	
	public function order(){
		$this->display();
	}
}
