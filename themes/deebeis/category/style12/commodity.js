$(function(){
	var nav1=$("#classify label");
	$.each(nav1,function(){
		$(this).click(function(){
			$(nav1).removeClass("onclick");
			$(this).addClass("onclick");
		});
	});
	var nav2=$("#country label");
	$.each(nav2,function(){
		$(this).click(function(){
			$(nav2).removeClass("onclick");
			$(this).addClass("onclick");
		});
	});
	var nav3=$("#age label");
	$.each(nav3,function(){
		$(this).click(function(){
			$(nav3).removeClass("onclick");
			$(this).addClass("onclick");
		});
	});
	var nav4=$("#brand label");
	$.each(nav4,function(){
		$(this).click(function(){
			$(nav4).removeClass("onclick");
			$(this).addClass("onclick");
		});
	});
});