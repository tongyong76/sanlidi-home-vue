<?php
class AdAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		$pid = $_REQUEST['pid'];
		if($pid){
			$adMod=M('ad');
			$list = $adMod->where('is_del=0 and pid='.$pid)->order('ordid')->select();
			$this->assign('list',$list);
			$this->display();
		}else{
			$adMod=M('ad');
			$list = $adMod->where('is_del=0')->order('cname')->select();
			$this->assign('list',$list);
			$this->display();
		}
	}
	

	
	//编辑商品信息
	public function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$adMod=M('ad');
		if($_POST['submit']){
			$pid = $_REQUEST['pid'];
			$data=$adMod->create();
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/ad/');
				$thumb=array('width'=>1200,'height'=>1200);
				$upload_info = $this->upload('./Uploads/ad/',$thumb);
				$data['imgurl'] = '/Uploads/ad/s_'. $upload_info['0']['savename'];
			}
			$adMod->where('id='.$id)->save($data);
			$this->success('修改成功',U('Ad/index',array('pid'=>$pid)));
		}else{
			$info = $adMod->where('id='.$id)->find();
			$this->assign('info',$info);
			$this->display();
		}
	}

	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$adMod=M('ad');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$adMod->where($data)->save($set);
		$val=$adMod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//搜索广告位
	public function search(){
		$mod = M('ad_search');
		$info = $mod->where('id=1')->find();
		$this->assign('info',$info);
		$this->display();
	}
	
	//文字广告
	public function word(){
		$mod = M('ad');
		$list = $mod->where('pid=6')->select();
		$this->assign('list',$list);
		$this->display();
	}

}

