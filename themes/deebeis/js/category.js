$(document).ready(function(){    
    $("#age li").click(function(){
        var num = $(this).prevAll().length;
        set_daogouBig(num);
        set_daogou(num);
        getData();
    });   
});

 var $_GET = (function(){
     var url = window.document.location.href.toString();
     var u = url.split("?");
     if(typeof(u[1]) == "string"){
         u = u[1].split("&");
         var get = {};
         for(var i in u){
             var j = u[i].split("=");
             get[j[0]] = j[1];
         }
         return get;
     } else {
         return {};
     }
 })();
 
//分类导航条
jQuery.extend({
    categoryNav:function (options) {
        var defaults = {
            //导航ul
            className:".cate_nav_item",
            //更多按钮
            moreItem:".more",
            arrow:".icon-arrow",
            arrowDown:".icon-arrow-down",
            arrowUp:".icon-arrow-up",
            showNum:14,
            jump:true
	};
        var initItem = function(obj){
            $(obj).find("li").removeClass("selected");
            $(obj).find(".nav_all").addClass("selected");
        };
        
        var config = $.extend({}, defaults, options);
        $.each($(config.className),function(i,n){
            var parent = $(this);
            var data_name = $(this).attr("data-name");
            var hideli = $(this).find("li:gt("+(config.showNum-1)+")");
            hideli.hide();
            //更多按钮
            var more = $(this).find(config.moreItem);
            $(more).bind("click",function(){
                var icon = $(this).find(config.arrow);
                var downClass = config.arrowDown.replace('.','');
                var upClass = config.arrowUp.replace('.','');
                if(icon.hasClass(downClass)){
                    $(icon).removeClass(downClass);
                    $(icon).addClass(upClass);
                    hideli.show();
                 // $(this).css("color","#FFFFFF");
                }else {
                    $(icon).removeClass(upClass);
                    $(icon).addClass(downClass);                   
                    hideli.hide();
                 // $(this).css("color","#000000");
                }
            });
            var li = $(this).find("li");
            var all = $(parent).find(".nav_all");
            $(li).not(config.moreItem).bind("click",function(){
                $(all).removeClass('nav_all_selected');
                if(!$(this).hasClass("disabled")){
                    if(data_name != 'brand_id'){
                        initItem($(config.className+"[data-name='brand_id']"));
                    }
                    var selected = false;
                    if($(this).hasClass("selected")){
                        selected = true;
                    }
                    //在没选中的情况下才重新获取数据并更新页面
                    if(!selected){
                        $(li).removeClass("selected");
                        $(this).addClass("selected");
                        getData();
                        if(config.jump){
                            jump_position('filter_area',0);
                        }
                    }
                }
            });
            $(all).bind('click',function(){
                $(li).removeClass("selected");
                $(this).addClass('nav_all_selected');
                getData();
                if(config.jump){
                    jump_position('filter_area',0);
                }
            });
        });      
    }
});

//左侧分类导航
jQuery.extend({
    leftCatNav:function(options){
        var defaults = {
            parent:"#catlist_menu",
            //滚动内容容器
            contant:"#c_catlist_scroll",
            //滚动内容
            item:"#c_catlist_ul",
            //向上滚动按钮
            up:"#c_catlist_up",
            //还能向上滚动按钮的样式
            up_hover:"catlist_up_hover",
            //向下滚动按钮
            down:"#c_catlist_down",
            //还能向下滚动按钮的样式
            down_hover:"catlist_down_hover",
            time:500
	};
        var initItem = function(obj){
            $(obj).find("li").removeClass("selected");
            $(obj).find(".nav_all").addClass("nav_all_selected");
        };
        var config = $.extend({}, defaults, options);
        var contant = $(config.contant);
        var ul = $(config.item);
        var max_down = 0 - $(ul).height();
        var scroll_height = $(config.contant).height();
        var scroll = function(slide){                    
            if(!$(ul).is(":animated")){
                var top = parseInt($(ul).css("marginTop").replace("px",''));
                //向下滚动
                if(top - scroll_height > max_down && slide=='down'){
                    $(ul).animate({marginTop:top-scroll_height+'px'}, config.time);
                    $(config.up).addClass(config.up_hover);
                    //当下次滚动高度不够时就不能继续滚动
                    if(top - 2*scroll_height > max_down){
                        $(config.down).addClass(config.down_hover);
                    }else{                   
                        $(config.down).removeClass(config.down_hover);
                    }
                //向上滚动
                }else if(top < 0 && slide=='up'){
                    $(ul).animate({marginTop:top+scroll_height+'px'}, config.time);
                    $(config.down).addClass(config.down_hover);
                    if(top + scroll_height < 0){
                        $(config.up).addClass(config.up_hover);
                    }else{                  
                        $(config.up).removeClass(config.up_hover);
                    }
                }
            }
        };
        
        $(config.up).click(function(){
            scroll("up");
        });
        $(config.down).click(function(){
            scroll("down");
        });
        $(config.parent).find("li").click(function(){
            var all_li = $(config.parent+" li");
            var cat_id = $(this).attr("cat-id");
            var value = $(this).attr("data-value");
            var cat_item = $(".cate_nav_item[cat-id='"+cat_id+"']");
            var li = $(cat_item).find("li");
            //去掉'全部'的选中效果
            $(cat_item).find('.nav_all').removeClass('nav_all_selected');
            var selected = $(cat_item).find("li[data-value='"+value+"']");
            $(li).removeClass("selected");
            $(selected).addClass("selected");
            //初始化品牌选项
            var brand = $(".cate_nav_item[data-name='brand_id']");
            initItem(brand);
            getData();
            $(all_li).removeClass("selected");
            $(this).addClass("selected");
            //jump_position('content_area',0);     
        });
    }
});

