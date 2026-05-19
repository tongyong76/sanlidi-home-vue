<?php
class ArticleAction extends BaseAction {
    public function index(){
		$id = intval($_GET['id']);
		$this->assign('id',$id);
		$mod = M('article');
		
		$this->assign('hotList',$this->getNews(5));
		
		import("@.ORG.Page");
		$count = $mod->where('is_del=0 and cate_id='.$id)->count();		
		$page=new Page($count,15);
		$page->setConfig('first','首页');
		$page->setConfig('last','末页');
		$page->setConfig('theme','%first% %upPage% %prePage%  %linkPage%  %nextPage% %downPage% %end%');
		$show=$page->show();
		$arcList = $mod->field('id,title,add_time')->where('is_del=0 and cate_id='.$id)->order('ordid desc,add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('arcList',$arcList);
		$this->assign('page',$show);
		
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
		
        $this->display();
    }
	
	public function detail(){
		$id = $_REQUEST['id'];
		$info = M('article')->where('id='.$id)->find();
		$info['seo_desc'] = msubstr(strip_tags($info['info']),45);
		$this->assign('info',$info);
		
		$this->assign('rList',$this->getNews(6));
		$this->display();
	}
}