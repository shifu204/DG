function snshareScroll(btn,showId){
    var btn = $(btn);
	var move = $(showId).find(".shareAreaList");
	var w , a;
	if(screen_width>=1280){
		w = 550 ;
	}else{
		w = 350 ;
	};
	btn.find("li").mouseover(function(){
		a = $(this).text()-1;
		$(this).addClass("hover").siblings().removeClass("hover");
		//move.stop().animate({marginLeft:[-a*w,'easeOutCubic']},300);
		move.stop().animate({marginLeft:-a*w},300);
		for(var i=0;i<2;i++){
			var v = $(move).find("dl").eq(2*a+i).find("img[src3]");
			v.each(function() {
				$(this).attr("src", $(this).attr("src3")).removeAttr("src3")
			});
		};	
	}); 	  
}
function snfloorScroll(box,showId,w){
    var box = $(box);
    var btnl = box.find(".btnL")
    var btnr = box.find(".btnR")
    var move = box.find(".proItemScroll");
    var oli = move.find(".proItemList");
    var len = box.find(".proItemList").length ;
    var i = 0;
	if(screen_width>=1280){
		w = 560 ;
	}else{
		w = 384 ;
	};
    btnl.click(function(){
        if(len == 1 || move.is(":animated"))return false;
        if(i==0){
		    oli.eq(len-1).css({"position":"relative","left":-len*w + "px"})
	    }
        i--;
        move.stop().animate({marginLeft:-i*w},300,function(){
            if(i==-1){
				i = len-1;
				oli.eq(len-1).removeAttr("style");
				move.css("marginLeft",-i*w);
			};
			showId.find("span").html(i+1);
			var v = $(oli).eq(i).find("img[src3]");
			v.each(function() {
				$(this).attr("src", $(this).attr("src3").replace('~',sn.cityId)).removeAttr("src3")
			});
        })
    });
    btnr.click(function(){
        if(len == 1 || move.is(":animated"))return false;
        if(i == len-1){
			oli.eq(0).css({"position":"relative","left":len*w + "px"});
		};		
        i++;
		move.stop().animate({marginLeft:-i*w},300,function(){
            if(i==len){
				i = 0;
				oli.eq(0).removeAttr("style");
				move.css("marginLeft",i*w);
			};
			showId.find("span").html(i+1);
			var v = $(oli).eq(i).find("img[src3]");
			v.each(function() {
				$(this).attr("src", $(this).attr("src3").replace('~',sn.cityId)).removeAttr("src3")
			});
        });		
    });
}
snScrollClick = function(){
	$(".indexBtnscroll").toggle(function(){
		$("#floorWarp").hide();
		$(".indexBtnscroll").find("img").eq(0).removeClass("hide");
		$(".indexBtnscroll").find("img").eq(1).addClass("hide");
		window.scrollTo(0,$(this).offset().top-430);
	},function(){		
		$(".indexBtnscroll").find("img").eq(0).addClass("hide");
		$(".indexBtnscroll").find("img").eq(1).removeClass("hide");
		$("#floorWarp").show();
	});
};
snshareImg = function(btn){
	$(btn).mouseover(function(){
		$(this).find("span").stop().animate({bottom:0},150);	
	}).mouseout(function(){
		$(this).find("span").stop().animate({bottom:"-24px"},150);	
	});
};
snTabBoxCout = function(btn,showId,cout){
	var time;
	$(btn).mouseover(function(){		
		index = $(btn).index(this);
		var ref = $(this);	
		time = setTimeout(function(){					
			$(showId).css("display","none").eq(index).css("display","block");
			var box=$(showId).eq(index);
			var textarea=$('textarea.J_lazyRender',box);
			if(textarea.length){
				var f1=textarea.hasClass('J_lazyEvBind')?1:0;
				var html=textarea[0].value;
				box.html(html.replace(/src(\s*)=(\s*)".+"/g,function(w){
					return w.replace('~',sn.cityId)
				}));
				if(f1){
					setTimeout(function(){
						//priceWavy(sn.cityId,box);
						snfloorScroll($("#snProlist .proArae").eq(index),$("#snProlist ol li").eq(index));
					},0);
				}
			}
			ref.addClass("hover").siblings().removeClass("hover");	
			$(cout).css("display","none").eq(index).css("display","block");	
		},200);	
		var v = $(showId).eq(index).find("img[src3]");
		v.each(function() {
			$(this).attr("src", $(this).attr("src3")).removeAttr("src3")
		});	
	}).mouseout(function(){
		clearTimeout(time);
	});
};

