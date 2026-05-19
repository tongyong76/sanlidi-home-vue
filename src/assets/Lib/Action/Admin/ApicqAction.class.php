<?php
class ApicqAction extends Action{
	//正式环境
	public $appKey = '100120';
	public $secret = '75ceff10be054407841c22747e26303b';
	public $baseUrl = 'http://openapi.springtour.com/openapi/router?';
	
	//测试环境
	// public $appKey = '100141';
	// public $secret = 'f2aeec1487f143be9d40ab7861f88bc4';
	// public $baseUrl = 'http://124.74.46.118:5001/openapi/router?';
	
	public function _initialize() {
		
		header("Content-Type:text/html; charset=UTF-8");
		set_time_limit(0);
		
	}
	
	//列表显示
	public function index(){
		
		$this->display();
		
	}
	
	/**自动更新 定时更新
	  *
	  * @return void
	  *
	  */
	public function autoUpdateAll(){
		
		$pList = M('goods')->field('id,ProductID,productAttribute,systemType')->where('GroupId = 2')->order('id desc')->select();
		foreach($pList as $key=>$value){
		//if($value['ProductID'] == 8166){
			//sleep(1);
			$baseInfo = $this->getProductBaseInfoSingle($value['productAttribute'],$value['systemType'],$value['ProductID']);
			$exist = count($baseInfo);
			if($exist){
				$desc = $this->getProductDescription($value['ProductID']);
				//更新线路
				$dstData['name'] = $baseInfo[0]['productName'];
				//分类 待补充
				$dstData['startCity'] = $baseInfo[0]['departCityName'];
				if($value['productAttribute'] == 7){
					$dstData['minprice'] = $baseInfo[0]['price'] + 300;
				}
				if($value['productAttribute'] == 13){
					$dstData['minprice'] = $baseInfo[0]['price'] + 200;
				}
				$dstData['days'] = $baseInfo[0]['travelDays'];
				$dstData['sign_up'] = $baseInfo[0]['advanceDay'];	
				$dstData['productPatternName'] = $baseInfo[0]['productPatternName'];
				$dstData['info'] = $desc['productRecommend'];
				$dstData['fees_in'] = $desc['feeNotice']['feeInclude'];
				$dstData['fees_out'] = $desc['feeNotice']['feeUninclude'];
				$dstData['notice'] = $productBookingNotice['warmTips'].$productBookingNotice['importantProvisions'].$productBookingNotice['supplementaryArticle'].$productBookingNotice['travelAlert'];
				$dstData['last_time'] = time();
				$dstData['is_del'] = 0;
				M('goods')->where("ProductID='".$value['ProductID']."'")->save($dstData);
				
				//更新行程
				M('trip')->where(array('pid'=>$value['id']))->delete();
				$JourneyListCount = count($desc['productJourneyList']);
				for($k=0;$k<$JourneyListCount;$k++){
					$JourneyData = $desc['productJourneyList'][$k];
					
					$jData['pid'] = $value['id'];
					$jData['name'] = $JourneyData['theme'];
					$jData['hotel'] = $JourneyData['hotel'] ? $JourneyData['hotel'] : '';
					$jData['ordid'] = $JourneyData['dayOfTrip'];
					$jData['info'] = $JourneyData['description'];
					if($JourneyData['food']){
						$jData['dinner'] = '[';
						if(strstr($JourneyData['food'],'早餐：酒店含早')) $jData['dinner'] .= '"1",';
						if(strstr($JourneyData['food'],'晚餐：团餐')) $jData['dinner'] .= '"2",';
						if(strstr($JourneyData['food'],'午餐：团餐')) $jData['dinner'] .= '"3",';
						$jData['dinner'] .= ']';
						$jData['dinner'] = str_replace('",]','"]',$jData['dinner']);
					}else{
						$jData['dinner'] = null;
					}
					
					M('trip')->add($jData);
				}			
				
				//更新价格
				$scheduleList = $this->getProductSchedule($value['ProductID']);
				$scheduleCount = count($scheduleList);
				$minprice = 0;
				for($k=0;$k<$scheduleCount;$k++){
					$scheduleData = $scheduleList[$k];
					if($k==0) M('departure_time')->where(array('pid'=>$value['id']))->delete();  //new
					//minprice
					$sData['ProductID'] = $value['ProductID'];
					$sData['ScheduleID'] = 0;
					$sData['departure_time'] = strtotime($scheduleData['day']);
					if($value['productAttribute'] == 7){
						$sData['price'] = $scheduleData['adultPrice'] + 300;
						$sData['child_price'] = $scheduleData['childPrice'] + 300;
					}
					if($value['productAttribute'] == 13){
						$sData['price'] = $scheduleData['adultPrice'] + 200;
						$sData['child_price'] = $scheduleData['childPrice'] + 200;
					}
					//roomPrice
					$sData['pid'] = $value['id'];
					$sData['ordid'] = 0;
					$sData['is_del'] = 0;
					M('departure_time')->add($sData);   //new
				}
			}else{
				//不存在
				//删除价格
				//M('departure_time')->where(array('pid'=>$value['id']))->delete();
				//删除行程
				//M('trip')->where(array('pid'=>$value['id']))->delete();
				//删除线路
				M('goods')->where(array('id'=>$value['id']))->save(array('is_del'=>1,'minprice'=>0));
			}
		//}
		}
		
	}
	
