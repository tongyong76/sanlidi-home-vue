<?php 
class SingleAction extends BaseAction 
{
    public function wifi(){
		
		//门店列表
		$shopList = M('shop')->select();
		$this->assign('shopList',$shopList);
		$this->display();

	}
}
