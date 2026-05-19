<?php
class ShopviewAction extends BaseAction{
	
	public function index(){
		$mod = M('shopview');
		//搜索
		$where = 'is_del=0';
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND shopview_name LIKE '%".$_REQUEST['keyword']."%'";
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
		$order_str = 'status desc,ordid desc'; //默认排序

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
		$mod=M('shopview');
		if ($_POST['submit']){
			
			$data = $mod->create();
			$data['hotel_name'] = strip_tags($data['hotel_name']);
			$data['exp_time'] = strtotime($_REQUEST['exp_time']);
			//新增上传第一张图片作为缩略图
			if ($_FILES['shopview_imgurl']['name'] != '') {
				mkdir('./Uploads/shop/');
				$thumb=array('width'=>1411,'height'=>793);
				$upload_info = $this->upload('./Uploads/shop/',$thumb);
				$data['shopview_imgurl'] = '/Uploads/shop/s_'. $upload_info['0']['savename'];
			}
			$newId = $mod->add($data);

			if($newId){
				//处理图库	
				// if ($_FILES['imgurl']['name'] != '') {
					// mkdir('./Uploads/shop/');
					// $thumb=array('width'=>1411,'height'=>793);
					// $upload_info = $this->upload('./Uploads/shop/',$thumb);
					// //$data['imgurl'] = '/Uploads/hotel/s_'. $upload_info['0']['savename'];
				// }
				//$total = count($upload_info);
				foreach($upload_info as $key=>$value){
					$imgData['img_url'] = '/Uploads/shop/s_'. $value['savename'];
					$imgData['ordid'] = $key + 1;
					$imgData['shopview_id'] = $newId;
					M('shopview_gallery')->add($imgData);
				}
				$this->success('添加成功！',U('Shopview/index'));
				//var_dump($_FILES);
			}else {
				
				$this->error($mod->getError());
				
			}
			
		}else {
			
			$this->display();
		
		}	
	}
	
	//修改
	public function edit(){
		
		$shopview_id = isset($_REQUEST['shopview_id'])?$_REQUEST['shopview_id']:'';
		$mod = M('shopview');		
		if ($_POST['submit']){
			$data = $mod->create();
			$data['shopview_name'] = strip_tags($data['shopview_name']);
			$data['exp_time'] = strtotime($_REQUEST['exp_time']);
			//上传图片
			if ($_FILES['shopview_imgurl']['name'] != '') {
				mkdir('./Uploads/shop/');
				$thumb=array('width'=>1411,'height'=>793);
				$upload_info = $this->upload('./Uploads/shop/',$thumb);
			}
			$save = $mod->where('shopview_id='.$data['shopview_id'])->save($data);
			
			//更新已有图片的ORDID
			foreach($data['ordid'] as $key=>$value){
				M('shopview_gallery')->where(array('img_id'=>$key))->save(array('ordid'=>$value));
			}
			
			//更新新增图片
			$now = M('shopview_gallery')->where(array('shopview_id'=>$shopview_id))->count();  //现有图片数量
			foreach($upload_info as $key=>$value){
				$imgData['img_url'] = '/Uploads/shop/s_'. $value['savename'];
				$imgData['ordid'] = $now + $key + 1;
				$imgData['shopview_id'] = $data['shopview_id'];
				M('shopview_gallery')->add($imgData);
			}
			
			//更新封面
			$new['shopview_imgurl'] = M('shopview_gallery')->where('shopview_id='.$data['shopview_id'])->order('ordid asc')->limit(1)->getfield('img_url');
			$mod->where('shopview_id='.$data['shopview_id'])->save($new);
			
			$this->success('修改成功！',U('Shopview/index'));
			
		}else {
			//酒店信息
			$info = $mod->where('shopview_id='.$shopview_id)->find();
			$this->assign('info',$info);
			
			//图片库
			$imgList = M('shopview_gallery')->where('shopview_id='.$shopview_id)->order('ordid asc')->select();
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
		$mod=M('shopview');
		foreach ($del_id as $id){
			$mod->where('shopview_id='.$id." and is_del=0")->setField('is_del',1);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod=M('shopview');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('shopview_id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$shopview_id = $_GET['id'];
		$type = $_GET['type'];
		$mod = M('shopview');
		$data['shopview_id']=$shopview_id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//批量修改分类moveTo
	public function moveTo(){
		
	}
}