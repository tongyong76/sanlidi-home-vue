// JavaScript Document
$(function(){
	$('.back_top').click(function(){
		$.scrollTo(0,1000);	
	});
	$('.s_list_bottom dl').hover(function(){
		$(this).addClass('hoverdl');
		$(this).children('dt').css('color','#2a98da');	
		
		$('.s_list_bottom dl dd').hide();
		$(this).children('dd').show();	
	},function(){
		$(this).removeClass('hoverdl');
		$(this).children('dt').css('color','#252525');	
		
		$('.s_list_bottom dl dd').hide();	
	});
	$('.ticket a').hover(function(){
		$(this).toggleClass('ticket_move');	
	});
	
	$('.s_r_list li').removeClass('lihover');
	$('.s_r_list li').hover(function(){
		$(this).addClass('lihover');	
	},function(){
		$('.s_r_list li').removeClass('lihover');	
	});

});

$(window).scroll(function(){
	if($(window).scrollTop()>=217){
		$('.shop_list').css({position:'fixed',top:'0',right:($(window).width()-1200)/2});
		$(".back_top").show();
	}else{
		$('.shop_list').css('position','static');
		$(".back_top").hide();//为了默认隐藏回到顶部
	};
});