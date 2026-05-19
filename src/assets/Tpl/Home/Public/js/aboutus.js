// JavaScript Document
$(function(){
	$('#wx').hover(function(){
		$(this).find('.w_scan').show();
	},function(){
		$(this).find('.w_scan').hide();	
	});
});