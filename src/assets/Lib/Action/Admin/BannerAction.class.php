<?php
class BannerAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		$bannerMod=M('banner');
		$list = $bannerMod->where('is_del=0')->order('ordid desc')->select();
		$menu = arrToMenu($list,0); 	
		$this->assign('list',$menu);
		$this->display();
	}
	
	//添加幻灯
	public function add(){
		
		$bannerMod=M('banner');
		if($_POST['submit']){
			$data=$bannerMod->create();
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/banner/');
				$thumb=array('width'=>2000,'height'=>1000);
				$upload_info = $this->upload('./Uploads/banner/',$thumb);
				$data['imgurl'] = '/Uploads/banner/s_'. $upload_info['0']['savename'];
			}
			$bannerMod->add($data);
			$this->success('添加成功',U('Banner/index'));
		}else{
			$pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:'';
			$this->assign("pid",$pid);
			$this->display();
		}
//		if($pid){
//			//小幻灯
//		}else{
//			//大幻灯
//		}
	}
	
	//编辑幻灯
	public function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$bannerMod=M('banner');
		if($_POST['submit']){
			$data=$bannerMod->create();
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/banner/');
				$thumb=array('width'=>2000,'height'=>1000);
				$upload_info = $this->upload('./Uploads/banner/',$thumb);
				$data['imgurl'] = '/Uploads/banner/s_'. $upload_info['0']['savename'];
			}
			$bannerMod->where('id='.$id)->save($data);
			//echo $bannerMod->getlastsql();
			$this->success('修改成功',U('Banner/index'));
		}else{
			$info = $bannerMod->where('id='.$id)->find();
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
		$mod=M('banner');
		foreach ($del_id as $id){
			$mod->where('id='.$id." and is_del=0")->setField('is_del',1);
			$mod->where("pid=$id and is_del=0")->setField("is_del",1);
		}
		$this->success('删除成功！');
	}
	
	//排序
	public function order(){
		if ($_POST['order']){
			$bannerMod=M('banner');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$bannerMod->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}

	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$bannerMod=M('banner');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$bannerMod->where($data)->save($set);
		$val=$bannerMod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}

}

