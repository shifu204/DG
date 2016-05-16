<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
echo '{"error_code":1,"error_msg":"","data":{"token":"bf49y%2BWeLFmXESqDTzYajVYXMZjpcHiK%2BWLzcZ9%2Bg7Gd2PPWzW1I0VA","user_id":116,"alias":"","avatar":"","gender":0,"phone":"","pet_birthday":"1970-01-01","pet_age":"45\u5c81","pet_type_id":0,"pet_type":"","pet_area_id":0,"pet_area":"","gained":5,"is_completed":false}}';

//更新qq登录用户的token
if(false){
    $sql = "SELECT user_id, user_name FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_type = 2 AND user_name LIKE '%@tencent'";
    $result = $GLOBALS['db']->getAll($sql);
    if(!empty($result)){
        foreach($result as $user){
            $token = explode("@", $user['user_name']);
            $insert = array();
            $insert['user_id'] = $user['user_id'];
            $insert['access_token'] = "qq_".$token[0];
            $sql = "SELECT * FROM " . $GLOBALS['ecs']->table("users_third_info") . " WHERE user_id = " . $user['user_id'] . " AND access_token LIKE 'qq_%'";
            $res = $GLOBALS['db']->getRow($sql);
            if(!empty($res)){
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("users"),array("user_name"=>$insert['access_token']),"UPDATE", " user_id = " . $insert['user_id']);
            } else {          
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("users_third_info"), $insert);           
            }
        }
    }
}
