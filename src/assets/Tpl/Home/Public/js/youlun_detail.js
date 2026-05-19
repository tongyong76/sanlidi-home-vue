// JavaScript Document
var setbackTop = function () {
    var retop = $("#retop");
    var pageW = $(window).width();
    if (pageW < 1320)
        retop.css({ "margin-left": pageW / 2 - 50 + "px" });
    else
        retop.css({ "margin-left": "610px" });
}
$(function () {

    /*价格选择的时候使用start*/
    var groupDate = $('#start_time').val();
    initAdultSelect(groupDate);
    totalMoney();
    $('.btn_yixuan').click(function (ev) { ev.stopPropagation(); if (!$(this).parent().hasClass('yixuan_show')) { $(this).parent().addClass('yixuan_show'); } else { $(this).parent().removeClass('yixuan_show'); } });

    /*价格选择的时候使用end*/

    $(".back_top").hide();//为了默认隐藏回到顶部
    setbackTop()
    $(window).resize(function () {
        setbackTop()
    })	//返回顶部布局修改
    $('input[name="tid"]').val('');
    var mod = $('input[name="mod"]').val();

    //行程列表展开收起
    $('.day_detail').each(function (i) {

        var rori = $.trim($(this).html());
        rori = rori.replace(/(<br>)+(<\/p>)$/i, '</p>');

        var rHeight = $(this).height();

        var dStr = $('.day_detail').eq(i).html();
        dStr = $.trim(dStr.replace(/<[^>].*?>/g, ''));
        dStr = strCut(dStr, 200);

        //原始数据、截断后数据
        var rDiv = "<div class='oridata'>" + rori + "</div>";
        var nDiv = "<div class='nowdata'>" + dStr + "</div>";
        $('.day_detail').eq(i).html(rDiv + nDiv);
        if (rHeight > 70) {
            $(this).attr('maxheight', rHeight);
            $(this).find('.oridata').hide();
            $(this).find('.nowdata').append($("<a class='show_all' href='javascript:void(0)'>【展开内容】</a>"));

        } else {
            //$(this).find('.oridata').append($("<a class='hidden_all' href='javascript:void(0)'>【收起内容】</a>"));
            $(this).find('.nowdata').hide();
        };


    });
    //预订须知展开收起
    $('.sh_ydxz').each(function (i) {

        var costHeight = $(this).height();

        var costori = $.trim($(this).html());
        costori = costori.replace(/(<br>)+(<\/p>)$/i, '</p>');
        var costStr = $('.sh_ydxz').eq(i).html();
        costStr = $.trim(costStr.replace(/<[^>].*?>/g, ''));
        costStr = strCut(costStr, 200);

        //原始数据、截断后数据
        var costrDiv = "<div class='oridata'>" + costori + "</div>";
        var costnDiv = "<div class='nowdata'>" + costStr + "</div>";
        $('.sh_ydxz').eq(i).html(costrDiv + costnDiv);
        if (costHeight > 70) {
            $(this).attr('maxheight', costHeight);
            $(this).find('.oridata').hide();
            $(this).find('.nowdata').append($("<a class='show_all' href='javascript:void(0)'>【展开内容】</a>"));

        } else {
            $(this).find('.nowdata').hide();
        };
    });

    $('.show_all').bind('click', function () {
        var maxheight = $(this).parent().parent().attr('maxheight');
        $(this).parent().hide();
        $(this).parent().prev().find('p:last').append($("<a class='hidden_all' href='javascript:void(0)'>【收起内容】</a>"));
        $(this).parent().prev().show();
        $(this).parent().parent().animate({ height: maxheight }, 300);


        $('.hidden_all').bind('click', function () {
            $(this).parent().parent().hide();
            $(this).parent().parent().next().show();
            $(this).parent().parent().parent().animate({ height: "70" }, 300);

            $(this).remove();

        });

    });

    //行程介绍
    //时间线去背景
    $('.intro_area').last().addClass('no_timeline');

    $('.ecode_img').hover(function () {
        $(this).find('.ecode').stop().fadeToggle(200);
    });

    //起价说明
    $('.declare').hover(function () {
        $(this).children('div').show();
    }, function () {
        $(this).children('div').hide();
    });

    //返回顶部
    $('.back_top').click(function () {
        $.scrollTo(0, 1000);
    });

    $('.info_tlist li a').click(function () {
        var _index = $(this).parent().index();
        if (_index == 0) {
            $.scrollTo($('.Mbottom').offset().top, 500);
        } else {
            $.scrollTo($('.info_wrap>div').eq(_index).offset().top + 1, 500);
        };
    });
	
	
	/**
	*预定相关
	*/
	
    //初始化预订如果是false就是没有数据 就应该是为暂无预订
    init($('.room_table tbody').children('tr').length>1);
    //舱房类型收起
    $('.room_all .close').on("click", function () {
        $(".room_table").hide();
        $('.room_all .close').css('display', 'none');
        $('.room_all .open').css('display', 'block');
    });
	
    //舱房类型展开
    $('.room_all .open').on("click", function () {
        $(".room_table").show();
        $('.room_all .open').css('display', 'none');
        $('.room_all .close').css('display', 'block');
    });
	
	//成人份数选择框点击事件
	$(".cangfangadult").change(function () {
		//生成房间的可选择范围
		var minPerson = $(this).data("minnum");//最少入住人数
		var maxPerson = $(this).data("maxnum");//最多入住人数
		var minQuantity = $(this).data("minquantity");//最少起订量
		var maxQuantity = $(this).data("maxquantity");//最大起订量
		var goodsid = $(this).data("goodsid");
		var date = $(this).data("date");
		var adult = $(this).val();
		var childClassName = "cangfangChild-" + goodsid + "-" + date;
		//得到原来选中的儿童数
		var childSelectedValue = $("." + childClassName).val();
		//重新修改一下逻辑,首先生成儿童的选择项,然后再决定房间的选择项
		var optionStr = generateChildSelect(adult, childSelectedValue, maxPerson,
				minQuantity, maxQuantity, minPerson, maxPerson);
		$("." + childClassName + " option").remove();
		$("." + childClassName).append(optionStr);
		var cangfangClassName = "cangfangHouse-" + goodsid + "-" + date;
		optionStr = generateFangjianSelect(adult, $("." + childClassName).val(), $("." + cangfangClassName).val(), minPerson, maxPerson, minQuantity, maxQuantity);
		$("." + cangfangClassName + " option").remove();
		$("." + cangfangClassName).append(optionStr);
		if ($("." + cangfangClassName).val() == '0' && adult != '0') {
			//提示对象，提示位置，left微调，top微调，提示类型error和warning。
			addTips($("." + cangfangClassName), '该舱房每间起住' + minPerson + '人,您可增加人数后选择。', 5, 5, 'warning');
		} else {
			removeTips(); //删除提示
		}
		//把人数房间数设置到仓房的div上
		setCangFangDivData(goodsid, date);
		//人数变化设置其它选项的份数
//		setVisaAndViewAndAddtion(date);
		itemTotalMoney(goodsid, date);
		totalMoney();
		generateSelectedHouse();
	});
	
	//儿童份数选择框点击事件
	$(".cangfangchild").change(function () {
		var child = $(this).val();
		var minPerson = $(this).data("minnum");//最少入住人数
		var maxPerson = $(this).data("maxnum");//最多入住人数
		var minQuantity = $(this).data("minquantity");//最少起订量
		var maxQuantity = $(this).data("maxquantity");//最大起订量
		var goodsid = $(this).data("goodsid");
		var date = $(this).data("date");
		var adultSelectClassName = "cangfangAdult-" + goodsid + "-" + date;
		var adult = $("." + adultSelectClassName).val();
		var cangfangClassName = "cangfangHouse-" + goodsid + "-" + date;
		var optionStr = generateFangjianSelect(adult, child, $("." + cangfangClassName).val(), minPerson, maxPerson, minQuantity, maxQuantity);
		$("." + cangfangClassName + " option").remove();
		$("." + cangfangClassName).append(optionStr);
		if ($("." + cangfangClassName).val() == '0' && adult != '0') {
			//提示对象，提示位置，left微调，top微调，提示类型error和warning。
			addTips($("." + cangfangClassName), '该舱房每间起住' + minPerson + '人,您可增加人数后选择。', 5, 5, 'warning');
		} else {
			removeTips(); //删除提示
		}
		//把人数房间数设置到仓房的div上
		setCangFangDivData(goodsid, date);
		//人数变化设置其它选项的份数
//		setVisaAndViewAndAddtion(date);
		itemTotalMoney(goodsid, date);
		totalMoney();
		generateSelectedHouse();
	});
	
	//房间数量改变事件
	$(".cangfanghouse").change(function () {
		var goodsid = $(this).data("goodsid");
		var date = $(this).data("date");
		//把人数房间数设置到仓房的div上
		setCangFangDivData(goodsid, date);
		//人数变化设置其它选项的份数
//		setVisaAndViewAndAddtion(date);
		itemTotalMoney(goodsid, date);
		totalMoney();
		generateSelectedHouse();
	});

});

