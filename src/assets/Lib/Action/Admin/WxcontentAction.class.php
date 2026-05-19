<?php
class WxcontentAction extends BaseAction{

	public function index(){
		$mod = M('wx_content');
		$data = $mod->order('ordid desc,id desc')->where("is_del=0")->select();	
		
		$this->assign('list',$data);
		$this->display();
	}
	
	//添加
	public function add(){
		$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$mod = M('wx_content');
		if ($_POST['submit']){		
			$data = $mod->create();
			//上传图片
			if ($_FILES['cimg']['name'] != '') {
				mkdir('./Uploads/weixin/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/weixin/',$thumb);
				$data['cimg'] = '/Uploads/weixin/s_'. $upload_info['0']['savename'];
			}
			$mod->where('id='.$data['pid'])->setField('is_end',0);
			$data['floor'] = $mod->where('id='.$data['pid'])->getField('floor')+1;
			$row = $mod->add($data);
			if ($row){
				$this->success('添加成功！',U('Wxcontent/index'));
			}else {
				$this->error($mod->getError());
			}
			
		}else {
			if($id){$this->assign('id',$id);};
			$data = M('wx_menu')->order('ordid desc')->where("is_del=0 and floor=2 and status=1")->select();
			//$menu = arrToMenu($data,0); 
			$this->assign('menu_list',$data);
			$this->display();
		}	
	}
	
	//修改
	public function edit(){
		
		$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';	
		$mod = M('wx_content');
		
		if ($_POST['submit']){
			$data=$mod->create();
			//上传图片
			if ($_FILES['cimg']['name'] != '') {
				mkdir('./Uploads/weixin/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/weixin/',$thumb);
				$data['cimg'] = '/Uploads/weixin/s_'. $upload_info['0']['savename'];
			}
			$data['floor'] = $mod->where('id='.$data['pid'])->getField('floor')+1;
			$save=$mod->where("id=$id")->save($data);
			$this->success('修改成功！',U('Wxcontent/index'));
			
		}else {
			if ($id==NULL){
				$this->error('请选择分类！');
			}
			$info=$mod->where('id='.$id)->find();			
			$this->assign('info',$info);
			$data = M('wx_menu')->order('ordid desc')->where("is_del=0 and floor=2 and status=1")->select();
			$this->assign('menu_list',$data);
			$this->display();	
		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$mod = M('wx_content');
		foreach ($del_id as $id){
			$mod->where('id='.$id." and is_del=0")->setField('is_del',1);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod = M('wx_content');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mod = M('wx_content');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type],'返回成功',1);
	}
	
	//ajax修改
	public function ajaxEdit(){
		$id = $_REQUEST['id'];
		$field = $_REQUEST['field'];
		$val = $_REQUEST['val'];
		$mod = M('wx_content');
		$mod->where('id='.$id)->setField($field,$val);
		$this->ajaxReturn('','',1);
	}
	
    //上传方法
    public function upload($savePath,$thumb=array()) {
    
    	import("ORG.Net.UploadFile");
    	$upload = new UploadFile();
    	$upload->maxSize  = 2097152;// 设置附件上传大小
    	$upload->savePath = $savePath;// 设置附件上传目录
    	$upload->saveRule = uniqid;
    	$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

    	if ($thumb) {
	    	$upload->thumb = true;
	    	$upload->thumbMaxWidth = $thumb['width'];    	
	    	$upload->thumbMaxHeight = $thumb['height'];
	    	$upload->thumbPrefix = 's_';
	    	$upload->thumbRemoveOrigin = true;
    	}

    	if(!$upload->upload()) {
    		// 上传错误提示错误信息
    		$this->error($upload->getErrorMsg());
    	}else{
    		// 上传成功 获取上传文件信息
    		$info =  $upload->getUploadFileInfo();
    	}
    	return $info;
    }
	
}