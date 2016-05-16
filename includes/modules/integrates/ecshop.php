<?php

/**
 * ECSHOP 会员数据处理类
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: ecshop.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 会员数据整合插件的代码必须和文件名保持一致 */
    $modules[$i]['code']    = 'ecshop';

    /* 被整合的第三方程序的名称 */
    $modules[$i]['name']    = 'ECSHOP';

    /* 被整合的第三方程序的版本 */
    $modules[$i]['version'] = '2.0';

    /* 插件的作者 */
    $modules[$i]['author']  = 'ECSHOP R&D TEAM';

    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    return;
}

require_once(ROOT_PATH . 'includes/modules/integrates/integrate.php');
class ecshop extends integrate
{
    var $is_ecshop = 1;
    
    /* 会员手机的字段名 */
    var $field_mobile    = '';


    function __construct($cfg)
    {
        $this->ecshop($cfg);
    }

    /**
     *
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function ecshop($cfg)
    {
        parent::integrate(array());
        $this->user_table = 'users';
        $this->field_id = 'user_id';
        $this->ec_salt = 'ec_salt';
        $this->field_name = 'user_name';
        $this->field_pass = 'password';
	$this->field_nickname = 'nickname';
        $this->field_email = 'email';
        $this->field_mobile = 'mobile_phone';
        $this->field_gender = 'sex';
        $this->field_bday = 'birthday';
        $this->field_reg_date = 'reg_time';
        $this->field_email_validated = 'email_validated';
        $this->need_sync = false;
        $this->is_ecshop = 1;
    }


    /**
     *  检查指定用户是否存在及密码是否正确(重载基类check_user函数，支持zc加密方法)
     *
     * @access  public
     * @param   string  $username   用户名
     *
     * @return  int
     */
    function check_user($username, $password = null, $from = null)
    {
        if ($this->charset != 'UTF8')
        {
            $post_username = ecs_iconv('UTF8', $this->charset, $username);
        }
        else
        {
            $post_username = $username;
        }

        if ($password === null && $from === null)
        {
            $sql = "SELECT " . $this->field_id .
                   " FROM " . $this->table($this->user_table).
                   " WHERE " . $this->field_name . "='" . $post_username . "'";

            return $this->db->getOne($sql);
        }
        else
        {
            if($from === null ){
                $sql = "SELECT user_id, password, salt,ec_salt " .
                       " FROM " . $this->table($this->user_table).
                       " WHERE user_name='$post_username'";
                $row = $this->db->getRow($sql);
                $ec_salt=$row['ec_salt'];
            //第三方登录用户
            } else {              
                $this->check_third_user($post_username, $password, $from );
            }
            
            if (empty($row))
            {
                return 0;
            }

            if (empty($row['salt']))
            {
                if ($row['password'] != $this->compile_password(array('password'=>$password,'ec_salt'=>$ec_salt)))
                {
                    return 0;
                }
                else
                {
                    if(empty($ec_salt))
                    {
                        $ec_salt=rand(1,9999);
                        $new_password=md5(md5($password).$ec_salt);
                        $sql = "UPDATE ".$this->table($this->user_table)."SET password= '" .$new_password."',ec_salt='".$ec_salt."'".
                               " WHERE user_name='$post_username'";
                        $this->db->query($sql);
                    }
                    return $row['user_id'];
                }
            }
            else
            {
                /* 如果salt存在，使用salt方式加密验证，验证通过洗白用户密码 */
                $encrypt_type = substr($row['salt'], 0, 1);
                $encrypt_salt = substr($row['salt'], 1);

                /* 计算加密后密码 */
                $encrypt_password = '';
                switch ($encrypt_type)
                {
                    case ENCRYPT_ZC :
                        $encrypt_password = md5($encrypt_salt.$password);
                        break;
                    /* 如果还有其他加密方式添加到这里  */
                    //case other :
                    //  ----------------------------------
                    //  break;
                    case ENCRYPT_UC :
                        $encrypt_password = md5(md5($password).$encrypt_salt);
                        break;

                    default:
                        $encrypt_password = '';

                }

                if ($row['password'] != $encrypt_password)
                {
                    return 0;
                }

                $sql = "UPDATE " . $this->table($this->user_table) .
                       " SET password = '".  $this->compile_password(array('password'=>$password)) . "', salt=''".
                       " WHERE user_id = '$row[user_id]'";
                $this->db->query($sql);

                return $row['user_id'];
            }
        }
    }
    
    /**
     *  检查指定手机是否存在
     *
     * @access  public
     * @param   string  $mobile   用户手机
     *
     * @return  boolean
     */
    function check_mobile($mobile){
        if (!empty($mobile))
        {
            /*检查手机号格式是否正确*/
            if(!is_mobile_phone($mobile)){
                return 3;  //手机号码格式不正确
            }
            /* 检查手机号是否重复 */
            $sql = "SELECT " . $this->field_id .
                       " FROM " . $this->table($this->user_table).
                       " WHERE " . $this->field_mobile . " = '$mobile' ";
            if ($this->db->getOne($sql, true) > 0)
            {
                $this->error = ERR_MOBILE_EXISTS;
                return 1;  //手机号已注册
            }   
            return false;
        } else {
            return 2; //手机号为空
        }
        
    }
    
