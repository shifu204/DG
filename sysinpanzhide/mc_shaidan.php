<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$_LANG['shaidantype'][0] = '晒晒商品';
$_LANG['shaidantype'][1] = '晒快递单';
$_LANG['shaidantype'][2] = '随便晒晒';


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
if ($_REQUEST['act'] == 'act_add_shaidan')
{
    include_once(ROOT_PATH . 'includes/lib_shaidan.php');
	$link = array();
	$link[] = array('text' => '晒单列表', 'href' => 'mc_shaidan.php?act=list_all');
	
	$user_id = intval($_POST['user_id']);	
    $sql = 'SELECT user_id, user_name, email FROM ' .$ecs->table('users') ." WHERE user_id ='$user_id'";
    $user_info = $db->getRow($sql);
		
	if(!$user_info['user_id']){
		sys_msg('添加晒单数据失败,不存在该会员;', 0, $link);
	}
	
    $message = array(
        'user_id'     => $user_info['user_id'],
        'user_name'   => $user_info['user_name'],
        'user_email'  => $user_info['email'],
        'status'  => 1,
        'msg_type'    => isset($_POST['msg_type']) ? intval($_POST['msg_type'])     : 0,
        'msg_title'   => isset($_POST['msg_title']) ? trim($_POST['msg_title'])     : '',
        'rec_id'      => 0, // 模拟下单，没有 rec_id
        'order_id'    => 0, // 同上
        'goods_id'    => isset($_POST['goods_id']) ? intval($_POST['goods_id'])     : 0,
        'msg_content' => isset($_POST['msg_content']) ? trim($_POST['msg_content']) : '',
        'order_id'=>empty($_POST['order_id']) ? 0 : intval($_POST['order_id']),
        'upload'      => (isset($_FILES['shaidan_img']['error']) && $_FILES['shaidan_img']['error'] == 0) || (!isset($_FILES['shaidan_img']['error']) && isset($_FILES['shaidan_img']['tmp_name']) && $_FILES['shaidan_img']['tmp_name'] != 'none')
         ? $_FILES['shaidan_img'] : array()
     );
    FB::info($message);
    if (add_shaidan($message))
    {
		sys_msg('恭喜，晒单数据添加成功！', 0, $link);
    }
    else
    {
		sys_msg('添加晒单数据失败,请检查;', 0, $link);
    }
}

/* 获取我要晒单商品 */
elseif ($_REQUEST['act'] == 'get_goods'){
	include_once(ROOT_PATH .'includes/cls_json.php');
	$result = array();
	
	$brand_id = intval($_REQUEST['brand_id']);
	
	if($brand_id){
		$cate = 0;//!empty($_REQUEST['cat'])   && intval($_REQUEST['cat'])   > 0 ? intval($_REQUEST['cat'])   : 0;
		$sort   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
		$order = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
		$cate_where = ($cate > 0) ? 'AND ' . get_children($cate) : '';
		/* 获得商品列表 */
		$sql = 'SELECT goods_id, goods_name FROM ' . $GLOBALS['ecs']->table('goods') . ' ' .
				"WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 AND brand_id = '$brand_id' $cate_where".
				"ORDER BY $sort $order";
		$result = $GLOBALS['db']->getAll($sql);
	}
	$result['goods'] = $result ? $result : ($brand_id ? array(array('goods_id'=>0,'goods_name'=>'无该品牌相关商品')) : array());

    $json = new JSON;
    echo $json->encode($result);
}

