//是否进行微信支付状态长轮询
var query_weixin = false;
var query_order = false;
function weixin_pay(order_sn,total_fee){
    //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
    var content = '';
    $.ajax({
        type:"post",
        data:{order_sn:order_sn},
        timeout:80000,
        url:"user.php?act=get_weixin_pay_href&get_page=1",
        async:false,
        success:function(data){
            content = data;
        }
    });
    
    var d = dialog({
        id:"wx_pay_dialog",
        content:content,
        width:700,
        heigth:400,
        onclose:function(){
            query_weixin = false;
            hideMask();
        },
        onshow:function(){
            showMask();
        }
    });
    d.show();
    query_weixin = true;
    query_weixin_pay(order_sn);
}

function query_weixin_pay(order_sn){
    if(!query_weixin){
        return false;
    }
    //每隔两秒查询一次订单是否已完成
    setTimeout(function(){
        $.ajax({
        type:"POST",
        url:"user.php?act=query_order_pay",
        timeout:80000,
        data:{order_sn:order_sn},
        success:function (data){
            if(data == '1'){
                query_weixin = false;              
                get_order_data($("#order_page").val());  
                dialog.get('wx_pay_dialog').close().remove();
            } else {
                query_weixin_pay(order_sn);
            }
        }
    });
    },2000);
    
}

function query_order_pay(order_sn){
    if(!query_order){
        return false;
    }
    //每隔两秒查询一次订单是否已完成
    setTimeout(function(){
        $.ajax({
        type:"POST",
        url:"user.php?act=query_order_pay",
        timeout:80000,
        data:{order_sn:order_sn},
        success:function (data){
            if(data == '1'){
                query_order = false;              
                get_order_data($("#order_page").val());
            } else {
                query_order_pay(order_sn);
            }
        }
    });
    },2000);
}


