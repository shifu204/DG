<?php
get_nav_position();
update_day_promote();
function get_day_promotion_id(){
    $act_id = 0;
    $now = gmtime();
    $today = date("Y-m-d", $now);
    //获取所有天天特价活动,按照排序高低来排
    $all_day = $GLOBALS['cidb']->where(array('act_type'=>9,'is_finished !='=>1))->order_by('sort_order ASC, act_id DESC')->get('goods_activity')->result_array();
    foreach ($all_day as $ak=>$av){
        $start_time = local_strtotime($today." ".$av['start_time']); 
        $tmp_end = intval($av['end_time']);
        if(empty($tmp_end)){
            $end_time = local_strtotime($today) +($av['loop']+1)*3600*24;
        } else {
            $end_time = local_strtotime($today." ".$av['end_time']);
        }
        //从未运行的情况下优先选择排序高的活动
        if(empty($av['last_time'])){
            //在活动时间内
            if($now >= $start_time && $now <= $end_time){
                $act_id = $av['act_id'];
                break;
            }
        //已经运行过
        } else {
            //循环时间
            $loop_time = $av['loop'] * 3600*24;
            if($loop_time == 0){
                $act_id = $av['act_id'];
                return $act_id;
            }else {
                if($now  - $av['last_time'] >= $loop_time){
                    $act_id = $av['act_id'];
                    return $act_id;
                }
            }    
        }
    }
    return $act_id;   
}

//更新天天特价活动
function update_day_promote(){   
    $act_id = get_day_promotion_id();
    $now = gmtime();  
    $today = date("Y-m-d", $now);
    if(!empty($act_id)){
        $promote_goods = $GLOBALS['cidb']->where('goods_activity.act_id = '.$act_id)->join('day_promote','day_promote.act_id = goods_activity.act_id')->get('goods_activity')->result_array();
        if(!empty($promote_goods)){    
            $act_updata['last_time'] = $now;
            //99表示正在运行
            $act_updata['is_finished'] = 99;
            //将正在运行的活动状态设置成停止
            $GLOBALS['cidb']->where(array('act_type'=>9,'is_finished'=>99))->update('goods_activity',array('is_finished'=>0));
            //将活动设置成运行状态
            $GLOBALS['cidb']->where('act_id = '.$act_id)->limit(1)->update('goods_activity',$act_updata);
            foreach($promote_goods as $pgk=>$pgv){
                $goods_updata['promote_start_date'] = local_strtotime($today." ".$pgv['start_time']);            
                $goods_updata['promote_end_date'] = local_strtotime($today." ".$pgv['end_time']);
                if(intval($pgv['end_time']) == 0){
                    $goods_updata['promote_end_date'] = $goods_updata['promote_end_date']+3600*24;
                }    
                $goods_updata['promote_price'] = $pgv['promote_price'];
                $GLOBALS['cidb']->where('goods_id = '.$pgv['goods_id'])->limit(1)->update('goods',$goods_updata);
            }
        }
    }       
}

//获取分类下的所有品牌
function get_catetory_brands($cat_id = 0){
    $GLOBALS['cidb']->select("brand.brand_id, brand.brand_name, brand.brand_logo, brand.brand_desc, COUNT(*) goods_num");
    if($cat_id != 0){
        $children = array_keys(cat_list($cat_id,0,false));
        if(!empty($children)){
            $GLOBALS['cidb']->where_in("goods.cat_id",$children);
        }
    }
    $GLOBALS['cidb']->where('brand.is_show = 1');
    $GLOBALS['cidb']->join('goods','goods.brand_id = brand.brand_id');
    $GLOBALS['cidb']->where(array("goods.is_on_sale"=>1));
    $brands = $GLOBALS['cidb']->group_by("brand_id")->order_by("brand.sort_order ASC")->get("brand")->result_array();
    return $brands;
}

/*获得指定tag分类下的子树*/
function get_child_tree_by_tag($cat_tag = '')
{
    $parent = $GLOBALS['cidb']->select('cat_id')->where(array('cat_tag'=>$cat_tag))->get("category")->row_array();
    $parent_id = $parent['cat_id'];
    if ($parent_id)
    {
        $child_sql = 'SELECT cat_id, cat_name, parent_id, is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$parent_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";
        $res = $GLOBALS['db']->getAll($child_sql);
        foreach ($res AS $row)
        {
            if ($row['is_show'])

               $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
               $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
               $three_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

               if (isset($row['cat_id']) != NULL)
                   {
                       $three_arr[$row['cat_id']]['cat_id'] = get_child_tree($row['cat_id']);

            }
        }
    }
    return $three_arr;
}

