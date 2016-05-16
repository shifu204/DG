<div class="user_form900 relative clearfix" id='user_form900'>
    <div class="common_form clearfix">
        <div class="form_title clearfix" style="display: none;">
            <ul class="title_tabs">             
                <li class="<?php if ($this->_var['default_page'] == 'register'): ?>hover<?php endif; ?>"><div data-name="icons-register" class="icons icons-register <?php if ($this->_var['default_page'] == 'register'): ?>icons-register-hover<?php endif; ?>"></div>注册</li>
                <li class="<?php if ($this->_var['default_page'] == 'login'): ?>hover<?php endif; ?>"><div data-name="icons-login" class="icons icons-login <?php if ($this->_var['default_page'] == 'login'): ?>icons-login-hover<?php endif; ?>"></div>登陆</li>
            </ul>
        </div>
        <div class="form_content register-content" <?php if ($this->_var['default_page'] != 'register'): ?> style="display: none;" <?php endif; ?>>
            <form id="register_form" autocomplete="off" >
                <div class="right-bg">
                    <div class="form-top">&nbsp;&nbsp;会员注册</div>
                    <table class="register-table">
                        <tbody>
                            <tr>
                                <th>用户名：</th>
                                <td>
                                    <div class="input-wrapper">
                                        <input id="register_username" type="text" name="username" class="myinput" value="请填用户名" />
