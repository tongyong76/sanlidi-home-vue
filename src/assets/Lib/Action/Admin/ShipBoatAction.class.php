<?php
class ShipBoatAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		$mod=M('ShipBoat');
		//搜索
		$where = 'is_del=0';
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND name LIKE '%".$_REQUEST['keyword']."%'";
			$this->assign('keyword', $_REQUEST['keyword']);
		}
		
		//排序功能
		if ($sort=='desc'){
			$sort='asc';
		}elseif ($sort=='asc'){
			$sort='desc';
		}
		$this->assign('order',$order);
		$this->assign('sort', $sort);
		$order_str = 'ordid desc'; //默认排序
		if ($order) {
			$order_str = 'ordid desc,'.$order . ' ' . $sort;
		}

		//分页
		//import("@.ORG.Page");
		import("ORG.Util.Page");
		$count=$mod->where($where)->count();
		$page=new Page($count,10);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		$show=$page->show();
		$data=$mod->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();
		
		$this->assign('boat',$data);
		$this->assign('page',$show);
		$this->display();
	}
	
	
	//添加商品
	public function add(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$this->assign('id',$id);
		$mod=M('ship_boat');
		if ($_POST['submit']){
			$data=$mod->create();
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/Boat/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/Boat/',$thumb);
				$data['imgurl'] = '/Uploads/Boat/s_'. $upload_info['0']['savename'];
			}
			$mod->add($data);		
			$this->success('添加成功',U('ShipBoat/index'));		
			
		}else{
			$shipCompany=M('ShipCompany');		
			$resCompany = $shipCompany->order('ordid desc')->where("is_del=0")->select();
			$this->assign('companyList',$resCompany);
			$this->display();
		}
	}

	
	//编辑商品信息
	public function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$mod=M('ship_boat');
		if($_POST['submit']){
			$data=$mod->create();
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/boat/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/boat/',$thumb);
				$data['imgurl'] = '/Uploads/boat/s_'. $upload_info['0']['savename'];
			}
			//var_dump($data);
			$mod->where('id='.$id)->save($data);
			$this->success('修改成功',U('ShipBoat/index'));
		}else{
			$boatInfo = $mod->where('id='.$id)->find();
			$this->assign('boatInfo',$boatInfo);
			$shipCompany=M('ShipCompany');		
			$resCompany = $shipCompany->order('ordid desc')->where("is_del=0")->select();
			$this->assign('companyList',$resCompany);
			$this->display();
		}
	}

	
	//删除商品
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$mod = M('ship_boat');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$mod->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');	
	}

	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mod = M('ship_boat');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod=M('ship_boat');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}

}