/*根据条件获得用户的订单列表*/
function get_orders($arguments = array()){
    $result = array();
    $user_id = $_SESSION['user_id'];
    $pay_status = $shipping_status = $order_status = $limit = '';
    if(!empty($user_id)){
        //支付状态
        if(isset($arguments['pay_status'])){
            $pay_status = " oi.pay_status ";
            if($arguments['pay_status'] == ORDER_NO_PAY){
                $pay_status .= " < 2 ";
            } else {
                $pay_status .= " = ".$arguments['pay_status'];
            }
        }
        //配送状态
        if(isset($arguments['shipping_status'])){
            $shipping_status = " oi.shipping_status = ".$arguments['shipping_status'];
        }
        //订单状态
        if(isset($arguments['order_status'])){
            $order_status = " oi.order_status ";
            if($arguments['order_status'] == ORDER_VALID){
                $order_status .= " < 2";
            } else {
                $order_status .= " = ".$arguments['order_status'];
            }
        }
        //订单的数量限制
        if(isset($arguments['limit']) && isset($arguments['offset'])){
            $limit = " LIMIT {$arguments['offset']} ,  {$arguments['limit']}";
        } else if(isset($arguments['limit'])){
            $limit = " LIMIT {$arguments['limit']}";
        } else if(isset($arguments['offset'])){
            $limit = " LIMIT 0 , {$arguments['offset']}";
        }
        $select = " SELECT oi.*,(".order_amount_field('oi.').") AS total_fee, (".order_should_pay_field('oi.').") AS should_pay, p.pay_code ";    
        if(isset($arguments['get_total'])){
            $select = "SELECT COUNT(*) AS nums,oi.order_sn ";
        }
        $sql = $select.
               " FROM ".$GLOBALS['ecs']->table('order_info')." AS oi ".
               " LEFT JOIN ".$GLOBALS['ecs']->table('payment')." AS p ON p.pay_id = oi.pay_id ".
               " WHERE oi.user_id = $user_id ";
        //排除货到付款的订单
        if(isset($arguments['no_cod'])){
            $sql .= " AND p.pay_code != 'cod' ";
        }
        $sql .=empty($pay_status)?"":" AND $pay_status ";
        $sql .=empty($shipping_status)?"":" AND $shipping_status ";
        $sql .=empty($order_status)?"":" AND $order_status ";
        $sql .=isset($arguments['where'])?$arguments['where']:"";
        if(isset($arguments['get_total'])){
            $sql .= " GROUP BY oi.order_sn ";
            $rows = $GLOBALS['db']->getALL($sql);
            return count($rows);
        }
        $sql .= " ORDER BY oi.add_time DESC ";
        $sql .=empty($limit)?"":" $limit ";
        $rows = $GLOBALS['db']->getALL($sql);
        if(!empty($rows)){
            foreach($rows as $rk=>$row){
                //获取订单商品
                $row['goods'] = array();
                $goods_info = $GLOBALS['cidb']->select("order_goods.goods_id,order_goods.goods_name,order_goods.goods_number,order_goods.goods_price,goods.goods_thumb")->where(array("order_id"=>$row['order_id']))->join("goods","goods.goods_id = order_goods.goods_id")->get("order_goods")->result_array();
                if(!empty($goods_info)){
                    $row['goods'] = $goods_info;
                }
                $row['formated_goods_amount']   = price_format($row['goods_amount'], false);
                $row['formated_discount']       = price_format($row['discount'], false);
                $row['formated_tax']            = price_format($row['tax'], false);
                $row['formated_shipping_fee']   = price_format($row['shipping_fee'], false);
                $row['formated_insure_fee']     = price_format($row['insure_fee'], false);
                $row['formated_pay_fee']        = price_format($row['pay_fee'], false);
                $row['formated_pack_fee']       = price_format($row['pack_fee'], false);
                $row['formated_card_fee']       = price_format($row['card_fee'], false);
                $row['formated_total_fee']      = price_format($row['total_fee'], false);
                $row['formated_money_paid']     = price_format($row['money_paid'], false);
                $row['formated_bonus']          = price_format($row['bonus'], false);
                $row['formated_integral_money'] = price_format($row['integral_money'], false);
                $row['formated_surplus']        = price_format($row['surplus'], false);
                $row['formated_order_amount']   = price_format(abs($row['order_amount']), false);
                $row['formated_should_pay']   = price_format(abs($row['should_pay']), false);
                $row['formated_add_time']       = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
                $row['order_status_str']        = get_order_status(0,$row);
                $row['invoice_no']    = $row['shipping_status'] == SS_UNSHIPPED ? 0 : $row['invoice_no'];
                $row['shipping_name'] = empty($row['shipping_note']) ? $row['shipping_name'] : $row['shipping_note'];
                /* 如果是未付款状态，生成支付按钮 */
                if ($row['pay_status'] == PS_UNPAYED &&
                   ($row['order_status'] == OS_UNCONFIRMED ||
                   $row['order_status'] == OS_CONFIRMED))
                {
                    $row['pay_online'] = get_pay_online($row['order_sn']);
                }
                else
                {
                    $row['pay_online'] = '';
                }
                $result[$row['order_id']] = $row;    
            }
        }
    }
    return $result;
}
//获取订单状态字符串
function get_order_status($order_id = 0,$order_info = array()){   
    $order_status = "";
    if(empty($order_info)){
        if($order_id > 0){
            $order_info = order_info($order_id);
        }
    }
    if(!empty($order_info)){
        $order_info["order_status_str"] = "";
        $os = $order_info['order_status'];
        $ps = $order_info['pay_status'];
        $ss = $order_info['shipping_status'];
        if(in_array($os,array(OS_CONFIRMED,OS_SPLITED,OS_SPLITING_PART))){
            if($ps <= PS_PAYING){
                $order_info["order_status_str"] = '等待付款';
            } else if($ps == PS_PAYED){
                $order_info["order_status_str"] = '已付款';
                if($ss <= SS_SHIPPED){
                    $order_info["order_status_str"] = '等待收货';
                } else if($ss == SS_RECEIVED){
                    $order_info["order_status_str"] = '已收货';
                }
            }
        } else {
            if($os == OS_UNCONFIRMED){
                $order_info["order_status_str"] = '未确认';
            }else if($os == OS_CANCELED){
                $order_info["order_status_str"] = '已取消';
            }else if($os == OS_INVALID){
                $order_info["order_status_str"] = '无效';
            }else if($os == OS_RETURNED){
                $order_info["order_status_str"] = '退货';
            }
        }
        
        if(!in_array($os, array(OS_CANCELED,OS_INVALID,OS_RETURNED)) && $ps == PS_PAYED && $ss >= SS_RECEIVED){
            $order_info["order_status_str"] = '已完成';
        }
    }
    $order_status = $order_info['order_status_str'];
    return $order_status;
}
//获取订单支付连接
function get_pay_online($order_sn){
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    include_once(ROOT_PATH . 'includes/lib_base.php');
    $pay_href = "";
    if($order_sn > 0){
        $order = order_info(0,$order_sn);      
        //如果订单未付款的，则进入支付页面
        if ($order['pay_status'] == PS_UNPAYED &&
            ($order['order_status'] == OS_UNCONFIRMED ||
            $order['order_status'] == OS_CONFIRMED))
        {
            //支付方式信息
            $payment_info = array();
            $payment_info = payment_info($order['pay_id']);
            //微信支付方式不返回支付连接，要点击支付按钮的时候才生成连接
            if($payment_info['pay_code'] == 'weixin'){
                return $payment_info['pay_code'];
            }
            //无效支付方式
            if ($payment_info === false) {
                die("无效的支付方式。");
            }
            else {
                //取得支付信息，生成支付代码
                $payment = unserialize_config($payment_info['pay_config']);
                if(!empty($payment)){
                    //获取需要支付的log_id
                    $order['log_id']    = get_paylog_id($order['order_id'], $pay_type = PAY_ORDER);
                    $order['user_name'] = $_SESSION['user_name'];
                    $order['pay_desc']  = $payment_info['pay_desc'];

                    /* 调用相应的支付方式文件 */
                    include_once(ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');

                    /* 取得在线支付方式的支付按钮 */
                    $pay_obj    = new $payment_info['pay_code'];
                    $pay_href = $pay_obj->get_pay_href($order, $payment);
                }
            }
        }
    }
    return $pay_href;
}

//获取商品评论数量 
function get_goods_comment_num ($id,$type){
    $comment_type = "comment_type IN ($type)";
    $count = $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('comment').
           " WHERE id_value = '$id' AND $comment_type AND status = 1 AND parent_id = 0");
    if(!$count || empty($count)){
        $count = 0;
    }
    return $count;
}

