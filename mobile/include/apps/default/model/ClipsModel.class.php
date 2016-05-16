<?php
/**
 * Description of Clips
 *
 * @author ming
 */
class ClipsModel extends BaseModel {
    /**
     * 查询会员的红包金额
     * @access  public
     * @param   integer     $user_id
     * @return  void
     */
    public function get_user_bonus($user_id = 0) {
        if ($user_id == 0) {
            $user_id = $_SESSION['user_id'];
        }

        $sql = "SELECT SUM(bt.type_money) AS bonus_value, COUNT(*) AS bonus_count " .
                "FROM " . $this->pre . "user_bonus AS ub, " . $this->pre . "bonus_type AS bt " .
                "WHERE ub.user_id = '$user_id' AND ub.bonus_type_id = bt.type_id AND ub.order_id = 0";
        $row = $this->row($sql);

        return $row;
    }
    
    /**
     * 获取用户中心默认页面所需的数据
     * @access  public
     * @param   int         $user_id            用户ID
     * @return  array       $info               默认页面所需资料数组
     */
    public function get_user_default($user_id) {
        $user_bonus = $this->get_user_bonus();

        $sql = "SELECT nickname, pay_points, user_money, credit_line, last_login, is_validated FROM " . $this->pre . "users WHERE user_id = '$user_id'";
        $row = $this->row($sql);
        $info = array();
        $info['username'] = stripslashes($_SESSION['user_name']);
        $info['shop_name'] = C('shop_name');
        $info['integral'] = $row['pay_points'] . C('integral_name');
        /* 增加是否开启会员邮件验证开关 */
        $info['is_validate'] = (C('member_email_validate') && !$row['is_validated']) ? 0 : 1;
        $info['credit_line'] = $row['credit_line'];
        $info['formated_credit_line'] = price_format($info['credit_line'], false);

        //新增获取用户头像，昵称
        if (method_exists('WechatController', 'get_avatar')) {
            $u_row = call_user_func(array('WechatController', 'get_avatar'), $user_id);
        } else {
            $u_row = '';
        }
        if ($u_row) {
            $info['nickname'] = $u_row['nickname'];
            $info['headimgurl'] = $u_row['headimgurl'];
        } else {
            $info['nickname'] = $info['username'];
            $info['headimgurl'] = __PUBLIC__ . '/images/get_avatar.png';
        }
        if(!empty($row['nickname'])){
            $info['nickname'] = $row['nickname'];
        }

        //如果$_SESSION中时间无效说明用户是第一次登录。取当前登录时间。
        $last_time = !isset($_SESSION['last_time']) ? $row['last_login'] : $_SESSION['last_time'];

        if ($last_time == 0) {
            $_SESSION['last_time'] = $last_time = gmtime();
        }

        $info['last_time'] = local_date(C('time_format'), $last_time);
        $info['surplus'] = price_format($row['user_money'], false);
        $info['bonus'] = sprintf(L('user_bonus_info'), $user_bonus['bonus_count'], price_format($user_bonus['bonus_value'], false));

        $this->table = 'order_info';
        $condition = "user_id = '" . $user_id . "' AND add_time > '" . local_strtotime('-1 months') . "'";
        $info['order_count'] = $this->count($condition);

        $condition = "user_id = '" . $user_id . "' AND shipping_time > '" . $last_time . "'" . order_query_sql('shipped');
        $info['shipped_order'] = $this->select($condition, 'order_id, order_sn');

        return $info;
    }
}
