<?php
/**
 * add by zbl 2013-08-29
 * 新增注册用户，累积注册用户，活跃用户-周内至少登陆１次，每月访问量
 * @var unknown_type
 */
define('IN_ECS', true);
//页面引用
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');

if($_REQUEST['act']=='list'){
	
	$user_situation=get_user_situation();
	
	$smarty->assign('user_situation_list', $user_situation['user_situation_list']);
	$smarty->assign('filter',       $user_situation['filter']);
	$smarty->assign('record_count', $user_situation['record_count']);
    $smarty->assign('page_count',   $user_situation['page_count']);
    $smarty->assign('full_page',    1);//解决分页查询出现页面重复情况
	
    $smarty->assign('ur_here', $_LANG['user_situation']);
    
    /* 在页脚显示内存信息 */
    assign_query_info();
	$smarty->display('user_situation.htm');
	
}elseif($_REQUEST['act']=='query'){
    
	$user_situation=get_user_situation();
	
	$smarty->assign('user_situation_list', $user_situation['user_situation_list']);
	$smarty->assign('filter',       $user_situation['filter']);
	$smarty->assign('record_count', $user_situation['record_count']);
    $smarty->assign('page_count',   $user_situation['page_count']);
	
    $smarty->assign('ur_here', $_LANG['user_situation']);
	$tpl =  'user_situation.htm';
    make_json_result($smarty->fetch($tpl), '',array('filter' => $user_situation['filter'], 'page_count' => $user_situation['page_count']));
}
?>