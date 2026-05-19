<?php
class ArticleCateAction extends BaseAction{
	
	public function index(){
		$cate_mod=M('ArticleCate');
		$data=$cate_mod->order('ordid desc,id desc')->where("is_del=0")->select();
		$menu = arrToMenu($data,0); 	
		$this->assign('cates_list',$menu);
		$this->display();
	}
	
	//添加
	public function add(){
		$cate_mod=M('ArticleCate');
		if ($_POST['submit']){
			
			$data=$cate_mod->create();
			$data['floor'] = $cate_mod->where('id='.$data['pid'])->getField('floor')+1;
			$row=$cate_mod->add($data);
			if ($row){
				$this->success('添加成功！',U('ArticleCate/index'));
			}else {
				$this->error($cate_mod->getError());
			}
			
		}else {
			
			$data=$cate_mod->order('ordid desc,id desc')->where("is_del=0")->select();
			$menu = arrToMenu($data,0); 
			$this->assign('cates_list',$menu);
			$this->display();
		}	
	}
	
	//修改
	public function edit(){
		
		$id=$_GET['id']?$_GET['id']:'';		
		$cate_mod=M('ArticleCate');
		
		if ($_POST['submit']){
			$data=$cate_mod->create();
			$data['floor'] = $cate_mod->where('id='.$data['pid'])->getField('floor')+1;
			$save=$cate_mod->where('id='.$data['id'])->save($data);
			$this->success('修改成功！',U('ArticleCate/index'));
			
		}else {
			if ($id==''){
				$this->error('请选择分类！');
			}
			$cate_info=$cate_mod->where('id='.$id)->find();			
			$this->assign('cate_info',$cate_info);
			$data=$cate_mod->order('ordid,id desc')->where("is_del=0 and id <>".$id)->select();
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
		$cate_mod=M('ArticleCate');
		$article_mod=M("Article");
		foreach ($del_id as $id){
			$cate_mod->where('id='.$id." and is_del=0")->setField('is_del',1);
			$article_mod->where("cate_id=$id and is_del=0")->setField("is_del",1);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$cate_mod=M('ArticleCate');
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
		$article_cate = M('ArticleCate');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$article_cate->where($data)->save($set);
		$val=$article_cate->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	
	
	
	
	
	
	
	
	
	
	
}