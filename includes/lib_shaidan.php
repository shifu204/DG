<?php

/**
 * ECSHOP 我要晒单函数库
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_shaidan.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

function update_comment_type(){
    $sql = "UPDATE " . $GLOBALS['ecs']->table('comment') . " SET comment_type = 1 WHERE token <> '' AND token is not null ";
    $GLOBALS['db']->query($sql);
}

/**
 * 处理上传文件，并返回上传图片名(上传失败时返回图片名为空）
 *
 * @access  public
 * @param array     $upload     $_FILES 数组
 * @param array     $type       图片所属类别，即data目录下的文件夹名
 *
 * @return string               上传图片名
 */
function shaidan_upload_file($upload, $type)
{
    if (!empty($upload['tmp_name']))
    {
        $ftype = check_file_type($upload['tmp_name'], $upload['name'], '|png|jpg|jpeg|gif|doc|xls|txt|zip|ppt|pdf|rar|docx|xlsx|pptx|');
        if (!empty($ftype))
        {
            $name = date('Ymd');
            for ($i = 0; $i < 6; $i++)
            {
                $name .= chr(mt_rand(97, 122));
            }

            $name = $_SESSION['user_id'] . '_' . $name . '.' . $ftype;

            $target = ROOT_PATH . DATA_DIR . '/' . $type . '/' . $name;
            if (!move_upload_file($upload['tmp_name'], $target))
            {
                $GLOBALS['err']->add($GLOBALS['_LANG']['upload_file_error'], 1);

                return false;
            }
            else
            {
                return $name;
            }
        }
        else
        {
            $GLOBALS['err']->add($GLOBALS['_LANG']['upload_file_type'], 1);

            return false;
        }
    }
    else
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['upload_file_error']);
        return false;
    }
}

/**
 *  添加晒单函数
 *
 * @access  public
 * @param   array       $message
 *
 * @return  boolen      $bool
 */
function add_shaidan($message, $is_simulate = false)
{
    $upload_size_limit = $GLOBALS['_CFG']['upload_size_limit'] == '-1' ? ini_get('upload_max_filesize') : $GLOBALS['_CFG']['upload_size_limit'];
    $status = isset($message['status'])?$message['status']:1 - $GLOBALS['_CFG']['message_check'];

    $last_char = strtolower($upload_size_limit{strlen($upload_size_limit)-1});
    $time = gmtime();

    switch ($last_char)
    {
        case 'm':
            $upload_size_limit *= 1024*1024;
            break;
        case 'k':
            $upload_size_limit *= 1024;
            break;
    }

    /*
    if ($message['upload'])
    {
        if($_FILES['shaidan_img']['size'] / 1024 > $upload_size_limit)
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['upload_file_limit'], $upload_size_limit));
            return false;
        }
        $img_name = shaidan_upload_file($_FILES['shaidan_img'], 'shaidanimg');

        if ($img_name === false)
        {
            return false;
        }
    }
    else
    {
        $img_name = '';
    }
     */

    if ($is_simulate == false) {
        // 若「商品已晒单」报错
        $sql = 'SELECT is_share FROM ' . $GLOBALS['ecs']->table('order_goods')
            . ' WHERE rec_id = ' . $message['rec_id'];
        $is_share = $GLOBALS['db']->getOne($sql);
        if ($is_share == 1) {
            $GLOBALS['err']->add($GLOBALS['_LANG']['goods_has_shared']);
            return false;
        }
    }
	
	if (empty($message['msg_title']) && $message['goods_id']){
        $sql = "SELECT goods_name FROM " .$GLOBALS['ecs']->table('goods')
             . " WHERE goods_id = " . $message['goods_id'] . " LIMIT 1";
		$message['msg_title'] = $GLOBALS['db']->getOne($sql);
		$message['msg_title'] = addslashes_deep($message['msg_title']);
	}

    if (empty($message['msg_title']) || !$message['goods_id'])
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['shaidan_title_empty']);
        return false;
    }
	
    $message['parent_id'] = $message['parent_id']?intval($message['parent_id']):0;
    $message['msg_area'] = isset($message['msg_area']) ? intval($message['msg_area']) : 0;
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('shaidan') .
            " (msg_id, parent_id, user_id, user_name, user_email, msg_title, msg_type, msg_status,  msg_content, msg_time, shaidan_img, rec_id, goods_id, msg_area, token)".
            " VALUES (NULL, '$message[parent_id]', '$message[user_id]', '$message[user_name]', '$message[user_email]', ".
            " '$message[msg_title]', '$message[msg_type]', '$status', '$message[msg_content]', '".$time."', '', '$message[rec_id]', '$message[goods_id]', '$message[msg_area]', '$message[token]')";
    $GLOBALS['db']->query($sql);
    $message_id = $GLOBALS['db']->insert_id();
    // Update ecs_order_goods
    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_goods')
         . ' SET is_share = 1 WHERE rec_id = ' . $message['rec_id'];
    FB::log($sql);
    $GLOBALS['db']->query($sql);
    //将晒单插入到用户评论列表中
    $comment = array();
    $comment['msg_id'] = $message_id;
    $comment['status'] = $status;
    $comment['comment_type'] = $message['msg_type'];
    $comment['id_value'] = $message['goods_id'];
    $comment['user_name'] = $message['user_name'];
    $comment['email'] = $message['user_email'];
    $comment['add_time'] = $time;
    $comment['comment_rank'] = 5;
    $comment['user_id'] = $message['user_id'];
    $GLOBALS['cidb']->insert("comment",$comment);
    return true;
}

