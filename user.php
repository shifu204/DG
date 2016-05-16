<?php

/**
 * ECSHOP 会员中心
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user.php 17217 2011-01-19 06:29:08Z liubo $
*/
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');
require_once(dirname(__FILE__) . '/includes/lib_shaidan.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');
include_once(ROOT_PATH . 'includes/lib_clips.php');

$user_id = $_SESSION['user_id'];
$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act='';
$smarty->assign('act',$action);

$pageSize = 10;
// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr =
array('login','act_login','register','act_register','act_edit_password','get_password','send_pwd_email','password', 'signin', 'add_tag', 'collect', 'return_to_cart', 'logout', 'email_list',
      'validate_email', 'send_hash_mail', 'order_query', 'is_registed', 'check_email','clear_history','qpassword_name', 'get_passwd_question', 'check_answer',
      'act_register_ajax','get_user_login_state','get_login_register_page','check_sms_code','user_mobile_email','check_mobile','delete_shaidan_img','query_login',
      'login_close_window','email_password','act_reset_password','act_proving_email','user_info','query_email_status','do_reset_password');

/* 显示页面的action列表 */
$ui_arr = array('register', 'login', 'profile', 'order_list', 'order_detail', 'address_list', 'collection_list',
'message_list', 'shaidan_list', 'tag_list', 'get_password', 'reset_password', 'booking_list', 'add_booking', 'account_raply',
'account_deposit', 'account_log', 'account_detail', 'act_account', 'pay', 'default', 'bonus', 'group_buy', 'group_buy_detail', 'affiliate', 'comment_list','validate_email','track_packages', 'transform_points','qpassword_name', 'get_passwd_question', 'check_answer','user_mobile_email',
        'email_password','do_reset_password');

/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
    if (!in_array($action, $not_login_arr))
    {
        if (in_array($action, $ui_arr))
        {
            /* 如果需要登录,并是显示页面的操作，记录当前操作，用于登录后跳转到相应操作
            if ($action == 'login')
            {
                if (isset($_REQUEST['back_act']))
                {
                    $back_act = trim($_REQUEST['back_act']);
                }
            }
            else
            {}*/
            if (!empty($_SERVER['QUERY_STRING']))
            {
                $back_act = 'user.php?' . strip_tags($_SERVER['QUERY_STRING']);
            }
            $action = 'login';
        }
        else
        {
            //未登录提交数据。非正常途径提交数据！
            die($_LANG['require_login']);
        }
    }
}

/* 如果是显示页面，对页面进行相应赋值 */
if (in_array($action, $ui_arr))
{
    assign_template();
    $position = assign_ur_here(0, $_LANG['user_center']);
    $smarty->assign('page_title', $position['title']); // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);
    $sql = "SELECT value FROM " . $ecs->table('shop_config') . " WHERE id = 419";
    $row = $db->getRow($sql);
    $car_off = $row['value'];
    $smarty->assign('car_off',       $car_off);
    /* 是否显示积分兑换 */
    if (!empty($_CFG['points_rule']) && unserialize($_CFG['points_rule']))
    {
        $smarty->assign('show_transform_points',     1);
    }
    $smarty->assign('helps',      get_shop_help());        // 网店帮助
    $smarty->assign('data_dir',   DATA_DIR);   // 数据目录
    $smarty->assign('action',     $action);
    $smarty->assign('lang',       $_LANG);
}

//用户中心欢迎页
if ($action == 'default')
{    
    include_once(ROOT_PATH .'includes/lib_clips.php');
    include_once(ROOT_PATH .'includes/lib_order.php');
    //需要显示的默认页
    if(isset($_REQUEST['default_page'])){
        $default_page = trim($_REQUEST['default_page']);
    } else {
        $default_page = 'my_order';
    }
    $smarty->assign('default_page',$default_page);
    $profile = get_user_profile($user_id); 
    $profile['profile']['total_used'] = price_format(get_user_total_used($user_id));
    $profile['profile']['last_login'] = local_date("Y-m-d",$profile['profile']['last_login']);
    $profile['profile']['rank_discount_header'] = get_rank_bar_header($profile['profile']);
    if($user_info['user_type'] > 0 ){
        $user_full = get_user_full($user_id);
        $profile['profile']['ori_id'] = $user_full['ori_id'];
    }
    //获取省列表
    $province = get_regions(1, 1);
    //帐户余额
    $surplus = get_user_surplus($user_id);
    $smarty->assign('surplus',$surplus);
    //有效红包个数
    $bonus_filter = array();
    $bonus_filter['get_total'] = 1;
    $bonus_filter['over_time'] = 0;
    $bonus_filter['is_used'] = 0;
    $total_bonus = get_bouns_list($user_id,$bonus_filter,0,0);
    /*获取绑定邮箱，绑定手机送积分状态*/
    if($user_info['email_validated'] == 1){
        $give_email_points = $GLOBALS['_CFG']['email_validate_points'];
    }
    if($user_info['mobile_validated'] == 1){
        $give_mobile_points = $GLOBALS['_CFG']['mobile_validate_points'];
    }
    $smarty->assign('give_email_points', $give_email_points);
    $smarty->assign('give_mobile_points', $give_mobile_points);
    /*获取用户订单状态数据*/
    $order_filter = array();
    $order_filter['get_total'] = true;
    $status = ' oi.pay_status <=1 AND oi.order_status NOT IN (2,3,4)'; //等待付款
    $order_filter['where'] = " AND $status ";
    $order_wait_pay = get_orders($order_filter); //等待付款
    
    $status = ' oi.pay_status = 2 AND oi.order_status NOT IN (2,3,4) AND oi.shipping_status != 2';
    $order_filter['where'] = " AND $status ";
    $order_wait_get = get_orders($order_filter); //等待收货
    
    $status = ' oi.pay_status = 2 AND oi.order_status NOT IN (2,3,4) AND oi.shipping_status = 2';
    $order_filter['where'] = " AND $status ";
    $order_finish = get_orders($order_filter); //已完成
    
    $status = ' oi.order_status = 2';
    $order_filter['where'] = " AND $status "; 
    $order_cancel = get_orders($order_filter); //已取消
    
    $smarty->assign('order_wait_pay', $order_wait_pay);
    $smarty->assign('order_wait_get', $order_wait_get);
    $smarty->assign('order_finish', $order_finish);
    $smarty->assign('order_cancel', $order_cancel);
    /*获取用户中心推荐晒单*/
    $recommend_shaidan = get_comment_shaidan_list(12, 0, array('status'=>1, 'is_shaidan'=>true, "is_recommend"=>1));
    
    /*获取用户浏览历史*/
    if(!empty($_COOKIE['ECS']['history'])){
        include_once(ROOT_PATH . 'includes/lib_goods.php');
        $goods_ids = explode(',', $_COOKIE['ECS']['history']);
        $history = array();
        foreach($goods_ids as $goods_id){
            $history[] = goods_info($goods_id);
        }
        $smarty->assign('history', $history);
    }
    $smarty->assign('bonus_num',$total_bonus);
    $smarty->assign('profile',$profile['profile']);
    $smarty->assign('extend_info_list',$profile['extend_info_list']);
    $smarty->assign('user_notice', $_CFG['user_notice']);
    $smarty->assign('prompt',      get_user_prompt($user_id));
    $smarty->assign('user_collects',get_user_collects($user_id));
    $smarty->assign('province',$province);
    $smarty->assign('shaidan_list', $recommend_shaidan);
    $hot_goods = get_recommend_goods("hot",'', 10);
    $smarty->assign('hot_goods',$hot_goods);
    if(isset($_REQUEST['is_ajax'])){
        $result['content'] = $smarty->fetch("library/user/my_profile.lbi");
        die(json_encode($result));
    }      
    $smarty->display('user_clips.dwt');
}

