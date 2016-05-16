<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/../includes/init.php');
$act = isset($_REQUEST['act'])? trim($_REQUEST['act']) : 'default';
$config_file = dirname(__FILE__).'/51activity_config.php';

if($act == 'list'){
    //读取活动配置文件
    if(file_exists($config_file)){
        require($config_file);
    }
    $smarty->assign('top_users',$top_users);
    $smarty->assign('top_amount',$top_amount);
    $smarty->display('activity/51activity.html');
}
else if($_REQUEST['act'] == 'set_top_users'){
    $users = $_REQUEST['username'];
    $amount = $_REQUEST['amount'];
    if(!empty($users) && !empty($amount)){
        if($fopen = fopen($config_file, 'wb')){
            $content = '<?php '."\r\n";
            $content .= '$top_users = ' . var_export($users,true) .';'."\r\n" .
                        '$top_amount = ' . var_export($amount,true).';'."\r\n";
            fputs($fopen, $content);
            fclose($fopen);
        } else {
            sys_msg('配置文件不存在或者文件位置不正确', 0);
        }      
    }
    sys_msg('设置成功！', 0);
}