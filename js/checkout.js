$(document).ready(function(){    
    ship_method_click();
    pay_method_click();
});
var selectedShipping = null;
var selectedPayment  = null;
var selectedPack     = null;
var selectedCard     = null;
var selectedSurplus  = '';
var selectedBonus    = 0;
var selectedIntegral = 0;
var selectedOOS      = null;
var alertedSurplus   = false;

var groupBuyShipping = null;
var groupBuyPayment  = null;

/*获取当前选中的收货人id*/
function get_address_id(){
    var selected = $("#consignee .selected_row[address-id]");
    if($(selected).length == 0){
        return false;    
    } else {
        return $(selected).attr("address-id");
    }
}

/*获取地区信息*/
function get_region(id){
    var select = $("#"+id);
    var data_type = $(select).attr("data-type");
    var parent_ele = $(select).parent();
    var type = 0;
    var parent = 0;
    if(data_type == "province"){
        parent = 1;
        type = 1;
        select.change(function(){
            province_change($(select));
        });
    } else if(data_type == "city"){
        type = 2;
        parent = $(parent_ele).find("select[data-type='province']").val();
        select.change(function(){
            city_change($(select));
        });
    } else if(data_type == "district"){
        type = 3;
        parent = $(parent_ele).find("select[data-type='city']").val();
    }
    $.getJSON("region.php?type="+type+"&parent="+parent,function(data){
        empty_select(id);
        $.each(data.regions,function(i,j){            
            var option = $("<option value='"+j.region_id+"'>"+j.region_name+"</option>");
            select.append(option);
        });
    });  
}

function province_change(obj){
    var parent_ele = $(obj).parent();
    var city = $(parent_ele).find("select[data-type='city']");
    var district = $(parent_ele).find("select[data-type='district']");
    get_region($(city).attr("id"));
    empty_select($(district).attr("id"));
}

function city_change(obj){
    var parent_ele = $(obj).parent();
    var district = $(parent_ele).find("select[data-type='district']");
    get_region($(district).attr("id"));
}

function empty_select(id){
   var select = $("#"+id);
   select.empty();
   select.append("<option value='0'></option>");
}

function insert_info_form(obj){
    if($(obj).find(".info_form").length == 0){
        var form = $("#consignee_info").html();
        $(form).appendTo($(obj));
    }
}


function show_consignee_saveBtn(){
    var btn = $("#consignee").find(".save_consignee_btn");
    $(btn).show();
}

function hide_consignee_saveBtn(){
    var btn = $("#consignee").find(".save_consignee_btn");
    $(btn).hide();
}

