<?php
class ShipCateAction extends BaseAction{
	
	public function index(){
		$cate_mod=M('ShipCate');
		$data=$cate_mod->order('ordid desc,id desc')->where("is_del=0")->select();	
		
		$menu = arrToMenu($data,0); 	
		//var_dump($menu);
		$this->assign('cates_list',$menu);
		$this->display();	
	}
	
	//添加
	public function add(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$cate_mod=M('ShipCate');
		if ($_POST['submit']){		
			$data=$cate_mod->create();
			$cate_mod->where('id='.$data['pid'])->setField('is_end',0);
			$data['floor'] = $cate_mod->where('id='.$data['pid'])->getField('floor')+1;
			$row=$cate_mod->add($data);
			if ($row){
				$this->success('添加成功！',U('ShipCate/index'));
			}else {
				$this->error($cate_mod->getError());
			}
			
		}else {
			if($id){$this->assign('id',$id);};
			$data=$cate_mod->order('ordid,id desc')->where("is_del=0 and floor=0")->select();
			$menu = arrToMenu($data,0);
			$this->assign('cates_list',$menu);
			$this->display();
		}	
	}
	
	//修改
	public function edit(){
		
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';	
		$cate_mod=M('ShipCate');
		
		if ($_POST['submit']){
			$data=$cate_mod->create();
			$data['floor'] = $cate_mod->where('id='.$data['pid'])->getField('floor')+1;
			$save=$cate_mod->where("id=$id")->save($data);
			$this->success('修改成功！',U('ShipCate/index'));
			
		}else {
			if ($id==NULL){
				$this->error('请选择分类！');
			}
			$info=$cate_mod->where('id='.$id)->find();			
			$this->assign('cateInfo',$info);
			$data=$cate_mod->order('ordid,id desc')->where("is_del=0 and floor<>3 and id <>".$id)->select();
			$menu = arrToMenu($data,0); 
			$this->assign('cates_list',$menu);
			$this->display();	
		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$cateMod=M('ShipCate');
		$mod=M("ship");
		foreach ($del_id as $id){			
			$res = $mod->where("cate_id=$id and is_del=0")->select();
			if($res) $this->error('请先删除分类下商品');
			$cateMod->where('id='.$id." and is_del=0")->setField("is_del",1);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$cateMod=M('ShipCate');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$cateMod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$cateMod = M('ShipCate');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$cateMod->where($data)->save($set);
		$val=$cateMod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type],'返回成功',1);
	}
	
	
	
	
	
	
	
	
	
	
	
	
}