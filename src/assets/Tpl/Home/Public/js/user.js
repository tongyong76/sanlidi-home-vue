// JavaScript Document
$(function(){
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
			
		}
		
			
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
	
	
	verify('#u_phone','请填写手机号码',/^(13[0-9]|15[0|3|6|7|8|9]|18[0-9]|147|17[6-8])\d{8}$/,'请填写正确的手机号码！');
	verify('#u_email','请填写邮箱号码',/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,'请填写正确的邮箱！');
	
});