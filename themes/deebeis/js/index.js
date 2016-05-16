/*首页幻灯片*/
//设置幻灯为运行前的广告背景颜色
$(document).ready(function(){
    //页面初始化时ad的背景颜色
    //var adInitColor = $(".flexslider li[class='flex-active-slide']").attr("data-color");
    //set_ad_color(adInitColor);
    //导购条
    daogouBar();
    //鼠标滑过商标显示商标介绍
    brandsDesc();
    //楼层导购条
    navBar();   
    //延迟加载图片
    $("img.lazy").lazyload({
        placeholder:"/images/loading.gif",
        effect:"fadeIn"
    });
    
    $(".floorItem:not('.floorad')").hover(function(){
        $(this).addClass("hover");
    },
        function(){
        $(this).removeClass("hover");
        }
    );
    
});

function set_ad_color(color){
    $(".navAd").animate({
        backgroundColor:color
    },500);
}

function set_ad_img(li){
    var img = $(li).attr('data-img');
    $(li).css("background", "url("+img+") center no-repeat");
}
$(".flexslider").flexslider({
    animation: "fade",
    slideshow: true,
    directionNav:false,
    animationSpeed:500,
    before: function(slider) {
        //将背景设置成data-original以优化页面加载时间
        var liNum = slider.getTarget('next');
        var li = $(".flexslider").find("li[data-img]")[liNum]; 
        set_ad_img(li);
        //查找下一次要变换的颜色
        //var liNum = slider.getTarget("next");
        //var li = $(".flexslider").find("li[data-color]")[liNum];     
        //set_ad_color($(li).attr("data-color"));
     },
     start: function(){
         var li = $(".flexslider").find("li[data-img]")[0];
         set_ad_img(li);
     }
});

/*楼层品牌放大div*/
var brandsDesc = function(){
    $(".floor .floor_r .frwrap").append('<div class="brandDesc"><img src="/images/loading.gif" alt="品牌介绍"/></div>');
    var li = $(".brandsList li");
    li.mouseover(function(){
        var src = $(this).attr("data-brand");
        var floor = $(this).parents(".floorwrap");
        var brandDesc = floor.find(".brandDesc");
        $(this).find("img").addClass("hover");
        if(src != ""){
            $(brandDesc).css("display","block");
            $(brandDesc).find("img").attr("src","/"+src);
        }
    });
    li.mouseout(function(){
        var floor = $(this).parents(".floorwrap");
        var brandDesc = floor.find(".brandDesc");
        $(this).find("img").removeClass("hover");
        $(brandDesc).css("display","none");
    });
};

/*楼层导航栏*/

var navBar = function(){
    var bar  = $(".floorNav");
    //所有楼层的div
    var contents = $(".floorContent");
    $.each(bar, function(bk,bn){
        var barLi = $(this).find("li"); 
        //当前楼层的div
        var content =$(this).parent().find(".floorContent");
        //默认选中每个导航栏的第一个li
        if($(this).find("li[class='current']").length == 0){
            //$(this + " li").first().addClass("current");
           $(barLi).first().addClass("first current");
           $(content).first().css("display","block");
        }  
        $(barLi).bind("mouseover",function(){             
            $(barLi).removeClass("first current");
            if($(this).prevAll().length == 0){
                $(this).addClass("first");
            }
            $(this).addClass("current");
            var curNum = $(this).prevAll().length;
            var nowContent = content[curNum];
            $(content).css("display","none");
            $(nowContent).css("display","block");
            $(nowContent).find(".lazy").lazyload();
        });            
    });
};

/*导购条*/
var daogouBar = function(){
    var bar = $(".daogouwrap");
    var daogouDiv = $(".daogouDiv");
    $(daogouDiv).first().css("display","block");
    var li = $(bar).find("ul li");
    $(li).bind("mouseover",function(){
        var position = $(this).prevAll().length;
        var nowDiv = daogouDiv[position];
        $(daogouDiv).css("display","none");
        $(nowDiv).css("display","block");
        $(nowDiv).find(".lazy").lazyload();
    });
};

