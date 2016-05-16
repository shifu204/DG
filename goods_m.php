<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="UTF-8">
	<title>商品详情</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">
	<link rel="apple-touch-icon-precomposed" href="http://image.yihaodianimg.com/mobile-website/images/website/screenLogo.png">
	<link href="goods_m_files/mobile.css" type="text/css" rel="stylesheet">
	<script>var _hmt = _hmt || [];_hmt.push(['_setCustomVar', 5,  'visitor', 'visitor', 1]);</script>

    <style type="text/css">
        .proDetailHeadInfo .item_price{
            height: 25px;
            line-height: 25px;
            padding: 0 10px;
            color: #ff3c3c;
        }

        .proDetailHeadInfo .item_price > span:first-child{
            float: left;
        }

        .proDetailHeadInfo .item_price > span:last-child{
            float: left;
            margin-left: 5px;
        }

        .proDetailHeadInfo .original_price{
            font-size: 12px;
            color: #999;
            text-decoration: line-through;
        }

        .proDetailHeadInfo .item_price .small, 
        .proDetailHeadInfo .item_price.small, 
        .proDetailHeadInfo .use_credit .small{
            font-size: 12px;
        }

        .proDetailHeadInfo .use_credit{
            height: 58px;
            border-top: 1px dashed #d3d3d3;
            border-bottom: 1px dashed #d3d3d3;
            margin: 0 7px 0 10px;
        }

        .proDetailHeadInfo .use_credit .option{
            height: 20px;
            line-height: 20px;
            margin-top: 10px;
        }

        .proDetailHeadInfo .use_credit .option input[type="checkbox"]{
            border-radius: 0;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background-size: 20px;
            background-repeat:no-repeat; 
            border:none;
            background-image:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAABQCAIAAADk7aCWAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4OTdGMTk0MjQ4M0IxMUUzOUM4NkUwMDgzMEE2MUE1NSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4OTdGMTk0MzQ4M0IxMUUzOUM4NkUwMDgzMEE2MUE1NSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjg5N0YxOTQwNDgzQjExRTM5Qzg2RTAwODMwQTYxQTU1IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjg5N0YxOTQxNDgzQjExRTM5Qzg2RTAwODMwQTYxQTU1Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+3jykDwAAA8FJREFUeNrsV19IU1EYd9t1U8dQl2XOmDbFVKZzOp0+ZGQYQlKRST0WQRD0Yr1khQ9iRklPET1FvkWFImp/HiowH3Iy50Yjl0yHkpvm/IfOdO1PPz1xkYFl2+6V4HwPl9/97uH+vvOd7/edcwQ9PT0xu2HCmF0ySkyJKTElDtsYjv5bWFiYnp7ucDhsNht/My4oKFAqlSKRSKVS8ZfqrKysjIwMgjFjnohTU1Nzc3MJnpmZ2S7PUSZOSkoqLi4WCATAy8vLw8PDwWCQc2KpVFpWVoZ1BV5bWzMYDD6fj3M5icVisOIJDD6wgptzHQuFQmQYMwZGbo1GI/IcfgNRKBQVFRXQ4l9/odFoUlJSCLZarW63O6IGgg7AMIxcLg8EAi6Xa7thqGE2OLvdPjExEWnLnJ+fxxMlqtVq2QmFGPSanZ1N8NTU1B/E8w/EZrN5ZWWFLKFOp5PJZCEDEI1arSYY6bVYLNHZJLxe78DAAClO5Ly8vJyUDzHEgWiIZD0ej8lkwopEbXfaKkeJRMIKJi4uTq/XIxoS3+DgIJ5R3hYhDPzX7/ezLQIRgBXc8MCPr5gxJ/sxqgyZJM0PTbGqqoqsNzzwLy4ucngQQLuHQAkmTRGGGoaf8xMIBLpVLXgdGxvj6QSC/hAbG5uZmel0OtkE8HT0Gdk0esqkxJSYEu/ypW2EeUhTTYkpMSWmxLtJLKhRNzRUdx/OucArcXX+1YIDNYxIUppZzx9x6cGzRcpagk0TXTwRZ++rOJJzieCxWUPf6BM+iNOScms1jQLBxj9nlx2vLPeCwQDnxMkJijPFzbEiycblds3dMXR73efhvKrjxYl1ujsJ4kRg8IEV3JzLSSRkTmpuYsbAgaCva7gZeQ5fx4f2V54va8tLO7oDyV5X7ikiL+9HHk/OmSO6Ldaor4mZ+PTkfEzi63T/dsMqcy7mK6oINow/N0/2Rtq5phY2bsBCAXOi8AY7oRCDXvWqcwR/cX74OPo0Ci3z9ee2uZVJsoSntU17ZQdDBiCaY3lXCEZ631ofxMQEo0C86l16aWwkxSlhpPW6VlI+xBAHokE+gBdWnd2WVn/AF7VNYqscpRI5KxhZXEpdSQuiIfF1GG/98C5FeXeCMDqHmn7619kWsRFBSQu44YG/09SEGXOyLX5bsPZa7pLmh6Z4ubKdrDc88LsWbeGpf0cNxP7907uRR7/1t9kUYahh+Dk/CECg/aPtW18HHS8iurTtfOjA+DMxk1CScco23ccmIPwzyv03x+lhjxJTYkr8fxH/EmAAFUGOYZQhl0MAAAAASUVORK5CYII='); 
        }

        .proDetailHeadInfo .use_credit .option input[type="checkbox"]:checked{
            background-position: 0 -20px; 
            background-color:rgba(0,0,0,0);
            border:none;
        }

        .proDetailHeadInfo .use_credit .credit_detail .price{
            color: #ff3c3c;
        }

        .proDetailBox .discount{
            background-color: #f27444;
        }

        .no_prom_notice{
            height: 38px;
            line-height: 38px;
            padding-left: 10px;
            border: 1px solid #f2f2f2;
            background-color: #ffffed;
            margin: 10px 0;
            display: none;
        }
        
        .proDetailHeadInfo p .promolabel {
        	display: inline-block;
			margin-left: 10px;
        }
        
        .proDetailHeadInfo .tuan{
        	margin-right:5px;
        }
        
        .proDetailHeadInfo .tuan em {
			display: inline-block;
			padding: 2px;
			background-color: #FE5955;
			color: #fff;
			margin-left: 10px;
		}
    </style>

