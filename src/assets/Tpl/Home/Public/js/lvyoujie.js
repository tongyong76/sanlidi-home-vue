var setbackTop = function () {
    var retop = $("#menu");
    var pageW = $(window).width();
    if (pageW < 1326)
        retop.css({ "margin-left": pageW / 2 - 143 + "px" });
    else
        retop.css({ "margin-left": "520px" });
}
$(function () {
	$("#menu").hide();
    $('body,html').animate({
        scrollTop: 0
    }, 100);
    //响应效果
    $(".module_head_price").hover(function () {
        $(this).children("a").addClass("icon");
    }, function () {
        $(this).children("a").removeClass("icon");
    });
    $(".module_product_word").hover(function () {
        $(this).children("a").addClass("icon");
    }, function () {
        $(this).children("a").removeClass("icon");
    });
    $("ul.nav li").hover(function () {
        $(this).find("a").children("span").stop().animate({ left: '50px' });
        $(this).find("a").children("em").stop().animate({ left: '50px' });
        $(this).find("a").children("i").stop().animate({ opacity: '0' });
    }, function () {
        $(this).find("a").children("span").stop().animate({ left: '10px' });
        $(this).find("a").children("em").stop().animate({ left: '10px' });
        $(this).find("a").children("i").stop().animate({ opacity: '1' });
    });
    //				$(window).scroll(function(){
    //					var oNavRoute=$('.module_nav');
    //					var nowH=$('.module_1').offset().top-100;
    //					var sH=$(window).scrollTop();
    //					sH>=nowH ? $(oNavRoute).addClass('module_fixed') : $(oNavRoute).removeClass('module_fixed');
    //					});
    //右侧导航菜单
    if ((navigator.userAgent.indexOf('Chrome') >= 0)) {
        $(document.body).animate({ scrollTop: "0px" }, 500)
    }
    else {
        $(document.documentElement).animate({ scrollTop: "0px" }, 500)
    }

    var menu = $("#menu");
    //var menu = $(".list_floor");
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
		var sH=$(window).scrollTop();
		sH>=641 ? $('#menu').show() : $('#menu').hide();
        var itemId = "";
        var top = $(document).scrollTop();
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
            //if (itemId == 'floor4' || itemId == 'floor5' || itemId == 'floor6')
            //    menu.find("a.floora").addClass("current");
            //else
            menu.find("[data-href=" + itemId + "]").addClass("current");
        }
    })
    //返回顶部
    $("#back-to-top").click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 1000);
        return false;
    })
    setbackTop()
    $(window).resize(function () {
        setbackTop()
    })	//悬浮框
})
