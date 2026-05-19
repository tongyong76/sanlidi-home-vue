<?php
//首页
class IndexAction extends Action {
    public function index(){
		//判断是否移动端
		if(isPhone()){
		//if(0){
			header("Location: http://m.33ly.com");
		}else{
			$lineMod = M('goods');
			
			//首页幻灯调用
			$bannerList = M('banner')->where('is_show=1 and is_del=0 and pid=0')->order('ordid desc')->limit(5)->select();
			foreach($bannerList as $key=>$value){
				$bannerList[$key]['son'] = M('banner')->where('is_show=1 and is_del=0 and pid='.$value['id'])->limit(3)->select();
			}
			$this->assign('bannerList',$bannerList);
			
			//楼层调用（延迟加载）			
			$this->assign("cjList",$this->getLinesByTypeId(3));			
			$this->assign("gnList",$this->getLinesByTypeId(2));
			$this->assign("zbList",$this->getLinesByTypeId(1));
			
			//推荐调用
			
			//当季热推
			$hotMod = new Model();
			$sql = "select *
			from 33_goods as g
			where g.is_del=0 and g.is_hot=1 and g.minprice<>0
			order by g.ordid desc,g.add_time desc
			limit 0,5";
			$hotList = $hotMod->query($sql);
			//$hotList = $lineMod->where('is_del=0 and is_hot=1')->limit(5)->select();
			foreach($hotList as $key=>$value){
				$hotList[$key]['info'] = msubstr(strip_tags($value['info']),45);
				$hotList[$key]['pinyin'] = M('goods_cate')->where('id='.$value['type_id'])->getfield('pinyin');
				$hotList[$key]['pinyin'] = str_replace("you","",$hotList[$key]['pinyin']);
				$hotList[$key]['switch'] = json_decode($value['switch']);
			}
			$this->assign('hotList',$hotList);
			
			//文章调用
			// $arcList = M('article')->where('cate_id=1 and is_del=0 and is_show=1')->order('ordid desc,add_time desc')->limit(5)->select();
			// $this->assign('arcList',$arcList);
			$this->assign('arcList',$this->getNews(3));
			
			// //广告调用
			// $adList = $this->getAd();
			// $this->assign('ad',$adList);
			
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
			
			//友链调用
			$linkList = M('link')->where('is_del=0 and is_show=1 and cate_id=2')->order('ordid desc')->select();
			$this->assign('linkList',$linkList);

			//载入导航
			$navMod = M('navigation');
			$navList = $navMod->where('is_del=0')->order('ordid desc')->select();
			$this->assign('navList',$navList);	
			
			//搜索推广
			$adSearch = M('ad_search')->where('id=1')->find();
			$this->assign('search',$adSearch);	
			
			//右侧相关
			//hot 按favs降序排列
			$goodsMod = M('goods');
			$hot=$goodsMod->order('favs desc')->limit(5)->select();
			$this->assign('hot',$hot);
			
			//顶部分类调用
			$lineCateList = $this->genTree5("goods_cate");
			$this->assign("lineCateList",$lineCateList);
			//$ress = sort($lineCateList[1]['son']);
			$arrr = my_mul_sort($lineCateList[2]['son'],'ordid');
			$this->assign("zbCateArr",my_mul_sort($lineCateList[1]['son'],'ordid'));
			$this->assign("gnCateArr",my_mul_sort($lineCateList[2]['son'],'ordid'));
			$this->assign("cjCateArr",my_mul_sort($lineCateList[3]['son'],'ordid'));
			$this->assign("tdCateArr",my_mul_sort($lineCateList[97]['son'],'ordid'));
			
			//广告调用
			$adList = $this->getAd();
			$this->assign('ad',$adList);
		}
		//var_dump($lineCateList[1]);
		$this->assign('isIndex',1);
		
		$this->display();
    }
	
	public function getLinesByTypeId($type_id){
		$linesMod = new Model();
		$sql = "select *
			from 33_goods as g
			where g.is_del=0 and g.is_show=1 and g.minprice<>0 and g.type_id = ". $type_id ."
			order by g.ordid desc,g.add_time desc
			limit 0,11";
		$linesList = $linesMod->order('ordid desc')->query($sql);
		return $linesList;
	}
	
	/**
     * 获取公司最新新闻
     * @access public
     * @param integer $num 显示数量
     * @return array
	 * @order add_time desc 按add_time降序排列
     */
	public function getNews($num){
		$mod = M('article');
		$hotList = $mod->field('id,title,add_time')->where('is_del=0 and cate_id=1')->order('ordid desc,add_time desc')->limit($num)->select();
		return $hotList;
	}
	
	/**
     * 广告集
     */
	function getAd(){
		//if(session('adlist')){
		//	$nlist = session('adlist');
		//}else{
			$list = M('ad')->where('is_del=0 and status =1')->select();
			foreach($list as $key=>$value){
				$nlist[$value['cname']] = $value;
			}
		//	session('adlist',$nlist);
		//}
		return $nlist;
	}
	
	/**
     * 无限分类数据树形格式化
     * @access public
     * @param integer $cateMod 分类模型
     * @return array
     */
	function genTree5($cateMod) {
		if(session('tree'.$cateMod)){
			$items = session('tree'.$cateMod);
		}else{
			$itemss = M($cateMod)->where('is_del=0')->select();
			foreach ($itemss as $key=>$value){
				$items[$value['id']] = $value;
			}
			session('tree'.$cateMod,$items);
		}
		//genTree5
		foreach ($items as $id=>$item)
			$items[$item['pid']]['son'][$item['id']] = &$items[$item['id']];
		return isset($items[0]['son']) ? $items[0]['son'] : array();
	}
}