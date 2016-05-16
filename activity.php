<?php

/**
 * ECSHOP 活动列表
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: activity.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');
include_once(ROOT_PATH . 'includes/lib_goods.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');
 $smarty->assign('helps',           get_shop_help()); 
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

assign_template();
assign_dynamic('activity');
$position = assign_ur_here(0, '"五一来了"宝宝疯抢的时节');
$smarty->assign('page_title',       $position['title']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
$apage = isset($_REQUEST['apage']) ? trim($_REQUEST['apage']) : 'default';
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';
$show_deaw_tips = (isset($_REQUEST['show_deaw_tips'])&& $_REQUEST['show_deaw_tips'] == 1) ? true : false;
$smarty->assign('show_deaw_tips', $show_deaw_tips);
if($apage == '51activity'){  
    $start_date = local_strtotime('2015/04/20');
    $start_date = local_strtotime('2015/04/27');
    $end_date = local_strtotime('2015/05/07')+24*3600;
    $now = gmtime();
    //如果是51活动页，则获取top10消费排行榜
    $config_file = ROOT_PATH . ADMIN_PATH . '/activity/51activity_config.php';
    include_once ($config_file);
    $sql = "SELECT u.user_id, u.user_name, u.nickname, u.user_type, SUM(pl.order_amount) AS total_amount FROM " . $GLOBALS['ecs']->table('users') . " AS u" . 
           " LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS oi ON oi.user_id = u.user_id " . 
           " LEFT JOIN " . $GLOBALS['ecs']->table('pay_log') . " AS pl ON pl.order_id = oi.order_id" . 
           " WHERE oi.add_time >= $start_date AND oi.add_time <= $end_date AND oi.order_status IN (1,5,6) " . 
           " GROUP BY u.user_id ".
           " ORDER BY total_amount DESC ";
    $res = $GLOBALS['db']->getAll($sql);
    $list = array();
    if(!empty($top_users)){
        foreach ($top_users as $k=>$top){
            $list[$k]['user_name'] = $top;
            $list[$k]['user_id'] = 0;
            if(!empty($top_amount[$k])){
                //$top_amount[$k] = 0;
                $list[$k]['total_amount'] = $top_amount[$k];
            }          
        }
    }
    $list = array_merge($list,$res);
    //处理用户名
    foreach ($list as $lk=>$lv){
        $list[$lk]['user_name'] = user_name_handle($lv);
        if(!empty($lv['total_amount'])){
            $list[$lk]['formated_total_amount'] = price_format($lv['total_amount'],false);
            //对会员名，消费额进行部分屏蔽处理
            $list[$lk]['user_name'] = user_name_shield($list[$lk]['user_name']);
            $list[$lk]['formated_total_amount'] = total_amount_shield($list[$lk]['formated_total_amount']);
        }
    }
    $smarty->assign('list',$list);
}

if($apage == '51activity' && $act == 'default'){       
    $smarty->display("zt/activity20150501.dwt");
}
/*检查用户是否有抽奖资格*/
elseif($apage == '51activity' && $act =='check_draw'){      
    $result = check_51activity($start_date, $end_date);
    if($result['err'] == 0 && $result['order_sn'] != 0){
        $goods_list = get_gift_list($start_date, $end_date, $result['order_sn']);
        /*奖品转盘角度 格式 奖品id=>转盘角度*/
        $goods_angles = array(158=>105, 225=>165, 179=>345, 227=>135,226=>315);
        $gift_id = array_rand($goods_list);
        $result['gift_id'] = $gift_id;
        $result['gift_angle'] = $goods_angles[$gift_id];
        $result['gift_name'] = $goods_list[$gift_id];
        //将抽奖结果插入到数据库
        $goods_info = goods_info($gift_id);
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('order_goods') . "( " .
                "order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id) " . 
                " VALUES ( " .
                "{$result['order_id']}, $gift_id, '{$goods_info['goods_name']}', '{$goods_info['goods_sn']}', 0, 1, '0', ".
                "0, '', 1, '', 0, 1, '')";
        $GLOBALS['db']->query($sql);
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('activity_data') . "(user_id, gift_id, order_sn, add_time) VALUES ({$_SESSION['user_id']}, $gift_id, '{$result['order_sn']}', $now)";
        $GLOBALS['db']->query($sql);
    }
    echo json_encode($result);
}

