<?php

/**
 * 德贝 微信支付插件
 * ============================================================================
 * $Author: ming $
 * $Id: weixin.php  ming $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/weixin.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'weixin_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = '德贝 @ming';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.deebei.net';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0beta';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'appid',           'type' => 'text',   'value' => ''),
        array('name' => 'mchid',               'type' => 'text',   'value' => ''),
        array('name' => 'key',           'type' => 'text',   'value' => ''),
        array('name' => 'appsecret',           'type' => 'text',   'value' => ''),
        array('name' => 'js_api_call_url',           'type' => 'text',   'value' => ''),
        array('name' => 'sslcert_path',           'type' => 'text',   'value' => ''),
        array('name' => 'sslkey_path',           'type' => 'text',   'value' => ''),
        array('name' => 'notify_url',           'type' => 'text',   'value' => ''),
    );

    return;
}

/**
 * 类
 */
class weixin
{
    /**
     * 构造函数
     */
    function __construct() {
        
    }
    
    /**
     * 获取微信支付二维码连接（调用统一下单api生成预支付订单）
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment = array()){
        require_once ROOT_PATH .'plugins/weixin/WxPayPubHelper.php';
        require_once ROOT_PATH .'includes/lib_order.php';
        $order_goods = order_goods($order['order_id']);
        $order_detail = '';
        if(!empty($order_goods)){
            foreach($order_goods as $goods){
                $order_detail .= $goods['goods_name'].'X'.$goods['goods_number'].'  ';
            }
        }
        
        $unifiedOrder = new UnifiedOrder_pub($payment);
        $unifiedOrder->setParameter('body', '订单号：'.$order['order_sn']); //商品描述
        $unifiedOrder->setParameter('out_trade_no', $order['order_sn']); //商户订单号
        $unifiedOrder->setParameter("total_fee",floatval($order['order_amount'])* 100); //总金额
        //$unifiedOrder->setParameter("total_fee",'1'); //总金额
        $unifiedOrder->setParameter("notify_url",$payment['notify_url']); //通知地址 
        $unifiedOrder->setParameter("trade_type","NATIVE"); //交易类型
        $unifiedOrder->setParameter("detail",$order_detail); //商品详细描述
        $unifiedOrderResult = $unifiedOrder->getResult();
        
        //商户根据实际情况设置相应的处理流程
	if ($unifiedOrderResult["return_code"] == "FAIL") 
	{
            //商户自行增加处理流程
            return $unifiedOrderResult['return_msg'];
	}
	elseif($unifiedOrderResult["result_code"] == "FAIL")
	{
            //商户自行增加处理流程
            return $unifiedOrderResult['err_code'].$unifiedOrderResult['err_code_des'];
	}
	elseif($unifiedOrderResult["code_url"] != NULL)
	{
            //从统一支付接口获取到code_url
            $code_url = $unifiedOrderResult["code_url"];
            return $code_url;
	}
    }
    
    function get_pay_href($order, $payment = array()){
        return $this->get_code($order, $payment);
    }
    
         
}

?>