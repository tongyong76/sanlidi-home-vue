<?php
class XihaAction extends BaseAction {
	
	//嘻哈线路详情页
	public function detail(){
		
		//根据ID读取文章
		$id = $_REQUEST['id'];

		//验证是否是嘻哈亲子
		$info = M('qinzi')->where('id='.$id)->find();
		$this->assign('info',$info);
		$nowtime = strtotime(date(Ymd));
		$depList = M('qinzi_departure')->where('pid='.$info['id'].' and departure_time>'.$nowtime.' and is_del=0')->order('departure_time')->select();
		$this->assign('depList',$depList);
		$this->display();
		
	}
	
	//表单提交
	public function form(){
		
		$cname = $_REQUEST['cname'];
		$cphone = $_REQUEST['cphone'];
		$tid = $_REQUEST['tid'];
		$adultN = $_REQUEST['adultN'];
		$childN = $_REQUEST['childN'];
		$cinfo = $_REQUEST['cinfo'];
			
		$data['cname'] = $cname;
		$sdata['cname'] = $cname;
		$data['cphone'] = $cphone;
		$sdata['cphone'] = $cphone;
		$data['cinfo'] = $cinfo;
		$data['tid'] = $tid;
		$data['adultN'] = $adultN;
		$data['childN'] = $childN;
		$data['add_time'] = time();
		
		//避免恶意灌水
		$exist_time = M('qinzi_form')->where($sdata)->order('add_time desc')->getfield('add_time');
		$exist_time = $exist_time?$exist_time:0;
		//10分钟内禁止重复提交
		$exp_time = $exist_time+3600*10;
		if($exp_time < $data['add_time']){
			$newId = M('qinzi_form')->add($data);
			if($newId){
				$this->ajaxReturn(0,'success',1);
			}else{
				$this->ajaxReturn(0,'failed',0);
			}
		}else{
			$this->ajaxReturn(0,'failed',2);
		}
		
		
	}
}