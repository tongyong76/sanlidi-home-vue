// JavaScript Document
$(function(){
	indexSlider();
	//$("#cy_data").datepicker(); 
	
function indexSlider(){
	var sBtn = $('.mSlider_nav').find('li');
	var sliderPic = $('#sliderCon').find('li');
	var timer=null;
	var iNow=0;
	
	$('.linka img').hover(function(){
		clearInterval(timer);	
	},function(){
		timer=setInterval(toRun,2000);		
	});
	$('.sliderConA img').hover(function(){
		clearInterval(timer);	
	},function(){
		timer=setInterval(toRun,2000);		
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
			timer=setInterval(toRun,2000);	
		}
	};
	
	timer=setInterval(toRun,2000);
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
	
};

	
	

});