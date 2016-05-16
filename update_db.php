<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$sql = 'SELECT user_id, user_name FROM '.$GLOBALS['ecs']->table('users') . ' WHERE user_type = 2';
$users = $GLOBALS['db']->getAll($sql);
foreach($users as $user){
   $token = explode('@', $user['user_name']);
   $access_token = 'qq_'.$token[0];
   $data['user_id'] = $user['user_id'];
   $data['access_token'] = $access_token;
   $GLOBALS['cidb']->insert('users_third_info',$data);
}
