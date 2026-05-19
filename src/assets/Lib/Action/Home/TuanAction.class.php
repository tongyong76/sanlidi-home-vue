<?php
class TuanAction extends BaseAction {
    public function submit(){
		if($_POST['submit']){
			$mod = M('order_tuan');
			$data = $mod->create();
			$data['time'] = strtotime($data['time']);
			$data['add_time'] = time();
			//var_dump($data);
			$newId = $mod->add($data);
			if($newId){
				$this->success('提交成功，我们的旅游顾问会尽快与您取得联系！');
			}
		}else{
			$this->display();
		}
	}
	
	public function index(){
		//$cid = $_REQUEST['cid'];
		$pinyin = $_REQUEST['pinyin'];
		$cateMod = M('goods_cate');
		
		$info = $cateMod->where('pinyin="'.$pinyin.'"')->find();
		$cid = $info['id'];
		$price = $_REQUEST['price'];		
		$this->assign('cid',$cid);
		
		//$info = $cateMod->where('id='.$cid)->find();
		$this->assign('cateInfo',$info);
		$floor = $info['floor'];
		//列表页三种
		//type_id
		if(!$floor){
			$where = 'is_del=0';
			$map_org = 'is_del=0';
		}
		if($floor == 1){
			$where = 'is_del=0 and type_id='.$cid;
			$map_org = 'is_del=0 and type_id='.$cid;
		}
		if($floor == 2){
			$cateList = $cateMod->where('pid='.$cid.' and is_del=0')->select();
			$this->assign('cateList',$cateList);
			if($cateList){
				$string = "(";
				foreach($cateList as $key=>$value){
					if($key) $string .= ',';
					$string .= $value['id'];
				}
				$string .= ")";
				$where = 'is_del=0 and cate_id in '.$string;
				$map_org = 'is_del=0 and cate_id in '.$string;
			}else{
				$where = 'is_del=0 and cate_id='.$cid;
				$map_org = 'is_del=0 and cate_id='.$cid;
			}
			$this->assign('nid',$cid);
			$this->assign('allid',$cid);
			$this->assign('allpinyin',$info['pinyin']);
			
			//团队游posi  只有 团队游>关键词
			$posi = $cateMod->where('id='.$cid)->getfield('pid');
			switch($posi){
				case 1:
					$this->assign('posi',"<a href='http://www.33ly.com/tuandui/'>团队游</a><b>></b><span>".$info['name']."</span>");
					//处理price
					$this->assign('price',C('zb_range'));
					switch($price){
						case 1:
							$minprice = 0;
							$maxprice = 100;
							break;
						case 2:
							$minprice = 100;
							$maxprice = 300;
							break;
						case 3:
							$minprice = 300;
							$maxprice = 400;
							break;
						case 4:
							$minprice = 500;
							$maxprice = 100000;
							break;
						default:
							break;
					}
					break;
				case 2:
					$this->assign('posi',"<a href='http://www.33ly.com/tuandui/'>团队游</a><b>></b><span>".$info['name']."</span>");
					$this->assign('price',C('gn_range'));
					switch($price){
						case 1:
							$minprice = 0;
							$maxprice = 1000;
							break;
						case 2:
							$minprice = 1000;
							$maxprice = 2000;
							break;
						case 3:
							$minprice = 2000;
							$maxprice = 5000;
							break;
						case 4:
							$minprice = 5000;
							$maxprice = 100000;
							break;
						default:
							break;
					}
					break;
				case 3:
					$this->assign('posi',"<a href='http://www.33ly.com/tuandui/'>团队游</a><b>></b><span>".$info['name']."</span>");
					$this->assign('price',C('cj_range'));
					switch($price){
						case 1:
							$minprice = 0;
							$maxprice = 1000;
							break;
						case 2:
							$minprice = 1000;
							$maxprice = 2000;
							break;
						case 3:
							$minprice = 2000;
							$maxprice = 5000;
							break;
						case 4:
							$minprice = 5000;
							$maxprice = 100000;
							break;
						default:
							break;
					}
					break;
				case 97:
					$this->assign('posi',"<a href='http://www.33ly.com/tuandui/'>团队游</a><b>></b><span>".$info['name']."</span>");
					break;
			}
		} 
		if($floor == 3){
			//cate_list并传递当前cate_id
			
			$cateList = $cateMod->where('pid='.$info['pid'].' and is_del=0')->select();
			$this->assign('cateList',$cateList);
			$string = "(";
			foreach($cateList as $key=>$value){
				if($key) $string .= ',';
				$string .= $value['id'];
			}
			$string .= ")";
			$where = 'is_del=0 and cate_id='.$cid;
			//$map_org = 'is_del=0 and cate_id in '.$string;
			$map_org = 'is_del=0 and cate_id='.$cid;
			$this->assign('nid',$info['id']);
			$this->assign('allid',$info['pid']);
			$this->assign('allpinyin',M('goods_cate')->where('id='.$info['pid'])->getfield('pinyin'));
			
			//posi
			$posi = $cateMod->where('id='.$info['pid'])->getfield('pid');
			switch($posi){
				case 1:
					$this->assign('posi',"<a href='http://www.33ly.com/zhoubian/'>周边游</a><b>></b><span>".$info['name']."</span>");
					$this->assign('price',C('zb_range'));
					switch($price){
						case 1:
							$minprice = 0;
							$maxprice = 100;
							break;
						case 2:
							$minprice = 100;
							$maxprice = 300;
							break;
						case 3:
							$minprice = 300;
							$maxprice = 400;
							break;
						case 4:
							$minprice = 500;
							$maxprice = 100000;
							break;
						default:
							break;
					}
					break;
				case 2:
					$this->assign('posi',"<a href='http://www.33ly.com/guonei/'>国内游</a><b>></b><span>".$info['name']."</span>");
					$this->assign('price',C('gn_range'));
					switch($price){
						case 1:
							$minprice = 0;
							$maxprice = 1000;
							break;
						case 2:
							$minprice = 1000;
							$maxprice = 2000;
							break;
						case 3:
							$minprice = 2000;
							$maxprice = 5000;
							break;
						case 4:
							$minprice = 5000;
							$maxprice = 100000;
							break;
						default:
							break;
					}
					break;
				case 3:
					$this->assign('posi',"<a href='http://www.33ly.com/chujing/'>出境游</a><b>></b><span>".$info['name']."</span>");
					$this->assign('price',C('cj_range'));
					switch($price){
						case 1:
							$minprice = 0;
							$maxprice = 1000;
							break;
						case 2:
							$minprice = 1000;
							$maxprice = 2000;
							break;
						case 3:
							$minprice = 2000;
							$maxprice = 5000;
							break;
						case 4:
							$minprice = 5000;
							$maxprice = 100000;
							break;
						default:
							break;
					}
					break;
				case 97:
					$this->assign('posi',"<a href='http://www.33ly.com/tuandui/'>团队游</a><b>></b><span>".$info['name']."</span>");
					break;
			}
		}
		
		//dayList
		$dayList = M('goods')->field('days')->where($map_org)->group('days')->select();
		$this->assign('dayList',$dayList);
		
		
		//筛选条件
		if (isset($_REQUEST['keyword']) && trim($_REQUEST['keyword'])) {
			$keyword = trim($_REQUEST['keyword']);
			$where .= " AND name LIKE '%".$keyword."%'";
			$this->assign('keyword', $keyword);
		}
		if (isset($_REQUEST['cid']) && trim($_REQUEST['cid'])) {
			$cid = trim($_REQUEST['cid']);
		//	$where .= " AND cate_id = $cid ";
			$this->assign('cid', $cid);
		}
		if (isset($_REQUEST['days']) && trim($_REQUEST['days'])) {
			$days = trim($_REQUEST['days']);
			$where .= " AND days = ".$days;
			$this->assign('days', $days);
		}
		//if (isset($_REQUEST['minprice']) && trim($_REQUEST['minprice'])) {
		if ($minprice) {
			//$minprice = trim($_REQUEST['minprice']);
			$where .= " AND minprice>='".$minprice."'";
			$this->assign('minprice', $minprice);
		}
		//if (isset($_REQUEST['maxprice']) && trim($_REQUEST['maxprice'])) {
		if ($maxprice) {
			//$maxprice = trim($_REQUEST['maxprice']);
			$where .= " AND minprice<'".$maxprice."'";
			$this->assign('maxprice', $maxprice);
		}
		
		//ajax分页
		import("@.ORG.Page");
		$list = M('goods')->where($where)->select();
		//var_dump($list);
		foreach($list as $key=>$value){
			$list[$key]['info'] = msubstr(strip_tags($value['info']),160);
		}
		$param = array(
			'result'=>$list,			//分页用的数组或sql
			'listvar'=>'list',			//分页循环变量
			'listRows'=>8,			//每页记录数
			//'parameter'=>'search=key&name=thinkphp',//url分页后继续带的参数
			'parameter'=>'',
			'target'=>'content',	//ajax更新内容的容器id，不带#
			'pagesId'=>'page',		//分页后页的容器id不带# target和pagesId同时定义才Ajax分页
			'template'=>'Index:ajaxlist',//ajax更新模板
		);
		$this->page($param);
		
		$this->display();
	}	
	
