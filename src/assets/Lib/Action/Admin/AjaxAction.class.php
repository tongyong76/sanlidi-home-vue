<?php
class AjaxAction extends BaseAction {	
	public function word(){
		$field = $_REQUEST['field'];
		$val = $_REQUEST['val'];
		$id = $_REQUEST['id'];
		$mod = M('ad');
		$mod->where('id='.$id)->setField($field,$val);
		$this->ajaxReturn('','',1);
	}
	
	//订单金额修改
	public function orderEdit(){
		//修改订单价格
		$field = $_REQUEST['field'];
		$val = $_REQUEST['val'];
		$id = $_REQUEST['id'];
		$mod = M('order');
		$mod->where('id='.$id)->setField($field,$val);
		
		//修改操作记录
		$dataModify['modify_type'] = '后台价格修改';
		$dataModify['modify_time'] = time();
		$dataModify['reason'] = '';
		$dataModify['admin_id'] = $this->uid;
		$dataModify['order_id'] = $id;
		M('order_modify')->add($dataModify);
		
		$this->ajaxReturn('','',1);
	}
	
	public function getScene(){
		$scene = "";
		$num = 0;
		$srcData = $_REQUEST['srcData'];
		$res = M('scenic')->where('is_del=0')->select();
		foreach($res as $key=>$value){
			if(strpos($srcData,$value['name'])){
				if($num){
					$scene .= ','.$value['name'];
				}else{
					$scene .= $value['name'];
				}
				$num++;
			}
		}
		$this->ajaxReturn($scene,'成功',1);
	}
	
	//获取下级分类的ID
	public function getCate(){
		$CateId = $_REQUEST['CateId'];
		$html = '';
		$cateList = M('goods_cate')->where('pid='.$CateId.' and is_del=0')->select();
		foreach($cateList as $key=>$value){
			$html .= '<option value="'.$value['id'].'" class="fs">'.$value['name'].'</option>';
		}
		$this->ajaxReturn($html,'success',1);
	}
	
	//获取酒店对应ID
	public function getHotel(){
		$srcData = $_REQUEST['srcData'];
		$res = M('hotel')->where('hotel_name="'.$srcData.'" and is_del=0')->getField('hotel_id');
		$res = $res?$res:0;
		$this->ajaxReturn($res,'success',1);
	}
	
	//删除图库中对应图片
	public function dropImg(){
		$img_id = $_REQUEST['img_id'];
		M('hotel_gallery')->where('img_id='.$img_id)->delete();
		$this->ajaxReturn($img_id,'success',1);
	}
	
	//删除图库中对应图片
	public function dropImgShop(){
		$img_id = $_REQUEST['img_id'];
		M('shopview_gallery')->where('img_id='.$img_id)->delete();
		$this->ajaxReturn($img_id,'success',1);
	}

}