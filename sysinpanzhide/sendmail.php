<?php
/**
 * ECSHOP 会员邮件群发
 * ============================================================================
 * 版权所有 西安php服务中心。
 * 网站地址: http://www.xaphp.com；
 * ============================================================================
 * $Author: qiyongdong  
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/mail_template.php');
/* 模板赋值 */
$smarty->assign('ur_here', '会员邮件群发');
$html_template_dir = dirname(__FILE__).'/templates/mail/email_template/';
if($_REQUEST['act'] == 'sendmail')
{
    /*查询所有会员信息*/
    $sql = "select user_name,email,user_id from ".$GLOBALS['ecs']->table('users')."";
    $res = $GLOBALS['db']->getAll($sql);
    $smarty->assign('users',$res);
    $templates = get_all_template();
    $smarty->assign('templates',$templates);
    // 包含 html editor 类文件
    include_once(ROOT_PATH . 'includes/cls_ueditor.php'); 
    $ueditor = new ueditor('email_template');
    $email_temp = $ueditor->create_html_editor();
    $smarty->assign("email_temp",$email_temp);
    //获取html邮件模板文
    $html_list = get_html_template($html_template_dir);
    $smarty->assign("html_list",$html_list);
    $smarty->display('mail/all_sendmail.htm');

}
if($_REQUEST['act'] == 'sendmail_act')
{
    $title = $_REQUEST['title'];
    $user_id = $_REQUEST['user_id'];
    $content =  str_replace('\\',"",$_POST['content']);
    if($_REQUEST['title'])
    {
        $_SESSION['title'] = $title;
    }
    if($content)
    {
        $_SESSION['content'] = $content;
    }
    if($_REQUEST['title'])
    {
        $title = $_REQUEST['title'];
        $content =  str_replace('\\',"",$_POST['content']);
    }
    else
    {
        $title =$_SESSION['title'];
        $content = $_SESSION['content'];
    }
    $size =10;
    $count = $GLOBALS['db']->getOne("select count(*) from ".$GLOBALS['ecs']->table('users')."");
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    $page = $_GET['page'];
    if(!isset($page))
    {
        $page = 1;
    }
    $start = ($page-1)*$size;

    $rows = $GLOBALS['db']->getAll("select email from ".$GLOBALS['ecs']->table('users')." limit $start,$size");
    foreach($rows as $value)
    {
        $email = $value['email'];
        send_mail($_CFG['shop_name'],$email, $title, $content, 1);
    }
    $pages = $page+1;
    if($max_page == $page)
    {
        $link[0] = array('href' => 'all_sendmail.php?act=sendmail', 'text' =>'返回发邮件窗口');
        sys_msg('发送邮件完毕',0,$link,false);
    }
    else
    {
        $link[0] = array('href' => 'all_sendmail.php?act=sendmail_act&page='.$pages.'', 'text' => '继续发送');
        $link[1] = array('href' => 'all_sendmail.php?act=sendmail', 'text' =>'停止发送');
        sys_msg('邮件成功发送，再发一封',0,$link,true);
    }
}

if($_REQUEST['act'] == 'load_template'){
    $result = array();
    $tpl = isset($_REQUEST['tpl'])?trim($_REQUEST['tpl']):'';
    if(!empty($tpl)){     
        $filename = $html_template_dir.$tpl;
        $content = file_get_contents($filename);
    }
    $result['content'] = $content;
    die(json_encode($result));
}

if($_REQUEST['act'] == 'do_sendmail'){
    $tpl = isset($_REQUEST['html_tpl'])?trim($_REQUEST['html_tpl']):$_SESSION['html_tpl'];
    $title = trim($_REQUEST['title']);
    $filename = $html_template_dir.$tpl;
    $page = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
    if($page == 1){
        $_SESSION['success_send_mail'] = 0;
    }
    //每次发送的用户数量
    $size = 50;
    //如果设置了开始位置
    $offset = isset($_REQUEST['offset'])?intval($_REQUEST['offset']):0;
    if(empty($offset)){
        $offset = ($page - 1) * $size;
    } else {
        $page = ceil($offset / $size) + 1;
        $_SESSION['success_send_mail'] = $offset;
    }
    if(!empty($tpl)){
        if($title){
            $_SESSION['title'] = $title;
        }
        if(isset($_SESSION['title'])){
            $title = $_SESSION['title'];
        }
        $_SESSION['html_tpl'] = $tpl;
        $select = " COUNT(*) ";
        $where = " WHERE email IS NOT NULL AND email != '' ";
        $count = $db->getOne("SELECT " . $select ." FROM " . $ecs->table("users"). $where);
        $max_page = ($count> 0) ? ceil($count / $size) : 1;    
        $select = 'user_name, nickname, email';
        //获取所有会员邮箱
        $sql = "SELECT ".$select." FROM " . $GLOBALS['ecs']->table('users') . $where ." GROUP BY email ORDER BY user_id DESC LIMIT $offset,$size";
        $users = $db->getAll($sql);
        //成功发送的邮件数
        $success_count = 0;   
        foreach($users as $user)
        {
            $email = $user['email'];
            if(empty($email)){
                continue;
            }
            if(stripos($user['user_name'],'@tencent') && !empty($user['nickname'])){
                $user['user_name'] = $user['nickname'];
            }
            $user_name = $user['user_name'];
            $smarty->assign('user_name',$user_name);
            $smarty->assign('email',$email);
            $smarty->caching = false;
            $content = $smarty->fetch("mail/email_template/".$tpl);
            ini_set('max_execution_time', '0');
            send_mail($user_name , $email , $title, $content, 1);
            ini_set('max_execution_time', '300');
            $success_count ++;
        }
        if(isset($_SESSION['success_send_mail'])){
            $success_count += $_SESSION['success_send_mail'];
        }
        $_SESSION['success_send_mail'] = $success_count;
        $pages = $page+1;
        if($max_page == $page)
        {
            $link[0] = array('href' => 'sendmail.php?act=sendmail', 'text' =>'返回发邮件窗口');
            sys_msg('发送邮件完毕',0,$link,false);
        }
        else
        {
            $link[0] = array('href' => 'sendmail.php?act=do_sendmail&page='.$pages.'', 'text' => '继续发送');
            $link[1] = array('href' => 'sendmail.php?act=sendmail', 'text' =>'停止发送');
            sys_msg('邮件成功发送，已发'.$success_count.'封邮件',0,$link,true,5);
        }
    }
}

function get_all_template(){  
    /* 获得所有邮件模板 */
    $sql = "SELECT template_id, template_code FROM " .$GLOBALS['ecs']->table('mail_templates') . " WHERE  type = 'template'";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->FetchRow($res))
    {
        if ($cur == null)
        {
            $cur = $row['template_id'];
        }

        $len = strlen($GLOBALS['_LANG'][$row['template_code']]);
        $templates[$row['template_id']] = $len < 18 ?
            $GLOBALS['_LANG'][$row['template_code']].str_repeat('&nbsp;', (18-$len)/2) ." [$row[template_code]]" :
            $GLOBALS['_LANG'][$row['template_code']] . " [$row[template_code]]";
    }
    return $templates;
}

function get_html_template($dir){
    $list = array();
    if(!is_dir($dir)){
        return $list;
    }
    $handle = opendir($dir);
    while (false !== ($file = readdir($handle))){
        if($file != '.' && $file != '..'){
            if(!is_dir($dir.$file)){
                array_push($list, $file);
            }          
        }
    }
    return $list;
}
?>