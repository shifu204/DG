<?php

/**
 * ECSHOP 选购中心
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: pick_out.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

function kl3w_set_user_jf(){
	//$GLOBALS['db']->query('UPDATE ' .$GLOBALS['ecs']->table('users'). " SET last_time = '0000-00-00 00:00:00'");
	if(!$_SESSION['user_id'])return 0; 
	$setnum = isset($GLOBALS['_CFG']['logintojf'])?intval($GLOBALS['_CFG']['logintojf']):30;#积分值必须大于1
	if(!$setnum)return -1;
	$user_id = intval($_SESSION['user_id']);
    $sql = 'SELECT last_time FROM ' .$GLOBALS['ecs']->table('users'). " WHERE user_id = '{$user_id}'";
    if ($row = $GLOBALS['db']->getRow($sql)){
		$row['last_time'] = strtotime($row['last_time']);
		if((date('Y-m-d',$row['last_time'])!==date('Y-m-d',gmtime()))&&((gmtime()>$row['last_time']))){
			$sql = "UPDATE " .$GLOBALS['ecs']->table('users'). " SET".
			" last_time = '".date('Y-m-d H:i:s',gmtime())."'". //更新时间
			", pay_points = pay_points + ".$setnum. //消费积分
			//", rank_points = rank_points + ".$setnum. //等级积分
			" WHERE user_id = '" . $user_id . "'";
			$GLOBALS['db']->query($sql); 
			return $setnum;
		} 
	}
	return 1;
}
echo kl3w_set_user_jf();
?>