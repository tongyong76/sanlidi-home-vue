<?php
class ThemeAction extends BaseAction {
	private $wx_appid = 'wx9f98139e202c7003';
	private $wx_appsecret = 'fc569bb8896f29b5269fe49169d0c77a';	
	
	public function index(){
		
    }

    public function kangzhanshengli(){
		$map1['id'] = array('in',array(594));
		$list1 = M('goods')->where($map1)->select();
		$this->assign('list1',$list1);
		
		$map2['id'] = array('in',array(587,851,853));
		$list2 = M('goods')->where($map2)->select();
		$this->assign('list2',$list2);
		$this->display();
	}
	
	public function sales(){
		$arr1 = array(1024,1026,601,1027,350,1028);
		$map1['id'] = array('in',$arr1);
		$map1['is_del'] = 0;
		$map1['minprice'] = array('neq',0);
		$list1 = M('goods')->where($map1)->select();
		$this->assign('list1',$list1);
		
		$arr2 = array(587,983,1025,1029,1030,355);
		$map2['id'] = array('in',$arr2);
		$map2['is_del'] = 0;
		$map2['minprice'] = array('neq',0);
		$list2 = M('goods')->where($map2)->select();
		$this->assign('list2',$list2);
		$this->display();
	}
	
	//lvyoujie1
	// public function lvyoujie(){
		// $mod = M('goods');
		// $map['is_show'] = 1;
		// $map['is_del'] = 0;
		// $map['minprice'] = array('neq',0);
		
		// //出境游
		// $map1 = $map;
		// $map1['type_id'] = 3;
		// $cjList = $mod->where($map1)->order('ordid desc')->limit(5)->select();
		// $this->assign('cjList',$cjList);
		
		// //国内游
		// $map2 = $map;
		// $map2['type_id'] = 2;
		// $gnList = $mod->where($map2)->order('ordid desc')->limit(5)->select();
		// $this->assign('gnList',$gnList);
		
		// //周边游
		// $map3 = $map;
		// $map3['type_id'] = 1;
		// $zbList = $mod->where($map3)->order('ordid desc')->limit(8)->select();
		// $this->assign('zbList',$zbList);
		
		// $this->display();
	// }
	
	public function lvyoujie(){
		
		//js-sdk
		import("@.ORG.Jssdk");
		$jssdk = new JSSDK($this->wx_appid,$this->wx_appsecret);
		$signPackage = $jssdk->GetSignPackage();
		$this->assign('signPackage',$signPackage);
		
		//分享信息
		$share['title'] = '9/9-10/10第2届三三旅游节震撼来袭';
		$share['desc'] = '三三旅游节：1599起全民游长白山，三三包机、浦东直飞、宿万达小镇4星酒店！';
		$share['link'] = 'http://m.33ly.com/Theme/lvyoujie';
		$share['icon'] = 'http://m.33ly.com/Public/images/lvyoujie/lvyoujielogo.png';    //分享图标
		//$share['wx_openid'] = $wx_openid;
		$this->assign('share',$share);
		
		//跟新计数
		M('variable')->where(array('key'=>'game_lyj2'))->setInc('count');
		
		$this->display();
	}
	
