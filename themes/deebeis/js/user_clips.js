define(function(require,exports,module){
    $(".menu-list li").click(function(){
        var selected = false;
        if($(this).hasClass("on")){
            selected = true;
        }
        $(".menu-list li").removeClass("on");
        $(this).addClass("on");
        var href = $(this).find("a").attr("data-href");
        if(typeof(href) != 'undefined' && !selected){
           get_user_data(href);
        }
    });
    
    //点击评分星星出现的效果
     $("a[name='goods_rank']").live("click",function(){
        var rank = $(this).parent();
        $(rank).find("a").removeClass("selected");
        $(rank).removeClass("rank_star1 rank_star2 rank_star3 rank_star4 rank_star5");
        var star_class = 'rank_star'+$(this).attr("data-value");
        $(rank).addClass(star_class);
        $(this).addClass("selected");
    }); 
    
    //点击左侧菜单获取对应的用户界面
    var get_user_data = function(href){
        $.ajax({
            type:"post",
            url:href,
            dataType:'json',
            success:function(data){
                $("#user_right_content").empty();
                $("#user_right_content").append(data.content);
            }
        });
    };    
});

/*热销推荐效果*/
$(function(){
    var page = 1;
    var i = 5;
   $(".hot-right").click(function(){
       var hot_view=$(".hot-view");
       var hot_view_ul=$(".hot-view-ul");
       var hot_width=hot_view.width();
       var len=hot_view.find("li").length;
       var page_count = Math.ceil(len / i);
       if(!hot_view_ul.is(":animated")){
            if(page===page_count){
                hot_view_ul.animate({ left : '0px'}, "slow");
                page = 1;  
            }else{  
                hot_view_ul.animate({ left : '-='+hot_width }, "slow");  //通过改变left值，达到每次换一个版面  
                page++;  
             } 
       }
   });
   $(".hot-left").click(function(){
       var hot_view=$(".hot-view");
       var hot_view_ul=$(".hot-view-ul");
       var hot_width=hot_view.width();
       var len=hot_view.find("li").length;
       var page_count = Math.ceil(len / i);
       if(!hot_view_ul.is(":animated")){
            if(page===1){
                hot_view_ul.animate({ left : '-='+hot_width*(page_count-1) }, "slow");
                page = page_count;  
            }else{  
                hot_view_ul.animate({ left : '+='+hot_width }, "slow");  //通过改变left值，达到每次换一个版面  
                page--;  
             } 
       }
    });

    var record_page = 1;
    var record_i = 2;
   $(".record-right").click(function(){
       var record_view=$(".record-view");
       var record_view_ul=$(".record-view-ul");
       var record_width=record_view.width();
       var len=record_view.find("li").length;
       var page_count = Math.ceil(len / record_i);
       if(!record_view_ul.is(":animated")){
            if(record_page===page_count){
                record_view_ul.animate({ left : '0px'}, "slow");
                record_page = 1;  
            }else{  
                record_view_ul.animate({ left : '-='+record_width }, "slow");  //通过改变left值，达到每次换一个版面  
                record_page++;  
             } 
       }
   });
   $(".record-left").click(function(){
       var record_view=$(".record-view");
       var record_view_ul=$(".record-view-ul");
       var record_width=record_view.width();
       var len=record_view.find("li").length;
       var page_count = Math.ceil(len / record_i);
       if(!record_view_ul.is(":animated")){
            if(record_page===1){
                record_view_ul.animate({ left : '-='+record_width*(page_count-1) }, "slow");
                record_page = page_count;  
            }else{  
                record_view_ul.animate({ left : '+='+record_width }, "slow");  //通过改变left值，达到每次换一个版面  
                record_page--;  
            } 
       }
    });
});