//初始化预订如果是false就是没有数据 就应该是为暂无预订
function init(ishasData) {
    if (ishasData) {
        yuding_btn(false)
        $(".yuding-start").on("click", function (e) {
            yuding_btn(true);
            //bookOrder();
        });
    }
    else {
        $('.yuding_top_r .btn').remove();
        $('.yuding_top_r').append('<span class="btn cbtn-orange btn-big yuding-start btn_mend">已售罄</span>');
    }
}

$(window).scroll(function () {
    var oNavRoute = $('.info_ttitle');
    var oNavBox = $('.yuding_box');
    var nowH = $('.Mbottom').offset().top;
    var sH = $(window).scrollTop();

    sH >= 400 ? $('.back_top').show() : $('.back_top').hide();
    //console.log(nowH+'和'+sH);
    sH >= nowH ? $(oNavRoute).addClass('fixed_info_top') : $(oNavRoute).removeClass('fixed_info_top');
    sH >= nowH ? $(oNavBox).addClass('fixed_yuding_box') : $(oNavBox).removeClass('fixed_yuding_box');

    var dMenu = $('.info_wrap>div');
    var dMenuLen = $(dMenu).size();
    var corres = 0;
    for (var i = 0; i < dMenuLen; i++) {
        if (i == dMenuLen - 1) {
            if (sH >= $(dMenu).eq(i).offset().top) {
                corres = i;
            }
        } else {
            if (sH >= $(dMenu).eq(i).offset().top && sH < $(dMenu).eq(i + 1).offset().top) {
                corres = i;
            }
        }
        $('.info_tlist li a').removeClass();
        $('.info_tlist li a').eq(corres).addClass('nhover');
    };


});

