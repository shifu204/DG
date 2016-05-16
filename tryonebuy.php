<?php

/**
 * ECSHOP 一听试购
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: pgcao (www.kl3w.com) $
 * $Id: tryonebuy.php 0001 2012-02-23 12:23:12Z pgcao $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

$action = isset($_REQUEST['act'])?$_REQUEST['act']:'';

function _tryonebuy_get_brand_list(){
    $sql = 'SELECT brand_id, brand_name, brand_logo FROM ' . $GLOBALS['ecs']->table('brand') . ' ORDER BY sort_order';
    $res = $GLOBALS['db']->getAll($sql);

    $brand_list = array();
    foreach ($res AS $key => $row){
        $brand_list[$key]['brand_id'] = $row['brand_id'];
        $brand_list[$key]['brand_name'] = addslashes($row['brand_name']);
        $brand_list[$key]['brand_logo'] = $row['brand_logo'];
    }

    return $brand_list;
}

//echo DateDiff(date('Y-m-d'),'2011-03-10','d');
function _DateDiff($date1, $date2, $unit = "") { //时间比较函数，返回两个日期相差几秒、几分钟、几小时或几天
	switch ($unit) { 
		case 's': 
			$dividend = 1; 
			break; 
		case 'i': 
			$dividend = 60; 
			break; 
		case 'h': 
			$dividend = 3600; 
			break; 
		case 'd': 
			$dividend = 86400; 
			break; 
		default: 
			$dividend = 86400; 
	} 
    $time1 = strtotime($date1);
    $time2 = strtotime($date2); 
    if ($time1 && $time2) {
        return (float)($time1 - $time2) / $dividend; 
    } else {
        return 0; 
    }
} 

if ($action == 'get_goods'){
	include_once('includes/cls_json.php');
	$result = array();
	
	$brand_id = intval($_REQUEST['brand_id']);
	
	if($brand_id){
		$cate = 0;//!empty($_REQUEST['cat'])   && intval($_REQUEST['cat'])   > 0 ? intval($_REQUEST['cat'])   : 0;
		$sort   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
		$order = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
		$cate_where = ($cate > 0) ? 'AND ' . get_children($cate) : '';
		/* 获得商品列表 */
		$sql = 'SELECT goods_id, goods_name, goods_thumb, shop_price FROM ' . $GLOBALS['ecs']->table('goods') . ' ' .
				"WHERE is_tryonebuy = 1 AND is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 AND brand_id = '$brand_id' $cate_where".
				"ORDER BY $sort $order";
		$result = $GLOBALS['db']->getAll($sql);
	}
    foreach($result as $key => $val){
         $goods_id_array[] = $val['goods_id'];
         $result[$key]['goods_thumb']  = get_image_path($val['goods_id'], $val['goods_thumb'], true);
         $result[$key]['shop_price'] = price_format($val['shop_price']);
    }
	
	$result['goods'] = $result ? $result : array();

    $json = new JSON;
    echo $json->encode($result);die();
	
}elseif($action == 'buy'){
    include_once('includes/cls_json.php');
    $_POST['goods'] = strip_tags(urldecode($_POST['goods']));
    $_POST['goods'] = json_str_iconv($_POST['goods']);

    if (empty($_POST['goods']))exit;
    $result = array('error' => 0, 'message' => '未知错误!','content' => '', 'goods_id'=>0);
    $json  = new JSON;
    $goods = $json->decode($_POST['goods']);
	if(!empty($goods->goods_id)){
		
		// 取得购物类型
        $flow_type = CART_GENERAL_GOODS;
		
        //保存收货人信息
        $consignee = array(
            'address_id'    => 0,
            'consignee'     => empty($goods->consignee)  ? '' :   compile_str(trim($goods->consignee)),
			
            'country'       => empty($goods->country)    ? '' :   intval($goods->country),
            'province'      => empty($goods->province)   ? '' :   intval($goods->province),
            'city'          => empty($goods->city)       ? '' :   intval($goods->city),
            'district'      => empty($goods->district)   ? '' :   intval($goods->district),
			
            'email'         => '',
            'address'       => empty($goods->address)    ? '' :   compile_str($goods->address),
            'zipcode'       => empty($goods->zipcode)    ? '' :   compile_str(make_semiangle(trim($goods->zipcode))),
            'tel'           => empty($goods->tel)        ? '' :   compile_str(make_semiangle(trim($goods->tel))),
            'mobile'        => empty($goods->tel)     ? '' :   compile_str(make_semiangle(trim($goods->tel))),
            'sign_building' => '',
            'best_time'     => '',
        );
		
		$payment = $db->getRow("SELECT pay_id,pay_name FROM " . $ecs->table('payment') . " WHERE pay_code='cod'"); 
		if(!$payment){
			$result['error'] = 1;
			$result['message'] = '下单失败！我们未开启付到付款';
			die($json->encode($result));
		}
		
		$shipping = $db->getRow("SELECT shipping_id,shipping_name FROM " . $ecs->table('shipping') . " WHERE shipping_code='sf_express'"); 
		if(!$shipping){
			$result['error'] = 1;
			$result['message'] = '下单失败！我们未开启免费快递';
			die($json->encode($result));
		}
		
		$_COOKIE['ECS']['tryonebuy'] = isset($_COOKIE['ECS']['tryonebuy']) ? $_COOKIE['ECS']['tryonebuy'] : '';
		if(empty($_COOKIE['ECS']['tryonebuy'])){
			$reinfo = $db->getOne("SELECT add_time FROM " . $ecs->table('order_info') . " WHERE tel='".$consignee['tel'] ."' AND user_id =0 AND order_status=0 ORDER BY add_time DESC"); 
		}else{
			$reinfo = $_COOKIE['ECS']['tryonebuy'];
            if(!is_numeric($reinfo) && strlen($reinfo)!=10) {
                $reinfo = gmtime();
            }
		}
		if($reinfo){
			//$daynum = _DateDiff(date('Y-m-d',$reinfo), date('Y-m-d',gmtime()), 'd');
			//if($daynum<1){
            $zero_clock = get_zero_clock('', $reinfo) + 86400;
            if (time() < $zero_clock) {
				$result['error'] = 1;
				$result['message'] = '下单失败！您今天已经使用过一听订购! 不能重复订购，如需修改或增加商品'."\n\n".'请联系德贝客服修改即可！客服电话：400-039-7006';
				if(empty($_COOKIE['ECS']['tryonebuy']))setcookie('ECS[tryonebuy]', $reinfo, $time + 86400 * 365, '/'); 
				die($json->encode($result));
			}
		}
		
        if ($_SESSION['user_id'] > 0){
            include_once(ROOT_PATH . 'includes/lib_transaction.php');
            //如果用户已经登录，则保存收货人信息
            $consignee['user_id'] = $_SESSION['user_id'];
            save_consignee($consignee, true);
        }else{
			$consignee['user_id'] = 0;
		}
		
		$order = $consignee;
		unset($order['address_id']);
        $order['add_time'] = gmtime();
        $order['order_status'] = OS_UNCONFIRMED;
        $order['shipping_status'] = SS_UNSHIPPED;
        $order['pay_status'] = PS_UNPAYED;
        $order['agency_id'] = get_agency_by_regions(array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']));
		
		$order['extension_code'] = '';
        $order['extension_id'] = 0;
        $order['surplus']  = 0;
        $order['integral'] = 0;
		
		$goods->number = 1; $goods->parent = 0; $goods->spec = array();
		
		/* 
		//如果商品有规格，而post的数据没有规格，跳到商品详情页 
		if (empty($goods->spec)){
		$sql = "SELECT COUNT(*) " ."FROM " . $ecs->table('goods_attr') . " AS ga, " .$ecs->table('attribute') . " AS a " .
		"WHERE ga.attr_id = a.attr_id " ."AND ga.goods_id = '" . $goods->goods_id . "' " ."AND a.attr_type = 1";
			if ($db->getOne($sql) > 0){
				$result['error'] = 9;
				$result['goods_id'] = $goods->goods_id;
				die($json->encode($result));
			}
		}
		*/
		
		clear_cart();// 先清空购物车
		
		// 商品数量是否合法
		if (!is_numeric($goods->number) || intval($goods->number) <= 0){
			$result['error'] = 1;
			$result['message'] = $_LANG['invalid_number'];
		}else{ 		
			
			// 添加到购物车
			if (addto_cart($goods->goods_id, $goods->number, $goods->spec, $goods->parent)){
				$result['error'] = 0;
				if ($_CFG['cart_confirm'] > 2){
					if (!cart_goods_exists($goods->goods_id,array()))addto_cart($goods->goods_id);
					// 检查订单中的商品
					$cart_goods = cart_goods($flow_type);
					if (empty($cart_goods)){
						$result['error'] = 1;
						$result['message'] = $_LANG['no_goods_in_cart'];
						die($json->encode($result));
					}
					
					// 保存到session
					$_SESSION['flow_consignee'] = stripslashes_deep($consignee);
					
					// 订单中的总额
					$total = order_fee($order, $cart_goods, $consignee);
					$order['bonus']        = $total['bonus'];
					$order['goods_amount'] = $total['goods_price'];
					$order['discount']     = $total['discount'];
					$order['surplus']      = $total['surplus'];
					$order['tax']          = $total['tax'];					
					
					$order['card_fee']      = $total['card_fee'];
					$order['order_amount']  = number_format($total['amount'], 2, '.', '');
					
					$order['pay_id'] = $payment['pay_id'];
					$order['pay_name'] = $payment['pay_name'];
					$order['pay_note'] = '使用一听试购，货到付款';
					
					$order['shipping_id'] = $shipping['shipping_id'];
					$order['shipping_name'] = $shipping['shipping_name'];
					
					/* 插入订单表 */
					$error_no = 0;
					do{						
						$order['order_sn'] = get_order_sn(); //获取新订单号
						$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order, 'INSERT');
						$error_no = $GLOBALS['db']->errno();
				
						if ($error_no > 0 && $error_no != 1062){
							$result['error'] = 1;
							$result['message'] = $GLOBALS['db']->errorMsg();
							die($json->encode($result));
						}
					}while ($error_no == 1062); //如果是订单号重复则重新提交数据
					
					$new_order_id = $db->insert_id();
					$order['order_id'] = $new_order_id;
                    $order['region'] = getRegionName($order['order_id']);
				
					/* 插入订单商品 */
					$sql = "INSERT INTO " . $ecs->table('order_goods') . "( " .
								"order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
								"goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id) ".
							" SELECT '$new_order_id', goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
								"goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id".
							" FROM " .$ecs->table('cart') .
							" WHERE session_id = '".SESS_ID."' AND rec_type = '$flow_type'";
					$db->query($sql); 
                                        clear_cart($flow_type);				
					/*记录用户浏览轨迹*/
                                        //trace::trace_browse(TRACE_ORDER_SUBMIT, $order['order_sn'], TRACE_FROM_ECS, true);
					$time = gmtime();
					setcookie('ECS[tryonebuy]', gmtime(), $time + 86400 * 365, '/'); 
					$result['message'] = '德贝商城会以安全、贴心和飞快的速度，将奶粉送到您的手上。';
					
                    /* 给商家发邮件 */
                    /* 增加是否给客服发送邮件选项 */
                    if ($_CFG['send_service_email'] && $_CFG['ym'] != '')
                    {
                        $tpl = get_mail_template('remind_of_new_order');
                        $smarty->assign('order', $order);
                        $smarty->assign('goods_list', $cart_goods);
                        $smarty->assign('shop_name', $_CFG['shop_name']);
                        $smarty->assign('send_date', date($_CFG['time_format']));
                        $content = $smarty->fetch('str:' . $tpl['template_content']);
                        send_mail($_CFG['shop_name'], $_CFG['ym'], $tpl['template_subject'], $content, $tpl['is_html']);
                    }
				}else{
					$result['message'] = $_CFG['cart_confirm'] == 1 ? $_LANG['addto_cart_success_1'] : $_LANG['addto_cart_success_2'];
				}
				$result['one_step_buy'] = $_CFG['one_step_buy'];
				insert_cart_info();
			}else{
				$result['error'] = 1;
				$result['message'] = '一听购买失败，请重新尝试！';
				$result['goods_id'] = stripslashes($goods->goods_id);
			}
		}
		unset($_SESSION['flow_consignee']);
		
		$result['confirm_type'] = !empty($_CFG['cart_confirm']) ? $_CFG['cart_confirm'] : 2;
		die($json->encode($result));
		
	}else{
        $result['error'] = 1;
        die($json->encode($result));
	}
	die();
}

