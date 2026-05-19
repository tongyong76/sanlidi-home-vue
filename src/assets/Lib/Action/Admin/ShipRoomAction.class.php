<?php
class ShipRoomAction extends BaseAction{
	
	//添加行程列表
	public function index(){
		$pid = $_REQUEST['sid'];
		$pInfo = M('ship')->where('id='.$pid)->find();
		$this->assign('pInfo',$pInfo);
		$list = M('ship_room')->where('ship_id='.$pid.' and is_del=0')->order('ordid desc')->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		$type = $_REQUEST['type'];		
		$this->assign('type',$type);

		$mod = M('ship_departure');		
		if ($_POST['submit']){
			
		}else{
			$ship_id = $_REQUEST['id'];
			$this->assign('ship_id',$ship_id);
			$roomList = M('ship_room')->where('ship_id='.$ship_id.' and is_del=0 and status=1')->select();
			$this->assign('roomList',$roomList);
			$this->display();
		}
	}
	
	public function getOneMore(){
		$ship_id = $_REQUEST['ship_id'];
		$data['ship_id'] = $ship_id;
		$data['is_del'] = 0;
		$data['is_show'] = 1;
		$newId = M('ship_room')->add($data);
		$this->ajaxReturn($newId,0,1);
	}
	
	public function changeData(){
		$field = $_REQUEST['field'];
		$val = $_REQUEST['val'];
		$id = $_REQUEST['id'];
		$mod = M('ship_room');
		$mod->where('id='.$id)->setField($field,$val);
		$this->ajaxReturn($mod->getlastsql(),'',1);
	}
	
	public function edit(){
		
		$mod = M('ship_departure');
		if ($_POST['submit']){
			$data=$mod->create();
			$data['ship_departure'] = strtotime($data['ship_departure']);
			$mod->where('id='.$data['id'])->save($data);
			//echo $mod->getlastsql();
			$this->success('添加成功',U('ShipDeparture/index',array('pid'=>$data['pid'])));
		}else{		
			$id = $_REQUEST['id'];
			$info = $mod->where('id='.$id)->find();
			$info['ship_departure'] = date('Y-m-d', $info['ship_departure']); 
			$this->assign('info',$info);
			$this->display();		
		}		
	}

	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$mod=M('ship_room');
		foreach ($del_id as $id){
			$mod->where('id='.$id.' and is_del=0')->setField('is_del',1);
		}
		//echo $mod->getlastsql(); 
		$this->success('删除成功！');
	}
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod=M('ship_room');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mod = M('shipRoom');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
}
	