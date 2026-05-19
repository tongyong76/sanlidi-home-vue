// JavaScript Document
$(function(){
	$('.zb_slider').bxSlider({
   		mode: 'fade',
		pause:5000,
   		captions: true,
   		pager:true,
		controls:false, //不显示左右按钮
   		auto:true
		
  	});
	
	//更多周边游推荐
	var repic = $('.recom_pic');
	var aRLi=$(repic).find('li');
	var picNum=$(aRLi).size();
	var liWidth=$(aRLi).eq(0).width()+20;
	var larrow=$('.left_arrow');
	var rarrow=$('.right_arrow');
	var iNum=1;
	var bBtn=true;
	
	
	$(larrow).click(function(){
		if(bBtn){
			bBtn=false;
			aRLi=$(repic).find('li');
			for(var i=0;i<iNum;i++){
				//console.log(picNum-i-1);
				$(repic).css('left',-liWidth*iNum);
				$(repic).css('width',liWidth*(picNum+iNum));
				var oLi=aRLi[picNum-i-1].cloneNode(true);
				$(repic).prepend(oLi);
					
			};	
			$(repic).animate({left:0},function(){
				for(var i=0;i<iNum;i++){
					//console.log(picNum-i);
					$(repic).find('li').last().remove();
					
				}	
				bBtn=true;
			});
			
		};
		
			
	});
	
	$(rarrow).click(function(){
		if(bBtn){
			bBtn = false;
			aRLi=$(repic).find('li');
			for(var i=0;i<iNum;i++){
				//console.log(iNum-i);
				$(repic).css('width',liWidth*(picNum+iNum));
				var oLi=aRLi[i].cloneNode(true);
				$(repic).append(oLi);
				//getWidth();	
			};	
			
			$(repic).animate({left : - iNum * liWidth},function(){
			
				for(var i=0;i<iNum;i++){
					$(repic).find('li').eq(0).remove();
					$(repic).css('left','0');	
				}
				
				bBtn = true;
			
			});
		}
			
	
	});
	
	//按照天数选择
	var dayBtn = $('.day_sort li a');
	var daySort = $('.day_sort_item').find('.day_sort_list');
	$(dayBtn).mousemove(function(){
		var index=$(this).parent().index();
		$(dayBtn).removeClass('active');	
		$(this).addClass('active');
		
		$(daySort).hide();
		$(daySort).eq(index).show();
		
	});	
});