</head>

<body class="relative">
	<img class="mlvwvvaomluapueoxzqw" style="display: none" src="goods_m_files/hm.gif" height="0" width="0">

	<header class="titleHead clearfix">
			<h2>商品简介i</h2>
			<button onclick="javascript:backlist();" class="backArrow cur">返回</button>
		</header>
	<div class="wrap">
		<ul class="tabV2 clearfix">
				<li class="cur"><a href="">基本信息</a></li>
				<li><a href="http://m.yhd.com/mw/picture/28352/1/1/">商品详情</a></li>
				<li><a href="http://m.yhd.com/mw/comment/28352/1/1/">评价（1147）</a></li>
			</ul>
			<input id="merchantId" value="1" type="hidden">
			<!--start proDetailHead-->
			<div class="proDetailHead mt clearfix">
				<!--start slide-->
				<div id="proDetailScroll" class="proDetailScroll">
					<div class="in">
						<ul class="clearfix"><li><img src="goods_m_files/CgQCtFDqitGAPzzkAAIJr7uadvc79701_200x200.jpg" alt="" height="154" width="154"></li>
							<li><img src="goods_m_files/CgQDrVH6L1KAU5SHAAKV0zCGnDo14801_200x200.jpg" alt="" height="154" width="154"></li>
								<li><img src="goods_m_files/CgQCsFH6LtSAbwiCAAKuhiOLFVk62601_200x200.jpg" alt="" height="154" width="154"></li>
								<li><img src="goods_m_files/CgQCtFDqitGAPzzkAAIJr7uadvc79701_200x200.jpg" alt="" height="154" width="154"></li>
								<li><img src="goods_m_files/CgQDrVH6L1KAU5SHAAKV0zCGnDo14801_200x200.jpg" alt="" height="154" width="154"></li></ul>
					</div>
				<span class="mYhdSrollArrow mYhdSrollArrowL"></span><span class="mYhdSrollArrow mYhdSrollArrowR"></span><div style="margin-left: -16.5px;" class="mYhdSrollNav"><a hre="javascript:;" class="cur">1</a><a hre="javascript:;">2</a><a hre="javascript:;">3</a></div></div>
				<!--end slide-->

				<div class="proDetailHeadInfo">
					<p class="item_price">
			                <span><span class="small">¥</span><span>157.0</span></span>
			                <span class="original_price">¥179.0</span></p>
						<a href="#promotiondetail"> <!--<p class="deepRed">有换购</p>-->
						<p class="grey">
								<span class="promolabel">优惠:</span>
								<i class="whiteTag deepRed">折</i>
								<i class="whiteTag deepRed">赠</i>
								</p>
						</a> <a href="http://m.yhd.com/mw/comment/28352/1/1/">
						<p class="starSec">
							<span class="star star4"></span>
							<span class="commtNum">99+</span>
						</p>
					</a>

				</div>
			</div>
			<!--end proDetailHead-->
			<div class="proDetailBtnSec mt">
				<span onclick="javascript:addfav(28352,1);" class="favBtn">
					<i id="favBtn" class="loveIco"></i>收藏 <em id="add1" class="favBtnTip none"><i></i></em> <em id="add2" class="favBtnTip none">商品已收藏<i></i></em>
				</span> 
				<input id="buycount" class="numIpt" value="1" type="number">
				<span class="cartBtnLong" id="adToCart" onclick="javascript:addProduct('99082','','1');">
						<i class="cartIco"></i>加入购物车
					</span>
				</div>

			<a href="http://m.yhd.com/mw/picture/28352/1/1/">
				<div class="proDetailBox mt">
					Abbott雅培 金装儿童喜康力儿童配方奶粉 智护100 900g/罐 4段3岁以上<i class="greyRightArrow"></i>
				</div>
			</a>
			
			<p class="no_prom_notice">兑换后此商品不参加其他优惠活动</p>
			
			<ul class="proDetailBox mt" id="promotiondetail">
					<a href="http://m.yhd.com/mw/promotionprolist/334346/326782/1/1?productId=28352&amp;merchantId=1&amp;promotiontitle=%E6%8C%87%E5%AE%9A%E5%95%86%E5%93%81%E6%BB%A12%E4%BB%B6%EF%BC%8C%E6%AF%8F%E4%BB%B68.0%E6%8A%98">
							<li>
									<p class="promTags">
										<span class="discount">折</span></p> 指定商品满2件，每件8.0折<i class="greyRightArrow"></i>
								</li>
							</a>
						<a href="http://m.yhd.com/mw/promotionprolist/342396/336407/1/1?productId=28352&amp;merchantId=1&amp;promotiontitle=%E6%8C%87%E5%AE%9A%E5%95%86%E5%93%81%E6%BB%A11%E4%BB%B6%EF%BC%8C%E5%8F%AF%E8%B5%A0%E4%BB%A5%E4%B8%8B%E4%BB%BB%E4%B8%80%E5%95%86%E5%93%81">
							<li>
									<p class="promTags">
										<span class="gift">赠</span></p> 指定商品满1件，可赠以下任一商品<i class="greyRightArrow"></i>
								</li>
							</a>
						</ul>
			<!-- 店铺入口 -->
			<div class="headWithLine mt">
					<p class="line"></p>
					<h4>买过该商品的还看过</h4>
				</div>

				<div class="threeColumnBox">
					<ul class="clearfix">
						<li><a href="http://m.yhd.com/mw/product/46468_1_1_1">
									<img src="goods_m_files/CgQCrlH6LhyAJ_U5AALcHstJlQ497001_200x200.jpg" alt="" height="80" width="80">
							</a>
								<p class="price">
									<span class="deepRed">¥188.0</span>
								</p>
								<p class="title">
									Abbott雅培 金装儿...</p></li>
						<li><a href="http://m.yhd.com/mw/product/28351_1_1_1">
									<img src="goods_m_files/CgQCsVH6L2yAbEkSAAI0-AJNV2U44901_200x200.jpg" alt="" height="80" width="80">
							</a>
								<p class="price">
									<span class="deepRed">¥66.0</span>
								</p>
								<p class="title">
									Abbott雅培 金装儿...</p></li>
						<li><a href="http://m.yhd.com/mw/product/28350_1_1_1">
									<img src="goods_m_files/CgQCrVH6L2aAA3i7AAKM87bSnLU87101_200x200.jpg" alt="" height="80" width="80">
							</a>
								<p class="price">
									<span class="deepRed">¥196.0</span>
								</p>
								<p class="title">
									Abbott雅培 金装幼...</p></li>
						</ul>
				</div>
				<div class="pages_nextprev">
					<a href="http://m.yhd.com/mw/interestpro/28352/1"><p class="floatListMore mt">更多商品</p></a>
				</div>
			</div>

	<div class="tipMask"></div>
	<div class="alertTip">
		<div class="tip" id="canclOdr" style="display: none">
			<div class="canclOdrIn clearfix">
				<p id="addtocartinfo"></p>
				<a id="noCancl" href="#" class="greyBtn tinyGreyBtn h30 closeBtn">继续购物</a>
				<a href="http://m.yhd.com/mw/cart" class="redBtn tinyGreyBtn h30">去购物车结算</a>
			</div>
		</div>
	</div>
	<div class="footer">
	<br>
    <div class="in">
        <div class="footSearch">
         <form method="get" action="/mw/search" name="searchForm" id="searchForm_id">
            <input name="keyword" id="keywordfoot" type="text">
            	<button type="button" onclick="return check()"></button>
         </form>
        </div>
       <a href="javascript:commonStatistics('','linkPosition','backhome','/mw/index/1');" class="homeBtn"><span></span></a>
        <a href="javascript:commonStatistics('','linkPosition','backhead','');" class="gotop">
            <span></span>
            <p>回顶部</p>
        </a>
    </div>
    <p class="link">
       <a href="http://www.yhd.com/?from=wap">PC版</a>
       <a href="http://m.yhd.com/istore/">下载iPhone客户端</a>&nbsp;&nbsp;&nbsp;<a class="androidBtn" href="http://m.yhd.com/downloads//TheStoreApp.apk">下载Android客户端</a>
    </p>

