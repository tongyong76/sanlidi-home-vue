<?php 
class ThemeAction extends BaseAction{
    public function ceb() {
		$this->display();
	}
	
	public function lingshan(){
		$sid = $_REQUEST['sid'];
		$aid = $_REQUEST['aid'];
		
		if($sid){
			$sidInfo = M('scenic')->where('id='.$sid)->find();
			$sidInfo['preId'] = M('scenic')->where('id<'.$sid.' and cate_id=9')->order('id desc')->getfield('id');
			$sidInfo['preName'] = M('scenic')->where('id<'.$sid.' and cate_id=9')->order('id desc')->getfield('name');
			$sidInfo['nextId'] = M('scenic')->where('id>'.$sid.' and cate_id=9')->order('id asc')->getfield('id');
			$sidInfo['nextName'] = M('scenic')->where('id>'.$sid.' and cate_id=9')->order('id asc')->getfield('name');
			$this->assign('sidInfo',$sidInfo);
			$this->assign('type','scenic');
			
			$sidRela = M('scenic')->where('id<>'.$sid.' and id<>'.$sidInfo['preId'].' and id<>'.$sidInfo['nextId'].' and cate_id=9')->limit(4)->select();
			$this->assign('sidRela',$sidRela);
			
		}elseif($aid){
			$aidInfo = M('article')->where('id='.$aid)->find();
			$aidInfo['preId'] = M('article')->where('id<'.$aid.' and cate_id=7')->order('id desc')->getfield('id');
			$aidInfo['preName'] = M('article')->where('id<'.$aid.' and cate_id=7')->order('id desc')->getfield('title');
			$aidInfo['nextId'] = M('article')->where('id>'.$aid.' and cate_id=7')->order('id asc')->getfield('id');
			$aidInfo['nextName'] = M('article')->where('id>'.$aid.' and cate_id=7')->order('id asc')->getfield('title');
			$this->assign('aidInfo',$aidInfo);
			$this->assign('type','article');
			
			$aidRela = M('article')->where('id<>'.$aid.' and id<>'.$aidInfo['preId'].' and id<>'.$aidInfo['nextId'].' and cate_id=7')->limit(4)->select();
			$this->assign('aidRela',$aidRela);
		}else{
			//景点
			$scenicList = M('scenic')->where('cate_id=9 and is_del=0')->order('ordid desc')->limit(25)->select();
			$this->assign('scenicList',$scenicList);
			//图文
			$arcData = M('article')->field('id,title')->where('cate_id=7 and is_del=0')->select();
			foreach($arcData as $key=>$value){
				$arcList[$value['id']] = $value;
			}
			$this->assign('arcList',$arcList);
			
			$this->assign('type','list');
		}	
		$this->display();
	}
	
	public function passport(){
		$this->display();
	}
	
	public function sanjijin(){
		$this->display();
	}
	
	public function nianhui(){
		$this->display();
	}
	