	//后台测试
	public function test(){
		$res = $this->getProductBaseInfo(13,0);
		var_dump($res[0]);
		echo "<br>";
		echo "<br>";
		$res2 = $this->getProductDescription(43974);
		var_dump($res2);
		echo "<br>";
		echo "<br>";
		$res3 = $this->getProductSchedule(43974);
		var_dump($res3);
	}
	
	/**查询单条产品的基本信息
	  *
	  * @param $productId string 产品ID
	  * @param $productAttribute string 1.门票  4.邮轮  6.签证 7.团队游  13 自由行
	  * @param $systemType string 0.国内 1.出境
	  * @return $res array
	  *
	  */
	public function getProductBaseInfoSingle($productAttribute,$systemType,$productId){
		
		//系统参数
		$param['method'] = 'product.queryBaseInfo';
		$param['format'] = 'json';
		$param['appKey'] = $this->appKey;
		$param['locale'] = 'zh_CN';
		$param['v'] = '2.0';
		
		//业务参数
		$productSelectCriteria['productId'] = $productId;
		$productSelectCriteria['productAttribute'] = $productAttribute;
		$productSelectCriteria['systemType'] = $systemType;
		$param['productSelectCriteria'] = json_encode($productSelectCriteria);
		
		$pagination['currentPage'] = "1";
		$pagination['pageSize'] = "1";
		$param['pagination'] = json_encode($pagination);
		
		ksort($param);  //排序
		$string = $this->__getString($param);
		$string = $this->secret.$string.$this->secret;
		$param['sign'] = strtoupper(SHA1($string));  //获得签名
		
		//var_dump($param);
		
		$url = $this->__formatUrl($param);
		$url = $this->baseUrl.$url;
		
		$res = http_post($url);
		$res = json_decode($res,true);
		//var_dump($res);
		
		return $res['successResponse']['productBaseInfoList'];
		
	}
	
	/**获取线路总数
	  *
	  * @return $res array
	  */
	public function getNum(){
		
		$param1 = $_REQUEST['param1'];
		$param2 = $_REQUEST['param2'];
		$res = $this->getProductBaseInfo($param1,$param2,1,1,0);
		$this->ajaxReturn($res,0,1);
		
	}
	
	/**获取列表并缓存
	  *
	  * @return $res array
	  */
	public function getProductList(){
		
		$param1 = $_REQUEST['param1'];
		$param2 = $_REQUEST['param2'];
		$param3 = $_REQUEST['param3'];
		$param4 = $_REQUEST['param4'];
		
		$list = $this->getProductBaseInfo($param1,$param2,$param3,$param4);
		SESSION('productList',$list);
		SESSION('param1',$param1);
		SESSION('param2',$param2);
		$res = count($list);
		$this->ajaxReturn($res,0,1);
		
	}
	