/*用户ajax注册*/
if($action=='act_register_ajax'){
    include_once(ROOT_PATH . 'includes/lib_passport.php');
    include_once('includes/cls_json.php');
    include_once('includes/lib_callback.php');
    $json = new JSON;
    $result = array();
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';

    $other['mobile_phone'] = isset($_POST['mobile_phone']) ? $_POST['mobile_phone'] : '';
    $other['nickname'] = isset($_POST['nickname']) ? trim($_POST['nickname']) : '';
    $username_count = 0;
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';
    
    if (strlen($username) < 3)
    {
        //show_message($_LANG['passport_js']['username_shorter']);
        $result['error'] = 1;
        $result['msg'] = $_LANG['passport_js']['username_shorter'];
        die($json->encode($result));
    }

    if (strlen($password) < 6)
    {
        //show_message($_LANG['passport_js']['password_shorter']);
        $result['error'] = 2;
        $result['msg'] = $_LANG['passport_js']['password_shorter'];
        die($json->encode($result));
    }

    if (strpos($password, ' ') > 0)
    {
        //show_message($_LANG['passwd_balnk']);
        $result['error'] = 3;
        $result['msg'] = $_LANG['passwd_balnk'];
        die($json->encode($result));
    }
    if(empty($email)){
        //$email = "user".time()."@deebei.net";
        //$email = false;
    }
    $mobile_check = $user->check_mobile($other['mobile_phone']);
    if ( $mobile_check != false)
    {   
        if($mobile_check == 1){
            $result['error'] = 4;
            $result['msg'] = '手机号已被绑定';
        } else if($mobile_check == 2){
            $result['error'] = 5;
            $result['msg'] = '手机号不能为空';
        } else if($mobile_check == 3){
            $result['error'] = 6;
            $result['msg'] = '手机号格式不正确';
        }
        
        die($json->encode($result));
    }
    
    if (register($username, $password, $email, $other) !== false)
    {
        /*注册送红包*/
        registe_give_bonus($_SESSION['user_id']);
        
        /* 写入密码提示问题和答案 */
        if (!empty($passwd_answer) && !empty($sel_question))
        {
            $sql = 'UPDATE ' . $ecs->table('users') . " SET `passwd_question`='$sel_question', `passwd_answer`='$passwd_answer'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
            $db->query($sql);
        }
        /* 判断是否需要自动发送注册邮件 */
        if ($GLOBALS['_CFG']['member_email_validate'] && $GLOBALS['_CFG']['send_verify_email'])
        {
            send_regiter_hash($_SESSION['user_id']);
        }
        /*记录网页浏览操作：用户注册*/
        //trace::trace_browse(TRACE_USER_REGISTER, $_SESSION['user_id'], TRACE_FROM_ECS, true);
        
        $ucdata = empty($user->ucdata)? "" : $user->ucdata;
        //show_message(sprintf($_LANG['register_success'], $username . $ucdata), array($_LANG['back_up_page'], $_LANG['profile_lnk']), array($back_act, 'user.php'), 'info');
        $result['error'] = 0;
        $result['msg'] = sprintf($_LANG['register_success'], $username . $ucdata);
        $user_info = get_user_info();
        $smarty->assign('user_info', $user_info);
        $result['user_info'] = $user_info;
        $result['content'] = $smarty->fetch('library/member_login_div.lbi');
        //百度自定义统计代码
        //$result['script'] = reg_callback();
        
        die($json->encode($result));
    }
    else
    {
        $result['error'] = 4;
        $result['msg'] = $_LANG['sign_up'];
        die($json->encode($result));
    }
}

/* 验证用户注册邮件 */
elseif ($action == 'validate_email')
{
    $hash = empty($_GET['hash']) ? '' : trim($_GET['hash']);
    if ($hash)
    {
        include_once(ROOT_PATH . 'includes/lib_passport.php');
        $id = register_hash('decode', $hash);
        if ($id > 0)
        {
            $sql = "UPDATE " . $ecs->table('users') . " SET is_validated = 1 WHERE user_id='$id'";
            $db->query($sql);
            $sql = 'SELECT user_name, email FROM ' . $ecs->table('users') . " WHERE user_id = '$id'";
            $row = $db->getRow($sql);
            show_message(sprintf($_LANG['validate_ok'], $row['user_name'], $row['email']),$_LANG['profile_lnk'], 'user.php');
        }
    }
    show_message($_LANG['validate_fail']);
}

/* 验证用户注册用户名是否可以注册 */
elseif ($action == 'is_registed')
{
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $username = isset($_REQUEST['username'])? trim($_REQUEST['username']):'';
    
    if(!empty($username)){  
        if ($user->check_user($username) || admin_registered($username))
        {
            die('true');
        }
        else
        {
            die('false');
        }
    } else {
        echo 'false';
    }
}

/* 验证用户邮箱地址是否被注册 */
/**
 * 如果返回false，则说明不能用此邮箱注册或绑定此邮箱
 */
elseif($action == 'check_email')
{
    $email = trim($_REQUEST['email']);
    $reverse = $_REQUEST['reverse'];
    if ($user->check_email($email))
    {
        if($reverse == 1){
            die('true');
        }
        die('false');
    }
    if($reverse == 1){
        die('该邮箱并未注册');
    }
    die('true');
}

