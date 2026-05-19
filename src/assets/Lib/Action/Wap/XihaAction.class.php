<?php
class XihaAction extends BaseAction {
	//嘻哈亲子移动首页
    public function index(){
		
		//嘻哈6条线路
		$list = M('qinzi')->where('is_del=0 and is_show=1')->order('ordid desc')->select();
		$this->assign('list',$list);
		
		$this->display();
    }
	
	//嘻哈线路详情页
	public function detail(){
		$id = $_REQUEST['id'];
		$mod = M('qinzi');		
		$info = $mod->where('id='.$id)->find();
		$info['dep'] = $this->getDeparture($info['id'],1);	
		//最低价
		$goodsTimeMod = M('departure_time');
		$depInfo = $goodsTimeMod->where('pid='.$info['id'])->find();
		$info['fPrice'] = $depInfo['price'];
		$info['sPrice'] = $depInfo['child_price'];
		
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

	public function getDeparture($id,$sday){
		$nowtime = strtotime(date(Ymd));
		$exptime = $nowtime + 3600*24*$sday;
		$query ='';
		$mod = M('departure_time');
		$list = $mod->where('pid='.$id.' and departure_time>='.$exptime.' and is_del=0')->order('departure_time')->limit(7)->select();
		//$this->assign('firstDep',date('Y-m-d',$list[0]['departure_time']));
		foreach($list as $key=>$value){
			if($key==0) $query .= date('n/d',$value['departure_time']);
			if($key<6 and $key>0) $query .= '，'.date('n/d',$value['departure_time']);
			if($key==6) $query .= '...';
		}
		return $query;
		//return date('Ymd',$exptime);
	}
}