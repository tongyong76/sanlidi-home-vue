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
	//返回顶部
	$('.back_top').click(function(){
		$.scrollTo(0,1000);	
	});
	
	$('.info_tlist li a').click(function(){
		var _index=$(this).parent().index();
		if(_index==0){
			$.scrollTo($('.Mbottom').offset().top,500);	
		}else{
			$.scrollTo($('.info_wrap>div').eq(_index).offset().top+1,500);		
		};	
	});
		
});

$(window).scroll(function(){
	var oNavRoute=$('.info_ttitle');
	var sH=$(window).scrollTop();

	sH>=400 ? $('.back_top').show() : $('.back_top').hide();
	//console.log(nowH+'和'+sH);
	
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
$(function(){
	$('.select_box').hover(function(){
		$(this).children('.select_list').show();	
	},function(){
		$(this).children('.select_list').hide();	
	});	
	
	$('#select_rlist li').click(function(){
		$('#select_riqi').html($(this).html());
		$('#select_riqi').css('color','#333');
		var tidData = $(this).attr('s-data');
		$('input[name="tid"]').val(tidData);
		$(this).parent().hide();	
	});
	$('#adA-le').click(function(){
		if($('#adultN').val()>1){
			$('#adultN').val(parseInt($('#adultN').val())-1);		
		}else{
			$('#adultN').val(1);	
		}
		
	});
	
	$('#adA-ri').click(function(){
		$('#adultN').val(parseInt($('#adultN').val())+1);	
	});
	$('#adC-le').click(function(){
		if($('#childN').val()>1){
			$('#childN').val(parseInt($('#childN').val())-1);		
		}else{
			$('#childN').val(1);	
		}	
	});
	$('#adC-ri').click(function(){
		$('#childN').val(parseInt($('#childN').val())+1);	
	});
	
});

//弹出框
function fancybox2(message,url){
	fancy();
	var fancybox2=$(".fancybox2"),fTitle2=$(".fTitle2"),fClose2=$(".fClose2"),fBtn2=$(".fBtn2");
	$(".fancybox2").show();
	

	//$(fTitle2).html(message+"<span class='fClose2'>×</span>");
	
	var fLeft2=($(window).width()-$(fancybox2).width())/2;
	var fTop2=($(window).height()-$(fancybox2).height())/2;
	$(fancybox2).css({top:fTop2,left:fLeft2});
	
	$(fBtn2).hover(function(){
		$(this).css('background','#40a1db');	
	},function(){
		$(this).css('background','#2a98da');	
	});
	$(fBtn2).click(function(){
		//$(this).parent().hide();	
		//$('.fMask').remove();
		//if(url) window.location.href = ROOT_PATH;
	});
	$(fClose2).click(function(){
		$('input[name="tid"]').val('');
		$(this).parent().parent().hide();
		$('.fMask').remove();	
	});
};
//右边悬浮
$(function(){
	var mainBlog=$(".mainBlog");
	var mainBloghx = mainBlog.offset().top+504;
	$(window).scroll(function(){	
		if($(window).scrollTop()>mainBloghx ){

			var xxx = $(window).scrollTop()+$(window).height()+300 >= $(document).height();
			if(!xxx) {
				mainBlog.css({"position":"fixed","top":"20px","width":"100%","z-index":"99"});
			}else{
				mainBlog.removeAttr('style').css({"position":"absolute","bottom":"0px","width":"260px"});
			}
			
		}else{
			mainBlog.css({"position":"relative","top":"0px"});
		}
	})

})





