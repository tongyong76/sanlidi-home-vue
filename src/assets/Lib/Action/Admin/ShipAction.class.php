<?php
class ShipAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		$id = $_REQUEST['id'];
		$this->assign('id',$id);
		$ship_mod=M('Ship');
		$ship_cate_mod=M('ShipCate');
		//搜索
		$where = 'is_del=0';
		if($id) $where .= ' AND type_id='.$id;
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND name LIKE '%".$_REQUEST['keyword']."%'";
			$this->assign('keyword', $_REQUEST['keyword']);
		}
		if (isset($_REQUEST['time_start']) && trim($_REQUEST['time_start'])) {
			$time_start = strtotime($_REQUEST['time_start']);
			$where .= " AND add_time>='".$time_start."'";
			$this->assign('time_start', $_REQUEST['time_start']);
		}
		if (isset($_REQUEST['time_end']) && trim($_REQUEST['time_end'])) {
			$time_end =strtotime($_REQUEST['time_end']) ;
			$where .= " AND add_time<='".$time_end."'";
			$this->assign('time_end', $_REQUEST['time_end']);
		}
		if (isset($_REQUEST['cate_id']) && intval($_REQUEST['cate_id'])) {
			$arr = $ship_cate_mod->select();
			$cate_arr = getEndChild($arr,$_REQUEST['cate_id']);
			if($cate_arr){
				$where .= " AND cate_id in (".implode(",",$cate_arr).")";
			}else{
				$where .= " AND cate_id=".$_REQUEST['cate_id'];
			}
			$this->assign('cate_id', $_REQUEST['cate_id']);
		}
		
		//排序功能
		if ($sort=='desc'){
			$sort='asc';
		}elseif ($sort=='asc'){
			$sort='desc';
		}
		$this->assign('order',$order);
		$this->assign('sort', $sort);
		$order_str = 'ordid desc,add_time desc'; //默认排序
		if ($order) {
			$order_str = 'ordid desc,'.$order . ' ' . $sort;
		}

		//分页
		//import("@.ORG.Page");
		import("ORG.Util.Page");
		$count=$ship_mod->where($where)->count();
		$page=new Page($count,30);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		echo $page->parameter;
		$show=$page->show();
		$data=$ship_mod->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$map['id']=$val['cate_id'];
			$map['is_del']=0;
			$ship[$i]=$val;
			$cate=$ship_cate_mod->field('name')->where($map)->find();
			$ship[$i]['cate_name']=$cate['name'];
			$ship[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		//品牌

		//分类
		$result=$ship_cate_mod->order('ordid,id desc')->where("is_del=0")->select();
		$menu = arrToMenu($result,$id); 
		$this->assign('cate_list',$menu);
		$this->assign('ship',$ship);
		$this->assign('page',$show);
		$this->display();
	}
	
	
	//添加商品
	public function add(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$this->assign('id',$id);
		$shipMod=M('ship');
		if ($_POST['submit']){
			$data=$shipMod->create();
			$data['start_time'] = strtotime($data['start_time']);
			$data['add_time'] = time();
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/ship/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/ship/',$thumb);
				$data['imgurl'] = '/Uploads/ship/s_'. $upload_info['0']['savename'];
			}
			$apple = $_REQUEST['Apple'];
			$data['service'] = json_encode($apple);
			$data['is_owner'] = $data['is_owner']?$data['is_owner']:0;
			$newId = $shipMod->add($data);
			if($newId && session('isCopy')){
				$dtData = M('ship_trip')->field('id',true)->where('pid='.session('isCopy').' and is_del=0')->select();
				foreach($dtData as $key=>$value){
					$dtData[$key]['pid'] = $newId;
				}
				M('ship_trip')->addAll($dtData);
				session('isCopy',null);
			}
			
			$this->success('添加成功',U('Ship/index'));			
		}else{
			$sn = 'T'.date('ymdHis',time());
			$this->assign('sn',$sn);
			$shipCate=M('ShipCate');		
			$resCate = $shipCate->order('ordid desc')->where("is_del=0")->select();
			$this->assign('cateList',$resCate);
			$shipBoat=M('ShipBoat');		
			$resBoat = $shipBoat->order('ordid desc')->where("is_del=0")->select();
			$this->assign('boatList',$resBoat);
			$shipPort=M('ShipPort');		
			$resPort = $shipPort->order('ordid desc')->where("is_del=0")->select();
			$this->assign('portList',$resPort);
			$this->display();
		}
	}

	
	//编辑商品信息
	public function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$shipMod=M('ship');
		if($_POST['submit']){
			$data=$shipMod->create();
			$data['start_time'] = strtotime($data['start_time']);
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/ship/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/ship/',$thumb);
				$data['imgurl'] = '/Uploads/ship/s_'. $upload_info['0']['savename'];
			}
			$apple = $_REQUEST['Apple'];
			$data['service'] = json_encode($apple);
			$data['is_owner'] = $data['is_owner']?$data['is_owner']:0;
			//var_dump($data);
			$shipMod->where('id='.$id)->save($data);
			$this->success('修改成功',U('Ship/index'));
		}else{
			$shipInfo = $shipMod->where('id='.$id)->find();
			$shipInfo['service'] = json_decode($shipInfo['service']);
			$this->assign('shipInfo',$shipInfo);
			$shipCate=M('ShipCate');		
			$resCate = $shipCate->order('ordid desc')->where("is_del=0")->select();
			$this->assign('cateList',$resCate);
			$shipBoat=M('ShipBoat');		
			$resBoat = $shipBoat->order('ordid desc')->where("is_del=0")->select();
			$this->assign('boatList',$resBoat);
			$shipPort=M('ShipPort');		
			$resPort = $shipPort->order('ordid desc')->where("is_del=0")->select();
			$this->assign('portList',$resPort);
			$this->display();
		}
	}

	
	//删除商品
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$mod = M('ship');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$mod->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');	
	}

	//线路复制
	public function copy(){
		if (!isset($_POST['id'])){
			$this->success('请选择要复制的线路！');
		}else{
			$copy_id = $_POST['id'][0];
			$srcInfo = M('ship')->where('id='.$copy_id)->find();
			$sn = 'T'.date('ymdHis',time());
			$this->assign('sn',$sn);
			$shipCate=M('ShipCate');		
			$resCate = $shipCate->order('ordid desc')->where("is_del=0")->select();
			$this->assign('cateList',$resCate);
			$shipCompany=M('ShipCompany');		
			$resCompany = $shipCompany->order('ordid desc')->where("is_del=0")->select();
			$this->assign('companyList',$resCompany);
			$shipPort=M('ShipPort');		
			$resPort = $shipPort->order('ordid desc')->where("is_del=0")->select();
			$this->assign('portList',$resPort);
			$this->assign('shipInfo',$srcInfo);
			session('isCopy',$copy_id);
			$this->display('add');
		}
	}

	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mod = M('ship');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod=M('ship');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	public function getChild(){
		$id = $_REQUEST['id'];
		$list = M('ship_cate')->field('id,name,floor')->where('pid='.$id)->select();
		if($list){
			$html = '<select name="cateList_'.$list[0]['floor'].'">'.
					'<option value="'.$id.'">--全部分类--</option>';
			foreach($list as $key=>$value){
				$html .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
			}
			$html .= '</select>';
			$this->ajaxReturn($html,$list[0]['floor'],1);
		}else{
			$floor = M('ship_cate')->where('id='.$id)->getfield('floor');
			$this->ajaxReturn("",$floor+1,0);
		}
	}

}