	//专题-团队游
	public function tuandui(){
		$slist = M('goods')->where('(brand_id=1 or brand_id=3) and is_show=1 and is_del=0 and minprice<>0')->order('ordid desc,add_time desc')->select();
		foreach($slist as $key=>$value){
			$slist[$key]['scenic'] = M('trip')->field('scene')->where('pid='.$value['id'])->limit(3)->select();
			$sindex = 0;
			$slist[$key]['scenicall'] = '';
			foreach($slist[$key]['scenic'] as $skey=>$svalue){
				if(!$sindex){
					$sindex++;
					$slist[$key]['scenicall'] .= $svalue['scene'];
				}else{
					$slist[$key]['scenicall'] .= ','.$svalue['scene'];
				}
			}
			$slist[$key]['dep'] = $this->getDeparture($value['id'],1);
			switch($value['type_id']){
				case 1:
					$slist[$key]['pinyin'] = 'zhoubian';
					break;
				case 2:
					$slist[$key]['pinyin'] = 'guonei';
					break;
				case 3:
					$slist[$key]['pinyin'] = 'chujing';
					break;
			}
		}
		$this->assign('slist',$slist);
		
		//1天汇总
		$daylist1 = M('goods')->where('type_id = 97 and days=1 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->select();
		foreach($daylist1 as $key=>$value){
			$daylist1[$key]['scenic'] = M('trip')->field('scene')->where('pid='.$value['id'])->limit(3)->select();
			$daylist1[$key]['switch'] = json_decode($value['switch']);
		}
		$this->assign('daylist1',$daylist1);
		
		//2天汇总
		$daylist2 = M('goods')->where('type_id = 97 and days=2 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->select();
		foreach($daylist2 as $key=>$value){
			$daylist2[$key]['scenic'] = M('trip')->field('scene')->where('pid='.$value['id'])->limit(3)->select();
			$daylist2[$key]['switch'] = json_decode($value['switch']);
		}		
		$this->assign('daylist2',$daylist2);
		
		//3天汇总
		$daylist3 = M('goods')->where('type_id = 97 and days=3 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->select();
		foreach($daylist3 as $key=>$value){
			$daylist3[$key]['scenic'] = M('trip')->field('scene')->where('pid='.$value['id'])->limit(3)->select();
			$daylist3[$key]['switch'] = json_decode($value['switch']);
		}
		$this->assign('daylist3',$daylist3);
		
		//高铁汇总
		$daylist4 = M('goods')->where('type_id = 97 and (name like "%双高%" or subname like "%双高%") and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->select();
		foreach($daylist4 as $key=>$value){
			$daylist4[$key]['scenic'] = M('trip')->field('scene')->where('pid='.$value['id'])->limit(3)->select();
			$daylist4[$key]['switch'] = json_decode($value['switch']);
		}
		
		$this->assign('daylist4',$daylist4);
		$this->display();
	}
	
	//专题-羊春聚惠
	public function ycjh(){
		$mod = M('goods');
		//周边
		$zbList = $mod->where('type_id=1 and is_del=0 and is_show=1 and id<>587 and switch like "%2%"')->select();
		foreach($zbList as $key=>$value){
			$zbList[$key]['info'] = msubstr(strip_tags($value['info']),120);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$zbList[$key]['dep'] = $this->getDeparture($value['id'],1);
			$zbList[$key]['switch'] = json_decode($value['switch']);
		}
		$this->assign('zbList',$zbList);
		//国内
		$gnList = $mod->where('type_id=2 and is_del=0 and is_show=1 and id<>587 and switch like "%2%"')->select();
		foreach($gnList as $key=>$value){
			$gnList[$key]['info'] = msubstr(strip_tags($value['info']),120);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$gnList[$key]['dep'] = $this->getDeparture($value['id'],1);
			$gnList[$key]['switch'] = json_decode($value['switch']);
		}
		$this->assign('gnList',$gnList);
		//出境
		$cjList = $mod->where('type_id=3 and is_del=0 and is_show=1 and id<>587 and switch like "%2%"')->select();
		foreach($cjList as $key=>$value){
			$cjList[$key]['info'] = msubstr(strip_tags($value['info']),120);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$cjList[$key]['dep'] = $this->getDeparture($value['id'],1);
			$cjList[$key]['switch'] = json_decode($value['switch']);
		}
		$this->assign('cjList',$cjList);
		$this->display();
	}
	
	public function korea(){
		$mod = M('goods');
		$topTwo = $mod->where('id=89 or id=90')->order('id')->select();
		foreach($topTwo as $key=>$value){
			$topTwo[$key]['info'] = msubstr(strip_tags($value['info']),80);
		}
		$this->assign('topTwo',$topTwo);
		$topList = $mod->where('cate_id=40 and is_del=0 and is_show=1 and id<>89 and id<>90 and minprice<>0')->limit(8)->select();
		foreach($topList as $key=>$value){
			$topList[$key]['info'] = msubstr(strip_tags($value['info']),80);
		}
		$this->assign('topList',$topList);
		$this->display();
	}
	
	public function chunjie(){
		$mod = M('goods');
		//韩国 40
		$floor[1] = $mod->where('cate_id=40 and switch like "%6%"')->order('ordid desc')->limit(3)->select();
		foreach($floor[1] as $key=>$value){
			$floor[1][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//东南亚 36,37,38,39,240,262,172,70
		$floor[2] = $mod->where('cate_id in (36,37,38,39,240,262,172,70) and switch like "%6%"')->order('ordid desc')->limit(3)->select();
		foreach($floor[2] as $key=>$value){
			$floor[2][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//日本 41
		$floor[3] = $mod->where('cate_id=41 and switch like "%6%"')->order('ordid desc')->limit(3)->select();
		foreach($floor[3] as $key=>$value){
			$floor[3][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//澳洲 171,54,53
		$floor[4] = $mod->where('cate_id in (171,54,53) and switch like "%6%"')->order('ordid desc')->limit(3)->select();
		foreach($floor[4] as $key=>$value){
			$floor[4][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//海岛 187,188,191,195
		$floor[5] = $mod->where('cate_id in (187,188,191,195) and switch like "%6%"')->order('ordid desc')->limit(3)->select();
		foreach($floor[5] as $key=>$value){
			$floor[5][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//巴厘岛 188
		$floor[6] = $mod->where('cate_id=188 and switch like "%6%"')->order('ordid desc')->limit(3)->select();
		foreach($floor[6] as $key=>$value){
			$floor[6][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//海南三亚 55,56
		$floor[7] = $mod->where('cate_id in (55,56) and switch like "%6%"')->order('ordid desc')->limit(3)->select();
		foreach($floor[7] as $key=>$value){
			$floor[7][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//云南丽江 162,161,165
		$floor[8] = $mod->where('cate_id in (162,161,165) and switch like "%6%"')->order('ordid desc')->limit(3)->select();		
		foreach($floor[8] as $key=>$value){
			$floor[8][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//北京 301
		$floor[9] = $mod->where('cate_id=301 and switch like "%6%"')->order('ordid desc')->limit(3)->select();
		foreach($floor[9] as $key=>$value){
			$floor[9][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		//桂林 160
		$floor[10] = $mod->where('cate_id=160 and switch like "%6%"')->order('ordid desc')->limit(3)->select();		
		foreach($floor[10] as $key=>$value){
			$floor[10][$key]['dep'] = $this->getDeparture($value['id'],1);
		}		
		//哈尔滨 232,233,234,235
		$floor[11] = $mod->where('cate_id in (232,233,234,235) and switch like "%6%"')->order('ordid desc')->limit(3)->select();		
		foreach($floor[11] as $key=>$value){
			$floor[11][$key]['dep'] = $this->getDeparture($value['id'],1);
		}
		
		$this->assign('floor',$floor);
		$this->display();
	}
	
	public function chunjie2016(){
		$mod = M('goods');
		$map['is_del'] = 0;
		$map['is_show'] = 1;
		$map['minprice'] = array('neq',0);
		$map['switch'] = array('like',"%6%");
		
		//国内
		$map1 = $map;
		$map1['type_id'] = 2;
		$gnList = $mod->where($map1)->limit(5)->order('ordid desc')->select();
		$this->assign('gnList',$gnList);
		
		//国内
		$map2 = $map;
		$map2['type_id'] = 3;
		$cjList = $mod->where($map2)->limit(5)->order('ordid desc')->select();
		$this->assign('cjList',$cjList);
				
		$this->display();
	}
	
	public function zhaopin(){
		if($_POST['submit']){
			$mod = M('zhaopin');
			$data = $mod->create();
			$newId = $mod->add($data);
			switch($data['sex']){
				case 1:
					$sex = '男';
					break;
				case 2:
					$sex = '女';
					break;
			}
			if($newId){
				$address = 'zhaopin@33ly.com';
				$address2 = '76597304@qq.com';
				$title = $data['name'].'应聘'.C('job.'.$data['job']).',性别'.$sex.',学历'.C('level.'.$data['level']).',手机号码'.$data['phone'];
				$message = $data['info'];
				SendMail($address,$title,$message);
				SendMail($address2,$title,$message);
				$this->success('您的申请已提交，请耐心等待。');
				
			}else{
				$this->error('非法操作');
			}
		}else{			
			$this->display();
		}
	}
	
	public function shiwangzhengba(){
		$this->display();
	}
	
	public function diaoyangshenghui(){
		$this->display();
	}
	
	public function poshuijie(){
		$this->display();
	}
	
	public function sanxiayoulun(){
		$this->display();
	}
	
	public function lvyoujie(){
		$mod = M('goods');
		$map['is_show'] = 1;
		$map['is_del'] = 0;
		$map['minprice'] = array('neq',0);
		
		$topInfo = $mod->where('id=1087')->find();
		$topInfo['dep'] = $this->getDeparture($topInfo['id'],1,4);
		$this->assign('topInfo',$topInfo);
		
		//出境游
		$map1 = $map;
		$map1['type_id'] = 3;
		$map1['id'] = array('neq',1087);
		$cjList = $mod->where($map1)->order('ordid desc')->limit(5)->select();
		$this->assign('cjList',$cjList);
		
		//国内游
		$map2 = $map;
		$map2['type_id'] = 2;
		$gnList = $mod->where($map2)->order('ordid desc')->limit(5)->select();
		$this->assign('gnList',$gnList);
		
		//周边游
		$map3 = $map;
		$map3['type_id'] = 1;
		$zbList = $mod->where($map3)->order('ordid desc')->limit(8)->select();
		$this->assign('zbList',$zbList);
		
		
		$this->display();
	}
	
	public function tianmuhu(){
		$this->display();
	}
	
	public function tachun(){
		$arr = array(8981,8983,8992,8993,8989,8991,8990,8986,8987,8988);
		$map['id'] = array('in',$arr);
		$map['is_del'] = 0;
		$map['minprice'] = array('neq',0);
		$map['is_show'] = 1;
		$list = M('goods')->where($map)->select();
		foreach($list as $key=>$value){
			$newList[$value['id']] = $value;
		}
		$this->assign('list',$newList);
		$this->display();
	}
	
	public function ribenqianzheng(){
		$map['is_del'] = 0;
		$map['minprice'] = array('neq',0);
		$map['is_show'] = 1;
		
		$map1 = $map;
		$map1['id'] = array('in',array(11995,8982,11708,11993));
		$zyxlist = M('goods')->where($map1)->order('ordid desc')->select();
		foreach($zyxlist as $key=>$value){
			$zyxlist[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$zyxlist[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$zyxlist[$key]['dep'] = $this->getDeparture($value['id'],1);
			$zyxlist[$key]['switch'] = json_decode($value['switch']);
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 1)){
				$zyxlist[$key]['is_zzt'] = 1;
			}
		}
		$this->assign('zyxlist',$zyxlist);
		
		$map2 = $map;
		$map2['id'] = array('in',array(11993,317,375,11708,10552,1762,12991,12992,11996));
		$gtlist = M('goods')->where($map2)->select();
		foreach($gtlist as $key=>$value){
			$gtlist[$key]['info'] = msubstr(strip_tags($value['info']),30);
		}
		$this->assign('gtlist',$gtlist);
		
		$this->display();
	}
	
	public function yiyuanqianzheng(){
		$this->display();
	}
	
	public function qingming(){
		$arr = array(8963,12982,133,12984,12985,8938,920,12986,605,8989,12987,8945,12990,12988,807,8992);//12989
		$map['id'] = array('in',$arr);
		$map['is_del'] = 0;
		$map['minprice'] = array('neq',0);
		$map['is_show'] = 1;
		$list = M('goods')->field('id,name,subname,imgurl,days,minprice,info')->where($map)->order('ordid desc')->select();
		foreach($list as $key=>$value){
			$newList[$value['days']][] = $value;
		}
		$this->assign('list',$newList);
		$this->display();
	}
	
	public function wuyue(){
		$arr = array(219,12994,13043,1097,1024,8989,8991,12988,807,13046,13048,13003,13050,133,13045,94,8938,605,8992,144,13053,13051,13052);
		$map['id'] = array('in',$arr);
		$map['is_del'] = 0;
		$map['minprice'] = array('neq',0);
		$map['is_show'] = 1;
		$list = M('goods')->field('id,name,subname,imgurl,days,minprice,info')->where($map)->order('ordid desc')->select();
		foreach($list as $key=>$value){
			$value['dep'] = $this->getDeparture($value['id'],$value['sign_up'],2);
			$newList[$value['days']][] = $value;
		}
		$this->assign('list',$newList);
		
		$this->display();
	}
	
	public function piaoliu(){
		$tagName = '漂流';
		$list = M('tag as t')
		->field('g.id,g.name,g.subname,g.imgurl,g.days,g.minprice,g.info')
		->join('33_goods_tag as gt on gt.tag_id=t.id')
		->join('33_goods as g on g.id=gt.goods_id')
		->where('t.name="'.$tagName.'" and g.is_del=0 and g.minprice<>0 and g.is_show=1')
		->select();
		
		foreach($list as $key=>$value){
			$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up'],2);
		}
		
		$this->assign('list',$list);
		$this->display();
	}
	
	public function wanshui(){
		$tagName = '漂流';
		$list = M('tag as t')
		->field('g.id,g.name,g.subname,g.imgurl,g.days,g.minprice,g.info')
		->join('33_goods_tag as gt on gt.tag_id=t.id')
		->join('33_goods as g on g.id=gt.goods_id')
		->where('t.name="'.$tagName.'" and g.is_del=0 and g.minprice<>0 and g.is_show=1')
		->select();
		
		foreach($list as $key=>$value){
			$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up'],2);
		}
		
		$this->assign('list',$list);
		$this->display();
	}
}