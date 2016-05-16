<?php

/**
 * ECSHOP 商品类型管理程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: goods_type.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$exc = new exchange($ecs->table("goods_type"), $db, 'cat_id', 'cat_name');

/*------------------------------------------------------ */
//-- 管理界面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'manage')
{
    assign_query_info();

    $smarty->assign('ur_here',          $_LANG['08_goods_type']);
    $smarty->assign('full_page',        1);

    $good_type_list = get_goodstype();
    $good_in_type = '';

    $smarty->assign('goods_type_arr',   $good_type_list['type']);
    $smarty->assign('filter',       $good_type_list['filter']);
    $smarty->assign('record_count', $good_type_list['record_count']);
    $smarty->assign('page_count',   $good_type_list['page_count']);

    $query = $db->query("SELECT a.cat_id FROM " . $ecs->table('attribute') . " AS a RIGHT JOIN " . $ecs->table('goods_attr') . " AS g ON g.attr_id = a.attr_id GROUP BY a.cat_id");
     while ($row = $db->fetchRow($query))
    {
        $good_in_type[$row['cat_id']]=1;
    }
    $smarty->assign('good_in_type', $good_in_type);

    $smarty->assign('action_link',      array('text' => $_LANG['new_goods_type'], 'href' => 'goods_type.php?act=add'));

    $smarty->display('goods_type.htm');
}

/*------------------------------------------------------ */
//-- 获得列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $good_type_list = get_goodstype();

    $smarty->assign('goods_type_arr',   $good_type_list['type']);
    $smarty->assign('filter',       $good_type_list['filter']);
    $smarty->assign('record_count', $good_type_list['record_count']);
    $smarty->assign('page_count',   $good_type_list['page_count']);

    make_json_result($smarty->fetch('goods_type.htm'), '',
        array('filter' => $good_type_list['filter'], 'page_count' => $good_type_list['page_count']));
}

/*------------------------------------------------------ */
//-- 修改商品类型名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_type_name')
{
    check_authz_json('goods_type');

    $type_id   = !empty($_POST['id'])  ? intval($_POST['id']) : 0;
    $type_name = !empty($_POST['val']) ? json_str_iconv(trim($_POST['val']))  : '';

    /* 检查名称是否重复 */
    $is_only = $exc->is_only('cat_name', $type_name, $type_id);

    if ($is_only)
    {
        $exc->edit("cat_name='$type_name'", $type_id);

        admin_log($type_name, 'edit', 'goods_type');

        make_json_result(stripslashes($type_name));
    }
    else
    {
        make_json_error($_LANG['repeat_type_name']);
    }
}

