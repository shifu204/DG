<?php
/**
 * 通用通知接口demo
 * ====================================================
 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
 * 商户接收回调信息后，根据需要设定相应的处理流程。
 * 
 * 这里举例使用log文件形式记录回调信息。
*/
    define('IN_ECS', true);
    require($_SERVER['DOCUMENT_ROOT'] .'/includes/init.php');
    include_once("./log_.php");
    require(ROOT_PATH . 'includes/lib_order.php');
    require(ROOT_PATH . 'includes/lib_payment.php');
    require_once(ROOT_PATH . 'plugins/weixin/WxPayPubHelper.php');    
    //获取微信支付配置
    $sql = "SELECT pay_id FROM " .$GLOBALS['ecs']->table('payment') . ' WHERE pay_code = "weixin"';
    $pay_id = $GLOBALS['db']->getOne($sql);
    $payment_info = payment_info($pay_id);
    $payment = unserialize_config($payment_info['pay_config']);
    //使用通用通知接口
	$notify = new Notify_pub($payment);
	//存储微信的回调
	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];	
	$notify->saveData($xml);
	
	//验证签名，并回应微信。
	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
	//尽可能提高通知的成功率，但微信不保证通知最终能成功。
	if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
	}else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
	}
	$returnXml = $notify->returnXml();
	echo $returnXml;
	
	//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
	
	//以log文件形式记录回调信息
	$log_ = new Log_();
	$log_name="./notify_url.log";//log文件路径
	if($notify->checkSign() == TRUE)
	{
            //插入响应信息到数据库中
            $insert_data = array();
            $insert_data['data'] = serialize($notify->data);
            $insert_data['return_code'] = $notify->data['return_code'];
            $insert_data['result_code'] = $notify->data['result_code'];
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL"){
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
            }
            else{
                //成功收到响应信息 
                $insert_data['type'] = 0; //微信支付结果通知
                $insert_data['openid'] = $notify->data['openid'];               
                $insert_data['out_trade_no'] = $notify->data['out_trade_no'];
                $insert_data['transaction_id'] = $notify->data['transaction_id'];
                $insert_data['time_end'] = $notify->data['time_end'];
                $insert_data['add_time'] = gmtime();
                
                $log_id = get_order_id_by_sn($notify->data['out_trade_no']);
                if(check_money($log_id, floatval($notify->data['total_fee'])/100 )){
                    order_paid($log_id, 2);
                }                
            }
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('weixin_pay') .
                    " (`type`, `openid`, `return_code`, `result_code`, `out_trade_no`, `transaction_id`, `time_end`,`data`, `add_time`) VALUES (" . 
                    " {$insert_data['type']}, '{$insert_data['openid']}', '{$insert_data['return_code']}', '{$insert_data['result_code']}', '{$insert_data['out_trade_no']}', '{$insert_data['transaction_id']}', '{$insert_data['time_end']}', '{$insert_data['data']}', '{$insert_data['add_time']}' )";
            $GLOBALS['db']->query($sql); 
	}
?>