</div>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write("<div style='display:none'>"+ unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Ff31531fca780ed5666345856c7edbf91' type='text/javascript'%3E%3C/script%3E")+"</div>");
</script><div style="display:none"><script src="goods_m_files/h.js" type="text/javascript"></script></div>
<script type="text/javascript" src="goods_m_files/zepto.js"></script>
<script type="text/javascript" src="goods_m_files/zepto_002.js"></script>
<script type="text/javascript" src="goods_m_files/v2_tracker_top.js"></script>
<script type="text/javascript" src="goods_m_files/tracker.js"></script>
<script type="text/javascript" src="goods_m_files/v2_tracker_hijack.js"></script>
<script type="text/javascript" src="goods_m_files/v2-mobile.js"></script>

<!-- 如果是客户端写客户端的统计 -->
<script type="text/javascript">

//	搜索自动提示
   sechSgst();
   function check(){
	 //保存搜索词
	   saveSearchHistory();
		var keyword=document.getElementById("keywordfoot").value;
		keyword = keyword.replace(/\s/gi,"");
		if(keyword==""){
			//  alert("搜索关键字不能为空");
			return false;
		} 
		url = "/mw/search?keyword="+keyword;
		commonStatistics('',"linkPosition","bottomsearch",url);
	  }
 //埋点统计公共方法
 function commonStatistics(formName,typekey,typevalue,locationUrl){
	 var posetype="1";
	if(typekey=='linkPosition') {
		posetype ="1";
	}else if(typekey=='buttonPosition'){
		posetype ="2";
	}else if(typekey=='pmId'){
		posetype = "3";
	}
	addTrackPositionToCookie(posetype,typevalue);
 	if(locationUrl != null && locationUrl != ''){
 		window.location = locationUrl;
 	}else{
 		gotracker(posetype,typevalue,null);
	}
	
 }	 
 
 
//在与用户相关的链接后面加随机数 
	var myDate = new Date();
	var now=myDate.getTime();
	//我的购物车
   var obj=document.getElementById("mycart");
   if(obj!=null){
  	 var href="/mw/cart?et="+now+"&er="+getRandomString(10);
  	 obj.setAttribute('href', href); 
   }
   //我的1号店
   obj=document.getElementById("mystore");
   if(obj!=null){
  	 var href="/mw/mycenter?et="+now+"&er="+getRandomString(10);
  	 obj.setAttribute('href', href); 
   }
   //面包屑我的1号店 
   obj=document.getElementById("crumbsmystore");
   if(obj!=null){
  	 var href="/mw/mycenter?et="+now+"&er="+getRandomString(10);
  	 obj.setAttribute('href', href); 
   }
   //切换省份
   obj=document.getElementById("changeprovince");
   if(obj!=null){
  	 var href="/mw/provice?et="+now+"&er="+getRandomString(10);
  	 obj.setAttribute('href', href); 
   }	
   //1号商城订单 
   obj=document.getElementById("mallorder");
   if(obj!=null){
  	 var href=document.getElementById("mallorder").getAttribute("href")+"&et="+now+"&er="+getRandomString(10);
  	 obj.setAttribute('href', href); 
   }
 
var wuserId = null;            
userinfo();
function userinfo(){
	var myDate = new Date();
	var now=myDate.getTime();
	 $.ajax({   
         type: "POST",   
         url: "/mw/indexinfo?et="+now+"&er="+getRandomString(10),   
         data: "",   
         dataType : "", 
         success: function(backdata) { 
        	var data = backdata;
        	var data = backdata.split("∑");
        	 if(data[0]!=null&&data[0]!=''){
        	 	$("#qj").append(data[0]);
        	 	$("#qj").show();
        	 }
        	 $("#bquantity").append(data[1]);
        	 $("#cprovincename").append(data[2]);
             if(data[0] !=""){
                $("#searchBtn").click();
             }
        	 //data[4]:token    data[5]:sharetokenflag
        	 if(data[4]== null || data[4]== "null"  ){
	        	 data[4] =""; 
	         }
	         if(data[5]== null || data[5]== "null"  ){
	        	 data[5] =""; 
	         }
	         if(data[4] !="" &&$('#logstatus').hasClass('noLog')){  //区分用户登录状态，登录后在我的1号店图标上加勾  
	         	$('#logstatus').removeClass('noLog');
	         }
         }   
     });   
}

function getCookie(name)
{
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg)){
		return (arr[2]);
	}
	else{
		return "";
	}
}
	
