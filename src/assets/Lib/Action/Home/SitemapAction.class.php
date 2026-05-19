<?php
class SitemapAction extends Action {
	
	public function create(){
		
		$type = $_REQUEST['type'];
		
		header("Content-type:text/html;charset=utf-8");
		//头部
		$content = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$content .= "<urlset xmlns = 'http://www.sitemaps.org/schemas/sitemap/0.9' xmlns:xsi = 'http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation = 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'>\n";
		
		switch($type){
			case 'goods':
				//首页和二级首页,always
				$single_array = array(
					array(
						'loc' => 'http://www.33ly.com/',
						'priority' => '1.0',
					),
					array(
						'loc' => 'http://www.33ly.com/chujing/',
						'priority' => '0.8',
					),
					array(
						'loc' => 'http://www.33ly.com/guonei/',
						'priority' => '0.8',
					),
					array(
						'loc' => 'http://www.33ly.com/zhoubian/',
						'priority' => '0.8',
					),
					array(
						'loc' => 'http://www.33ly.com/youlun/',
						'priority' => '0.8',
					),
					array(
						'loc' => 'http://www.33ly.com/qianzheng/',
						'priority' => '0.8',
					),
					array(
						'loc' => 'http://www.33ly.com/tuandui/',
						'priority' => '0.8',
					)
				);
				//var_dump($single_array);
				foreach($single_array as $data){
					$content .= $this->create_item($data);
				}
				
				//列表页数据
				$cateMod = M('goods_cate');
				$list = $cateMod->field('id,pid,floor,pinyin')->where('is_del=0 and floor<>1')->select();
				foreach($list as $key=>$value){
					$gType = $this->getCateType($value['floor'],$value['pid']);
					$cate_array[$key]['loc'] = 'http://www.33ly.com/'.$gType.'/'.$value['pinyin'].'/';
					$cate_array[$key]['priority'] = '0.6';
					$cate_array[$key]['lastmod'] = date('Y-m-d',time());
					$cate_array[$key]['changefreq'] = 'weekly';
				}
				//var_dump($cate_array);
				foreach($cate_array as $data){
					$content .= $this->create_item($data);
				}
				
				//线路终页
				$goodsMod = M('goods');
				$glist = $goodsMod->field('id,type_id')->select();
				foreach($glist as $key=>$value){
					switch($value['type_id']){
						case 1:
							$gType = 'zhoubian';
							break;
						case 2:
							$gType = 'guonei';
							break;
						case 3:
							$gType = 'chujing';
							break;
						case 97:
							$gType = 'group';
							break;
						case 326:
							$gType = 'suzhou';
							break;
					}
					$goods_array[$key]['loc'] = 'http://www.33ly.com/'.$gType.'/xianlu-'.$value['id'].'.html';
					$goods_array[$key]['priority'] = '0.5';
					$goods_array[$key]['lastmod'] = date('Y-m-d',time());
					$goods_array[$key]['changefreq'] = 'weekly';
				}
				foreach($goods_array as $data){
					$content .= $this->create_item($data);
				}				
				break;
			case 'scenic':
				//景点库终页
				$scenicMod = M('scenic');
				$scenicList = $scenicMod->field('id')->select();
				foreach($scenicList as $key=>$value){
					$scenic_array[$key]['loc'] = 'http://www.33ly.com/scenic/jing-'.$value['id'].'.html';
					$scenic_array[$key]['priority'] = '0.3';
					$scenic_array[$key]['lastmod'] = date('Y-m-d',time());
					$scenic_array[$key]['changefreq'] = 'weekly';
				}
				foreach($scenic_array as $data){
					$content .= $this->create_item($data);
				}
				break;
			case 'ask':
				//问答终页
				$askMod = M('ask');
				$askList = $askMod->field('id,add_time')->select();
				foreach($askList as $key=>$value){
					$ask_array[$key]['loc'] = 'http://www.33ly.com/ask/question-'.$value['id'].'.html';
					$ask_array[$key]['priority'] = '0.3';
					$ask_array[$key]['lastmod'] = date('Y-m-d',$value['add_time']);
					$ask_array[$key]['changefreq'] = 'weekly';
				}
				foreach($ask_array as $data){
					$content .= $this->create_item($data);
				}
				break;
			default:
				break;
		}
		
		//结尾
		$content .= '</urlset>';
		
		//输出到文件
		$file_name = "sitemap_".$type.".xml";
		$fp = fopen($file_name,'w+');
		fwrite($fp,$content);
		fclose($fp);
		echo $file_name."已更新".date('Y-m-d',time());
	}
	
	public function create_item($data){
	    $item="<url>\n";
	    $item.="<loc>".$data['loc']."</loc>\n";
	    $item.="<priority>".$data['priority']."</priority>\n";
	    if($data['lastmod']){
			$item.="<lastmod>".$data['lastmod']."</lastmod>\n";
		}
		if($data['changefreq']){
			$item.="<changefreq>".$data['changefreq']."</changefreq>\n";
		}
	    $item.="</url>\n";
	    return $item;
	}
	
	public function getCateType($floor,$pid){
		$cateMod = M('goods_cate');
		if($floor == 2){
			switch($pid){
				case 1:
					$type = 'zhoubian';
					break;
				case 2:
					$type = 'guonei';
					break;
				case 3:
					$type = 'chujing';
					break;
				case 97:
					$type = 'group';
					break;
			}
		}
		if($floor == 3){
			$ppid = $cateMod->where('id='.$pid)->getfield('pid');
			switch($ppid){
				case 1:
					$type = 'zhoubian';
					break;
				case 2:
					$type = 'guonei';
					break;
				case 3:
					$type = 'chujing';
					break;
				case 97:
					$type = 'group';
					break;
			}
		}
		return $type;
	}
}