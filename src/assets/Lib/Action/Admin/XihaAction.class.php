<?php
class XihaAction extends BaseAction{

	public function index(){
		$mod=M('qinzi');

		//搜索
		$where = 'is_del=0';
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
			$this->assign('cate_id', $_POST['cate_id']);
		}
	
		//分页
		import("@.ORG.Page");
		$count=$mod->where($where)->count();
		$page=new Page($count,10);
		$show=$page->show();
		$data=$mod->where($where)->order('ordid desc,id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$map['id']=$val['cate_id'];
			$map['is_del']=0;
			$xihas[$i]=$val;
			$xihas[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		
		//分类
		$this->assign('xihas',$xihas);
		$this->assign('page',$show);
		$this->display();
	}


	//添加
	public function add(){
		$xiha=M('qinzi');
		if ($_POST['submit']){
			$data=$xiha->create();
			$data['add_time'] = time();
			//上传图片
			if ($_FILES['img']['name'] != '') {
				mkdir('./Uploads/');
				$thumb=array('width'=>500,'height'=>1000);
				$upload_info = $this->upload('./Uploads/',$thumb);
				$data['img'] = '/Uploads/s_'. $upload_info['0']['savename'];
			}
			$xiha->add($data);
			$this->success('添加成功',U('Xiha/index'));
		}else{
			$xiha_cate=M('xiha_cate');
		
			$result = $xiha_cate->order('ordid desc,id desc')->where("is_del=0")->select();
			$this->assign('cate_list',$result);
			$this->display();		
		}

	}
	
	//修改
	public function edit(){
		$xiha=M('qinzi');
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		
		if($_POST['submit']){
			if ($_POST['title']==''){
				$this->error('标题不能为空！');
			}
			$data=$xiha->create();
			//上传图片
			if ($_FILES['img']['name'] != '') {
				mkdir('./Uploads/');
				$thumb=array('width'=>500,'height'=>1000);
				$upload_info = $this->upload('./Uploads/',$thumb);
				$data['img'] = '/Uploads/s_'. $upload_info['0']['savename'];
			}			
			if ($id){
				//var_dump($data);
				$xiha->where('id='.$id)->save($data);
				$this->success('修改成功',U('Xiha/index'));
			}else {
				$xiha->add($data);
				$this->success('修改成功',U('Xiha/index'));
			}
			
		}else {
			$info = $xiha->where('id='.$id)->find();
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
		$xiha = M('Xiha');
		$xiha_cate_mod=M('xiha_cate');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$xiha->where('id='.$id)->save($data);
			$cid=$xiha->where("id=$id")->getField("cate_id");
			$xiha_cate_mod->where("id=$cid and is_del=0")->setDec("xiha_nums");
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$xiha = M('Xiha');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$xiha->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}

	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$xiha = M('qinzi');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$xiha->where($data)->save($set);
		$val=$xiha->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	public function form(){
		$mod = M('qinzi_form');
		import("ORG.Util.Page");
		$count=$mod->count();
		$page=new Page($count,10);
		$show=$page->show();
		$list = $mod->where(1)->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		foreach($list as $key=>$value){
			$qInfo = M('qinzi_departure')->join('33_qinzi as q on 33_qinzi_departure.pid=q.id')->where('33_qinzi_departure.id='.$value['tid'])->find();
			if($qInfo['title']){
				$list[$key]['pinfo'] = $qInfo['title'].',出发日期：'.date('m-d',$qInfo['departure_time']);
			}else{
				$list[$key]['pinfo'] = '';
			}
		}
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}

}
	