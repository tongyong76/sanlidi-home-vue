<?php 
class AskAction extends BaseAction 
{
    public function tags(){
		$id = $_REQUEST['id']?trim($_REQUEST['id']):1;
		
		//tagInfo 
		$tagInfo = M('tag')->where('id='.$id)->find();
		$this->assign('tagInfo',$tagInfo);
		
		//tagList
		import("@.ORG.Pagetag");
		$count =  M('ask_tag as t')->where('t.tag_id='.$id)->join('33_ask as a on t.ask_id=a.id')->count();
		$p = new Page($count, 10);
		$tagList = M('ask_tag as t')->where('t.tag_id='.$id)->join('33_ask as a on t.ask_id=a.id')->limit($p->firstRow . ',' . $p->listRows)->select();
		foreach($tagList as $key=>$value){
			$tagList[$key]['son'] = M('ask_tag as t')->where('t.ask_id='.$value['id'])->join('33_tag as tt on t.tag_id = tt.id')->select();
		}
		//分页式样
		$p->setConfig('last',"..".$p->totalPages);
		$p->setConfig('first',"1..");
		$p->setConfig('theme', '%first% %upPage%%linkPage% %downPage% %end%');
		$page = $p->show();	
		$this->assign('page',$page);
		$this->assign('tagList',$tagList);
		
		$this->display();
	}
	
	public function detail(){
		$id = $_REQUEST['id']?$_REQUEST['id']:1;
		$info = M('ask')->where('id='.$id)->find();
		//var_dump($info);
		$this->assign('info',$info);
		
		//标签
		$this->assign('tags',M('ask_tag')->where('ask_id='.$id)->select());
		
		//计数
		if($info){
			M('ask')->where('id='.$id)->setInc('count');
		}
		
		//首页左侧特价
		$tj = M('goods')->where('is_del=0 and is_show=1 and switch like "%2%"')->order('ordid desc')->limit(2)->select();
		foreach($tj as $key=>$value){
			switch($value['type_id']){
				case 1:
					$tj[$key]['pinyin'] = 'zhoubian';
					break;
				case 2:
					$tj[$key]['pinyin'] = 'guonei';
					break;
				case 3:
					$tj[$key]['pinyin'] = 'chujing';
					break;
			}
		}
		$this->assign('tj',$tj);
		
		//相关行程
		$this->assign('likeThis',$this->likeThis());
		
		//同类问题(目的地)
		
		
		
		$this->display();
	}
	
	public function question(){
		$this->display();
	}
	
	//相关行程
	public function likeThis(){
		$mod = M('goods');
		$map['is_del'] = 0;
		$map['is_hot'] = 1;
		$map['minprice'] = array('neq',0);
		$list = $mod->where($map)->order('add_time desc')->limit(4)->select();
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),45);
			switch($value['type_id']){
				case 1:
					$list[$key]['type'] = 'zhoubian';
					break;
				case 2:
					$list[$key]['type'] = 'guonei';
					break;
				case 3:
					$list[$key]['type'] = 'chujing';
					break;
				case 97:
					$list[$key]['type'] = 'group';
					break;
				case 326:
					$list[$key]['type'] = 'suzhou';
					break;
			}
		}
		return $list;
	}
	
}
