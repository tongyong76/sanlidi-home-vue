
function showDialog(obj) {
    var width = 600;
    var height = 359;
    var canclose = true;
    var url = $(obj).children("img").attr("data-src");
    var backdiv = document.createElement("div");
    backdiv.innerHTML = ("<div id='backdiv' style='background-image:url(/App/Tpl/Home/Public/theme/poshuijie/opacity_layer.png);background-repeat:repeat;z-index:9999;position:absolute;left:0px;top:0px;width:100%;height:" + document.body.scrollHeight + "px;'></div>");
    var centerdiv = document.createElement("div");
    var marl = (document.body.clientWidth - width) / 2;
    var mart = (window.screen.height - height) / 2 - 80;
    var closestr = "<img onclick='closedialog()' width='20' height='20' style='cursor:pointer;' title='关闭' src='/App/Tpl/Home/Public/theme/poshuijie/close-qr-code.png'></div>";
    if (canclose == false) {
        closestr = "";
    }
    centerdiv.innerHTML = "<div id='centerdiv' style='-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius:5px; padding:5px;width:" + width + "px;height:" + (height + 40) + "px;margin-left:" + marl + "px;margin-top:" + mart + "px;position:fixed;'><div  style='top: 20px; right: 0px; position: absolute;'>"
        + closestr
          + "<img style='border:10px solid #fff;' src=" + url + " /></div>";

    document.body.appendChild(backdiv);

    document.getElementById("backdiv").appendChild(centerdiv);


}
function closedialog(closeType) {
    if (window.top.dialogClose1) {
        window.top.dialogClose1(closeType);
    }
    window.top.document.getElementById("backdiv").parentNode.removeChild(window.top.document.getElementById("backdiv"));
}// 随机生成制定长度字符串
function randomChar(l) {
    var x = "0123456789qwertyuioplkjhgfdsazxcvbnm";
    var tmp = "";

    for (var i = 0; i < l; i++) {
        tmp += x.charAt(Math.ceil(Math.random() * 100000000) % x.length);
    }
    return tmp;
}