<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/order.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

if($action == 'ajax_order_detail'){
    $order_id = isset($_REQUEST['order_id'])?intval($_REQUEST['order_id']):0;
    $result = array();
    $result['error'] = '';
    if(empty($order_id)){
        $result['error'] = 1;
        $result['msg']= "订单不存在";
        die(json_encode($result));
    }
    $order = order_info($order_id);
    $order['status_str'] = get_order_status(0, $order);

    $goods = order_goods($order_id);
    if(!empty($goods)){
        foreach($goods as $gk=>$gv){
            $goods[$gk]['detail'] = get_goods_info($gv['goods_id']);
            $goods[$gk]['formated_subtotal'] = price_format($gv['subtotal'],false);
            $goods[$gk]['formated_goods_price'] = price_format($gv['goods_price'],false);
        }
    }
    $smarty->assign('order',$order);
    $smarty->assign('goods',$goods);
    $result['content'] = $smarty->fetch("order/order_details.dwt");
    die(json_encode($result));
}
?>