$(document).ready(function(){
    $(".update[edit-name]").die('click').live("click",function(){
        var _this = $(this);
        var parent = $(_this).parent();
        //字段名
        var field = $(_this).attr("edit-name");
        //字段的span
        var field_span = $(".black[data-name='"+field+"']");
        var ori = $(field_span).html();
        var span_ori = $(_this).html();
        //插入input元素
        if(field == 'sex'){
            var input = $("<input name='"+field+"' type='radio' value='1' data-ori='"+ori+"' data-value='男' />男<label class='radio-blank'></label><input name='"+field+"' type='radio' value='0' data-value='女' />女<label class='radio-blank'></label><input name='"+field+"'  type='radio' value='2' data-value='保密' />保密<label class='radio-blank'></label>");
        } else {
            var input = $("<input name='"+field+"' class='profile-item' data-ori='"+ori+"' type='text' />");
            $(input).val(ori);
        }
        $(field_span).empty();
        $(input).appendTo(field_span);
        //保存，取消按钮
        var save = $("<label class='opt-btn save'>保存</label>");
        var cancel = $("<label class='opt-btn cancel'>取消</label>");       
        $(_this).remove();
        $(save).appendTo(parent);
        $(cancel).appendTo(parent);       
    });
    //保存，取消按钮的效果
    $(".opt-btn").die("click").live("click",function(){
        var _this = $(this);
        var parent = $(_this).parent();
        //包含输入框的span
        var span = $(parent).find("span[data-name]");
        //字段名
        var field = $(span).attr("data-name");
        var input = $(parent).find("span[data-name] input");
        //输入框的原始值
        var ori = $(input).attr("data-ori");
        //用户输入的值
        var input_value = $(input).val();
        //保存完成之后显示在span里的内容
        var show_value = false;
        var update_span = $("<span class='update' edit-name='"+field+"'>修改</span>");
        //修改，取消按钮
        var buttons = $(parent).find(".opt-btn");
        var hide_buttons = true;
        if($(_this).hasClass("save")){
            //是否提交修改请求
            var go = true;
            //验证输入内容
            if($("#my_profile").validate().element(input)){
                if($(input).attr('type') == 'radio'){
                    input_value = $(input+":checked").val();
                    show_value = $(input+":checked").attr("data-value");
                }
                if(field == 'email'){
                    //邮箱验证
                    $("#proving_email").val(input_value);
                    show_email_proving();
                    go = false;
                    hide_buttons = false;
                }
                if(field == 'bindname'){
                    //会员帐户验证
                    $("#proving_bindname").val(input_value);
                    show_account_proving();
                    go = false;
                    hide_buttons = false;
                }
                if(field == 'mobile_phone'){
                    $("#proving_mobile").val(input_value);
                    show_mobile_proving();
                    go = false;
                    hide_buttons = false;
                }
                if(go){
                    $.ajax({
                        type:'post',
                        url:'user.php?act=ajax_submit_user_field',
                        dataType:'json',
                        data:{field:field,value:input_value},
                        async:false,
                        success:function(data){
                            if(data.error == ''){
                                return;
                            } else {
                                hide_buttons = false;
                            }
                        }
                    });
                }
            } else {
                hide_buttons = false;
            }
        }
        if($(_this).hasClass("cancel")){
            input_value = ori;
        }
        if(hide_buttons){
            $(update_span).appendTo(parent);
            $(buttons).remove();
            $(span).empty();
            if(show_value){
                $(span).html(show_value);
            } else {
                $(span).html(input_value);
            }
        }
    });
    
    //收起按钮
    $(".shaidan-close").live("click",function(){
        $(this).closest(".my-discuss").css("display","none");
    });
    
    //晒单图片删除按钮
    $(".shaidan_img_div").live('hover',function(event){
        var rsp = $(this).find(".rsp");
        var operate = $(this).find(".operate");
        if(event.type == 'mouseenter'){
            $(rsp).show();
            $(operate).show();
        }else {
            $(rsp).hide();
            $(operate).hide();
        }
    });
    
    $(".shaidan_img_div .operate").die().live("click",function(event){
        var parent = $(this).parent();
        var img_id = $(parent).attr("data-id");
        $.ajax({
            url:"user.php?act=delete_shaidan_img&img_id="+img_id,
            type:'get',
            success:function(data){
                if(data != 'false'){
                   $(parent).remove();
                }
            }
        });
        return false;
    });
    //
});

var query_email = false;
/*查询邮箱验证状态*/
function query_email_validate(edit_password){
    if(!query_email){
        return false;
    }
    //每隔3秒查询一次
    setTimeout(function(){
        $.ajax({
        type:"POST",
        url:"user.php?act=query_email_validate",
        timeout:80000,
        dataType:"json",
        success:function (data){
            if(data.error == '1'){
                query_email = false;
                if(edit_password == 'undefined' || typeof(edit_password) == 'undefined'){
                    success_email_proving();
                    var data_span = $("span[data-name='email']").parent().find(".green").length;
                    if(data_span == 0){
                        if(data.give_points){
                            $("span[data-name='email']").parent().append($("<span class='green'>（"+data.give_points+"）</span>"));
                        }
                    }
                } else {
                    //修改密码绑定的邮箱验证成功
                    password_email_proving();
                }
            } else {
                query_email_validate(edit_password);
            }
        }
    });
    },3000);
}

//显示邮件验证界面
function show_email_proving(){
    fullbgShow();
    $(".proving-ing,.proving-ok").hide();                    
    $("#proving-bg,#proving-mail,.proving-not").show();
}
//邮件验证成功
function success_email_proving(){
    $("#proving-mail .proving-ing").hide();         
    $("#proving-mail .proving-ok").show(); 
    var email = $("#proving_ing_email").html();
    $("input[name=email]").attr("data-ori", email);  
    $("input[name=email]").parent().parent().find(".red_yanzheng").hide();
    $("input[name=email]").parent().parent().find(".opt-btn").filter(".cancel").click();
    
}

function password_email_proving(){
    $("#proving-mail .proving-ing").hide();   
    $("#fullbg,#proving-bg,.proving-box").hide();
    $(".m-right-box").show();
}
//显示帐号验证界面
function show_account_proving(){
    fullbgShow();
    $(".proving-ing,.proving-ok").hide(); 
    $("#proving_user_form .bindname-error-tips").hide();
    $("#proving-bg,#proving-user,.proving-not").show();
}
//隐藏帐号验证界面
function success_account_proving(){
    $(".proving-ing,.proving-ok,.proving-not").hide(); 
    $("#proving-user .proving-ok").show(); 
}

