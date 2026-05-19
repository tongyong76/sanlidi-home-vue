<?php
class ApilyqAction extends Action{
	public $SecretKey = 'A483B64FA5244E92994438709328B7F8';
	public $sessionTime = 14400;

	//列表显示
	public function index(){
		$this->display();
	}
	
	public function getSingleLine(){
		header("Content-Type: text/html;charset=utf-8");
		$id = $_REQUEST['id'];
		$productId = "fffb50ce3b404fb7ac20d0bbb8b6d146";
		$postUrl = "http://service.lvyouquan.cn/BusinessOpenAPIService.svc/GetProduct";
		$data = "<?xml version='1.0' encoding='utf-8'?>
				<GetProductRequest xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema'>
				 <SecretKey>". $this->SecretKey ."</SecretKey>
				  <ProductIDs>
					<ProductID>4f2916c030a64401b367a6d2487d5dbb</ProductID>
				  </ProductIDs>
				  <Type>0</Type>
				</GetProductRequest>";
		$res = pp($data,$postUrl);
		$xml = (array)simplexml_load_string($res);
		var_dump($xml);
	}
	
	public function getSingleLineTest(){
		$xml_data = '<?xml version="1.0" encoding="utf-8"?>
					<GetProductRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
					  <SecretKey>fffb50ce3b404fb7ac20d0bbb8b6d146</SecretKey>
					  <ProductIDs>
						<ProductID>4f2916c030a64401b367a6d2487d5dbb</ProductID>
					  </ProductIDs>
					  <Type>0</Type>
					</GetProductRequest>';
		$url = 'http://service.lvyouquan.cn/BusinessOpenAPIService.svc/GetProduct';
		$header[] = "Content-type: text/xml";//定义content-type为xml
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
		$response = curl_exec($ch);
		if(curl_errno($ch))
		{
			print curl_error($ch);
		}
		curl_close($ch);
		
		$xml = (array)simplexml_load_string($response);
		var_dump($xml);
		
	}
	
	public function test(){
		$sPage = $_REQUEST['sPage'];
		$ePage = $_REQUEST['ePage'];
		$pageNum = $_REQUEST['pageNum'];
		$productNum = $_REQUEST['productNum'];
		$this->ajaxReturn($ePage,$sPage,1);
	}
	
