// JavaScript Document
$(function () {
    $('.s_list_bottom dl').hover(function () {
        $(this).addClass('hoverdl');
        $(this).children('dt').css('color', '#2a98da');

        $('.s_list_bottom dl dd').hide();
        $(this).children('dd').show();
    }, function () {
        $(this).removeClass('hoverdl');
        $(this).children('dt').css('color', '#252525');

        $('.s_list_bottom dl dd').hide();
    });
    $('.ticket a').hover(function () {
        $(this).toggleClass('ticket_move');
    });

    $('.s_r_list li').removeClass('lihover');
    $('.s_r_list li').hover(function () {
        $(this).addClass('lihover');
    }, function () {
        $('.s_r_list li').removeClass('lihover');
    });
    n.init();

    $(".smallAccordion").children("ul").children("li").hover(function (e) {
        alert
        var hoverLi = $(".smallAccordion").children("ul").children(".current");
        var thisLi = $(this);
        if (thisLi[0] != hoverLi[0]) {
            hoverLi.removeClass("current");
            $(".title", hoverLi)[0].className = "title";
            thisLi.addClass("current");
            $(".title", thisLi)[0].className = "title title_h";
        }
    }, function () {

    });
});

$(window).scroll(function () {
    if ($(window).scrollTop() >= 735) {
        $('.shop_list').css({ position: 'fixed', top: '0', right: ($(window).width() - 1200) / 2 });
    } else {
        $('.shop_list').css('position', 'static');
    };
});



var n = {
    init: function () {
        $btn = $(".poi_cruise_togglebtn"),
        $btnSpan = $btn.children("span"),
        $info = $(".poi_cruise_infos"),
        $numbers = $(".ship_number"),
        $description = $(".poi_cruise_infos_bd");
        var n = -180,
        a = {
            "margin-top": "20px",
            padding: "40px 60px"
        },
        s = {
            "margin-top": "0",
            padding: "0",
            height: "0"
        },
        r = !0;
        $info.show().css({
            overflow: "hidden",
            height: "0"
        }).css(s),
        $btn.on("click",
        function () {
            if (r) {
                var t = $description.length ? $description.height() : 0;
                t += 60,
                a.height = t
            }

            $btnSpan.css({
                "transform": "rotate(" + n + "deg)",
                "-moz-transform": "rotate(" + n + "deg)",
                "-ms-transform": "rotate(" + n + "deg)",
                "-webkit-transform": "rotate(" + n + "deg)",
                "-o-transform": "rotate(" + n + "deg)"
            }),
            $info.animate(r ? a : s,
            function () {
                n = -180 === n ? 0 : -180
            }),
            r = !r
        }),
        $numbers.each(function () {
            var i = $(this),
            e = parseInt($(this).attr("data-value")),
            n = 0;
			if(e){
				setTimeout(function () {
					var t = setInterval(function () {
						i.html(++n),
						n === e && clearInterval(t)
					},
					Math.min(30, 600 /e))
				},
				200)
			}else{
				i.html(0);
			}
        }),
        $picTabs = $(".poi_banner_point_items").children("a"),
        $pics = $(".poi_banner_pic").find("img")
        //i.carouselPic($picTabs, $pics, "current", 4e3)
    }
};