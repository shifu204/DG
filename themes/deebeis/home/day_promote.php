<div class="dayPromoteBody" id="dayPromoteBody">
    <div id="left-side-buoy"></div>
<script type="text/javascript">
$(function(){
        
        //点击左边按钮，
        $(".r_ll").click(function(){
            var scroll_l = $("#slide>ul");
            scroll_l.animate({marginLeft:"+1080px"},100,function(){
            scroll_l.css({marginLeft:0}).find("li:last-child").prependTo(scroll_l);
            scroll_l.css({marginLeft:0}).find("li:last-child").prependTo(scroll_l);
            scroll_l.css({marginLeft:0}).find("li:last-child").prependTo(scroll_l);
            scroll_l.css({marginLeft:0}).find("li:last-child").prependTo(scroll_l);
            });
        });

        //点击右边按钮，
        $(".r_rr").click(function(){
            var scroll_r = $("#slide>ul");
            scroll_r.animate({marginLeft:"-1080px"},100,function(){
                scroll_r.css({marginLeft:0}).find("li:first").appendTo(scroll_r);
                scroll_r.css({marginLeft:0}).find("li:first").appendTo(scroll_r);
                scroll_r.css({marginLeft:0}).find("li:first").appendTo(scroll_r);
                scroll_r.css({marginLeft:0}).find("li:first").appendTo(scroll_r);
            });
        });
        
        $(".r_ll").hover(
            function(){
                $(this).addClass("r_llh");
            },
            function(){
                $(this).removeClass("r_llh");
            }
        );
        $(".r_rr").hover(
            function(){
                $(this).addClass("r_rrh");
            },
            function(){
                $(this).removeClass("r_rrh");
            }
        );
        
});

</script>
    <div class="section">
        <div class="roll clearfix">
            <div class="roll_title"></div>
            <div class="roll_l">
                <input type="button" class="r_ll"/>
            </div>
            <div id="slide" class="slide">
                <ul class="clearfix">
                    <?php foreach($this->_var['day_promote'] as $dk=>$dv):?>
                    <li>
                        <div class="photo">
                            <img class="lazy"  width="246" height="308" src="<?php echo $dv['promote_img']?>"  />
                        </div>
                        <div class="rsp"></div>
                        <a href="<?php echo $dv['url']?>" target="_blank">
                        <div class="text">
                            <h3></h3>
                            <div class="endtime" value="<?php echo $dv['promote_end_date'];?>"></div>                    
                        </div>
                        </a>
                        <div class="dayPromotePrice color8" style="position: absolute; top:65px;left:30px; font-size: 18px; cursor: pointer;"><?php echo $dv['formated_promote_price']?><del style="padding-left: 20px;"><?php echo $dv['formated_shop_price']?></del></div>
                    </li>
                    <?php endforeach;?>
                </ul>
            <div class="clear"></div>
            </div>
            <div class="roll_r">
                <input type="button" class="r_rr"/>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function(){
        /*天天特价*/
        //$(".section ul li .rsp").hide();	
        $(".section ul li").hover(function(){  
                $(this).addClass('hover');
                $(this).find(".rsp").stop().fadeTo(0,0.6);
                $(this).find(".text").stop().show().animate({top:'0'}, {duration: 100});
            },
            function(){
                $(this).removeClass('hover');
                $(this).find(".rsp").stop().fadeTo(0,0);
                $(this).find(".text").stop().animate({top:'-10'}, {duration: 100});
                $(this).find(".text").animate({top:'-10'}, {duration: 0}).hide();
            }
        );
    });
    //倒计时
    //var serverTime =  * 1000; //服务器时间，毫秒数 
    $(function(){ 
        //var dateTime = new Date(); 
        //var difference = dateTime.getTime() - serverTime; //客户端与服务器时间偏移量 
        setInterval(function(){ 
          $(".endtime").each(function(){ 
            var obj = $(this); 
            var endTime = new Date(parseInt(obj.attr('value')) * 1000); 
            var nowTime = new Date(); 
            //var nMS=endTime.getTime() - nowTime.getTime() + difference; 
            var nMS=endTime.getTime() - nowTime.getTime();
            var myD=Math.floor(nMS/(1000 * 60 * 60 * 24)); //天 
            var myH=Math.floor(nMS/(1000*60*60)) % 24; //小时 
            var myM=Math.floor(nMS/(1000*60)) % 60; //分钟 
            var myS=Math.floor(nMS/1000) % 60; //秒 
            var myMS=Math.floor(nMS/100) % 10; //拆分秒 
            if(myH < 10 ){
                myH = "0" + myH;
            }
            if(myM < 10 ){
                myM = "0" + myM;
            }
            if(myS < 10 ){
                myS = "0" + myS;
            }
            if(myS>= 0){ 
                var str = myH+":"+myM+":"+myS+"."+myMS; 
            }else{ 
                var str = "00:00:00.0";     
            } 
            obj.html(str); 
          }); 
        }, 100); //每个0.1秒执行一次 
    });
</script>