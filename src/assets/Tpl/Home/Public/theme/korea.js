// JavaScript Document
$(function(){
	/*$('.slider ul').css('width',$('.slider ul li').size()*$('.slider ul li').eq(0).width());*/
	$('.re4 li a').eq(0).click(function(){
		$.scrollTo($('#reason').offset().top,1000);	
	});
	$('.re4 li a').eq(3).click(function(){
		$.scrollTo($('#recommend').offset().top,1000);	
	});
	$('.re4 li a').eq(4).click(function(){
		$.scrollTo($('#routes').offset().top,1000);	
	});
	$('.re4 li a').eq(5).click(function(){
		$.scrollTo($('#enjoy').offset().top,1000);	
	});
	
	$('#xuanfu span a').click(function(){
		$.scrollTo(0,1000);	
	});
	
	$('.re1 li').hover(function(){
		$(this).addClass('hover');
	},function(){
		$(this).removeClass('hover');
	});
	$('.re2 li').hover(function(){
		$(this).addClass('hover');
	},function(){
		$(this).removeClass('hover');
	});
	$('.re3 li').hover(function(){
		$(this).addClass('hover');
	},function(){
		$(this).removeClass('hover');
	});
	
	$('.re3 li').eq(3).addClass('nomar');
	$('.re3 li').eq(7).addClass('nomar');
	
	$('#slider').bxSlider({
       mode: 'fade',
  	   captions: true,
	   pager:false,
	   auto:true,
	   speed:200
  	});
	
	
		
});


$(window).scroll(function(){
	if($(window).scrollTop()>=500){
		$('#xuanfu').show();	
	}else{
		$('#xuanfu').hide();	
	};
		
});