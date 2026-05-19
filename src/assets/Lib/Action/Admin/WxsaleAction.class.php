<?php
class WxsaleAction extends BaseAction{

	public function index(){
		$mod = M('sale_goods as sg');
		$data = $mod->field('*,sg.id as id')->join('33_goods as g on g.id=sg.gid')->order('sg.id desc')->where("sg.is_del=0")->select();
		$this->assign('list',$data);
		$this->display();
	}
	
	//添加
	public function add(){
		$mod = M('sale_goods');
		if ($_POST['submit']){		
			$data = $mod->create();
			$data['sale_left'] = $data['sale_num'];
			$data['sale_start'] = strtotime($data['sale_start']);
			$data['sale_end'] = strtotime($data['sale_end']);
			$data['dep_time'] = strtotime($data['dep_time']);
			$row = $mod->add($data);
			if ($row){
				$this->success('添加成功！',U('Wxsale/index'));
			}else {
				$this->error($mod->getError());
			}
			
		}else {
			$this->display();
		}	
	}
	
	//修改
	public function edit(){
		
		$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';	
		$mod = M('sale_goods');
		
		if ($_POST['submit']){
			$data=$mod->create();
			$data['sale_start'] = strtotime($data['sale_start']);
			$data['sale_end'] = strtotime($data['sale_end']);
			$data['dep_time'] = strtotime($data['dep_time']);
			$save=$mod->where("id=$id")->save($data);
			$this->success('修改成功！',U('Wxsale/index'));
			
		}else {
			$info=$mod->where('id='.$id)->find();			
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
		$mod = M('sale_goods');
		foreach ($del_id as $id){
			$mod->where('id='.$id." and is_del=0")->setField('is_del',1);
		}
		$this->success('删除成功！');
	}

}