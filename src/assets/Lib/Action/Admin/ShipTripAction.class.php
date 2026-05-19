<?php
class ShipTripAction extends BaseAction{

	public function index(){
		$pid = $_REQUEST['pid'];
		$pInfo = M('ship')->where('id='.$pid)->find();
		$this->assign('pInfo',$pInfo);
		$mod = M('ship_trip');
		$list = $mod->where('is_del=0 and pid='.$pid)->order('ordid asc')->select();
		$this->assign('list',$list);
		$this->display();		
	}
	
	public function add(){
		$mod = M('ship_trip');		
		if ($_POST['submit']){
			$data=$mod->create();
			$data['dinner'] = json_encode($data['dinner']);
			$mod->add($data);
			$this->success('添加成功',U('ShipTrip/index',array('pid'=>$data['pid'])));
		}else{
			$this->assign('pid',$_REQUEST['id']);
			$this->display();
		}
	}
	
	public function edit(){
		$id = $_REQUEST['id'];
		$mod = M('ship_trip');
		if ($_POST['submit']){
			$data=$mod->create();
			$data['dinner'] = json_encode($data['dinner']);
			$mod->where('id='.$id)->save($data);
			$this->success('修改成功',U('ShipTrip/index',array('pid'=>$data['pid'])));
		}else{
			$info = $mod->where('id='.$id)->find();
			$info['dinner'] = json_decode($info['dinner']);
			$this->assign('info',$info);
			$this->display();
		}
	}
	
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的行程！');
		}
		$del_id = $_POST['id'];
		$mod = M('ship_trip');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$mod->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	
}

