<?php
class DepartureAction extends BaseAction{
	
	//添加行程列表
	public function index(){
		$pid = $_REQUEST['pid'];
		$this->assign('pid',$pid);
		$pInfo = M('goods')->where('id='.$pid)->find();
		$this->assign('pInfo',$pInfo);
		$mod = M('departure_time');
		$nowTime = time();
		$list = $mod->where('pid='.$pid.' and is_del=0 and departure_time >= '.$nowTime)->order('departure_time desc')->select();
		$jumpUrl = $_SERVER['HTTP_REFERER'];
		$jumpUrl =  str_replace(".html", "", $jumpUrl);	
		if(session('skeyword')=='Public'){
			$skeyword = '';
		}else{
			$skeyword = session('skeyword');
		}
		if(session('scate_id') && !strstr($jumpUrl,"scate_id")){
			$jumpUrl .= '/cate_id/'.session('scate_id');
		}
		if($skeyword && !strstr($jumpUrl,"keyword")){
			$jumpUrl .='/keyword/'.$skeyword;
		}
		$jumpUrl2 = $jumpUrl;
		//echo session('skeyword');
		if($_REQUEST['jumpUrl2']){
			
			$jumpUrl2 = base64_decode(urldecode(str_replace('*','%',$_REQUEST['jumpUrl2'])));
			$jumpUrl = $jumpUrl2;
		}
		
		$this->assign('jumpUrl',str_replace('%','*',urlencode(base64_encode($jumpUrl))));
		$this->assign('jumpUrl2',$jumpUrl2);

		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		$type = $_REQUEST['type'];
		
		$this->assign('type',$type);

		$mod = M('departure_time');		
		if ($_POST['submit']){
			$data=$mod->create();
			$jumpUrl = $_REQUEST['jumpUrl'];
			//获取最低价
			$minprice = M('goods')->where('id='.$data['pid'])->getField('minprice');
			if($data['price'] < $minprice){
				M('goods')->where('id='.$data['pid'])->setField('minprice',$minprice);
			}
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
			$this->success('添加成功',U('Departure/index',array('pid'=>$data['pid'],'jumpUrl2'=>$jumpUrl)));
			//SESSION('skeyword',NULL);
			//SESSION('scate_id',NULL);
			//$this->success('添加成功',$jumpUrl);
		}else{
			$this->assign('pid',$_REQUEST['id']);
			$jumpUrl = $_REQUEST['jumpUrl'];
			$this->assign('jumpUrl',$jumpUrl);
			
			$this->display();
		}
	}	
	
	public function edit(){
		
		$mod = M('departure_time');
		if ($_POST['submit']){
			$data=$mod->create();
			$jumpUrl = $_REQUEST['jumpUrl'];
			$data['departure_time'] = strtotime($data['departure_time']);
			$mod->where('id='.$data['id'])->save($data);
			//echo $mod->getlastsql();
			$this->success('添加成功',U('Departure/index',array('pid'=>$data['pid'],'jumpUrl2'=>$jumpUrl)));
			//SESSION('skeyword',NULL);
			//SESSION('scate_id',NULL);
			//$this->success('修改成功',$jumpUrl);
		}else{		
			$id = $_REQUEST['id'];
			$info = $mod->where('id='.$id)->find();
			$info['departure_time'] = date('Y-m-d', $info['departure_time']); 
			$this->assign('info',$info);
			$jumpUrl = $_REQUEST['jumpUrl'];
			$this->assign('jumpUrl',$jumpUrl);
			$this->display();		
		}		
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$pid = $_REQUEST['pid'];
		$jumpUrl = $_REQUEST['jumpUrl'];
		$del_id = $_POST['id'];
		$mod=M('departure_time');
		foreach ($del_id as $id){
			$mod->where('id='.$id.' and is_del=0')->setField('is_del',1);
		}
		//echo $mod->getlastsql(); 
		$this->success('删除成功！',U('Departure/index',array('pid'=>$pid,'jumpUrl2'=>$jumpUrl)));
	}
	
	//获取最低价
	public function getMinprice(){
		$goodsId = $_REQUEST['goods_id'];
		$goodsMod = M('goods');
		$goodsTimeMod = M('departure_time');
		$nowTime = time();
		$minprice = $goodsTimeMod->where('pid='.$goodsId.' and is_del=0 and departure_time > '.$nowTime)->min('price');
		$goodsMod->where('id='.$goodsId)->setfield('minprice',$minprice);
		$this->ajaxReturn($minprice,'',1);	
	}
	
}
	