// JavaScript Document
$(function () {
    //首页幻灯切换
    indexSlider();
    //换一批
    //changeGroup();
    hotScheduleEvent();
    $("img.lazy").lazyload({ effect: "fadeIn", threshold: 200 });

    company.init(); QuestionV2.init();
});

function indexSlider() {
    var sBtn = $('.mSlider_nav').find('li');
    var sliderPic = $('#sliderCon').find('li');
    var timer = null;
    var iNow = 0;

    $('.linka img').hover(function () {
        clearInterval(timer);
    }, function () {
        timer = setInterval(toRun, 5000);
    });
    $('.sliderConA img').hover(function () {
        clearInterval(timer);
    }, function () {
        timer = setInterval(toRun, 5000);
    });
    for (var i = 0; i < sBtn.length; i++) {
        sBtn[i].index = i;

        sBtn[i].onmouseover = function () {
            clearInterval(timer);
            iNow = this.index;
            for (var i = 0; i < sBtn.length; i++) {
                sBtn[i].className = '';
                sliderPic[i].className = 'none';
            }
            this.className = 'current';
            sliderPic[this.index].className = '';
        }
        sBtn[i].onmouseout = function () {
            timer = setInterval(toRun, 5000);
        }
    };

    timer = setInterval(toRun, 5000);
    function toRun() {
        if (iNow == sBtn.length - 1) {
            iNow = 0;
        } else {
            iNow++;
        }
        for (var i = 0; i < sBtn.length; i++) {
            sBtn[i].className = '';
            sliderPic[i].className = 'none'
        };
        sBtn[iNow].className = 'current';
        sliderPic[iNow].className = '';

    };

    $('.sliderConA').find('img').hover(function () {
        $(this).addClass('leftimg');
    }, function () {
        $(this).removeClass('leftimg');
    });
    //沪上特价
    var repic = $('.recom_pic');
    var aRLi = $(repic).find('li');
    var picNum = $(aRLi).size();
    var liWidth = $(aRLi).eq(0).width() + 12;
    var larrow = $('.left_arrow');
    var rarrow = $('.right_arrow');
    var iNum = 1;
    var bBtn = true;


    $(larrow).click(function () {
        if (bBtn) {
            bBtn = false;
            aRLi = $(repic).find('li');
            for (var i = 0; i < iNum; i++) {
                //console.log(picNum-i-1);
                $(repic).css('left', -liWidth * iNum);
                $(repic).css('width', liWidth * (picNum + iNum));
                var oLi = aRLi[picNum - i - 1].cloneNode(true);
                $(repic).prepend(oLi);

            };
            $(repic).animate({ left: 0 }, function () {
                for (var i = 0; i < iNum; i++) {
                    //console.log(picNum-i);
                    $(repic).find('li').last().remove();

                }
                bBtn = true;
            });

        };


    });

    $(rarrow).click(function () {
        if (bBtn) {
            bBtn = false;
            aRLi = $(repic).find('li');
            for (var i = 0; i < iNum; i++) {
                //console.log(iNum-i);
                $(repic).css('width', liWidth * (picNum + iNum));
                var oLi = aRLi[i].cloneNode(true);
                $(repic).append(oLi);
                //getWidth();	
            };

            $(repic).animate({ left: -iNum * liWidth }, function () {

                for (var i = 0; i < iNum; i++) {
                    $(repic).find('li').eq(0).remove();
                    $(repic).css('left', '0');
                }

                bBtn = true;

            });
        }


    });
};


