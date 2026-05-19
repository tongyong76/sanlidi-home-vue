<?php
class ShopAction extends BaseAction {
	
    public function view(){		
		
		$type = $_REQUEST['type'];
		switch($type){
			case 1:
				$map['shopview_cate'] = '周边游';
				break;
			case 2:
				$map['shopview_cate'] = '国内游';
				break;
			case 3:
				$map['shopview_cate'] = '出境游';
				break;
			case 4:
				$map['shopview_cate'] = '单项服务';
				break;
		}
		$this->assign('type',$type);
		$map['is_del'] = 0;
		$map['status'] = 1;
		$map['exp_time'] = array('gt',time());
		$list = M('shopview')->where($map)->order('ordid desc')->select();
		$this->assign('list',$list);
		$this->display();
		
    }
	
	public function detail(){
		
		if(isPhone()){
			$this->assign('isPhone',1);
		}
		$id = $_REQUEST['id'];
		$list = M('shopview_gallery')->where(array('shopview_id'=>$id))->order('ordid asc')->select();
		$this->assign('list',$list);
		$this->display();
		
	}
	
}