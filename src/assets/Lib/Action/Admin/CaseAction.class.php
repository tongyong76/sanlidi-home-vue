<?php
class CaseAction extends BaseAction{

	public function index(){
		$caseMod=M('case');
		
		$where = "is_del = 0";
		//搜索
		if (isset($_POST['keyword']) && trim($_POST['keyword'])) {
			$where .= " AND title LIKE '%".$_POST['keyword']."%'";
			$this->assign('keyword', $_POST['keyword']);
		}
		if (isset($_POST['time_start']) && trim($_POST['time_start'])) {
			$time_start = strtotime($_POST['time_start']);
			$where .= " AND add_time>='".$time_start."'";
			$this->assign('time_start', $_POST['time_start']);
		}
		if (isset($_POST['time_end']) && trim($_POST['time_end'])) {
			$time_end =strtotime($_POST['time_end']) ;
			$where .= " AND add_time<='".$time_end."'";
			$this->assign('time_end', $_POST['time_end']);
		}
		if (isset($_POST['cate_id']) && intval($_POST['cate_id'])) {
			$where .= " AND cate_id=".$_POST['cate_id'];
			$this->assign('cate_id', $_GET['cate_id']);
		}
	
		//分页
		import("@.ORG.Page");
		$count=$caseMod->where($where)->count();
		$page=new Page($count,10);
		$show=$page->show();
		$caseList=$caseMod->where($where)->order('ordid asc,id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($caseList as $val){
			$caseList[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		
		//分类
		$this->assign('caseList',$caseList);
		$this->assign('page',$show);
		$this->display();
	}


	//添加
	public function add(){
		$caseMod=M('Case');
		if ($_POST['submit']){
			$data=$caseMod->create();
			$data['add_time'] = time();
			//上传图片
			if ($_FILES['pic']['name'] != '') {
				mkdir('./Uploads/case/');
				$thumb=array('width'=>600,'height'=>5000);
				$upload_info = $this->upload('./Uploads/case/',$thumb);
				$data['pic'] = '/Uploads/case/s_'. $upload_info['0']['savename'];
			}
			$caseMod->add($data);
			$this->success('添加成功',U('Case/index'));
		}else{
			$article_cate=M('category');
		
			$result = $article_cate->order('ord desc,id desc')->where("is_del=0 and status=1")->select();
			$this->assign('cate_list',$result);
			$this->display();		
		}

	}
	
	//修改
	public function edit(){
		$caseMod=M('case');
		$article_cate=M('category');	
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		
		if($_POST['submit']){
			if ($_POST['name']==''){
				$this->error('标题不能为空！');
			}
			$data=$caseMod->create();
			//上传图片
			if ($_FILES['pic']['name'] != '') {
				mkdir('./Uploads/case/');
				$thumb=array('width'=>600,'height'=>5000);
				$upload_info = $this->upload('./Uploads/case/',$thumb);
				$data['pic'] = '/Uploads/case/s_'. $upload_info['0']['savename'];
			}			
			if ($id){
				//var_dump($data);
				$caseMod->where('id='.$id)->save($data);
				$this->success('修改成功',U('Case/index'));
			}else {
				$caseMod->add($data);
				$this->success('修改成功',U('Case/index'));
			}
			
		}else {
			$result = $article_cate->order('ord desc,id desc')->where("is_del=0 and status=1")->select();
			$case_info = $caseMod->where('id='.$id)->find();
			$this->assign('case_info',$case_info);
			$this->display();

		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的案例！');
		}
		$del_id = $_POST['id'];
		$caseMod = M('case');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$caseMod->where('id='.$id)->save($data);
			$cid=$caseMod->where("id=$id")->getField("cate_id");
		}
		//$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$caseMod = M('case');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$caseMod->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}

	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$caseMod = M('case');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$caseMod->where($data)->save($set);
		$val=$caseMod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
	