function success_jsonpCallback(param){
	//alert(param);
}

	
//清除yhdcomefromwebsite这个cookie
function backwap(url)
{
	debugger;
	delCookie("yhdcomefromwebsite");
	window.location.href =url+"?websiteflag=true";  
}

function delCookie(name){//为了删除指定名称的cookie，可以将其过期时间设定为一个过去的时间
	var date = new Date();
	date.setTime(date.getTime() - 10000);
	document.cookie = name + "=a; expires=" + date.toGMTString();
}

//保存用户搜索历史记录 
function saveSearchHistory(){
	var keyword=document.getElementById("keywordfoot").value.toString();
  //将用户的搜索关键词保存到local storage中 ，不支持则保存到cookie中 ,最多保留20个  ,去重,且过滤掉空字符串 
  var shistory="";
  var arrhistory="";
  if(window.localStorage){
  	if(window.localStorage.getItem("historykeyword")!=null){
  		shistory = window.localStorage.getItem("historykeyword");
  		arrhistory = window.localStorage.getItem("historykeyword").split("|");
  	}
  }else{
  	if(getCookie("historykeyword")!=null){
  		shistory = getCookie("historykeyword");
  		arrhistory = getCookie("historykeyword").split("|");
  	}
  }
  
  var isdiff = 0;
  if(arrhistory!=""&&arrhistory.length>0){
  	$.each(arrhistory,function(index,item){
  		if(keyword==item){
  			isdiff = 1;
  		}
  	});
  }
  
  keyword=keyword.replace(/(^\s*)|(\s*$)/g, "");  
  if(keyword==""){ //搜索历史过滤空字符串 
  	isdiff = 1;
  }
  
  if(isdiff==0){
	    if(arrhistory!=""&&arrhistory.length==20){
	    		arrhistory.splice(0,1);
	    		shistory = arrhistory.join("|");
	    		shistory+="|"+keyword;
  	}else{
  		if(arrhistory.length<1){
  			shistory+=keyword;
  		}else{
  			shistory+="|"+keyword;
  		}
  	}
  	
  	if(window.localStorage){
  		window.localStorage.setItem("historykeyword",shistory);
  	}else{
  		setCookie("historykeyword",shistory);
  	}
	}
	
}


