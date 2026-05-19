// JavaScript Document
$(function(){
	// //结算信息
	// $('.acc_member span').html(parseInt($('#tour_num option:selected').text()));
	// $('.acc_total span').html(parseInt($('.acc_price span').html())*parseInt($('#tour_num option:selected').text()));
	// $('#tour_num').change(function(){
		// $('.acc_member span').html(parseInt($('#tour_num option:selected').text()));
		// $('.acc_total span').html(parseInt($('.acc_price span').html())*parseInt($('#tour_num option:selected').text()));	
	// });
	
	
	
	//重要提醒内容收缩
	var aBtn = $('.o_n_title a');
	$(aBtn).click(function(){
		$(this).parent().next().fadeToggle();	
	});
	
	
});