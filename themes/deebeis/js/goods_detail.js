/* 
 商品详情页
 */
$(document).ready(function(){
    $("#tabs_label li").click(function(){
        var lis = $("#tabs_label li");
        var block = $("#com_h blockquote");
        if(typeof($(this).attr("data-position")) != "undefined"){
            var position = $(this).attr("data-position");
        } else {
            var position = $(this).prevAll().length;
        }
        $(lis).removeClass("selected");
        $(this).addClass("selected");
        $(block).hide();
        $("#com_h blockquote:eq("+position+")").show();
    });
    scroll_fixed($(".tabsWrap"),"tabs_fixed",$("#goods_area"),$(".footer"));
    var related_goods = $("#goodsInfo .textInfo .related_goods img");
    related_goods.hover(
        function(){
            related_goods.css("border","1px solid #CCC");
            $(this).css("border","1px solid red");
        },
        function(){
            related_goods.css("border","1px solid #CCC");
        }
    );
});