function initAdultSelect(groupDate) {
    $(".cangfangadult").each(function () {
        var minPerson = $(this).data("minnum");//最少入住人数
        var maxPerson = $(this).data("maxnum");//最多入住人数
        var minQuantity = $(this).data("minquantity");//最少起订量
        var maxQuantity = $(this).data("maxquantity");//最大起订量
        var goodsid = $(this).data("goodsid");
        var optionStr = "";
        var adultMin = minQuantity;
        var adultMax = maxPerson * maxQuantity;
        if (adultMax > 50) {
            adultMax = 50;
        }
        optionStr = "<option value=\"0\">0</option>";
        for (i = adultMin ; i <= adultMax ; i++) {
            optionStr += "<option value=\"" + i + "\">" + i + "</option>";
        }
        var adultClassName = "cangfangAdult-" + goodsid + "-" + groupDate;
        $("." + adultClassName + " option").remove();
        $("." + adultClassName).append(optionStr);
    });
}
function totalMoney() {
    var totalMoney = 0;
    var groupDate = $('#start_time').val();
    var totalHouse = 0;
    $(".kefang-" + groupDate + "-div").each(function () {

        var useFlag = $(this).attr("data-useflag");
        if (useFlag == 'Y') {
            totalHouse += Number($(this).attr("data-quantity"));
        }
    });
    if (totalHouse == 0) {
        $(".all_price").html("<dfn>¥</dfn>" + 0);
        return;
    }
    $(".need-submit-data").each(function () {
        //是不是使用标志
        var useFlag = $(this).attr("data-useflag");
        if (useFlag == 'Y') {
            totalMoney += Number($(this).attr("data-totalmoney"));
        }
    });
    $(".all_price").html("<dfn>¥</dfn>" + totalMoney);
    $("#totalPrice").val(totalMoney);
}
//每一项仓房的总价计算
function itemTotalMoney(goodsid, date) {
    var className = "kefang-" + goodsid + "-" + date;
    var adult = $("." + className).attr("data-adultnumber");
    var child = $("." + className).attr("data-childnumber");
    var houseNum = $("." + className).attr("data-quantity");
    var maxPerson = $("." + className).attr("data-maxperson");
    var bedPrice = $("." + className).attr("data-bedprice");
    var fstPrice = $("." + className).attr("data-fstprice");
    var secPrice = $("." + className).attr("data-secprice");
    var childPrice = $("." + className).attr("data-childprice");
    //如果没有设置就让他等于第一二的成人价
    if (bedPrice == "" || (typeof (bedPrice) != "number" && typeof (bedPrice) != "string")) {
        bedPrice = fstPrice;
    }
    //如果没有设置就让他等于第一二的成人价
    if (childPrice == "" || (typeof (childPrice) != "number" && typeof (childPrice) != "string")) {
        childPrice = fstPrice;
    }
    if (secPrice == "" || (typeof (secPrice) != "number" && typeof (secPrice) != "string")) {
        secPrice = fstPrice;
    }
    var cangfangAdultClass = "cangfangAdult-" + goodsid + "-" + date;
    if (houseNum == 0) {
        $("." + cangfangAdultClass).parent("td").parent("tr").find(".room_price").html("<dfn>¥</dfn>" + 0);
        $("." + cangfangAdultClass).parent("td").parent("tr").find(".room_price_b").html("");
        $("." + className).attr("data-totalMoney", 0);
        return;
    }
    var totalMoney = 0;
    var totalPerson = Number(adult) + Number(child);
    //床位费
    var totalBedPrice = (houseNum * maxPerson - totalPerson) * bedPrice;
    //正好一个房间最多住二个人
    if (houseNum * 2 >= totalPerson) {
        //总价计算=订购人数*第1、2人成人销售价+（间数*最大入住人数-订购人数）*床位费
        totalMoney = totalPerson * fstPrice + totalBedPrice;
    } else {
        if ((totalPerson - houseNum * 2) <= child) {
            totalMoney = houseNum * 2 * fstPrice + totalBedPrice + (totalPerson - houseNum * 2) * childPrice;
        } else {
            totalMoney = houseNum * 2 * fstPrice + totalBedPrice + child * childPrice +
                    (totalPerson - houseNum * 2 - child) * secPrice;
        }
    }
    $("." + cangfangAdultClass).parent("td").parent("tr").find(".room_price").html("<dfn>¥</dfn>" + totalMoney);

    $("." + className).attr("data-totalMoney", totalMoney);
}

