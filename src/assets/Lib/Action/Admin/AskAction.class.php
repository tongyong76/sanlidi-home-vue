<?php
class AskAction extends BaseAction{

	public function index(){
		$mod=M('ask');
		
		//搜索
		$where = 'is_del=0';
		if (isset($_POST['keyword']) && trim($_POST['keyword'])) {
			$where .= " AND ask LIKE '%".$_POST['keyword']."%'";
			$this->assign('keyword', $_POST['keyword']);
		}
	
		//分页
		import("@.ORG.Page");
		$count=$mod->where($where)->count();
		$page=new Page($count,10);
		$show=$page->show();
		$data=$mod->where($where)->order('answer asc,ordid desc,id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$data[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		
		//分类
		$this->assign('askList',$data);
		$this->assign('page',$show);
		$this->display();
	}


	//添加
	public function add(){
		$mod = M('Ask');
		$tagMod = M('tag');
		$askTagMod = M('ask_tag');
		if ($_POST['submit']){
			$data=$mod->create();
			$data['add_time'] = time();
			$newId = $mod->add($data);
			
			//处理标签
			$tags = isset($_POST['tags']) && trim($_POST['tags']) ? trim($_POST['tags']) : '';
			if ($tags) {
				//标签不存在则添加
				$tags_arr = explode(',', $_POST['tags']);
				$tags_arr = array_unique($tags_arr);
				foreach ($tags_arr as $tag) {
					$isset_id = $tagMod->where("name='".$tag."'")->getfield('id');
					if ($isset_id) {
						$askTagMod->add(array(
								'ask_id' => $newId,
								'tag_id' => $isset_id,
						));
						//$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
					} else {
						$tag_id = $tagMod->add(array('name' => $tag));
						$askTagMod->add(array(
								'ask_id' => $newId,
								'tag_id' => $tag_id
						));
						//$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
					}
				}
			}
			
			$this->success('添加成功',U('Ask/index'));
		}else{
			$this->display();		
		}

	}
	
	//修改
	public function edit(){
		$mod = M('Ask');
		$tagMod = M('tag');
		$askTagMod = M('ask_tag');
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		
		if($_POST['submit']){
			if ($_POST['ask']==''){
				$this->error('非法操作');
			}
			$data=$mod->create();		
			if ($id){
				$mod->where('id='.$id)->save($data);				
			}else {
				$id = $mod->add($data);
			}
			
			//处理标签
			$askTagMod->where("ask_id='".$id."'")->delete();//删除原有的
			$tags = isset($_POST['tags']) && trim($_POST['tags']) ? trim($_POST['tags']) : '';
			if ($tags) {
				//标签不存在则添加
				$tags_arr = explode(',', $_POST['tags']);
				$tags_arr = array_unique($tags_arr);
				foreach ($tags_arr as $tag) {
					$isset_id = $tagMod->where("name='".$tag."'")->getfield('id');
					if ($isset_id) {
						$askTagMod->add(array(
								'ask_id' => $id,
								'tag_id' => $isset_id,
						));
						//$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
					} else {
						$tag_id = $tagMod->add(array('name' => $tag));
						$askTagMod->add(array(
								'ask_id' => $id,
								'tag_id' => $tag_id
						));
						//$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
					}
				}
			}
			$this->success('修改成功',U('Ask/index'));
			
			
		}else {
			$askInfo = $mod->where('id='.$id)->find();
			$tag_arr = $askTagMod->join('33_tag as T on 33_ask_tag.tag_id=T.id')->field('T.name')->where('ask_id='.$id)->select();
			foreach ($tag_arr as $tag){
				$tags[] .=$tag['name'];
			}
			$askInfo['tags'] = implode(',', $tags);
			$this->assign('askInfo',$askInfo);
			$this->display();

		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$article = M('Article');
		$article_cate_mod=M('ArticleCate');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$article->where('id='.$id)->save($data);
			$cid=$article->where("id=$id")->getField("cate_id");
			$article_cate_mod->where("id=$cid and is_del=0")->setDec("article_nums");
		}
		//$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$mod = M('Ask');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$mod->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}

	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$mod = M('Ask');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$mod->where($data)->save($set);
		$val=$mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}

	public function upload($savePath,$thumb=array()) {
    
    	import("ORG.Net.UploadFile");
    	$upload = new UploadFile();
    	$upload->maxSize  = 2097152;// 设置附件上传大小
    	$upload->savePath = $savePath;// 设置附件上传目录
    	$upload->saveRule = uniqid;
    	$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

    	if ($thumb) {
	    	$upload->thumb = true;
	    	$upload->thumbMaxWidth = $thumb['width'];    	
	    	$upload->thumbMaxHeight = $thumb['height'];
	    	$upload->thumbPrefix = 's_';
	    	$upload->thumbRemoveOrigin = true;
    	}

    	if(!$upload->upload()) {
    		// 上传错误提示错误信息
    		$this->error($upload->getErrorMsg());
    	}else{
    		// 上传成功 获取上传文件信息
    		$info =  $upload->getUploadFileInfo();
    	}
    	return $info;
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
	