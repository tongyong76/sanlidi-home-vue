// JavaScript Document
var setbackTop=function(){
	var retop=$("#retop");
	var pageW=$(window).width();
	if(pageW<1320)
		 retop.css({"margin-left":pageW/2-50+"px"});
	else
		 retop.css({"margin-left":"610px"});
}
$(function () {
	$(".back_top").hide();//为了默认隐藏回到顶部

	//返回顶部
	$('.back_top').click(function(){
		$.scrollTo(0,1000);	
	});
	setbackTop()
	$(window).resize(function(){
		setbackTop()
	})	
});

$(window).scroll(function(){
	var sH=$(window).scrollTop();
	sH>=400 ? $('.back_top').show() : $('.back_top').hide();
});