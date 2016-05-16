<?php
define('BASEPATH', ROOT_PATH.'includes/');
define('EXT', '.php');
require_once(BASEPATH . 'database/DB' . EXT);

function &instantiate_class(&$class_object)
{
    return $class_object;
}

function log_message($level = 'error', $message, $php_error = FALSE)
{
    if($level == 'debug') {
        return;
    } else if($level == 'error'){
        echo $message;
    }
}

function show_error($message){
    echo $message;
    exit;
}
function CIDB()
{
    $params = array(
    'dbdriver' => 'mysql',
    //'hostname' => 'localhost',
    // 'username' => 'root',
    // 'password' => '',
    // 'database' => 'debei_test',
    'hostname' => 'qdm234013142.my3w.com',
    'username' => 'qdm234013142',
    'password' => 'yangjianwei',
    'database' => 'qdm234013142_db',
    //'char_set' => 'utf-8',
    'dbcollat' => 'utf8_general_ci',
    'pconnect' => TRUE,
    'db_debug' => FALSE,
    'cache_on' => FALSE,    
    'dbprefix'=>'ecs_'
    );
    $db = DB($params,TRUE); 
    return $db;
}

