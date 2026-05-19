<?php
class TestAction extends Action{

	//获取所有菜单结构
	public function menu(){
		header("Content-Type:text/html; charset=UTF-8");
		
		//生成菜单结构
		
		//身份 权限 
		//uid 唯一
		$uid = $_COOKIE['id'];
		$role_id = M('admin')->where('id='.$uid)->getfield('role_id');
		
		//权限表
		$mlist = M('admin_auth')->where('role_id='.$role_id)->getfield('menu_id',true);//对应的menu_id list
		//echo $uid;
		$mod = M('admin_menu');
		$map_floor1['floor'] = 1;
		$map_floor1['is_del'] = 0;
		$map_floor1['id'] = array('in',$mlist);
		$data=$mod->where($map_floor1)->order('ordid desc')->select();

		foreach($data as $key=>$value){
			$map_floor2['pid'] = $value['id'];
			$map_floor2['id'] = array('in',$mlist);
			$list2 = $mod->where($map_floor2)->select();
			$data[$key]['son'] = $list2;
		}
		//$menu = arrToMenu($data,0);

		$this->assign('menu_list',$data);
		$this->display();
	}

}

