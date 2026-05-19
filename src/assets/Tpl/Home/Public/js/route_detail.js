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
	
	
	//日历
	var oCalBox=document.getElementById('tour_cal');
	var oNowTime=document.getElementById('nowTime');
	var aTd=oCalBox.getElementsByTagName('td');
	var aNowSpan = oNowTime.getElementsByTagName('span');
	
	var bBtn=true;
	var oDate=new Date();
	
	var id = $('#tour_id').val();
	var tmonth = oDate.getMonth()+1;
	var year = oDate.getFullYear();
	
	
	//var vData = $('.start_date').html();
	
	//var re=/[0-9]{1,4}/gi;
	//vDD = vData.match(re);  //output "test"
	
	vYear = $('input[name="startYear"]').val();
	vMonth = $('input[name="startMonth"]').val();	
	if(!vYear){
		vYear = oDate.getFullYear();
		vMonth = oDate.getMonth()+1;
	}
	showDate(oNowTime,vYear,vMonth,true);

	showBtn();
	//showColor(oDate.getDate());
	$("#nowTime").on("click",'.enable',function(){
		$(this).parent().parent().find('td').removeClass('select');
		$(this).addClass('select');
		var thisValue = $(this).attr('data');
		$('input[name="tid"]').val(thisValue);
	});
	
	function showDate(obj,year,month,bBtn){
		//console.log($(aTd).size());
		$('.cover').hide();
		
		var oDate=new Date();
		var dayNum=0;
		//console.log(!obj.bBtn);
		if(!obj.bBtn){
			obj.oTitle=document.createElement('div');	
			obj.oTitle.className='calTitle';
			obj.appendChild(obj.oTitle);
			
			var oTable = document.createElement('table');
			oTable .className='caltab1';
			var oThead = document.createElement('tHead');
			var oTr = document.createElement('tr');
			var arr = ['周日','周一','周二','周三','周四','周五','周六'];
			for(var i=0;i<7;i++){
				var oTh = document.createElement('th');
				oTh.innerHTML = arr[i];
				//if(i==0 || i==6){
				//	oTh.className = 'orange';
				//}
				oTr.appendChild(oTh);
			}
			oThead.appendChild(oTr);
			oTable.appendChild(oThead);
			
			var oTbody = document.createElement('tBody');
			for(var i=0;i<6;i++){
				var oTr = document.createElement('tr');
				for(var j=0;j<7;j++){
					var oTd = document.createElement('td');
					oTr.appendChild(oTd);
				}
				oTbody.appendChild(oTr);
			}
			oTable.appendChild(oTbody);
			obj.appendChild(oTable);
			
			
			obj.bBtn = true;
		}
		obj.oTitle.innerHTML = '<div class="l"><span><img src="'+APP_PATH+'/images/left_arrow.png"></span></div><div class="c"><span>'+year+'</span>年<span>'+month+'</span>月</div><div class="r"><span><img src="'+APP_PATH+'/images/right_arrow.png"></span></div>';
		var aTd = obj.getElementsByTagName('td');
		$(aTd).html('');
		
		if(month==1 || month==3 || month==5 || month==7 || month==8 || month==10 || month==12){
			dayNum = 31;
		}else if(month==4 || month==6 || month==9 || month==11){
			dayNum = 30;
		}else if(month==2 && isLeapYear(year)){
			dayNum = 29;
		}else{
			dayNum = 28;
		}
		oDate.setFullYear(year);
		var today = oDate.getDate();
		oDate.setMonth(month-1,1);
		
		//oDate.setDate(1);
		oOffset = oDate.getDay();
		
		switch(oOffset){
			case 0:
				for(var i=0;i<dayNum;i++){
					aTd[i].innerHTML = i+1;
				}
			break;
			case 1:
				for(var i=0;i<dayNum;i++){
					aTd[i+1].innerHTML = i+1;
				}
			break;
			case 2:
				for(var i=0;i<dayNum;i++){
					aTd[i+2].innerHTML = i+1;
				}
			break;
			case 3:
				for(var i=0;i<dayNum;i++){
					aTd[i+3].innerHTML = i+1;
				}
			break;
			case 4:
				for(var i=0;i<dayNum;i++){
					aTd[i+4].innerHTML = i+1;
				}
			break;
			case 5:
				for(var i=0;i<dayNum;i++){
					aTd[i+5].innerHTML = i+1;
				}
			break;
			case 6:
				for(var i=0;i<dayNum;i++){
					aTd[i+6].innerHTML = i+1;
				}
			break;			
		}
		if(month == tmonth) aTd[today+oOffset-1].innerHTML = '今天';
		
		$.ajax({
			url: 'index.php?m=Tour&a=getDate',
			type: 'POST',
			data: {id: id,month: month,year: year,mod: mod},
			dataType: 'json',
			success: function(result){
				if(result.status){
					//alert(aTd.length);
					objData = eval('('+result.data+')');
					
					for(var i=0;i<aTd.length;i++){
						var x = i % 7;
						if(!aTd[i].innerHTML) aTd[i].className='gray';
						if(typeof(objData[i-oOffset+1]) == "undefined"){
							//变色、不能点
							if(x == 0 || x == 6){
								aTd[i].className='orange';
							}else{
								aTd[i].className='gray';
							}
							
						}else{
							//标志，颜色，手型
							var oP = document.createElement('p');
							oP.innerHTML = objData[i-oOffset+1].price+'元';
							aTd[i].appendChild(oP);
							aTd[i].className='enable';
							$(aTd[i]).attr('data',objData[i-oOffset+1].id);
							
							var oDe=$("<div class='oDe'>");
							if(objData[i-oOffset+1].pid == 590 || objData[i-oOffset+1].pid == 591 || objData[i-oOffset+1].pid == 592 || objData[i-oOffset+1].pid == 593){
								if(objData[i-oOffset+1].child_price != 0) var oDchild=$("<div class='oDechild'>1大1小：<span>"+objData[i-oOffset+1].child_price+"</span>元</div>");
								if(objData[i-oOffset+1].price != 0) var oDeman=$("<div class='oDeman'>加1人：<span>"+objData[i-oOffset+1].price+"</span>元</div>");
							}else{
								if(objData[i-oOffset+1].child_price != 0) var oDchild=$("<div class='oDechild'>儿童价：<span>"+objData[i-oOffset+1].child_price+"</span>元</div>");
								if(objData[i-oOffset+1].price != 0) var oDeman=$("<div class='oDeman'>成人价：<span>"+objData[i-oOffset+1].price+"</span>元</div>");	
							}
							
							
							//$('.oDchild span').html(objData[i-oOffset+1].child_price);
							//$('.oDeman span').html(objData[i-oOffset+1].price);
							$(oDe).append(oDchild);
							$(oDe).append(oDeman);
							$(aTd[i]).append(oDe);
							
						}
						
						$(aTd[i]).hover(function(){
							$(this).find('.oDe').show();	
						},function(){
							$(this).find('.oDe').hide();	
						});

					}
					
					if(month == tmonth) aTd[today+oOffset-1].className="today";
					$('.Main .Mtop .MT_left .tour_cal .cover').hide();			
				}	
			}
			
			
		});
		
		
				
	};
	
	function showBtn(){
		
		var leftMonth =aNowSpan[0];
		var rightMonth =aNowSpan[3];
		var nowYear = parseInt(aNowSpan[1].innerHTML);	
		var nowMonth=parseInt(aNowSpan[2].innerHTML);
			
		//console.log(nowYear+'-'+nowMonth);
		$(leftMonth).parent().click(function(){
			if(nowMonth == 1){
				showDate(oNowTime,nowYear-1,12,true);
			}
			else{
				showDate(oNowTime,nowYear,nowMonth-1,true);
			}
			showBtn();
			//showColor(new Date().getDate());
			$('.Main .Mtop .MT_left .tour_cal .cover').show();
			
		});
		$(rightMonth).parent().click(function(){
			if(nowMonth == 12){
				showDate(oNowTime,nowYear+1,1,true);
			}
			else{
				showDate(oNowTime,nowYear,nowMonth+1,true);
			}
			showBtn();
			$('.Main .Mtop .MT_left .tour_cal .cover').show();
		});
	}
	
	function isLeapYear(year){
		if(year%4==0 && year%100!=0){
			return true;
		}
		else{
			if(year%400==0){
				return true;
			}
			else{
				return false;
			}
		}
	}
	
	//行程介绍
 	//时间线去背景
	$('.intro_area').last().addClass('no_timeline');

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
	var nowH=$('.Mbottom').offset().top;
	var sH=$(window).scrollTop();

	sH>=400 ? $('.back_top').show() : $('.back_top').hide();
	//console.log(nowH+'和'+sH);
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