/* 验证用户手机号码是否被注册 */
elseif($action == 'check_mobile')
{
    $mobile = isset($_REQUEST['mobile_phone'])? trim($_REQUEST['mobile_phone']):'';
    if(!is_mobile_phone($mobile)){
        die("手机号码不正确");
    }
    if(!empty($mobile)){  
        if ($user->check_mobile($mobile))
        {
            die('手机号已被绑定');
        }
        else
        {
            die('true');
        }
    } else {
        echo 'false';
    }
}
/*检测用户名是否可以被绑定*/
elseif($action == 'check_bindname'){
    include_once(ROOT_PATH . 'includes/lib_passport.php');
    $username = isset($_REQUEST['bindname'])? trim($_REQUEST['bindname']):'';
    if(!empty($username)){  
        if ($user->check_user($username) && !(admin_registered($username)))
        {
            die('true');
        }
        else
        {
            die('不存在该用户');
        }
    } else {
        die('不存在该用户');
    }
}
/* 用户登录界面 */
elseif ($action == 'login')
{
    $back_act = isset($_GET['back_act']) ? trim($_GET['back_act']) : '';
    if(empty($user_id)){
        if (empty($back_act))
        {
            if (empty($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
            {
                $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
            }
            else
            {
                $back_act = 'user.php';
            }

        }

        $captcha = intval($_CFG['captcha']);
        if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
        {
            $GLOBALS['smarty']->assign('enabled_captcha', 1);
            $GLOBALS['smarty']->assign('rand', mt_rand());
        }
        $smarty->assign('default_page','login');
        $smarty->assign('back_act', $back_act);
        //不使用ajax方式登录，通过网页表单提交到地址登录
        if(!empty($back_act)){
            $smarty->assign('back_act', $back_act);
        }
        $smarty->display('user_passport.dwt');
    } else {
        header("Location:user.php");
    }
}

/* 处理会员的登录 */
elseif ($action == 'act_login')
{
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';


    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
    {
        if (empty($_POST['captcha']))
        {
            show_message($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'user.php', 'error');
        }

        /* 检查验证码 */
        include_once('includes/cls_captcha.php');

        $validator = new captcha();
        $validator->session_word = 'captcha_login';
        if (!$validator->check_word($_POST['captcha']))
        {
            show_message($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'user.php', 'error');
        }
    }

    if ($user->login($username, $password,isset($_POST['remember'])))
    {
        update_user_info();
        recalculate_price();

        $ucdata = isset($user->ucdata)? $user->ucdata : '';
        if(isset($GLOBALS['_SERVER']['HTTP_REFERER'])){
            ecs_header("Location:".$GLOBALS['_SERVER']['HTTP_REFERER']);
            die();
        } else {
            show_message($_LANG['login_success'] . $ucdata , array($_LANG['back_up_page'], $_LANG['profile_lnk']), array($back_act,'user.php'), 'info');
        }
    }
    else
    {
        $_SESSION['login_fail'] ++ ;
        show_message($_LANG['login_failure'], $_LANG['relogin_lnk'], 'user.php', 'error');
    }
}

/* 处理 ajax 的登录请求 */
elseif ($action == 'signin')
{
    include_once('includes/cls_json.php');
    $json = new JSON;

    $username = !empty($_POST['username']) ? json_str_iconv(trim($_POST['username'])) : '';
    $password = !empty($_POST['password']) ? trim($_POST['password']) : '';
    $captcha = !empty($_POST['captcha']) ? json_str_iconv(trim($_POST['captcha'])) : '';
    $remember = isset($_POST['remember_me']) ? true : null ;
    $result   = array('error' => 0, 'content' => '');

    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
    {
        if (empty($captcha))
        {
            $result['error']   = 1;
            $result['content'] = $_LANG['invalid_captcha'];
            die($json->encode($result));
        }

        /* 检查验证码 */
        include_once('includes/cls_captcha.php');

        $validator = new captcha();
        $validator->session_word = 'captcha_login';
        if (!$validator->check_word($_POST['captcha']))
        {

            $result['error']   = 1;
            $result['content'] = $_LANG['invalid_captcha'];
            die($json->encode($result));
        }
    }
    if ($user->login($username, $password, $remember))
    {
        update_user_info();  //更新用户信息
        recalculate_price(); // 重新计算购物车中的商品价格      
        $smarty->assign('user_info', get_user_info());
        $ucdata = empty($user->ucdata)? "" : $user->ucdata;
        $result['ucdata'] = $ucdata;
        $result['content'] = $smarty->fetch('library/member_login_div.lbi');
    }
    else
    {
        $_SESSION['login_fail']++;
        if ($_SESSION['login_fail'] > 2)
        {
            $smarty->assign('enabled_captcha', 1);
            $result['html'] = $smarty->fetch('library/member_login_div.lbi');
        }
        $result['error']   = 1;
        $result['content'] = $_LANG['login_failure'];
    }
    die($json->encode($result));
}

/* 退出会员中心 */
elseif ($action == 'logout')
{
    if ((!isset($back_act)|| empty($back_act)) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
    {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }

    $user->logout();
    $ucdata = empty($user->ucdata)? "" : $user->ucdata;
    //show_message($_LANG['logout'] . $ucdata, array($_LANG['back_up_page'], $_LANG['back_home_lnk']), array($back_act, 'index.php'), 'info');
    header("Location:index.php");
}

elseif ($action == 'logout_ajax'){
    include_once('includes/cls_json.php');
    $json = new JSON;
    if ((!isset($back_act)|| empty($back_act)) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
    {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }

    $user->logout();
    $ucdata = empty($user->ucdata)? "" : $user->ucdata;   
    $result['error']   = 0;
    $smarty->assign('user_info',array());
    $result['content'] = $smarty->fetch('library/member_info.lbi');
    $result['member_login_div'] = $smarty->fetch('library/member_login_div.lbi');
    die($json->encode($result));
}

/* 修改个人资料的处理 */
elseif ($action == 'act_edit_profile')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $birthday = trim($_POST['birthdayYear']) .'-'. trim($_POST['birthdayMonth']) .'-'.
    trim($_POST['birthdayDay']);
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $other['msn'] = $msn = isset($_POST['extend_field1']) ? trim($_POST['extend_field1']) : '';
    $other['qq'] = $qq = isset($_POST['extend_field2']) ? trim($_POST['extend_field2']) : '';
    $other['office_phone'] = $office_phone = isset($_POST['extend_field3']) ? trim($_POST['extend_field3']) : '';
    $other['home_phone'] = $home_phone = isset($_POST['extend_field4']) ? trim($_POST['extend_field4']) : '';
    $other['mobile_phone'] = $mobile_phone = isset($_POST['extend_field5']) ? trim($_POST['extend_field5']) : '';
    $sel_question = empty($_POST['sel_question']) ? '' : compile_str($_POST['sel_question']);
    $passwd_answer = isset($_POST['passwd_answer']) ? compile_str(trim($_POST['passwd_answer'])) : '';

    /* 更新用户扩展字段的数据 */
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);

    foreach ($fields_arr AS $val)       //循环更新扩展用户信息
    {
        $extend_field_index = 'extend_field' . $val['id'];
        if(isset($_POST[$extend_field_index]))
        {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr(htmlspecialchars($_POST[$extend_field_index]), 0, 99) : htmlspecialchars($_POST[$extend_field_index]);
            $sql = 'SELECT * FROM ' . $ecs->table('reg_extend_info') . "  WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            if ($db->getOne($sql))      //如果之前没有记录，则插入
            {
                $sql = 'UPDATE ' . $ecs->table('reg_extend_info') . " SET content = '$temp_field_content' WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            }
            else
            {
                $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . " (`user_id`, `reg_field_id`, `content`) VALUES ('$user_id', '$val[id]', '$temp_field_content')";
            }
            $db->query($sql);
        }
    }

    /* 写入密码提示问题和答案 */
    if (!empty($passwd_answer) && !empty($sel_question))
    {
        $sql = 'UPDATE ' . $ecs->table('users') . " SET `passwd_question`='$sel_question', `passwd_answer`='$passwd_answer'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
        $db->query($sql);
    }

    if (!empty($office_phone) && !preg_match( '/^[\d|\_|\-|\s]+$/', $office_phone ) )
    {
        show_message($_LANG['passport_js']['office_phone_invalid']);
    }
    if (!empty($home_phone) && !preg_match( '/^[\d|\_|\-|\s]+$/', $home_phone) )
    {
         show_message($_LANG['passport_js']['home_phone_invalid']);
    }
    if (!is_email($email))
    {
        show_message($_LANG['msg_email_format']);
    }
    if (!empty($msn) && !is_email($msn))
    {
         show_message($_LANG['passport_js']['msn_invalid']);
    }
    if (!empty($qq) && !preg_match('/^\d+$/', $qq))
    {
         show_message($_LANG['passport_js']['qq_invalid']);
    }
    if (!empty($mobile_phone) && !preg_match('/^[\d-\s]+$/', $mobile_phone))
    {
        show_message($_LANG['passport_js']['mobile_phone_invalid']);
    }


    $profile  = array(
        'user_id'  => $user_id,
        'nickname' => isset($_POST['nickname']) ? trim($_POST['nickname']) : '',
        'email'    => isset($_POST['email']) ? trim($_POST['email']) : '',
        'sex'      => isset($_POST['sex'])   ? intval($_POST['sex']) : 0,
        'birthday' => $birthday,
        'other'    => isset($other) ? $other : array()
        );


    if (edit_profile($profile))
    {
        show_message($_LANG['edit_profile_success'], $_LANG['profile_lnk'], 'user.php?act=profile', 'info');
    }
    else
    {
        if ($user->error == ERR_EMAIL_EXISTS)
        {
            $msg = sprintf($_LANG['email_exist'], $profile['email']);
        }
        else
        {
            $msg = $_LANG['edit_profile_failed'];
        }
        show_message($msg, '', '', 'info');
    }
}

/* 修改会员密码 */
elseif ($action == 'act_edit_password')
{
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $user_id      = isset($_POST['uid'])  ? intval($_POST['uid']) : $user_id;
    $code         = isset($_POST['code']) ? trim($_POST['code'])  : '';

    if (strlen($new_password) < 6)
    {
        show_message($_LANG['passport_js']['password_shorter']);
    }

    $user_info = $user->get_profile_by_id($user_id); //论坛记录

    if (($user_info && (!empty($code) && md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']) == $code)) || ($_SESSION['user_id']>0 && $_SESSION['user_id'] == $user_id && $user->check_user($_SESSION['user_name'], $old_password)))
    {
		
        if ($user->edit_user(array('username'=> (empty($code) ? $_SESSION['user_name'] : $user_info['user_name']), 'old_password'=>$old_password, 'password'=>$new_password), empty($code) ? 0 : 1))
        {
            $sql="UPDATE ".$ecs->table('users'). "SET `ec_salt`='0' WHERE user_id= '".$user_id."'";
            $db->query($sql);
            $user->logout();
            show_message($_LANG['edit_password_success'], $_LANG['relogin_lnk'], 'user.php?act=login', 'info');
        }
        else
        {
            show_message($_LANG['edit_password_failure'], $_LANG['back_page_up'], '', 'info');
        }
    }
    else
    {
        show_message($_LANG['edit_password_failure'], $_LANG['back_page_up'], '', 'info');
    }

}

/* 添加一个红包 */
elseif ($action == 'act_add_bonus')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $bouns_sn = isset($_POST['bonus_sn']) ? intval($_POST['bonus_sn']) : '';

    if (add_bonus($user_id, $bouns_sn))
    {
        show_message($_LANG['add_bonus_sucess'], $_LANG['back_up_page'], 'user.php?act=bonus', 'info');
    }
    else
    {
        $err->show($_LANG['back_up_page'], 'user.php?act=bonus');
    }
}


/* 取消订单 */
elseif ($action == 'ajax_cancel_order')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $result = array();
    $result['error'] = '';
    if (cancel_order($order_id, $user_id))
    {
        
    }
    else
    {
        $result['error'] = 1;
        $result['content'] = '不能取消订单。';
    }
    die(json_encode($result));
}
/*删除订单*/
elseif($action == 'ajax_delete_order'){
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $result = array();
    $result['error'] = '';
    if(!delete_order($order_id,$user_id)){
        $result['error'] = 1;
    }
    die(json_encode($result));
}
/*确认收货*/
elseif ($action == 'ajax_affirm_received')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $result = array();
    $result['error'] = '';
    if (affirm_received($order_id, $user_id))
    {

    }
    else
    {
        $result['error'] = 1;
        $result['content'] = '确认收货失败。';
    }
    die(json_encode($result));
}
/*评论订单列表*/
elseif ($action == 'ajax_comment_order')
{
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $order_filter = array();
    $order_filter['where'] = " AND oi.order_id = ".$order_id;
    $order = get_orders($order_filter);
    $result = array();
    $result['error'] = '';
    if(empty($order) && $order['user_id'] != $user_id){
        //订单不存在
        $result['error'] = 1;
        die(json_encode($result));
    }
    
    if($order['is_commented'] != 0){
        //已经评论了
        $result['error'] = 2;
        die(json_encode($result));
    }   
    //获取订单评论界面
    $smarty->assign("order",$order[$order_id]);
    $result['content'] = $smarty->fetch("library/user/user_comment_form.lbi");
    die(json_encode($result));
}

/* 确认收货 */
elseif ($action == 'affirm_received')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    if (affirm_received($order_id, $user_id))
    {
        ecs_header("Location: user.php?act=order_list\n");
        exit;
    }
    else
    {
        $err->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }
}

/* 会员退款申请界面 */
elseif ($action == 'account_raply')
{
    $smarty->display('user_transaction.dwt');
}

/* 合并订单 */
elseif ($action == 'merge_order')
{
    include_once(ROOT_PATH .'includes/lib_transaction.php');
    include_once(ROOT_PATH .'includes/lib_order.php');
    $from_order = isset($_POST['from_order']) ? trim($_POST['from_order']) : '';
    $to_order   = isset($_POST['to_order']) ? trim($_POST['to_order']) : '';
    if (merge_user_order($from_order, $to_order, $user_id))
    {
        show_message($_LANG['merge_order_success'],$_LANG['order_list_lnk'],'user.php?act=order_list', 'info');
    }
    else
    {
        $err->show($_LANG['order_list_lnk']);
    }
}
/* 将指定订单中商品添加到购物车 */
elseif ($action == 'return_to_cart')
{
    include_once(ROOT_PATH .'includes/cls_json.php');
    include_once(ROOT_PATH .'includes/lib_transaction.php');
    $json = new JSON();

    $result = array('error' => 0, 'message' => '', 'content' => '');
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    if ($order_id == 0)
    {
        $result['error']   = 1;
        $result['message'] = $_LANG['order_id_empty'];
        die($json->encode($result));
    }

    if ($user_id == 0)
    {
        /* 用户没有登录 */
        $result['error']   = 1;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    }

    /* 检查订单是否属于该用户 */
    $order_user = $db->getOne("SELECT user_id FROM " .$ecs->table('order_info'). " WHERE order_id = '$order_id'");
    if (empty($order_user))
    {
        $result['error'] = 1;
        $result['message'] = $_LANG['order_exist'];
        die($json->encode($result));
    }
    else
    {
        if ($order_user != $user_id)
        {
            $result['error'] = 1;
            $result['message'] = $_LANG['no_priv'];
            die($json->encode($result));
        }
    }

    $message = return_to_cart($order_id);

    if ($message === true)
    {
        $result['error'] = 0;
        $result['message'] = $_LANG['return_to_cart_success'];
        die($json->encode($result));
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = $_LANG['order_exist'];
        die($json->encode($result));
    }

}

/* 编辑使用余额支付的处理 */
elseif ($action == 'act_edit_surplus')
{
    /* 检查是否登录 */
    if ($_SESSION['user_id'] <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单号 */
    $order_id = intval($_POST['order_id']);
    if ($order_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查余额 */
    $surplus = floatval($_POST['surplus']);
    if ($surplus <= 0)
    {
        $err->add($_LANG['error_surplus_invalid']);
        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
    }

    include_once(ROOT_PATH . 'includes/lib_order.php');

    /* 取得订单 */
    $order = order_info($order_id);
    if (empty($order))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单用户跟当前用户是否一致 */
    if ($_SESSION['user_id'] != $order['user_id'])
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单是否未付款，检查应付款金额是否大于0 */
    if ($order['pay_status'] != PS_UNPAYED || $order['order_amount'] <= 0)
    {
        $err->add($_LANG['error_order_is_paid']);
        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
    }

    /* 计算应付款金额（减去支付费用） */
    $order['order_amount'] -= $order['pay_fee'];

    /* 余额是否超过了应付款金额，改为应付款金额 */
    if ($surplus > $order['order_amount'])
    {
        $surplus = $order['order_amount'];
    }

    /* 取得用户信息 */
    $user = user_info($_SESSION['user_id']);

    /* 用户帐户余额是否足够 */
    if ($surplus > $user['user_money'] + $user['credit_line'])
    {
        $err->add($_LANG['error_surplus_not_enough']);
        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
    }

    /* 修改订单，重新计算支付费用 */
    $order['surplus'] += $surplus;
    $order['order_amount'] -= $surplus;
    if ($order['order_amount'] > 0)
    {
        $cod_fee = 0;
        if ($order['shipping_id'] > 0)
        {
            $regions  = array($order['country'], $order['province'], $order['city'], $order['district']);
            $shipping = shipping_area_info($order['shipping_id'], $regions);
            if ($shipping['support_cod'] == '1')
            {
                $cod_fee = $shipping['pay_fee'];
            }
        }

        $pay_fee = 0;
        if ($order['pay_id'] > 0)
        {
            $pay_fee = pay_fee($order['pay_id'], $order['order_amount'], $cod_fee);
        }

        $order['pay_fee'] = $pay_fee;
        $order['order_amount'] += $pay_fee;
    }

    /* 如果全部支付，设为已确认、已付款 */
    if ($order['order_amount'] == 0)
    {
        if ($order['order_status'] == OS_UNCONFIRMED)
        {
            $order['order_status'] = OS_CONFIRMED;
            $order['confirm_time'] = gmtime();
        }
        $order['pay_status'] = PS_PAYED;
        $order['pay_time'] = gmtime();
    }
    $order = addslashes_deep($order);
    update_order($order_id, $order);

    /* 更新用户余额 */
    $change_desc = sprintf($_LANG['pay_order_by_surplus'], $order['order_sn']);
    log_account_change($user['user_id'], (-1) * $surplus, 0, 0, 0, $change_desc);

    /* 跳转 */
    ecs_header('Location: user.php?act=order_detail&order_id=' . $order_id . "\n");
    exit;
}

/* 编辑使用余额支付的处理 */
elseif ($action == 'act_edit_payment')
{
    /* 检查是否登录 */
    if ($_SESSION['user_id'] <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查支付方式 */
    $pay_id = intval($_POST['pay_id']);
    if ($pay_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    include_once(ROOT_PATH . 'includes/lib_order.php');
    $payment_info = payment_info($pay_id);
    if (empty($payment_info))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单号 */
    $order_id = intval($_POST['order_id']);
    if ($order_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得订单 */
    $order = order_info($order_id);
    if (empty($order))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单用户跟当前用户是否一致 */
    if ($_SESSION['user_id'] != $order['user_id'])
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单是否未付款和未发货 以及订单金额是否为0 和支付id是否为改变*/
    if ($order['pay_status'] != PS_UNPAYED || $order['shipping_status'] != SS_UNSHIPPED || $order['goods_amount'] <= 0 || $order['pay_id'] == $pay_id)
    {
        ecs_header("Location: user.php?act=order_detail&order_id=$order_id\n");
        exit;
    }

    $order_amount = $order['order_amount'] - $order['pay_fee'];
    $pay_fee = pay_fee($pay_id, $order_amount);
    $order_amount += $pay_fee;

    $sql = "UPDATE " . $ecs->table('order_info') .
           " SET pay_id='$pay_id', pay_name='$payment_info[pay_name]', pay_fee='$pay_fee', order_amount='$order_amount'".
           " WHERE order_id = '$order_id'";
    $db->query($sql);

    /* 跳转 */
    ecs_header("Location: user.php?act=order_detail&order_id=$order_id\n");
    exit;
}

/* ajax 发送验证邮件 */
elseif ($action == 'send_hash_mail')
{
    include_once(ROOT_PATH .'includes/cls_json.php');
    include_once(ROOT_PATH .'includes/lib_passport.php');
    $json = new JSON();

    $result = array('error' => 0, 'message' => '', 'content' => '');

    if ($user_id == 0)
    {
        /* 用户没有登录 */
        $result['error']   = 1;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    }

    if (send_regiter_hash($user_id))
    {
        $result['message'] = $_LANG['validate_mail_ok'];
        die($json->encode($result));
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = $GLOBALS['err']->last_message();
    }

    die($json->encode($result));
}

/* 清除商品浏览历史 */
elseif ($action == 'clear_history')
{
    setcookie('ECS[history]',   '', 1);
} 
else if($action == 'get_user_login_state'){
    $result = array();
    if(empty($user_info['user_id'])){
        $result['error'] = 1;
    } else {
        $result['error'] = '';
    }
    die(json_encode($result));
}
/*获取用户订单*/
else if($action == 'ajax_get_orders'){
    include_once(ROOT_PATH .'includes/lib_order.php');
    $result = array();
    $pageSize = 5;
    $order_status = isset($_REQUEST['order_status'])?intval($_REQUEST['order_status']):0;
    $page = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
    $add_time = $status = '';

    //等待付款
    if($order_status == 1){
        $status = ' oi.pay_status <=1 AND oi.order_status NOT IN (2,3,4)';
    }
    //等待收货
    else if($order_status == 2){
        $status = ' oi.pay_status = 2 AND oi.order_status NOT IN (2,3,4) AND oi.shipping_status != 2';
    }
    //已完成
    else if($order_status == 3){
        $status = ' oi.pay_status = 2 AND oi.order_status NOT IN (2,3,4) AND oi.shipping_status = 2';
    }
    //已取消
    else if($order_status == 4){
        $status = ' oi.order_status = 2';
    }
    $where = '';
    if(!empty($order_time)){
        $where .= ' AND '.$order_time;
    }
    if(!empty($status)){
        $where .= ' AND '.$status;
    }
    $filter = array();
    if(!empty($where)){
        $filter['where'] = $where;
    }
    $filter['get_total'] = 1;
    $total_orders = get_orders($filter);   
    unset($filter['get_total']);
    $filter['limit'] = $pageSize;
    $filter['offset'] = ($page-1)*$filter['limit'];
    $pager = get_ajax_pager('get_order_data', array(), $total_orders,$page,$pageSize);
    $orders = get_orders($filter);
    $smarty->assign('orders',$orders);
    $smarty->assign('pager',$pager);
    $smarty->assign('page',$page);
    $smarty->assign('order_status',$order_status);
    $content = $smarty->fetch("library/user/my_order.lbi");
    $result['content'] = $content;
    die(json_encode($result));
}   
/*提交评论*/
else if($action == 'ajax_submit_comment'){
    include_once(ROOT_PATH .'includes/lib_order.php');
    $result = array();
    $result['error'] = '';
    $order_id = isset($_GET['order_id'])?intval($_GET['order_id']):0;
    if($order_id == 0){
        //订单不存在
        $result['error'] = 1;
        die(json_encode($result));
    }
    $order_info = order_info($order_id);
    if($order_info['user_id'] != $user_id){
        //该订单不属于该用户
        $result['error'] = 2;
        die(json_encode($result));
    }
    if($order_info['is_commented'] == 1){
        //该订单已经评论了
        $result['error'] = 3;
        die(json_encode($result));
    }
    $comment_data = $_POST['comment_data'];
    $time = gmtime();
    $ip = real_ip();
    if(!empty($comment_data)){
        foreach($comment_data as $ck=>$cv){
            $goods_id = str_replace("goods_id_", '', $ck);
            $insert = $cv;
            $insert['comment_type'] = 0 ; 
            $insert['id_value'] = $goods_id ; 
            $insert['user_name'] = $user_info['user_name'];
            $insert['add_time'] = $time;
            $insert['status'] = 0;
            $insert['user_id'] = $user_id;
            $insert['msg_id '] = 0;
            $insert['ip_address '] = $ip;
            //插入数据库
            $res = $GLOBALS['cidb']->insert('comment',$insert);
        }
        //更新对应的order评论状态
        if($GLOBALS['cidb']->where(array('order_id'=>$order_id))->limit(1)->update('order_info',array('is_commented'=>1))){
            die(json_encode($result));
        } else {
            //更新数据库失败
            $result['error'] = 4;
            die(json_encode($result));
        }
    }
}
/*添加晒单界面*/
else if($action == 'ajax_shaidan_order'){
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $order_filter = array();
    $order_filter['where'] = " AND oi.order_id = ".$order_id;
    $order = get_orders($order_filter);
    $result = array();
    $result['error'] = '';
    if(empty($order) && $order['user_id'] != $user_id){
        //订单不存在
        $result['error'] = 1;
        die(json_encode($result));
    }
    
    if($order['is_shaidan'] != 0){
        //已经评论了
        $result['error'] = 2;
        die(json_encode($result));
    }
    //为每个goods_id分配一个token值
    foreach ($order[$order_id]['goods'] as $gk=>$goods){
        $token = $user_id."_".$order[$order_id]['goods'][$gk]['goods_id']."_".$order_id;
        $order[$order_id]['goods'][$gk]['token'] = $token;
        //查找已经上传了的图片
        $images = $GLOBALS['cidb']->select("shaidan_img,img_id")->where(array("token"=>$token))->get("shaidan_img")->result_array();
        $order[$order_id]['goods'][$gk]['images'] = $images;
    }
    //获取订单评论界面
    $smarty->assign("order",$order[$order_id]);
    $result['content'] = $smarty->fetch("library/user/user_shaidan_form.lbi");
    die(json_encode($result));
}
// 上传晒单图片
elseif ($action == 'upload_shaidan_img') {
    $msg_id = isset($_REQUEST['shaidan_img']) ? $_REQUEST['shaidan_img'] : '';
    $shaidan_img = isset($_REQUEST['shaidan_img']) ? $_REQUEST['shaidan_img'] : '';
    $token       = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
    $sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('shaidan_img')
         . ' VALUES (0, \'' . $shaidan_img . '\', \'' . $token . '\')';
    FB::log($sql);
    if ($GLOBALS['db']->query($sql)) {
        echo $GLOBALS['db']->insert_id();
    }
}
//删除晒单照片
elseif($action == 'delete_shaidan_img'){
    $img_id = isset($_REQUEST['img_id'])?intval($_REQUEST['img_id']):0;
    if(!empty($img_id)){
        $img = $GLOBALS['cidb']->where('img_id = '.$img_id)->get("shaidan_img")->row_array();
        $img_path = $img['shaidan_img'];      
        if(substr(ROOT_PATH, strlen(ROOT_PATH) - 1) == '/'){
            if(substr($img_path,0,1) == '/'){
                $img_path = substr($img_path,1);
            }
        }
        $result = @unlink(ROOT_PATH.$img_path);
        $result1 = @unlink(str_replace('normal', 'source', ROOT_PATH.$img_path));
        $result2 = @unlink(str_replace('normal', 'thumb', ROOT_PATH.$img_path));
        if($result){
            $GLOBALS['cidb']->where('img_id = '.$img_id)->limit(1)->delete("shaidan_img");
        }else {
            echo 'false';
        }
    }
}
/*插入晒单*/
elseif($action == 'submit_shaidan_order'){
    include_once(ROOT_PATH .'includes/lib_order.php');
    $result = array();
    $result['error'] = '';
    $order_id = isset($_GET['order_id'])?intval($_GET['order_id']):0;
    if($order_id == 0){
        //订单不存在
        $result['error'] = 1;
        die(json_encode($result));
    }
    $order_info = order_info($order_id);
    if($order_info['user_id'] != $user_id){
        //该订单不属于该用户
        $result['error'] = 2;
        die(json_encode($result));
    }
    if($order_info['is_shaidan'] == 1){
        //该订单已经评论了
        $result['error'] = 3;
        die(json_encode($result));
    }
    $shaidan_data = $_POST['shaidan_data'];   
    $time = gmtime();
    $ip = real_ip();
    $shaidan_result = true;
    
    if(!empty($shaidan_data)){
        foreach($shaidan_data as $ck=>$cv){
            $goods_id = str_replace("goods_id_", '', $ck);
            
            $comment['content'] =trim($cv['content']);
            $comment['title'] = trim($cv['title']);
            $comment['comment_rank'] = intval($cv['goods_rank']);
            $comment['token'] = trim($cv['token']);          
            $comment['comment_type'] = 0 ; 
            //查找是否有上传图片
            $imgs = get_shaidan($comment['token']);
            if(!empty($imgs)){
                $comment['comment_type'] = 1;
            }
            $comment['id_value'] = $goods_id ; 
            $comment['user_name'] = empty($user_info['nickname']) ? $user_info['user_name'] : $user_info['nickname'];
            $comment['add_time'] = $time;
            $comment['status'] = 0;
            $comment['user_id'] = $user_id;
            $comment['ip_address '] = $ip;
            $comment['parent_id'] = 0;
            //插入数据库
            $res = $GLOBALS['cidb']->insert('comment',$comment);   
        }
        //更新对应的order状态
        $GLOBALS['cidb']->where('order_id = '.$order_id)->limit(1)->update('order_info',array('is_commented'=>1));
        die(json_encode($result));
    } else {
        //晒单数据为空 
        $result['error'] = 5;
        die(json_encode($result));
    }
}
/*获取评论资料*/
elseif($action == 'ajax_get_comment'){
    $result = array();
    $pageSize = 5;
    $comment_status = isset($_REQUEST['comment_status'])?intval($_REQUEST['comment_status']):1;
    $page = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
    //$add_time = $status = '';
    //$now_time = gmtime();
    $offset = ($page - 1) * $pageSize;
    $comment_filter = array();
    $comment_filter['comment_status'] = $comment_status;
    $comment_filter['get_total'] = 1;
    $comment_filter['user_id'] = $user_id;
    $total_comments = get_comment_shaidan_list(0,0,$comment_filter);
    unset($comment_filter['get_total']);
    $comments = get_comment_shaidan_list($pageSize,$offset,$comment_filter);
    $pager = get_ajax_pager('get_comment_data', array(), $total_comments,$page,$pageSize);
    $smarty->assign('comments',$comments);
    $smarty->assign('pager',$pager);
    $smarty->assign('page',$page);
    $smarty->assign('comment_status',$comment_status);
    $result['content'] = $smarty->fetch("library/user/my_comment.lbi");
    die(json_encode($result));
}
/*评论详情*/
elseif($action == 'ajax_comment_detail'){
    $is_reply = isset($_REQUEST['is_reply'])? true:false;
    $comment_id = isset($_REQUEST['comment_id'])?intval($_REQUEST['comment_id']):0;
    $comment_filter['comment_id'] = $comment_id;
    $comment = get_comment_shaidan_list(1,0,$comment_filter);
    if(!empty($comment)){
        $comment = $comment[0];
        $comment['replys'] = get_comment_replys($comment_id);
        $smarty->assign('is_reply', $is_reply);
        $smarty->assign('comment',$comment);
        $content = $smarty->fetch('library/user/my_comment_detail.lbi');
    }
    die(json_encode($content));
}
/*修改评论*/
elseif($action == 'ajax_edit_comment'){
    $comment_id = isset($_REQUEST['comment_id'])?intval($_REQUEST['comment_id']): 0;
    $comment_filter['comment_id'] = $comment_id;
    $comment_filter['user_id'] = $user_id;
    $comment = get_comment_shaidan_list(1,0,$comment_filter);
    $result = array();
    $result['error'] = '';
    if(!empty($comment)){
        $comment = $comment[0];   
        if($comment['status'] == 0){
            //如果评论的token为空，则需要生成一个token，否则不能上传图片
            if(empty($comment['token'])){
                /*评论token*/
                $comment['token'] = $user_id . "_" . $comment['comment_id']."_" . gmtime();
                //更新该评论的token
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('comment'),array('token'=>$comment['token']), 'UPDATE', 'comment_id = '.$comment['comment_id']);
            }
            $smarty->assign('comment',$comment);
            $content = $smarty->fetch('library/user/user_edit_shaidan.lbi');
            $result['content'] = $content;
        } else {
            $result['error'] = 1;
        }
    }
    die(json_encode($result));
}
/*修改晒单*/
elseif($action == 'ajax_submit_edit_shaidan'){
    $msg_id = isset($_REQUEST['msg_id'])?intval($_REQUEST['msg_id']):0;
    $result = array();
    $result['error'] = '';
    if(!empty($msg_id)){
        $update = array();
        $update['title'] = trim($_REQUEST['msg_title']);
        $update['content'] = trim($_REQUEST['msg_content']);
        $update['comment_rank'] = intval($_REQUEST['msg_rank']);
        if(!empty($update['content'])){
            //更新晒单内容
            $comment_filter['comment_id'] = $msg_id;
            $comment_filter['user_id'] = $user_id;
            $comment = get_comment_shaidan_list(1,0,$comment_filter);
            $comment = $comment[0];
            if(!empty($comment) && $comment['user_id'] == $user_id && $comment['status'] == 0 ){
                //查找是否有图片
                $imgs = get_shaidan($comment['token']);
                if(!empty($imgs)){
                    $update['comment_type'] = 1;
                } else {
                    //相反，如果没有图片而该评论又是晒单评论时，则将该评论修改为普通评论
                    if($comment['comment_type'] == 1){
                        $update['comment_type'] = 0;
                    }
                }
                //更新晒单内容
                $res = $GLOBALS['cidb']->where('comment_id = '.$msg_id)->limit(1)->update("comment",$update);
                if(!$res){
                    //更新数据库失败
                    $result['error'] = 3;
                }
                die(json_encode($result));
            } else {
                //该晒单不是该用户发表的
                $result['error'] = 2;
                die(json_encode($result));
            }
        }else {
            //晒单内容为空
            $result['error'] = 1;
            die(json_encode($result));
        }
    }
}
/*提交评论*/
elseif($action == 'ajax_submit_edit_comment'){
    $comment_id = isset($_REQUEST['comment_id'])?intval($_REQUEST['comment_id']):0;
    $result = array();
    $result['error'] = '';
    if(!empty($comment_id)){
        $update = array();
        $update['content'] = trim($_REQUEST['content']);
        $update['comment_rank'] = intval($_REQUEST['comment_rank']);
        if(!empty($update['content']) && !empty( $update['comment_rank'])){
            $comment = $GLOBALS['cidb']->where(array("user_id"=>$user_id,'comment_id'=>$comment_id))->get("comment")->row_array();
            if(!empty($comment)){
                $res = $GLOBALS['cidb']->where(array("comment_id"=>$comment_id,"user_id"=>$user_id))->limit(1)->update("comment",$update);
                if(!$res){
                    //更新数据库失败
                    $result['error'] = 3;
                }
                die(json_encode($result));
            } else {
                //该评论不是该用户发表的
                $result['error'] = 2;
                die(json_encode($result));
            }
        }else {
            //评论内容为空
            $result['error'] = 1;
            die(json_encode($result));
        }
    }
}
/*提交评论回复*/
elseif($action == 'submit_reply_comment'){
    $comment_id = isset($_REQUEST['comment_id']) ? $_REQUEST['comment_id'] : 0;
    $reply = isset($_REQUEST['reply']) ? trim($_REQUEST['reply']) : '';
    $result = array();
    $result['error'] = '';
    if($comment_id > 0 && !empty($reply)){
        //获取comment内容
        $comment = get_comment_shaidan_list(1, 0, array('comment_id'=>$comment_id));
        $comment = $comment[0];
        $insert = array();
        $insert['comment_type'] = 3;
        $insert['id_value'] = $comment['id_value'];       
        $insert['user_name'] = get_user_name();
        $insert['content'] = $reply;
        $insert['comment_rank'] = $comment['comment_rank'];
        $insert['add_time'] = gmtime();
        $insert['ip_address'] = real_ip();
        $insert['parent_id'] = $comment_id;
        $insert['user_id'] = $user_id;
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('comment'), $insert);       
    } else {
        //comment_id为空或评论内容为空
        $result['error'] = 1;
    }
    die(json_encode($result));
}
/*获取红包*/
elseif($action == 'ajax_get_bonus'){
    $bonus_status = isset($_REQUEST['bonus_status'])?intval($_REQUEST['bonus_status']):0;
    $page = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
    $result = array();
    $result['error'] = '';
    $offset = ($page - 1)* $pageSize;
    $bonus_filter = array();
    if($bonus_status == 0){
        $bonus_filter['is_used'] = 0;
        $bonus_filter['over_time'] = 0;
    }else if($bonus_status == 1){
        $bonus_filter['is_used'] = 1;
    }else if($bonus_status == 2){
        $bonus_filter['over_time'] = 1;
    }else if($bonus_status == 3){
        $bonus_filter['over_time'] = 0;
    }
    $bonus = get_bouns_list($user_id,$bonus_filter,$pageSize,$offset);
    $bonus_filter['get_total'] = 1;
    $total_bonus = get_bouns_list($user_id,$bonus_filter,0,0);
    //获取各种红包数量
    $bonus_filter['is_used'] = 0;
    $bonus_filter['over_time'] = 0;
    $bonus_effective = get_bouns_list($user_id,$bonus_filter,0,0); //未使用
    
    $bonus_filter['is_used'] = 1;
    unset($bonus_filter['over_time']);
    $bonus_used = get_bouns_list($user_id,$bonus_filter,0,0); //已使用
    
    unset($bonus_filter['is_used']);
    $bonus_filter['over_time'] = 1;
    $bonus_overtime = get_bouns_list($user_id,$bonus_filter,0,0); //已过期
    
    $smarty->assign('bonus_effective', $bonus_effective);
    $smarty->assign('bonus_used', $bonus_used);
    $smarty->assign('bonus_overtime', $bonus_overtime);
    $pager = get_ajax_pager('get_bonus_data', array(), $total_bonus,$page,$pageSize);
    $smarty->assign('bonus',$bonus);
    $smarty->assign('page',$page);
    $smarty->assign('pager',$pager);
    $smarty->assign('bonus_status',$bonus_status);
    $result['content'] = $smarty->fetch("library/user/my_bonus.lbi");
    die(json_encode($result));
} 
/*更改用户资料*/
elseif($action == 'ajax_submit_user_field'){
    $is_update = false;
    $field = isset($_POST['field'])?trim($_POST['field']):'';
    $value = isset($_POST['value'])?trim($_POST['value']):'';
    $edit_array = array("email","mobile_phone","home_phone","office_phone","qq","birthday","nickname","sex");
    $result = array();
    $result['error'] = 0;
    if(empty($field) || $value == ''){
        //输入值不能为空
        $result['error'] = 1;
        die(json_encode($result));
    }
    if(!in_array($field, $edit_array)){
        //该字段不能更改
        $result['error'] = 2;
        die(json_encode($result));
    }
    if(in_array($field, array('email','mobile_phone'))){
        //查找是否已绑定给其他用户
        $where = array($field=>$value);
        $res = $GLOBALS['cidb']->where($where)->where("user_id != ".$user_id)->get("users")->result_array();      
        if(empty($res)){
            $is_update = true;           
        } else {
            //该字段已被其他用户绑定
            $result['error'] = 3;
            die(json_encode($result));
        }
    } else {
        $is_update = true;
    }
    if($is_update){
        //更新数据库
        $update[$field] = $value;
        $GLOBALS['cidb']->where(array("user_id"=>$user_id))->update("users",$update);
        $result['error'] = '';
    }
    die(json_encode($result));
}
/*订单详细信息*/
elseif($action == 'order_detail'){
    include_once ROOT_PATH.'includes/lib_order.php';
    $order_id = isset($_REQUEST['order_id'])?intval($_REQUEST['order_id']):0;
    $order = order_info($order_id);
    if(!empty($order)){      
        $order['formated_pay_time'] = local_date('Y-m-d H:i:s',$order['pay_time']);
        $order['status_str'] = get_order_status(0,$order);
    }
    $select = "order_goods.*,goods.goods_thumb";
    $where = array();
    $where['order_id'] = $order_id;
    $goods = $GLOBALS['cidb']->select($select)->where($where)->join("goods","goods.goods_id = order_goods.goods_id")->get("order_goods")->result_array();
    if(!empty($goods)){
        foreach($goods as $gk=>$gv){
            $goods[$gk]['formated_goods_price'] = price_format($gv['goods_price'],false);
            $goods[$gk]['formated_subtotal'] = price_format($gv['goods_price'] * $gv['goods_number'],false);
        }
    }
    $order['invoice_no']    = $order['shipping_status'] == SS_UNSHIPPED ? 0 : $order['invoice_no'];
    $tmp = explode('<br>', $order['invoice_no'] );
    if(count($tmp) > 1){
        $order['invoice_no'] = $tmp[0];
    }
    $order['shipping_name'] = empty($order['shipping_note']) ? $order['shipping_name'] : $order['shipping_note'];
    $smarty->assign("order",$order);
    $smarty->assign("goods",$goods);
    $smarty->display("order/order_details.dwt");
    //die($content);
    die();
    
}
//获取微信支付二维码地址
elseif($action == 'get_weixin_pay_href'){    
    include_once ROOT_PATH.'includes/lib_order.php';
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $order_sn = isset($_POST['order_sn']) ? trim($_POST['order_sn']) :'0';
    if(!empty($order_sn)){
        $order = order_info(0,$order_sn); 
        if ($order['pay_status'] == PS_UNPAYED &&
            ($order['order_status'] == OS_UNCONFIRMED ||
            $order['order_status'] == OS_CONFIRMED))
        {
            //支付方式信息
            $payment_info = array();
            $payment_info = payment_info($order['pay_id']);
            if($payment_info['pay_code'] == 'weixin'){
                //取得支付信息，生成支付代码
                $payment = unserialize_config($payment_info['pay_config']);
                if(!empty($payment)){
                    //获取需要支付的log_id
                    $order['log_id']    = get_paylog_id($order['order_id'], $pay_type = PAY_ORDER);
                    $order['user_name'] = $_SESSION['user_name'];
                    $order['pay_desc']  = $payment_info['pay_desc'];

                    /* 调用相应的支付方式文件 */
                    include_once(ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');

                    /* 取得在线支付方式的支付按钮 */
                    $pay_obj    = new $payment_info['pay_code'];
                    $pay_href = $pay_obj->get_pay_href($order, $payment);
                    if(isset($_REQUEST['get_page'])){
                        $smarty->assign('pay_href',$pay_href);
                        $smarty->assign('order',$order);
                        echo $smarty->fetch("library/common/weixin_pay_page.lbi");                       
                    } else {
                        echo $pay_href;
                    }
                }
            }
        }
    }
}
elseif($action == 'get_qrcode_img'){
    $text = trim($_REQUEST['qrcode']);
    if(!empty($text)){
        include_once(ROOT_PATH . 'includes/phpqrcode.php');
        QRcode::png($text, false, QR_ECLEVEL_H, 5,0);
    }
    die();
}
//查询订单支付状态
elseif($action == 'query_order_pay'){
    include_once ROOT_PATH.'includes/lib_order.php';
    $order_sn = isset($_POST['order_sn']) ? trim($_POST['order_sn']) :'0';
    if(!empty($order_sn)){       
        $order_info = order_info(0, $order_sn);
        if($order_info['pay_status'] == PS_PAYED){
            die('1');
        }
    }
    die('0');
}
//查询邮件验证状态
elseif($action == 'query_email_validate'){
    $result = array();
    $sql = "SELECT email_validated FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = " . $_SESSION['user_id'];
    $validate = $GLOBALS['db']->getOne($sql);
    $result['error'] = $validate;
    if($validate == 1){
        $result['give_points'] = '已送'.$GLOBALS['_CFG']['email_validate_points'].'积分';
    }
    die(json_encode($result));
}

elseif($action == 'ajax_get_addresses'){
    //获取用户收货地址
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH .'includes/lib_clips.php');
    include_once(ROOT_PATH .'includes/lib_order.php');
    $addresses = address_list($user_id);
    if(!empty($addresses)){
        foreach($addresses as $ak=>$address){
            $addresses[$ak]['consignee_info'] = getRegionName(0, $address['country'], $address['province'], $address['city'], $address['district']);
        }
    }
    $smarty->assign('addresses',$addresses);
    $smarty->assign('add_num', count($addresses));
    $result['content'] = $smarty->fetch("library/user/my_consignee.lbi");
    die(json_encode($result));
}
/*更改密码界面*/
elseif($action == 'edit_password'){
    $smarty->assign('user_info', $user_info);
    $result['content'] = $smarty->fetch("library/user/my_edit_password.lbi");
    die(json_encode($result));
}
/*更改密码操作*/
elseif($action == 'ajax_edit_password'){
    include_once(ROOT_PATH . 'includes/lib_passport.php');
    $old_password = isset($_POST['oldpassword']) ? trim($_POST['oldpassword']) : null;
    $new_password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $user_id = $_SESSION['user_id'];
    $result = array();
    $result['error'] = '';
    if(!edit_password($user_id, $old_password, $new_password)){
        $result['error'] = 1;
    }
    die(json_encode($result));
    
}
/*更新用户资料*/
elseif($action == 'update_profile'){
    $update = array();
    isset($_REQUEST['mobile_phone'])? $update['mobile_phone'] = trim($_REQUEST['mobile_phone']):'';
    isset($_REQUEST['email'])?$update['email'] = trim($_REQUEST['email']):'';   
    $GLOBALS['cidb']->where("user_id = ". $user_id)->limit(1)->update("users",$update);
    update_user_info();
    ecs_header("Location:user.php?act=user_mobile_email");
}

/*用户绑定手机和邮箱*/
elseif($action == 'user_mobile_email'){
    $profile = get_user_profile($user_id);  
    $smarty->assign('profile',$profile['profile']);
    if(isset($_REQUEST['is_ajax'])){
        $result['content'] = $smarty->fetch('library/user/my_contact.lbi');
        die(json_encode($result));
    }
    $smarty->display('user_clips.dwt');
}
/*获取用户注册登录页*/
elseif($action == 'get_login_register_page'){
    $default_page = isset($_REQUEST['default_page'])?trim($_REQUEST['default_page']):'login';
    assign_template();
    $smarty->assign("default_page",$default_page);
    echo $smarty->fetch("library/user/register_login.lbi");
}
/*检测手机验证码*/
elseif($action == 'check_sms_code'){
    $post_code = isset($_POST['register_sms_code'])?trim($_POST['register_sms_code']):0;
    $post_mobile = isset($_POST['register_mobile'])?trim($_POST['register_mobile']):0;
    $mobile = isset($_SESSION['smsCode_mobile'])?$_SESSION['smsCode_mobile']:0;
    $available_time = isset($_SESSION['smsCode_available_time'])?$_SESSION['smsCode_available_time']:0;
    $smsCode = isset($_SESSION['smsCode'])?$_SESSION['smsCode']:0;
    if(!empty($mobile) && !empty($available_time) && !empty($smsCode)){
        $now = gmtime();
        if($available_time - $now > 0 && $post_code == $smsCode && $post_mobile == $mobile){
            echo 'true';
        }else {
            echo '手机验证码不正确';
        }
    } else {
        echo '手机验证码不正确';
    }
}
/*检测用户登录状态*/
elseif($action == 'query_login'){
    $result = array();
    if(!empty($user_id)){
        $result['result'] = 1;
        $result['content'] = $smarty->fetch('library/member_login_div.lbi');
        if(isset($_SESSION['register_type'])){
            $result['from'] = $_SESSION['register_type'];
            //清空
            unset($_SESSION['register_type']);
        }              
    } else {
        $result['result'] = 0;
    }
    die(json_encode($result));
}
/*用户登录之后关闭额外打开的窗口*/
elseif($action == 'login_close_window'){
    if(isset($_SESSION['other_device']) && $_SESSION['other_device'] == 1){
        header("Location:user.php");
	die();
    } else {
        $content = $smarty->fetch('library/common/close_window.lbi');
        echo $content;
    }
}
/*邮箱找回密码界面*/
elseif($action == 'email_password'){
    $no_captcha = isset($_REQUEST['no_captcha']) ? true : false;
    $jump_captcha = false;  
    if($no_captcha){
        $jump_captcha = true;
    }
    if(isset($_POST['captcha']) && trim($_POST['captcha']) == $_SESSION['authnum_session']){
        $jump_captcha = true;
    }
    if(isset($_POST['email']) && $jump_captcha){
        $result = array();
        $result['error'] = '';
        //根据email获取匹配的用户信息
        $email = trim($_POST['email']);
        $sql = "SELECT user_id, user_name, nickname FROM " . $ecs->table('users') . " WHERE email = '" . $email . "' LIMIT 1 ";
        $info = $db->getRow($sql);
        if(!empty($info)){
            $link = "http://".$_SERVER['HTTP_HOST']."/user.php?act=act_reset_password";
            //加密串
            $now = gmtime();
            $params_str = "u=".$info['user_id']."&name=".$info['user_name']."&nick=".$info['nickname']."&e=".$email."&t=".$now;
            $link .= "&tag=".encode($params_str);
            //发送邮件
            if(!empty($info['nickname'])){
                $username = $info['nickname'];
            } else {
                $username = $info['user_name'];
            }
            send_mail($username, $email, "邮箱重置密码", "<a href='$link'>点击链接找回密码</a>", 1);
        } else {
            $result['error'] = 1; //用户不存在
        }
        die(json_encode($result));
    }
    $smarty->display('user/reset_password.dwt');
}
/*获取用户信息*/
elseif($action == 'user_info'){
    include_once (ROOT_PATH."includes/lib_passport.php");
    $user_name = isset($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
    $info = user_info_by_name($user_name);
    //对用户信息进行处理
    if(isset($info['email'])){
        $tmp = explode('@', $info['email']);
        //末3位
        $sub = substr($tmp[0], -3, 3);  
        $info['email_formated'] = str_replace($sub, "***", $tmp[0])."@".$tmp[1];
    }
    die(json_encode($info));
}
/*点击重置密码链接进行的操作*/
elseif($action == 'act_reset_password'){
    $tag = isset($_REQUEST['tag']) ? trim($_REQUEST['tag']) : '';
    $now = gmtime();
    $result = array();
    $result['error'] = '';
    if(!empty($tag)){
        $params_str = decode($tag);
        $params = trace::convert_url_query($params_str);
        //链接有效时间为两小时
        if(!empty($params) && isset($params['u']) &&isset($params['e']) && ($now - $params['t'] <= 7200)){
            //显示修改密码界面
            //die('修改密码界面');
            $_SESSION['reset_info'] = array();
            $_SESSION['reset_info']['reset_method'] = 'email';
            $_SESSION['reset_info']['reset_validate'] = 1;
            $_SESSION['reset_info']['reset_user_id'] = $params['u'];
            $_SESSION['reset_info']['reset_time'] = $params['t'];
            $smarty->assign('do_reset_password',true);
            $smarty->display('user/reset_password.dwt');    
        }
    } else {
        //参数不正确
        $result['error'] = 1;
    }
    die('页面不存在！');
}
/*用户提交新密码操作*/
elseif($action == 'do_reset_password'){
    $password = trim($_POST['password']);
    $now = gmtime();  
    if(isset($_SESSION['reset_info']) && ($now - $_SESSION['reset_info']['reset_time'] <= 7200) && $_SESSION['reset_info']['reset_user_id'] > 0){
        include_once (ROOT_PATH.'includes/lib_passport.php');
        if($_SESSION['reset_info']['reset_method'] == 'email'){
            if(reset_password( $_SESSION['reset_info']['reset_user_id'],$password)){
                unset($_SESSION['reset_info']);
                $smarty->assign('success_reset_password', true);
                $smarty->display('user/reset_password.dwt');
                exit;
            }
        }
    }
    show_message('系统发生错误，请稍后重试。', $links, $hrefs);
}

/*邮箱取回密码，查询用户是否点击了邮箱中的找回密码链接*/
elseif($action == 'query_email_status'){
    $result = array();
    $result['error'] = '';
    if(!empty($_SESSION['reset_info']) && $_SESSION['reset_info']['reset_method'] == 'email'){
        if(isset($_SESSION['reset_info']['reset_user_id'])){
            true;
        } else {
            //没有该用户
            $result['error'] = 2;
        }
    } else {
        //没有重置信息
        $result['error'] = 1;
    }
    die(json_encode($result));
}
/*发送邮箱验证邮件*/
elseif($action == 'send_proving_email'){
    $captcha = $_REQUEST['captcha'];
    $email = trim($_REQUEST['email']);
    $result = array();
    $result['error'] = '';
    $result['email'] = $email;
    if(!empty($_SESSION['user_id'])){
        $link = "http://".$_SERVER['HTTP_HOST']."/user.php?act=act_proving_email";
        //加密串
        $now = gmtime();
        $params_str = "u=".$user_info['user_id']."&name=".$user_info['user_name']."&nick=".$user_info['nickname']."&e=".$email."&t=".$now;
        $link .= "&tag=".encode($params_str);
        //发送邮件
        if(!empty($user_info['nickname'])){
            $username = $user_info['nickname'];
        } else {
            $username = $user_info['user_name'];
        }        
        send_mail($username, $email, "邮箱绑定验证邮件", "<a href='$link'>点击进行邮箱绑定验证</a>", 1);  
        //重新将用户的邮件验证状态设置为未验证
        $profile = array('user_id'=>$user_id, 'email_validated'=>0);
        edit_profile($profile);
    } else {
        $result['error'] = 1; //用户未登录
    }
    die(json_encode($result));
}
elseif($action == 'act_proving_email'){
    $tag = isset($_REQUEST['tag']) ? trim($_REQUEST['tag']) : '';
    $now = gmtime();
    if(!empty($tag)){
        $params_str = decode($tag);
        $params = trace::convert_url_query($params_str);
        //链接有效时间为两小时
        if(!empty($params) && isset($params['u']) &&isset($params['e']) && ($now - $params['t'] <= 7200)){
            //修改用户邮箱
            $profile = array('user_id'=>$params['u'], 'email'=>$params['e'],'email_validated'=>1);       
            if(edit_profile($profile)){
                if($GLOBALS['_CFG']['email_validate_points'] > 0 ){
                    //修改邮箱成功，查询是否赠送了绑定邮箱积分，如没有则赠送用户积分
                    $account_log = get_log_account($params['u'], ACT_EMAIL_VALIDATE);
                    if(empty($account_log)){
                        //没有赠送，则赠送相应积分
                        log_account_change($params['u'], 0, 0, 0, $GLOBALS['_CFG']['email_validate_points'], '绑定邮箱赠送'.$GLOBALS['_CFG']['email_validate_points'].'积分', ACT_EMAIL_VALIDATE);
                    }
                }
                ecs_header("location:user.php");
            }
        } else {
            die("链接有效时间已过。");
        }
    }
    die("非法链接");
}
elseif($action == 'act_proving_mobile'){
    $mobile_phone = trim($_REQUEST['mobile_phone']);
    $result = array();
    $result['error'] = '';
    $profile['user_id'] = $user_id;
    $profile['other'] = array( 'mobile_validated'=>1, 'mobile_phone'=>$mobile_phone);
    if(edit_profile($profile)){
        if($GLOBALS['_CFG']['mobile_validate_points'] > 0 ){
            //修改手机成功，查询是否赠送了绑定手机积分，如没有则赠送用户积分
            $account_log = get_log_account($user_id, ACT_MOBILE_VALIDATE);
            if(empty($account_log)){
                //没有赠送，则赠送相应积分
                log_account_change($user_id, 0, 0, 0, $GLOBALS['_CFG']['mobile_validate_points'], '绑定手机赠送'.$GLOBALS['_CFG']['mobile_validate_points'].'积分', ACT_MOBILE_VALIDATE);
            }
            $result['give_points'] = '已送'.$GLOBALS['_CFG']['mobile_validate_points'].'积分';
        }
    } else {
        $result['error'] = 1;
    }
    die(json_encode($result));
}
//绑定帐户，检查用户帐号密码并绑定帐号
elseif($action == 'check_user'){
    $username = trim($_POST['bindname']);
    $password = trim($_POST['password']);
    $result = array();
    $result['error'] = '';
    if($user_info['user_type'] > 0){
        //查看用户是否已经绑定了其它帐户id
        $user_full = get_user_full($user_id);
        if(empty($user_full['ori_id'])){
            $bind_id = $user->check_user($username,$password);
            if($bind_id){
                third_bind_user($user_id, $bind_id);
                //退出当前登录
                $user->logout();
            } else {
                $result['error'] = 'false';
            }
        } else {
            //此用户已绑定德贝帐户
            $result['error'] = 2;
        }
    } else {
        //此用户不是第三方注册用户，不能绑定其它帐户
        $result['error'] = 1;
    }
    die(json_encode($result));
}
/*以下是函数列表*/

function getOrderGoodsNotShared($user_id) {
    $sql = 'SELECT oi.order_sn, og.rec_id, og.order_id, og.goods_id, og.goods_name FROM ' . $GLOBALS['ecs']->table('order_goods') . ' AS og'
         . ' LEFT JOIN ' . $GLOBALS['ecs']->table('order_info') . ' AS oi'
         . ' ON oi.order_id = og.order_id '
         . ' WHERE oi.user_id = ' . $user_id . ' AND shipping_status = ' . SS_RECEIVED
         . ' AND is_share = 0 AND is_gift = 0 AND shipping_time >' . VALID_SHARE_ORDER_TIME;
    FB::log($sql);
    $goods = $GLOBALS['db']->getAll($sql);
    FB::info($goods);
    return $goods;
}

function get_user_profile($user_id){
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $user_info = get_profile($user_id);
    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $GLOBALS['db']->getAll($sql);

    $sql = 'SELECT reg_field_id, content ' .
           'FROM ' . $GLOBALS['ecs']->table('reg_extend_info') .
           " WHERE user_id = $user_id";
    $extend_info_arr = $GLOBALS['db']->getAll($sql);

    $temp_arr = array();
    foreach ($extend_info_arr AS $val)
    {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }

    foreach ($extend_info_list AS $key => $val)
    {
        switch ($val['id'])
        {
            case 1:     $extend_info_list[$key]['content'] = $user_info['msn']; break;
            case 2:     $extend_info_list[$key]['content'] = $user_info['qq']; break;
            case 3:     $extend_info_list[$key]['content'] = $user_info['office_phone']; break;
            case 4:     $extend_info_list[$key]['content'] = $user_info['home_phone']; break;
            case 5:     $extend_info_list[$key]['content'] = $user_info['mobile_phone']; break;
            default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
        }
    }
    $return['extend_info_list'] = $extend_info_list;
    $return['profile'] = $user_info;
    return $return;
}

/*删除订单*/
function delete_order($order_id,$user_id){
    $sql = "SELECT user_id, order_id, order_sn , surplus , integral , bonus_id, order_status, shipping_status, pay_status FROM " .$GLOBALS['ecs']->table('order_info') ." WHERE order_id = '$order_id'";
    $order = $GLOBALS['db']->GetRow($sql);
    if (empty($order))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['order_exist']);
        return false;
    }
    if($order['order_status'] == OS_CANCELED){
        //订单处于取消状态，可以删除
        $order_info_table = $GLOBALS['ecs']->table("order_info");
        $order_goods_table = $GLOBALS['ecs']->table("order_goods");
        $sql = "DELETE FROM $order_info_table, $order_goods_table USING $order_info_table,$order_goods_table WHERE $order_goods_table.order_id =  $order_info_table.order_id AND $order_info_table.order_id = $order_id";
        if($GLOBALS['db']->query($sql)){
            return true;
        }
    }else {
        return false;
    }
    
}

function get_rank_bar($profile = array()){
    $return = '';
    if(!empty($profile)){
        $rank_points_str = ($profile['rank_points'] - $profile['min_points']).'/'.($profile['next_min_points'] - $profile['min_points']);
        $rank_bar = '<div class="rank-bar">
                        <div class="rank-points"></div>
                        <div class="points-num">'.$rank_points_str.'</div>
                    </div>';
        if($profile['rank_level'] >= 4){
            $return = "当前等级：<img class='rank-img' src='/themes/deebeis/images1/rank{$profile['rank_level']}.jpg' />  享受<font class='red-discount'>".$profile['discount']."</font>折";
        } else {
            $return = "当前等级：<img class='rank-img' src='/themes/deebeis/images1/rank{$profile['rank_level']}.jpg' />$rank_bar<img class='rank-img' src='/themes/deebeis/images1/rank{$profile['next_rank_level']}.jpg' />下一等级：<font class='red-discount'>{$profile['next_discount']}</font>折";
        }
        
    }
    return $return;
}

function get_rank_bar_header($profile = array()){
    $return = '';
    if(!empty($profile)){
        $rank_points_str = ($profile['rank_points'] - $profile['min_points']).'/'.($profile['next_min_points'] - $profile['min_points']);
        $rank_bar = '<div class="rank-bar-header">
                        <div class="rank-points-header"></div>
                    </div>';
        if($profile['rank_level'] >= 4){
            $return = "<span class='span14'>等级：V{$profile['rank_level']}</span>$rank_bar(享受{$profile['next_discount']}折)</span>";
        } else {
            $return = "<span class='span14'>等级：V{$profile['rank_level']}</span>$rank_bar<span class='span14'>V{$profile['next_rank_level']}({$profile['next_discount']}折)</span>";
        }
        
    }
    return $return;
}
?>
