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
	//首页幻灯切换
	indexSlider();
	$(".case_word").hover(function () {
        $(this).children("a").addClass("icon");
    }, function () {
        $(this).children("a").removeClass("icon");
    });
	$(".back_top").hide();//为了默认隐藏回到顶部
	setbackTop()
	$(window).resize(function(){
		setbackTop()
	})	//返回顶部布局修改
	//返回顶部
	$('.back_top').click(function(){
		$.scrollTo(0,1000);	
	});
	set_sub_type();
    waterfall();
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
};	
$(window).scroll(function(){
	var oNavRoute=$('.type');
	var oNavWord=$('.case');
	var nowH=$('.product').offset().top;
	var sH=$(window).scrollTop();
	sH>=400 ? $('.back_top').show() : $('.back_top').hide();
	sH>=nowH ? $(oNavRoute).addClass('type_fixed') : $(oNavRoute).removeClass('type_fixed');
	sH>=nowH ? $(oNavWord).addClass('case_fixed') : $(oNavWord).removeClass('case_fixed');
	})
function set_sub_type() {
                var arr = [];

            }
            function waterfall() {
                var marginBottom = 24;
                var result = [];
                var cols = 4;
                var colsHeight = [];
                for (var i = 0; i < cols; i++) {
                    colsHeight[i] = 0;
                }
                var container = { w: 1200 };
                var w = 282;
                var marginRight = (container.w - cols * w) / (cols - 1);
                $("#waterfall").css("position", "relative");
                $("#waterfall li:visible").each(function () {
                    var col = 0;
                    for (var i = 1; i < colsHeight.length; i++) {
                        if (colsHeight[i] < colsHeight[col]) {
                            col = i;
                        }
                    }
                    var x = col * (marginRight + w);
                    var y = colsHeight[col];
                    colsHeight[col] = y + $(this).innerHeight() + marginBottom;
                    $(this).css({ margin: 0, position: "absolute" });
                    result[result.length] = { x: x + "px", y: y + "px" };
                });
                var col = 0;
                for (var i = 1; i < colsHeight.length; i++) {
                    if (colsHeight[i] > colsHeight[col]) {
                        col = i;
                    }
                }
                $("#waterfall").css("height", colsHeight[col] + "px");
                var i = 0;
                $("#waterfall li:visible").each(function () {
                    $(this).animate({ left: result[i].x, top: result[i].y });
                    i++;
                });
            }