function check_51activity($start_date,$end_date){
    $user_id = $_SESSION['user_id'];
    $result = array();
    $result['err'] = 0;
    $result['msg'] = '';
    if($user_id > 0 ){               
        $now = gmtime();
        if($start_date <= $now && $now <= $end_date){
            //获取用户在活动时间内下的订单
            $sql = "SELECT order_id, order_sn, user_id FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE add_time >= $start_date AND add_time <= $end_date AND user_id = $user_id ORDER BY add_time DESC";
            $orders = $GLOBALS['db']->getAll($sql);
            if(!empty($orders)){
                //检查订单是否已经抽过奖
                $result['draw_number'] = 0;
                $result['order_sn'] = 0;
                $result['order_id'] = 0;
                foreach ($orders as $key => $order){
                    if(!check_order_draw($order['order_sn'])){
                        $result['draw_number'] ++;
                        /*默认抽奖顺序是从最新的订单开始抽奖*/
                        if(empty($result['order_sn'])){
                            $result['order_sn'] = $order['order_sn'];
                            $result['order_id'] = $order['order_id'];
                        }
                    }
                } 
                if($result['draw_number'] == 0){
                    $result['err'] = 3;
                    $result['msg'] = '您的抽奖机会已用完！活动期间每下一张订单可以获取一次抽奖机会！';
                }
            }else {
                $result['err'] = 3;
                $result['msg'] = '您的抽奖机会已用完！活动期间每下一张订单可以获取一次抽奖机会！';
            }
        } else {
            $result['err'] = 2;
            $result['msg'] = '不在活动时间内。';
        }
    }else {
        $result['err'] = 1;
        $result['msg'] = '请您先登录。';
    }
    return $result;
}

function check_order_draw($order_sn){
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('activity_data') . " WHERE order_sn = '$order_sn'";
    $res = $GLOBALS['db']->getOne($sql);
    if($res > 0 ){
        return true;
    }else {
        return false;
    }
}

/*获取礼物列表*/
function get_gift_list($start_date, $end_date, $order_sn = 0){
    //Symbiotics牛初乳，小蜜蜂柠檬油指甲修护霜，小蜜蜂紫草膏，水宝宝防晒霜无泪无油无香防水，水宝宝防晒霜30ml
    $goods_ids = array(179=>'小蜜蜂紫草膏',227=>'水宝宝防晒霜无泪无油无香防水',226=>'水宝宝防晒霜30ml');
    if(!empty($order_sn)){
        $user_profile = get_profile($_SESSION['user_id']);
        $order_info = order_info(0, $order_sn);
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('activity_data') . " WHERE gift_id = 158 ";
        $znum = $GLOBALS['db']->getOne($sql);
        //500元以内的订单无法抽取 Symbiotics牛初乳且只能送3个      
        if(intval($order_info['total_fee']) >= 500 && $znum < 3 && local_strtotime($user_profile['reg_time']) >= $start_date){
            $goods_ids[158] = 'Symbiotics牛初乳';
        }  
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('activity_data') . " WHERE gift_id = 225 ";
        $xnum = $GLOBALS['db']->getOne($sql);
        //小蜜蜂柠檬油指甲修护霜 只送2个,且用户注册时间大于活动时间       
        if($xnum < 2){
            $goods_ids[225] = '小蜜蜂柠檬油指甲修护霜';
        }
    }
    return $goods_ids;
}

/*对用户名进行屏蔽处理*/
function user_name_shield($user_name){
    $length = mb_strlen($user_name,'utf-8');
    $return = '';
    if($length > 2 ){
        //对后2位进行处理
        $return = mb_substr($user_name, 0, $length-2,'utf-8')."**";
    } else {
        $return = $user_name;
    }
    return $return;
}

function total_amount_shield($total_amount) {
    $length = strlen($total_amount);
    $return = '';
    if($length > 0 ){
        //对第一位数字进行屏蔽
        $return = substr_replace($total_amount, '*',2,2);
    }
    return $return;
}