<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$code = isset($_REQUEST['error'])?trim($_REQUEST['error']):404;
assign_template();
$position = assign_ur_here();
$smarty->assign('page_title',      $position['title']);    // 页面标题
$smarty->assign('keywords',        htmlspecialchars($_CFG['shop_keywords']));
$smarty->assign('description',     htmlspecialchars($_CFG['shop_desc']));
$smarty->display('/errorpage/'.$code.'.dwt');
