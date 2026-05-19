<?php
class ArticleAction extends BaseAction{

	public function index(){
		$article=M('Article');
		$article_cate=M('ArticleCate');
		//搜索
		$where = 'is_del=0';
		if (isset($_POST['keyword']) && trim($_POST['keyword'])) {
			$where .= " AND title LIKE '%".$_POST['keyword']."%'";
			$this->assign('keyword', $_POST['keyword']);
		}
		if (isset($_POST['time_start']) && trim($_POST['time_start'])) {
			$time_start = strtotime($_POST['time_start']);
			$where .= " AND add_time>='".$time_start."'";
			$this->assign('time_start', $_POST['time_start']);
		}
		if (isset($_POST['time_end']) && trim($_POST['time_end'])) {
			$time_end =strtotime($_POST['time_end']) ;
			$where .= " AND add_time<='".$time_end."'";
			$this->assign('time_end', $_POST['time_end']);
		}
		if (isset($_POST['cate_id']) && intval($_POST['cate_id'])) {
			$where .= " AND cate_id=".$_POST['cate_id'];
			$this->assign('cate_id', $_POST['cate_id']);
		}
	
		//分页
		import("@.ORG.Page");
		$count=$article->where($where)->count();
		$page=new Page($count,10);
		$show=$page->show();
		$data=$article->where($where)->order('ordid desc,id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$i=0;
		foreach ($data as $val){
			$map['id']=$val['cate_id'];
			$map['is_del']=0;
			$articles[$i]=$val;
			$cate=$article_cate->field('name')->where($map)->find();
			$articles[$i]['cate_name']=$cate['name'];
			$articles[$i]['key']=$page->firstRow+$i+1;
			$i++;
		}
		
		//分类
		$result=$article_cate->order('ordid desc,id desc')->where("is_del=0 and status=1")->select();
		$this->assign('articles',$articles);
		$this->assign('cate_list',$result);
		$this->assign('page',$show);
		$this->display();
	}


	//添加
	public function add(){
		$article=M('Article');
		if ($_POST['submit']){
			$data=$article->create();
			$data['add_time'] = time();
			//上传图片
			if ($_FILES['img']['name'] != '') {
				mkdir('./Uploads/');
				$thumb=array('width'=>100,'height'=>1000);
				$upload_info = $this->upload('./Uploads/',$thumb);
				$data['img'] = '/Uploads/s_'. $upload_info['0']['savename'];
			}
			$article->add($data);
			$this->success('添加成功',U('Article/index'));
		}else{
			$article_cate=M('article_cate');
		
			$result = $article_cate->order('ordid desc,id desc')->where("is_del=0")->select();
			$this->assign('cate_list',$result);
			$this->display();		
		}

	}
	
	//修改
	public function edit(){
		$article=M('Article');
		$article_cate=M('article_cate');	
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		
		if($_POST['submit']){
			if ($_POST['title']==''){
				$this->error('标题不能为空！');
			}
			$data=$article->create();
			//上传图片
			if ($_FILES['img']['name'] != '') {
				mkdir('./Uploads/');
				$thumb=array('width'=>100,'height'=>1000);
				$upload_info = $this->upload('./Uploads/',$thumb);
				$data['img'] = '/Uploads/s_'. $upload_info['0']['savename'];
			}			
			if ($id){
				//var_dump($data);
				$article->where('id='.$id)->save($data);
				$this->success('修改成功',U('Article/index'));
			}else {
				$article->add($data);
				$this->success('修改成功',U('Article/index'));
			}
			
		}else {
			$result = $article_cate->order('ordid desc,id desc')->where("is_del=0")->select();
			$article_info = $article->where('id='.$id)->find();
			$this->assign('cate_list',$result);
			$this->assign('article',$article_info);
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
		$article_cate_mod=M('article_cate');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$article->where('id='.$id)->save($data);
			$cid=$article->where("id=$id")->getField("cate_id");
			$article_cate_mod->where("id=$cid and is_del=0")->setDec("article_nums");
		}
		$this->success('删除成功！');
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$article = M('Article');
			foreach ($_POST['orders'] as $id => $ordid) {
				$data['ordid'] = $ordid;
				$article->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}

	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$article = M('Article');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$article->where($data)->save($set);
		$val=$article->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}

}
	