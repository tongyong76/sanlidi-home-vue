<?php
class VisaAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		$id = $_REQUEST['id'];
		$this->assign('id',$id);
		$visa_mod=M('Visa');
		$visa_cate_mod=M('VisaCate');
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
			$arr = $visa_cate_mod->select();
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
		$count=$visa_mod->where($where)->count();
		$page=new Page($count,30);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		echo $page->parameter;
		$show=$page->show();
		$data=$visa_mod->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$map['id']=$val['cate_id'];
			$map['is_del']=0;
			$visa[$i]=$val;
			$cate=$visa_cate_mod->field('name')->where($map)->find();
			$visa[$i]['cate_name']=$cate['name'];
			$visa[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		//品牌

		//分类
		$result=$visa_cate_mod->order('ordid,id desc')->where("is_del=0")->select();
		$menu = arrToMenu($result,$id); 
		$this->assign('cate_list',$menu);
		$this->assign('visa',$visa);
		$this->assign('page',$show);
		$this->display();
	}
	
	
	//添加商品
	public function add(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$this->assign('id',$id);
		$visaMod=M('visa');
		if ($_POST['submit']){
			$data=$visaMod->create();
			$data['add_time'] = time();
			//var_dump($data);
			if ($_FILES['file1']['name'] != '' or $_FILES['file2']['name'] != '') {
				mkdir('./Uploads/visa/');
				$upload_info = $this->uploadFile('./Uploads/visa/');
				if($upload_info[0]){
					$data['file1_name'] = $upload_info['0']['name'];
					$data['file1'] = '/Uploads/visa/'. $upload_info['0']['savename'];
				}
				if($upload_info[1]){
					$data['file2_name'] = $upload_info['1']['name'];
					$data['file2'] = '/Uploads/visa/'. $upload_info['1']['savename'];
				}
				
			}
			$visaMod->add($data);
			$this->success('添加成功',U('Visa/index'));			
		}else{
			$cateMod=M('VisaCate');		
			$result = $cateMod->order('ordid desc')->where("is_del=0")->select();
			$menu = arrToMenu($result,0); 
			$this->assign('cate_list',$menu);
			$this->display();
		}
	}

	
	//编辑商品信息
	public function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$visaMod=M('visa');
		if($_POST['submit']){
			$data=$visaMod->create();
			if ($_FILES['file1']['name'] != '' or $_FILES['file2']['name'] != '') {
				mkdir('./Uploads/visa/');
				$upload_info = $this->uploadFile('./Uploads/visa/');
				if($upload_info[0]){
					$data['file1_name'] = $upload_info['0']['name'];
					$data['file1'] = '/Uploads/visa/'. $upload_info['0']['savename'];
				}
				if($upload_info[1]){
					$data['file2_name'] = $upload_info['1']['name'];
					$data['file2'] = '/Uploads/visa/'. $upload_info['1']['savename'];
				}
				
			}
			$visaMod->where('id='.$id)->save($data);
			$this->success('修改成功',U('Visa/index'));
		}else{
			$visa_cate=M('VisaCate');		
			$result = $visa_cate->order('ordid desc')->where("is_del=0")->select();
			$menu = arrToMenu($result,0); 
			$this->assign('cate_list',$menu);
			$visaInfo = $visaMod->where('id='.$id)->find();
			$this->assign('visaInfo',$visaInfo);
			$this->display();
		}
	}
	
	//ajax删除表单
	public function ajaxDelDoc(){
		$file_name = $_REQUEST['file_name'];
		$id = $_REQUEST['id'];
		$data[$file_name] = '';
		$data[$file_name.'_name'] = '';
		M('visa')->where('id='.$id)->save($data);
		$this->ajaxReturn(1);
	}

	
	//删除商品
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$mod = M('visa');
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
		$mod = M('visa');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod=M('visa');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	public function getChild(){
		$id = $_REQUEST['id'];
		$list = M('visa_cate')->field('id,name,floor')->where('pid='.$id)->select();
		if($list){
			$html = '<select name="cateList_'.$list[0]['floor'].'">'.
					'<option value="'.$id.'">--全部分类--</option>';
			foreach($list as $key=>$value){
				$html .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
			}
			$html .= '</select>';
			$this->ajaxReturn($html,$list[0]['floor'],1);
		}else{
			$floor = M('visa_cate')->where('id='.$id)->getfield('floor');
			$this->ajaxReturn("",$floor+1,0);
		}
	}

}

