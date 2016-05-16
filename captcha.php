<?php

/**
 * ECSHOP 生成验证码
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: captcha.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_captcha.php');
$type = isset($_REQUEST['captcha_type'])?trim($_REQUEST['captcha_type']):'default';
if($type == 'default'){
    $img = new captcha(ROOT_PATH . 'data/captcha/', $_CFG['captcha_width'], $_CFG['captcha_height']);
    @ob_end_clean(); //清除之前出现的多余输入
    if (isset($_REQUEST['is_login']))
    {
        $img->session_word = 'captcha_login';
    }
    $img->generate_image();
}

elseif($type == 'get_img'){
    include_once ROOT_PATH.'includes/cls_validateCode.php';
    $_vc = new ValidateCode();  //实例化一个对象
    $_vc->setSize(130, 35);
    $_vc->doimg();  
    $_SESSION['authnum_session'] = $_vc->getCode();//验证码保存到SESSION中
}

elseif($type == 'check_code'){
    $code = isset($_REQUEST['captcha'])?strtolower(trim($_REQUEST['captcha'])):'';
    if($code == strtolower($_SESSION['authnum_session'])){
        echo 'true';
    } else {
        echo 'false';
    }
}
?>