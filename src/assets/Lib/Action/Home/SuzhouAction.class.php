<?php 
class SuzhouAction extends BaseAction 
{
    public function index(){

		//一日游 id=327
		$dayOneList = $this->getLinesById(327);
		foreach($dayOneList as $key=>$value){
			if($key == 0){
				$dayOneList[$key]['dep'] = $this->getDeparture($value['id'],1);
			}else{
				$dayOneList[$key]['dep'] = $this->getDeparture($value['id'],1,4);
			}
		}
		$this->assign('dayOneList',$dayOneList);
		
		$dayTwoList = $this->getLinesById(328);
		foreach($dayTwoList as $key=>$value){
			if($key == 0){
				$dayTwoList[$key]['dep'] = $this->getDeparture($value['id'],1);
			}else{
				$dayTwoList[$key]['dep'] = $this->getDeparture($value['id'],1,4);
			}
		}
		$this->assign('dayTwoList',$dayTwoList);
		
		$dayThreeList = $this->getLinesById(329);
		foreach($dayThreeList as $key=>$value){
			if($key == 0){
				$dayThreeList[$key]['dep'] = $this->getDeparture($value['id'],1);
			}else{
				$dayThreeList[$key]['dep'] = $this->getDeparture($value['id'],1,4);
			}
		}
		$this->assign('dayThreeList',$dayThreeList);
		
		$this->display();
	}
	
