Utils.in_Array = function(stringToSearch, arrayToSearch)
{
	if(typeof stringToSearch == 'string' || typeof stringToSearch == 'number'){
	   for(var i = 0; i < arrayToSearch.length; i++){
		   if(arrayToSearch[i] == stringToSearch){return true;}
	   }
	}
	return false;
}
Utils.inTeger = function(val,n)
{
	if(!(typeof val == 'string' || typeof val == 'number')){return false;}
	var Letters;
	if(n==1){
  	   Letters = "0123456789.";
    }else if(n==2){
  	   Letters = "0123456789-";
    }else{
  	   Letters = "0123456789";
    }
    for (i=0; i<val.length; i++){ 
  	   if(Letters.indexOf(val.charAt(i)) == -1){ 
  	      return false;
  	   }
    }
    return true;
}

Utils.isMobile = function (val)
{
	var mobile = val+'';
	if(mobile.length != 11){
	    return false;
	}else if(!Utils.inTeger(mobile, 0)){
		return false;
	}else{
        var arr = new Array('134','135','136','137','138','139','147','150','151','152','157','158','159','182','187','188'/*移动*/,'130','131','132','155','156','185','186','145'/*联通*/,'133','153','180','189'/*电信*/,'181','183','184'/*其它*/);
        return Utils.in_Array(mobile.substring(0,3), arr);
	}
	return false;
}
Utils.isNumber = function(val)
{
	if(!(typeof val == 'string' || typeof val == 'number')){return false;}
    var reg = /^[\d|\.|,]+$/;
    return reg.test(val);
}

function setshowgoods(src,txt,money){
	var html = '<div style="text-align:center"><img style="width:145px;height:145px;" src="'+src+'" title="'+txt+'" /></div>';
	html += '<div style="padding:10px 0px;font-size:14px;"><strong>'+txt+'</strong></div>';
	html += '<p class="price">德贝价：'+money+'</p>';
	document.getElementById("showgoods").innerHTML = html;
}

function tryonebuypost(obj){

	obj.consignee.value = Utils.trim(obj.consignee.value);
	obj.tel.value = Utils.trim(obj.tel.value);
	obj.country.value = parseInt(Utils.trim(obj.country.value));
	obj.province.value = parseInt(Utils.trim(obj.province.value));
	obj.city.value = parseInt(Utils.trim(obj.city.value));
	obj.district.value = parseInt(Utils.trim(obj.district.value));
	obj.address.value = Utils.trim(obj.address.value);
	obj.zipcode.value = Utils.trim(obj.zipcode.value);
	
	
	var notgoods_id = false;
	if(!obj.brand_id.value || !obj.goods_id){
		alert('请先选择商品');
		return false;
	}
	if(obj.goods_id.length){
		for(var i=0;i<obj.goods_id.length;i++){
			if(obj.goods_id[i].checked)notgoods_id = obj.goods_id[i].value;
		}
	}else{
		if(obj.goods_id.checked)notgoods_id = obj.goods_id.value;
	}
	if(!notgoods_id){
		alert('请先选择商品');
		return false;
	}
	
	if(!obj.consignee.value){
		alert('请正确填写收货人名');
		return false;
	}
	if(obj.tel.value.length != 11 || !Utils.isMobile(obj.tel.value)){
		alert('请正确填写联系手机');
		return false;
	}
	if(obj.country.value<1||obj.province.value<1||obj.city.value<1||obj.district.value<1){
		alert('请正确选择收货地址');
		return false;
	}
	if(!obj.address.value){
		alert('请正确填写收货详细街道地址');
		return false;
	}
	if(!obj.zipcode.value||!Utils.isNumber(obj.zipcode.value)){
		alert('请正确填写邮政编码');
		return false;
	}

	var trygoods = new Object();
	trygoods.brand_id = obj.brand_id.value;
	trygoods.goods_id = notgoods_id;
	
	trygoods.consignee = obj.consignee.value;
	trygoods.tel = obj.tel.value;
	
	trygoods.country = obj.country.value;
	trygoods.province = obj.province.value;
	trygoods.city = obj.city.value;
	trygoods.district = obj.district.value;
	
	trygoods.address = obj.address.value;
	trygoods.zipcode = obj.zipcode.value;
	return trygoods;
}

function getGoodsResponse(result){
	var sel = document.getElementById("goods_id");
	var html = '';
	if (result.goods){
		for (i = 0; i < result.goods.length; i ++ ){
			html += '<li><label onclick="setshowgoods(\''+result.goods[i].goods_thumb+'\',\''+result.goods[i].goods_name+'\',\''+result.goods[i].shop_price+'\')"><input name="goods_id" type="radio" value="'+result.goods[i].goods_id+'" /> '+result.goods[i].goods_name+'</label></li>';
		}
	}else{
		html = '无此品牌相关商品!';
	}
	sel.innerHTML = html;
}

function getGoods(value){
	if(document.getElementById("showgoods"))document.getElementById("showgoods").innerHTML = '';
	//Ajax.call('tryonebuy.php', 'act=get_goods&brand_id=' + value, getGoodsResponse, 'GET', 'JSON');
        $.get("tryonebuy.php",'act=get_goods&brand_id=' + value, getGoodsResponse,function(data){
            getGoodsResponse(data)
        },'json');
}
