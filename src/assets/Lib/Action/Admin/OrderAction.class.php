<?php
class OrderAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		//$this->assign('posi',"123");
		$mod=M('order');
		//状态
		$result = M('order_status')->select();
		$this->assign('statusList',$result);
		
		//搜索
		if($_REQUEST['type'] == 1){
			$where = '1=1 and ordshare is not null ';
		}else{
			$where = '1=1 and ordshare is null ';
		}
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND (ordname LIKE '%".$_REQUEST['keyword']."%')";
			$this->assign('keyword', $_REQUEST['keyword']);
			SESSION('skeyword',$_REQUEST['keyword']);
		}
		if (isset($_REQUEST['ordsn']) && trim($_REQUEST['ordsn'])) {
			$where .= " AND ordsn LIKE '%".$_REQUEST['ordsn']."%'";
			$this->assign('ordsn', $_REQUEST['ordsn']);
			SESSION('sordsn',$_REQUEST['ordsn']);
		}
		if (isset($_REQUEST['time_start']) && trim($_REQUEST['time_start'])) {
			$time_start = strtotime($_REQUEST['time_start']);
			$where .= " AND add_time>='".$time_start."'";
			$this->assign('time_start', $_REQUEST['time_start']);
		}
		if (isset($_REQUEST['time_end']) && trim($_REQUEST['time_end'])) {
			$time_end =strtotime($_REQUEST['time_end']) ;
			$where .= " AND add_time<='".$time_end."'";
			$this->assign('time_end', $_REQUEST['time_end']);
		}
		if (isset($_REQUEST['cate_id']) && intval($_REQUEST['cate_id'])) {
			$where .= " AND ordstatus=".$_REQUEST['cate_id'];
			$this->assign('cate_id', $_REQUEST['cate_id']);
			SESSION('scate_id',$_REQUEST['cate_id']);
		}
		
		//排序功能
		if ($sort=='desc'){
			$sort='asc';
		}elseif ($sort=='asc'){
			$sort='desc';
		}
		$this->assign('order',$order);
		$this->assign('sort', $sort);
		$order_str = 'add_time desc'; //默认排序
		if ($order) {
			$order_str = $order . ' ' . $sort;
		}

		//分页
		//import("@.ORG.Page");
		import("ORG.Util.Page");
		$count=$mod->where($where)->count();
		$page=new Page($count,15);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		$page->parameter = "";
		if($_REQUEST['ordsn']) $page->parameter .= "ordsn=".$_REQUEST['ordsn'].'&';
		if($_REQUEST['time_start']) $page->parameter .= "time_start=".$_REQUEST['time_start'].'&';
		if($_REQUEST['time_end']) $page->parameter .= "time_end=".$_REQUEST['time_end'].'&';
		if($_REQUEST['cate_id']) $page->parameter .= "cate_id=".$_REQUEST['cate_id'].'&';
		if($_REQUEST['keyword']) $page->parameter .= "keyword=".$_REQUEST['keyword'].'&';
		if($_REQUEST['p']) $page->parameter .= "p=".$_REQUEST['p'].'&';
		
		$show=$page->show();
		$data=$mod->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();
		foreach ($data as $key=>$val){
			switch($val['ordpay']){
				case 0:
					$data[$key]['ordpay'] = "未选择";
					break;
				case 1:
					$data[$key]['ordpay'] = "支付宝";
					break;
				case 2:
					$data[$key]['ordpay'] = "门店";
					break;
				case 3:
					$data[$key]['ordpay'] = "转账";
					break;
				case 4:
					$data[$key]['ordpay'] = "移动支付";
					break;
			}
		}

		$this->assign('list',$data);
		$this->assign('page',$show);
		$this->display();
	}

	
	//编辑商品信息
	public function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$this->assign('type',$_REQUEST['type']);
		$mod=M('order');
		if($_POST['submit']){
			$ordpay = $_REQUEST['ordpay'];
			$reason = $_REQUEST['reason'];
			//echo $ordpay;
			if($ordpay == 911){
				//1订单状态（-2后台取消）
				$dataOrder['ordstatus'] = -2;
				$dataOrder['is_edit'] = 0;
				$dataOrder['clsrz'] = $_REQUEST['clsrz'];
				$mod->where('id='.$id)->save($dataOrder);
				//2操作记录
				$dataModify['modify_type'] = '后台取消';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = $_REQUEST['clsrz'].'-'.$reason;
				$dataModify['admin_id'] = $this->uid;
				$dataModify['order_id'] = $id;
				M('order_modify')->add($dataModify);
			}elseif($ordpay == 910){
				//1订单状态（3交易完成）
				$dataOrder['ordstatus'] = 3;
				$dataOrder['is_edit'] = 0;
				$mod->where('id='.$id)->save($dataOrder);				
				//2操作记录
				$dataModify['modify_type'] = '支付完成';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = $reason;
				$dataModify['admin_id'] = $this->uid;
				$dataModify['order_id'] = $id;
				M('order_modify')->add($dataModify);
			}elseif($ordpay == 912){
				//1订单状态（3交易完成）
				$dataOrder['notice'] = $_REQUEST['notice'];
				$mod->where('id='.$id)->save($dataOrder);				
				//2操作记录
				$dataModify['modify_type'] = '上传出团通知书';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = '';
				$dataModify['admin_id'] = $this->uid;
				$dataModify['order_id'] = $id;
				M('order_modify')->add($dataModify);
			}elseif($ordpay == 1){
				//1修改支付方式
				$dataOrder['ordpay'] = $ordpay;
				//2订单状态（1等待支付）
				$dataOrder['ordstatus'] = 1;
				$dataOrder['is_edit'] = 0;
				$mod->where('id='.$id)->save($dataOrder);
				//2操作记录
				$dataModify['modify_type'] = '支付宝';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = $reason;
				$dataModify['admin_id'] = $this->uid;
				$dataModify['order_id'] = $id;
				M('order_modify')->add($dataModify);
				//发短信给客户
				//短信…………
				$orderInfo = $mod->where('id='.$id)->find();
				$msgPhone = $orderInfo['cphone'];
				$msgData = "您的订单".$orderInfo['ordname']."可以支付了，请登录后台进行支付http://m.33ly.com/uc";
				sendmessage($msgPhone,$msgData);
			}
			elseif($ordpay == 3){
				//1修改支付方式
				$dataOrder['ordpay'] = $ordpay;
				//2订单状态（1等待支付）
				$dataOrder['ordstatus'] = 1;
				$dataOrder['is_edit'] = 0;
				$mod->where('id='.$id)->save($dataOrder);
				//2操作记录
				$dataModify['modify_type'] = '银行转账';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = $reason;
				$dataModify['admin_id'] = $this->uid;
				$dataModify['order_id'] = $id;
				M('order_modify')->add($dataModify);
				//发短信给客户
				//短信…………
			}
			elseif($ordpay == 909){  //转门店
				//1修改支付方式
				$dataOrder['ordpay'] = 0;
				//2订单状态（4转门店跟进）
				$dataOrder['ordstatus'] = 4;
				$dataOrder['is_edit'] = 0;
				$mod->where('id='.$id)->save($dataOrder);
				//2操作记录
				$dataModify['modify_type'] = '转门店跟进';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = $reason;
				$dataModify['admin_id'] = $this->uid;
				$dataModify['order_id'] = $id;
				M('order_modify')->add($dataModify);
			}else{//状态不变更，只有操作记录
				$dataOrder['ordstatus'] = 5;
				$mod->where('id='.$id)->save($dataOrder);
				$dataModify['modify_type'] = '沟通记录';
				$dataModify['modify_time'] = time();
				$dataModify['reason'] = $reason;
				$dataModify['admin_id'] = $this->uid;
				$dataModify['order_id'] = $id;
				M('order_modify')->add($dataModify);
			}
			
			//处理出团单
			if ($_FILES['notice']['name'] != '') {
				mkdir('./Uploads/notice/');
				$upload_info = $this->uploadFile('./Uploads/notice/');
				if($upload_info[0]){
					M('order_notice')->where('order_id='.$id)->delete();
					$noticeData['notice_name'] = $upload_info['0']['name'];
					$noticeData['notice_url'] = '/Uploads/notice/'. $upload_info['0']['savename'];
					$noticeData['order_id'] = $id;
					$noticeData['add_time'] = time();
					M('order_notice')->add($noticeData);
				}
			}
			
			$this->redirect('Order/index');
		}else{
			//订单信息
			$info = $mod->field('*,33_order.id as id')->join('33_user as u on u.id=33_order.uid')->where('33_order.id='.$id)->find();
			$info['sales_name'] = M('wx_user')->where(array('wx_openid'=>$info['ordshare']))->getfield('real_name');
			$this->assign('info',$info);
			
			//修改记录
			$modify_record = M('order_modify')->join('33_admin as a on a.id=33_order_modify.admin_id')->where('order_id='.$id)->order('33_order_modify.modify_time desc')->select();
			$this->assign('modify_record',$modify_record);
			
			//出行人信息
			$cardList = M('user_card')->where('oid='.$info['id'])->order('card_type asc,oid asc')->select();
			$this->assign('cardList',$cardList);
			
			//出团单信息
			$noticeInfo = M('order_notice')->where('order_id='.$info['id'])->find();
			$this->assign('noticeInfo',$noticeInfo);
			
			$this->display();
		}
	}
	
	//ajax删除出团单
	public function ajaxDelDoc(){
		$order_id = $_REQUEST['order_id'];
		M('order_notice')->where('order_id='.$order_id)->delete();
		$this->ajaxReturn(1);
	}	
	
	//删除商品
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		foreach ($del_id as $id){
			$this->delete_item($id);
		}
		$this->success('删除成功！');	
	}

	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mod = M('goods');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod=M('goods');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	public function getChild(){
		$id = $_REQUEST['id'];
		$list = M('goods_cate')->field('id,name,floor')->where('pid='.$id)->select();
		if($list){
			$html = '<select name="cateList_'.$list[0]['floor'].'">'.
					'<option value="'.$id.'">--全部分类--</option>';
			foreach($list as $key=>$value){
				$html .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
			}
			$html .= '</select>';
			$this->ajaxReturn($html,$list[0]['floor'],1);
		}else{
			$floor = M('goods_cate')->where('id='.$id)->getfield('floor');
			$this->ajaxReturn("",$floor+1,0);
		}
	}
	
	public function tuan(){
		$mod=M('order_tuan');
		import("ORG.Util.Page");
		$count=$mod->count();
		$page=new Page($count,10);
		$show=$page->show();
		$data=$mod->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$data);
		$this->assign('page',$show);
		$this->display();
	}
	
	public function tuanView(){
		$id = $_REQUEST['id'];
		$mod=M('order_tuan');
		$info = $mod->where('id='.$id)->find();
		$this->assign('info',$info);
		$this->display();
	}

}

