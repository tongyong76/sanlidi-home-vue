<?php 
class ScenicAction extends BaseAction 
{
    public function index(){

		$this->display();
		
	}
	
	public function detail(){
		
		//景点信息
		$id = $_REQUEST['id']?$_REQUEST['id']:6813;
		$info = M('scenic')->where('id='.$id)->find();
		if($_REQUEST['id']) M('scenic')->where('id='.$_REQUEST['id'])->setInc('count');
		$this->assign('info',$info);
		
		//标签
		$tagList = M('scenic_tag as s')->join('33_tag as t on t.id=s.tag_id')->field('s.*,t.name')->where('s.scenic_id='.$id)->select();
		$this->assign('tagList',$tagList);
		
		//相关景点
		$scenicList = M('scenic')->where('is_del=0')->order('count desc')->limit(10)->select();
		foreach($scenicList as $key => $value){
			$scenicList[$key]['son'] = M('scenic_tag as s')->join('33_tag as t on t.id=s.tag_id')->field('s.*,t.name')->where('s.scenic_id='.$value['id'])->limit(2)->select();
		}
		$this->assign('scenicList',$scenicList);
		
		//相关线路
		//当季热推
		$sn = rand(0,7);
		$hotMod = new Model();
		$sql = "select *
		from 33_goods as g
		where g.is_del=0 and g.is_hot=1 and g.minprice<>0
		order by g.ordid desc,g.add_time desc
		limit ".$sn.",8";
		$hotList = $hotMod->query($sql);
		//$hotList = $lineMod->where('is_del=0 and is_hot=1')->limit(5)->select();
		foreach($hotList as $key=>$value){
			$hotList[$key]['info'] = msubstr(strip_tags($value['info']),80);
			$hotList[$key]['pinyin'] = M('goods_cate')->where('id='.$value['type_id'])->getfield('pinyin');
			$hotList[$key]['pinyin'] = str_replace("you","",$hotList[$key]['pinyin']);
			$hotList[$key]['switch'] = json_decode($value['switch']);
			$hotList[$key]['dep'] = $this->getDeparture($value['id'],1);
			if(stripos($value['switch'],'1') or ($value['brand_id'] == 1)){
				$hotList[$key]['is_zzt'] = 1;
			}
		}
		$this->assign('hotList',$hotList);
		
		$this->display();
	}
	
}
