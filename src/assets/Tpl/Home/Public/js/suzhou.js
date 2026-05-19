//右侧导航菜单
$(function () {
	if ((navigator.userAgent.indexOf('Chrome') >= 0)) {
		$(document.body).animate({ scrollTop: "0px" }, 500)
	}
	else {
		$(document.documentElement).animate({ scrollTop: "0px" }, 500)
	}

	//var menu = $("#menu");
	var menu = $(".list_floor");
	var items = $("#content").find("div.floor");
	menu.find("a").each(function () {
		var m = $(this);
		m.click(function () {
			var itemid = m.attr("data-href");
			var _itemtop = $("#" + itemid).offset().top;
			if ((navigator.userAgent.indexOf('Chrome') >= 0)) {

				$(document.body).animate({ scrollTop: _itemtop + "px" }, 500)
			}
			else {

				$(document.documentElement).animate({ scrollTop: _itemtop + "px" }, 500)
			}
		})

	})

	$(window).scroll(function () {
		var top = $(document).scrollTop();
		var itemId = "";
		items.each(function () {
			var m = $(this);
			var _top = m.offset().top;

			if (top > _top - 400) {
				itemId = m.attr("id");
			}
			else {
				return false;
			}

		})


		var currentLink = menu.find("a.current");

		if (itemId && currentLink.attr("data-href") != itemId) {
			currentLink.removeClass("current");
			if (itemId == 'floor4' || itemId == 'floor5' || itemId == 'floor6')
				menu.find("a.floora").addClass("current");
			else
				menu.find("[data-href=" + itemId + "]").addClass("current");
		}
	})
	
	//返回顶部
	$("#back-to-top").click(function(){
		  $('body,html').animate({
			  scrollTop: 0
		  },1000);
		  return false;
	})
})