<!--                                        <label class="input-background input-user-name"></label>-->
                                        <img src="<?php echo $this->_var['theme_path']; ?>images1/register_login/input-user.jpg" class="input-background">
                                        <label class="input-tips">3-20位字符，支持汉字、字母、数字</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>手机号：</th>
                                <td>
                                    <div class="input-wrapper">
                                        <input id="register_mobile" type="text" name="mobile_phone" class="myinput" value="请填入手机号" maxlength="11"/>
                                        <label></label>
                                        <label class="input-tips">您可以用该手机号登录和找回密码</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>请设置密码：</th>
                                <td>
                                    <div class="input-wrapper">
                                        <input type="text"  id="register_password" name="password" class="myinput" value="请填写密码" />
                                        <img src="<?php echo $this->_var['theme_path']; ?>images1/register_login/input-pwd.jpg" class="input-background">
                                        <label class="input-tips" style="width: 277px;">6-20位字符，建议由字母，数字和符号两种以上组合</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>请确认密码：</th>
                                <td>
                                    <div class="input-wrapper">
                                        <input type="text" name="re_password" class="myinput" value="请再次填写密码" />
                                        <img src="<?php echo $this->_var['theme_path']; ?>images1/register_login/input-pwd.jpg" class="input-background">
                                        <label class="input-tips">请再次输入密码</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>验证码：</th>
                                <td>
                                    <div class="input-wrapper">
                                        <input type="text" name="captcha" class="myinput" style="width: 56px" value="" />
                                        &nbsp;<img  title="点击刷新" src="captcha.php?captcha_type=get_img" align="absbottom" onclick="this.src='captcha.php?captcha_type=get_img&time='+Math.random();" style="width: 88px; height: 32px;" name="register"></img>
                                        &nbsp;<span style="color: #595959;">看不清？</span><span style="color: #2e4cb7; cursor: pointer;" onclick="register.src='captcha.php?captcha_type=get_img&time='+Math.random();">换一张</span>
                                    </div>
                                </td>
                            </tr>
                            <!--<tr>
                                <th>短信验证码：</th>
                                <td>
                                    <input type="text" id="register_smscode" name="smscode" class="myinput" style="width: 116px" />
                                    <input type="button" class="buttons" value="获取验证码" style="width: 116px;" onclick="get_sms_code(this);"/>
                                </td>
                            </tr>-->
                            <!--<tr>
                                <th></th>
                                <td>
                                    <input type="checkbox" name="read_notice" class="checkbox"/>阅读并同意“德贝用户协议”
                                </td>
                            </tr>-->
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 5px 0px;">
                                    <input id="deebei_register_btn" class="common_form_btn" type="submit" value=""/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="other_login">
                                    合作商户登陆德贝：
                                    <a class="icons" href="/api/qq/deebei/oauth/" target="_blank" >
                                        <img src="<?php echo $this->_var['theme_path']; ?>/images1/register_login/icons-qq.jpg" />
                                    </a>
                                    <a class="icons" href="<?php echo $this->_var['wx_login_url']; ?>" target="_blank" >
                                        <img src="<?php echo $this->_var['theme_path']; ?>/images1/register_login/icons-wx.jpg" />
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </form>
        </div>
        <div class="form_content login-content" <?php if ($this->_var['default_page'] != 'login'): ?> style="display: none;" <?php endif; ?>>
            <form id="login_form" method="post" autocomplete="off" >
                <div class="right-bg">
                    <div class="form-top">
                        <div class="form-top-login">&nbsp;&nbsp;会员登录</div>
                        <div class="form-top-go-register"><a href="javaScript:goto_register()" class="go-register" >快速注册》</a></div>
                    </div>
                    <table class="login-table">
                        <tbody>
                            <tr>
                                <th>账户：</th>
                                <td>
                                    <div class="input-wrapper">
                                        <input type="text" name="username" class="myinput" value="请填写用户名、邮箱、手机号" />
                                        <!--<label class="input-background input-user-name"></label>-->
                                        <img src="<?php echo $this->_var['theme_path']; ?>images1/register_login/input-user.jpg" class="input-background">
                                        <div class='error'></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>密码：</th>
                                <td>
                                    <div class="input-wrapper">
                                        <input type="text" name="password" class="myinput" value="请填写密码" />
                                        <img src="<?php echo $this->_var['theme_path']; ?>images1/register_login/input-pwd.jpg" class="input-background">
                                        <!--<label class="input-background input-password"></label>-->
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td>
                                    <input type="checkbox" name="remember_me" class="checkbox"/>记住我（15天内免登录）
                                    <a href="user.php?act=email_password" target="_blank"><span class="forget-pwd">忘记密码？</span></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 18px 0px;">
                                    <div id="direct_shopping" style="display: none;">
                                        <input class="common_form_btn" type="submit" value="" />
                                        <a href="flow.php?step=checkout&direct_shopping=1" class="common_form_btn direct-buy-btn"></a>
                                    </div>
                                    <div id="common_shopping">
                                        <input class="common_form_btn" type="submit" value="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="other_login">
                                    <div class="bottom-border">
                                        合作商户登陆德贝：
                                        <a class="icons" href="/api/qq/deebei/oauth/" target="_blank"  
                                        <?php if(isset($this->_var['back_act']) && !empty($this->_var['back_act'])):?>
                                            onclick="query_login(login_go_to_cart)"
                                        <?php else:?>
                                            onclick="query_login()"
                                        <?php endif;?>
                                        >
                                            <img src="<?php echo $this->_var['theme_path']; ?>/images1/register_login/icons-qq.jpg" />
                                        </a>
                                        <a class="icons" href="<?php echo $this->_var['wx_login_url']; ?>" target="_blank" >
                                            <img src="<?php echo $this->_var['theme_path']; ?>/images1/register_login/icons-wx.jpg" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </form>
        </div>
    </div>
    <div id="register_success" class="clearfix">
        <div class="success-msg">
            恭喜，<span id="register_success_username"></span>已注册成功！
        </div>
        <div class="success-btn">
            <a class="go_to_shop_btn" href="/" style="margin-right: 16px;"></a>
            <a class="go_to_user_clips_btn" href="user.php"></a>
        </div>
    </div>
    <div id='close_form' class='absolute icons icons-close'></div>
</div>
<script type="text/javascript">
    $(".title_tabs li").click(function(){
        $("#register_success").hide();
        var lis = $(".title_tabs li");
        var divs = $(".title_tabs li div");
        var selected_div  = $(this).find("div");
        var form_content = $(".common_form .form_content");
        var position = $(this).prevAll().length;
        $(lis).removeClass("hover");
        $(this).addClass("hover");
        $.each(divs,function(){
            var hover = $(this).attr("data-name");
            $(this).removeClass(hover+"-hover");
        });
        $(selected_div).addClass($(selected_div).attr("data-name")+"-hover");
        $(form_content).hide();
        $(form_content).eq(position).show();
        if($(form_content).eq(position).find("[id=login_form]").length > 0){
            if(need_query_login == false){
                need_query_login = true;
                query_login(user_is_login);
            }
        }
    });
    
    $("#close_form").click(function(){
        var d = dialog.get("user_login_dialog");
        d.close().remove();
    });
   