/*------------------------------------------------------ */
//-- 切换启用状态
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'toggle_enabled')
{
    check_authz_json('goods_type');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("enabled='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 添加商品类型
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('goods_type');

    $smarty->assign('ur_here',     $_LANG['new_goods_type']);
    $smarty->assign('action_link', array('href'=>'goods_type.php?act=manage', 'text' => $_LANG['goods_type_list']));
    $smarty->assign('action',      'add');
    $smarty->assign('form_act',    'insert');
    $smarty->assign('goods_type',  array('enabled' => 1));

    assign_query_info();
    $smarty->display('goods_type_info.htm');
}

elseif ($_REQUEST['act'] == 'insert')
{
    //$goods_type['cat_name']   = trim_right(sub_str($_POST['cat_name'], 60));
    //$goods_type['attr_group'] = trim_right(sub_str($_POST['attr_group'], 255));
    $goods_type['cat_name']   = sub_str($_POST['cat_name'], 60);
    $goods_type['attr_group'] = sub_str($_POST['attr_group'], 255);
    $goods_type['enabled']    = intval($_POST['enabled']);

    if ($db->autoExecute($ecs->table('goods_type'), $goods_type) !== false)
    {
        $links = array(array('href' => 'goods_type.php?act=manage', 'text' => $_LANG['back_list']));
        sys_msg($_LANG['add_goodstype_success'], 0, $links);
    }
    else
    {
        sys_msg($_LANG['add_goodstype_failed'], 1);
    }
}

/*------------------------------------------------------ */
//-- 编辑商品类型
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{
    $goods_type = get_goodstype_info(intval($_GET['cat_id']));

    if (empty($goods_type))
    {
        sys_msg($_LANG['cannot_found_goodstype'], 1);
    }

    admin_priv('goods_type');

    $smarty->assign('ur_here',     $_LANG['edit_goods_type']);
    $smarty->assign('action_link', array('href'=>'goods_type.php?act=manage', 'text' => $_LANG['goods_type_list']));
    $smarty->assign('action',      'add');
    $smarty->assign('form_act',    'update');
    $smarty->assign('goods_type',  $goods_type);

    assign_query_info();
    $smarty->display('goods_type_info.htm');
}

elseif ($_REQUEST['act'] == 'update')
{
    $goods_type['cat_name']   = sub_str($_POST['cat_name'], 60);
    $goods_type['attr_group'] = sub_str($_POST['attr_group'], 255);
    $goods_type['enabled']    = intval($_POST['enabled']);
    $cat_id                   = intval($_POST['cat_id']);
    $old_groups               = get_attr_groups($cat_id);

    if ($db->autoExecute($ecs->table('goods_type'), $goods_type, 'UPDATE', "cat_id='$cat_id'") !== false)
    {
        /* 对比原来的分组 */
        $new_groups = explode("\n", str_replace("\r", '', $goods_type['attr_group']));  // 新的分组

        foreach ($old_groups AS $key=>$val)
        {
            $found = array_search($val, $new_groups);

            if ($found === NULL || $found === false)
            {
                /* 老的分组没有在新的分组中找到 */
                update_attribute_group($cat_id, $key, 0);
            }
            else
            {
                /* 老的分组出现在新的分组中了 */
                if ($key != $found)
                {
                    update_attribute_group($cat_id, $key, $found); // 但是分组的key变了,需要更新属性的分组
                }
            }
        }

        $links = array(array('href' => 'goods_type.php?act=manage', 'text' => $_LANG['back_list']));
        sys_msg($_LANG['edit_goodstype_success'], 0, $links);
    }
    else
    {
        sys_msg($_LANG['edit_goodstype_failed'], 1);
    }
}

/*------------------------------------------------------ */
//-- 删除商品类型
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('goods_type');

    $id = intval($_GET['id']);

    $name = $exc->get_name($id);

    if ($exc->drop($id))
    {
        admin_log(addslashes($name), 'remove', 'goods_type');

        /* 清除该类型下的所有属性 */
        $sql = "SELECT attr_id FROM " .$ecs->table('attribute'). " WHERE cat_id = '$id'";
        $arr = $db->getCol($sql);

        $GLOBALS['db']->query("DELETE FROM " .$ecs->table('attribute'). " WHERE attr_id " . db_create_in($arr));
        $GLOBALS['db']->query("DELETE FROM " .$ecs->table('goods_attr'). " WHERE attr_id " . db_create_in($arr));

        $url = 'goods_type.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error($_LANG['remove_failed']);
    }
}

