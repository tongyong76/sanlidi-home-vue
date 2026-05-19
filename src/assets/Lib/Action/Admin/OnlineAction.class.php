<?php
class OnlineAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		$goods_mod=M('Goods');
		$goods_cate_mod=M('GoodsCate');
		$group = $_REQUEST['group'];
	
		//搜索
		if($group){
			$where = 'GroupId='.$group.' and is_del=0 and type_id<>97';
		}else{
			$where = 'is_del=0 and type_id<>97';
		}
		
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND (name LIKE '%".$_REQUEST['keyword']."%' or subname LIKE '%".$_REQUEST['keyword']."%')";
			$this->assign('keyword', $_REQUEST['keyword']);
			//SESSION('skeyword',$_REQUEST['keyword']);
		}
		if (isset($_REQUEST['sn']) && trim($_REQUEST['sn'])) {
			$where .= " AND (sn LIKE '%".$_REQUEST['sn']."%')";
			$this->assign('sn', $_REQUEST['sn']);
			//SESSION('sn',$_REQUEST['sn']);
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
		
		$cateArr['f1'] = $_REQUEST['f1']?$_REQUEST['f1']:0;
		$cateArr['f2'] = $_REQUEST['f2']?$_REQUEST['f2']:0;
		//分类回传
		$f2List = M('goods_cate')->where('pid='.$cateArr['f1'].' and is_del=0')->select();
		$this->assign('f2List',$f2List);
		$cateArr['f3'] = $_REQUEST['f3']?$_REQUEST['f3']:0;
		$f3List = M('goods_cate')->where('pid='.$cateArr['f2'].' and is_del=0')->select();
		$this->assign('f3List',$f3List);
		$this->assign('cateArr',$cateArr);
		
		//根据cate_id获取对应goods_id集合
		if($cateArr['f3']){
			$map['cate_id'] = $cateArr['f3'];
			$goods_ids = M('goods_cate_rela')->where($map)->getfield('goods_id',true);
			$goods_ids = implode(',',$goods_ids);
			//$where .= " AND cate_id=".$cateArr['f3'];
			$where .= " AND id in (".$goods_ids.")";
		}elseif($cateArr['f2']){
			$arr = $goods_cate_mod->where('is_del=0')->select();
			$cate_arr = getEndChild($arr,$cateArr['f2']);

			if(empty($cate_arr)){
				$map['cate_id'] = $cateArr['f2'];
				$goods_ids = M('goods_cate_rela')->where($map)->getfield('goods_id',true);				
			}else{
				$map['cate_id'] = array('in',$cate_arr);
				$goods_ids = M('goods_cate_rela')->where($map)->group('goods_id')->getfield('goods_id',true);
			}
			$goods_ids = implode(',',$goods_ids);
			$where .= " AND id in (".$goods_ids.")";
		}elseif($cateArr['f1']){
			$arr = $goods_cate_mod->where('is_del=0')->select();
			$cate_arr = getEndChild($arr,$cateArr['f1']);
			$map['cate_id'] = array('in',$cate_arr);
			$goods_ids = M('goods_cate_rela')->where($map)->group('goods_id')->getfield('goods_id',true);
			$goods_ids = implode(',',$goods_ids);
			$where .= " AND id in (".$goods_ids.")";
		}
		
		// if (isset($_REQUEST['cate_id']) && intval($_REQUEST['cate_id'])) {
			// $arr = $goods_cate_mod->select();
			// $cate_arr = getEndChild($arr,$_REQUEST['cate_id']);
			// if($cate_arr){
				// $where .= " AND cate_id in (".implode(",",$cate_arr).")";
			// }else{
				// $where .= " AND cate_id=".$_REQUEST['cate_id'];
			// }
			// $this->assign('cate_id', $_REQUEST['cate_id']);
			// SESSION('scate_id',$_REQUEST['cate_id']);
		// }
		
		//排序功能
		if ($sort=='desc'){
			$sort='asc';
		}elseif ($sort=='asc'){
			$sort='desc';
		}
		$this->assign('order',$order);
		$this->assign('sort', $sort);
		$order_str = 'is_show desc,ordid desc,add_time desc'; //默认排序
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
		if($_REQUEST['group']) $page->parameter .= "group=".$_REQUEST['group'].'&';
		if($_REQUEST['f1']) $page->parameter .= "f1=".$_REQUEST['f1'].'&';
		if($_REQUEST['f2']) $page->parameter .= "f2=".$_REQUEST['f2'].'&';
		if($_REQUEST['f3']) $page->parameter .= "f3=".$_REQUEST['f3'].'&';
		if($_REQUEST['keyword']) $page->parameter .= "keyword=".$_REQUEST['keyword'].'&';
		if($_REQUEST['sn']) $page->parameter .= "sn=".$_REQUEST['sn'].'&';
		if($_REQUEST['p']) $page->parameter .= "p=".$_REQUEST['p'].'&';
		$this->assign('p',$_REQUEST['p']);
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
	
	//同行商品
	public function product(){
		$goods_mod=M('Goods');
		$goods_cate_mod=M('GoodsCate');
		$group = $_REQUEST['group'];
		$this->assign('group',$group);
	
		//搜索
		if($group){
			$where = 'GroupId='.$group.' and is_del=0 and type_id<>97';
		}else{
			$where = 'is_del=0 and type_id<>97';
		}
		
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND (name LIKE '%".$_REQUEST['keyword']."%' or subname LIKE '%".$_REQUEST['keyword']."%')";
			$this->assign('keyword', $_REQUEST['keyword']);
			//SESSION('skeyword',$_REQUEST['keyword']);
		}
		if (isset($_REQUEST['sn']) && trim($_REQUEST['sn'])) {
			$where .= " AND (sn LIKE '%".$_REQUEST['sn']."%')";
			$this->assign('sn', $_REQUEST['sn']);
			//SESSION('sn',$_REQUEST['sn']);
		}
		if (isset($_REQUEST['ProductID']) && trim($_REQUEST['ProductID'])) {
			$where .= " AND ProductID='".$_REQUEST['ProductID']."'";
			$this->assign('ProductID', $_REQUEST['ProductID']);
			//SESSION('sn',$_REQUEST['sn']);
		}
		if (isset($_REQUEST['productAttribute']) && trim($_REQUEST['productAttribute'])) {
			$where .= " AND productAttribute='".$_REQUEST['productAttribute']."'";
			$this->assign('productAttribute', $_REQUEST['productAttribute']);
			//SESSION('sn',$_REQUEST['sn']);
		}
		
		// if (isset($_REQUEST['cate_id']) && intval($_REQUEST['cate_id'])) {
			// $arr = $goods_cate_mod->select();
			// $cate_arr = getEndChild($arr,$_REQUEST['cate_id']);
			// if($cate_arr){
				// $where .= " AND cate_id in (".implode(",",$cate_arr).")";
			// }else{
				// $where .= " AND cate_id=".$_REQUEST['cate_id'];
			// }
			// $this->assign('cate_id', $_REQUEST['cate_id']);
			// SESSION('scate_id',$_REQUEST['cate_id']);
		// }
		
		//排序功能
		if ($sort=='desc'){
			$sort='asc';
		}elseif ($sort=='asc'){
			$sort='desc';
		}
		$this->assign('order',$order);
		$this->assign('sort', $sort);
		$order_str = 'is_show desc,ordid desc,add_time desc'; //默认排序
		if ($order) {
			$order_str = 'ordid desc,'.$order . ' ' . $sort;
		}

		//分页
		import("@.ORG.Page");
		//import("ORG.Util.Page");
		$count=$goods_mod->where($where)->count();
		$where2 = $where;
		$where2 .= " and is_show=1";
		$count2=$goods_mod->where($where2)->count();
		$this->assign('count',$count2);
		$page=new Page($count,10);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		$page->parameter = "";
		
		if($_REQUEST['id']) $page->parameter .= "id=".$_REQUEST['id'].'&';
		if($_REQUEST['cate_id']) $page->parameter .= "cate_id=".$_REQUEST['cate_id'].'&';
		if($_REQUEST['group']) $page->parameter .= "group=".$_REQUEST['group'].'&';
		if($_REQUEST['keyword']) $page->parameter .= "keyword=".$_REQUEST['keyword'].'&';
		if($_REQUEST['sn']) $page->parameter .= "sn=".$_REQUEST['sn'].'&';
		if($_REQUEST['ProductID']) $page->parameter .= "ProductID=".$_REQUEST['ProductID'].'&';
		if($_REQUEST['productAttribute']) $page->parameter .= "productAttribute=".$_REQUEST['productAttribute'].'&';
		if($_REQUEST['p']) $page->parameter .= "p=".$_REQUEST['p'].'&';
		$this->assign('p',$_REQUEST['p']);
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
	
	
	//添加商品
	public function add(){
		//$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		//$this->assign('id',$id);
		$goodsMod=M('goods');
		if ($_POST['submit']){
			$data=$goodsMod->create();
			$data['add_time'] = $data['last_time'] = time();
			
			//上传图片
			if ($_FILES['upfile']['name'] != '') {
				mkdir('./Uploads/goods/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/goods/',$thumb);
				$data['imgurl'] = '/Uploads/goods/s_'. $upload_info['0']['savename'];
			}
			//var_dump($data);
			$fruit = $_REQUEST['Fruit'];
			$data['switch'] = json_encode($fruit);
			$apple = $_REQUEST['Apple'];
			$data['service'] = json_encode($apple);

			$newId = $goodsMod->add($data);
			$relaData['goods_id'] = $newId;
			$relaData['cate_id'] = $data['cate_id'];
			M('goods_cate_rela')->add($relaData);
			// if($newId && session('isCopy')){
				// $dtData = M('trip')->field('id',true)->where('pid='.session('isCopy').' and is_del=0')->select();
				// foreach($dtData as $key=>$value){
					// $dtData[$key]['pid'] = $newId;
				// }
				// M('trip')->addAll($dtData);
				// session('isCopy',null);
			// }
			$this->success('添加成功',U('Online/index'));		
			//$this->redirect('Line/index',array('id'=>$data['type_id']));
		}else{
			$sn = 'T'.date('ymdHis',time());
			$this->assign('sn',$sn);
			$lincCate=M('GoodsCate');
			$result = $lincCate->order('ordid desc')->where("is_del=0")->select();
			$menu = arrToMenu($result,0); 
			$this->assign('cate_list',$menu);
			$this->display();
		}
	}

	
	//编辑商品信息
	public function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$goodsMod=M('goods');
		if($_POST['submit']){
			$jumpUrl = $_REQUEST['jumpUrl'];
//			echo $jumpUrl;
			$data=$goodsMod->create();
			$data['is_own'] = $data['is_own']?$data['is_own']:0;
			$data['is_new'] = $data['is_new']?$data['is_new']:0;
			$data['is_onsale'] = $data['is_onsale']?$data['is_onsale']:0;
			$data['is_hot'] = $data['is_hot']?$data['is_hot']:0;
			$data['is_holiday'] = $data['is_holiday']?$data['is_holiday']:0;
			$data['is_ds'] = $data['is_ds']?$data['is_ds']:0;
			$data['last_time'] = time();
//			$data['info'] = descclear($data['info']);
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/goods/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/goods/',$thumb);
				$data['imgurl'] = '/Uploads/goods/s_'. $upload_info['0']['savename'];
			}
			//获取TYPE_ID
			$tid = $goodsMod->where('id='.$id)->getfield('type_id');
			$apple = $_REQUEST['Apple'];
			$data['service'] = json_encode($apple);
			//var_dump($data);
			$goodsMod->where('id='.$id)->save($data);
			
			//处理移动详情
			$mData['goods_m_info'] = $_REQUEST['m_info']?$_REQUEST['m_info']:0;
			if(!empty($mData['goods_m_info'])){
				$mData['goods_id'] = $id;
				$gd_id = M('goods_detail')->where('goods_id='.$id)->getfield('gd_id');
				if(empty($gd_id)){
					M('goods_detail')->add($mData);
				}else{
					M('goods_detail')->where('gd_id='.$gd_id)->save($mData);
				}
			}else{
				
			}
			
			//处理标签
			$goodsTagMod = M('goods_tag');
			$tagMod = M('tag');
			$goodsTagMod->where("goods_id='".$id."'")->delete();//删除原有的
			$tags = isset($_POST['tags']) && trim($_POST['tags']) ? trim($_POST['tags']) : '';
			$tags = str_replace("，",",",$tags);
			if ($tags) {
				//标签不存在则添加
				$tags_arr = explode(',', $tags);
				$tags_arr = array_unique($tags_arr);
				foreach ($tags_arr as $tag) {
					$isset_id = $tagMod->where("name='".$tag."'")->getfield('id');
					if ($isset_id) {
						$goodsTagMod->add(array(
								'goods_id' => $id,
								'tag_id' => $isset_id,
						));
						//$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
					} else {
						$tag_id = $tagMod->add(array('name' => $tag));
						$goodsTagMod->add(array(
								'goods_id' => $id,
								'tag_id' => $tag_id,
						));
						//$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
					}
				}
			}
			
			echo "<script>alert('修改成功');window.close();</script>";
		}else{
			$goodsInfo = $goodsMod->where('id='.$id)->find();
			$goodsInfo['switch'] = json_decode($goodsInfo['switch']);
			$goodsInfo['service'] = json_decode($goodsInfo['service']);
			//当然线路分类
			$goodsInfo['cates'] = M('goods_cate_rela')->where('goods_id="'.$id.'"')->getfield('cate_id',true);
			
			$goodsTagMod = M('goods_tag');
			$tag_arr = $goodsTagMod->join('33_tag as T on 33_goods_tag.tag_id=T.id')->field('T.name')->where('goods_id='.$id)->select();
			foreach ($tag_arr as $tag){
				$tags[] .=$tag['name'];
			}
			$goodsInfo['tags'] = implode(',', $tags);
			$goods_m_info = M('goods_detail')->where('goods_id='.$id)->getField('goods_m_info');
			if(!empty($goods_m_info)){
				$goodsInfo['goods_m_info'] = $goods_m_info;
			}
			$this->assign('goodsInfo',$goodsInfo);
			
			//目录分类列表
			$goods_cate=M('GoodsCate');	
			$result = $goods_cate->order('ordid desc')->where("is_del=0")->select();
			$menu = arrToMenu($result,$goodsInfo['type_id']); 
			$this->assign('cate_list',$menu);
			
			$this->display();
		}
	}
	
	//编辑商品信息
	public function edit1(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$goodsMod=M('goods');
		if($_POST['submit']){
			$data=$goodsMod->create();
			$data['last_time'] = time();
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/goods/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/goods/',$thumb);
				$data['imgurl'] = '/Uploads/goods/s_'. $upload_info['0']['savename'];
			}
			//获取TYPE_ID
			$tid = $goodsMod->where('id='.$id)->getfield('type_id');
			$fruit = $_REQUEST['Fruit'];
			$data['switch'] = json_encode($fruit);
			$goodsMod->where('id='.$id)->save($data);
			//$this->success('修改成功',U('Line/index',array('id'=>$tid)));
			$this->redirect('Line/index',array('id'=>$tid));
		}else{
			$goodsInfo = $goodsMod->where('id='.$id)->find();
			$goodsInfo['switch'] = json_decode($goodsInfo['switch']);
			$this->assign('goodsInfo',$goodsInfo);
			$goods_cate=M('GoodsCate');		
			$result = $goods_cate->order('ordid desc')->where("is_del=0")->select();
			$menu = arrToMenu($result,$goodsInfo['type_id']); 
			$this->assign('cate_list',$menu);			
			$this->display();
		}
	}

	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$mod = M('goods');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$mod->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	
	//线路复制
	public function copy(){
		if (!isset($_POST['id'])){
			$this->success('请选择要复制的线路！');
		}else{
			$copy_id = $_POST['id'][0];
			$srcInfo = M('goods')->where('id='.$copy_id)->find();
			$sn = 'T'.date('ymdHis',time());
			$this->assign('sn',$sn);
			$lineCate=M('GoodsCate');		
			$result = $lineCate->order('ordid desc')->where("is_del=0")->select();
			$menu = arrToMenu($result,$srcInfo['type_id']); 
			$this->assign('id',$srcInfo['type_id']);
			$this->assign('cid',$srcInfo['cate_id']);
			$this->assign('cate_list',$menu);
			//$this->assign('goodsInfo',$srcInfo);
			session('isCopy',$copy_id);
			$this->assign('goodsInfo',$srcInfo);
			$this->display('add');
			
			//$mod = M('goods');
			//$data['is_del']=1;
			//foreach ($del_id as $id){
			//	$mod->where('id='.$id)->save($data);
			//}
			//$this->success('删除成功！');
		}
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
			$jumpUrl = $_SERVER['HTTP_REFERER'];
			$jumpUrl =  str_replace(".html", "", $jumpUrl);	
			if (isset($_REQUEST['skeyword']) && trim($_REQUEST['skeyword'])) {
				$jumpUrl .= "/keyword/".$_REQUEST['skeyword'];
			}
			if (isset($_REQUEST['sn']) && trim($_REQUEST['sn'])) {
				$jumpUrl .= "/sn/".$_REQUEST['sn'];
			}
			if (isset($_REQUEST['f1']) && trim($_REQUEST['f1'])) {
				$jumpUrl .= "/f1/".$_REQUEST['f1'];
			}
			if (isset($_REQUEST['f2']) && trim($_REQUEST['f2'])) {
				$jumpUrl .= "/f2/".$_REQUEST['f2'];
			}
			if (isset($_REQUEST['f3']) && trim($_REQUEST['f3'])) {
				$jumpUrl .= "/f3/".$_REQUEST['f3'];
			}
			if (isset($_REQUEST['p']) && trim($_REQUEST['p'])) {
				$jumpUrl .= "/p/".$_REQUEST['p'];
			}
			$mod=M('goods');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id." and is_del=0")->save($data);
			}
			
			// $jumpUrl =  str_replace(".html", "", $jumpUrl);	
			// if(session('scate_id') && !strstr($jumpUrl,"scate_id")){
				// $jumpUrl .= '/cate_id/'.session('scate_id');
			// }
			// if(session('skeyword') && !strstr($jumpUrl,"keyword")){
				// $jumpUrl .='/keyword/'.session('skeyword');
			// }
			//echo $jumpUrl;
			$this->success('修改成功',$jumpUrl);
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
	
	//添加线路分类
	public function addCate(){
		$data['goods_id'] = $_REQUEST['gid'];
		$data['cate_id'] = $_REQUEST['cid'];
		$cateInfo = M('goods_cate')->where('id="'.$data['cate_id'].'"')->find();
		if($cateInfo['floor'] <> 3 and $cateInfo['is_end'] <> 1) $this->ajaxReturn('请选择下级分类',0,2);
		$is_exist = M('goods_cate_rela')->where($data)->getfield('goods_id');
		if(empty($is_exist)){
			M('goods_cate_rela')->add($data);
			$this->ajaxReturn(0,0,1);
		}else{
			$this->ajaxReturn('分类已存在',0,2);
		}
	}
	
	//删除线路分类
	public function removeCate(){
		$data['goods_id'] = $_REQUEST['gid'];
		$data['cate_id'] = $_REQUEST['cid'];
		M('goods_cate_rela')->where($data)->delete();
		//如果父分类
		$this->ajaxReturn(0,0,1);
	}


}

