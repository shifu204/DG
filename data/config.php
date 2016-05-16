<?php
// 注意文件路径
// database host
//$db_host   = "localhost:3306";
$db_host   = "qdm234013142.my3w.com";

// database name
//$db_name   = "debei_test";
$db_name   = "qdm234013142_db";

// database username
//$db_user   = "root";
$db_user   = "qdm234013142";

// database password
//$db_pass   = "";
$db_pass   = "yangjianwei";

// table prefix
$prefix    = "ecs_";

$timezone    = "PRC";

$cookie_path    = "/";

$cookie_domain    = "";

$session = "1440";

/*topic.php?topic_id=24，只需要填写topic_id*/
$sub_domain = array(
    'nutrilon'=>"22",
    'cowgate'=>"28",
    'aptamil'=>"24",
    'friso'=>"23",
    'hipp'=>"26",
    'neocate'=>"29",
    'semper'=>"27",
    'similac'=>"21",
    'frisobaby'=>"30"
);

define('EC_CHARSET','utf-8');

define('ADMIN_PATH','sysinpanzhide');

define('AUTH_KEY', 'this is a key');

define('OLD_AUTH_KEY', '');

define('API_TIME', '2016-05-16 09:13:54');

define('DEBUG_MODE', 0);

/* 取得当前ecshop所在的根目录 */
define('ROOTPATH', str_replace('data/config.php', '', str_replace('\\', '/', __FILE__)));

//define('COMPUTER_DOMAIN','www.deebei.net');
define('COMPUTER_DOMAIN','www.test3.com');
define('MOBILE_DOMAIN','m.deebei.net');

#用于开发时生成日志用
function _P($val, $stop=0, $mod=0){
	header("Content-Type:text/html; charset=utf-8");
	$mod = $mod ? 'var_dump' : 'print_r'; 
	echo '<pre>';$mod($val);echo '</pre>';
	if($stop)exit(0);
}
function _L($name, $array, $mod='a'){
	$logfile = ROOTPATH.'~bug_'.$name.'.log';
	if(@$fp = fopen($logfile, $mod)) {
		@fwrite($fp, date('Y-m-d H:i:s', time())."\r\n");
		@fwrite($fp, '--------------------------------------------'."\r\n");
		@fwrite($fp, var_export($array,TRUE)."\r\n");
		@fwrite($fp, '============================================'."\r\n");
		@fclose($fp);
	}
}
?>