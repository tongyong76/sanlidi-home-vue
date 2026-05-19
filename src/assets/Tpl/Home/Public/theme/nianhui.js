var preidx = [];
var num = 0;
var timer;
var timer2;
if(navigator.userAgent.indexOf("MSIE 6.0") > 0)
{
	$('body').html('');
	alert('您的浏览器版本过低，请升级浏览器或更新最新版本');
	window.location.href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie";
}
$(function() {
	$('.bcj_click').click(function(){
		$(this).parent().parent().parent().css('height','auto');
		$(this).hide();
		$(this).parent().find('.bcj_click_off').show();
	});
	$('.bcj_click_off').click(function(){
		$(this).parent().parent().parent().css('height','230px');	
		$(this).hide();
		$(this).parent().find('.bcj_click').show();
	});
	
	$('.pic1').click(function(){
		$.scrollTo($('.jp').offset().top,1000);	
	});
	$('.pic4').click(function(){
		$.scrollTo($('.bj_con').eq(0).offset().top-10,1000);	
	});
	$('.pic5').click(function(){
		$.scrollTo($('.bj_con').eq(1).offset().top-10,1000);	
	});
	
	$('.text').animate({
		paddingTop: 100,
		opacity: 1,
		zIndex: 3
	}, {
		easing: "easeInOutSine",
		duration: 2000,
		complete: function() {
			$('.pic-location').fadeIn(1500, function() {
				timer = setInterval(function() {
					if (num < 8) {
						randomChange();
					}
				}, 1000);
			});
			$('.text_bg').animate({opacity:1});
		}
	});
	//$('.text_bg').animate({opacity:1,zIndex:3},{duration:10000});
	
	$('.filter a').click(function(){
		fancybox($(this).attr('title'),$(this).attr('vhref'));
	});
	
})
var autoChange = function() {
	$('.filter').stop(true, true).fadeIn(1000, function() {
		picHover();
		$('.bg').fadeIn(3000, function() {
			setTimeout(function(){
				$('.bg').fadeOut(2000);
			},2000)
		})
	});
	setTimeout(function() {
		autoFilter();
	}, 6000);
}
var autoFilter = function() {
	var i=-1;
	clearInterval(timer2);
	timer2 = setInterval(function(){
		var idx = parseInt(Math.random() * 8);
		while(i==idx){
			idx = parseInt(Math.random() * 8);
		}
		i = idx;
		$('.header-pic').eq(idx).find('.filter').fadeOut(1500,function(){
			$('.header-pic').eq(idx).find('.filter').fadeIn(1500);
		});
	},1500)
}
var randomChange = function() {
	var idx = parseInt(Math.random() * 8);
	if (preidx[idx] != 1) {
		changeTo(idx);
		num++;
		preidx[idx] = 1;
		if (num >= 8) {
			clearInterval(timer);
			setTimeout(function(){
				autoChange();
			},1000)
			return;
		}
	} else {
		randomChange();
	}
	return;
}
var changeTo = function(index) {
	$('.header-pic').eq(index).fadeIn(1000);
}
var picHover = function() {
	$document = $(document);
	$('.header-pic').on('mouseover', function() {
		clearInterval(timer2);
		//$(this).find('.filter').stop(true, true).fadeOut(1000);
	})
	$('.header-pic').on('mouseleave', function() {
		autoFilter();
		//$('.filter').stop(true, true).fadeIn(1000);
	})
}


function fancy(){
	var fMask=$('<div class="fMask">');
	$(fMask).css('height',$('body').height());
	$('body').append(fMask); 
};

function fancybox(title,url){
	fancy();
	var fancybox=$("<div class='fancybox'>");
	var fTitle=$("<div class='fTitle'>");
	var fClose=$("<span class='fClose'>×</span>");
	var fBtn=$("<div class='fBtn'>");
	
	$(fTitle).html(title);
	$(fBtn).html('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="800" height="452"><param name="movie" value="http://www.33ly.com/Uploads/video/Flvplayer.swf?autostart=true" /><param name="quality" value="high" /><param name="allowFullScreen" value="true" /><param name="FlashVars" value="vcastr_file=http://www.33ly.com/Uploads/video/'+url+'&LogoText=www.33ly.com&BufferTime=3&IsAutoPlay=1" /><embed src="http://www.33ly.com/Uploads/video/Flvplayer.swf?autostart=true" allowfullscreen="true" flashvars="vcastr_file=http://www.33ly.com/Uploads/video/'+url+'&LogoText=www.33ly.com&IsAutoPlay=1" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="800" height="452"></embed></object>');
	$(fTitle).append(fClose);
	$(fancybox).append(fTitle);
	$(fancybox).append(fBtn);
	$("body").append(fancybox);
	
	var fLeft=($(window).width()-$(fancybox).width())/2;
	var fTop=($(window).height()-$(fancybox).height())/2;
	$(fancybox).css({top:fTop,left:fLeft});
	
	$(fClose).click(function(){
		$(this).parent().parent().remove();
		$('.fMask').remove();	
	});
};