<?php
class SuzhouAction extends BaseAction {
    //深度苏州
	public function index(){
		//苏州热推 type_id=326
		$hotList = M('goods')->where('type_id=326 and is_del=0 and minprice<>0')->order('ordid desc,add_time desc')->limit(5)->select();
		$this->assign('hotList',$hotList);
		
		$this->display();
    }
	
	public function category(){
		$cid = $_REQUEST['cid'];
		//条件们
		if($cid){
			$map['cate_id'] = $cid;
			switch($cid){
				case 327:
					$this->assign('stitle','1日游');
					break;
				case 328:
					$this->assign('stitle','2日游');
					break;
				case 329:
					$this->assign('stitle','3日游');
					break;
			}
			$this->assign('cid',$cid);
		}else{
			$map['type_id'] = 326;
			$this->assign('stitle','全部');
			$this->assign('cid',0);
		}
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		$list = M('goods')->where($map)->order('ordid desc,add_time desc')->limit(6)->select();
		$this->assign('list',$list);
		
		$this->assign('title','深度苏州');
		$this->display();
	}
	
	public function search(){
		$keyword = htmlspecialchars($_REQUEST['keyword']);
		$this->assign('keyword',$keyword);
		if(!$_REQUEST['city']){
			$map['type_id'] = 326;
		}else{
			$jumpUrl = U('Index/search',array('SearchKey'=>$keyword));
			header('Location:'.$jumpUrl);
		}
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		$map['name|subname|info'] = array('like','%'.$keyword.'%');
		$list = M('goods')->where($map)->order('ordid desc,add_time desc')->limit(6)->select();
		$this->assign('list',$list);
		
		$this->assign('title','搜索页');
		$this->assign('stitle','搜索"'.$keyword.'"的结果');
		$this->display();
	}

	public function detail(){
		$id = $_REQUEST['id'];
		//获取seseion
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$mod = M('goods');
		
		$info = $mod->where('id='.$id)->find();
		//$info['dep'] = $this->getDeparture($info['id'],$info['sign_up']);
		$info['dep'] = $this->getDeparture($info['id'],1);
		$info['dep'] = $info['dep']?$info['dep']:0;
		//$info['imgurl'] = image(__ROOT__.$info['imgurl'],320,200,1);
		
		switch($info['type_id']){
			case 1:
				$info['tt'] = "周边游";
				break;
			case 2:
				$info['tt'] = "国内游";
				break;
			case 3:
				$info['tt'] = "出境游";
				break;
			case 326:
				$info['tt'] = "深度苏州";
				break;
		}
		$this->assign('info',$info);
		
		//获取行程
		$trip = M('trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
		foreach($trip as $key=>$value){
			$trip[$key]['dinner'] = json_decode($value['dinner']);
			$scene = explode(',',$value['scene']);
			foreach($scene as $skey=>$svalue){
				if($svalue && $skey<3){
					$res = M('scenic')->where('name="'.$svalue.'"')->find();
					$trip[$key]['scenic'][$skey] = $res;
				}
			}
			//获取景点
			//$trip[$key]['scene'] = explode(',',$value['scene']);
		}
		$this->assign('trip',$trip);
		
		$this->display();
	}
	
	/**
     * 获取最近行程
     * @access public
     * @param integer $id 线路id
     * @return query
     */
	public function getDeparture($id,$sday){
		$nowtime = strtotime(date(Ymd));
		$exptime = $nowtime + 3600*24*$sday;
		$query ='';
		$mod = M('departure_time');
		$list = $mod->where('pid='.$id.' and departure_time>='.$exptime.' and is_del=0')->order('departure_time')->limit(7)->select();
		//$this->assign('firstDep',date('Y-m-d',$list[0]['departure_time']));
		foreach($list as $key=>$value){
			if($key==0) $query .= date('n/d',$value['departure_time']);
			if($key<5 and $key>0) $query .= '，'.date('n/d',$value['departure_time']);
			if($key==5) $query .= '...';
		}
		return $query;
		//return date('Ymd',$exptime);
	}
	
	//下拉获取更多行程
	public function getMore(){
		$mod = M('goods');
		$cid = $_REQUEST['cid']?$_REQUEST['cid']:0;
		$sid = $_REQUEST['sid'];
		$num = 3;
		
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		
		if($cid){
			$map['cate_id'] = $cid;
		}else{
			$map['type_id'] = 326;
			$keyword = htmlspecialchars($_REQUEST['keyword']);
			if($keyword){
				$map['name|subname|info'] = array('like','%'.$keyword.'%');
			}
		}
		$list = $mod->where($map)->order('ordid desc,add_time desc')->limit($sid,$num)->select();
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
}