//获取购物车商品数量及总价
function get_cart_info()
{
    $sql = 'SELECT SUM(goods_number) AS number, SUM(goods_price * goods_number) AS amount' .
           ' FROM ' . $GLOBALS['ecs']->table('cart') .
           " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $row = $GLOBALS['db']->GetRow($sql);

    if ($row)
    {
        $number = intval($row['number']);
        $amount = floatval($row['amount']);
    }
    else
    {
        $number = 0;
        $amount = 0;
    }
    $amount = price_format($amount, false);
    $result['number'] = $number;
    $result['amount'] = $amount;
    return $result;
}
//获取当前导航栏位置
function get_nav_position(){ 
    $nav_list = get_navigator("middle");
    $nav_list = $nav_list['middle'];
    //获取当前网页的域名后面的地址
    $cur_url = substr(strrchr($_SERVER['REQUEST_URI'],'/'),1);
    if (intval($GLOBALS['_CFG']['rewrite']))
    {
        if(strpos($cur_url, '-'))
        {
            preg_match('/([a-z]*)-([0-9]*)/',$cur_url,$matches);
            $cur_url = $matches[1].'.php?id='.$matches[2];
        }
    }
    else
    {
        $cur_url = substr(strrchr($_SERVER['REQUEST_URI'],'/'),1);
    }
    $tmp_name = basename(PHP_SELF);
    if (intval($GLOBALS['_CFG']['rewrite']))
    {
        $filename = strpos($tmp_name,'-') ? substr($tmp_name, 0, strpos($tmp_name,'-')) : substr($tmp_name, 0, -4);
    }
    else
    {
        $filename = substr($tmp_name, 0, -4);
    }
    
    $nav_pos = false;
    foreach($nav_list as $nk=>$nv){
        if($nv['url'] == $cur_url){
            $nav_pos = $nk;
        }
    }
    if(!$nav_pos){
        $nav_pos = 0;
    }
    return $nav_pos;
}

