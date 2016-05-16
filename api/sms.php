<?php
define('IN_ECS', true);
require('./init.php');
//var_dump($db);
//exit();
//$userId='dbmy';
//$passwd='dbmy123';
$userId='DBMY';
$passwd='DBMY369';

$act = isset($_REQUEST['act'])?trim($_REQUEST['act']):'default';
$type = $_REQUEST['type'];//短信类型
$mobile = $_REQUEST['mobile'];//手机号
$content = $_REQUEST['content'];//内容
//$remote_server = "http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL";
$remote_server = "http://yes.itissm.com/api/MsgSend.asmx?WSDL";
$send_type = isset($_REQUEST['send_type'])?trim($_REQUEST['send_type']):'SendMsg';
$send_time =  date('Y-m-d H:i:s',time());
$channel = $params['Channel'];
$sign = '【德贝母婴】';
$params = array(
    "userCode"=>$userId,
    "userPass"=>$passwd,
    "DesNo"=>$mobile,
    "Channel"=>76,
    "Msg"=>$content.$sign
);
$return = array();
$return['result'] = 1;
if(empty($mobile)){
    die(json_encode($return));
}

if($act == 'default'){
    //订单提醒
    isset($_REQUEST['order_id']) ? $order_id = intval($_REQUEST['order_id']) : die('{"desc":"订单ID不存在"}');
    $result = send_sms($remote_server,$send_type, $params);
    $return = array('order_id'=>$order_id,'desc'=>'操作成功');
    if($result->SendMsgResult > 0){     
        global $order_id;
        global $type;
        // 连接数据库
        global $db_host;
        global $db_user;
        global $db_pass;
        global $db_name;
        global $prefix;
        $connect = mysql_connect($db_host, $db_user, $db_pass) or die ("链接错误");
        mysql_query("set names 'utf8'"); 
        $select_db = mysql_select_db($db_name, $connect);
        $sql = 'UPDATE `' . $prefix . 'order_info` SET `is_remind_' . $type . '` = 1'
             . ' WHERE `order_id` = ' . $order_id;
        mysql_query($sql);
        $return['result'] = 0;
    } else {
        $return['desc'] = '操作失败';
        $return['result'] = 1;
        $return['code'] = $result->SendMsgResult;
        die(json_encode($return));
    }
}
elseif($act == 'register_code'){
    $minute = 5;
    $smsCode = randStr(6,'NUMBER');
    $available_time = $minute * 60;
    $content = "亲爱的用户，欢迎您加入德贝商城，您的手机验证码为：{$smsCode}，此验证码{$minute}分钟内有效，请于网站上填写后完成注册。".$sign;
    $params['Msg'] = $content;
    //防止多次请求发送短信
    $last_send_time = $_SESSION['smsCode_available_time'];
    $last_send_mobile = $_SESSION['smsCode_mobile'];
    //在120秒内重复点击不重新发送短信
    if(gmtime() - ($last_send_time - $available_time) < 120){
        die(json_encode($return));
    }
    $result = send_sms($remote_server,$send_type, $params);
    if($result->SendMsgResult > 0){
        $send_time = gmtime();
        $_SESSION['smsCode_mobile'] = $mobile;
        $_SESSION['smsCode_available_time'] = $send_time + $available_time;
        $_SESSION['smsCode'] = $smsCode;
        //$cookies = array(
        //    'smsCode_mobile'=>$mobile,
        //    'smsCode_send_time'=>$send_time,          
        //    'smsCode'=>$smsCode
        //);
        //设置cookie
        //setcookie('register_sms_arguments', $cookies, $send_time + $available_time);
        //setcookie('smsCode_mobile',$mobile,$send_time + $available_time);
        //setcookie('smsCode_send_time',$send_time,$send_time + $available_time);
        //setcookie('smsCode',$smsCode,$send_time + $available_time);
        $return['result'] = 0;
    }
    $return['code'] = $result->SendMsgResult;
    die(json_encode($return));
}
elseif($act == 'common_msg'){
//    $params['Msg'] = $content;
//    echo $remote_server;
//    echo $send_type;
//    var_dump($params);
//    $content1=$_REQUEST['content'];
//    $content = "$content1".$sign;
//    $params['Msg'] = $content;
    $result = send_sms($remote_server,$send_type, $params);
//    var_dump($result);
    if($result->SendMsgResult > 0){
        $sql = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$_REQUEST[template_id]','success','$channel','$send_time')";
        $db->query($sql);
        $return['result'] = 0;
    }else{
        $sql = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$_REQUEST[template_id]','fail','$channel','$send_time')";
        $db->query($sql);
        $return['result'] = 2;
    }
    die(1);
}
elseif($act == 'paysms'){
//    支付提醒
    
    isset($_REQUEST['order_id']) ? $order_id = intval($_REQUEST['order_id']) : die('{"desc":"订单ID不存在"}');
    
    $template = $_REQUEST['template'];
    if(isset($template)){
        $sql2 = "select * from ".$ecs->table('sms_templates')." where template_id=".$template;
        $row = $db->GetRow($sql2);
        
        $content = $row['template_content'];
        $params['Msg'] = $content.$sign;
        
        $result = send_sms($remote_server,$send_type, $params);
//        $result->SendMsgResult = "-4";
        if($result->SendMsgResult > 0){
            $sql = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$template','success','$channel','$send_time')";
            $db->query($sql);
            $sql1='update `' .$prefix . 'order_info` set `is_remind_' . $type .'`=1 where `order_id` = ' . $order_id;
            $db->query($sql1);
            $return['result'] = 0;
        }else{
            $sql = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$template','fail','$channel','$send_time')";
            $db->query($sql);
            $return['result'] = $result->SendMsgResult;
        }
//        die(json_encode($return));
        echo $return['result'];
    }
}
elseif($act == 'expressSMS'){
//    发货提醒
    isset($_REQUEST['order_id']) ? $order_id = intval($_REQUEST['order_id']) : die('{"desc":"订单ID不存在"}');
    $template = 0;
    $result = send_sms($remote_server,$send_type, $params);
	$result->SendMsgResult =1;
    if($result->SendMsgResult > 0){
            $sql = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$template','success','$channel','$send_time')";
            $db->query($sql);
            $sql1='update `' .$prefix . 'order_info` set `is_remind_' . $type .'`=1 where `order_id` = ' . $order_id;
            $db->query($sql1);
            $return['result'] = 0;
        }else{
            $sql = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$template','fail','$channel','$send_time')";
            $db->query($sql);
            $return['result'] = $result->SendMsgResult;
        }
    echo $return['result'];
}
elseif($act == 'sendSMS'){
    //发送消息
    $template = $_REQUEST['template'];
    if(isset($template)){
        $sql = "select * from ".$ecs->table('sms_templates')." where template_id=".$template;
        $row = $db->GetRow($sql);
        $content = $row['template_content'];
        $params['Msg'] = $content.$sign;
        
        $result = send_sms($remote_server,$send_type, $params);
        
//        $result->SendMsgResult = 2114400654268836991;
        if($result->SendMsgResult > 0){
            $sql = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$template','success','$channel','$send_time')";
            $db->query($sql);
            $return['result'] = 0;
        }else{
            $sql = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$template','fail','$channel','$send_time')";
            $db->query($sql);
            $return['result'] = $result->SendMsgResult;
        }
        echo $return['result'];
    }   
}
elseif($act == 'returnSMS') {
    //提示返回
    
    
    
    //查询用户手机号码
    $user_id = $_REQUEST['user_id'];
    $sql = "select mobile_phone from ".$ecs->table('users')." where user_id=".$user_id;
    $res = $db->GetOne($sql);
    if(empty($res)){
        $return['result'] = 4;
    }else{
        //查询模板发送内容
        $params['DesNo'] = $res;
        $template = $_REQUEST['template'];
        $sql_1 = "select * from ".$ecs->table('sms_templates')." where template_id=".$template;
        $row = $db->GetRow($sql_1);
        $content = $row['template_content'];
        $params['Msg'] = $content.$sign;
        $result = send_sms($remote_server,$send_type, $params);
//        $result->SendMsgResult = 2114400654268836991;
        if($result->SendMsgResult > 0){
            $sql_2 = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$template','success','$channel','$send_time')";
            $db->query($sql_2);
            $return['result'] = 0;
        }else{
            $sql_2 = "insert into ".$ecs->table('sms_send')." (send_mobile, send_template_id, send_state, Channel, send_time) values ('$mobile','$template','fail','$channel','$send_time')";
            $db->query($sql_2);
            $return['result'] = $result->SendMsgResult;
        }
    }
    echo $return['result'];
    
    
    
}


function send_sms($remote_server,$send_type,$params){
    header("Content-type: text/html; charset=utf-8");
    $client = new SoapClient($remote_server);
    $param = $params;
    $p = $client->__soapCall($send_type,array('parameters' => $param));
    return $p;
    
}

function randStr($len=6,$format='ALL') { 
    switch($format) { 
        case 'ALL':
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~'; break;
        case 'CHAR':
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~'; break;
        case 'NUMBER':
        $chars='0123456789'; break;
        default :
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~'; 
        break;
    }
    mt_srand((double)microtime()*1000000*getmypid()); 
    $password="";
    while(strlen($password)<$len) {
        $password.=substr($chars,(mt_rand()%strlen($chars)),1);
    }
    return $password;
 } 
