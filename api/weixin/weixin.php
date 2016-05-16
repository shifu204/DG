<?php
define('IN_ECS', true);
require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');
require('./cls_weixin.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_debei.php');
$act = isset($_REQUEST['act'])? trim($_REQUEST['act']) : 'default';

if($act == 'web_login'){
    $wx = new debei_weixin();
    //微信网页登录回调接口 
    if($user_info = $wx->web_respond()){
        $openid = $user_info->openid;
        $unionid = $user_info->base_info->unionid;
        $access_token = 'wx_'.$openid;
        if (check_third_user_exists($openid,'wx')){
            $GLOBALS['user']->set_session($openid, 'wx');
            $GLOBALS['user']->set_cookie($openid, null, 'wx');
            update_user_info();
            $url = 'user.php?act=login_close_window';
        } else {
            $reg_date = time();
            $ip = real_ip();
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table("users")
             . "(`user_name`,`user_type`, `nickname`, `reg_time`, `last_login`, `last_ip`, `ec_salt`)"
             . " VALUES ('$access_token' , 4 , '{$user_info->base_info->nickname}', '$reg_date', '$reg_date', '$ip', '{$user_info->base_info->nickname}')";
            $GLOBALS['db']->query($sql);
            $user_id = $GLOBALS['db']->insert_id();
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table("users_third_info") . 
               " (`user_id`, `access_token`, `headimgurl`, `unionid`)".
               " VALUES ($user_id, '$access_token', '{$user_info->base_info->headimgurl}', '$unionid')";
            $GLOBALS['db']->query($sql);
            $GLOBALS['user']->set_session($openid, 'wx');
            $GLOBALS['user']->set_cookie($openid, null, 'wx');
            //注册送红包
            registe_give_bonus($_SESSION['user_id']);
            //trace::trace_browse(TRACE_USER_REGISTER, $_SESSION['user_id'], TRACE_FROM_ECS, true);
            update_user_info();
            $_SESSION['register_type'] = 'new_wx_user';
        }       
        header('Location:http://' . $_SERVER['HTTP_HOST'] . '/' . $url); 
        exit;
    }
}