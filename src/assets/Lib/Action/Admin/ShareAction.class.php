<?php
class ShareAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		$goods_mod=M('Goods');
		$goods_cate_mod=M('GoodsCate');
		
		//搜索
		$where = 'is_del=0';
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND (name LIKE '%".$_REQUEST['keyword']."%' or subname LIKE '%".$_REQUEST['keyword']."%')";
			$this->assign('keyword', $_REQUEST['keyword']);
			SESSION('skeyword',$_REQUEST['keyword']);
		}
		if (isset($_REQUEST['sn']) && trim($_REQUEST['sn'])) {
			$where .= " AND (sn LIKE '%".$_REQUEST['sn']."%')";
			$this->assign('sn', $_REQUEST['sn']);
			SESSION('sn',$_REQUEST['sn']);
		}
		if (isset($_REQUEST['time_start']) && trim($_REQUEST['time_start'])){
			$time_start = strtotime($_REQUEST['time_start']);
			$where .= " AND add_time>='".$time_start."'";
			$this->assign('time_start', $_REQUEST['time_start']);
		}
		if (isset($_REQUEST['time_end']) && trim($_REQUEST['time_end'])) {
			$time_end =strtotime($_REQUEST['time_end']);
			$where .= " AND add_time<='".$time_end."'";
			$this->assign('time_end', $_REQUEST['time_end']);
		}
		
		$cateArr['f1'] = $_REQUEST['f1'];
		$cateArr['f2'] = $_REQUEST['f2'];
		//分类回传
		$f2List = M('goods_cate')->where('pid='.$cateArr['f1'].' and is_del=0')->select();
		$this->assign('f2List',$f2List);
		$cateArr['f3'] = $_REQUEST['f3'];
		$f3List = M('goods_cate')->where('pid='.$cateArr['f2'].' and is_del=0')->select();
		$this->assign('f3List',$f3List);
		$this->assign('cateArr',$cateArr);
		
		if($cateArr['f3']){
			$where .= " AND cate_id=".$cateArr['f3'];
		}elseif($cateArr['f2']){
			$arr = $goods_cate_mod->select();
			$cate_arr = getEndChild($arr,$cateArr['f2']);
			$where .= " AND cate_id in (".implode(",",$cate_arr).")";
		}elseif($cateArr['f1']){
			$arr = $goods_cate_mod->select();
			$cate_arr = getEndChild($arr,$cateArr['f1']);
			$where .= " AND cate_id in (".implode(",",$cate_arr).")";
		}
		
		//排序功能
		if ($sort=='desc'){
			$sort='asc';
		}elseif ($sort=='asc'){
			$sort='desc';
		}
		$this->assign('order',$order);
		$this->assign('sort', $sort);
		$order_str = 'is_share desc,ordid desc,add_time desc'; //默认排序
		if ($order) {
			$order_str = 'ordid desc,'.$order . ' ' . $sort;
		}

		//分页
		import("@.ORG.Page");
		//import("ORG.Util.Page");
		$count=$goods_mod->where($where)->count();
		$page=new Page($count,10);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		$page->parameter = "";
		
		if($_REQUEST['id']) $page->parameter .= "id=".$_REQUEST['id'].'&';
		if($_REQUEST['cate_id']) $page->parameter .= "cate_id=".$_REQUEST['cate_id'].'&';
		if($_REQUEST['f1']) $page->parameter .= "f1=".$_REQUEST['f1'].'&';
		if($_REQUEST['f2']) $page->parameter .= "f2=".$_REQUEST['f2'].'&';
		if($_REQUEST['f3']) $page->parameter .= "f3=".$_REQUEST['f3'].'&';
		if($_REQUEST['keyword']) $page->parameter .= "keyword=".$_REQUEST['keyword'].'&';
		if($_REQUEST['sn']) $page->parameter .= "sn=".$_REQUEST['sn'].'&';
		if($_REQUEST['p']) $page->parameter .= "p=".$_REQUEST['p'].'&';
		//echo $page->parameter;
		$show=$page->show();
		$data=$goods_mod->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$map['id']=$val['cate_id'];
			$map['is_del']=0;
			$goods[$i]=$val;
			$cate=$goods_cate_mod->field('name')->where($map)->find();
			$goods[$i]['cate_name']=$cate['name'];
			$goods[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		//品牌

		//分类
		$result=$goods_cate_mod->order('ordid,id desc')->where("is_del=0")->select();
		$menu = arrToMenu($result,0);
		$this->assign('cate_list',$menu);
		$this->assign('goods',$goods);
		$this->assign('page',$show);
		$this->display();
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
	
	public function editSharePrice(){
		
		$field = $_REQUEST['field'];
		$val = $_REQUEST['val'];
		$id = $_REQUEST['id'];
		$mod = M('goods');
		$mod->where('id='.$id)->setField($field,$val);
		$this->ajaxReturn('','',1);
		
		$this->display();
		
	}
	
	//订单列表
	public function order(){
		$mod = M('order as o');
		
		//条件
		$where = 'o.ordshare<>""';
		if (isset($_REQUEST['sharename']) && trim($_REQUEST['sharename'])) {
			$where .= " AND (wu.wx_nickname LIKE '%".$_REQUEST['sn']."%')";
			$this->assign('sharename', $_REQUEST['sharename']);
		}
		if (isset($_REQUEST['time_start']) && trim($_REQUEST['time_start'])){
			$time_start = strtotime($_REQUEST['time_start']);
			$where .= " AND o.add_time>='".$time_start."'";
			$this->assign('time_start', $_REQUEST['time_start']);
		}
		if (isset($_REQUEST['time_end']) && trim($_REQUEST['time_end'])) {
			$time_end =strtotime($_REQUEST['time_end']);
			$where .= " AND o.add_time<='".$time_end."'";
			$this->assign('time_end', $_REQUEST['time_end']);
		}
		
		//分页
		import("@.ORG.Page");
		//import("ORG.Util.Page");
		$count = $mod
		->field('o.id,o.gid,wu.wx_nickname as wx_nickname,o.ordsn,o.ordname,o.ordshare,o.adult_num,o.child_num,o.ordstatus,o.add_time as add_time,g.share_price')
		->join('33_wx_user as wu on wu.wx_openid=o.ordshare')
		->join('33_goods as g on g.id=o.gid')
		->where($where)
		->order('o.add_time desc')
		->count();
		$page=new Page($count,10);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		$page->parameter = "";
		
		if($_REQUEST['time_start']) $page->parameter .= "time_start=".$_REQUEST['time_start'].'&';
		if($_REQUEST['time_end']) $page->parameter .= "time_end=".$_REQUEST['time_end'].'&';
		if($_REQUEST['sharename']) $page->parameter .= "sharename=".$_REQUEST['sharename'].'&';
		if($_REQUEST['p']) $page->parameter .= "p=".$_REQUEST['p'].'&';
		//echo $page->parameter;
		$show=$page->show();		
		$data = $mod
		->field('o.id,o.gid,wu.wx_nickname as wx_nickname,o.ordsn,o.ordname,o.ordshare,o.adult_num,o.child_num,o.ordstatus,o.add_time as add_time,g.share_price')
		->join('33_wx_user as wu on wu.wx_openid=o.ordshare')
		->join('33_goods as g on g.id=o.gid')
		->where($where)
		->order('o.add_time desc')
		->select();
		$i=0;
		foreach ($data as $val){
			$orderList[$i]=$val;
			$orderList[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		$this->assign('orderList',$orderList);
		$this->assign('page',$show);
		$this->display();
	}

}