// 创建一个闭包    
(function($) {      
    var filter = new Object(); 
    //html元素
    var _obj = {};
    //插件设置
    var config = {};
    // 插件的定义     
    $.fn.GoodsFilterBar = function(options){
        var obj = $(this);
        _obj = obj;       
        config = $.extend({}, $.fn.GoodsFilterBar.defaults, options);
        var orders = $(obj).find(config.orderClass);
        var labels = $(obj).find(config.labelClass);
        $(orders).live("click",function(){
            //这个排序是否被选中，默认false
            var selected = false;
            if($(this).hasClass(config.orderSelectedClass.replace(".",""))){
                selected = true;
            }
            if(!selected){
                $(orders).removeClass("selected-order");
                $(this).addClass("selected-order");
            } else {
                var sort_order = $(this).attr("order").toLowerCase();
                var arrow = $(this).find("span");
                var down_arrow = config.arrowDown.replace(".","");
                var up_arrow = config.arrowUp.replace(".","");
                if(sort_order == "desc"){
                   $(arrow).removeClass(down_arrow);
                   $(arrow).addClass(up_arrow);
                   $(this).attr("order","asc");
                } else {
                    $(arrow).removeClass(up_arrow);
                   $(arrow).addClass(down_arrow);
                   $(this).attr("order","desc");
                }
            }
            getData();
        });        
    };
       
    $.fn.GoodsFilterBar.defaults = {
        //排序class
        orderClass:".sort-order",
        //选中的排序样式
        orderSelectedClass:".selected-order",
        //标签class
        labelClass:".filter-label",
        //箭头向上样式
        arrowUp:".arrow-up",
        //箭头向下样式
        arrowDown:".arrow-down"
    };
    
    $.fn.GoodsFilterBar.getvalues = function(){     
        //选中的排序
        var selected = $(_obj).find(config.orderSelectedClass);
        filter.sort_by = $(selected).attr("data-value");
        filter.sort_order = $(selected).attr("order");
        filter.price_min = $("#price_min").val();
        filter.price_max = $("#price_max").val();
        return filter;
    };
// 闭包结束    
})(jQuery);  

//ajax
function getData(page){  
    if(typeof(page) == 'undefined'){
        page = 1;
    }
    var category = $("#category").val();
    var selected = $(".cate_nav_item:not([data-name='brand_id'])").find("li[class='selected']");
    var filt = new Array();
    $.each(selected,function(i,j){
        var value = $(this).attr("data-value");      
        filt.push(value);
    });
    var brand_str = 'brand_id=';
    var brand = $(".cate_nav_item[data-name='brand_id']");
    var brand_li = $(brand).find("li");   
    if($(brand).length > 0){
        $.each(brand_li,function(){
            if($(this).hasClass("selected")){
                brand_str +=$(this).attr("data-value");
            }
        });
    } else {
        brand_str += 0;
    }
    //获取filter-bar的元素属性
    var other_filter = $(".filter-bar").GoodsFilterBar.getvalues();
    $.ajax({
        type: "POST",
        url: "category.php?act=ajax&"+brand_str+"&page="+page,
        data: {category:category,filt:filt,other_filter:other_filter},
        dataType:"json",
        async:false,
        success: function(data){
            $("#content_area").empty();
            $("#content_area").append(data.content);   
            $("#filter_bar_pager").empty();
            $("#filter_bar_pager").append(data.top_page);
            $(brand_li).not(".more").addClass("disabled");
            $.each(data.able_brand,function(i,j){
                $(brand).find("li[data-value="+j+"]").removeClass("disabled");
            });
            
        }
     });
}

//用户点击领取红包
function get_bonus(obj){
    var href= $(obj).attr("data-href");
    $.post(href,function(data){
        if(data.error == ''){
            alert('领取红包成功！');
        } else if(data == '-1'){
            alert('登录之后才能领取红包');
        }       
        else {
            alert(data.message);
        }
    },'json');
}