/*收货人信息点击效果*/
function address_row_click(){
    var address_rows = $("#consignee").find(".address_row");
    var edit_spans = $(address_rows).find(".edit_span > .edit");
    var delete_spans = $(address_rows).find(".edit_span > .delete");
    var set_default = $(address_rows).find(".edit_span > .set-default");
    var radios = $(address_rows).find("input[type='radio']");
    var info_form = $("#consignee_info");    
    $.each(address_rows,function(){
        $(this).bind("click",function(){
            show_address_row($(this));
            if($(this).attr("id") == "add_address"){
                show_consignee_saveBtn();
                var form = $(this).nextAll(".info_form");
                if($(form).length == 0){
                    var form = $("#consignee_info").html();
                    $(form).appendTo($(this).parent()); 
                    var form = $(this).parent().find(".info_form").last();
                    $(form).attr("id","new_address");
                    var selects = $(form).find("select");
                    $.each(selects,function(a,b){
                        var data_type = $(b).attr("data-type");
                        var ele_id = "new_address_"+data_type;
                        $(b).attr("id",ele_id);
                        get_region(ele_id);
                    });  
                } else {
                    $(form).show();
                }              
            }           
            getShipping();
        });
    });
    
    $.each(edit_spans,function(){
        $(this).bind("click",function(){     
            show_consignee_saveBtn();
            var parent = $(this).parents(".address_row");
            insert_info_form(parent);
            var address_id = $(parent).attr("address-id");
            var my_form = $(parent).find(".info_form");
            var inputs = $(my_form).find("input");
            var selects = $(my_form).find("select");
            var eles = $.merge(inputs,selects);
            $.each(eles,function(i,j){
                var ele_name = $(j).attr("name");
                var o_name = "o_"+ele_name;
                var o = $(parent).find("input[name='"+o_name+"']").val();
                if(ele_name == "address_id"){
                    $(j).val(address_id);
                } else if(ele_name == "province" || ele_name == "city" || ele_name =="district"){
                    var type = 0;
                    var area_parent = 0;
                    var this_id = address_id+"_"+ele_name;
                    $(j).attr("id",this_id);
                    switch (ele_name){
                        case "province":
                            type = 1;
                            area_parent = 1;
                            $(j).bind("change",function(){
                                province_change($(j));
                            });
                            break;
                        case "city":
                            type = 2;
                            area_parent = $(parent).find("input[name='o_province']").val();
                            $(j).bind("change",function(){
                                city_change($(j));
                            });
                            break;
                        case "district":
                            type = 3;
                            area_parent = $(parent).find("input[name='o_city']").val();
                    }
                    $.getJSON("region.php?type="+type+"&parent="+area_parent,function(data){
                        $.each(data.regions,function(a,b){
                            var selected = '';
                            if(b.region_id == o){
                                selected = "selected='selected'"
                            }
                            var option = "<option value='"+b.region_id+"' "+selected+">"+b.region_name+"</option>";
                            $(j).append(option);
                        });
                    });
                } else {
                    $(j).val(o);    
                }
                
            });
        });
    });
    $.each(delete_spans,function(dk,dv){      
       $(this).bind("click",function(e){
           e.stopPropagation();
           var parent = $(this).parents(".address_row");
           $.getJSON("flow.php?act=delete_address&address_id="+$(parent).attr("address-id"),function(data){
               if(data.error == ''){
                   $("#step-1").empty();
                   $("#step-1").append(data.addresses);
                   getShipping();
               }else {
                   return ;
               }
           });
       });
    });
    $.each(set_default,function(sk,sv){
        $(this).bind("click",function(){
            var parent = $(this).parents(".address_row");
            $.getJSON("flow.php?act=set_default_address&address_id="+$(parent).attr("address-id"),function(data){
               if(data == 1){
                   $(address_rows).find("label").removeClass("default");
                   $(parent).find("label").addClass("default");
               }else {
                   return ;
               }
           });
        });
    });
}

function show_address_row(obj){
    var address_rows = $("#consignee").find(".address_row");
    var edit_spans = $(address_rows).find(".edit_span");
    var radios = $(address_rows).find("input[type='radio']");
    address_rows.removeClass("selected_row");
    edit_spans.removeClass("selected_span");
    $(radios).attr("checked",false);
    var all_form  = $("#consignee").find(".info_form");
    var my_form = $(obj).find(".info_form");
    $(all_form).hide();
    $(my_form).show();
    $(obj).addClass("selected_row");
    $(obj).find(".edit_span").addClass("selected_span");
    $(obj).find("input[type='radio']").attr('checked',true);
}

function add_address(){      
    var bh = $("body").height();
    var bw = $("body").width(); 
    $("#fullbg").css({
        height:bh,
        width:bw, 
        display:"block"
    });
    $("#editAddress").hide();
    $("#addAddress").show();
    //清空表单内容
    document.getElementById("my_profile_form").reset();
    $("#my_profile_form").find("input[name='address_id']").val('');
    $("#my_profile_region select[name='province'] option[value=0]").attr("selected","selected");
    $("#my_profile_region select[name='city'] option[value=0]").attr("selected","selected");
    $("#my_profile_region select[name='district'] option[value=0]").attr("selected","selected");
    
    $("#my-profile-border,.my-profile-bg").show();
}

