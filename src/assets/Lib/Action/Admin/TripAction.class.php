<?php
class TripAction extends BaseAction{

	public function index(){
		$pid = $_REQUEST['pid'];
		$pInfo = M('goods')->where('id='.$pid)->find();
		$this->assign('pInfo',$pInfo);
		$mod = M('trip');
		$list = $mod->where('is_del=0 and pid='.$pid)->order('ordid asc')->select();
		$this->assign('list',$list);
		$this->display();		
	}
	
	public function add(){
		$mod = M('trip');		
		if ($_POST['submit']){
			$data=$mod->create();
			$data['dinner'] = json_encode($data['dinner']);
			$mod->add($data);
			$this->success('添加成功',U('Trip/index',array('pid'=>$data['pid'])));
		}else{
			$this->assign('pid',$_REQUEST['id']);
			$this->display();
		}
	}
	
	//旧版 删除日期20141109
	// public function edit(){
		// $id = $_REQUEST['id'];
		// $mod = M('trip');	
		// if ($_POST['submit']){
			// $data=$mod->create();
			// $data['dinner'] = json_encode($data['dinner']);
			// $mod->where('id='.$id)->save($data);
			// $this->success('修改成功',U('Trip/index',array('pid'=>$data['pid'])));
		// }else{			
			// $info = $mod->where('id='.$id)->find();
			// $info['dinner'] = json_decode($info['dinner']);
			// $this->assign('info',$info);
			// $this->display();
		// }
	// }
	
	public function edit(){
		$pid = $_REQUEST['pid'];
		$mod = M('trip');	
		if ($_POST['submit']){
			$jumpUrl = $_REQUEST['jumpUrl'];
			$data=$mod->create();
			foreach($data['dinner'] as $key=>$value){
				$data['dinner'][$key] = json_encode($value);
			}
			//$data['dinner'] = json_encode($data['dinner']);
			$sum = count($data['name']);
			for($i=0;$i<$sum;$i++){
				$fdata[$i]['id'] = 0;
				$fdata[$i]['pid'] = $pid;
				$fdata[$i]['name'] = $data['name'][$i];
				$fdata[$i]['dinner'] = $data['dinner'][$i];
				$fdata[$i]['hotel'] = $data['hotel'][$i];
				$fdata[$i]['scene'] = $data['scene'][$i];
				$fdata[$i]['traffic_type'] = $data['traffic_type'][$i];
				$fdata[$i]['traffic_time'] = $data['traffic_time'][$i];
				$fdata[$i]['info'] = $data['info'][$i];
				$fdata[$i]['ordid'] = $i+1;
				$fdata[$i]['is_del'] = 0;
			}
			//删除原始行程
			$mod->where('pid='.$pid)->delete();
			if($sum == 1){
				$mod->add($fdata[0]);
			}else{
				$mod->addAll($fdata);
			}
			echo "<script>alert('修改成功');window.close();</script>";//新窗口打开
			//echo $mod->getlastsql();
			//$this->success('修改成功',U('Trip/edit',array('pid'=>$pid)));
			
			//新窗口打开
			//SESSION('skeyword',0);
			//SESSION('scate_id',0);
			//header("Location:".$jumpUrl);
		}else{
			$jumpUrl = $_SERVER['HTTP_REFERER'];
			$jumpUrl =  str_replace(".html", "", $jumpUrl);	
			if(session('scate_id') && !strstr($jumpUrl,"scate_id")){
				$jumpUrl .= '/cate_id/'.session('scate_id');
			}
			if(session('skeyword') && !strstr($jumpUrl,"keyword")){
				$jumpUrl .='/keyword/'.session('skeyword');
			}
			$this->assign('jumpUrl',$jumpUrl);
			$tripList = $mod->where('pid='.$pid)->order('ordid')->select();
			foreach($tripList as $key=>$value){
				$tripList[$key]['dinner'] = json_decode($value['dinner']);
			}
			$this->assign('pid',$pid);
			$this->assign('tripList',$tripList);
			$tripInfo = M('goods')->where('id='.$pid)->find();
			$this->assign('tripInfo',$tripInfo);
			$this->assign('totalDay',$tripInfo['days']);
			
			if($tripInfo['is_zyx']){
				$this->display('edit_zyx');
			}else{
				$this->display();
			}	
		}
	}

	public function edit2(){
		$pid = $_REQUEST['pid'];
		$mod = M('trip');	
		if ($_POST['submit']){
			$data=$mod->create();
			foreach($data['dinner'] as $key=>$value){
				$data['dinner'][$key] = json_encode($value);
			}
			//$data['dinner'] = json_encode($data['dinner']);
			$sum = count($data['name']);
			for($i=0;$i<$sum;$i++){
				$fdata[$i]['id'] = 0;
				$fdata[$i]['pid'] = $pid;
				$fdata[$i]['name'] = $data['name'][$i];
				$fdata[$i]['dinner'] = $data['dinner'][$i];
				$fdata[$i]['hotel'] = $data['hotel'][$i]; 
				$fdata[$i]['scene'] = $data['scene'][$i];		
				$fdata[$i]['info'] = $data['info'][$i];						
				$fdata[$i]['ordid'] = $i+1;
				$fdata[$i]['is_del'] = 0;			
			}
			//删除原始行程
			$mod->where('pid='.$pid)->delete();
			echo $sum;
			if($sum == 1){
				$mod->add($fdata[0]);
			}else{
				$mod->addAll($fdata);
			}
			//$this->success('修改成功',U('Trip/edit',array('pid'=>$pid)));
		}else{			
			$tripList = $mod->where('pid='.$pid)->order('ordid')->select();
			foreach($tripList as $key=>$value){
				$tripList[$key]['dinner'] = json_decode($value['dinner']);
			}
			$this->assign('pid',$pid);
			$this->assign('tripList',$tripList);
			$tripInfo = M('goods')->where('id='.$pid)->find();
			$this->assign('tripInfo',$tripInfo);
			$this->assign('totalDay',$tripInfo['days']);
			$this->display();
		}
	}
	
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的行程！');
		}
		$del_id = $_POST['id'];
		$mod = M('trip');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$mod->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	
}