snTabBox = function(btn,showId){
	var time;
	$(btn).mouseover(function(){		
		index = $(btn).index(this);
		if(!$(this).html())return;
		var ref = $(this);	
		time = setTimeout(function(){					
			$(showId).css("display","none").eq(index).css("display","block");
            // 激活延迟加载
            $("img.lazy").lazyload();
			var box=$(showId).eq(index);
			var textarea=$('textarea.J_lazyRender',box);
			if(textarea.length){
				var html=textarea[0].value;
				box.html(html.replace(/src(\s*)=(\s*)".+"/g,function(w){
					return w.replace('~',sn.cityId)
				}));
				/*setTimeout(function(){
					priceWavy(sn.cityId,box);
				},0)*/
			}
			ref.addClass("hover").siblings().removeClass("hover");
		},200);	
		var v = $(showId).eq(index).find("img[src3]");
		var f = $(showId).eq(index).find("iframe[src2]");
		v.each(function() {
			$(this).attr("src", $(this).attr("src3")).removeAttr("src3")
		});	
		f.each(function() {
			$(this).attr("src", $(this).attr('src2')).removeAttr("src2");
		});
	}).mouseout(function(){
		if(time)clearTimeout(time);
	});
};
snbrandTab = function(btn,showId){
	$(btn).mouseover(function(){
		var index = $(btn).index(this);
		$(this).addClass("on").siblings().removeClass("on");
		$(showId).css("display","none").eq(index).css("display","block");
        // 激活延迟加载
        $("img.lazy").lazyload();
		var box=$(showId).eq(index);
		var textarea=$('textarea.J_lazyRender',box);
		if(textarea.length){
			var html=textarea[0].value;
			box.html(html.replace(/src(\s*)=(\s*)".+"/g,function(w){
				return w.replace('~',sn.cityId)
			}));
		}
		var v = $(showId).eq(index).find("img[src3]");
		v.each(function() {
			$(this).attr("src", $(this).attr("src3")).removeAttr("src3")
		});
	});
};
snFloorMenu = function(btn){
	var time;
	$(btn).hover(function(){
		var ref = $(this);	
		time = setTimeout(function(){
			ref.find("b").addClass("hover");
			ref.find(".layOutlist").css("display","block");
		},200);		
	},function(){
		clearTimeout(time);
		$(this).find(".layOutlist").mouseover(function(){
			$(this).css("display","block");
			$(this).parent().find("b").addClass("hover");
		});
		$(this).find("b").removeClass("hover");
		$(this).find(".layOutlist").css("display","none");	
	});	
};

