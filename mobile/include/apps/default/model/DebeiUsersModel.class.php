<?php
/**
 * ECTouch Open Source Project
 * ============================================================================
 * Copyright (c) 2012-2014 http://www.deebei.net All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：DebeiUserModel.php
 * ----------------------------------------------------------------------------
 * 功能描述：Debei 用户模型
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */
/* 访问控制 */
defined('IN_ECTOUCH') or die('Deny Access');

class DebeiUsersModel extends UsersModel {
    /**
     * 用户注册，登录函数
     *
     * @access  public
     * @param   string       $username          注册用户名
     * @param   string       $password          用户密码
     * @param   string       $email             注册email
     * @param   array        $other             注册的其他信息
     *
     * @return  bool         $bool
     */
    function register($username, $password, $email = '', $other = array()) {
        /* 检查注册是否关闭 */
        $shop_reg_closed = C('shop_reg_closed');
        if (!empty($shop_reg_closed)) {
            ECTouch::err()->add(L('shop_register_closed'));
        }
        /* 检查username */
        if (empty($username)) {
            ECTouch::err()->add(L('username_empty'));
        } else {
            if (preg_match('/\'\/^\\s*$|^c:\\\\con\\\\con$|[%,\\*\\"\\s\\t\\<\\>\\&\'\\\\]/', $username)) {
                ECTouch::err()->add(sprintf(L('username_invalid'), htmlspecialchars($username)));
            }
        }

        /* 检查email */
//        if (empty($email)) {
//            ECTouch::err()->add(L('email_empty'));
//        } else {
//            if (!is_email($email)) {
//                ECTouch::err()->add(sprintf(L('email_invalid'), htmlspecialchars($email)));
//            }
//        }

        if (ECTouch::err()->error_no > 0) {
            return false;
        }

        /* 检查是否和管理员重名 */
        if (model('Users')->admin_registered($username)) {
            ECTouch::err()->add(sprintf(L('username_exist'), $username));
            return false;
        }

        if (!ECTouch::user()->add_user($username, $password, $email = '')) {
            if (ECTouch::user()->error == ERR_INVALID_USERNAME) {
                ECTouch::err()->add(sprintf(L('username_invalid'), $username));
            } elseif (ECTouch::user()->error == ERR_USERNAME_NOT_ALLOW) {
                ECTouch::err()->add(sprintf(L('username_not_allow'), $username));
            } elseif (ECTouch::user()->error == ERR_USERNAME_EXISTS) {
                ECTouch::err()->add(sprintf(L('username_exist'), $username));
            } elseif (ECTouch::user()->error == ERR_INVALID_EMAIL) {
                ECTouch::err()->add(sprintf(L('email_invalid'), $email));
            } elseif (ECTouch::user()->error == ERR_EMAIL_NOT_ALLOW) {
                ECTouch::err()->add(sprintf(L('email_not_allow'), $email));
            } elseif (ECTouch::user()->error == ERR_EMAIL_EXISTS) {
                ECTouch::err()->add(sprintf(L('email_exist'), $email));
            } else {
                ECTouch::err()->add('UNKNOWN ERROR!');
            }

            //注册失败
            return false;
        } else {
            //注册成功

            /* 设置成登录状态 */
            ECTouch::user()->set_session($username);
            ECTouch::user()->set_cookie($username);

            /* 注册送积分 */
            $register_points = C('register_points');
            if (!empty($register_points)) {
                model('ClipsBase')->log_account_change($_SESSION['user_id'], 0, 0, C('register_points'), C('register_points'), L('register_points'));
            }
            
            //定义other合法的变量数组
            $other_key_array = array('msn', 'qq', 'office_phone', 'home_phone', 'mobile_phone', 'parent_id');
            $update_data['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));
            if ($other) {
                foreach ($other as $key => $val) {
                    //删除非法key值
                    if (!in_array($key, $other_key_array)) {
                        unset($other[$key]);
                    } else {
                        $other[$key] = htmlspecialchars(trim($val)); //防止用户输入javascript代码
                    }
                }
                $update_data = array_merge($update_data, $other);
            }
            $condition['user_id'] = $_SESSION['user_id'];
            $this->update($condition, $update_data);

            /* 推荐处理 */
            $affiliate = unserialize(C('affiliate'));
            if (isset($affiliate['on']) && $affiliate['on'] == 1) {
                // 推荐开关开启
                $up_uid = model('Users')->get_affiliate();
                empty($affiliate) && $affiliate = array();
                $affiliate['config']['level_register_all'] = intval($affiliate['config']['level_register_all']);
                $affiliate['config']['level_register_up'] = intval($affiliate['config']['level_register_up']);
                if ($up_uid) {
                    if (!empty($affiliate['config']['level_register_all'])) {
                        if (!empty($affiliate['config']['level_register_up'])) {
                            $res = $this->row("SELECT rank_points FROM " . $this->pre . "users WHERE user_id = '$up_uid'");
                            if ($res['rank_points'] + $affiliate['config']['level_register_all'] <= $affiliate['config']['level_register_up']) {
                                model('ClipsBase')->log_account_change($up_uid, 0, 0, $affiliate['config']['level_register_all'], 0, sprintf(L('register_affiliate'), $_SESSION['user_id'], $username));
                            }
                        } else {
                            model('ClipsBase')->log_account_change($up_uid, 0, 0, $affiliate['config']['level_register_all'], 0, L('register_affiliate'));
                        }
                    }

                    //设置推荐人
                    $sql = 'UPDATE ' . $this->pre . 'users SET parent_id = ' . $up_uid . ' WHERE user_id = ' . $_SESSION['user_id'];

                    $this->query($sql);
                }
            }

            model('Users')->update_user_info();      // 更新用户信息
            model('Users')->recalculate_price();     // 重新计算购物车中的商品价格
            //记录页面浏览记录：用户注册
            //trace::trace_browse(TRACE_USER_REGISTER, $_SESSION['user_id'], TRACE_FROM_ECT);
            return true;
        }
    }
    
    function check_mobile($mobile){
        if(!empty($mobile)){
            $search ='/^(1(([35][0-9])|(47)|[8][0126789]))\d{8}$/';
            if(!preg_match($search,$mobile)) {
                return false;
            }
            $res = M()->table("users")->field("user_id")->where("mobile_phone = ".$mobile)->find();
            if(empty($res)){
                return true;
            }
        }     
        return false;
    }
    
    /**
     * 检查该用户是否启动过第三方登录 
     * @param type $aite_id
     * @return type 
     */
    function get_one_user($info) {
        $access_token = $info['type'].'_'.$info['openid'];
        $sql = 'SELECT count(*) as count FROM ' . $this->pre . 'users_third_info   WHERE access_token = "'.$access_token.'"';
        $res = $this->row($sql);
        return $res['count'];
    }
    
    /**
     * 插入第三方登录信息到数据库 
     * @param type $info
     * @return boolean
     */
    function third_reg($info) {
        $this->table = 'users';
        $data['sex'] = $info['sex'];
        $data['reg_time'] = gmtime();
        $data['user_rank'] = $info['rank_id'];
        $data['email'] = $info['email'];
        $data['is_validated'] = 1;
        $data['user_name'] = $info['aite_id'];
        //$data['access_token'] = $info['aite_id'];
        if($info['type'] == 'qq'){
            $data['user_type'] = '2';
        }
        if(isset($info['ori_user_name'])){
            $data['nickname'] = $info['ori_user_name'];
        }
        if ($this->insert($data)) {
            $id = mysql_insert_id();
         
            $this->table = "users_third_info";
            $touch_data['user_id'] = $id;
            $touch_data['access_token'] = $info['aite_id'];
            $this->insert($touch_data);
            return true;
        } else {
            return false;
        }
    }
}