	//入库
	public function importProduct($startPage = 1){
		//ini_set('max_execution_time', '1000');
		set_time_limit(0);
		
		$sPage = $_REQUEST['sPage'];
		$ePage = $_REQUEST['ePage'];
		$pageNum = $_REQUEST['pageNum'];
		$productNum = $_REQUEST['productNum'];
		
		$startPage = $sPage?$sPage:1;
		$totalCount = $ePage?$ePage:$pageNum;
		
		//$total = $this->getTotalFileNum();
		$returnData = '';
		for($i=$startPage;$i<=$totalCount;$i++){    //$total['XmlFileCount']
			$res = $this->getProductFile($i);
			$productCount = $res->Products->Product->count();
			for($j=0;$j<$productCount;$j++){
				$dstData = array();
				
				$srcData = (array)$res->Products->Product[$j];
				//是否需要更新：12天未更新
				$lastTime = M('goods')->where('ProductID="'.$srcData['ProductID'].'"')->getField('last_time');
				if(($lastTime + (3600*24*12)) > time()) continue;
								
				//排除的出发城市
				$cityArr = array('上海','无锡','南通','苏州');
				if(!in_array($srcData['StartCity'],$cityArr)){
					continue;
				}
				
				//打印信息
				$nowProductNum = ($i - 1) * 50 + ($j + 1);
				$returnData .= "<p>共".$productNum."个产品，正在更新第".$nowProductNum."个...</p>";
				
				//var_dump($srcData);
				$sellPoints = (array)$res->Products->Product[$j]->SellPoints->SellPoint;
				//卖点
				if($sellPoints){
					$data = '';
					foreach($sellPoints as $key=>$value){
						$data .= '<p>'.$value.'</p>';
					}
					$dstData['info'] = $data;
				}
				//缩略图
				$images = (array)$res->Products->Product[$j]->Images->Image;

				//数据映射
				$dstData['is_ds'] = 0;  //初始化
				$dstData['fees_in'] = descclear((string)$res->Products->Product[$j]->PriceInclude);
				$dstData['fees_out'] = descclear((string)$res->Products->Product[$j]->PriceNotInclude);
				$dstData['notice'] = descclear((string)$res->Products->Product[$j]->BookMustKnow);
				$dstData['visa'] = descclear((string)$res->Products->Product[$j]->VisaInfo);
				$dstData['ProductID'] = $srcData['ProductID'];
				$dstData['name'] = $srcData['ProductName'];
				$dstData['sn'] = $srcData['ProductCode'];
				$dstData['tag'] = $srcData['ProductCode'];
				$dstData['StartCity'] = $srcData['StartCity'];
				$dstData['ProductLevel'] = $srcData['ProductLevel'];
				$dstData['days'] = $srcData['JourneyDays'];
				$dstData['sign_up'] = $srcData['SignUpEndDays'];
				$dstData['imgurl'] = $images[0];
				switch($srcData['ProductType']){
					case '跟团游':
						$dstData['is_zyx'] = 0;
						break;
					case '自由行':
						//$dstData['is_zyx'] = 1;
						//$dstData['is_ds'] = 1;		//变更属性
						$dstData['is_zyx'] = 0;
						break;
				}
				switch($srcData['TravelType']){
					case '出境游':
						$dstData['type_id'] = 3;
						$dstData['service'] = '["1"]';
						break;
					case '国内游':
						$dstData['type_id'] = 2;
						$dstData['service'] = '';
						$dstData['is_ds'] = 1;		//变更属性
						break;
					case '周边游':
						$dstData['type_id'] = 1;
						$dstData['service'] = '';
						$dstData['is_ds'] = 1;		//变更属性
						break;
				}
				
				$dstData['add_time'] = time();
				$dstData['last_time'] = time();
				$dstData['LocalImage'] = 0;
				$dstData['GroupId'] = 1;
				//$dstData['is_show'] = 0;
				$dstData['is_del'] = 0;

				//分类完善
				$thirdLevelAreasCount = $res->Products->Product[$j]->ThirdLevelAreas->ThirdLevelArea->count();
				for($k=0;$k<$thirdLevelAreasCount;$k++){
					$thirdLevelAreas = (string)$res->Products->Product[$j]->ThirdLevelAreas->ThirdLevelArea[$k];
					$cateId = M('goods_cate')->where('name="'.$thirdLevelAreas.'" and floor=3')->getField('id');
					if($cateId){
						if($k==0) $dstData['cate_id'] = $cateId;
					}else{
						//先检查二级分类是否存在
						$secondLevelArea = (string)$res->Products->Product[$j]->SecondLevelArea;
						$floorTwo = M('goods_cate')->where('name="'.$secondLevelArea.'" and floor=2')->getField('id');
						if($floorTwo){
							$threeData['pid'] = $floorTwo;
						}else{
							$secondData['pid'] = $dstData['type_id'];
							$secondData['floor'] = 2;
							$secondData['name'] = $secondLevelArea;
							$secondData['pinyin'] = pinyin($secondData['name']);
							$secondData['ordid'] = 0;
							$secondData['is_auto'] = 1;
							$secondData['is_show'] = 1;
							$secondData['is_end'] = 0;
							$secondData['is_del'] = 0;
							$newSecondCateId = M('goods_cate')->add($secondData);
							$threeData['pid'] = $newSecondCateId;
						}
						$threeData['floor'] = 3;
						$threeData['name'] = $thirdLevelAreas;
						$threeData['pinyin'] = pinyin($threeData['name']);
						$threeData['ordid'] = 0;
						$threeData['is_auto'] = 1;
						$threeData['is_show'] = 1;
						$threeData['is_end'] = 1;
						$threeData['is_del'] = 0;
						$newThirdCateId = M('goods_cate')->add($threeData);
						if($k==0) $dstData['cate_id'] = $newThirdCateId;
					}
				}
				
				//排除的分类
				// $cityArr = array('日韩','东南亚','海岛');
				// $p_cate_map['name'] = array('in',$cityArr);
				// $p_cate_map['floor'] = 2;
				// $p_cate_rs = M('goods_cate')->where($p_cate_map)->getField('id',true);
				// $p_cate_map2['pid'] = array('in',$p_cate_rs);
				// $p_cate_map2['floor'] = 3;
				// $p_cate_rs2 = M('goods_cate')->where($p_cate_map2)->getField('id',true);				
				// if(!in_array($dstData['cate_id'],$p_cate_rs2)){
					// $dstData['is_ds'] = 1;
				// }
				
				//更新线路信息
				$this->updateProduct($srcData['ProductID'],$dstData);
				$pid = M('goods')->where("ProductID='".$srcData['ProductID']."'")->getField('id');
				
				//更新行程
				$journeyCount = $res->Products->Product[$j]->ProductJourneies->ProductJourney->count();
				$scenicPool = array();
				for($k=0;$k<$journeyCount;$k++){
					$journeyData = (array)$res->Products->Product[$j]->ProductJourneies->ProductJourney[$k];
					
					$jData['pid'] = $pid;
					$jData['name'] = $journeyData['JourneyRang'];
					$jData['hotel'] = $journeyData['StayDesc'] ? $journeyData['StayDesc'] : '';
					$jData['ordid'] = $journeyData['Index'];
					$jData['info'] = (string)$res->Products->Product[$j]->ProductJourneies->ProductJourney[$k]->JourneyDetail;
					$jData['info'] = descclear($jData['info']);
					if(session('scenicArr')){
						$scenicArr = session('scenicArr');
					}else{
						$scenicArr = M('scenic')->where('is_del=0')->select();
						session('scenicArr',$scenicArr);
					}
					$jData['scene'] = '';
					$num = 0;
					foreach($scenicArr as $key=>$value){
						if(strpos($jData['info'],$value['name'])){
							if($num){
								if(in_array($value['name'],$scenicPool)){
									//
								}else{
									$jData['scene'] .= ','.$value['name'];
									$scenicPool[] = $value['name'];
								}								
							}else{
								$jData['scene'] .= $value['name'];
								$scenicPool[] = $value['name'];
							}
							$num++;
						}			
					}
					if($journeyData['IsHaveBreakfast'] == 'true') $eData[$k][0] = '"1"';
					if($journeyData['IsHaveLunch'] == 'true') $eData[$k][1] = '"2"';
					if($journeyData['IsHaveDinner'] == 'true') $eData[$k][2] = '"3"';
					$jData['dinner'] = '['.implode(',',$eData[$k]).']';
					unset($eData[$k]);
					if($jData['dinner'] == '[]') $jData['dinner'] = null;
					$rsId = M('trip')->where("pid=".$pid." and ordid=".$jData['ordid'])->getField('id');
					if($rsId){
						unset($jData['scene']);   //景点库不更新
						M('trip')->where("id=".$rsId)->save($jData);
					}else{
						M('trip')->add($jData);
					}
				}
				
				//更新出发日期信息
				$scheduleCount = $res->Products->Product[$j]->ProductSchedules->ProductSchedule->count();
				$minprice = 0;
				for($k=0;$k<$scheduleCount;$k++){
					$scheduleData = (array)$res->Products->Product[$j]->ProductSchedules->ProductSchedule[$k];
					if($k==0) M('departure_time')->where("ProductID='".$scheduleData['ProductID']."'")->delete();  //new
					//最低价格
					$minprice = $minprice?$minprice:$scheduleData['PersonPrice'];
					if($scheduleData['PersonPrice'] < $minprice){
						$minprice = $scheduleData['PersonPrice'];
					}
					$updateDate['minprice'] = $minprice;
					$updateDate['market_price'] = $minprice * 1.1;
					if(($k+1) == $scheduleCount) M('goods')->where("ProductID='".$srcData['ProductID']."'")->save($updateDate);
					$sData['ProductID'] = $scheduleData['ProductID'];
					$sData['ScheduleID'] = $scheduleData['ScheduleID'];
					$sData['departure_time'] = strtotime($scheduleData['ScheduleDate']);
					$sData['price'] = $scheduleData['PersonPrice'];
					$sData['child_price'] = $scheduleData['ChildPrice'];
					$sData['pid'] = $pid;
					$sData['ordid'] = 0;
					$sData['is_del'] = 0;
					// $rss = M('departure_time')->where("ScheduleID='".$sData['ScheduleID']."'")->find();
					// if($rss['id']){
						// M('departure_time')->where("ScheduleID='".$sData['ScheduleID']."'")->save($sData);
					// }else{
						// M('departure_time')->add($sData);
					// }
					M('departure_time')->add($sData);   //new
				}
			}
		}
		$this->ajaxReturn($returnData,'success',1);
	}
	