function show_mobile_proving(){
    fullbgShow();
    $(".proving-ing,.proving-ok").hide(); 
    $("#proving-bg,#proving-mobile,.proving-not").show();
}

function success_mobile_proving(){
    $(".proving-ing,.proving-ok,.proving-not").hide(); 
    $("#proving-mobile .proving-ok").show(); 
    var mobile = $("input[name=mobile_phone]");
    var input_span =  $("span[data-name='mobile_phone']");
    $(input_span).parent().find(".red_yanzheng").hide();
    $(mobile).attr('data-ori',$(mobile).val());
    $(input_span).parent().find(".opt-btn").filter(".cancel").click();
    
}

/*晒单相关*/
function submit_comment(id){
    var order_id = id;
    var row = $("#shaidan_form_"+id+" div[name='shaidan_row']");
    var submit = true;
    var error_str = '';
    var send = {};
    $.each(row,function(key,value){
        var val = {};
        var goods_id = $(this).attr("id");
        var goods_rank = $(this).find(".shaidan .rank_star a[class='selected']").attr("data-value");
        if(typeof(goods_rank) == 'undefined'){
            goods_rank = 5;
        }
        var msg_content = $(this).find(".shaidan textarea[name='shaidan_content']").val();
        var token = $(this).find(".shaidan_img").attr("token");
        var msg_title = $(this).find(".shaidan input[name='shaidan_title']").val();
        var imgs = $(this).find(".shaidan_img img");
        if(msg_content == ''){
            error_str = '请填写晒单内容。';
            submit = false;
        }
        if($(imgs).length > 0 ){
            if(msg_title == ''){
                error_str = '请填写晒单标题。';
                submit = false;
            }
        }
        val['content'] = msg_content;
        val['token'] = token;
        val['title'] = msg_title;
        val['goods_rank'] = goods_rank;
        send[goods_id] = val;
    }); 
    if(submit){        
        $.ajax({
            type:'post',
            dataType:'json',
            data:{shaidan_data:send},
            url:'user.php?act=submit_shaidan_order&order_id='+order_id,
            success:function(data){
                if(data.error == ''){
                    alert("您的晒单已成功提交，待管理员审核后，就能显示在相应的产品页。");
                    get_order_data($("#order_page").val());
                }else {
                    alert("晒单失败。");
                    return false;
                }
            }
        });
    }else {
        alert(error_str);
        return submit;
    }
}

function submit_edit_shaidan(id){
    var msg_id = id;
    var msg_rank = $("#shaidan_form_"+id+" .rank_star a[class='selected']").attr("data-value");
    var msg_title = $("#shaidan_form_"+id+" input[name='shaidan_title']").val();
    var msg_content = $("#shaidan_form_"+id+" textarea[name='shaidan_content']").val();
    var imgs = $("#shaidan_form_"+id+" .shaidan_img img");
    var submit = true;
    if($(imgs).length > 0 ){
        if(msg_title == ''){
            alert("晒单标题不能为空。");
            submit = false;
        }
    }
    if(msg_content == ''){
        alert("晒单内容不能为空。");
        submit = false;
    }
    if(submit){
        $.post("user.php?act=ajax_submit_edit_shaidan",{msg_id:msg_id,msg_title:msg_title,msg_content:msg_content,msg_rank:msg_rank},function(data){
            if(data.error == ''){
                alert("您的晒单已经成功更新，请等待管理员审核。");
                get_comment_data($("#comment_page").val());
            } else {
                alert("修改晒单失败。");
                return false;
            }                     
        },'json');
    }
}
/*获取评论页面*/
function reply_shaidan(comment_id){
    //获取晒单内容
    $.getJSON('user.php?act=ajax_comment_detail',{comment_id:comment_id,is_reply:1},function(data){      
        $("#user_center_top").empty();
        $("#user_center_top").prepend(data);
        $("#comment_table_"+comment_id+" .my-discuss").show();
    });
}
/*提交回复*/
function submit_reply(comment_id){
    var content = $("#reply_comment_"+comment_id).val();
    if($.trim(content) == ''){
        alert('回复内容不能为空。');
        return false;
    }
    $.post('user.php?act=submit_reply_comment',{comment_id:comment_id,reply:content},function(data){
        if(data.error == ''){
            alert("您的回复已成功提交，等待管理员审核通过后就可以在该评论中显示。");
            $("#user_center_top .my-coment-table").hide();
        } else if(data.error == 1){
            alert('回复内容为空。');
        }
    },'json');
}

/*查看更多晒单*/
function more_shaidan(obj){
    var share_box = $(".share-box:gt(2)");
    if($(obj).hasClass("close_more")){
        $(share_box).hide();
        $(obj).removeClass("close_more");
        $(obj).html("查看更多");
    } else {
        $(share_box).show();
        $(obj).addClass("close_more");
        $(obj).html("收起更多");
    }
}
