<?php
class LineAction extends BaseAction{

	//分页显示所有商品
	public function index(){
		$id = $_REQUEST['id'];
		$this->assign('id',$id);
		$goods_mod=M('Goods');
		$goods_cate_mod=M('GoodsCate');
		//搜索
		$where = 'is_del=0';
		if($id) $where .= ' AND type_id='.$id;
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
		if (isset($_REQUEST['cate_id']) && intval($_REQUEST['cate_id'])) {
			$arr = $goods_cate_mod->select();
			$cate_arr = getEndChild($arr,$_REQUEST['cate_id']);
			if(empty($cate_arr)){
				$map['cate_id'] = $_REQUEST['cate_id'];
				$goods_ids = M('goods_cate_rela')->where($map)->getfield('goods_id',true);
			}else{
				$map['cate_id'] = array('in',$cate_arr);
				$goods_ids = M('goods_cate_rela')->where($map)->group('goods_id')->getfield('goods_id',true);
			}
			$goods_ids = implode(',',$goods_ids);
			$where .= " AND id in (".$goods_ids.")";
			
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
		$order_str = 'is_show desc,ordid desc,add_time desc'; //默认排序
		if ($order) {
			$order_str = 'is_show desc,ordid desc,'.$order . ' ' . $sort;
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
		$menu = arrToMenu($result,$id); 
		$this->assign('cate_list',$menu);
		$this->assign('goods',$goods);
		$this->assign('page',$show);
		$this->display();
	}
	
	
	//添加商品
	public function add(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$is_zyx=isset($_REQUEST['is_zyx'])?$_REQUEST['is_zyx']:'';
		$this->assign('id',$id);
		$goodsMod=M('goods');
		if ($_POST['submit']){
			$data=$goodsMod->create();
			$data['is_zyx'] = $data['is_zyx']?$data['is_zyx']:0;
			$data['add_time'] = $data['last_time'] = time();
			
			//上传图片
			if ($_FILES['upfile']['name'] != '') {
				mkdir('./Uploads/goods/');
				$thumb=array('width'=>600,'height'=>1000);
				$upload_info = $this->upload('./Uploads/goods/',$thumb);
				$data['imgurl'] = '/Uploads/goods/s_'. $upload_info['0']['savename'];
			}
			$fruit = $_REQUEST['Fruit'];
			$data['switch'] = json_encode($fruit);
			$apple = $_REQUEST['Apple'];
			$data['service'] = json_encode($apple);
			$newId = $goodsMod->add($data);
			
			$relaData['goods_id'] = $newId;
			$relaData['cate_id'] = $data['cate_id'];
			M('goods_cate_rela')->add($relaData);
			
			if($data['is_zyx'] and ($data['type_id'] <> 1)){
				//自由行航班信息
				$flightData = M('goods_flight')->create();
				$sum = count($flightData['daytime']);
				for($i=0;$i<$sum;$i++){
					$fdata[$i]['pid'] = $newId;
					$fdata[$i]['daytime'] = $flightData['daytime'][$i];
					$fdata[$i]['traffic_type'] = $flightData['traffic_type'][$i];
					$fdata[$i]['traffic_no'] = $flightData['traffic_no'][$i];
					$fdata[$i]['traffic_level'] = $flightData['traffic_level'][$i];
					$fdata[$i]['start_place'] = $flightData['start_place'][$i];
					$fdata[$i]['start_time'] = $flightData['start_time'][$i];
					$fdata[$i]['start_port'] = $flightData['start_port'][$i];
					$fdata[$i]['arrival_place'] = $flightData['arrival_place'][$i];
					$fdata[$i]['arrival_time'] = $flightData['arrival_time'][$i]; 
					$fdata[$i]['arrival_port'] = $flightData['arrival_port'][$i];							
					$fdata[$i]['ordid'] = $i+1;
					$fdata[$i]['is_del'] = 0;
				}
				M('goods_flight')->where('pid='.$newId)->delete();
				M('goods_flight')->addAll($fdata);
				
				//自由行酒店信息
				$hotelData = M('goods_hotel')->create();
				$sum = count($hotelData['hotel_day']);
				for($i=0;$i<$sum;$i++){
					$hdata[$i]['pid'] = $newId;
					$hdata[$i]['hotel_day'] = $hotelData['hotel_day'][$i];
					$hdata[$i]['hotel_type'] = $hotelData['hotel_type'][$i];
					$hdata[$i]['hotel_id'] = $hotelData['hotel_id'][$i];
					$hdata[$i]['ordid'] = $i+1;
					$hdata[$i]['is_del'] = 0;
				}
				M('goods_hotel')->where('pid='.$newId)->delete();
				M('goods_hotel')->addAll($hdata);
			}
			
			//复制
			if($newId && session('isCopy')){
				$dtData = M('trip')->field('id',true)->where('pid='.session('isCopy').' and is_del=0')->select();
				foreach($dtData as $key=>$value){
					$dtData[$key]['pid'] = $newId;
				}
				M('trip')->addAll($dtData);
				session('isCopy',null);
			}
			//$this->success('添加成功',U('Line/index',array('id'=>$data['type_id'])));		
			$this->redirect('Line/index',array('id'=>$data['type_id']));
		}else{
			$sn = 'T'.date('ymdHis',time());
			$this->assign('sn',$sn);
			$lincCate=M('GoodsCate');		
			$result = $lincCate->order('ordid desc')->where("is_del=0")->select();
			$menu = arrToMenu($result,$id); 
			$this->assign('cate_list',$menu);
			
			if($is_zyx){
				$this->display('add_zyx');
			}else{
				$this->display();
			}			
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
			$data['is_zyx'] = $data['is_zyx']?$data['is_zyx']:0;
			//自由行则处理费用
			// if($data['is_zyx']){
				// $fees_in[0] = $_REQUEST['fee_in_0']?str_replace('|','',$_REQUEST['fee_in_0']):'';
				// $fees_in[1] = $_REQUEST['fee_in_1']?str_replace('|','',$_REQUEST['fee_in_1']):'';
				// $fees_in[2] = $_REQUEST['fee_in_2']?str_replace('|','',$_REQUEST['fee_in_2']):'';
				// $data['fees_in'] = implode('|',$fees_in);
				
				// $fees_out[0] = $_REQUEST['fee_out_0']?str_replace('|','',$_REQUEST['fee_out_0']):'';
				// $fees_out[1] = $_REQUEST['fee_out_1']?str_replace('|','',$_REQUEST['fee_out_1']):'';
				// $data['fees_out'] = implode('|',$fees_out);
			// }
			
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
			$fruit = $_REQUEST['Fruit'];
			$data['switch'] = json_encode($fruit);
			$apple = $_REQUEST['Apple'];
			$data['service'] = json_encode($apple);
			$goodsMod->where('id='.$id)->save($data);
			//$this->success('修改成功',U('Line/index',array('id'=>$tid)));
			//$this->redirect('Line/index',array('id'=>$tid));
			//$this->success('修改成功',$jumpUrl);

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
			
			if($data['is_zyx']){
				//自由行航班信息
				$flightData = M('goods_flight')->create();
				$sum = count($flightData['daytime']);
				for($i=0;$i<$sum;$i++){
					$fdata[$i]['pid'] = $flightData['pid'];
					$fdata[$i]['daytime'] = $flightData['daytime'][$i];
					$fdata[$i]['traffic_type'] = $flightData['traffic_type'][$i];
					$fdata[$i]['traffic_no'] = $flightData['traffic_no'][$i];
					$fdata[$i]['traffic_level'] = $flightData['traffic_level'][$i];
					$fdata[$i]['start_place'] = $flightData['start_place'][$i];
					$fdata[$i]['start_time'] = $flightData['start_time'][$i];
					$fdata[$i]['start_port'] = $flightData['start_port'][$i];
					$fdata[$i]['arrival_place'] = $flightData['arrival_place'][$i];
					$fdata[$i]['arrival_time'] = $flightData['arrival_time'][$i]; 
					$fdata[$i]['arrival_port'] = $flightData['arrival_port'][$i];							
					$fdata[$i]['ordid'] = $i+1;
					$fdata[$i]['is_del'] = 0;
				}
				M('goods_flight')->where('pid='.$flightData['pid'])->delete();
				M('goods_flight')->addAll($fdata);
				
				//自由行酒店信息
				$hotelData = M('goods_hotel')->create();
				$sum = count($hotelData['hotel_day']);
				for($i=0;$i<$sum;$i++){
					$hdata[$i]['pid'] = $hotelData['pid'];
					$hdata[$i]['hotel_day'] = $hotelData['hotel_day'][$i];
					$hdata[$i]['hotel_type'] = $hotelData['hotel_type'][$i];
					$hdata[$i]['hotel_id'] = $hotelData['hotel_id'][$i];
					$hdata[$i]['ordid'] = $i+1;
					$hdata[$i]['is_del'] = 0;
				}
				M('goods_hotel')->where('pid='.$hotelData['pid'])->delete();
				M('goods_hotel')->addAll($hdata);
			}
			
			//提交前清除SESSION
			SESSION('skeyword',0);
			SESSION('scate_id',0);
			
			header("Location:".$jumpUrl);
		}else{
			$jumpUrl = $_SERVER['HTTP_REFERER'];
			$jumpUrl =  str_replace(".html", "", $jumpUrl);	
			if(session('scate_id') && !strstr($jumpUrl,"scate_id")){
				$jumpUrl .= '/cate_id/'.session('scate_id');
			}
			if(session('skeyword') && !strstr($jumpUrl,"keyword")){
				$jumpUrl .='/keyword/'.session('skeyword');
			}
			//echo $jumpUrl;
//			echo $cate_id;
			$this->assign('jumpUrl',$jumpUrl);
			$goodsInfo = $goodsMod->where('id='.$id)->find();
			$goodsInfo['switch'] = json_decode($goodsInfo['switch']);
			$goodsInfo['service'] = json_decode($goodsInfo['service']);
			//当然线路分类
			$goodsInfo['cates'] = M('goods_cate_rela')->where('goods_id="'.$id.'"')->getfield('cate_id',true);			
			$goods_cate=M('GoodsCate');	
			$result = $goods_cate->order('ordid desc')->where("is_del=0")->select();
			$menu = arrToMenu($result,$goodsInfo['type_id']);
			$this->assign('cate_list',$menu);

			//新标签
			$goodsTagMod = M('goods_tag');
			$tag_arr = $goodsTagMod->join('33_tag as T on 33_goods_tag.tag_id=T.id')->field('T.name')->where('goods_id='.$id)->select();
			foreach ($tag_arr as $tag){
				$tags[] .=$tag['name'];
			}
			$goodsInfo['tags'] = implode(',', $tags);

			if($goodsInfo['is_zyx'] and ($goodsInfo['type_id']<>1 or $goodsInfo['tpl_id'] == 1)){
				
				//显示航班信息
				$flight_data = M('goods_flight')->where('pid='.$id)->order('ordid')->select();
				$this->assign('flight_data',$flight_data);
				
				//显示酒店信息
				$hotel_data = M('goods_hotel as gh')->join('33_hotel as h on h.hotel_id=gh.hotel_id')->where('gh.pid='.$id)->order('gh.ordid')->select();
				$this->assign('hotel_data',$hotel_data);

				//$goodsInfo['fees_in'] = explode('|',$goodsInfo['fees_in']);
				//$goodsInfo['fees_out'] = explode('|',$goodsInfo['fees_out']);
				$this->assign('goodsInfo',$goodsInfo);
				$this->display('edit_zyx');

			}else{
				
				$this->assign('goodsInfo',$goodsInfo);
				$this->display();
				
			}			
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
			if($srcInfo['is_zyx']){
				//显示航班信息
				$flight_data = M('goods_flight')->where('pid='.$srcInfo['id'])->order('ordid')->select();
				$this->assign('flight_data',$flight_data);
				
				//显示酒店信息
				$hotel_data = M('goods_hotel')->where('pid='.$srcInfo['id'])->order('ordid')->select();
				$this->assign('hotel_data',$hotel_data);

				$goodsInfo['fees_in'] = explode('|',$goodsInfo['fees_in']);
				$goodsInfo['fees_out'] = explode('|',$goodsInfo['fees_out']);
				$this->assign('goodsInfo',$srcInfo);
				$this->display('add_zyx');
			}else{
				$this->assign('goodsInfo',$srcInfo);
				$this->display('add');
			}
			
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
			$mod=M('goods');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id." and is_del=0")->save($data);
			}
			$jumpUrl = $_SERVER['HTTP_REFERER'];
			$jumpUrl =  str_replace(".html", "", $jumpUrl);	
			if(session('scate_id') && !strstr($jumpUrl,"scate_id")){
				$jumpUrl .= '/cate_id/'.session('scate_id');
			}
			if(session('skeyword') && !strstr($jumpUrl,"keyword")){
				$jumpUrl .='/keyword/'.session('skeyword');
			}
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

}