//获取用户红包列表
function get_bouns_list($user_id, $arguments = array(), $num = 10, $start = 0){
    $day = getdate();
    $cur_date = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);
    $sql = "SELECT u.bonus_sn, u.order_id,u.used_time, b.type_name, b.type_money, b.min_goods_amount, b.use_start_date, b.use_end_date,b.min_amount ".
           " FROM " .$GLOBALS['ecs']->table('user_bonus'). " AS u ,".
           $GLOBALS['ecs']->table('bonus_type'). " AS b".
           " WHERE u.bonus_type_id = b.type_id AND u.user_id = '" .$user_id. "'";
    if(isset($arguments['is_used'])){
        //已使用
        if($arguments['is_used'] == 1){
            $sql .= " AND u.used_time != 0";
        //未使用    
        }else if($arguments['is_used'] == 0){
            $sql .= " AND u.used_time = 0";
        }
    }
    if(isset($arguments['over_time'])){
        //未过期
        if($arguments['over_time'] == 0){
            $sql .= " AND b.use_end_date > $cur_date ";
        }
        //已过期
        elseif($arguments['over_time'] == 1){
            $sql .= " AND b.use_end_date < $cur_date ";
        }
    }
    if(isset($arguments['get_total'])){
        $total =count( $GLOBALS['db']->getALL($sql));
        return $total;
    }
    $res = $GLOBALS['db']->selectLimit($sql, $num, $start);
    $arr = array();
   
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        /* 先判断是否被使用，然后判断是否开始或过期 */
        if (empty($row['order_id']))
        {
            /* 没有被使用 */
            if ($row['use_start_date'] > $cur_date)
            {
                $row['status'] ='<font style="color:green;">'. $GLOBALS['_LANG']['not_start']. '</font>';
            }
            else if ($row['use_end_date'] < $cur_date)
            {
                $row['status'] ='<font style="color:red;">'. $GLOBALS['_LANG']['overdue']. '</font>';
            }
            else
            {
                $row['status'] ='<font style="color:green;">'. $GLOBALS['_LANG']['not_use']. '</font>';
            }
        }
        else
        {
            $row['status'] ='<font style="color:red">'. $GLOBALS['_LANG']['had_use']. '</font>';
        }
        if(!empty($row['used_time'])){
            $row['used_time'] = local_date("Y-m-d H:i:s",$row['used_time']);
        }else {
            $row['used_time'] = $GLOBALS['_LANG']['not_use'];
        }
        $row['type_money'] = price_format($row['type_money']);
        $row['min_goods_amount'] = price_format($row['min_goods_amount']);
        $row['use_startdate']   = local_date($GLOBALS['_CFG']['date_format'], $row['use_start_date']);
        $row['use_enddate']     = local_date($GLOBALS['_CFG']['date_format'], $row['use_end_date']);
        $arr[] = $row;
    }
    return $arr;
}
/*
 * ajax获取数据，url是js的callback函数
 */