$smarty->assign('lang', $_LANG);

/* 取得国家列表、商店所在国家、商店所在国家的省列表 */
if ($action == 'tryonebuymin'){
    //获取商品信息
    $goods_id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
    if(!empty($goods_id)){
        $select = "goods_id, goods_name, shop_price, promote_price, promote_start_date, promote_end_date, goods_thumb";
        $where = array();
        $where['goods_id'] = $goods_id;
        $where['is_tryonebuy'] = $where['is_on_sale'] = 1;
        $goods = $GLOBALS['cidb']->select($select)->where($where)->get("goods")->row_array();
        if(!empty($goods)){
            /* 修正促销价格 */
            if ($goods['promote_price'] > 0)
            {
                $promote_price = bargain_price($goods['promote_price'], $goods['promote_start_date'], $goods['promote_end_date']);
            }
            else
            {
                $promote_price = 0;
            }
            $goods['promote_price_org'] =  $promote_price;
            $goods['promote_price'] =  price_format($promote_price);
            if($promote_price > 0){
                $goods['shop_price'] = $promote_price;
            }
            $goods['shop_price'] = price_format($goods['shop_price'],false);
        }
    }
    $smarty->assign('goods',$goods);
    $smarty->assign('country_list',       get_regions());
    $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));
    $smarty->assign('name_of_region',   array($_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']));
    $smarty->display('tryonebuy_min.html');die();
}

//$brandlogo_list = _tryonebuy_get_brand_list();
//$smarty->assign('brandlogo_list', $brandlogo_list);
//$brand_list = get_brand_list();//所有品牌

$brand_list = array();$brand_list_arr = get_brands(8);//某分类下品牌
foreach($brand_list_arr as $key=>$val){
	$brand_list[$val['brand_id']] = $val['brand_name'];
}
$smarty->assign('brand_list', $brand_list);
//_P($brand_list,1);

$position = assign_ur_here(0, '一听试购');
$smarty->assign('page_title', $position['title']); // 页面标题

$smarty->assign('brand_id', intval($_REQUEST['brand_id'])); // 页面标题

$smarty->display('tryonebuy.html');


// 功能：获得指定日期当天、当星期、当月或当年的开始的时间戮
//      支持夏令时
//      支持选择星期几作为星期之始
// @param   $t  从哪天开始
//              ''  留空是当天
//              w   当星期（中国习惯以 Monday 作为第一天）
//              m   当月
//              y   当年 
// @param   $timestamp  指定一个时间戳，默认为当前时间
// @param   $week_start 从星期几开始，0~6表示日至六
// @return  $day_start  返回开始的时间戳
//
function get_zero_clock($t = '', $timestamp = null, $week_start = 5, $method = 0){
    // 设置默认为当前时间
    $timestamp = $timestamp ? $timestamp : time();
    // 获得当天开始时间戳
    // 方法一比方法二要快，我循环调用 1000 次示例结果是 2.16s 比 2.81s
    if ($method == 1) {
        $day_start = $timestamp - ($timestamp + date('Z', $timestamp)) % 86400;
    } else {
        $day_start = strtotime(date('Y-m-d', $timestamp)); 
    }
    if ($t) {
        $arr_date = localtime($timestamp, true);
        // mday 的取值范围从 1 开始，yday 的取值范围从 0 开始，wday 虽然也从 0
        // 开始，但是我们习惯把 Monday 作为第 1 天
        $tm = 'tm_' . $t;
        if ($t == 'm') {
            $days = $arr_date[$tm . 'day'] - 1;
        } elseif ($t == 'w' && $week_start > 0) {
            if ((idate('w', $timestamp)) >= $week_start) {
                $days = $arr_date[$tm . 'day'] - $week_start;
            } else {
                $days = $arr_date[$tm . 'day'] + (7 - $week_start);
            }
        } else {
            $days = $arr_date[$tm . 'day'];
        }
        $seconds = 86400 * $days;
        $day_start -= $seconds;
        // 夏令时的地区回补（因不确定所有夏令时都是提前 1 小时）
        if (($shour = idate('H', $day_start)) >= 22) {
            $day_start += 3600 * (24 - $shour);
        }
    }
    return $day_start;
} 
?>
