<?php
class VisaCateAction extends BaseAction{
	
	public function index(){
		$cate_mod=M('VisaCate');
		$data=$cate_mod->order('ordid desc,id desc')->where("is_del=0")->select();	
		
		$menu = arrToMenu($data,0); 	
		//var_dump($menu);
		$this->assign('cates_list',$menu);
		$this->display();	
	}
	
	//添加
	public function add(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$cate_mod=M('VisaCate');
		if ($_POST['submit']){		
			$data=$cate_mod->create();
			$cate_mod->where('id='.$data['pid'])->setField('is_end',0);
			$data['floor'] = $cate_mod->where('id='.$data['pid'])->getField('floor')+1;
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/visa/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/visa/',$thumb);
				$data['imgurl'] = '/Uploads/visa/s_'. $upload_info['0']['savename'];
			}
			$row=$cate_mod->add($data);
			if ($row){
				$this->success('添加成功！',U('VisaCate/index'));
			}else {
				$this->error($cate_mod->getError());
			}
			
		}else {
			if($id){$this->assign('id',$id);};
			$data=$cate_mod->order('ordid,id desc')->where("is_del=0 and floor<>3")->select();
			$menu = arrToMenu($data,0); 
			$this->assign('cates_list',$menu);
			$this->display();
		}	
	}
	
	//修改
	public function edit(){
		
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';	
		$cate_mod=M('VisaCate');
		
		if ($_POST['submit']){
			$data=$cate_mod->create();
			$data['floor'] = $cate_mod->where('id='.$data['pid'])->getField('floor')+1;
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/visa/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/visa/',$thumb);
				$data['imgurl'] = '/Uploads/visa/s_'. $upload_info['0']['savename'];
			}
			$save=$cate_mod->where("id=$id")->save($data);
			$this->success('修改成功！',U('VisaCate/index'));
			
		}else {
			if ($id==NULL){
				$this->error('请选择分类！');
			}
			$cate_info=$cate_mod->where('id='.$id)->find();			
			$this->assign('cate_info',$cate_info);
			$data=$cate_mod->order('ordid,id desc')->where("is_del=0 and floor<>3 and id <>".$id)->select();
			$menu = arrToMenu($data,0); 
			$this->assign('cates_list',$menu);
			$this->display();	
		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的分类！');
		}
		$del_id = $_POST['id'];
		$cateMod=M('VisaCate');
		$lineMod=M("visa");
		foreach ($del_id as $id){
			$cateMod->where('id='.$id.' and is_del=0')->setField('is_del',1);
			$lineMod->where("cate_id=$id and is_del=0")->setField("is_del",1);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$cate_mod=M('VisaCate');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$cate_mod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$visaCate = M('VisaCate');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$visaCate->where($data)->save($set);
		$val=$visaCate->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type],'返回成功',1);
	}
	
}