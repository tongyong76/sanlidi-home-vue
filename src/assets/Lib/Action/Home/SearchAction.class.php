<?php 
class SearchAction extends BaseAction 
{
    public function index(){
		$goodsMod = M('goods');
		
		//处理关键词
		$keyword = trim($_REQUEST['keyword'])?trim($_REQUEST['keyword']):0;
		
		//通用参数
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		
		if($keyword){
		$keyword = htmlspecialchars(stip_tags($keyword));
		$this->assign('keyword',$keyword);
		$map['name|subname'] = array('like','%'.$keyword.'%');
		}
		
		$goodsList = $goodsMod->where($map)->select();

		$this->assign('goodsList',$goodsList);
		$this->display();
	}
	
}
