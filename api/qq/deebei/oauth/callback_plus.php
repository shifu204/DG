<?php
define('IN_ECS', true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_transaction.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_passport.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_debei.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_callback.php');
require_once("../../API/qqConnectAPI.php");
$qc = new QC();
$openid =  $qc->get_openid();
$user_info = $qc->get_user_info();

if (isset($user_info)) {
    $url = 'user.php?act=login_close_window';   
    //$user_name = $openid . '@tencent';
    $access_token = 'qq_'.$openid;
    $headimgurl = "";
    //用户QQ头像
    if(isset($user_info['figureurl_qq_1']) || isset($user_info['figureurl_qq_2'])){
        if(!empty($user_info['figureurl_qq_2'])){
            $headimgurl = $user_info['figureurl_qq_2'];
        } else {
            $headimgurl = $user_info['figureurl_qq_1'];
        }
    }
    if (check_third_user_exists($openid,'qq')){
        $GLOBALS['user']->set_session($openid, 'qq');
        $GLOBALS['user']->set_cookie($openid, null, 'qq');
        update_user_info();
        //获取用户头像是否更新了
        $sql = "SELECT headimgurl FROM " . $GLOBALS['ecs']->table("users_third_info") . " WHERE access_token = '$access_token'";
        $res = $GLOBALS['db']->getOne($sql);
        if($res != $headimgurl){
            //更新用户头像
            $update = array();
            $update['headimgurl'] = $headimgurl;
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("users_third_info"), $update, "UPDATE", " access_token = '$access_token' ");
        }
        if (getUserEmail()) {
            $is_exsit_email = true;
        }
    } else {
        $reg_date = gmtime();
        //$email     = !empty($_GET['email']) ? trim($_GET['email']) : 'no_email_' . $openid . '@qq.com';
        $ip = real_ip();
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table("users")
             . "(`user_name`,`user_type`, `nickname`, `reg_time`, `last_login`, `last_ip`, `ec_salt`)"
             . " VALUES ('$access_token' , 2 , '$user_info[nickname]', '$reg_date', '$reg_date', '$ip', '$user_info[nickname]')";
        $GLOBALS['db']->query($sql);
        $user_id = $GLOBALS['db']->insert_id();
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table("users_third_info") . 
               " (`user_id`, `access_token`, `headimgurl`)".
               " VALUES ($user_id, '$access_token', '$headimgurl')";
        $GLOBALS['db']->query($sql);
        $GLOBALS['user']->set_session($openid, 'qq');
        $GLOBALS['user']->set_cookie($openid, null, 'qq');
        //注册送红包
        registe_give_bonus($_SESSION['user_id']);
        //trace::trace_browse(TRACE_USER_REGISTER, $_SESSION['user_id'], TRACE_FROM_ECS, true);
        update_user_info();
        $_SESSION['register_type'] = 'new_qq_user';
    }  
    
    header('Location: http://www.deebei.net/' . $url); 
    exit;
}