//设置签证观光附加的份数并计算每一项的总价
// function setVisaAndViewAndAddtion(dateParam) {
    // var kefangClassName = "kefang-" + dateParam + "-div";
    // var totalAdult = 0;
    // var totalChild = 0;
    // $("." + kefangClassName).each(function () {
        // var house_quantity = Number($(this).attr("data-quantity"));
        // if (house_quantity != 0 && house_quantity != '0') {
            // totalAdult += Number($(this).attr("data-adultnumber"));
            // totalChild += Number($(this).attr("data-childnumber"));
        // }
    // });
    // var quantity = Number(totalAdult) + Number(totalChild);
    // $("#totalPersonNumber").val(quantity);
    // $(".bx_list3").each(function () {
        // var date = $(this).data("date");
        // var goodsid = $(this).data("goodsid");
        // if (date == dateParam) {
            // $(this).text(quantity + "人");
            // var className = goodsid + "-" + date + "-div";
            // var childPrice = $("." + className).data("childprice");
            // var adultPrice = $("." + className).data("adultprice");
            // if (childPrice == "" || (typeof (childPrice) != "number" && typeof (childPrice) != "string")) {
                // childPrice = adultPrice;
                // $("." + className).attr("data-childprice", childPrice);
            // }
            // var totalMoney = totalAdult * adultPrice + totalChild * childPrice;
            // $(this).next(".bx_list4").find("span:eq(0)").text("¥" + totalMoney);
            // $("." + className).attr("data-adultnumber", totalAdult);
            // $("." + className).attr("data-childnumber", totalChild);
            // $("." + className).attr("data-quantity", quantity);
            // $("." + className).attr("data-totalMoney", totalMoney);
        // }
    // });
