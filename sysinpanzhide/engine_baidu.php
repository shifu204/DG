<?php
/**
 * ECSHOP 管理中心搜索统计相关
 * $Author: ming
 * $Id: engine_baidu.php 17217 2015-03-18 09:15:23 ming
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'keywords_list';

if($act == 'keywords_list'){   
    $keywords_list = keyword_list_basic();

    $smarty->assign('ur_here',     $_LANG['searchengine_baidu']);
    $smarty->assign('action_link', array('text' =>'百度搜索引擎关键词', 'href' => 'engine_baidu.php?act=keywords_list'));
    $smarty->assign('keywords_list', $keywords_list['result']);
    $smarty->assign('record_count', $keywords_list['record_count']);
    $smarty->assign('page_count', $keywords_list['page_count']);
    $smarty->assign('filter', $keywords_list['filter']);
    $smarty->assign('full_page', true);
    $smarty->assign('start_date', $keywords_list['filter']['start_date']);
    $smarty->assign('end_date', $keywords_list['filter']['end_date']);
    $smarty->display("search_engine/baidu_keywords.html");
}
elseif($act == 'query'){
    $keywords_list = keyword_list_basic();

    $smarty->assign('keywords_list', $keywords_list['result']);
    $smarty->assign('filter', $keywords_list['filter']);
    $smarty->assign('record_count', $keywords_list['record_count']);
    $smarty->assign('page_count', $keywords_list['page_count']);
    $smarty->assign('start_date', $keywords_list['filter']['start_date']);
    $smarty->assign('end_date', $keywords_list['filter']['end_date']);
    $sort_flag  = sort_flag($keywords_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    make_json_result($smarty->fetch('search_engine/baidu_keywords.html'), '',
        array('filter' => $keywords_list['filter'], 'page_count' => $keywords_list['page_count']));
}
elseif($act == 'keyword_detail'){
    $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    if(!empty($keyword)){
        $detail = keyword_detail($keyword);
        $detail['filter']['act'] = 'keyword_detail_query';
        $smarty->assign('keyword_detail', $detail['result']);
        $smarty->assign('filter', $detail['filter']);
        $smarty->assign('record_count', $detail['record_count']);
        $smarty->assign('page_count', $detail['page_count']);
        $smarty->assign('full_page', true);
        $smarty->display('search_engine/keyword_detail.html');
    }
}
elseif($act = 'keyword_detail_query'){
    $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    if(!empty($keyword)){
        $detail = keyword_detail($keyword);
        $detail['filter']['act'] = 'keyword_detail_query';
        $smarty->assign('keyword_detail', $detail['result']);
        $smarty->assign('filter', $detail['filter']);
        $smarty->assign('record_count', $detail['record_count']);
        $smarty->assign('page_count', $detail['page_count']);
        make_json_result($smarty->fetch('search_engine/keyword_detail.html'), '',
        array('filter' => $detail['filter'], 'page_count' => $detail['page_count']));
    }
}

/**
 * 关键词基础信息
 */