	/**ajax逐步更新线路并返回线路
	  *
	  * @return $res array
	  */
	public function updateOneByOne(){
		
		$nowIndex = $_REQUEST['nowIndex'];
		$listNum = $_REQUEST['listNum'];
		$param1 = $_REQUEST['param1'];
		$param2 = $_REQUEST['param2'];
		
		if($nowIndex < $listNum){
			$list = SESSION('productList');
			
			if($list == ''){
				$this->ajaxReturn('','更新列表为空',0);
			}else{
				$value = $list[$nowIndex];
				
				$dstData = array();  //初始化$dstData
				$desc = $this->getProductDescription($value['productId']);
				
				//映射
				$dstData['sn'] = 'T'.date('ymdHis',time());
				$dstData['name'] = $value['productName'];
				//分类 待补充
				$dstData['startCity'] = $value['departCityName'];
				$dstData['GroupId'] = 2;   //0本地 1旅游圈 2春秋
				$dstData['tag'] = 'CQ'.$value['productId'];
				if($param1 == 7){
					$dstData['minprice'] = $value['price'] + 300;
				}
				if($param1 == 13){
					$dstData['minprice'] = $value['price'] + 200;
				}
				$dstData['days'] = $value['travelDays'];
				$dstData['sign_up'] = $value['advanceDay'];	
				$dstData['ProductID'] = $value['productId'];
				$dstData['productPatternName'] = $value['productPatternName'];
				if($param2 == 0 and $value['travelDays'] <=3){
					$dstData['type_id'] = 1;
				}elseif($param2 == 0 and $value['travelDays'] > 3){
					$dstData['type_id'] = 2;
				}elseif($param2 == 1){
					$dstData['type_id'] = 3;
				}else{
					$dstData['type_id'] = '';
				}
				$dstData['productAttribute'] = $param1;
				$dstData['systemType'] = $param2;
				$dstData['info'] = $desc['productRecommend'];
				$dstData['fees_in'] = $desc['feeNotice']['feeInclude']?$desc['feeNotice']['feeInclude']:'';
				$dstData['fees_out'] = $desc['feeNotice']['feeUninclude']?$desc['feeNotice']['feeUninclude']:'';
				$dstData['notice'] = $desc['productBookingNotice']['warmTips'].$desc['productBookingNotice']['importantProvisions'].$desc['productBookingNotice']['supplementaryArticle'].$desc['productBookingNotice']['travelAlert'];
				$dstData['is_ds'] = 0;
				$dstData['is_del'] = 0;
				$dstData['is_show'] = 0;
				$dstData['add_time'] = time();
				$dstData['last_time'] = time();
				
				$updateRes = $this->__updateGoods($value['productId'],$dstData);
				//基础信息更新完毕
				
				//更新行程
				if($updateRes['status'] == 'add'){
					M('trip')->where(array('pid'=>$updateRes['goods_id']))->delete();
					$JourneyListCount = count($desc['productJourneyList']);
					for($k=0;$k<$JourneyListCount;$k++){
						$JourneyData = $desc['productJourneyList'][$k];
						
						$jData['pid'] = $updateRes['goods_id'];
						$jData['name'] = $JourneyData['theme'];
						$jData['hotel'] = $JourneyData['hotel'] ? $JourneyData['hotel'] : '';
						$jData['ordid'] = $JourneyData['dayOfTrip'];
						$jData['info'] = $JourneyData['description'];
						if($JourneyData['food']){
							$jData['dinner'] = '[';
							if(strstr($JourneyData['food'],'早餐：酒店含早')) $jData['dinner'] .= '"1",';
							if(strstr($JourneyData['food'],'晚餐：团餐')) $jData['dinner'] .= '"2",';
							if(strstr($JourneyData['food'],'午餐：团餐')) $jData['dinner'] .= '"3",';
							$jData['dinner'] .= ']';
							$jData['dinner'] = str_replace('",]','"]',$jData['dinner']);
						}else{
							$jData['dinner'] = null;
						}
						
						M('trip')->add($jData);
					}
				}
				if($updateRes['status'] == 'update'){
					//暂不处理
				}
					
				//更新价格
				$scheduleList = $this->getProductSchedule($value['productId']);
				$scheduleCount = count($scheduleList);
				for($k=0;$k<$scheduleCount;$k++){
					$scheduleData = $scheduleList[$k];
					if($k==0) M('departure_time')->where(array('pid'=>$updateRes['goods_id']))->delete();  //new
					//minprice
					$sData['ProductID'] = $value['productId'];
					$sData['ScheduleID'] = 0;
					$sData['departure_time'] = strtotime($scheduleData['day']);
					//加价设置
					$param1 = SESSION('param1');
					$param2 = SESSION('param2');
					if($param1 == 7){
						$addPrice = 300;
						$addChildPrice = 300;
					}
					if($param1 == 13){
						$addPrice = 200;
						$addChildPrice = 200;
					}
					
					$sData['price'] = $scheduleData['adultPrice'] + $addPrice;
					$sData['child_price'] = $scheduleData['childPrice'] + $addChildPrice;
					//roomPrice
					$sData['pid'] = $updateRes['goods_id'];
					$sData['ordid'] = 0;
					$sData['is_del'] = 0;
					M('departure_time')->add($sData);   //new
				}
				$nowIndex++;
				$info = '<p>本次更新共'.$listNum.'个产品，第'.$nowIndex.'个更新完毕...</p>';
				$this->ajaxReturn($nowIndex,$info,1);
			}			
		}else{
			SESSION('productList',NULL);
			SESSION('param1',NULL);
			SESSION('param2',NULL);
			$this->ajaxReturn($nowIndex,'更新完毕！',2);
		}
		
	}
	
