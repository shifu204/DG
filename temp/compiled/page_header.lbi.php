<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">   
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $this->_var['page_title']; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />   
		<meta property="qc:admins" content="202226306764552516654" />
        <meta name="description" content="<?php echo $this->_var['description']; ?>" />
        <meta name="keywords" content="<?php echo $this->_var['keywords']; ?>" />
        <link rel="shortcut icon" href="favicon.ico" />
        <link rel="stylesheet" type="text/css" href="themes/deebeis/style1.css?v=2015040302" />
        <?php echo $this->smarty_insert_scripts(array('files'=>'jquery.min.js,jquery.lazyload.min.js,common.js')); ?>
        <script type='text/javascript' src='js/sea.js?v=20150403'></script>
        <script type='text/javascript' src='js/sea.config.js?v=20150403'></script>
        <script type='text/javascript' src='themes/deebeis/js/user.js'></script>
        <script type="text/javascript" src="js/artdialog/dialog-min.js"></script>
        <link rel="stylesheet" type="text/css" href="js/artdialog/css/ui-dialog.css" /> 
        <script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "//hm.baidu.com/hm.js?5f1f3b639fab4611ee9cc459ecde269b";
          var s = document.getElementsByTagName("script")[0]; 
          s.parentNode.insertBefore(hm, s);
        })();        
        </script>
        <script  type="text/javascript">
            var _sogou_sa_q = _sogou_sa_q || [];
            _sogou_sa_q.push(['_sid', '272811-280669']);
            (function() {
                var _sogou_sa_protocol = (("https:" == document.location.protocol) ? "https://" : "http://");
                var _sogou_sa_src=_sogou_sa_protocol+"hermes.sogou.com/sa.js%3Fsid%3D272811-280669";
                document.write(unescape("%3Cscript src='" + _sogou_sa_src + "' type='text/javascript'%3E%3C/script%3E"));
            })();
          </script>
    </head>
    <body>
        <div class="topNav clearfix">
            <div class="w1190" style="margin-bottom:0;">
                <div class="f_l" id="welcome">
                    <?php @include $this->_var['theme_path'].'library/member_info.lbi';?>
                </div>
                <div class="f_r">          
                    <div id="user_login_div"><?php @include $this->_var['theme_path'].'library/member_login_div.lbi';?></div>
                </div>
            </div>
        </div>
        
        <div class="head">
            <div class="wm01190 clearfix">
                <div class="f_l pdl20 logo">
                    <a href="/">
                        <img id="logo" name="logo" src="<?php echo $this->_var['theme_path']; ?>images1/logo.png" width="130"></img>
                        <img src="<?php echo $this->_var['theme_path']; ?>images1/logo-right.png"/>
                    </a>
                </div>
                <div class="f_l searchbox">
                    <div class="inputbox clearfix">
                        <div class="f_l inputbox-center">
                            <input value="全场免运费，顺丰极速包邮"  id="search"/>
                        </div>
                        <div class="f_l inputbox-right">   
                            <a id="searchBtn" >搜索</a>
                        </div>
                    </div>
                    <div class="f_l clearfix keywords">
                        <?php $_from = $this->_var['searchkeywords']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'keywords_0_87185300_1463562371');if (count($_from)):
    foreach ($_from AS $this->_var['keywords_0_87185300_1463562371']):
?>
                        <a href="javascript:;"><?php echo $this->_var['keywords_0_87185300_1463562371']; ?></a>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </div>
                </div>
                <div class="f_r">
                    <img src="<?php echo $this->_var['theme_path']; ?>images1/service_phone.gif" style="margin-bottom:28px;" />
                    <a href="http://www.deebei.net/category.php?id=131&filter_id=244#filter_area"><img src="<?php echo $this->_var['theme_path']; ?>images1/buy_give_x.png" ></img></a>
                </div>
            </div>
        </div>
        
        
        <div class="nav">
            <div class="wm01190 navWrap clearfix">
                <?php echo $this->fetch('library/common/all_cat_nav.lbi'); ?>
                <?php $nav_pos = get_nav_position(); $count_nav = count($this->_var['navigator_list']['middle']); ?>
                <div class="f_l navList" id="navList">
                    <ul>                  
                        <?php foreach($this->_var['navigator_list']['middle'] as $nk=>$nav):?>
                        <li  class="<?php if($nk == $count_nav - 1):?> navListLast <?php endif;?> <?php if($nk == $nav_pos):?> selected <?php endif;?>">
                            <a href="<?php echo $nav['url'];?>"><?php echo $nav['name'];?></a>

                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
        
<script type="text/javascript">
    var screenWidth = screen.width;
    var screenHeight = screen.height;   
    /*现在的时间，在ajax查询参数后面添加变量，防止ie缓存ajax查询*/   
    $(document).ready(function(){
        if(screenWidth >= 1920){
            $(".f1nobg").addClass("f1bg");
        }
        /*关键字搜索区域*/
       place_holder($("#search"));
       $("#searchBtn").click(function(){
           var keyword = $("#search").val();
           if(keyword == '' || keyword == '全场免运费，顺丰极速包邮'){
               return;
           } else {
               window.open("search.php?keywords="+keyword);
           }
       });

       window.onkeydown=function(e){
           if(e.keyCode == 13){
               $("#searchBtn").click();
           }
       };

       $(".keywords a").click(function(){
           window.open("search.php?keywords="+$(this).html());
       });
       /*关键字搜索区域*/

       $('#fav').addFavorite('德贝母婴商城',"www.deebei.net");
    });

jQuery.fn.addFavorite = function(l, h) {
    return this.click(function() {
        var t = jQuery(this);        
        if(jQuery.browser.msie) {
            window.external.addFavorite(h, l);
        } else if (jQuery.browser.mozilla || jQuery.browser.opera) {
            t.attr("rel", "sidebar");
            t.attr("title", l);
            t.attr("href", h);
        } else {
            alert("请使用Ctrl+D将本页加入收藏夹！");
        }
    });
};
</script>
<script type="text/javascript">
    //导航栏滑过效果
    $("#navList").find("li").hover(function(){
        $(this).addClass("hover");
    },function(){
        $(this).removeClass("hover");
    });
    //JS获取cookie的值
    function getCookie(objName){//获取指定名称的cookie的值
        var arrStr = document.cookie.split("; ");
        for(var i = 0;i < arrStr.length;i ++){
         var temp = arrStr[i].split("=");
         if(temp[0] == objName) return unescape(temp[1]);
        }
    }
    
    $(".navAllCat .child-cat-child").hover(
      function(){
          $(this).css("color","red");
      },
      function(){
          $(this).css("color","#000");
      }      
    );
   
</script>