/**
 *  获取用户评论以及晒单内容
 *
 * @access  public
 * @param   int     $page_size      列表最大数量
 * @param   int     $start          列表起始页
 * @param   array   $arguments      参数数组
 * @return  array
 */
function get_comment_shaidan_list($page_size, $start,$arguments=array())
{
    $select = "SELECT c.*, g.goods_name AS cmt_name,g.goods_id,g.goods_thumb, r.content AS reply_content, r.add_time AS reply_time ";
    if(isset($arguments['get_total'])){
        $select = "SELECT COUNT(*) AS nums ";
    }
    $sql = $select.
           " FROM " . $GLOBALS['ecs']->table('comment') . " AS c ".
           " LEFT JOIN " . $GLOBALS['ecs']->table('comment') . " AS r " . " ON r.parent_id = c.comment_id AND r.parent_id > 0 AND r.user_id = 0 ".
           " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g " . " ON c.id_value = g.goods_id ";
    $where = ' WHERE c.parent_id = 0 ';
    if(isset($arguments['user_id'])){
        $where .= ' AND c.user_id = ' . $arguments['user_id'];
    }
    $sql .= $where;
    if(isset($arguments['get_total'])){
        $rows = $GLOBALS['db']->getOne($sql);
        return $rows;
    }
    if(isset($arguments['comment_id'])){
        $sql .= " AND c.comment_id = ".$arguments['comment_id'];
    }
    if(isset($arguments['status'])){
        $sql .= " AND c.status = " . $arguments['status'];
    }
    if(isset($arguments['is_shaidan'])){
        $sql .= " AND c.comment_type = 1 ";
    }
    if(isset($arguments['is_recommend'])){
        $sql .= " AND c.is_recommend = " . $arguments['is_recommend'];
    }
    $sort_by = 'c.add_time';
    $sort_order = 'DESC';
    if(isset($arguments['sort_by'])){
        $sort_by = $arguments['sort_by'];
    }
    if(isset($arguments['sort_order'])){
        $sort_order = $arguments['sort_order'];
    }
    $sql .= " ORDER BY $sort_by $sort_order";  
    $res = $GLOBALS['db']->SelectLimit($sql, $page_size, $start);
    
    $comments = array();
    $to_article = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['formated_add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
        if ($row['reply_time'])
        {
            $row['formated_reply_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['reply_time']);
        }
        if ($row['comment_type'] == 1)
        {
            $to_article[] = $row["id_value"];
        }
        if(!empty($row['reply_content'])){
            $row['replys_num'] = 1;
        }else {
            $row['replys_num'] = 0;
        }
        if($row['status'] == 1){
            $row['comment_status'] = '<font style="color:green">'. $GLOBALS['_LANG']['pass_shenhe'].'</font>';
        } else {
            $row['comment_status'] = '<font style="color:red">'. $GLOBALS['_LANG']['not_pass_shenhe'].'</font>';
        }
        $row['reply_num'] = comment_reply_num($row['comment_id']);
        //获取晒单内容
        if(!empty($row['token'])){
            $row['imgs'] = get_shaidan($row['token']);           
        }
        $comments[] = $row;
    }
    return $comments;
}

//根据晒单token获取晒单图片
function get_shaidan($token){
    $imgs = $GLOBALS['cidb']->select("shaidan_img,img_id")->where(array("token"=>$token))->get("shaidan_img")->result_array();
    return $imgs;
}

//获取评论回复数
function comment_reply_num($comment_id){
    $sql = "SELECT COUNT(*) as reply_num FROM " . $GLOBALS['ecs']->table('comment') . " WHERE parent_id = $comment_id AND status = 1 AND user_id > 0 ";
    return $GLOBALS['db']->getOne($sql);
}

function get_comment_replys($comment_id, $status = 1){
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('comment') . 
           " WHERE parent_id = $comment_id ";
    if($status != ''){
        $sql .= " AND status = $status";
    }
    $res = $GLOBALS['db']->query($sql);
    $arr = array();
    while($row = $GLOBALS['db']->fetchRow($res)){
        $row['formated_add_time'] = local_date('Y-m-d H:i:s', $row['add_time']);
        $arr[] = $row;
    }
    return $arr;
}