// }

//把人数房间数设置到仓房的div上
function setCangFangDivData(goodsid, date) {
    var adult = $(".cangfangAdult-" + goodsid + "-" + date).val();
    var child = $(".cangfangChild-" + goodsid + "-" + date).val();
    var house = $(".cangfangHouse-" + goodsid + "-" + date).val();
    var kefangClassName = "kefang-" + goodsid + "-" + date;
    $("." + kefangClassName).attr("data-adultnumber", adult);
    $("." + kefangClassName).attr("data-childnumber", child);
    $("." + kefangClassName).attr("data-quantity", house);
    $("." + kefangClassName).attr("data-useflag", "Y");
}

//生成儿童的可选择范围
function generateChildSelect(adult, childSelectedValue, maxPerson, minQuantity, maxQuantity, minPerson, maxPerson) {
    var min = minPerson * minQuantity - adult;
    var max = maxPerson * maxQuantity - adult;
    var optionStr = "";
    var maxHouse = adult;
    var childMin = 0;//规定最小的就为0
    var childMax = maxHouse * maxPerson - adult;
    if (min > childMin) {
        childMin = min;
    }
    if (childMax > max) {
        childMax = max;
    }
    if (childMin != 0) {
        optionStr = "<option value=\"0\">0</option>";
    }
    for (i = childMin; i <= childMax ; i++) {
        if (i == childSelectedValue) {
            optionStr += "<option selected=\"selected\" value=\"" + i + "\">" + i + "</option>";
        } else {
            optionStr += "<option value=\"" + i + "\">" + i + "</option>";
        }
    }
    return optionStr;
}
//生成房间的可选择范围
function generateFangjianSelect(adult, child, houseSelectedValue, minPerson, maxPerson, minQuantity, maxQuantity) {
    var maxHouse = adult;
    var minHouse = Math.floor(adult / maxPerson);
    if (minHouse < minQuantity) {
        minHouse = minQuantity;
    }
    if (maxQuantity < maxHouse) {
        maxHouse = maxQuantity;
    }
    var totalPerson = Number(adult) + Number(child);
    var optionStr = "";
    //如果总的人数少于最少的入住人数,直接就是0
    if (totalPerson < minPerson) {
        optionStr += "<option value=\"0\">0</option>";
        return optionStr;
    }
    //如果最大最小相等,总人数不能被房间整除,直接返回0
    if (minPerson == maxPerson) {
        if (totalPerson % minPerson != 0) {
            optionStr += "<option value=\"0\">0</option>";
            return optionStr;
        }
    }
    for (i = minHouse; i <= maxHouse ; i++) {
        var a = i * minPerson;
        var b = i * maxPerson;
        if (a <= totalPerson && totalPerson <= b) {
            if (i == houseSelectedValue) {
                optionStr += "<option selected=\"selected\" value=\"" + i + "\">" + i + "</option>";
            } else {
                optionStr += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }
    }
    if (optionStr == "") {
        optionStr = "<option value=\"0\">0</option>";
    }
    return optionStr;
}

//生成已选的列表
function generateSelectedHouse() {
    var groupDate = $('#start_time').val();
    var selectedHouseStr = "";
    $(".kefang-" + groupDate + "-div").each(function () {
        var useFlag = $(this).attr("data-useflag");
        var quantity = $(this).attr("data-quantity");
        if (useFlag == 'Y' && quantity != 0) {
            var adult = $(this).attr("data-adultnumber");
            var child = $(this).attr("data-childnumber");
            var totalMoney = $(this).attr("data-totalmoney");
            var branchName = $(this).attr("data-branchname");
            selectedHouseStr += "<li>";
            selectedHouseStr += "<span class=\"yixuan_price\">¥" + totalMoney + "</span>";
            selectedHouseStr += "<h4>" + branchName + "</h4>";
            selectedHouseStr += "<p><span>成人：" + adult + "人</span><span>儿童：" + child + "人</span><span>房间数：" + quantity + "间</span></p>";
            selectedHouseStr += "</li>";
        }
    });
    $(".yixuan_list").html(selectedHouseStr);
}

//为选择的仓房生成提交数据
// function createCangxingItem(index, quantity, visitTime, name, adultTotalPerson, childTotalPerson, totalPerson, itemTotalPrice, roomMaxInPerson) {
    // $("#saveOrder").append('<input type=hidden name=itemMap[' + index + '].visitTime value=' + visitTime + '>');
    // $("#saveOrder").append('<input type=hidden name=itemMap[' + index + '].personNumber value=' + totalPerson + ' >');
    // $("#saveOrder").append('<input type=hidden name=itemMap[' + index + '].goodsId value=' + index + '>');
    // $("#saveOrder").append('<input type="hidden" name="itemMap[' + index + '].name" value=' + name + '>');
    // $("#saveOrder").append('<input type=hidden name=itemMap[' + index + '].childNumber value=' + childTotalPerson + ' >');
    // $("#saveOrder").append('<input type=hidden name=itemMap[' + index + '].adultNumber value=' + adultTotalPerson + ' >');
    // $("#saveOrder").append('<input type=hidden name=itemMap[' + index + '].quantity value=' + quantity + '>');
    // $("#saveOrder").append('<input type=hidden name=itemMap[' + index + '].totalPrice value=' + itemTotalPrice + ' >');
    // $("#saveOrder").append('<input type=hidden name=itemMap[' + index + '].roomMaxInPerson value=' + roomMaxInPerson + ' >');
// }

//生成已选的列表
function generateSelectedHouse() {
    var groupDate = $('#start_time').val();
    var selectedHouseStr = "";
    $(".kefang-" + groupDate + "-div").each(function () {
        var useFlag = $(this).attr("data-useflag");
        var quantity = $(this).attr("data-quantity");
        if (useFlag == 'Y' && quantity != 0) {
            var adult = $(this).attr("data-adultnumber");
            var child = $(this).attr("data-childnumber");
            var totalMoney = $(this).attr("data-totalmoney");
            var branchName = $(this).attr("data-branchname");
            selectedHouseStr += "<li>";
            selectedHouseStr += "<span class=\"yixuan_price\">¥" + totalMoney + "</span>";
            selectedHouseStr += "<h4>" + branchName + "</h4>";
            selectedHouseStr += "<p><span>成人：" + adult + "人</span><span>儿童：" + child + "人</span><span>房间数：" + quantity + "间</span></p>";
            selectedHouseStr += "</li>";



        }
    });
    $(".yixuan_list").html(selectedHouseStr);
}

//预订按钮
function yuding_btn(data) {
    $('.yuding_top_r .btn').remove();
    if (data == true) {
        $('.yuding_top_r').append('<span class="btn cbtn-orange btn-big yuding-btn">立即预订</span>');
        $('.yuding_detail').show().siblings('.yuding_detail').hide();
        $(".room_table").show(); bookOrder();
    } else {
        $('.yuding_top_r').append('<span class="btn cbtn-orange btn-big yuding-start">开始预订</span>');
    }
}

function Number(val) {
    return parseInt(val);

}
function removeTips() {
    $('.js_nameFull').remove();
}

function bookOrder() {
    //预订提交数据
    $(".yuding-btn").on("click", function () {
        var totalHouseNum = 0;//算总的房间数
		var totalAdultNum = 0;//算总大人数
		var totalChildNum = 0;//算总儿童数
		var totalMoneyAll = 0;//算总儿童数
		var txtTitle = '';//标题
        var visitTime = $("#start_time").val();//团期
        $(".need-submit-data").each(function () {
            //是否选择
            var useFlag = $(this).attr("data-useflag");
            if (useFlag == 'Y') {
                //项目类型
                var goodsid = $(this).attr("data-goodsid");
                var quantity = $(this).attr("data-quantity");
                var name = $(this).attr("data-name");
                var adultNumber = $(this).attr("data-adultnumber");
                var childNumber = $(this).attr("data-childnumber");
                var childPrice = $(this).attr("data-childprice");
                var adultPrice = $(this).attr("data-adultprice");
                var totalMoney = $(this).attr("data-totalmoney");
                var roomMaxInPerson = $(this).attr("data-maxPerson");
				
				if(txtTitle == ''){
					txtTitle += (name + 'x' + quantity);
				}else{
					txtTitle += (' ' + name + 'x' + quantity);
				}
                totalHouseNum += Number(quantity);//算总的房间数
				totalAdultNum += Number(adultNumber);//算总大人数
				totalChildNum += Number(childNumber);//算总儿童数
				totalMoneyAll += Number(totalMoney);
                if (Number(quantity) != 0) {
                    //createCangxingItem(goodsid, quantity, visitTime, name, adultNumber, childNumber, Number(adultNumber) + Number(childNumber), totalMoney, roomMaxInPerson);
                }
            }
        })
        if (totalHouseNum == 0) {
            alert("请选择人数和房间数!");
            return;
        }
		//拼接QUERY
		gid = $('input[name="gid"]').val();
		query = base64_encode(txtTitle+'|'+totalAdultNum+'|'+totalChildNum+'|'+totalHouseNum+'|'+totalMoneyAll);
		query = query.replace(/\//ig,'*');
		query = query.replace(/\+/ig,'_');
		jumpUrl = ROOT_PATH+'/Order/confirm/type/1/gid/' +gid+'/query/' +query;
		//alert(jumpUrl);
		window.location.href = jumpUrl;
		
		
		
		/***********
		*1.房型单独拼接
		*2.试试用JSON来传递
		*3.
		*
		*/
    });

}

function base64_encode(str)
{
    var str = toUTF8(str);
    var base64EncodeChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'.split('');
    var out, i, j, len, r, l, c;
    i = j = 0;
    len = str.length;
    r = len % 3;
    len = len - r;
    l = (len / 3) << 2;
    if (r > 0) {
        l += 4;
    }
    out = new Array(l);
 
    while (i < len) {
        c = str.charCodeAt(i++) << 16 |
            str.charCodeAt(i++) << 8  |
            str.charCodeAt(i++);
        out[j++] = base64EncodeChars[c >> 18]
            + base64EncodeChars[c >> 12 & 0x3f]
            + base64EncodeChars[c >> 6  & 0x3f]
            + base64EncodeChars[c & 0x3f] ;
    }
    if (r == 1) {
        c = str.charCodeAt(i++);
        out[j++] = base64EncodeChars[c >> 2]
            + base64EncodeChars[(c & 0x03) << 4]
            + "==";
        }
    else if (r == 2) {
        c = str.charCodeAt(i++) << 8 |
            str.charCodeAt(i++);
        out[j++] = base64EncodeChars[c >> 10]
             + base64EncodeChars[c >> 4 & 0x3f]
             + base64EncodeChars[(c & 0x0f) << 2]
             + "=";
    }
    return out.join('');
}


function base64_decode(str)
{
    var base64DecodeChars = [
            -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63,
            52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1,
            -1,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,
            -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1
        ];
    var c1, c2, c3, c4;
    var i, j, len, r, l, out;
 
    len = str.length;
    if (len % 4 != 0) {
        return '';
    }
    if (/[^ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789\+\/\=]/.test(str)) {
        return '';
    }
    if (str.charAt(len - 2) == '=') {
        r = 1;
    }
    else if (str.charAt(len - 1) == '=') {
        r = 2;
    }
    else {
        r = 0;
    }
    l = len;
    if (r > 0) {
        l -= 4;
    }
    l = (l >> 2) * 3 + r;
    out = new Array(l);
 
    i = j = 0;
    while (i < len) {
        // c1
        c1 = base64DecodeChars[str.charCodeAt(i++)];
        if (c1 == -1) break;
 
        // c2
        c2 = base64DecodeChars[str.charCodeAt(i++)];
        if (c2 == -1) break;
 
        out[j++] = String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));
 
        // c3
        c3 = base64DecodeChars[str.charCodeAt(i++)];
        if (c3 == -1) break;
 
        out[j++] = String.fromCharCode(((c2 & 0x0f) << 4) | ((c3 & 0x3c) >> 2));
 
        // c4
        c4 = base64DecodeChars[str.charCodeAt(i++)];
        if (c4 == -1) break;
 
        out[j++] = String.fromCharCode(((c3 & 0x03) << 6) | c4);
    }
    return toUTF16(out.join(''));
}
 
