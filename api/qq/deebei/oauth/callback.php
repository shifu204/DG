<?php
require_once("../../API/qqConnectAPI.php");
$qc = new QC();
$qq_callback = $qc->qq_callback();
$openid =  $qc->get_openid();
header('Location: http://www.deebei.net/api/qq/deebei/oauth/callback_plus.php');
exit;