	/**ajax异步更新所有线路
	  *
	  * @return $res array
	  */
	public function doUpdateAll(){
		
		$param1 = $_REQUEST['param1'];
		$param2 = $_REQUEST['param2'];
		$param3 = $_REQUEST['param3'];
		$param4 = $_REQUEST['param4'];
		
		$list = $this->getProductBaseInfo($param1,$param2,$param3,$param4);
		$count = count($list);
		
		//入库
		foreach($list as $value){
			$dstData = array();  //初始化$dstData
			
			$desc = $this->getProductDescription($value['productId']);
			
			//映射
			$dstData['sn'] = 'T'.date('ymdHis',time());
			$dstData['name'] = $value['productName'];
			//分类 待补充
			$dstData['startCity'] = $value['departCityName'];
			$dstData['GroupId'] = 2;   //0本地 1旅游圈 2春秋
			$dstData['tag'] = 'CQ'.$value['productId'];
			if($param1 == 7){
				$dstData['minprice'] = $value['price'] + 300;
			}
			if($param1 == 13){
				$dstData['minprice'] = $value['price'] + 200;
			}			
			$dstData['days'] = $value['travelDays'];
			$dstData['sign_up'] = $value['advanceDay'];	
			$dstData['ProductID'] = $value['productId'];
			$dstData['productPatternName'] = $value['productPatternName'];
			if($param2 == 0 and $value['travelDays'] <=3){
				$dstData['type_id'] = 1;
			}elseif($param2 == 0 and $value['travelDays'] > 3){
				$dstData['type_id'] = 2;
			}elseif($param2 == 1){
				$dstData['type_id'] = 3;
			}else{
				$dstData['type_id'] = '';
			}
			$dstData['info'] = $desc['productRecommend'];
			$dstData['fees_in'] = $desc['feeNotice']['feeInclude'];
			$dstData['fees_out'] = $desc['feeNotice']['feeUninclude'];
			$dstData['notice'] = $productBookingNotice['warmTips'].$productBookingNotice['importantProvisions'].$productBookingNotice['supplementaryArticle'].$productBookingNotice['travelAlert'];
			$dstData['is_ds'] = 0;
			$dstData['is_del'] = 0;
			$dstData['is_show'] = 0;
			$dstData['add_time'] = time();
			$dstData['last_time'] = time();
			
			$updateRes = $this->__updateGoods($value['productId'],$dstData);
			//基础信息更新完毕
			
			//更新行程
			if($updateRes['status'] == 'add'){
				M('trip')->where(array('pid'=>$updateRes['goods_id']))->delete();
				$JourneyListCount = count($desc['productJourneyList']);
				for($k=0;$k<$JourneyListCount;$k++){
					$JourneyData = $desc['productJourneyList'][$k];
					
					$jData['pid'] = $updateRes['goods_id'];
					$jData['name'] = $JourneyData['theme'];
					$jData['hotel'] = $JourneyData['hotel'] ? $JourneyData['hotel'] : '';
					$jData['ordid'] = $JourneyData['dayOfTrip'];
					$jData['info'] = $JourneyData['description'];
					if($JourneyData['food']){
						$jData['dinner'] = '[';
						if(strstr($JourneyData['food'],'早餐：酒店含早')) $jData['dinner'] .= '"1",';
						if(strstr($JourneyData['food'],'晚餐：团餐')) $jData['dinner'] .= '"2",';
						if(strstr($JourneyData['food'],'午餐：团餐')) $jData['dinner'] .= '"3",';
						$jData['dinner'] .= ']';
						$jData['dinner'] = str_replace('",]','"]',$jData['dinner']);
					}else{
						$jData['dinner'] = null;
					}
					
					M('trip')->add($jData);
				}
			}
			if($updateRes['status'] == 'update'){
				//暂不处理
			}
				
			//更新价格
			$scheduleList = $this->getProductSchedule($value['productId']);
			$scheduleCount = count($scheduleList);
			$minprice = 0;
			for($k=0;$k<$scheduleCount;$k++){
				$scheduleData = $scheduleList[$k];
				if($k==0) M('departure_time')->where(array('pid'=>$updateRes['goods_id']))->delete();  //new
				//minprice
				$sData['ProductID'] = $value['productId'];
				$sData['ScheduleID'] = 0;
				$sData['departure_time'] = strtotime($scheduleData['day']);
				$sData['price'] = $scheduleData['adultPrice'];
				$sData['child_price'] = $scheduleData['childPrice'];
				//roomPrice
				$sData['pid'] = $updateRes['goods_id'];
				$sData['ordid'] = 0;
				$sData['is_del'] = 0;
				M('departure_time')->add($sData);   //new
			}
			
			//var_dump($value);
			//var_dump($desc);
			
		//var_dump($scheduleList);
		}
		
		// //状态码-根据conut自动识别是否是最后一组数据
		// if($count == $param4){
			// //go on
		// }else{
			// //end
		// }	
		
	}
	
