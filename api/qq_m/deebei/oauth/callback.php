<?php
require_once("../../API/qqConnectAPI.php");
$qc = new QC();
$qq_callback = $qc->qq_callback();
$openid =  $qc->get_openid();
header('Location: http://m.deebei.net/api/qq_m/deebei/oauth/callback_plus.php');
exit;