	public function category(){
		$pinyin = $_REQUEST['pinyin'];
		$this->assign('pinyin',$pinyin);
		
		//根据拼音获取cid
		$cateMod = M('goods_cate');
		$info = $cateMod->where('pinyin="'.$pinyin.'" and is_del=0')->find();
		$this->assign('cateInfo',$info);
		$cid = $info['id'];
		$cateList = $cateMod->where('pid='.$info['pid'].' and is_del=0')->order('ordid desc')->select();
		$this->assign('cateList',$cateList);
		
		$this->assign('posi',"<a href='http://www.33ly.com/suzhou/'>深度苏州</a><b>></b><span>苏州".$info['name']."</span>");
		
		//ajax分页
		import("@.ORG.Page");
		$where['is_del'] = 0;
		$where['cate_id'] = $cid;
		$where['minprice'] = array('neq',0);
		$list = M('goods')->where($where)->order('ordid desc,add_time desc')->select();
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),80);
			//$list[$key]['dep'] = $this->getDeparture($value['id'],$value['sign_up']);
			$list[$key]['dep'] = $this->getDeparture($value['id'],1);
			$list[$key]['switch'] = json_decode($value['switch']);
		}
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Tour:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		
		//特价
		$this->assign('tj',$this->tejia(2));
		
		$this->display();
	}
	
	//租车cate_id=138
	public function daoyou(){
		$list = M('goods')->where('cate_id=138')->order('ordid desc')->select();
		$this->assign('list',$list);
		
		$this->assign('posi',"<a href='http://www.33ly.com/suzhou/'>深度苏州</a><b>></b><span>导游</span>");
		
		//特价
		$this->assign('tj',$this->tejia(2));
		
		$this->display();
	}
	
	//租车cate_id=335
	public function zuche(){
		$list = M('goods')->where('cate_id=335')->order('ordid desc')->select();
		$this->assign('list',$list);
		
		$this->assign('posi',"<a href='http://www.33ly.com/suzhou/'>深度苏州</a><b>></b><span>租车</span>");
		
		//特价
		$this->assign('tj',$this->tejia(2));
		
		$this->display();
	}
	
	public function detail(){		
		$id = $_REQUEST['id'];
		if(isPhone()) header("Location: http://m.33ly.com/Tour/detail/id/".$id.".html");
		$mod = M('goods');
		$cateMod = M('goods_cate');
		$weekarray=array("日","一","二","三","四","五","六");
		$this->assign('week',$weekarray);
			
		$info = $mod->where('id='.$id)->find();
		if(!$info){
			$this->error('非法操作！','/');
		}
		
		//$info['dep'] = $this->getDeparture($info['id'],$info['sign_up']);
		$info['dep'] = $this->getDeparture($info['id'],1);
		
		//最近行程
		$nowtime = strtotime(date(Ymd));
		$exptime = $nowtime + 3600*24*1;
		
		$map['cid'] = $id;
		$firstDep = M('departure_time')->where('pid='.$id.' and departure_time>='.$exptime.' and is_del=0')->order('departure_time')->find();
		$info['startYear'] = date('Y',$firstDep['departure_time']);
		$info['startMonth'] = date('m',$firstDep['departure_time']);
				
		$info['service'] = json_decode($info['service']);
		$info['seo_desc_common'] = msubstr(strip_tags($info['info']),45);
		$this->assign('info',$info);
		$floor = M('goods_cate')->where('id='.$info['cate_id'])->getfield('floor');

		//面包屑
		$pidInfo = $cateMod->where('id='.$info['cate_id'])->find();
		$ppid = $cateMod->where('id='.$pidInfo['pid'])->getfield('pid');
		$key = $ppid?$ppid:$pidInfo['pid'];
		switch($key){
			case 1:
				$this->assign('posi',"<a href='".__ROOT__."/zhoubian/'>周边游</a><b>></b><a href='".__ROOT__."/zhoubian/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>
				");
				$deptime = $nowtime + 3600*24*30;
				$depList = M('departure_time')->where('pid='.$info['id'].' and departure_time>'.$nowtime.' and departure_time<'.$deptime.' and is_del=0')->order('departure_time')->select();
				$this->assign('depList',$depList);
				break;
			case 2:
				$this->assign('posi',"<a href='".__ROOT__."/guonei/'>国内游</a><b>></b><a href='".__ROOT__."/guonei/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>
				");
				$deptime = $nowtime + 3600*24*90;
				$depList = M('departure_time')->where('pid='.$info['id'].' and departure_time>'.$nowtime.' and departure_time<'.$deptime.' and is_del=0')->order('departure_time')->select();
				$this->assign('depList',$depList);
				break;
			case 3:
				$this->assign('posi',"<a href='".__ROOT__."/chujing/'>出境游</a><b>></b><a href='".__ROOT__."/chujing/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>
				");
				$deptime = $nowtime + 3600*24*90;
				$depList = M('departure_time')->where('pid='.$info['id'].' and departure_time>'.$nowtime.' and departure_time<'.$deptime.' and is_del=0')->order('departure_time')->select();
				$this->assign('depList',$depList);
				break;
			case 97:
				$this->assign('posi',"<a href='".__ROOT__."/tuandui/'>团队游</a><b>></b><a href='".__ROOT__."/tuandui/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>
				");
				break;
			case 326:
				$this->assign('posi',"<a href='".__ROOT__."/suzhou/'>深度苏州</a><b>></b><a href='".__ROOT__."/suzhou/".$pidInfo['pinyin']."/'>苏州".$pidInfo['name']."</a><b>></b>
				");
				$deptime = $nowtime + 3600*24*30;
				$depList = M('departure_time')->where('pid='.$info['id'].' and departure_time>'.$nowtime.' and departure_time<'.$deptime.' and is_del=0')->order('departure_time')->select();
				$this->assign('depList',$depList);
				break;
		}
		
		//获取行程
		$trip = M('trip')->where('pid='.$info['id'].' and is_del=0')->order('ordid')->select();
		foreach($trip as $key=>$value){
			$trip[$key]['dinner'] = json_decode($value['dinner']);
			$scene = explode(',',$value['scene']);
			foreach($scene as $skey=>$svalue){
				if($svalue && $skey<3){
					$res = M('scenic')->where('name="'.$svalue.'" and is_del=0')->find();
					$trip[$key]['scenic'][$skey] = $res;
				}
			}

			//获取景点
			//$trip[$key]['scene'] = explode(',',$value['scene']);
		}
		$this->assign('trip',$trip);
		
		//相关线路 大分类下其他线路or同关键词线路or同分类
		//rList1为同小分类下其他线路，rList2为大分类下其他线路
		$rList1 = M('goods')->where('cate_id='.$info['cate_id'].' and is_del=0 and is_show=1 and minprice<>0 and id !='.$id)->order('ordid desc,add_time desc')->limit(4)->select();
		foreach($rList1 as $key=>$value){
			$catepinyin = M('goods_cate')->where('id='.$value['type_id'])->getfield('pinyin');
			$rList1[$key]['catepinyin'] = 'zhoubian';
		}
		//$rList2 = M('goods')->where('type_id='.$info['type_id'].' and is_del=0')->limit(4)->select();
		//$rList = array_merge($rList1,$rList2);
		//$rList = explode(array_unique(implode(',',$rList)));
		//var_dump($rList);
		$this->assign('relative',$rList1);
		
		//生成二维码
		$curl =  "http://".$_SERVER ['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$ewmurl = $this->erweima($curl,$info['sn']);
		$this->assign('ewmurl',$ewmurl);
		
		$this->display();
	}
	
}
