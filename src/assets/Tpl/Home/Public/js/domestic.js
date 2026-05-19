// JavaScript Document
$(function(){
	$('.gn_slider').bxSlider({
   		mode: 'fade',
		pause:5000,
   		captions: true,
   		pager:true,
		controls:false, //不显示左右按钮
   		auto:true,
  	});
	
	//更多国内游推荐
	var aDBtn=$('.domore_list').find('a');
	var aCity=$('.stra_bj');
	
	$(aDBtn).click(function(){
		var _index=$(this).index();
		
		$(aDBtn).removeClass();
		$(this).addClass('do_active');
		$(aCity).hide();
		$(aCity).eq(_index).show();
			
	});
	
	
});