<?php
class ScenicAction extends BaseAction{
	
	public function index(){
		$mod = M('scenic');
		$cate_mod=M('ScenicCate');
		//搜索
		$where = 'is_del=0';
		$order = isset($_REQUEST['order']) && trim($_REQUEST['order']) ? trim($_REQUEST['order']) : '';
		$sort = isset($_REQUEST['sort']) && trim($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'desc';
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$where .= " AND name LIKE '%".$_REQUEST['keyword']."%'";
			$this->assign('keyword', $_REQUEST['keyword']);
		}
		if (isset($_REQUEST['cate_id']) && intval($_REQUEST['cate_id'])) {
			$arr = $cate_mod->select();
			$cate_arr = getEndChild($arr,$_REQUEST['cate_id']);
			if($cate_arr){
				$where .= " AND cate_id in (".implode(",",$cate_arr).")";
			}else{
				$where .= " AND cate_id=".$_REQUEST['cate_id'];
			}
			$this->assign('cate_id', $_REQUEST['cate_id']);
		}
		
		
		//排序功能
		if ($sort=='desc'){
			$sort='asc';
		}elseif ($sort=='asc'){
			$sort='desc';
		}
		$this->assign('order',$order);
		$this->assign('sort', $sort);
		$order_str = 'status,ordid desc'; //默认排序

		//分页
		//import("@.ORG.Page");
		import("ORG.Util.Page");
		$count=$mod->where($where)->count();
		$page=new Page($count,30);
		foreach($where as $key=>$val) {
			$page->parameter   .=   "$key=".urlencode($val).'&';
		}
		$page->parameter = "";
		if($_REQUEST['cate_id']) $page->parameter .= "cate_id=".$_REQUEST['cate_id'].'&';
		if($_REQUEST['keyword']) $page->parameter .= "keyword=".$_REQUEST['keyword'].'&';
		if($_REQUEST['p']) $page->parameter .= "p=".$_REQUEST['p'].'&';
		//echo $page->parameter;
		$show=$page->show();
		$data=$mod->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$map['id']=$val['cate_id'];
			$map['is_del']=0;
			$data[$i]=$val;
			$cate=$cate_mod->field('name')->where($map)->find();
			$data[$i]['cate_name']=$cate['name'];
			$data[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		//品牌

		//分类
		$result=$cate_mod->order('ordid,id desc')->where("is_del=0")->select();
		$menu = arrToMenu($result,0); 
		$this->assign('cate_list',$menu);
		$this->assign('scenicList',$data);
		$this->assign('page',$show);
		$this->display();
	}
	
	//添加
	public function add(){
		$mod=M('scenic');
		$tagMod = M('tag');
		$scenicTagMod = M('scenic_tag');
		if ($_POST['submit']){
			
			$data = $mod->create();
			$data['name'] = strip_tags($data['name']);
			$data['floor'] = $mod->where('id='.$data['pid'])->getField('floor')+1;
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/scenic/');
				$thumb=array('width'=>800,'height'=>800);
				$upload_info = $this->upload('./Uploads/scenic/',$thumb);
				$data['imgurl'] = '/Uploads/scenic/s_'. $upload_info['0']['savename'];
			}
			$row = $mod->add($data);
			
			if ($row){
				
				//处理标签
				$tags = isset($_POST['tags']) && trim($_POST['tags']) ? trim($_POST['tags']) : '';
				if ($tags) {
					//标签不存在则添加
					$tags_arr = explode(',', $_POST['tags']);
					$tags_arr = array_unique($tags_arr);
					foreach ($tags_arr as $tag) {
						$isset_id = $tagMod->where("name='".$tag."'")->getfield('id');
						if ($isset_id) {
							$scenicTagMod->add(array(
									'scenic_id' => $row,
									'tag_id' => $isset_id,
							));
							//$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
						} else {
							$tag_id = $tagMod->add(array('name' => $tag));
							$scenicTagMod->add(array(
									'scenic_id' => $row,
									'tag_id' => $tag_id
							));
							//$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
						}
					}
				}				
				
				$this->success('添加成功！',U('Scenic/index'));
			}else {
				$this->error($mod->getError());
			}
			
		}else {
			
			$data = $mod->order('ordid desc,id desc')->where("is_del=0 and floor=1")->select();
			//$menu = arrToMenu($data,0); 
			$this->assign('cates_list',$data);
			$this->display();
		}	
	}
	
	//修改
	public function edit(){
		
		$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$mod = M('scenic');
		$tagMod = M('tag');
		$scenicTagMod = M('scenic_tag');
		$cate_mod = M('scenic_cate');
		
		if ($_POST['submit']){
			$data = $mod->create();
			$data['name'] = strip_tags($data['name']);
			$data['floor'] = $mod->where('id='.$data['pid'])->getField('floor')+1;
			//上传图片
			if ($_FILES['imgurl']['name'] != '') {
				mkdir('./Uploads/scenic/');
				$thumb=array('width'=>800,'height'=>800);
				$upload_info = $this->upload('./Uploads/scenic/',$thumb);
				$data['imgurl'] = '/Uploads/scenic/s_'. $upload_info['0']['savename'];
			}
			$save = $mod->where('id='.$data['id'])->save($data);
			
			//处理标签
			$scenicTagMod->where("scenic_id='".$id."'")->delete();//删除原有的
			$tags = isset($_POST['tags']) && trim($_POST['tags']) ? trim($_POST['tags']) : '';
			if ($tags) {
				//标签不存在则添加
				$tags_arr = explode(',', $_POST['tags']);
				$tags_arr = array_unique($tags_arr);
				foreach ($tags_arr as $tag) {
					$isset_id = $tagMod->where("name='".$tag."'")->getfield('id');
					if ($isset_id) {
						$scenicTagMod->add(array(
								'scenic_id' => $id,
								'tag_id' => $isset_id,
						));
						//$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
					} else {
						$tag_id = $tagMod->add(array('name' => $tag));
						$scenicTagMod->add(array(
								'scenic_id' => $id,
								'tag_id' => $tag_id
						));
						//$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
					}
				}
			}
			
			//$this->success('修改成功！',U('Scenic/index'));
			echo "<script>alert('修改成功');window.close();</script>";
		}else {
			if ($id==''){
				$this->error('请选择分类！');
			}
			$info = $mod->where('id='.$id)->find();	
			$tag_arr = $scenicTagMod->join('33_tag as T on 33_scenic_tag.tag_id=T.id')->field('T.name')->where('scenic_id='.$id)->select();
			foreach ($tag_arr as $tag){
				$tags[] .=$tag['name'];
			}
			$info['tags'] = implode(',', $tags);				
			$this->assign('info',$info);
			$list = $cate_mod->order('ordid,id desc')->where("is_del=0 and id <>".$id)->select();
			$menu = arrToMenu($list,0); 
			$this->assign('cates_list',$menu);
			$this->display();	
		}		
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$cate_mod=M('scenic');
		$article_mod=M("Article");
		foreach ($del_id as $id){
			$cate_mod->where('id='.$id." and is_del=0")->setField('is_del',1);
			$article_mod->where("cate_id=$id and is_del=0")->setField("is_del",1);
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$cate_mod=M('scenic');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$cate_mod->where('id='.$id." and is_del=0")->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$article_cate = M('scenic');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$article_cate->where($data)->save($set);
		$val=$article_cate->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//批量修改分类moveTo
	public function moveTo(){
		
	}
}