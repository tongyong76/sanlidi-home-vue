<?php
class VisaAction extends BaseAction {
    public function index(){
		$login_sign = session('login_from')?session('login_from'):0;
		$this->assign('login_sign',$login_sign);
		$pinyin = $_REQUEST['pinyin'];
		//$id = $_REQUEST['id'];
		$cateInfo = M('visa_cate')->where('pinyin="'.$pinyin.'"')->find();
		$this->assign('cateInfo',$cateInfo);
		$visaList = M('visa')->where('cate_id='.$cateInfo['id'].' and is_del=0')->select();
		$this->assign('visaList',$visaList);
		
		//获取相关线路（相关分类）
		$goodsCate = M('goods_cate');
		$goodsCateInfo = $goodsCate->where('pinyin like "%'.$pinyin.'%"')->find();
		$this->assign('goodsCateInfo',$goodsCateInfo);
		$sList = M('goods')->where('cate_id='.$goodsCateInfo['id'].' and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->limit(5)->select();
		foreach($sList as $key=>$value){
			switch($value['type_id']){
				case 1:
					$sList[$key]['cpinyin'] = 'zhoubian';
					break;
				case 2:
					$sList[$key]['cpinyin'] = 'guonei';
					break;
				case 3:
					$sList[$key]['cpinyin'] = 'chujing';
					break;
			}
		}
		$this->assign('sList',$sList);
		
		//签证问题
		$qList = M('article')->where('cate_id=4 and is_del=0')->select();
		$this->assign('qList',$qList);
        $this->display();
    }
	
	public function detail(){
		$login_sign = session('login_from')?session('login_from'):0;
		$this->assign('login_sign',$login_sign);
		$id = $_REQUEST['id'];
		$visaInfo = M('visa')->where('id='.$id)->find();
		$this->assign('visaInfo',$visaInfo);
		$cateInfo = M('visa_cate')->where('id='.$visaInfo['cate_id'])->find();
		$this->assign('cateInfo',$cateInfo);
		
		//签证问题
		$qList = M('article')->where('cate_id=4 and is_del=0')->select();
		$this->assign('qList',$qList);
		$this->display();
	}
}