<?php
set_time_limit(0); 
class IndexAction extends BaseAction{

	//后台首页
    public function index(){
    	$this->assign('loginname',$_COOKIE['loginname']);
    	$this->display();
    }
    
    //左侧导航栏
    public function left(){
		//身份 权限 
		//uid 唯一
		$uid = $_COOKIE['id'];
		$role_id = M('admin')->where('id='.$uid)->getfield('role_id');
		
		$role_id = $role_id?$role_id:99;
		//权限表
		$mlist = M('admin_auth')->where('role_id='.$role_id)->getfield('menu_id',true);//对应的menu_id list
		//echo $uid;
		$mod = M('admin_menu');
		$map_floor1['floor'] = 1;
		$map_floor1['is_del'] = 0;
		$map_floor1['id'] = array('in',$mlist);
		$map_floor1['is_show'] = 1;
		$data=$mod->where($map_floor1)->order('ordid desc')->select();

		foreach($data as $key=>$value){
			$map_floor2['pid'] = $value['id'];
			$map_floor2['id'] = array('in',$mlist);
			$map_floor2['is_show'] = 1;
			$list2 = $mod->where($map_floor2)->order('ordid desc')->select();
			$data[$key]['son'] = $list2;
		}
		//$menu = arrToMenu($data,0);

		$this->assign('menu_list',$data);
    	$this->display();
    }
    
    //系统环境信息
    public function main(){
    	$server_info = array(
    		'CMS版本'=>C('cms_versions'),
    		'操作系统'=>PHP_OS,
    		'运行环境'=>$_SERVER["SERVER_SOFTWARE"],
    		'PHP运行方式'=>php_sapi_name(),
    		'最大上传限制'=>ini_get('upload_max_filesize'),
    		'最大执行时间'=>ini_get('max_execution_time').'秒',
    		'服务器时间'=>date("Y年n月j日 H:i:s"),
    		//'北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
    		'服务器域名/IP'=>$_SERVER['SERVER_NAME'].' ['.gethostbyname($_SERVER['SERVER_NAME']).']',
    		'剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
    	);
    	$this->assign('server_info',$server_info);
    	$this->display();
    } 
    
    //更新缓存
    public function delcache(){
		//$this->getMinprice();
    	import("@.ORG.Dir");
    	$dir = new Dir;
    	@unlink(RUNTIME_PATH.'~runtime.php');
    	if(is_dir(RUNTIME_PATH.'Cache')){
    		$dir->delDir(RUNTIME_PATH.'Cache');
    	}
    	if(is_dir(RUNTIME_PATH.'Data')){
    		$dir->delDir(RUNTIME_PATH.'Data');
    	}
    	if(is_dir(RUNTIME_PATH.'Logs')){
    		$dir->delDir(RUNTIME_PATH.'Logs');
    	}
    	if(is_dir(RUNTIME_PATH.'Temp')){
    		$dir->delDir(RUNTIME_PATH.'Temp');
    	}
    	$this->ajaxReturn('清除成功','清除成功',1);
    }
	
	//清除session
	public function delsession(){
		session(null);
	}
	
	//导航管理
	public function navigation(){
		$nav_mod = M("Navigation");
		$act = $_GET['act'];
		switch($act){
			case "add":
				if(!$_POST['name']){
					$this->error('名称不能为空！');
				}else{
					$data['name']=$_POST['name'];
					$data['url']=$_POST['url'];
					$data['ord']=$_POST['ord'];
					$row=$nav_mod->add($data);
					if($row){
						$this->success('添加成功！');
					}
				}
				break;
			case "edit":
				$id=$_GET['id']?$_GET['id']:'';
				if($id){
					$data['name']=$_POST['name'][$id];
					$data['url']=$_POST['url'][$id];
					$data['ord']=$_POST['orders'][$id];
					$nav_mod->where('id='.$id)->save($data);
					$this->success('修改成功！');
				}else{
					$this->error('参数错误！');
				}				
				break;
			case "del":
				if (!isset($_POST['id'])){
					$this->error('请选择要删除的项目！');
				}
				$del_id = $_POST['id'];
				foreach ($del_id as $id){
					$nav_mod->where('id='.$id)->delete();
				}
				$this->success('删除成功！');				
				break;
			case "ord":
				if ($_POST['order']){
					foreach ($_POST['orders'] as $id => $ordid) {
						$data['ordid'] = $ordid;
						$nav_mod->where('id='.$id)->save($data);
					}
					$this->success('修改成功！');					
				}
				break;
			default:
				$nav_mod = $nav_mod->order("ordid desc")->select();
				$this->assign("navigation",$nav_mod);
				$this->display();
		}
	}
	
	//定时查看订单信息checkOrder
	public function checkOrder(){
		$mod = M('order');
		if(session('lastOrderId')){
			//echo 'ee'.session('lastOrderId');
			$newOrderId = $mod->order('id desc')->limit(1)->getfield('id');
			if($newOrderId > session('lastOrderId')){
				session('lastOrderId',$newOrderId);
				$this->ajaxReturn(0,0,2);
			}else{
				$this->ajaxReturn(0,0,3);
			}
			
		}else{
			$count = $mod->where('ordstatus=0')->count();
			$lastOrderId = $mod->order('id desc')->limit(1)->getfield('id');
			session('lastOrderId',$lastOrderId);
			$this->ajaxReturn($count,0,1);
		}
		
		
	}

}