function get_ajax_pager($url, $param, $record_count, $page = 1, $size = 10,$jump='')
{
    $size = intval($size);
    if ($size < 1)
    {
        $size = 10;
    }

    $page = intval($page);
    if ($page < 1)
    {
        $page = 1;
    }

    $record_count = intval($record_count);

    $page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
    if ($page > $page_count)
    {
        $page = $page_count;
    }
    /* 分页样式 */
    $pager['styleid'] = isset($GLOBALS['_CFG']['page_style'])? intval($GLOBALS['_CFG']['page_style']) : 0;

    $page_prev  = ($page > 1) ? $page - 1 : 1;
    $page_next  = ($page < $page_count) ? $page + 1 : $page_count;

    /* 将参数合成url字串 */
    $param_url = '?';
    foreach ($param AS $key => $value)
    {
        $param_url .= $key . '=' . $value . '&';
    }

    $pager['url']          = $url;
    $pager['start']        = ($page -1) * $size;
    $pager['page']         = $page;
    $pager['size']         = $size;
    $pager['record_count'] = $record_count;
    $pager['page_count']   = $page_count;
    if(!empty($jump)){
        $pager['jump'] = $jump;
    }
    if ($pager['styleid'] == 0)
    {
        $pager['page_first']   = $url . $param_url . 'page=1';
        $pager['page_prev']    = $url . $param_url . 'page=' . $page_prev;
        $pager['page_next']    = $url . $param_url . 'page=' . $page_next;
        $pager['page_last']    = $url . $param_url . 'page=' . $page_count;
        $pager['array']  = array();
        for ($i = 1; $i <= $page_count; $i++)
        {
            $pager['array'][$i] = $i;
        }
    }
    else
    {
        $_pagenum = 10;     // 显示的页码
        $_offset = 2;       // 当前页偏移值
        $_from = $_to = 0;  // 开始页, 结束页
        if($_pagenum > $page_count)
        {
            $_from = 1;
            $_to = $page_count;
        }
        else
        {
            $_from = $page - $_offset;
            $_to = $_from + $_pagenum - 1;
            if($_from < 1)
            {
                $_to = $page + 1 - $_from;
                $_from = 1;
                if($_to - $_from < $_pagenum)
                {
                    $_to = $_pagenum;
                }
            }
            elseif($_to > $page_count)
            {
                $_from = $page_count - $_pagenum + 1;
                $_to = $page_count;
            }
        }
        $url_format = 'javascript:'.$url;
        $pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? $url_format .'(1)' : '';
        $pager['page_prev']  = ($page > 1) ? $url_format .'(' .$page_prev .')': '';
        $pager['page_next']  = ($page < $page_count) ? $url_format .'(' .$page_next . ')': '';
        $pager['page_last']  = ($_to < $page_count) ? $url_format . '(' . $page_count . ')': '';
        $pager['page_kbd']  = ($_pagenum < $page_count) ? true : false;
        $pager['page_number'] = array();
        for ($i=$_from;$i<=$_to;++$i)
        {
            $pager['page_number'][$i] = $url_format .'(' . $i .')';
        }
    }
    $pager['search'] = $param;

    return $pager;
}