$(document).ready(function(){
    <?php if ($this->_var['default_page'] == 'login'): ?>
        if(need_query_login == false){
            need_query_login = true;
            query_login(user_is_login);
        }
    <?php endif; ?>
    $("#register_form").resetForm();
//    $("#register_form td").find("input[class='myinput']").focus(function(){
//        var parent = $(this).parent();
//        var tips = $(parent).find("label[class='input-tips']");
//        var err_tips = $(parent).find("label[class='input-tips-error']");
//        if(!$(err_tips).is(":visible")){
//            $(tips).css("display","block");
//        }
//    });
//    $("#register_form td").find("input[class='myinput']").blur(function(){
//        var parent = $(this).parent();
//        var tips = $(parent).find("label[class='input-tips']");
//        $(tips).hide();
//    });
    
    $("#register_form td,#login_form td").find("input[class='myinput']").bind({
        focus:function(){
            if(this.value == this.defaultValue){
                if(this.defaultValue == "请填写密码" || this.defaultValue == "请再次填写密码" ){
                    this.value="";
                    this.type="password";
                }else{
                    this.value="";
                }
            
            }

        },
        blur:function(){
            
            var parent = $(this).parent();
            var err_tips = $(parent).find("label[class='input-tips-error']");
//            var tips = $(parent).find("label[class='input-tips']");
            if(!$(err_tips).is(":visible")){
                if(this.value == ""){
                    if(this.defaultValue == "请填写密码" || this.defaultValue == "请再次填写密码" ){
                        this.type="text";
                        this.value = this.defaultValue;
                    }else{
                        this.value = this.defaultValue;
                    }
                }
            }
        }
    });
    
    $("#register_form").validate({       
        submitHandler:function(form){
            $(form).ajaxSubmit({
                url:"user.php?act=act_register_ajax",
                type:"post",
                dataType:'json', 
                success:function(data){
                    if(data.error == '0'){                      
                        $(".register-content").hide();
                        $("#register_success").show();
                        $("#user_login_div").html(data.content);
                        $("#register_success_username").html(data.user_info.username);
                        $("#register_form").resetForm();
                        //统计地址
                        $("#register_form").append("<iframe style='display:none' src='statistics.php?act=register_success'></iframe>");
                        if(data.script){
                            eval(data.script);
                        }
                    } else {
                        alert(data.msg);
                    }
                }
            });
        }      
    });
    
    $('#login_form').submit(function() {
        $(this).ajaxSubmit({
            url:"user.php?act=signin",
            type:"post",
            dataType:'json',
            async:false,
            success:function(data){
                if(data.error == 0){  
                    <?php if ($this->_var['back_act'] == 'cart'): ?>
                        login_go_to_cart();
                        return;
                    <?php endif; ?>
                    var d = dialog.get("user_login_dialog");
                    d.close().remove();
                    $("#user_login_div").html(data.content);
                } else {
                    $("#login_form div[class='error']").html(data.content);
                }
            }
        });
        return false;
    });    
});  

function get_sms_code(obj){   
    $(obj).focus();
    if($("#register_form").validate().element($("#register_username"))){
        $.ajax({
            type:"post",
            url:"/api/sms.php?act=register_code",
            dataType:"json",
            data:{mobile:$("#register_username").val()},
            success:function(data){
                if(data.result == '0'){
                    $(obj).attr("count",120);
                    sms_count_down(obj);
                } else {
                    $(obj).val("获取验证码失败");
                }
            }
        });
    } 
}
function sms_count_down(obj){
    $(obj).attr('disabled',true); 
    $(obj).addClass("disabled");
    var count = setInterval(function(){
        var time = $(obj).attr("count");
        if(parseInt(time) > 0){
            var str = (parseInt(time)-1)+"秒后再获取验证码";
            $(obj).val(str);
            $(obj).attr("count",parseInt(time) - 1);
        } else {
            clearInterval(count);
            $(obj).val("重新获取验证码");
            $(obj).removeAttr("disabled");
        }
    },1000);
}
function goto_register(){
    $(".user_form900").find(".login-content").css('display','none');
    $(".user_form900").find(".register-content").css('display','block');
}
</script>