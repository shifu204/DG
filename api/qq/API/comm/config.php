<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

define("ROOT",dirname(dirname(__FILE__))."/");
define("CLASS_PATH",ROOT."class/");
$qq_config = array(
    'appid'=>'101005171',
    'appkey'=>'0486a347eb378ae4537cf66681b7083e',
    'callback'=>'http://www.deebei.net/api/qq/deebei/oauth/callback.php',
    'scope'=>'',
    'errorReport'=>true,
    'storageType'=>'file',
    'host'=>'localhost',
    'user'=>'root',
    'password'=>'root',
    'database'=>'test'
);
