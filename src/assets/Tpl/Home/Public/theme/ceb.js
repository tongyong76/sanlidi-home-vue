// JavaScript Document
$(function(){
	$('.btn_mask').click(function(){
		$.scrollTo('#method',1500);
	});
	$('#run_card').click(function(){
		$.scrollTo('#method',1500);
	});
	
	$('.m_img1 span').click(function(){
		window.location.href='http://xyk.cebbank.com/home/ps/cardapplylist.htm?pro_code=FHTG180000PC04';	
	});
	$('.m_img1 span').hover(function(){
		$(this).children('img').attr('src','http://www.33ly.com/App/Tpl/Home/Public/theme/ceb/apply_hover.png');
	},function(){
		$(this).children('img').attr('src','http://www.33ly.com/App/Tpl/Home/Public/theme/ceb/apply.png');
	});
	
	$('.act_list ul li a').hover(function(){
		$(this).addClass('hover');
	},function(){
		$(this).removeClass();	
	});	
	
	$('.back_top').click(function(){
		$.scrollTo(0,1000);	
	});
		
});


window.onscroll=function(){
	if($(window).scrollTop()>=400){
		$('.back_top').css('display','block');	
	}else{
		$('.back_top').css('display','none');	
	};
		
};