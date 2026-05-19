// JavaScript Document
var setbackTop=function(){
	var retop=$("#retop");
	var pageW=$(window).width();
	if(pageW<1320)
		 retop.css({"margin-left":pageW/2-50+"px"});
	else
		 retop.css({"margin-left":"610px"});
}
$(function(){
	$(".back_top").hide();//为了默认隐藏回到顶部
	setbackTop()
	$(window).resize(function(){
		setbackTop()
	})	//返回顶部布局修改
	$('input[name="tid"]').val('');
	var mod = $('input[name="mod"]').val();
	
	//行程列表展开收起
	$('.day_detail').each(function(i){

		var rori=$.trim($(this).html());
		rori=rori.replace(/(<br>)+(<\/p>)$/i,'</p>');
		
		var rHeight = $(this).height();
		
		var dStr=$('.day_detail').eq(i).html();
		dStr=$.trim(dStr.replace(/<[^>].*?>/g,''));
		dStr=strCut(dStr,200);
		
		//原始数据、截断后数据
		var rDiv="<div class='oridata'>"+rori+"</div>";
		var nDiv="<div class='nowdata'>"+dStr+"</div>";
		$('.day_detail').eq(i).html(rDiv+nDiv);
		if(rHeight > 70){
			$(this).attr('maxheight',rHeight);
			$(this).find('.oridata').hide();
			$(this).find('.nowdata').append($("<a class='show_all' href='javascript:void(0)'>【展开内容】</a>"));
			
		}else{
			//$(this).find('.oridata').append($("<a class='hidden_all' href='javascript:void(0)'>【收起内容】</a>"));
			$(this).find('.nowdata').hide();
		};
		
		
	});
	//预订须知展开收起
	$('.sh_ydxz').each(function(i){
		
		var costHeight = $(this).height();

		var costori=$.trim($(this).html());
		costori=costori.replace(/(<br>)+(<\/p>)$/i,'</p>');
		var costStr=$('.sh_ydxz').eq(i).html();
		costStr=$.trim(costStr.replace(/<[^>].*?>/g,''));
		costStr=strCut(costStr,200);
		
		//原始数据、截断后数据
		var costrDiv="<div class='oridata'>"+costori+"</div>";
		var costnDiv="<div class='nowdata'>"+costStr+"</div>";
		$('.sh_ydxz').eq(i).html(costrDiv+costnDiv);
		if(costHeight > 70){
			$(this).attr('maxheight',costHeight);
			$(this).find('.oridata').hide();
			$(this).find('.nowdata').append($("<a class='show_all' href='javascript:void(0)'>【展开内容】</a>"));
			
		}else{
			$(this).find('.nowdata').hide();
		};
	});
	
	$('.show_all').bind('click',function(){
		var maxheight = $(this).parent().parent().attr('maxheight');
		$(this).parent().hide();
		$(this).parent().prev().find('p:last').append($("<a class='hidden_all' href='javascript:void(0)'>【收起内容】</a>"));
		$(this).parent().prev().show();
		$(this).parent().parent().animate({height:maxheight},300);
		
		
		$('.hidden_all').bind('click',function(){
		$(this).parent().parent().hide();
		$(this).parent().parent().next().show();
		$(this).parent().parent().parent().animate({height:"70"},300);	
		
		$(this).remove();
			
		});	
		
	});

 	//时间线去背景
	$('.intro_area').last().addClass('no_timeline');

	//线路二维码
	$('.ecode_img').hover(function(){
		$(this).find('.ecode').stop().fadeToggle(200);
	});
	
	//起价说明
	$('.declare').hover(function(){
		$(this).children('div').show();	
	},function(){
		$(this).children('div').hide();		
	});
	
	//返回顶部
	$('.back_top').click(function(){
		$.scrollTo(0,1000);	
	});
	
	$('.info_tlist li a').eq(0).click(function(){
		$.scrollTo($('.Mbottom').offset().top,500);
	});
	$('.info_tlist li a').eq(1).click(function(){
		$.scrollTo($('#r_cost').offset().top,500);
	});
		
});

$(window).scroll(function(){
	var oNavRoute=$('.info_ttitle');
	var nowH=$('.Mbottom').offset().top;
	var sH=$(window).scrollTop();

	sH>=400 ? $('.back_top').show() : $('.back_top').hide();
	sH>=nowH ? $(oNavRoute).addClass('fixed_info_top') : $(oNavRoute).removeClass('fixed_info_top');
	
	var dMenu=$('.info_wrap>div');
	var dMenuLen=$(dMenu).size();
	var corres=0;
	for(var i=0;i<dMenuLen;i++){
		if(i==dMenuLen-1){
			if(sH>=$(dMenu).eq(i).offset().top){
				corres=i;
			}
		}else{
			if(sH>=$(dMenu).eq(i).offset().top && sH<$(dMenu).eq(i+1).offset().top){
				corres=i;
			}			
		}
		$('.info_tlist li a').removeClass();
		$('.info_tlist li a').eq(corres).addClass('nhover');			
	};
	
	
});