/* 晒单删除审核 */
elseif ($_REQUEST['act'] == 'allow_deny'){
	$link = array(); $setnumtype = 0;
	$link[] = array('text' => '晒单列表', 'href' => 'mc_shaidan.php?act=list_all');
	
	$sel_action = $_POST['sel_action'];
	$sel_action = in_array($sel_action,array('remove','allow','deny'))?$sel_action:'';
	
	#商店设置中增加晒单设置积分值
	#INSERT INTO ecs_shop_config(parent_id, code, type, value, sort_order)VALUES('2','shaidanjf','text','30','1')
	if($sel_action=='allow')$setnumtype = 1;$setnum = intval($GLOBALS['_CFG']['shaidanjf']);#获得积分值
	$sql = 'SELECT user_id,msg_status,parent_id FROM ' . $ecs->table('shaidan') . " WHERE " . db_create_in($_POST['checkboxes'], 'msg_id');
	$msg_list = $GLOBALS['db']->getAll($sql);
	foreach ($msg_list AS $key => $value){ 
		if($value['msg_status']&&$sel_action=='allow')$setnum=0; 
		if(!$value['msg_status']&&$sel_action=='remove')$setnum=0; 
		if(!$value['msg_status']&&$sel_action=='deny')$setnum=0; 
		//if($value['parent_id'])$setnum=0; #回复的不送积分
		setshaidanjf($value['user_id'],$setnum,$setnumtype);
	}
	
	$dotxt = '请选择需要处理的晒单数据再执行相关操作！';
	if (isset($_POST['checkboxes'])){
		if($sel_action=='remove'){
			$dotxt = '晒单数据批量删除成功！';
			$db->query("DELETE FROM " . $ecs->table('shaidan') . " WHERE " . db_create_in($_POST['checkboxes'], 'msg_id'));
			$db->query("DELETE FROM " . $ecs->table('shaidan') . " WHERE " . db_create_in($_POST['checkboxes'], 'parent_id'));
		}elseif($sel_action=='allow'){
			$dotxt = '晒单数据批量审核通过并显示成功！';
			$db->query("UPDATE " . $ecs->table('shaidan') . " SET msg_status = 1  WHERE " . db_create_in($_POST['checkboxes'], 'msg_id'));
		}elseif($sel_action=='deny'){
			$dotxt = '晒单数据批量审核撤销并隐藏成功！';
			$db->query("UPDATE " . $ecs->table('shaidan') . " SET msg_status = 0  WHERE " . db_create_in($_POST['checkboxes'], 'msg_id'));
		}
	}
	
	clear_cache_files();
	sys_msg($dotxt, 0, $link);
}
else if ($_REQUEST['act'] == 'allow_single') {
    $msg_id = $_REQUEST['msg_id'] ? intval($_REQUEST['msg_id']) : 0;
    $sql = 'SELECT sh.*, og.goods_name, og.goods_price, og.goods_number, (og.goods_price * og.goods_number) AS goods_amount, is_return FROM ' . $GLOBALS['ecs']->table('shaidan') . ' AS sh'
         . ' LEFT JOIN ' . $GLOBALS['ecs']->table('order_goods') . ' AS og'
         . ' ON sh.rec_id = og.rec_id'
         . ' WHERE msg_id = ' . $msg_id . ' AND is_return = 0 LIMIT 1';
    FB::log($sql);
    $shaidan = $GLOBALS['db']->getRow($sql);
    FB::info($shaidan);
    $smarty->assign('shaidan',      $shaidan);
    $smarty->assign('full_page',    1);
    $smarty->display('mc_shaidan_allow_single.htm');
}

else if ($_REQUEST['act'] == 'shaidan_allow') {
    $msg_id          = $_REQUEST['msg_id'] ? intval($_REQUEST['msg_id']) : 0;

    $sql = 'SELECT user_id, is_return FROM ' . $GLOBALS['ecs']->table('shaidan') . ' WHERE msg_id = ' . $msg_id;
    $shaidan = $GLOBALS['db']->getRow($sql);
    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('shaidan')
         . ' SET msg_status = 1'
         . ' WHERE msg_id = ' . $msg_id . ' AND msg_status = 0';
    $GLOBALS['db']->query($sql);

    $link[] = array('text' => '晒单列表', 'href' => 'mc_shaidan.php?act=list_all');
    sys_msg('审核通过', 0, $link);
}

