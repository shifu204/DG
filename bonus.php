<?php
/**
 * ECSHOP 红包
**/
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$action = isset($_REQUEST['act'])?trim($_REQUEST['act']):'default';
// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr = array();
$ui_arr = array();
/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
    if (!in_array($action, $not_login_arr))
    {
        if (in_array($action, $ui_arr))
        {
            header("Location:user.php?act=login");
        }
        else
        {
            //未登录提交数据。非正常途径提交数据！
            die('-1');
        }
    } 
}

$user_id = $_SESSION['user_id'];
if($action == 'user_ajax_get'){
    $result = array();
    $result['error'] = $result['message'] =  '';
    $type_id = isset($_REQUEST['type_id'])?intval($_REQUEST['type_id']):0;
    //获取红包信息
    $bonus_info = $GLOBALS['cidb']->where("type_id = ". $type_id)->get("bonus_type")->row_array();
    if(!empty($bonus_info)){
        //用户点击领取的红包
        if($bonus_info['send_type'] == 5){
            //查询已领取的红包
            $where = array('bonus_type_id'=>$type_id,'user_id'=>$user_id);
            $got = $GLOBALS['cidb']->where($where)->get("user_bonus")->result_array();
            if($bonus_info['get_num'] > 0){
                $count = count($got);
                if($count >= $bonus_info['get_num']){
                    //该用户不能再领取该种类红包
                    $result['error'] = '-4';
                    $result['message'] = $_LANG['over_bonus_num'];
                    die(json_encode($result));
                }
                else 
                {
                    //为用户添加红包
                    $insert = array();
                    $insert['bonus_type_id'] = $type_id;
                    $insert['bonus_sn'] = $insert['used_time'] = $insert['order_id'] = $insert['emailed'] = 0;
                    $insert['user_id'] = $user_id;
                    $GLOBALS['cidb']->insert("user_bonus",$insert);
                    die(json_encode($result));
                }
            }
        }
        else 
        {
            //非法操作
            $result['error'] = '-3';
            $result['message'] = $_LANG['no_register_operate'];
            die(json_encode($result));
        }
    } 
    else 
    {
        $result['error'] = '-2';
        $result['message'] = $_LANG['bonus_not_exist'];
        //红包不存在
        die(json_encode($result));
    }
}