	public function detail(){
		$id = $_REQUEST['id'];
		$mod = M('goods');
		$cateMod = M('goods_cate');
			
		$info = $mod->where('id='.$id)->find();
		if(!$info){
			$this->error('非法操作！','__ROOT__');
		}
		$info['dep'] = $this->getDeparture($info['id']);
		$info['seo_desc'] = msubstr(strip_tags($info['info']),45);
		$this->assign('info',$info);
		$floor = M('goods_cate')->where('id='.$info['cate_id'])->getfield('floor');

		//面包屑
		$pidInfo = $cateMod->where('id='.$info['cate_id'])->find();
		$this->assign('posi',"<a href='".__ROOT__."/tuandui/'>团队游</a><b>></b><a href='".__ROOT__."/tuandui/".$pidInfo['pinyin']."/'>".$pidInfo['name']."</a><b>></b>");

		
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
		$rList1 = M('goods')->where('cate_id='.$info['cate_id'].' and is_del=0 and id !='.$id)->limit(4)->select();
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
	
	public function page($param) {
		extract($param);
		import("@.ORG.Page");
		//总记录数
		$flag = is_string($result);
		$listvar = $listvar ? $listvar : 'list';
		$listRows = $listRows? $listRows : 10;
		if ($flag)
			$totalRows = M()->table($result . ' a')->count();
		else
			$totalRows = ($result) ? count($result) : 1;
		//创建分页对象
		if ($target && $pagesId)
			$p = new Page($totalRows, $listRows, $parameter, $url,$target, $pagesId);
		else
			$p = new Page($totalRows, $listRows, $parameter,$url);
		//抽取数据
		if ($flag) {
			$result .= " LIMIT {$p->firstRow},{$p->listRows}";
			$voList = M()->query($result);
		} else {
			$voList = array_slice($result, $p->firstRow, $p->listRows);
		}
		$pages = C('PAGE');//要ajax分页配置PAGE中必须theme带%ajax%，其他字符串替换统一在配置文件中设置，
		//可以使用该方法前用C临时改变配置
		foreach ($pages as $key => $value) {
			$p->setConfig($key, $value); // 'theme'=>'%upPage% %linkPage% %downPage% %ajax%'; 要带 %ajax%
		}
		//分页显示
		$page = $p->show();
		//模板赋值
		$this->assign($listvar, $voList);
		$this->assign("page", $page);
		if ($this->isAjax()) {//判断ajax请求
			layout(false);
			$template = (!$template) ? 'ajaxlist' : $template;
			exit($this->fetch($template));
		}
		return $voList;
	}
}