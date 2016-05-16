<?php
/**
 * ECSHOP 浏览记录管理
 * $Author: ming $
 * $Id: trace.php 17219 2015-03-20 17:50:19Z ming $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/cls_trace.php');
require_once(ROOT_PATH . 'includes/lib_order.php');

$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

if($act == 'order_trace'){
    $order_id = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0 ;
    if(!empty($order_id)){
        //获取订单信息
        $order_info = order_info($order_id);
        $order_info['order_goods'] = order_goods($order_id);
        
        //获取订单浏览记录
        $trace = get_order_trace($order_info['order_sn']);
        $smarty->assign('order_info', $order_info);
        $smarty->assign('trace', $trace);
        $smarty->assign('full_page', true);
        $smarty->display('search_engine/order_trace.html');
    }
}
/*获取注册跟踪数据*/
elseif($act == 'registe_trace'){
    $user_id = isset($_REQUEST['user_id'])? intval($_REQUEST['user_id']) : 0 ;
    if(!empty($user_id)){
        //注册用户浏览记录
        $trace = get_registe_trace($user_id);
        $smarty->assign('trace', $trace);
        $smarty->assign('full_page', true);
        $smarty->display('search_engine/registe_trace.html');
    }
}

/*获取下单跟踪数据*/
function get_order_trace($order_sn){
    $result = array();
    $sql = "SELECT sesskey FROM " . $GLOBALS['ecs']->table('trace_browse') . " WHERE action = " . TRACE_ORDER_SUBMIT . " AND data = '$order_sn' ORDER BY trace_id ASC";
    $sesskey = $GLOBALS['db']->getOne($sql);
    if(!empty($sesskey)){
        //获取页面浏览记录
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('trace_browse') . " WHERE sesskey = '$sesskey'";
        $res = $GLOBALS['db']->query($sql);
        while($row =  $GLOBALS['db']->fetchRow($res)){
            $row['add_time'] = local_date("Y-m-d H:i:s", $row['add_time']);
            $row['from'] = $GLOBALS['_LANG']['from'][$row['from']];
            $row['ori_action'] = $row['action'];
            $row['action'] = $GLOBALS['_LANG']['action'][$row['action']];
            $result['page_trace'][] = $row;
        }
        //如果由搜索引擎进入，并且有关键词等相关信息
        if(!empty($result['page_trace']) && !empty($result['page_trace'][0]['referer_domain'])){
            //判断是哪个搜索引擎进入的
            $engine = trace::get_engine_type($result['page_trace'][0]['referer_domain']);
        }
        if(!empty($engine) && $engine != 'ecshop' && $engine != 'ectouch'){
            $table = 'engine_baidu';
            $sql = "SELECT * FROM " . $GLOBALS['ecs']->table($table) . " WHERE sesskey = '$sesskey'";
            $keyword = $GLOBALS['db']->getRow($sql);
            $result['keyword'] = $keyword;
            $result['engine'] = $engine;
        }       
    }
    return $result;
}

function get_registe_trace($user_id){
    $result = array();
    $sql = "SELECT sesskey FROM " . $GLOBALS['ecs']->table('trace_browse') . " WHERE action = " . TRACE_USER_REGISTER . " AND data = '$user_id' ORDER BY trace_id ASC";
    $sesskey = $GLOBALS['db']->getOne($sql);
    if(!empty($sesskey)){
        //获取页面浏览记录
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('trace_browse') . " WHERE sesskey = '$sesskey'";
        $res = $GLOBALS['db']->query($sql);
        while($row =  $GLOBALS['db']->fetchRow($res)){
            $row['add_time'] = local_date("Y-m-d H:i:s", $row['add_time']);
            $row['from'] = $GLOBALS['_LANG']['from'][$row['from']];
            $row['ori_action'] = $row['action'];
            $row['action'] = $GLOBALS['_LANG']['action'][$row['action']];
            $result['page_trace'][] = $row;
        }
        if(!empty($result['page_trace']) && !empty($result['page_trace'][0]['referer_domain'])){
            //判断是哪个搜索引擎进入的
            $engine = trace::get_engine_type($result['page_trace'][0]['referer_domain']);
        }
        if(!empty($engine) && $engine != 'ecshop' && $engine != 'ectouch'){
            $table = 'engine_baidu';
            $sql = "SELECT * FROM " . $GLOBALS['ecs']->table($table) . " WHERE sesskey = '$sesskey'";
            $keyword = $GLOBALS['db']->getRow($sql);
            $result['keyword'] = $keyword;
            $result['engine'] = $engine;
        }  
    }
    return $result;
}