</script>
<script type="text/javascript" src="goods_m_files/jq.js"></script>
	<script type="text/javascript" src="goods_m_files/mobile.js"></script>
	<script type="text/javascript" src="goods_m_files/cart.js"></script>
	<script type="text/javascript">

	trackerContainer.addParameter(new Parameter("extField7","99082"));
	
    function backlist(){
		var backreferer = 'http://m.yhd.com/mw/listproduct/5984_2_1/1?name=%E5%A5%B6%E7%B2%89';
		if(backreferer!='null'&&backreferer!=''){
			document.location =backreferer;
		}else{
			window.history.back(-1);
		}
	}
    yhdLib_inshop.fullColumnScroll({
        id:'proDetailScroll', //boxId
        prevnext:true,//是否显示左右翻页导航
        numnav:true,//是否显示数字翻页导航
        time:2//自动翻页时间*秒
    });

    if(navigator.userAgent.indexOf('UC') > -1){
		var lazySourceLen = $("img.lazy").length;
		for(var i=0; i<lazySourceLen;i++){
			var lazyImg = $("img.lazy:eq("+i+")");
			var lazySourceSrc = lazyImg.attr("data-original");
			lazyImg.attr("src", lazySourceSrc).removeAttr("data-original").show();
		}
    }

	//赠品点击左右箭头 
	multiDivScroll();

    num_buy();

     $('#noCancl').click(function(){//点击 否 关闭 取消订单 弹框
        $(".tipMask, .tip").hide();
        if($('#adToCart').hasClass('greyBtn')){
        	$('#adToCart').removeClass('greyBtn');
        }
        return false;
    })  
    
    fChangeBigImgByThumb('.thumbCol i', '#slideIn img');//详情页点击缩略图切换大图
    immediately(document.getElementById("buycount"));
    
	function immediately(element){
		if("\v"=="v") {
			element.onpropertychange = webChange;
		}else{
			element.addEventListener("input",webChange,false);
		}
		function webChange(){
			if(element.value==null||element.value==''||element.value<1){
			alert("请输入正确的数量");
			}
		}
	}
	
   
    function addProduct(pmId,promotionId,merchantId) {
    	/* commonStatistics('',"buttonPosition","addcart",'');
    	commonStatistics("","pmId",pmId,""); */
    	addcartStatistics(pmId);
    	$('#adToCart').addClass('greyBtn');//加入购物车 按钮点击变灰
	    var buynum = $("#buycount").val();
	    if(buynum==null||buynum==''||buynum<1){
	    	alert("请输入正确的数量");
	    	return false;
	    }
	    var needpoint = 0;
		var obj = $("#needpoint");
		var checkbox = $("input[type='checkbox']");
		if(obj!=null&&checkbox!=null&&checkbox[0]!=null&&checkbox[0].checked==true){
		    needpoint=obj.val();
		}	
	    if(promotionId==''||promotionId=='null'){
	    	promotionId = '0';
	    }
	    var landingpagePoint = '';
	    if(landingpagePoint!=''&&landingpagePoint!='0'){//landingpage积分商品，按landingpage商品加购物车
	    	needpoint = 0;
	    }
	    var now = new Date();
		 $.ajax({   
	         type: "POST",   
	         url: "/mw/buyproduct/"+pmId+"/"+buynum+"?now="+now+"&needpoint="+needpoint+"&promotionId="+promotionId+"&merchantId="+merchantId,  
	         data: "",   
	         dataType : "", 
	         success: function(backdata) {
	          	var data = backdata.split("∑");  
	            backdata=data[0];
	         	if(backdata.indexOf("登录")>0){
	         		window.location.href="/mw/login";
	         	}else{
		         	$("#addtocartinfo").html(backdata);
					$(".tipMask, #canclOdr").show();
		         	fSetAlertTipPosition('#canclOdr');
	         	}   
	      }
	  }); 
 	}
	 
	 function addfav(productId,merchantId){
	 	commonStatistics('',"buttonPosition","addfav",'');
	 	$.ajax({   
		         type: "POST",   
		         url: "/mw/addmyfav/"+productId+"/"+merchantId+"?isSerial=true",   
		         data: "",   
		         dataType : "", 
		         success: function(backdata) {
		         	if(backdata=="请先登录"||backdata=="用户未授权,请先登录"){
		         		window.location.href ="/mw/login";
		         	}
				
		         	document.getElementById("add1").innerHTML=backdata; 
		         	
			        if($('#favBtn').hasClass('cur')){
			            $('#favBtn').next().next().fadeIn(400);
			        }else{
			            $('#favBtn').addClass('cur').next().fadeIn(400);
			        }
			        setTimeout(function(){
			            $('#favBtn').siblings().fadeOut(600);
			        }, 2000);
			        return false;
				   
		         }   
		 }); 
	 }
	 
	var refer='http://m.yhd.com/mw/listproduct/5984_2_1/1?name=%E5%A5%B6%E7%B2%89';
	var merchantId=document.getElementById("merchantId").value;
   	if((refer.indexOf("/mw/login")>0||refer.indexOf("/mw/authorize")>0||refer.indexOf("/mw/malldologin")>0)&&needpoint>0){
   		addProduct();
   	}
   	
   	//保存用户浏览商品记录,最多保留100个 
   	var pid = 28352;
   	saveBrowsingpro();
   	function saveBrowsingpro(){
	    var shistory="";
	    var arrhistory="";
	    if(false){
	    	if(window.localStorage.getItem("browsinghistory")!=null){
	    		shistory = window.localStorage.getItem("browsinghistory");
	    		arrhistory = window.localStorage.getItem("browsinghistory").split("|");
	    	}
	    }else{
	    	if(getCookie("browsinghistory")!=null){
	    		shistory = getCookie("browsinghistory");
	    		arrhistory = getCookie("browsinghistory").split("|");
	    	}
	    }
	    
	    var isdiff = 0;
	    if(arrhistory!=""&&arrhistory.length>0){
	    	$.each(arrhistory,function(index,item){
	    		if(pid==item){
	    			isdiff = 1;
	    		}
	    	});
	    }
	    
	    if(isdiff==0){
		    if(arrhistory!=""&&arrhistory.length==100){
	    		arrhistory.splice(0,1);
	    		shistory = arrhistory.join("|");
	    		shistory+="|"+pid;
	    	}else{
	    		if(arrhistory.length<1){
	    			shistory+=pid;
	    		}else{
	    			shistory+="|"+pid;
	    		}
	    	}
	    	
	    	if(true){
	    		window.localStorage.setItem("browsinghistory",shistory);
	    	}else{
	    		//setCookie("browsinghistory",shistory);
	    	}
	   	}
   	}
   	
   	
   	//保存商城商品浏览历史
   	var site='stroe';
   	if(site=='mall'){
   		 savemallbrowse();
   	}else{
   		 saveurl="/mw/savebrowse?productId="+pid+"&isYhd=1";
   	 	 $.ajax({ 
   	 	 	type: "POST",   
			url: saveurl,
			data:'', 
			success:function(result) { 
				//alert(result);
			}, 
			timeout:3000 
		});
   	}
   	function savemallbrowse(){
   		 saveurl="http://m.yhd.com/mw/savebrowse?productId="+pid;
   	 	 $.ajax({ 
			url: saveurl,
			dataType:'jsonp', 
			data:'', 
			jsonp:'callback', 
			jsonpCallback:"success_jsonpCallback",
			success:function(result) { 
				//alert(result);
			}, 
			timeout:3000 
		});
   	}
   	
   	if(document.querySelector("#chk_credit")!=null){
	    document.querySelector("#chk_credit").addEventListener("click", function(event){
	        if(false){//此处判断积分是否足够
	            event.preventDefault();
	            alert("对不起，您的积分不足，无法兑换");
	            return false;
	        }
	        if(this.checked){
	        	$("#promotiondetail").hide();
	        	if($("#promotiondetail")!=null&&$("#promotiondetail").length>0){
	            	$(".no_prom_notice").show();
	        	}
	            $(".item_price").addClass("small");
	            $(".credit_detail").removeClass("small");
	        }
	        else{
	            $("#promotiondetail").show();
	            $(".no_prom_notice").hide();
	            $(".item_price").removeClass("small");
	            $(".credit_detail").addClass("small");
	        }
	    });
   	}


</script>


</body></html>
