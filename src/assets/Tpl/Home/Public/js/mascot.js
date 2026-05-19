// JavaScript Document
$(function(){
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
});