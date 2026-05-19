// JavaScript Document
$(function(){
	//选项卡
	$('#out_sel_name li').click(function(){
		$('#out_sel_name li a').removeClass('sel_route');
		$(this).children("a").addClass('sel_route');
		$('#out_route_list .main_part_item').hide();
		$('#out_route_list .main_part_item').eq($(this).index()).show();
	});
	$('#domestic_sel_name li').click(function(){
		$('#domestic_sel_name li a').removeClass('sel_route');
		$(this).children("a").addClass('sel_route');
		$('#domestic_route_list .main_part_item').hide();
		$('#domestic_route_list .main_part_item').eq($(this).index()).show();
	});
	$('#around_sel_name li').click(function(){
		$('#around_sel_name li a').removeClass('sel_route');
		$(this).children("a").addClass('sel_route');
		$('#around_route_list .main_part_item').hide();
		$('#around_route_list .main_part_item').eq($(this).index()).show();
	});
	//首页幻灯切换
	indexSlider();
	//换一批
	changeGroup();
	
	$("img.lazy").lazyload({effect: "fadeIn",threshold : 200});
});

function indexSlider(){
	var sBtn = $('.mSlider_nav').find('li');
	var sliderPic = $('#sliderCon').find('li');
	var timer=null;
	var iNow=0;
	
	$('.linka img').hover(function(){
		clearInterval(timer);	
	},function(){
		timer=setInterval(toRun,5000);		
	});
	$('.sliderConA img').hover(function(){
		clearInterval(timer);	
	},function(){
		timer=setInterval(toRun,5000);		
	});
	for(var i=0;i<sBtn.length;i++){
		sBtn[i].index=i;
		
		sBtn[i].onmouseover=function(){
			clearInterval(timer);
			iNow=this.index;
			for(var i=0;i<sBtn.length;i++){
				sBtn[i].className='';
				sliderPic[i].className='none';	
			}
			this.className='current';
			sliderPic[this.index].className='';	
		}	
		sBtn[i].onmouseout=function(){
			timer=setInterval(toRun,5000);	
		}
	};
	
	timer=setInterval(toRun,5000);
	function toRun(){
		if(iNow==sBtn.length-1){
			iNow=0;	
		}else{
			iNow++;	
		}	
		for(var i=0;i<sBtn.length;i++){
			sBtn[i].className='';
			sliderPic[i].className='none'	
		};
		sBtn[iNow].className='current';
		sliderPic[iNow].className='';
		
	};
	
	$('.sliderConA').find('img').hover(function(){
		$(this).addClass('leftimg');	
	},function(){
		$(this).removeClass('leftimg');	
	});
};

function changeGroup(){
	$('.change').click(function(){
		var start = $(this).find('input').val();
		$.ajax({
			url: 'index.php?m=Index&a=nextPage',
			type: 'POST',
			data: {start: start},
			dataType: 'json',
			success: function(result){
				$('.h_list_item').find('li').remove();
				$(result.data).appendTo('.h_list_item');
				$('.change').find('input').attr('value',result.info);
			}
		});
	});
};