function hotScheduleEvent() {
    var $hotSchedule = {
        $main: $(".hotSchedule .main"),
        $dt: $(".hotSchedule dt"),
        $dl: $(".hotSchedule dl"),
        $li: $(".hotSchedule .month li"),
        $move: $(".hotSchedule .move")
    };
    var Sys = {}, s, ua = navigator.userAgent.toLowerCase();
    (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] : (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] : (s = ua.match(/chrome\/([\d.]+)/)) ?
           Sys.chrome = s[1] : (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] : (s = ua.match(/version\/([\d.]+).*safari/))
           ? Sys.safari = s[1] : 0; if (Sys.ie) {
               $hotSchedule.$main[0].attachEvent('onmousewheel', mouseWheelHandler);
           }
           else {
               $hotSchedule.$main[0].addEventListener('mousewheel', mouseWheelHandler);
               $hotSchedule.$main[0].addEventListener("DOMMouseScroll", mouseWheelHandler);
           }
    function hotScheduleScroll(direction, speed) {
        var $dl = $hotSchedule.$dl;
        var currentMargin = parseInt($dl.css("margin-top").replace("px", ""));
        speed = speed ? speed : 300; if ($dl.is(":animated"))
            $dl.stop();
        if (direction == "up") {
            if (currentMargin + 100 >= -25) {
                $dl.animate({ "margin-top": "-25px" }, speed, hotScheduleLisntener);
            }
            else
                $dl.animate({ "margin-top": "+=100" }, speed, hotScheduleLisntener);
        } else {
            if (currentMargin + 25 <= -($dl.height() - $hotSchedule.$main.height())) { return; }
            $dl.animate({ "margin-top": "-=100" }, speed, hotScheduleLisntener);
        }
    }
    function mouseWheelHandler(e) {
        if (!e.preventDefault)
            e.returnValue = false; else
            e.preventDefault(); e = e || window.event;
        var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));
        switch (delta) {
            case 1:
                hotScheduleScroll("up", 200);
                break;
            case -1:
                hotScheduleScroll("down", 200);
                break;
        }
    }
    function hotScheduleLisntener() {
        var $dl = $hotSchedule.$dl;
        var $move = $hotSchedule.$move;
        var currentMargin = parseInt($dl.css("margin-top").replace("px", ""));
        if (currentMargin < -25) {
            $hotSchedule.$move.filter(".moveUp").show();
        }
        else { $hotSchedule.$move.filter(".moveUp").hide(); }
        if (currentMargin + 25 <= -($dl.height() - $hotSchedule.$main.height())) {
            $hotSchedule.$move.filter(".moveDown").hide();
        }
        else
            $hotSchedule.$move.filter(".moveDown").show();
        $hotSchedule.$dt.each(function (index, item) {
            var top = $(item).position().top;
            if (top < 80) {
                $(item).addClass("active").siblings("dt").removeClass("active");
                var month = $(this).attr("data-date");
                $hotSchedule.$li.each(function () {
                    if ($(this).attr("data-date") == month) {
                        $(this).addClass("active").siblings().removeClass("active");
                    }
                });
            }
        });
    }
    $hotSchedule.$dt.bind("click", function () {
        var $this = $(this); var $dl = $this.parent();
        if ($dl.is(":animated"))
            $dl.stop(); var month = $this.attr("data-date");
        var range = $this.position().top + 25;
        $dl.animate({
            "margin-top": "-=" + range
        },
        500, function () {
            hotScheduleLisntener();
        });
        $this.addClass("active").siblings().removeClass("active");
        $hotSchedule.$li.each(function () {
            if ($(this).attr("data-date") == month) {
                $(this).addClass("active").siblings().removeClass("active");
            }
        });
    });
    $hotSchedule.$li.bind("click", function () {
        var $this = $(this);
        $this.addClass("active").siblings().removeClass("active");
        var month = $this.attr("data-date");
        $hotSchedule.$dt.each(function () {
            if ($(this).attr("data-date") == month) {
                $(this).click();
                $this.addClass("active").siblings().removeClass("active");
            }
        });
    });
    $(".hotSchedule").hover(function () {
        var $this = $(this);
        var $dl = $hotSchedule.$dl;
        var currentMargin = parseInt($dl.css("margin-top").replace("px", ""));
        if (parseInt($dl.css("margin-top").replace("px", "")) < -25) {
            $this.find(".moveUp").show();
        }
        if ($dl.height() > $this.find(".main").height() && currentMargin + 25 > -($dl.height() - $hotSchedule.$main.height()))
            $this.find(".moveDown").show();
    }, function () {
        $(this).find(".move").hide();
    });
    $hotSchedule.$move.bind("click", function () {
        var $this = $(this);
        if ($this.hasClass("moveUp"))
            hotScheduleScroll("up");
        else
            hotScheduleScroll("down");
    });
}




