<?php
class XihaDepartureAction extends BaseAction{
	
	//添加行程列表
	public function index(){
		$pid = $_REQUEST['pid'];
		$pInfo = M('qinzi')->where('id='.$pid)->find();
		$this->assign('pInfo',$pInfo);
		$mod = M('qinzi_departure');
		$list = $mod->where('pid='.$pid.' and is_del=0')->order('departure_time desc')->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		$type = $_REQUEST['type'];		
		$this->assign('type',$type);

		$mod = M('qinzi_departure');		
		if ($_POST['submit']){
			$data=$mod->create();
			if($type == 'muti'){
				$timeStart = strtotime($_REQUEST['time_start']);
				$timeEnd = strtotime($_REQUEST['time_end']);
				$days = $_REQUEST['days'];
				for($i=$timeStart;$i<=$timeEnd;$i+=86400){
					if(in_array(date('w',$i),$days)){
						$data['departure_time'] = $i;
						$data['add_time'] = time();
						$mod->add($data);
					}										
				}
			}
			
			if($type == 'single'){
				$timeStart = $_REQUEST['time_start'];
				$data['departure_time'] = strtotime($timeStart);
				$data['add_time'] = time();
				$mod->add($data);
			}
			$this->success('添加成功',U('XihaDeparture/index',array('pid'=>$data['pid'])));
		}else{
			$this->assign('pid',$_REQUEST['id']);
			$this->display();
		}
	}	
	
	public function edit(){
		
		$mod = M('qinzi_departure');
		if ($_POST['submit']){
			$data=$mod->create();
			$data['xiha_departure'] = strtotime($data['xiha_departure']);
			$mod->where('id='.$data['id'])->save($data);
			//echo $mod->getlastsql();
			$this->success('添加成功',U('XihaDeparture/index',array('pid'=>$data['pid'])));
		}else{		
			$id = $_REQUEST['id'];
			$info = $mod->where('id='.$id)->find();
			$info['xiha_departure'] = date('Y-m-d', $info['xiha_departure']); 
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
		$mod=M('qinzi_departure');
		foreach ($del_id as $id){
			$mod->where('id='.$id.' and is_del=0')->setField('is_del',1);
		}
		//echo $mod->getlastsql(); 
		$this->success('删除成功！');
	}	
	
}
	