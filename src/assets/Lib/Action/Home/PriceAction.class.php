<?php
//首页
class PriceAction extends BaseAction {
    public function index(){

		$stime = strtotime('20160308');
		$etime = strtotime('20160319');
		
		$res = M('wx_gameinfo as gi')->join('33_wx_user as u on u.wx_openid=gi.wx_openid')->join('33_wx_gameprice as gp on gp.price_status=gi.status')->where('exchange_time > '.$stime.' and exchange_time < '.$etime.' and phone_number <> ""')->order('gi.shop_id,gi.status')->select();
		
		var_dump($res[0]);
		
		echo "<br>微信帐号\t|\t姓名\t|\t电话\t|\t兑换奖项\t|\t门店\t|\t申请时间<br>";
		
		foreach($res as $key=>$value){
			switch($value['shop_id']){
				case 1:
					$res[$key]['shopname'] = '市区形象店(总部)';
					break;
				case 2:
					$res[$key]['shopname'] = '园区圆融店(台湾馆)';
					break;
				case 3:
					$res[$key]['shopname'] = '园区永旺店(邮轮馆)';
					break;
				case 4:
					$res[$key]['shopname'] = '新区泉屋店';
					break;
				case 5:
					$res[$key]['shopname'] = '相城繁花店(韩国馆)';
					break;
				case 6:
					$res[$key]['shopname'] = '吴中丽丰店';
					break;
				case 7:
					$res[$key]['shopname'] = '吴中永旺店(海岛馆)';
					break;
				case 8:
					$res[$key]['shopname'] = '吴江正翔店(泰国馆)';
					break;
				case 9:
					$res[$key]['shopname'] = '昆山形象店';
					break;
				case 10:
					$res[$key]['shopname'] = '昆山世茂店';
					break;
				case 11:
					$res[$key]['shopname'] = '常熟旗舰店';
					break;
				case 12:
					$res[$key]['shopname'] = '常熟欧尚店';
					break;
				case 13:
					$res[$key]['shopname'] = '太仓华旭广场店';
					break;
				case 14:
					$res[$key]['shopname'] = '张家港购物公园店';
					break;
				case 15:
					$res[$key]['shopname'] = '南通旗舰店';
					break;
			}
			echo $value['wx_nickname'].'
			|
			'.$value['real_name'].'
			|
			'.$value['phone_number'].'
			|
			'.$value['price_name'].'
			|
			'.$res[$key]['shopname'].'
			|
			'.date('Y-m-d H:i',$value['exchange_time']).'
			<br>';
		}
    }
	
	public function down(){
		set_time_limit(0);
		$mod = M('wx_user');
		$userList = $mod->where('is_local=0')->select();
		mkdir('./Uploads/weixin/');
		import("ORG.Util.Image");
		$image=new Image();
		foreach($userList as $key=>$value){
			preg_match('/^(http:\/\/)/si',$value['wx_headimgurl'],$result);
			if($result){
				$image->thumb($value['wx_headimgurl'],'/Uploads/weixin/'.$value['wx_openid'].'.jpg_320x1000.jpg','',320,10000);
				$image->thumb($value['wx_headimgurl'],'/Uploads/weixin/'.$value['wx_openid'].'.jpg');
				$data['wx_headimgurl']='/Uploads/weixin/'.$value['wx_openid'].'.jpg';
				$data['is_local'] = 1;
				$mod->where('wx_openid="'.$value['wx_openid'].'"')->save($data);
				echo $value['wx_openid'].'<br>';
			}
		}
		var_dump($userList);
		// $where['id'] = $_GET['id'];
		// $item=$items_mod->field('id,img,item_key')->where($where)->find();
		// preg_match('/^(http:\/\/)/si',$item['img'],$result);
		// if ($result){
			// $dir=date("Ymd");
			// mkdir('./Uploads/LocalItems/'.$dir);
			// import("ORG.Util.Image");
			// $image=new Image();
			// $image->thumb($item['img'],'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.jpg_100x1000.jpg','',100,10000);
			// $image->thumb($item['img'],'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.jpg_210x1000.jpg','',210,10000);
			// $image->thumb($item['img'],'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.jpg_350x1000.jpg','',350,10000);
			// $image->thumb($item['img'],'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.jpg_500x1000.jpg','',500,10000);
			// $data['img']=C('web_path').'Uploads/LocalItems/'.$dir.'/'.$item['item_key'].'.jpg';
			// $items_mod->where('id='.$item['id'])->save($data);
			// $this->success('生成本地图片成功');
		// }else {
			// $this->error('图片已下载到本地');
		// }
	}
	
	public function imgtest(){
		// set_time_limit(0);
		// mkdir('./Uploads/weixin/');
		// import("ORG.Util.Image");
		// $image=new Image();
		// $res = $image->thumb('http://www.33ly.com/Uploads/goods/s_54e19b5e8fa9d.jpg','33ly/Uploads/weixin/123.jpg');
		
		$img = file_get_contents('http://www.33ly.com/Uploads/goods/s_54e19b5e8fa9d.jpg'); 
		@file_put_contents('http://localhost/33ly/Uploads/weixin/123.jpg',$img);
	}

}