/*保存收货人信息*/
function save_address(){
    var inputs = $("#my_profile_form input");
    var selects = $("#my_profile_form select");
    var eles = $.merge(inputs,selects);
    var send = {};
    var val = {};
    var contact_method = 0;
    var province = 0;
    var city = 0;
    var district = 0;
    var submit = true;
    $.each(eles,function(i,j){
        var name = $(j).attr("name");          
        val[name] = $(j).val(); 
        if(name == 'consignee'){
            var consignee = val[name];
            var reg = /[\u4E00-\u9FA5\uF900-\uFA2D]/;
            if(!reg.test(consignee)){
                $("#errName").html("收货人格式不正确");
                submit = false;
            }else if(consignee.length < 2){
                $("#errName").html("收货人名称最少为2位");
                submit = false;
            }             
        }
        if(name == 'address'){
            var address = val[name];
            if(address == ''){
                $("#errAddress").html("请填写收货人的详细地址");
                submit = false;
            }
        }
        if(name == 'mobile'){
            var mobile = val[name];
            if(mobile == ''){                  
                contact_method = contact_method +1;
            } else {
                var patrn = /^(13[0-9]|15[0-9]|18[0-9]|147|17[0-9])\d{8}$/;
                if (!patrn.exec(mobile)) {
                    $("#errMobile").html("手机号码格式不正确");
                    submit = false;
                }
            }
        }
        if(name == 'tel'){
            var tel = val[name];
            if(tel == ''){
                contact_method = contact_method +1;
            }
        }           
        if(name = 'province'){
            province = val[name];
        }
        if(name = 'city'){
            city = val[name];
        }
        if(name = 'district'){
            district = val[name];
        }
    });
    send[0] = val;
    if(province == 0 || city == 0 || city == null ){
        $("#errAddress").html("请选择收货人所在区域");
        submit = false;
    }
    if(contact_method == 2){
        $("#errMobile").html("请留下手机号码或者固话以方便联系");
        submit = false;
    }
    if(submit){
        $.ajax({
            type:"post",
            url:"flow.php?step=save_consignee&from=flow_consignee",
            dataType:'json',
            data:{consignee_data:send},
            success:function(data){
                if(data.error == 0){
                    $("#step-1").empty();
                    $("#step-1").append(data.addresses);
                    getShipping();
                    $("#fullbg,#my-profile-border,.my-profile-bg").hide();
                }
            }
        });
    }
}

/*修改地址*/
function edit_address(address_id){
    var form = $("#my_profile_form");
    var address = $("#address_"+address_id);
    $(form).find("#my_profile_consignee").val($(address).find("span[data-name='consignee']").html());
    $(form).find("#my_profile_address").val($(address).find("span[data-name='address']").html());
    $(form).find("#my_profile_mobile").val($(address).find("span[data-name='mobile']").html());
    $(form).find("#my_profile_tel").val($(address).find("span[data-name='tel']").html());
    $(form).find("#address_id").val(address_id);
    //获取区域信息
    var province =$("#address_province_"+address_id).val();
    var city = $("#address_city_"+address_id).val();
    var district = $("#address_district_"+address_id).val();
    $.ajax({
        url:"region.php?act=get_region&id_pre=my_profile",
        type:"post",
        dataType:'json',
        data:{province:province,city:city,district:district},
        success:function(data){
            $(form).find("#my_profile_region").html(data);
        }
    });
    var bh = $("body").height();
    var bw = $("body").width(); 
    $("#fullbg").css({
        height:bh,
        width:bw, 
        display:"block"
    });
    $("#my-profile-border,.my-profile-bg").show();
    $(form).find("#editAddress").show();
    $(form).find("#addAddress").hide();
    $(form).show();     
}

function ship_method_click(){
    var method = $("div .ship_method");
//    var method_1 = $(".express");
    var radios = $(method).find("input[type='radio']");
    $.each(method,function(){
        $(this).live("click",function(){
            $(radios).attr("checked",false);
            $(this).find("input[type='radio']").attr("checked",true);
            $(".ship_method").removeClass("express-selected")
            $(this).addClass("express-selected");
            select_shipping();
        });
    });
}

