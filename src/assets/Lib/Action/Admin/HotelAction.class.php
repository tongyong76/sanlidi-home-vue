<?php
class HotelAction extends BaseAction{
	
	public function index(){
		$mod = M('hotel');
		//搜索
		$where = 'is_del=0';
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND hotel_name LIKE '%".$_REQUEST['keyword']."%'";
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
		$order_str = 'status,ordid desc'; //默认排序

		//分页
		//import("@.ORG.Page");
		import("ORG.Util.Page");
		$count=$mod->where($where)->count();
		$page=new Page($count,30);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		//echo $page->parameter;
		$show=$page->show();
		$data=$mod->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$data[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}

		$this->assign('hotelList',$data);
		$this->assign('page',$show);
		$this->display();
	}
	
	//添加
	public function add(){
		$mod=M('hotel');
		if ($_POST['submit']){
			
			$data = $mod->create();
			$data['hotel_name'] = strip_tags($data['hotel_name']);
			//新增上传第一张图片作为缩略图
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/hotel/');
				$thumb=array('width'=>600,'height'=>480);
				$upload_info = $this->upload('./Uploads/hotel/',$thumb);
				$data['hotel_imgurl'] = '/Uploads/hotel/s_'. $upload_info['0']['savename'];
			}
			$newId = $mod->add($data);

			if($newId){
				//处理图库	
				// if ($_FILES['imgurl']['name'] != '') {
					// mkdir('./Uploads/hotel/');
					// $thumb=array('width'=>600,'height'=>480);
					// $upload_info = $this->upload('./Uploads/hotel/',$thumb);
					// //$data['imgurl'] = '/Uploads/hotel/s_'. $upload_info['0']['savename'];
				// }
				foreach($upload_info as $key=>$value){
					$imgData['img_url'] = '/Uploads/hotel/s_'. $value['savename'];
					$imgData['hotel_id'] = $newId;
					M('hotel_gallery')->add($imgData);
				}
				$this->success('添加成功！',U('Hotel/index'));

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
		$mod = M('hotel');		
		if ($_POST['submit']){
			$data = $mod->create();
			$data['hotel_name'] = strip_tags($data['hotel_name']);
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/hotel/');
				$thumb=array('width'=>600,'height'=>480);
				$upload_info = $this->upload('./Uploads/hotel/',$thumb);
				//如何处理首图被删情况
				//$data['hotel_imgurl'] = '/Uploads/hotel/s_'. $upload_info['0']['savename'];
			}
			$save = $mod->where('hotel_id='.$data['hotel_id'])->save($data);
			//更新新增图片
			foreach($upload_info as $key=>$value){
				$imgData['img_url'] = '/Uploads/hotel/s_'. $value['savename'];
				$imgData['hotel_id'] = $data['hotel_id'];
				M('hotel_gallery')->add($imgData);
			}
			
			$this->success('修改成功！',U('Hotel/index'));
			
		}else {
			//酒店信息
			$info = $mod->where('hotel_id='.$id)->find();		
			$this->assign('info',$info);
			
			//图片库
			$imgList = M('hotel_gallery')->where('hotel_id='.$id)->select();
			$this->assign('imgList',$imgList);
			
			$this->display();	
		}		
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$mod=M('hotel');
		$article_mod=M("Article");
		foreach ($del_id as $id){
			$mod->where('hotel_id='.$id." and is_del=0")->setField('is_del',1);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod=M('hotel');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('goods_id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$article_cate = M('scenic');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$article_cate->where($data)->save($set);
		$val=$article_cate->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//批量修改分类moveTo
	public function moveTo(){
		
	}
}