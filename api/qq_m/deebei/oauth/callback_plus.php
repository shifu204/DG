<?php
define('IN_ECS', true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_transaction.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_passport.php');
require_once("../../API/qqConnectAPI.php");
$qc = new QC();
$openid =  $qc->get_openid();
$user_info = $qc->get_user_info();

if (isset($user_info)) {
    $user_name = $openid . '@tencent';
    if (check_user_exits($user_name)){
        $GLOBALS['user']->set_session($user_name);
        $GLOBALS['user']->set_cookie($user_name);
        update_user_info();
        if (getUserEmail()) {
            $is_exsit_email = true;
        }
    } else {
        $reg_date = time();
        // 创建密码，无实际用途，仅为了不使密码为空之用
        $password = time(); 
        //$email     = !empty($_GET['email']) ? trim($_GET['email']) : 'no_email_' . $openid . '@qq.com';
        $ip = real_ip();
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table("users")
             . "(`user_type`, `user_name`, `nickname`, `password`, `reg_time`, `last_login`, `last_ip`, `ec_salt`)"
             . " VALUES (2, '$user_name', '$user_info[nickname]', '$password', '$reg_date', '$reg_date', '$ip', '$user_info[nickname]')";
        $GLOBALS['db']->query($sql);
           
        $GLOBALS['user']->set_session($user_name);
        $GLOBALS['user']->set_cookie($user_name);
        update_user_info();
    }
    $url = '';
    if (empty($is_exsit_email)) {
        $url = 'user.php?act=profile';
    }
    header('Location: http://www.deebei.net/' . $url); 
    exit;
}
