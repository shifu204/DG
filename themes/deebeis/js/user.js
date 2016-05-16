var is_login = false;
var need_query_login = false;
var refresh = new Array("flow.php","user.php");

function open_dialog(id,topfix){
    var dialog = $("#"+id);
    var document_width = $(document).width();
    var ele_width = dialog.width();
    dialog.css("left",document_width/2 - ele_width/2);
    if(typeof(topfix) != 'undefined'){
        if(!isNaN(topfix)){
            var top = ($(window).height() - dialog.height())/2 + topfix;   
        } else {
            var top = ($(window).height() - dialog.height())/2;   
        }
        dialog.css("top", top);
    }
    showMask();
    dialog.show(); 
}

function showMask(){  
    var mask = $(".maskDiv");
    if(mask.length == 0){
        mask = $("<div class='maskDiv'>&nbsp;</div>");
        $(mask).appendTo("body");
    }
    $("body").css("overflow","hidden");
    $(mask).css("height",$(document).height());  
    $(mask).css("width",$(document).width());  
    $(mask).show();
}

function hideMask(){
    var mask = $(".maskDiv");
    $(mask).hide();
    $("body").css("overflow","auto");
}

function close_form(form_id){
    hideMask();
    $("#"+form_id).find("input").val("");
    $("#"+form_id).hide();
    hide_tips(form_id);
}

function user_login_dialog(param,direct_shopping){
    if(typeof(param) == 'undefined'){
        param = 'login'; 
    }
    /*是否显示直接购买按钮*/
    if(typeof(direct_shopping) == 'undefined'){
        direct_shopping = false;
    }
    var user_form = $("#user_form900");
    //防止打开多个登录注册窗口
    if($(user_form).length == 0){
        var content = '';
        $.ajax({
            type:"get",
            url:"user.php?act=get_login_register_page&default_page="+param,
            async:false,
            success:function(data){
                content = data;
            }
        });
        var d = dialog({
            id:"user_login_dialog",
            width:934,
            content:content,
            onclose:function(){
                hideMask();
            },
            onshow:function(){
                showMask();
            }
        });
        d.show();
        if(direct_shopping){
            $("#common_shopping").hide();
            $("#direct_shopping").show();
        }
    }
}

/*微信登录扫描框*/
function wx_login(){
    var content = $('#wx_login_frame');
    var d = dialog({
        id:"wx_login",
        content:content,
        onclose:function(){
            hideMask();
        },
        onshow:function(){
            showMask();
        },
        button: [
                 {
                     value: '关闭'
                 }
             ]
    });
    d.show();
}

/*查询用户登录状态*/
function query_login(callback){
    if(need_query_login){
        setTimeout(function(){
            $.getJSON("user.php?act=query_login&_t="+new Date().getTime(), function(data){
                if(data.result == 1){
                    need_query_login = false;
                    if(typeof(data.from) != 'undefined' && data.from != 'undefined'){
                        $("body").append("<iframe style='display:none' src='statistics.php?act="+data.from+"'></iframe>");
                    }
                    callback(data);
                } else {
                    query_login(callback);
                }
            });
        },2000);
    }
}

/*用户已登录回调函数*/
function user_is_login(data){
    $("#user_login_div").empty();
    $("#user_login_div").html(data.content);
    //关闭登录框
    var d = dialog.get("user_login_dialog");
    d.close().remove();    
}

/*用户登录之后跳转到购物车*/
function login_go_to_cart(){
    window.location.href="flow.php?step=cart";
}