function keyword_list_basic(){
    $filter = array();
    $filter['keyword'] = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    $filter['sort_by'] = isset($_REQUEST['sort_by']) ? trim($_REQUEST['sort_by']) : 'count';
    $filter['sort_order'] = isset($_REQUEST['sort_order']) ? trim($_REQUEST['sort_order']) : 'DESC';
    $filter['start_date'] = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
    $filter['end_date'] = isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : '';
    $where = '';

    if(!empty($filter['keyword'])){
        $where .= " AND eb.wd LIKE '%".  mysql_like_quote($filter['keyword']) . "%'";
    }
    //格式化时间
    if(empty($filter['start_date']) && empty($filter['end_date'])){
        //默认时间是一个星期的统计数据
        $today = strtotime(local_date('Y-m-d'));   //本地时间
        $filter['start_date']  = $today - 86400 * 6; //六天之前
        $filter['end_date'] = $today + 86400; //明天零时
    }else {
        if(!empty($filter['start_date'])){
            $filter['start_date'] = local_strtotime($filter['start_date']);
        }
        if(!empty($filter['end_date'])){
            $filter['end_date'] = local_strtotime($filter['end_date']);
        }
    }
    
    if($filter['start_date']){
        $where .=" AND eb.time >= {$filter['start_date']}";
    }
    if($filter['end_date']){
        $where .=" AND eb.time <= {$filter['end_date']}";
    }
       
    //获取总数
    $sql = "SELECT COUNT(*) ". 
            " FROM " . $GLOBALS['ecs']->table('engine_baidu') . " AS eb " . 
            " LEFT JOIN " .
            " (SELECT * FROM " . $GLOBALS['ecs']->table('trace_browse') . " WHERE action = " . TRACE_ORDER_SUBMIT .") " .
            " AS tb  ON tb.sesskey = eb.sesskey".
            " WHERE wd !='' ". $where .
            " GROUP BY wd ";
    $filter['record_count'] = count($GLOBALS['db']->getAll($sql));
    
    $filter = page_and_size($filter);
    $limit = " LIMIT {$filter['start']} , {$filter['page_size']}";
    $sql = "SELECT COUNT(eb.id) as count, COUNT(tb.data) as order_count, eb.wd, search_data, r.register_count ". 
            " FROM " . $GLOBALS['ecs']->table('engine_baidu') . " AS eb " . 
            " LEFT JOIN " . " (SELECT * FROM " . $GLOBALS['ecs']->table('trace_browse') . " WHERE action = " . TRACE_ORDER_SUBMIT .") " . " AS tb  ON tb.sesskey = eb.sesskey" . 
            " LEFT JOIN " . " (SELECT COUNT(eb.id) as register_count, eb.wd FROM " . $GLOBALS['ecs']->table("engine_baidu") . " AS eb LEFT JOIN " . $GLOBALS['ecs']->table("trace_browse") . " AS tb ON tb.sesskey = eb.sesskey WHERE tb.action = " . TRACE_USER_REGISTER ." GROUP BY eb.wd) AS r ON r.wd = eb.wd " .   
            " WHERE eb.wd !='' ". $where .
            " GROUP BY wd ORDER BY {$filter['sort_by']} {$filter['sort_order']}, time DESC " .$limit;
    //$res = $GLOBALS['db']->getAll($sql);
    $res = $GLOBALS['db']->query($sql);
    $arr = array();
    while($row = $GLOBALS['db']->fetchRow($res)){
        //$row['register_count'] = get_wd_register($row['wd'],'baidu', $filter);    
        $row['total_amount'] = get_wd_order_amount($row['wd'],'baidu', $filter);
        $search_data = unserialize($row['search_data']);
        $row['where'] = $search_data['where'];
        $row['why'] = $search_data['why'];
        $row['what'] = $search_data['what'];
        $row['how'] = $search_data['how'];
        $arr[] = $row;
    }
    $filter['start_date'] = local_date('Y-m-d', $filter['start_date']);
    $filter['end_date'] = local_date('Y-m-d', $filter['end_date']);
    $filter['page_count'] = ceil($filter['record_count'] / $filter['page_size']);
    return array('result'=>$arr, 'filter'=>$filter, 'page_count'=>$filter['page_count'], 'record_count'=>$filter['record_count']);
}