var company = {
    init: function () {
        $target = $("#Company"),
        $target.length && (
        $tab = $target.find(".cru_tabs04"),
         $tabs = $tab.children("a"),
          $container = $target.find(".ani_wrap_in"),
           $items = $target.find(".ani_item"),
         $target.find(".cru_tabs04").on("click", "a",
        function () {
            var _index = $tab.find("a.current").index();
            company.show($(this).index(), _index)
        }),
         $items.find(".cruise_comp_list").find("li").hover(function () {
             $(this).find("span").slideDown(400)
         },
        function () {
            $(this).find("span").slideUp(400)
        }), $container.find(".cru_qa_num").on("click", "a",
        function () {
            var e = t(this),
            i = index(),
            n = parents(".cruise_comp").eq(0);
            parent().children().removeClass("current").eq(i).addClass("current"),
            n.children(".cruise_comp_list2").hide().eq(i).show()
        }))
    },
    show: function (i, n) {
        if (i !== n) {
            $target = $("#Company"),

            $target.length && (
                $tab = $target.find(".cru_tabs04"),
                $tabs = $tab.children("a"),
                $container = $target.find(".ani_wrap_in"),
                $items = $target.find(".ani_item"),
                $tabs.eq(n).removeClass("current"),
                $tabs.eq(i).addClass("current")
            );
            var a = i - n > 0,
            r = $items.eq(n).width();
            $items.eq(i).show(),
            $items.eq(a ? n : i).css("margin-left", a ? 0 : -r).stop().animate({
                "margin-left": a ? -r : 0
            },
            500,
            function () {
                $items.eq(n).hide().css("margin-left", 0)
            })
        }
    }
};




var QuestionV2 = {
    init: function () {
        $target = $("#QuestionV2"),
        $target.length && ($target.find(".cru_class_tab").on("click", "a[data-index]",
        function () {
            var i = $(this),
            n = $(this).index();
            i.parent().children().removeClass("current").eq(n).addClass("current"),
            $target.find(".cru_class").hide().eq(n).show()
        }), $target.find(".cru_qa_num").on("click", "a",
        function () {
            var e = $(this),
            i = e.index(),
            n = e.parents(".cru_class").eq(0);
            n.children(".cru_qa_list").hide().eq(i).show(),
            e.parent().children().removeClass("current").eq(i).addClass("current")
        }), "ontouchend" in document ? $target.find(".expand_all").click(function () {
            var e = $(this),
            n = "收起" == $(this).text() ? !1 : !0;
            if (QuestionV2.expandToggle($(this), n), n) {
                var a = function () {
                    $(document).off("mousedown", a),
                    QuestionV2.expandToggle(e, !1)
                };
                $(document).on("mousedown", a)
            }
        }) : ($target.find("dl").mouseleave(function () {
            var e = $(this).find(".expand_all");
            e.length && QuestionV2.expandToggle(e, !1)
        }), $target.find(".expand_all").mouseenter(function () {
            QuestionV2.expandToggle($(this), !0)
        }).click(function () {
            QuestionV2.expandToggle($(this), "收起" == $(this).text() ? !1 : !0)
        })))
    },
    expandToggle: function (t, e) {
        var i = t.parents(".cru_qa");
        e ? (i.find("span[data-type='label']").hide(), i.find("span[data-type='content']").show(), i.addClass("cru_qa_hover"), t.text("收起")) : (i.find("span[data-type='label']").show(), i.find("span[data-type='content']").hide(), i.removeClass("cru_qa_hover"), t.text("展开全部"))
    }
};