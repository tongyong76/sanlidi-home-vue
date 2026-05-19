// JavaScript Document
$(function () {
    //首页幻灯切换
    indexSlider();
	
	$("img.lazy").lazyload({effect: "fadeIn",threshold : 200});
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
