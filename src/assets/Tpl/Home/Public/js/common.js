// JavaScript Document
$(function(){
	showNav();
	
	$('#wx').hover(function(){
		$(this).find('.w_scan').show();
	},function(){
		$(this).find('.w_scan').hide();	
	});
	
	// $('.in_t_r ul li').hover(function(){
	// 	$(this).children('.slo_img').stop().animate({left:'-63px',top:'-2px'},100).end().children('.slo_des').show(400);	
	// 	$('.slo_des').eq(0).css('right','-60px');
	// },function(){
	// 	$(this).children('.slo_des').hide().end().children('.slo_img').stop().animate({left:'0px',top:'0px'},100);	
	// });
	$(".sidenav li").hover(function() {
		$(this).find("div").stop().animate({left: "114", opacity:1}, "800").css("display","block");
		$(this).animate({left: "-84"}, "800");
	},function(){
		$(this).find("div").stop().animate({left: "0", opacity: 0}, "800");
		$(this).animate({left: "0"}, "800");
	});
	$('.md_list ul li:odd').css('background-color','#f1f1f1');
	$('.md_list ul li').hover(function(){
		$(this).css('font-weight','bold');	
	},function(){
		$(this).css('font-weight','normal');	
	});
	$('.mendian').hover(function(){
		$(this).find('.md_list').show().animate({"margin-left":"0px"});	
	},function(){
		$(this).find('.md_list').hide().animate({"margin-left":"8px"});	
	});
	
	$('.friendLink').hover(function(){
		$(this).find('.flink_list').show();	
	},function(){
		$(this).find('.flink_list').hide();	
	});
	
	$(".hint").each(function(){
		$(this).val($(this).attr('defaultValue'));	
	});
	$(".hint").focus(function(){
		if($(this).val() == $(this).attr('defaultValue')){
			$(this).val('').removeClass('hint');				
		};
	});
	$(".hint").blur(function(){
		if($(this).val() == ''){
			$(this).addClass('hint').val($(this).attr('defaultValue'));			
		};
	});
	/*$.getScript("http://float2006.tq.cn/floatcard?adminid=8128341&sort=1");*/
	
});	

function showNav(){
	var navBtn = $('#all_p');
	var navItem = $('.n_item_list').find('.leftItem');
	var navItem2 = $('.n_item_list2').find('.leftItem');
	var navItemMenu = $('.list_submenu');
	var timer2=null;
	
	$('#n_item').hover(function(){
		timer2=setTimeout(function(){
			$('.n_item_list2').addClass('n_item_list2_on');	
		},500);
		
	},function(){
		clearTimeout(timer2);
		$('.n_item_list2').removeClass('n_item_list2_on');
	});
	
	
	$(navItem).mouseenter(function(){
		//console.log('移入');
		$(this).find('.l_m_name').addClass('l_m_noopa');
		$(this).find('.list_submenu').addClass('list_submenu_on');	
	});
	$(navItem).mouseleave(function(){
		//console.log('移出');
		$(this).find('.l_m_name').removeClass('l_m_noopa');
		$(this).find('.list_submenu').removeClass('list_submenu_on');	
	});
	
	//内页导航下拉框
	$(navItem2).mouseenter(function(){
		$(this).find('.l_m_name').addClass('l_m_noopa');
		$(this).find('.list_submenu').addClass('list_submenu_on');
		$('.n_item_list2').css('border-right-color','#2a98da');	
	});
	$(navItem2).mouseleave(function(){
		$(this).find('.l_m_name').removeClass('l_m_noopa');
		$(this).find('.list_submenu').removeClass('list_submenu_on');
		$('.n_item_list2').css('border-right-color','#cccccc');	
	});
	
	
};

function verify(id,prompting,uanmebool,rightnumber){
	if(arguments.length==2){
		$(id).blur(function(){
			if($.trim($(this).val())==""){
				$(this).parent().next().addClass("error").removeClass("correct").html(prompting);
			}else{
				$(this).parent().next().addClass("correct").removeClass("error").html("&nbsp;");	
			};
		});	
	}else if(arguments.length==4){
		$(id).blur(function(){			
			if($.trim($(this).val())==""){
				$(this).parent().next().addClass("error").removeClass("correct").html(prompting);
			}else{					
				if(!uanmebool.test($.trim($(this).val()))){
					$(this).parent().next().addClass("error").removeClass("correct").html(rightnumber);
				}else{
					$(this).parent().next().addClass("correct").removeClass("error").html("&nbsp;");	
				};
			};				
		});	
	};
};

function headlogin(){
	var requesturl = encodeURIComponent(window.location.href);
	window.location.href = ROOT_PATH+"/login.html?requesturl="+requesturl;
	
};

//支付宝付款
function payByAlipay(id){
	window.location.href = ROOT_PATH+"/doalipay.html?id="+id;
}

function cancelOrder(id){
	var bHeight = $('body').height();
	var bWidth = $('body').width();
	$("body").append("<div class='layerCover'></div>");
	var lHeight = $('.layerCover').height();
	if (bHeight > lHeight){
		$('.layerCover').css('height',bHeight)
	}
	var mHeight = $('.messageBox').height();
	var mWidth = $('.messageBox').width();
	$('.messageBox').css('left',bWidth/2-mWidth/2+'px');
	$('.messageBox').css('top',lHeight/2-mHeight/2+'px');
	$('.messageBox textarea').val('');
	$('.messageBox').show();
	$('.messageBox input[name="order_id"]').val(id);
	$('#order_'+id).addClass('delThis');
}

function cancelco(){
	$('.messageBox').hide();
	$('.layerCover').remove();
}


function fancy(){
	var fMask=$('<div class="fMask">');
	$(fMask).css('height',$('body').height());
	$('body').append(fMask); 
};

function fancybox(message,url){
	fancy();
	var fancybox=$("<div class='fancybox'>");
	var fTitle=$("<div class='fTitle'>");
	var fClose=$("<span class='fClose'>×</span>");
	var fBtn=$("<div class='fBtn'>");

	//$('body').css('overflow-y','hidden');
	$(fTitle).html(message);
	$(fBtn).html('确定');
	
	$(fTitle).append(fClose);
	$(fancybox).append(fTitle);
	$(fancybox).append(fBtn);
	$("body").append(fancybox);
	
	var fLeft=($(window).width()-$(fancybox).width())/2;
	var fTop=($(window).height()-$(fancybox).height())/2-100;
	$(fancybox).css({top:fTop,left:fLeft});
	
	$(fBtn).hover(function(){
		$(this).css('background','#40a1db');	
	},function(){
		$(this).css('background','#2a98da');	
	});
	$(fBtn).click(function(){
		$(this).parent().remove();	
		$('.fMask').remove();
		if(url) window.location.href = ROOT_PATH;
	});
	$(fClose).click(function(){
		$(this).parent().parent().remove();
		$('.fMask').remove();	
	});
};

function strCut(str,len){
	if(str.length>len){
		str=str.substring(0,len)+'...';	
	};
	return str;	
};