function toUTF8(str)
{
    if (str.match(/^[\x00-\x7f]*$/) != null) {
        return str.toString();
    }
    var out, i, j, len, c, c2;
    out = [];
    len = str.length;
    for (i = 0, j = 0; i < len; i++, j++) {
        c = str.charCodeAt(i);
        if (c <= 0x7f) {
            out[j] = str.charAt(i);
        }
        else if (c <= 0x7ff) {
            out[j] = String.fromCharCode(0xc0 | (c >>> 6),
                                         0x80 | (c & 0x3f));
        }
        else if (c < 0xd800 || c > 0xdfff) {
            out[j] = String.fromCharCode(0xe0 | (c >>> 12),
                                         0x80 | ((c >>> 6) & 0x3f),
                                         0x80 | (c & 0x3f));
        }
        else {
            if (++i < len) {
                c2 = str.charCodeAt(i);
                if (c <= 0xdbff && 0xdc00 <= c2 && c2 <= 0xdfff) {
                    c = ((c & 0x03ff) << 10 | (c2 & 0x03ff)) + 0x010000;
                    if (0x010000 <= c && c <= 0x10ffff) {
                        out[j] = String.fromCharCode(0xf0 | ((c >>> 18) & 0x3f),
                                                     0x80 | ((c >>> 12) & 0x3f),
                                                     0x80 | ((c >>> 6) & 0x3f),
                                                     0x80 | (c & 0x3f));
                    }
                    else {
                       out[j] = '?';
                    }
                }
                else {
                    i--;
                    out[j] = '?';
                }
            }
            else {
                i--;
                out[j] = '?';
            }
        }
    }
    return out.join('');
}
 