/*检测字符串是否是手机号码*/
function is_mobile_phone($mobile = ''){
    if(!empty($mobile)){
        $search ='/^(1[3|5|7|8][0-9])\d{8}$/';
        if(preg_match($search,$mobile)) {
            return true;
        }
    }
    return false;
}

/*分词*/
function splitword($string){
    require_once ROOT_PATH.'includes/cls_splitword.php';
    $sp = new SplitWord();
    $sp->SetSource($string);
    $sp->StartAnalysis();
    $res = $sp->GetFinallyKeywords();
    if(!empty($res)){
        return explode(',', $res);
    }  
    return false;
}

/*注册送红包*/
function registe_give_bonus($user_id){
    $time = gmtime();
    /*注册送红包*/
    $sql = 'SELECT type_id FROM ' . $GLOBALS['ecs']->table("bonus_type").' WHERE send_type = 4  AND send_start_date <= '.$time.' AND send_end_date >= '.$time.' AND type_money > 0';
    $bonus = $GLOBALS['db']->getALL($sql,true);
    if(!empty($bonus) && is_array($bonus)){
        foreach($bonus as $type_id){
            $sql = "INSERT INTO ".$GLOBALS['ecs']->table('user_bonus')."(bonus_type_id, bonus_sn, user_id, used_time, order_id, emailed)"."VALUES('".$type_id."', 0, '".$user_id."', 0, 0, 0)";
            $GLOBALS['db']->query($sql);
        }
    }
}

/*根据广告位置标记获取广告*/
function get_ads_by_tag ($ad_tag = ''){
    $now = gmtime();
    $sql = "SELECT a.*, ap.ad_width, ap.ad_height, ap.position_style, ap.position_tag FROM " . $GLOBALS['ecs']->table('ad') . " AS a " .  
           "LEFT JOIN " . $GLOBALS['ecs']->table('ad_position') . " AS ap ON a.position_id = ap.position_id" . 
           " WHERE a.start_time < $now AND a.end_time > $now AND enabled = 1 AND media_type = 0 AND ap.position_tag = '$ad_tag'" . 
           " ORDER BY a.sort_order ASC";
    $ads = $GLOBALS['db']->getAll($sql);
    if(!empty($ads)){
        foreach($ads as $akey => $ad){
            $ad['src'] = (strpos($ad['ad_code'], 'http://') === false && strpos($ad['ad_code'], 'https://') === false) ?
                        DATA_DIR . "/afficheimg/$ad[ad_code]" : $ad['ad_code'];
            $GLOBALS['smarty']->assign('ad',$ad);
            $ads[$akey]['html'] = $GLOBALS['smarty']->fetch('str:'.$ad['position_style']);
        }
    }
    return $ads;
}

/*处理用户名*/
function user_name_handle($user_info){
    if($user_info['user_type'] > 0){
        return $user_info['nickname'];
    }
    return $user_info['user_name'];
}

/*支付成功回调函数*/
function pay_success_respond(){
    //支付完成之后显示51活动页
    //$redict_url = "activity.php?apage=51activity&show_deaw_tips=1#draw-edge";
    $result = '';
    if(isset($_REQUEST['is_ajax'])){
        $result['redict_url'] = $redict_url;
        return $result;
    } else {
        /*关闭提示页面*/
        //header("Location: $redict_url");
        echo '<script type="text/javascript">window.close();</script>';
    }
}

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @return String
 */
 function encode($string = '', $skey = 'deebei') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
 }
 
  /**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @return String
 */
 function decode($string = '', $skey = 'deebei') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
 }
 
 /**
  * 获取专题页的商品
  */
 function get_topic_goods($topic_id){
    $result = array();
    if($topic_id > 0 ){
        //获取专题页的分类
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('topic_class') . " WHERE topic_id = $topic_id";
        $class = $GLOBALS['db']->getAll($sql);
        if(!empty($class)){
            for($i = 0; $i < count($class); $i++){
                $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('topic_goods') . " WHERE topic_id = $topic_id AND class_id = {$class[$i]['class_id']}";
                $goods = $GLOBALS['db']->getAll($sql);
            }
        }
    }
    return $result;
 }