	/**春秋产品基本信息更新
	  * @param $ProductID string 供应商产品编号
	  * @param $Data array 更新字段数组
	  * @return $res array 
	  */
	private function __updateGoods($ProductID,$Data){
		
		$mod = M('goods');
		$rs = $mod->where("ProductID='".$ProductID."'")->getfield('id');
		if($rs){
			// unset($Data['imgurl']);  //imgurl字段不更新
			// unset($Data['cate_id']);  //cate_id字段不更新
			// unset($Data['name']);    //name字段不更新
			// unset($Data['info']);    //info字段不更新
			unset($Data['add_time']); //add_time字段不更新
			unset($Data['is_show']); //is_show字段不更新
			// unset($Data['fees_in']);
			// unset($Data['fees_out']);
			$mod->where("ProductID='".$ProductID."'")->save($Data);
			$res['status'] = 'update';
			$res['goods_id'] =$rs;
		}else{
			$newId = $mod->add($Data);
			$res['status'] = 'add';
			$res['goods_id'] =$newId;
		}
		return $res;
		
	}
	
	
	/**产品基本信息查询
	  * @param $productAttribute string 1.门票  4.邮轮  6.签证 7.团队游  13 自由行
	  * @param $systemType string 0.国内 1.出境
	  * @param $currentPage string 当前页数
	  * @param $pageSize string 每页展示条数
	  * @return $res array
	  */
	public function getProductBaseInfo($productAttribute,$systemType,$currentPage=1,$pageSize=1,$returnType=1){
		//系统参数
		$param['method'] = 'product.queryBaseInfo';
		$param['format'] = 'json';
		$param['appKey'] = $this->appKey;
		$param['locale'] = 'zh_CN';
		$param['v'] = '2.0';
		
		//业务参数
		$productSelectCriteria['productAttribute'] = $productAttribute;
		$productSelectCriteria['systemType'] = $systemType;
		$param['productSelectCriteria'] = json_encode($productSelectCriteria);
		
		$pagination['currentPage'] = $currentPage;
		$pagination['pageSize'] = $pageSize;
		$param['pagination'] = json_encode($pagination);
		
		ksort($param);  //排序
		$string = $this->__getString($param);
		$string = $this->secret.$string.$this->secret;
		$param['sign'] = strtoupper(SHA1($string));  //获得签名
		
		$url = $this->__formatUrl($param);
		$url = $this->baseUrl.$url;
		
		$res = http_post($url);
		$res = json_decode($res,true);
		
		if($returnType == 1){
			return $res['successResponse']['productBaseInfoList'];
		}else{
			return $res['successResponse']['pagination']['totalCount'];
		}
	}
	
