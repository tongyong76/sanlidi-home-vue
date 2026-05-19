<?php
class ShipAction extends BaseAction {
	public function index(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$id = $_REQUEST['id'];
		$this->assign('type_id',$id);
		if($id == 1) $this->assign('title','日韩航线');
		if($id == 2) $this->assign('title','东南亚航线');
		//$id = $_REQUEST['id'];
		// $cateInfo = M('ship_cate')->where('pinyin="'.$pinyin.'"')->find();
		// $this->assign('cateInfo',$cateInfo);
		$nowTime = time();
		$shipList = M('ship')->where('cate_id='.$id.' and is_del=0 and is_show=1 and minprice<>0 and start_time>'.$nowTime)->order('ordid desc,start_time')->limit(4)->select();
		$this->assign('list',$shipList);
        $this->display();
    }

    public function detail(){
		if(session('empInfo')) $this->assign('empInfo',session('empInfo'));
		$id = $_REQUEST['id'];
		$mod = M('ship');
		$info = $mod->where('id='.$id)->find();
		$this->assign('shipInfo',$info);
		// $cateInfo = $cateMod->where('id='.$info['cate_id'])->find();
		// $this->assign('cateInfo',$cateInfo);
		
		//获取行程
		$trip = M('ship_trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
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
	
	public function getMore(){
		$mod = M('ship');
		$tid = $_REQUEST['tid'];
		$sid = $_REQUEST['sid'];
		$num = 3;
		$nowTime = time();
		$list = $mod->where('cate_id='.$tid.' and is_del=0 and is_show=1 and minprice<>0 and start_time>'.$nowTime)->order('ordid desc,add_time desc')->limit($sid,$num)->select();
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),45);
		}
		if($list){
			$this->assign('list', $list);
			$data['list'] = $this->fetch('ajax_ship_list');
			$data['sid'] = $sid + $num;
			$this->ajaxReturn($data,'',1);
		}else{
			$this->ajaxReturn('','',0);
		}
	}
}