/*------------------------------------------------------ */
//-- 商品属性
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'goods_attr_list'){
    require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $code   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $suppliers_id = isset($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? '' : trim($_REQUEST['suppliers_id'])) : '';
    $is_on_sale = isset($_REQUEST['is_on_sale']) ? ((empty($_REQUEST['is_on_sale']) && $_REQUEST['is_on_sale'] === 0) ? '' : trim($_REQUEST['is_on_sale'])) : '';

    $handler_list = array();
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');

    if ($_REQUEST['act'] == 'goods_attr_list' && isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
    }

    /* 供货商名 */
    $suppliers_list_name = suppliers_list_name();
    $suppliers_exists = 1;
    if (empty($suppliers_list_name))
    {
        $suppliers_exists = 0;
    }
    $smarty->assign('is_on_sale', $is_on_sale);
    $smarty->assign('suppliers_id', $suppliers_id);
    $smarty->assign('suppliers_exists', $suppliers_exists);
    $smarty->assign('suppliers_list_name', $suppliers_list_name);
    unset($suppliers_list_name, $suppliers_exists);

    /* 模板赋值 */
    $goods_ur = array('' => $_LANG['01_goods_list'], 'virtual_card'=>$_LANG['50_virtual_card_list']);
 
    $smarty->assign('ur_here', $_LANG['09_goods_attr']);

    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'goods.php?act=list', 'text' => $_LANG['01_goods_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('code',     $code);
    $smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('intro_list',   get_intro_list());
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('list_type',    $_REQUEST['act'] == 'list' ? 'goods' : 'trash');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    $suppliers_list = suppliers_list_info(' is_check = 1 ');
    $suppliers_list_count = count($suppliers_list);
    $smarty->assign('suppliers_list', ($suppliers_list_count == 0 ? 0 : $suppliers_list)); // 取供货商列表

    // function path -> lib_goods.php line 812
    $goods_list = goods_list(0);
    
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('full_page',    1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);

    /* 显示商品列表页面 */
    assign_query_info();
    
    /*获取商品类型*/
    $good_type_list = get_goodstype();
    $goods_type = array();
    if(!empty($good_type_list['type'])){
        foreach($good_type_list['type'] as $gtk=>$gtv){
            $goods_type[$gtk] = $gtv;
            $goods_type[$gtk]['attrs'] = get_attrlist(array('goods_type'=>$gtv['cat_id'],'sort_by'=>'sort_order','sort_order'=>'DESC'));
        }
    }
    $smarty->assign('goods_type_list',$goods_type);
    $smarty->assign('full_page',1);
    $smarty->display("goods/goods_attr_list.htm");
}

elseif ($_REQUEST['act'] == 'attr_query')
{
    require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $goods_type_id = isset($_REQUEST['goods_type'])?intval($_REQUEST['goods_type']):0;
    
    $goods_list = goods_list(0);
    if(!empty($goods_type_id)){
        $goods_type_filter = array(
            'goods_type'=>$goods_type_id,
            'sort_by'=>'sort_order',
            'sort_order'=>'DESC'
        );
        $goods_type = get_attrlist($goods_type_filter);
        $smarty->assign('goods_type',$goods_type);
        if(!empty($goods_list['goods'])){
            foreach ($goods_list['goods'] as $glk=>$goods){
                //获取每个商品的属性
                $sql = "SELECT ga.*,at.attr_name FROM ".$ecs->table("goods_attr")." AS ga ".
                       " LEFT JOIN ".$ecs->table("attribute")."AS at ON ga.attr_id = at.attr_id".
                       " WHERE at.cat_id = ".$goods_type_id ." AND ga.goods_id = ".$goods['goods_id'].
                       " ORDER BY at.sort_order DESC, at.attr_id ASC";
                $attrs = $db->getAll($sql);
                $tmp_attrs = array();
                if(!empty($attrs)){
                    foreach($attrs as $attr){
                        $tmp_attrs[$attr['attr_id']] = $attr;
                    }
                }
                $goods_list['goods'][$glk]['attrs'] = $tmp_attrs;
            }
        }
    }
    
    $handler_list = array();
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');

    if (isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
    }
    $goods_list['filter']['goods_type'] = $goods_type_id;
    $smarty->assign('code',         $code);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('list_type',    $is_delete ? 'trash' : 'goods');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);

    $tpl = $is_delete ? 'goods/goods_attr_list.htm' : 'goods/goods_attr_list.htm';

    make_json_result($smarty->fetch($tpl), '',
        array('filter' => $goods_list['filter'], 'page_count' => $goods_list['page_count']));
}
elseif ($_REQUEST['act'] == 'edit_goods_attr'){
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    $result = array();
    $result['error'] = 0;
    $result['message'] = '';
    $result['content'] = '<div style="width:30px;">&nbsp;</div>';
    $parms = isset($_REQUEST['JSON'])?$json->decode($_REQUEST['JSON'],1):'';
    if(!empty($parms)){
        $data['attr_id'] = trim($parms['attr_id']);
        $data['goods_id'] = trim($parms['goods_id']);
        $data['attr_value'] = trim($parms['val']);
        $goods_attr_id = trim($parms['goods_attr_id']);
        //商品属性为空，则新插入到数据库
        if(empty($goods_attr_id)){
            if(!empty($data['attr_value'])){
                $GLOBALS['cidb']->insert("goods_attr",$data);
            }
        } else {
            //如果属性值为空则删除数据库相应的记录
            if(empty($data['attr_value'])){
                $GLOBALS['cidb']->where('goods_attr_id = '.$goods_attr_id)->limit(1)->delete("goods_attr");               
            } else {
                $GLOBALS['cidb']->where('goods_attr_id = '.$goods_attr_id)->limit(1)->update("goods_attr",$data);            
            }       
        }
        
        if(!empty($data['attr_value'])){
            $result['content'] = $data['attr_value'];
        }
    }
    die(json_encode($result));
}
elseif($_REQUEST['act'] == 'edit_jquery_data'){
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    $result = array();
    $result['error'] = 0;
    $result['message'] = '';
    $result['content'] = '';
    $parms = isset($_REQUEST['JSON'])?$json->decode($_REQUEST['JSON'],1):'';
    if(!empty($parms)){
        if(!empty($parms['attr_id'])){
            $attr = $GLOBALS['cidb']->where("attr_id = ".$parms['attr_id'])->get("attribute")->row_array();
            $attr_values = explode("\n", $attr['attr_values']);
            if($attr['attr_input_type'] == 1){
                $result['is_select'] = 1;
            }
            if(!empty($attr_values)){
                array_unshift($attr_values, '');
            }
            $result['content'] = $attr_values;
        }
    }
    die(json_encode($result));
}
elseif($_REQUEST['act'] == 'edit_goods_sub_title'){
    $goods_id   = intval($_POST['id']);
    $goods_sub_title = json_str_iconv(trim($_POST['val']));
    if ($GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods'), array('goods_sub_title'=>$goods_sub_title), "UPDATE", 'goods_id = '.$goods_id ))
    {
        clear_cache_files();
        make_json_result(stripslashes($goods_sub_title));
    }
}
/**
 * 获得所有商品类型
 *
 * @access  public
 * @return  array
 */
function get_goodstype()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        /* 记录总数以及页数 */
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('goods_type');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查询记录 */
        $sql = "SELECT t.*, COUNT(a.cat_id) AS attr_count ".
               "FROM ". $GLOBALS['ecs']->table('goods_type'). " AS t ".
               "LEFT JOIN ". $GLOBALS['ecs']->table('attribute'). " AS a ON a.cat_id=t.cat_id ".
               "GROUP BY t.cat_id " .
               'LIMIT ' . $filter['start'] . ',' . $filter['page_size'];
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $all = $GLOBALS['db']->getAll($sql);

    foreach ($all AS $key=>$val)
    {
        $all[$key]['attr_group'] = strtr($val['attr_group'], array("\r" => '', "\n" => ", "));
    }

    return array('type' => $all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/**
 * 获得指定的商品类型的详情
 *
 * @param   integer     $cat_id 分类ID
 *
 * @return  array
 */
function get_goodstype_info($cat_id)
{
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('goods_type'). " WHERE cat_id='$cat_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 更新属性的分组
 *
 * @param   integer     $cat_id     商品类型ID
 * @param   integer     $old_group
 * @param   integer     $new_group
 *
 * @return  void
 */
function update_attribute_group($cat_id, $old_group, $new_group)
{
    $sql = "UPDATE " . $GLOBALS['ecs']->table('attribute') .
            " SET attr_group='$new_group' WHERE cat_id='$cat_id' AND attr_group='$old_group'";
    $GLOBALS['db']->query($sql);
}

/**
 * 获取属性列表
 *
 * @return  array
 */
function get_attrlist($filter = array())
{
    /* 查询条件 */
//    $filter = array();
//    $filter['goods_type'] = empty($_REQUEST['goods_type']) ? 0 : intval($_REQUEST['goods_type']);
//    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'sort_order' : trim($_REQUEST['sort_by']);
//    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    
    $where = (!empty($filter['goods_type'])) ? " WHERE a.cat_id = '$filter[goods_type]' " : '';

    /* 分页大小 */
    $filter = page_and_size($filter);

    /* 查询 */
    $sql = "SELECT a.*, t.cat_name " .
            " FROM " . $GLOBALS['ecs']->table('attribute') . " AS a ".
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods_type') . " AS t ON a.cat_id = t.cat_id " . $where .
            " ORDER BY $filter[sort_by] $filter[sort_order] , attr_id ASC";

    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
        $row[$key]['attr_input_type_desc'] = $GLOBALS['_LANG']['value_attr_input_type'][$val['attr_input_type']];
        $row[$key]['attr_values']      = str_replace("\n", ", ", $val['attr_values']);
    }
    
    return $row;
}

?>