function pay_method_click(){
    var method = $(".pay_method > .pay_name");
    var radios = $(method).find("input[type='radio']");
    $.each(method,function(){
        $(this).live("click",function(){
            if(!$(this).hasClass("disabled")){
                $(radios).attr("checked",false);
                $(".pay_field ").removeClass("method-selected");
                $(this).addClass("method-selected");
                var my_radio = $(this).find("input[type='radio']");
                $(my_radio).attr("checked",true);
                select_payment();
                //var data_id = $(my_radio).attr("data-id");
                //var pay_desc = $(".pay_description[data-id='"+data_id+"']");
                //$(".pay_description").hide();
                //$(pay_desc).show();
            }
        });
    });
}
//设置应付金额
function set_total(data){
    var order_total = $("#ECS_ORDERTOTAL");
    $("#payPriceId").html(data.total.amount_formated);
    $("#goods_total_price").html(data.total.formated_goods_price);
    //alert(data.total.shipping_fee);
    if(data.total.shipping_fee > 0 ){
        $("#shipping_fee").html(data.total.shipping_fee_formated);
        $("#shipping_fee_tr").show();
    } else {
        $("#shipping_fee_tr").hide();
    }
    if(data.total.surplus > 0 ){
        $("#surplus_money").html("-"+data.total.surplus_formated);
        $("#surplus_money_tr").show();
    } else {
        $("#surplus_money_tr").hide();
    }
    if(data.total.integral_money > 0){
        $("#integral_money_formated").html("-"+data.total.integral_formated);
        $("#integral_money_formated_tr").show();
    } else {
        $("#integral_money_formated_tr").hide();
    }
    if(data.total.bonus > 0 ){
        $("#bonus_money").html("-"+data.total.bonus_formated);
        $("#bonus_money_tr").show();
    } else {
        $("#bonus_money_tr").hide();
    }
    //设置获得的积分
    $("#will_get_integral").html(data.total.will_get_integral);
    
}
function getShipping(){
    var selected_address = get_address_id();
    if(selected_address === false){
        return;
    }
    $.ajax({
        type:"post",
        url:"flow.php?act=get_shipping"+"&address_id="+selected_address,
        dataType:"json",
        success:function(data){
            if(data.error == ""){
                $("#shippint_method_area").empty();
                $("#shippint_method_area").append(data.content);
                ship_method_click();
            }
        }
    });
}

function select_shipping(){
    var selected_address = get_address_id();
    var shipping = $(".ship_name input[type='radio']:checked");
    if(selected_address === false){
        alert("请先选择收货人信息。");
        return;
    }
    $.ajax({
        type:"post",
        url:"flow.php?step=select_shipping&shipping="+$(shipping).attr("data-id")+"&address_id="+selected_address,
        dataType:"json",
        success:function(data){
            if(data.error == ""){
                set_total(data);
                set_payment(data.support_cod);
                //设置商品列表的配送方式
                $("#order-cart .p-express").html(data.shipping_name);
            }
        }
    });
}
//设置支付方式的状态

function set_payment(support_cod){
    var payments =  $("input[name='payment']");
    var payments1 =  $(".pay_field");
    $(payments1).removeClass("method-selected");
    $(payments).removeAttr("checked");
    var cod = $(payments+"[is_cod=1]");
    if(support_cod == '1'){
        $(cod).parent().removeClass("disabled");
        $(cod).attr("disabled",false);      
    } else {
        $(cod).parent().addClass("disabled");
        $(cod).attr("disabled",true);
    }
}

function select_payment(){
    var selected_address = get_address_id();
    if(selected_address === false){
        alert("请先选择收货人信息。");
        return;
    }
    var payment = $(".pay_name input[type='radio']:checked");
    $.ajax({
        type:"post",
        url:"flow.php?step=select_payment&payment="+$(payment).attr("data-id"),
        dataType:"json",
        success:function(data){
            if(data.error == ""){
                set_total(data);
            }else {
                alert(data.error);
            }
        }
    });
}

//提交订单
function check_order_form(form){
  var addressSelected = false;
  var paymentSelected = false;
  var shippingSelected = false;
  var address1 = $("#step-1").find(".address_row").has("input[type='radio']:checked");
  var address = $("#step-1 .selected_row[address-id]");
  var payment = $("#step-3").find(".pay_name").filter(".method-selected");
  var shipping = $("#step-2").find(".ship_name").has("input[type='radio']:checked");
  if($(shipping).length == 0){
      alert("请选择一种配送方式。");
      return false;
  }
  if($(payment).length == 0){
      alert("请选择一种支付方式。");
      return false;
  }
  if($(address).length == 0){
      alert("请选择收货人信息。");
      return false;
  }

  return true;
}
//使用红包
function change_bonus(val){
    $.getJSON("flow.php?step=change_bonus&bonus="+val,function(data){
        if(data.error == ""){
            set_total(data);
        }else {
            alert(data.error);
        }
    });
}

//验证红包序列号
function validate_bonus(bonusSn){
    var sn = $.trim(bonusSn);
    if(sn != ""){
        $.getJSON("flow.php?step=validate_bonus&bonus_sn="+sn,function(data){
            if(data.error == ""){
                set_total(data);
            }else {
                alert(data.error);
            }
        });
    } else {
        alert("请输入红包序列号。");
    }
}

