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
	//选项卡切换
	$(".tab-btn").find("li").each(function () {
		$(this).click(function (e) {
			$(this).addClass("active").siblings("li").removeClass("active");
			$(".tab-main").find("div.tab-mod").removeClass("active");
			$(".tab-btn").find(".search-city").removeClass("active");
			var _index = $(this).index();
			$(".tab-main").find("div.tab-mod").eq(_index).addClass("active");
			$(".tab-btn").find(".search-city").eq(_index).addClass("active");
			//$("#J-search-city").attr("href", $(this).find("a").attr("data-href")).find("span").html("更多" + $(this).find("a").text() + "相关线路");
			e.stopPropagation();    //  阻止事件冒泡
		})
	})
});

$(window).scroll(function(){
	var nowH=$('.section-wp').offset().top;
	var sH=$(window).scrollTop();
	sH>=400 ? $('.back_top').show() : $('.back_top').hide();
});