//八连版
var isRun = false;
function snFlash(){	
	if(isRun){return;}
	var timer2;
	var flashIndex = 0;
	var snFlashTimer = null;
	var box = $("#slideContBox");
	var numHandle = box.find(".handle").find("em");
	var obj = box.find(".slideCont");
	if(!obj.length){return;}
	var objnum = obj.length-1;
	//初始化
	obj.eq(0).show();//.find("dd").css({opacity:1});
	//触显当前
	numHandle.hover(function(){
		clearInterval(snFlashTimer);
		var ref = $(this)		
		timer2 = setTimeout(function(){
			flashIndex = ref.index();
			move(flashIndex)
		},100);
	},function(){
		clearTimeout(timer2);
	});	
	//右侧移入清除定时器
	box.hover(function(){
		clearTimeout(timer2);
		clearInterval(snFlashTimer);
	},function(){
		clearTimeout(timer2);
		clearInterval(snFlashTimer);
		snFlashTimer = setInterval(function(){
			flashIndex++;
			if(flashIndex>objnum){flashIndex=0;}			
			move(flashIndex);
		},5000);
	}).trigger("mouseleave");	
	//动画效果
	function move(v){
		if(v!=0){
			//initAd(v);
		}
		numHandle.removeClass("on");
		numHandle.eq(v).addClass("on");
		var _dl = obj.eq(v);
		var sdl = _dl.siblings(".slideCont");
		var _dd = _dl.find("dd");
		var sdd = sdl.find("dd");
		obj.hide();
		
		//sdl.css({"z-index":1});	
		_dl.show();//.css({"z-index":200});
		
		if(jQuery.browser.msie){
			_dd.stop(true,true).show();
			sdd.stop(true,true).hide();
		}else{
			_dd.stop(true,true).animate({opacity:1},1000);
			sdd.stop(true,true).animate({opacity:0},1000);
		}
		
		/*
		var img = _dl.find("img[src3]");
		img.each(function() {
			$(this).attr("src", $(this).attr("src3")).removeAttr("src3");
		});
		*/
	}
	//wangping_八连版触发懒加载
	function initAd(u) {
		u++;
		var textarea = $("#textarea_"+u);
		if (textarea.length>0) {
			var jsonStr = textarea.val();
			var html = "";
			var json = eval("(" + jsonStr + ")");
			
			var w = (is_big_screen) ? "710" : "510";
			var src = json.src;
			if (!is_big_screen) {
				src = json.srcB;
			}
			
			var href = json.href;
			var alt = json.alt;
			var mdName = json.mdName;
			html = "<a href='" + href + "' title='"
				+ alt + "' target='_blank' name='" + mdName
				+ "'><img width='" + w + "' height='420' src='" + src
				+ "' alt='" + alt + "' /></a>";
			
			var slideId = $("#slide_" + u);
			slideId.html(html);
		}
	}
	isRun = true;
};

(function($){
	snFlash();
	snScrollClick();//收起楼层 显示楼层	
	snshareImg(".shareImg li");//晒单专区 最热图集鼠标放置后显示标题效果
	
	snfloorScroll($("#snProlist .proArae").eq(0),$("#snProlist ol li").eq(0));
	
	/*snfloorScroll($("#snProlist .proArae").eq(1),$("#snProlist ol li").eq(1));
	snfloorScroll($("#snProlist .proArae").eq(2),$("#snProlist ol li").eq(2));
	snfloorScroll($("#snProlist .proArae").eq(3),$("#snProlist ol li").eq(3));*/
	
	snshareScroll($("#snshare ol"),$("#snshare"));//晒单专区-分享乐趣，让我们更亲近-四轮播Tab
	
	snTabBoxCout("#snProlist ul li","#snProlist .proArae","#snProlist ol li");//最新抢购Tab

	snTabBox("#snNoice dl dt","#snNoice dl dd");//公告资迅Tab
	//snTabBox("#snexpress dl dt","#snexpress dl .snexpressOut");//旅行Tab
	snTabBox("#snSub dl dt","#snSub dl dd");//独家首页Tab
	//snTabBox("#bookRack dl dt","#bookRack dl dd");//图书Tab
	
	snTabBox("#floor00 .sub_cat_name li","#floor00 .sub_cat_content");
	snTabBox("#floor01 .sub_cat_name li","#floor01 .sub_cat_content");
	snTabBox("#floor02 .sub_cat_name li","#floor02 .sub_cat_content");
	snTabBox("#floor03 .sub_cat_name li","#floor03 .sub_cat_content");
	snTabBox("#floor04 .sub_cat_name li","#floor04 .sub_cat_content");
	snTabBox("#floor05 .sub_cat_name li","#floor05 .sub_cat_content");
	snbrandTab(".floor00 .snBrand ol li",".floor00 .snBrand ul li");
	snbrandTab(".floor01 .snBrand ol li",".floor01 .snBrand ul li");
	snbrandTab(".floor02 .snBrand ol li",".floor02 .snBrand ul li");
	snbrandTab(".floor03 .snBrand ol li",".floor03 .snBrand ul li");
	snbrandTab(".floor04 .snBrand ol li",".floor04 .snBrand ul li");
	snbrandTab(".floor05 .snBrand ol li",".floor05 .snBrand ul li");
	
	snFloorMenu(".pull_down_cats");//楼层全类下拉
	
	/*
	showThreeSort();
	$(".datalazyload").datalazyload({type : "textarea"});
	$("a[name*=dac_index_],input[name*=dac_index_]").live("click",function(){
	    sendDatasIndex(this);
	});
	*/
})(jQuery);