function keyword_detail($keyword = ''){
    if(empty($keyword)){
        $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    }
    if(!empty($keyword)){
        $filter = array();
        $filter['keyword'] = $keyword;
        $filter['sort_by'] = isset($_REQUEST['sort_by']) ? trim($_REQUEST['sort_by']) : 'time';
        $filter['sort_order'] = isset($_REQUEST['sort_order']) ? trim($_REQUEST['sort_order']) : 'DESC';
        $filter['start_date'] = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
        $filter['end_date'] = isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : '';
        $where = " WHERE eb.wd = '$keyword'";
        //格式化时间
        if(empty($filter['start_date']) && empty($filter['end_date'])){
            //默认时间是一个星期的统计数据
            $today = strtotime(local_date('Y-m-d'));   //本地时间
            $filter['start_date']  = $today - 86400 * 6; //六天之前
            $filter['end_date'] = $today + 86400; //明天零时
        }else {
            if(!empty($filter['start_date'])){
                $filter['start_date'] = local_strtotime($filter['start_date']);
            }
            if(!empty($filter['end_date'])){
                $filter['end_date'] = local_strtotime($filter['end_date']);
            }
        }
        
        if($filter['start_date']){
            $where .=" AND eb.time >= {$filter['start_date']}";
        }
        if($filter['end_date']){
            $where .=" AND eb.time <= {$filter['end_date']}";
        }
        
        //获取总数
        $sql = " SELECT COUNT(eb.sesskey)" . 
               " FROM " . $GLOBALS['ecs']->table('engine_baidu') . " AS eb " . 
               " LEFT JOIN " . $GLOBALS['ecs']->table('trace_browse') . " AS tb ON tb.sesskey = eb.sesskey " . 
               $where . 
               " GROUP BY eb.sesskey ";
        $filter['record_count'] = count($GLOBALS['db']->getAll($sql));
        
        $filter = page_and_size($filter);
        $limit = " LIMIT {$filter['start']} , {$filter['page_size']}";
        $sql = " SELECT COUNT(tb.sesskey) as page_count, eb.from, eb.target_url, eb.time, tb.from as device, tb.ip, tb.action, tb.area " . 
               " FROM " . $GLOBALS['ecs']->table('engine_baidu') . " AS eb " . 
               " LEFT JOIN " . $GLOBALS['ecs']->table('trace_browse') . " AS tb ON tb.sesskey = eb.sesskey " . 
               $where . 
               " GROUP BY eb.sesskey " . 
               " ORDER BY `{$filter['sort_by']}` {$filter['sort_order']}" . $limit;
        $arr = array();
        $res = $GLOBALS['db']->query($sql);
        while($row = $GLOBALS['db']->fetchRow($res)){
            $row['time'] = local_date('Y-m-d H:i:s', $row['time']);
            $row['device'] = $GLOBALS['_LANG']['from'][$row['device']];
            $row['action'] = $GLOBALS['_LANG']['action'][$row['action']];
            $arr[] = $row;
        }
        $filter['start_date'] = local_date('Y-m-d', $filter['start_date']);
        $filter['end_date'] = local_date('Y-m-d', $filter['end_date']);
        $filter['page_count'] = ceil($filter['record_count'] / $filter['page_size']);
        return array('result'=>$arr, 'filter'=>$filter, 'page_count'=>$filter['page_count'], 'record_count'=>$filter['record_count']);
    } else {
        return false;
    }
}

//根据关键词获取该关键词的用户注册数
function get_wd_register($wd,$engine = 'baidu',$filter = array()){
    $table = $GLOBALS['ecs']->table('engine_'.$engine);
    $where_time = ' AND 1';
    if($filter['start_date']){
        $where_time .=" AND eb.time >= {$filter['start_date']}";
    }
    if($filter['end_date']){
        $where_time .=" AND eb.time <= {$filter['end_date']}";
    }
    $sql = "SELECT COUNT(tb.trace_id) FROM " . $table . " AS eb" . 
           " LEFT JOIN " . $GLOBALS['ecs']->table('trace_browse') . " AS tb ON tb.sesskey = eb.sesskey " . 
           " WHERE eb.wd = '$wd' AND tb.action = " . TRACE_USER_REGISTER . $where_time;
   return $GLOBALS['db']->getOne($sql);
}

//根据关键词获取该关键词的订单的金额
function get_wd_order_amount($wd,$engine = 'baidu',$filter = array()){
    $table = $GLOBALS['ecs']->table('engine_'.$engine);
    $where_time = ' AND 1';
    if($filter['start_date']){
        $where_time .=" AND eb.time >= {$filter['start_date']}";
    }
    if($filter['end_date']){
        $where_time .=" AND eb.time <= {$filter['end_date']}";
    }
    $sql = "SELECT tb.data  FROM " . $table . " AS eb " . 
           " LEFT JOIN " . $GLOBALS['ecs']->table('trace_browse') . " AS tb ON tb.sesskey = eb.sesskey " . 
           " WHERE eb.wd = '$wd' AND tb.action = " . TRACE_ORDER_SUBMIT . $where_time . 
           " GROUP BY tb.sesskey";         
    $orders = $GLOBALS['db']->getAll($sql);
    if(!empty($orders)){
        $total_amount = 0;
        foreach($orders as $order){
            $sql = "SELECT pl.order_amount FROM " . $GLOBALS['ecs']->table('pay_log') . " AS pl " . 
                   " LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS oi ON oi.order_id = pl.order_id ".
                   " WHERE oi.order_sn = ". $order['data'];
            $amount = $GLOBALS['db']->getOne($sql);
            $total_amount += $amount;
        }
        return $total_amount;
    }
    return 0;
}