	public function tachun(){
		$arr = array(8983,8992,8993,8989,8991,8990,8986,8987,8988);
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
	
	public function tianmuhu(){
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
		$arr = array(219,12994,13043,1097,13028,12993,1025,8989,8991,12988,807,13046,13048,13003,13050,133,13045,94,8938,605,8992,144,13053,13051,13052);
		$map['id'] = array('in',$arr);
		$map['is_del'] = 0;
		$map['minprice'] = array('neq',0);
		$map['is_show'] = 1;
		$list = M('goods')->field('id,name,subname,imgurl,days,minprice,info')->where($map)->order('ordid desc')->select();
		foreach($list as $key=>$value){
			$value['dep'] = $this->getDeparture($value['id'],$value['sign_up'],3);
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
	
	//温泉玩雪
	public function wenquanhuaxue(){
		$tag1 = '温泉';
		$tag2 = '滑雪';
		$list = M('tag as t')
		->field('g.id,g.type_id,g.name,g.subname,g.imgurl,g.days,g.minprice,g.info')
		->join('33_goods_tag as gt on gt.tag_id=t.id')
		->join('33_goods as g on g.id=gt.goods_id')
		->where('t.name in("'.$tag1.'","'.$tag2.'") and g.is_del=0 and g.minprice<>0 and g.is_show=1')
		->select();
		
		foreach($list as $key=>$value){
			$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up'],2);
			switch($value['type_id']){
				case 1:
					$nlist['zhoubian'][] = $list[$key];
					break;
				case 2:
					$nlist['guonei'][] = $list[$key];
					break;
				case 3:
					$nlist['chujing'][] = $list[$key];
					break;
			}
		}
		
		$this->assign('list',$nlist);
		$this->display();
	} 
	
	public function chunjie(){
		$startTime = "2017-01-18";
		$endTime = "2017-02-13";
		$list = getGoodsByTime($startTime,$endTime);
		
		foreach($list as $key=>$value){
			$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up'],2);
			switch($value['type_id']){
				case 1:
					$nlist['zhoubian'][] = $list[$key];
					break;
				case 2:
					$nlist['guonei'][] = $list[$key];
					break;
				case 3:
					$nlist['chujing'][] = $list[$key];
					break;
			}
		}
		
		$this->assign('list',$nlist);
		$this->display();
	}
	
	public function ziyouxing(){
		$list = M('goods')->where('is_del=0 and minprice<>0 and is_show=1 and name like "%自由行%"')->select();
		
		foreach($list as $key=>$value){
			$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up'],2);
			switch($value['type_id']){
				case 1:
					$nlist['zhoubian'][] = $list[$key];
					break;
				case 2:
					$nlist['guonei'][] = $list[$key];
					break;
				case 3:
					$nlist['chujing'][] = $list[$key];
					break;
			}
		}
		
		$this->assign('list',$nlist);
		$this->display();
	}

	public function tag(){
		$tagName = $_REQUEST['tag'];
		$this->assign('tag',$tagName);
		$list = M('tag as t')
		->field('g.id,g.type_id,g.name,g.subname,g.imgurl,g.days,g.minprice,g.info')
		->join('33_goods_tag as gt on gt.tag_id=t.id')
		->join('33_goods as g on g.id=gt.goods_id')
		->where('t.name="'.$tagName.'" and g.is_del=0 and g.minprice<>0 and g.is_show=1')
		->select();
		
		foreach($list as $key=>$value){
			$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up'],2);
			switch($value['type_id']){
				case 1:
					$nlist['zhoubian'][] = $list[$key];
					break;
				case 2:
					$nlist['guonei'][] = $list[$key];
					break;
				case 3:
					$nlist['chujing'][] = $list[$key];
					break;
			}
		}
		
		$this->assign('list',$nlist);
		$this->display();
	}
	
	public function zizutuan(){
		
		$map['brand_id|switch'] = array(array('in',array(1,2,3,4)),array('like','%1%'),'_multi'=>true);
		$map['is_del'] = 0;
		$map['minprice'] = array('neq',0);
		$map['is_show'] = 1;
		$list = M('goods')->field('id,name,subname,type_id,imgurl,days,minprice,info')->where($map)->select();
		
		foreach($list as $key=>$value){
			$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up'],2);
			switch($value['type_id']){
				case 1:
					$nlist['zhoubian'][] = $list[$key];
					break;
				case 2:
					$nlist['guonei'][] = $list[$key];
					break;
				case 3:
					$nlist['chujing'][] = $list[$key];
					break;
			}
		}
		
		$this->assign('list',$nlist);
		
		//去哪儿
		if($this->wxUserInfo['type_id'] == 1){
			$wx_openid = SESSION('wx_openid');
			$wx_id = M('wx_user')->where('wx_openid="'.$wx_openid.'"')->getfield('wx_id');
			$share['title'] = '三三旅游自主班';
			$share['desc'] = '家离店不远，家门口接送，家人式服务';
			$share['link'] = 'http://m.33ly.com/Theme/zizutuan?wid='.$wx_id;
			$share['icon'] = 'http://www.33ly.com/Uploads/logo.png';    //分享图标
			$share['wx_openid'] = $wx_openid;
			$this->assign('share',$share);
			$this->assign('is_sales',1);
		}else{
			
		}		
		
		$this->display();
		
	}
	
	/**
     * 获取最近行程
     * @access public
     * @param integer $id 线路id
     * @return query
     */
	public function getDeparture($id,$sday,$length=6){
		$nowtime = strtotime(date(Ymd));
		$exptime = $nowtime + 3600*24*$sday;
		$query ='';
		$mod = M('departure_time');
		$list = $mod->where('pid='.$id.' and departure_time>='.$exptime.' and is_del=0')->order('departure_time')->limit(7)->select();
		//$this->assign('firstDep',date('Y-m-d',$list[0]['departure_time']));
		foreach($list as $key=>$value){
			if($key==0) $query .= date('n/d',$value['departure_time']);
			if($key<$length and $key>0) $query .= '，'.date('n/d',$value['departure_time']);
			if($key==$length) $query .= '...';
		}
		return $query;
		//return date('Ymd',$exptime);
	}

}