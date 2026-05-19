<?php 
class TestAction extends BaseAction 
{	
	public $appKey = '100141';
	public $secret = 'f2aeec1487f143be9d40ab7861f88bc4';
	public $baseUrl = 'http://124.74.46.118:7003/openapi/router?';

	public function index(){
		$str = "0";
		if(empty($str)){
			echo '1';
		}else{
			echo '0';
		}
	}
	
	
	public function xm(){
		
		$res = getGoodsByTime('2016-01-10','2016-12-16');
		foreach($res as $key=>$value){
			
		}
		var_dump($res);
		
	}
	
	public function cq(){
		header("Content-type: text/html; charset=utf-8"); 
		$param['method'] = 'product.queryBaseInfo';
		$param['format'] = 'json';
		$param['appKey'] = $this->appKey;
		$param['locale'] = 'zh_CN';
		$param['v'] = '2.0';
		
		$productSelectCriteria['productAttribute'] = '7';
		$productSelectCriteria['systemType'] = '0';
		$param['productSelectCriteria'] = json_encode($productSelectCriteria);
		
		$pagination['currentPage'] = '1';
		$pagination['pageSize'] = '100';
		$param['pagination'] = json_encode($pagination);
		
		ksort($param);
		
		echo "参数：<br>";
		var_dump($param);
		$string = $this->getString($param);
		$string = $this->secret.$string.$this->secret;
		
		echo "<br>";
		echo "签名字符串：<br>";
		echo $string;
		
		$param['sign'] = strtoupper(SHA1($string));  //获得签名
		//echo "<br><br>sign:".$param['sign']; 
		//$param['sign'] = 'BCDE974FD500797B8511493DE5B2F939CE2CA2D3';
		echo "<br><br>sign:".$param['sign'];
		
		ksort($param);
		
		$url = $this->formatUrl($param);
		$url = $this->baseUrl.$url;
		echo "<br><br>请求URL:<br>";
		var_dump($url);   //打印URL
		
		//curl
		$res = http_post($url);
		$res = json_decode($res,true);
		var_dump($res['successResponse']['productBaseInfoList'][0]);
		
	}
	
	//将一纬数组拼接成字符串
	private function getString($arr){
		foreach($arr as $key=>$value){
			$string = $string.$key.$value;
		}
		return $string;
	}
	
	//格式化参数
	private function formatUrl($arr){
		foreach($arr as $key=>$value){
			$newArr[] = $key.'='.$value;
		}
		$string = implode('&',$newArr);
		return $string;
	}
}
