<?php
class NavigationAction extends BaseAction{

	public function index(){
		$mod = M('navigation');
		//搜索
		$where = 'is_del=0';
		$data = $mod->where($where)->order('ordid desc,id desc')->select();
		$this->assign('list',$data);
		$this->display();
	}


	//添加
	public function add(){
		$mod=M('navigation');
		if ($_POST['submit']){
			$data=$mod->create();
			$data['add_time'] = time();
			$mod->add($data);
			$this->success('添加成功',U('Navigation/index'));
		}else{
			$this->display();		
		}

	}
	
	//修改
	public function edit(){
		$mod = M('Navigation');
		$article_cate=M('article_cate');	
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		
		if($_POST['submit']){
			if ($_POST['name']==''){
				$this->error('标题不能为空！');
			}
			$data = $mod->create();				
			$mod->where('id='.$id)->save($data);
			$this->success('修改成功',U('Navigation/index'));			
		}else {
			$info = $mod ->where('id='.$id)->find();
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
		$mod = M('navigation');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$mod->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod = M('navigation');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}

	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mod = M('navigation');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
	