	/**产品描述查询
	  * @param $productId 春秋产品ID
	  * @param $productAttribute string 1.门票  4.邮轮  6.签证 7.团队游  13 自由行
	  * @param $systemType string 0.国内 1.出境
	  * @return $res array
	  */
	public function getProductDescription($productId){	
		//系统参数
		$param['method'] = 'product.queryProductDescription';
		$param['format'] = 'json';
		$param['appKey'] = $this->appKey;
		$param['locale'] = 'zh_CN';
		$param['v'] = '2.0';
		
		//业务参数
		$productSelectCriteria['productId'] = $productId;
		$param['productSelectCriteria'] = json_encode($productSelectCriteria);
		
		ksort($param);  //排序
		$string = $this->__getString($param);
		$string = $this->secret.$string.$this->secret;
		$param['sign'] = strtoupper(SHA1($string));  //获得签名
		
		$url = $this->__formatUrl($param);
		$url = $this->baseUrl.$url;
		
		$res = http_post($url);
		$res = json_decode($res,true);
		return $res['successResponse'];
		
	}
	
	/**产品班期查询
	  * @param $productId 春秋产品ID
	  * @param $productAttribute string 1.门票  4.邮轮  6.签证 7.团队游  13 自由行
	  * @param $systemType string 0.国内 1.出境
	  * @return $res array
	  */
	public function getProductSchedule($productId){	
		//系统参数
		$param['method'] = 'product.queryProductSchedule';
		$param['format'] = 'json';
		$param['appKey'] = $this->appKey;
		$param['locale'] = 'zh_CN';
		$param['v'] = '2.0';
		
		//业务参数
		$productSelectCriteria['productId'] = $productId;
		$param['productSelectCriteria'] = json_encode($productSelectCriteria);
		
		ksort($param);  //排序
		$string = $this->__getString($param);
		$string = $this->secret.$string.$this->secret;
		$param['sign'] = strtoupper(SHA1($string));  //获得签名
		
		$url = $this->__formatUrl($param);
		$url = $this->baseUrl.$url;
		
		$res = http_post($url);
		$res = json_decode($res,true);

		return $res['successResponse'];
		
	}
	
	//将一维数组拼接成字符串
	private function __getString($arr){
		foreach($arr as $key=>$value){
			$string = $string.$key.$value;
		}
		return $string;
	}
	
	//格式化参数
	private function __formatUrl($arr){
		foreach($arr as $key=>$value){
			$newArr[] = $key.'='.$value;
		}
		$string = implode('&',$newArr);
		return $string;
	}
	
	
}