    /**
     *  用户登录函数
     *
     * @access  public
     * @param   string  $username
     * @param   string  $password
     *
     * @return void
     */
    function login($username, $password, $remember = null)
    {
        //检测用户名格式
        if(is_mobile_phone($username)){
            //从数据库中获取用户名
            $sql = "SELECT " . $this->field_name . " FROM " . $this->table($this->user_table) . " WHERE " . $this->field_mobile . " = '$username' ";
            $username = $this->db->getOne($sql);
			var_dump($username);
        }
        
        if ($this->check_user($username, $password) > 0)
        {
            if ($this->need_sync)
            {
                $this->sync($username,$password);
            }
            $this->set_session($username);
            $this->set_cookie($username, $remember);

            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * 检测第三方登录用户
     * @param type $post_username
     * @param type $password
     * @param type $from
     * @return int
     */
    function check_third_user($post_username,$password, $from){
        $access_token = $from.'_'.$post_username;
        $sql = "SELECT u.user_id, u.password, u.salt, u.ec_salt".
               " FROM ".$this->table($this->user_table)." AS u ".
               " LEFT JOIN ".$this->table('users_third_info')." AS ut ".
               " ON ut.user_id = u.user_id ".
               " WHERE ut.access_token = '$access_token'";
        $row = $this->db->getRow($sql);
        $ec_salt=$row['ec_salt'];
        if (empty($row))
        {
            return 0;
        }
        
        if (empty($row['salt']))
        {
            if ($row['password'] != $this->compile_password(array('password'=>$password,'ec_salt'=>$ec_salt)))
            {
                return 0;
            }
            else
            {
                if(empty($ec_salt))
                {
                    $ec_salt=rand(1,9999);
                    $new_password=md5(md5($password).$ec_salt);
                    $sql = "UPDATE ".$this->table($this->user_table)."SET password= '" .$new_password."',ec_salt='".$ec_salt."'".
                           " WHERE access_token = '$access_token'";
                    $this->db->query($sql);
                }
                return $row['user_id'];
            }
        }
        else
        {
            /* 如果salt存在，使用salt方式加密验证，验证通过洗白用户密码 */
            $encrypt_type = substr($row['salt'], 0, 1);
            $encrypt_salt = substr($row['salt'], 1);

            /* 计算加密后密码 */
            $encrypt_password = '';
            switch ($encrypt_type)
            {
                case ENCRYPT_ZC :
                    $encrypt_password = md5($encrypt_salt.$password);
                    break;
                /* 如果还有其他加密方式添加到这里  */
                //case other :
                //  ----------------------------------
                //  break;
                case ENCRYPT_UC :
                    $encrypt_password = md5(md5($password).$encrypt_salt);
                    break;

                default:
                    $encrypt_password = '';

            }

            if ($row['password'] != $encrypt_password)
            {
                return 0;
            }

            $sql = "UPDATE " . $this->table($this->user_table) .
                   " SET password = '".  $this->compile_password(array('password'=>$password)) . "', salt=''".
                   " WHERE user_id = '$row[user_id]'";
            $this->db->query($sql);

            return $row['user_id'];
        }
    }

    /**
     *  设置指定用户SESSION
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function set_session ($username='', $from = '')
    {
        if (empty($username))
        {
            $GLOBALS['sess']->destroy_session();
        }
        else
        {
            $field = 'user_name';
            if(!empty($from)){
                $field = 'access_token';
                $username = $from.'_'.$username;
            }
            $sql = "SELECT u.user_id, u.password, u.email FROM " . $GLOBALS['ecs']->table('users') . " AS u ".
                   " LEFT JOIN " . $GLOBALS['ecs']->table('users_third_info') . " AS ut ON ut.user_id = u.user_id".
                   " WHERE $field = '$username' LIMIT 1";
            $row = $GLOBALS['db']->getRow($sql);
            
            if ($row)
            {
                $_SESSION['user_id']   = $row['user_id'];
                $_SESSION['user_name'] = $username;
                $_SESSION['email']     = $row['email'];
            }
        }
    }
    
    /**
     *  设置cookie
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function set_cookie($username='', $remember= null, $from = '')
    {
        if (empty($username))
        {
            /* 摧毁cookie */
            $time = time() - 3600;
            setcookie("ECS[user_id]",  '', $time, $this->cookie_path);            
            setcookie("ECS[password]", '', $time, $this->cookie_path);

        }
        elseif ($remember)
        {
            /* 设置cookie */
            $time = time() + 3600 * 24 * 15;
            
            $field = 'user_name';
            if(!empty($from)){
                $field = 'access_token';
                $username = $from.'_'.$username;
            }
            setcookie("ECS[username]", $username, $time, $this->cookie_path, $this->cookie_domain);
            $sql = "SELECT user_id, password FROM " . $GLOBALS['ecs']->table('users') . " WHERE $field = '$username' LIMIT 1";
            $row = $GLOBALS['db']->getRow($sql);
            if ($row)
            {
                setcookie("ECS[user_id]", $row['user_id'], $time, $this->cookie_path, $this->cookie_domain);
                setcookie("ECS[password]", $row['password'], $time, $this->cookie_path, $this->cookie_domain);
            }
        }
    }
}

?>