function change_surplus(val){
    if (selectedSurplus == val)
    {
      return;
    }
    else
    {
      selectedSurplus = val;
    }
    $.getJSON("flow.php?step=change_surplus",'surplus=' + val, function(obj){
        if (obj.error)
        {
          try
          {
            document.getElementById("ECS_SURPLUS_NOTICE").innerHTML = obj.error;
            document.getElementById('ECS_SURPLUS').value = '0';
            document.getElementById('ECS_SURPLUS').focus();            
          }
          catch (ex) { }
        }
        else
        {
          try
          {
            set_total(obj);
          }
          catch (ex) { }
          orderSelectedResponse(obj.content);
        }       
    });
}
/* *
 * 回调函数
 */
function orderSelectedResponse(result)
{
  if (result.error)
  {
    alert(result.error);
    location.href = './';
  }
  try
  {
    var layer = document.getElementById("ECS_ORDERTOTAL");

    layer.innerHTML = (typeof result == "object") ? result.content : result;

    if (result.payment != undefined)
    {
      var surplusObj = document.forms['theForm'].elements['surplus'];
      if (surplusObj != undefined)
      {
        surplusObj.disabled = result.pay_code == 'balance';
      }
    }
  }
  catch (ex) { }
}

/* *
 * 改变积分
 */
function change_integral(val)
{
  if (selectedIntegral == val)
  {
    return;
  }
  else
  {
    selectedIntegral = val;
  }

  //Ajax.call('flow.php?step=change_integral', 'points=' + val, changeIntegralResponse, 'GET', 'JSON');
  $.getJSON("flow.php?step=change_integral", 'points=' + val, function(obj){
    
      if (obj.error)
        {
          try
          {
            document.getElementById('ECS_INTEGRAL_NOTICE').innerHTML = obj.error;
            document.getElementById('ECS_INTEGRAL').value = '0';
            document.getElementById('ECS_INTEGRAL').focus();
          }
          catch (ex) { }
        }
        else
        {
          try
          {
            set_total(obj);
          }
          catch (ex) { }
          orderSelectedResponse(obj.content);
        }
  });
}
function change_num(rec_id, diff){
    var goods_number =Number($("#goods_number_" + rec_id).val()) + Number(diff); 
    change_goods_num(rec_id, goods_number);
}
function change_goods_num(rec_id, goods_number){
    goods_number = Number(goods_number);
    if(!goods_number){
        $("#goods_number_"+rec_id).val(1);
        goods_number = 1;
    }
    if(goods_number > 0 ){
        $.ajax({
            url:'flow.php?step=ajax_update_cart&from_checkout=1',
            type:'POST',
            dataType:'JSON',
            data: 'rec_id=' + rec_id +'&goods_number=' + goods_number,
            success:function(data){
                $("#goods_number_"+rec_id).val(goods_number);
                $("#goods_subtotal_"+rec_id).html(data.goods_subtotal);
                $("#total_goods_price").html(data.total.goods_price_formated);
                set_total(data);
            }
        });
    }
}
function delect_goods(val){
    var delect=$("#delect"+val);
    var r= confirm("是否将这件商品移出购物车？");
    if(r)
        $.getJSON("flow.php?step=drop_goods", 'id=' + val, function(obj){
            $(delect).closest('.review-tbody').remove();
//            var ee=$("#step-4").find(".review-body");
//            set_total(obj);
            if (obj.error=="您的购物车中没有商品！")
            {
              try
              {
                $(".review-body").find(".review-none").css("display","block");
                $("#goods_total_price").html("¥ 0.00");
                $("#payPriceId").html("¥ 0.00");
                $("#will_get_integral").html("0");
                $(".integral-keyong").html("（可用0点）");
              }
              catch (ex) { }
            }
            else
            {
              try
              {
                set_total(obj);
              }
              catch (ex) { }
              orderSelectedResponse(obj.content);
            }
//            for(var key in obj){  
//                if(typeof(obj[key])=='array'||typeof(obj[key])=='object'){//递归调用    
//                    print_array(obj[key]);  
//                }else{  
//                    $(".shuzu").html(key + ' = ' + obj[key] + '<br>');  
//                }  
//            } 
        });
    else{
        
    }
    
    
    
//    var x;
//    var r=confirm("您确定要删除这件商品吗？");
//    if (r==true)
//      {
//      x="You pressed OK!";
//      }
//    else
//      {
//      x="You pressed Cancel!";
//      }
//    document.getElementById("demo").innerHTML=x;
    
    
    
//    $.getJSON("flow.php?step=drop_goods", 'id=' + val, function(){
//        $("#order-cart").remove();
//    });
}