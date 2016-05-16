<?php


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require('mc_function.php'); 

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}


/*------------------------------------------------------ */
//-- 批量写入
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'mc_add')
{
    //header('Content-type: text/html; charset=gb2312'); //如果乱码,加入此句;	
    $link[] = array('text' => $_LANG['go_back'], 'href' => 'mc_comment.php');

    //$upfile_flash
   $comment_id = $_REQUEST['comment_id'];
   $comment_num = (is_numeric($_REQUEST['comment_num']) && $_REQUEST['comment_num']>0 ? $_REQUEST['comment_num'] : 100);
   $mc_user_str = '';
   $mc_comment_str = '';
   $mc_comment_id = '';
      
   //上传,备份;
    $user_str = $_POST['userall'];
    $comment_str = $_POST['msgall'];
    $select_user = trim($_POST['select_user']);
    $cat_id = trim($_POST['cat_id']);
    $brand_id = trim($_POST['brand_id']);
    
    /*处理用户名*/
    if($select_user != '0'){
        $limit = $select_user;
        if($select_user == 'all'){
            $limit = 0;
        }
        $users = $GLOBALS['cidb']->select("user_name")->where("user_name IS NOT NULL AND user_name !=''")->order_by("user_id ASC");
        if(!empty($limit)){
            $users->limit($limit);
        }
        $users_res = $users->get("users")->result_array();
    }
    if(!empty($users_res)){
        $tmp_users = '';
        foreach($users_res as $users){
            $tmp_users .=$users['user_name'].",";
        }
        $user_str = substr($tmp_users, 0 , strlen($tmp_users)-1);
    }
    
    /*处理商品ID*/
    if($cat_id != '0' || $brand_id != 0){
        if($cat_id == 'all'){
            $_POST['cat_id'] = 0;
        }
        include_once 'includes/lib_goods.php';
        $goods = goods_list(0);
        if(!empty($goods['goods'])){
            $tmp_goods = '';
            foreach($goods['goods'] as $mc_goods){
                $tmp_goods .=$mc_goods['goods_id'].",";
            }
            $comment_id = substr($tmp_goods, 0 , strlen($tmp_goods)-1);
        }
    }
    if(!$comment_id){
	sys_msg('需评论的商品ID不能为空,请检查;', 0, $link);
    }else{
	$mc_comment_id = mc_explode_str($comment_id, ',', 'int');
        if(!$mc_comment_id){		   	
            sys_msg('没有符合条件的,评论商品ID,请检查;', 0, $link);
        }		
    }
    
    /* 读取用户名 */
    if($user_str){
	$mc_user_str = mc_explode_str($user_str, ',', 'user');
	//截取字符,返加数组
	if(!$mc_user_str){
            sys_msg('请按要求填写用户名;', 0, $link);
	}
    }else{
        sys_msg('请填写相关用户名;', 0, $link);	
    }
	 
    /* 读取评论内容 */
    if($comment_str){
	//截取字符,返加数组
	$mc_comment_str = mc_explode_str($comment_str, "\r\n");
	if(!$mc_comment_str){
            sys_msg('请按要求填写评论内容;', 0, $link);
	}
    }else{
        sys_msg('请填写评论内容;', 0, $link);	
    }  	 
    $dnum = $comment_num; $datearr = array(); $ntime = gmtime();	
    $ntime = $ntime - $comment_num*2-600; 
    for($i=1; $i<=$comment_num; $i++){
	$datearr[] = $ntime+($i*2+rand(1,600));
    }
    rsort($datearr);//对数组重新与降序(大在前小在后)进行排序  用sort刚相反

    //写入评论
    for($i=1; $i<=$comment_num; $i++){
        $dnum--;
        //取出一条商品ID
        $random_id = rand(0, (count($mc_comment_id)-1));		
        $random_comment_id = $mc_comment_id[$random_id];
        //取出一个用户名
        $user_id = rand(0, (count($mc_user_str)-1));		
        $random_user_id = $mc_user_str[$user_id];
        //取出一条评论内容
        $comment_id = rand(0, (count($mc_comment_str)-1));		
        $random_comment_str = $mc_comment_str[$comment_id];

        //判断是否有此商品
        $sql = "SELECT COUNT(*) FROM " .$ecs->table('goods'). " where goods_id='$random_comment_id'";
        $renum = $db->getOne($sql);
	//_L(1,$renum);
	if($renum){
            $comment_type = 0;
            $id_value = $random_comment_id;
            //$random_user_id = iconv("gb2312","UTF-8",trim($random_user_id));  //乱码
            $sql2 = "SELECT user_id,email,user_name FROM " .$ecs->table('users'). " where user_name='$random_user_id'";
            $renum2 = $db->getRow($sql2);
            //_L(2,$renum2);
            if($renum2){	
                //usleep(50000);		  
                $email = $renum2['email'];
                $user_name = $renum2['user_name'];
                //$random_comment_str = iconv("gb2312","UTF-8",trim($random_comment_str));  //乱码
                $comment_rank = rand(4,5);
                $add_time = $datearr[$dnum];
                $ip_address = '';		  
                $status = 1; //不需要审核
                $parent_id = 0;
                $user_id = $renum2['user_id'];
                //写入
                $sql = "insert into " .$ecs->table('comment'). "  set comment_type='$comment_type',id_value='$id_value',email='$email',user_name='$user_name',content='$random_comment_str',comment_rank='$comment_rank',add_time='$add_time',ip_address='$ip_address',status='$status',parent_id='$parent_id',user_id='$user_id'";
                //_L('ok',$sql);
                $db->query($sql);
            }
	}       
    }
    ////_L('o1',$user_str);
    //_L('o2',$comment_str);
    sys_msg('恭喜，批量评论成功！', 0, $link);
	
}
elseif($_REQUEST['act'] == 'add_shaidan'){
    require_once (ROOT_PATH . 'includes/lib_shaidan.php');
    $link[] = array('text' => $_LANG['go_back'], 'href' => 'mc_comment.php');
    $title = trim($_REQUEST['shaidan_title']);
    $content = trim($_REQUEST['shaidan_content']);
    $user = trim($_REQUEST['shaidan_username']);
    $id_value = intval(trim($_REQUEST['id_value']));
    $token = $_REQUEST['token'];
    //查找用户是否存在
    $sql = "SELECT user_id FROM " . $GLOBALS['ecs']->table("users") . " WHERE user_name = '$user' OR nickname = '$user' ORDER BY user_id LIMIT 1";
    $user_id = $GLOBALS['db']->getOne($sql);
    if(empty($user_id)){
        sys_msg('用户不存在', 0, $link);
    }
    $insert_data['comment_type'] = 0;
    //查找是否有提交图片
    $imgs = get_shaidan($token);
    if(!empty($imgs)){
        $insert_data['comment_type'] = 1;
    }
    $insert_data['id_value'] = $id_value;
    $insert_data['user_name'] = $user;
    $insert_data['title'] = $title;
    $insert_data['content'] = $content;
    $insert_data['comment_rank'] = 5;
    $insert_data['add_time'] = gmtime();
    $insert_data['ip_address'] = real_ip();
    $insert_data['status'] = 0;
    $insert_data['parent_id'] = 0;
    $insert_data['user_id'] = $user_id;
    $insert_data['is_return'] = 1;
    $insert_data['token'] = $token;
    $GLOBALS['cidb']->insert("comment",$insert_data);
    sys_msg('成功添加晒单', 0, $link);
    
}
elseif($_REQUEST['act'] == 'upload_shaidan_img'){
    $msg_id = isset($_REQUEST['shaidan_img']) ? $_REQUEST['shaidan_img'] : '';
    $shaidan_img = isset($_REQUEST['shaidan_img']) ? $_REQUEST['shaidan_img'] : '';
    $token       = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
    $sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('shaidan_img')
         . ' VALUES (0, \'' . $shaidan_img . '\', \'' . $token . '\')';
    FB::log($sql);
    if ($GLOBALS['db']->query($sql)) {
        echo $GLOBALS['db']->insert_id();
    }
}

