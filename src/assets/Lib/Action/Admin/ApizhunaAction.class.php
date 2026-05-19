<?php
class ApizhunaAction extends Action{
	public $agent_id = '2702249';
	public $agent_md = '7965ab3839bdf7b2';

	public function hotellist(){
		header("Content-Type: text/html;charset=utf-8");
		
		$cityid = "0101" ; //城市id，因为是demo所以这里是固定的，正式情况请传入正式id
		$method = "search" ; //要请求的接口,即接口文档中的接口名称
		
		$apiurl = "http://open.zhuna.cn/api/gateway.php?method=".$method."&agent_id=".$this->agent_id."&agent_md=".$this->agent_md."&cityid=".$cityid; 
		echo $apiurl."<br>";
		$jsondata = json_decode(file_get_contents($apiurl),true);
		print_r($jsondata);
	}
	
}