else if ($_REQUEST['act'] == 'shaidan_return') {
    FB::info($_REQUEST);
    $msg_id          = $_REQUEST['msg_id'] ? intval($_REQUEST['msg_id']) : 0;
    $return_amount    = $_REQUEST['return_amount'] ? floatval($_REQUEST['return_amount']) : 0;
    $return_integral = $_REQUEST['return_integral'] ? floatval($_REQUEST['return_integral']) : 0;
    $highest_amount = 5;
    $highest_integral = 500;
    $return_amount    = $return_amount > $highest_amount ? $highest_amount : $return_amount;
    $return_integral = $return_integral > $highest_integral ? $highest_integral : $return_integral;

    $sql = 'SELECT user_id, is_return FROM ' . $GLOBALS['ecs']->table('shaidan') . ' WHERE msg_id = ' . $msg_id;
    $shaidan = $GLOBALS['db']->getRow($sql);
    if ($shaidan['is_return']) {
        $link[] = array('text' => '晒单列表', 'href' => 'mc_shaidan.php?act=list_all');
        sys_msg('审核失败，晒单请勿重复审核', 0, $link);
    } else {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('shaidan')
             . ' SET return_amount = ' . $return_amount . ', return_integral = ' . $return_integral . ', is_return = 1 , msg_status = 1'
             . ' WHERE msg_id = ' . $msg_id . ' AND is_return = 0';
        $GLOBALS['db']->query($sql);
        if ($return_amount || $return_integral) {
            log_account_change($shaidan['user_id'], $return_amount, 0, 0, $return_integral, '晒单奖励' , '晒单奖励');
        }

        $link[] = array('text' => '晒单列表', 'href' => 'mc_shaidan.php?act=list_all');
        sys_msg('审核通过', 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 操作界面
/*------------------------------------------------------ */
else
{
	$smarty->assign('ur_here', '我要晒单');

    assign_query_info();
    $shaidan_list = get_shaidan_lists();

    $smarty->assign('shaidan_list',     $shaidan_list['lists']);
    $smarty->assign('filter',       $shaidan_list['filter']);
    $smarty->assign('record_count', $shaidan_list['record_count']);
    $smarty->assign('page_count',   $shaidan_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_msg_id', '<img src="images/sort_desc.gif">');
	
	$smarty->assign('brand_list', get_brand_list());

    $smarty->assign('full_page',    1);
    $smarty->display('mc_shaidan.htm');
}

/**
 * 获取评论列表
 * @access  public
 * @return  array
 */
function get_shaidan_lists()
{
    /* 过滤条件 */
    $filter['keywords']   = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
    $filter['msg_type']   = isset($_REQUEST['msg_type']) ? intval($_REQUEST['msg_type']) : -1;
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'f.msg_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = '';
    if ($filter['keywords'])
    {
        $where .= " AND f.msg_title LIKE '%" . mysql_like_quote($filter['keywords']) . "%' ";
    }
    if ($filter['msg_type'] != -1)
    {
        $where .= " AND f.msg_type = '$filter[msg_type]' ";
    }

    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('shaidan'). " AS f WHERE parent_id>=0 " . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);
	
    $sql = "SELECT f.parent_id, f.msg_id, f.user_id, f.user_name, f.msg_title, f.msg_content, f.msg_type, f.rec_id, f.goods_id, f.msg_status, f.msg_time, f.msg_area, ".//, COUNT(r.msg_id) AS reply " .
        " (og.goods_price * og.goods_number) AS goods_amount, f.return_amount, f.return_integral, f.is_return, u.user_type, u.ec_salt ".
            "FROM " . $GLOBALS['ecs']->table('shaidan') . " AS f ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('order_goods') . " AS og ON og.rec_id = f.rec_id ".
            //"LEFT JOIN " . $GLOBALS['ecs']->table('shaidan') . " AS r ON r.parent_id=f.msg_id ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON u.user_id = f.user_id ".
            "WHERE f.parent_id>=0 $where " .
            "GROUP BY f.msg_id ".
            "ORDER by $filter[sort_by] $filter[sort_order] ".
            "LIMIT " . $filter['start'] . ', ' . $filter['page_size'];

    $msg_list = $GLOBALS['db']->getAll($sql);
	//_P($msg_list,1);

    foreach ($msg_list AS $key => $value){   
		if($value['order_id'] > 0){
            //$msg_list[$key]['order_sn'] = $GLOBALS['db']->getOne("SELECT order_sn FROM " . $GLOBALS['ecs']->table('order_info') ." WHERE order_id= " .$value['order_id']);
        }
		$msg_list[$key]['parent_id']    = $value['parent_id'];
		$msg_list[$key]['msg_key']    = $value['msg_id'] + 198400;
        $msg_list[$key]['msg_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['msg_time']);
        $msg_list[$key]['msg_type'] = $GLOBALS['_LANG']['shaidantype'][$value['msg_type']];
		$msg_list[$key]['msg_status'] = $value['msg_status']?true:false;
        $msg_list[$key]['buyer'] = $value['user_type'] ? $value['ec_salt'] : $value['user_name'];
    }

    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('lists' => $msg_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    FB::info($arr);

    return $arr;
}

#增加晒单积分[消费积分或等级积分只可选一]
function setshaidanjf($user_id,$setnum=0,$setnumtype=0){
	$user_id = intval($user_id);
	if(!$user_id||$setnum==0)return;
    $sql = 'SELECT pay_points FROM ' .$GLOBALS['ecs']->table('users'). " WHERE user_id = '{$user_id}'";
    $rs = $GLOBALS['db']->getAll($sql);
	if(!$rs||(!$rs[0]['pay_points']&&!$setnumtype))return;
	$setnumtype = $setnumtype ? '+' : '-';
	$sql = "UPDATE " .$GLOBALS['ecs']->table('users'). " SET".
			" pay_points = pay_points".$setnumtype.$setnum. //消费积分
			//" rank_points = rank_points".$setnumtype.$setnum. //等级积分
			" WHERE user_id = '" . $user_id . "'";
	$GLOBALS['db']->query($sql); 
}
?>