/*------------------------------------------------------ */
//-- 操作界面
/*------------------------------------------------------ */
else
{
	/*
	$comment_num = 1000;
	$dnum = $comment_num; $datearr = array(); $ntime = time();
	
	$ntime = $ntime - $comment_num*2-600;
	 
	for($i=1; $i<=$comment_num; $i++){
		$datearr[] = $ntime+($i*2+rand(1,600));
	}
	rsort($datearr);//对数组重新与降序(大在前小在后)进行排序
	for($i=1; $i<=$comment_num; $i++){
		$dnum--;
		$add_time = $datearr[$dnum];
		echo date('Y-m-d H:i:s',$add_time).'<br />';
	}
	_P(1,1);
	*/
    //获取商品分类信息
    $smarty->assign('cat_list',     cat_list(0));
    $smarty->assign('brand_list',get_brand_list());
    //晒单token
    $token = 'back_'.gmtime();
    $timestamp = gmtime();
    $smarty->assign('token',$token);
    $smarty->assign('timestamp',$timestamp);
    $smarty->display('mc_comment.htm');
}

function mc_reg_user($str,$password){
    if(!$str) return false;
    $str_arr = explode(',',$str);
    //用户信息
    $password_str = md5($password); 
    $email = mc_random(8,'abcdefghijklmnopqrstuvwxyz').'@126.com';
    $reg_time = time();
    $alias ='';
    $msn = mc_random(8,'abcdefghijklmnopqrstuvwxyz').'@hotmail.com';
    $qq = mc_random(9,'1234567890');
    $office_phone = mc_random(7,'1234567890');
    $home_phone = mc_random(7,'1234567890');
    $mobile_phone = mc_random(11,'1234567890');
    $is_validated = 0;
    $credit_line = 0;
    $passwd_question = '';
    $passwd_answer  = '';

    $reg_i =0;
    foreach($str_arr as $key => $value){
       if(strlen($value)>=3){
            //判断用户是否存在;
            $res = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') ." WHERE user_name = '$value'");
            if(!$res){
                $sql = "insert into ecs_users set email='$email',user_name='$value',password='$password_str',reg_time='$reg_time',last_login='$reg_time',alias='$alias',msn='$msn',qq='$qq',office_phone='$office_phone',home_phone='$home_phone',mobile_phone='$mobile_phone',is_validated='$is_validated',credit_line='$credit_line',passwd_question='$passwd_question',passwd_answer='$passwd_answer'";
                $GLOBALS['db']->query($sql);
                $reg_i++;
            }		   
       }
    }
    return $reg_i;
}


?>