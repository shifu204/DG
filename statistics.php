<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_callback.php');
$act = isset($_REQUEST['act'])?trim($_REQUEST['act']):'default';
$smarty->assign('act',$act);
if($act == 'default'){
    $smarty->display('statistics.dwt');
}
else if($act == 'register_success'){
    $statistics_code = '';
    $smarty->assign('statistics_code',$statistics_code);
    $smarty->display('statistics.dwt');
}
else if($act == 'cart_done'){
    $statistics_code = '';
    $smarty->assign('statistics_code',$statistics_code);
    $smarty->display('statistics.dwt');
}
else if($act == 'new_qq_user'){
    $statistics_code = '';
    $smarty->assign('statistics_code',$statistics_code);
    $smarty->display('statistics.dwt');
}
else if($act == 'new_wx_user'){
    $smarty->assign('statistics_code',$statistics_code);
    $smarty->display('statistics.dwt');
}
/*推送百度统计代码*/
else if($act == 'push_script'){
    $type = trim($_GET['type']);
    $script = reg_callback($type);
    $smarty->assign('script', $script);
    $smarty->display('statistics.dwt');
}
