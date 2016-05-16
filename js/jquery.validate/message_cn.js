jQuery.extend(jQuery.validator.messages, {
    required: "不能为空",
    remote: "请修正该字段",
    email: "请输入正确格式的电子邮件",
    url: "请输入合法的网址",
    date: "请输入合法的日期",
    dateISO: "请输入合法的日期 (ISO).",
    number: "请输入合法的数字",
    digits: "只能输入整数",
    creditcard: "请输入合法的信用卡号",
    equalTo: "请再次输入相同的值",
    accept: "请输入拥有合法后缀名的字符串",
    maxlength: jQuery.validator.format("请输入一个 长度最多是 {0} 的字符串"),
    minlength: jQuery.validator.format("请输入一个 长度最少是 {0} 的字符串"),
    rangelength: jQuery.validator.format("请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
    range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
    max: jQuery.validator.format("请输入一个最大为{0} 的值"),
    min: jQuery.validator.format("请输入一个最小为{0} 的值")
});

jQuery.validator.addMethod("mobile_phone", function(value, element) {
    var patrn = /^(13[0-9]|15[0-9]|18[0-9]|147|17[0-9])\d{8}$/;
    if (!patrn.exec(value)) {
        return false;
    }
    return true;
},"请填写正确的手机号码");

jQuery.validator.addMethod("is_registered", function(value, element,param) {
    var submit = false;
    $.ajax({
        url:param,
        type:"post",
        async:false,
        data:{username:value},
        success:function(data){
            if(data == 'true'){
                submit =  false;
            } else {
                submit =  true;
            }
        }
    });
    return submit;
},"帐号名已被注册");
jQuery.validator.addMethod("email_registered", function(value, element,param) {
    var submit = false;
    $.ajax({
        url:param,
        type:"post",
        async:false,
        data:{email:value},
        success:function(data){
            if(data == 'false'){
                submit = false;
            } else {
                submit = true;
            }
        }
    });
    return submit;
},"邮箱已被绑定");
jQuery.validator.addMethod("mobile_registered", function(value, element,param) {
    var submit = false;
    $.ajax({
        url:param,
        type:"post",
        async:false,
        data:{mobile_phone:value},
        success:function(data){
            if(data == 'false' || data == '手机号已被绑定'){
                submit = false;
            } else {
                submit = true;
            }
        }
    });
    return submit;
},"手机号码已被绑定");

jQuery.validator.addMethod("isZipCode", function(value, element) {   
    var tel = /^[0-9]{6}$/;
    return this.optional(element) || (tel.test(value));
}, "请正确填写您的邮政编码");

jQuery.validator.addMethod("select_one", function(value, element) {   
    if(typeof(value) != 'undefined' && value !='undefined'){
        return true;
    } else {
        return false;
    }
}, "请选中一个选项");

jQuery.validator.addMethod("user_exist", function(value, element,param) {
    var submit = false;
    $.ajax({
        url:param,
        type:"post",
        async:false,
        data:{username:value},
        success:function(data){
            if(data == 'true'){
                submit =  true;
            } else {
                submit =  false;
            }
        }
    });
    return submit;
},"帐号不存在");
/*设置表单验证默认规则*/
$.validator.setDefaults({
        onkeyup: false,
        errorClass:'input-tips-error',
        errorPlacement:function(error, element){
             error.appendTo(element.parent());
        },
        rules:{
            username:{
                required:true,
                rangelength:[3,20],
                is_registered:"user.php?act=is_registed"
            },
            nickname:{
                minlength:3
            },
            bindname:{
                required:true,
                rangelength:[3,20],
                remote: {
                    url:"user.php?act=check_bindname",
                    type:"post",
                    dataType:"text",
                    async:false
                }
            },
            sex:{
                select_one:true
            },
            mobile_phone:{
                required:true,
                mobile_phone:true,
                mobile_registered:"user.php?act=check_mobile"
            },
            email:{
                required:true,
                email:true,
                email_registered:"user.php?act=check_email"
            },
            password:{
                required: true,
                minlength: 6
            },
            oldpassword:{
                required: true,
                minlength: 6
            },
            //注册时再次输入密码
            re_password: {
                required: true,
                minlength: 6,
                equalTo: "#register_password"
            },
            //邮件找回密码时再次输入的密码
            re_password_email:{
                required: true,
                minlength: 6,
                equalTo: "#re_password_email"
            },
            captcha:{
                required:true,
                remote: "captcha.php?captcha_type=check_code"
            },
            smscode:{   
                remote: {
                    url:"user.php?act=check_sms_code",
                    type:"post",
                    dataType:"text",
                    data:{
                        register_mobile:function(){
                            return $("#register_username").val();
                        },
                        register_sms_code:function(){
                            return $("#register_smscode").val();
                        }
                    }
                }               
            },
            user_exist:{
                required:true,
                user_exist:"user.php?act=is_registed"
            }
        },
        messages:{
            nickname:"请填写长度最少为3位的昵称",
            oldpassword:"请填写长度最少为6位的密码",
            password:"请填写长度最少为6位的密码",
            re_password:"两次填写的密码不一致",
            captcha:"验证码不正确",
            smscode:"手机验证码不正确",
            bindname:"不存在该用户"
        }
    });