function toUTF16(str)
{
    if ((str.match(/^[\x00-\x7f]*$/) != null) ||
        (str.match(/^[\x00-\xff]*$/) == null)) {
        return str.toString();
    }
    var out, i, j, len, c, c2, c3, c4, s;
 
    out = [];
    len = str.length;
    i = j = 0;
    while (i < len) {
        c = str.charCodeAt(i++);
        switch (c >> 4) {
            case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
            // 0xxx xxxx
            out[j++] = str.charAt(i - 1);
            break;
            case 12: case 13:
            // 110x xxxx   10xx xxxx
            c2 = str.charCodeAt(i++);
            out[j++] = String.fromCharCode(((c  & 0x1f) << 6) |
                                            (c2 & 0x3f));
            break;
            case 14:
            // 1110 xxxx  10xx xxxx  10xx xxxx
            c2 = str.charCodeAt(i++);
            c3 = str.charCodeAt(i++);
            out[j++] = String.fromCharCode(((c  & 0x0f) << 12) |
                                           ((c2 & 0x3f) <<  6) |
                                            (c3 & 0x3f));
            break;
            case 15:
            switch (c & 0xf) {
                case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
                // 1111 0xxx  10xx xxxx  10xx xxxx  10xx xxxx
                c2 = str.charCodeAt(i++);
                c3 = str.charCodeAt(i++);
                c4 = str.charCodeAt(i++);
                s = ((c  & 0x07) << 18) |
                    ((c2 & 0x3f) << 12) |
                    ((c3 & 0x3f) <<  6) |
                     (c4 & 0x3f) - 0x10000;
                if (0 <= s && s <= 0xfffff) {
                    out[j++] = String.fromCharCode(((s >>> 10) & 0x03ff) | 0xd800,
                                                  (s         & 0x03ff) | 0xdc00);
                }
                else {
                    out[j++] = '?';
                }
                break;
                case 8: case 9: case 10: case 11:
                // 1111 10xx  10xx xxxx  10xx xxxx  10xx xxxx  10xx xxxx
                i+=4;
                out[j++] = '?';
                break;
                case 12: case 13:
                // 1111 110x  10xx xxxx  10xx xxxx  10xx xxxx  10xx xxxx  10xx xxxx
                i+=5;
                out[j++] = '?';
                break;
            }
        }
    }
    return out.join('');
}