	//产品是否存在
	public function updateProduct($ProductID,$Data){
		
		$mod = M('goods');
		$rs = $mod->where("ProductID='".$ProductID."'")->find();
		if($rs['id']){
			unset($Data['imgurl']);  //imgurl字段不更新
			unset($Data['cate_id']);  //cate_id字段不更新
			unset($Data['name']);    //name字段不更新
			unset($Data['info']);    //info字段不更新
			unset($Data['add_time']); //add_time字段不更新
			unset($Data['fees_in']);
			unset($Data['fees_out']);
			$mod->where("ProductID='".$ProductID."'")->save($Data);
		}else{
			$mod->add($Data);
		}
				
	}
	
	public function getProductFile($num = 1){
		$postUrl = 'http://api.lvyouquan.cn/BusinessXml/'.$this->SecretKey.'/Product/Detail'.$num.'.xml';
		$rs = http_request($postUrl);
		$xml = simplexml_load_string($rs);
		return $xml;
	}
	
	//或的文件列表
	public function getTotalFileNum(){
		$postUrl = 'http://api.lvyouquan.cn/BusinessXml/'.$this->SecretKey.'/Product/Total.xml';
		$rs = http_request($postUrl);
		$xml = (array)simplexml_load_string($rs);
		$this->ajaxReturn($xml,'success',1);
		//return $xml;
	}
	
}