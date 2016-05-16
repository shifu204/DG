<?php

/**
 * ECSHOP 商品分类
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: category.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$smarty->assign('ecs_css_path','themes/'.$_CFG['template'].'/style1.css');
$smarty->assign('theme_path','themes/'.$_CFG['template'].'/');
assign_template();

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得请求的分类 ID */
if (isset($_REQUEST['id']))
{
    $cat_id = intval($_REQUEST['id']);
}
elseif (isset($_REQUEST['category']))
{
    $cat_id = intval($_REQUEST['category']);
}
if(isset($_REQUEST['brand'])){
    $brand_id = intval($_REQUEST['brand']);
}
else {
    $brand_id = 0;
}
/*分类选择选中的分类ID*/
$filter_id = isset($_REQUEST['filter_id']) ? trim($_REQUEST['filter_id']) : 0;
$filter_ids = explode(',', $filter_id);
/*选中的其它选项*/
$other_filter = isset($_REQUEST['other_filter']) ?  $_REQUEST['other_filter'] : array();
$cat_info = get_cat_info($cat_id);
$cat_nav = array();
$time = time();
//$filt = $_SESSION['category_filt'];
//if($filt['category'] != $cat_id){
//    $filt = array();
//    $filt['category'] = $cat_id;
//    $filt['page'] = 1;
//}
$filt = array();
$filt['category'] = $cat_id;
$filt['brand_id'] = $brand_id;
$filt['other_filter'] = $other_filter;
$dwt = 'common.dwt';
$smarty->assign('page_title', $position['title']);    // 页面标题
$smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
if(isset($cat_info['style']) && !empty($cat_info['style'])){
    //$dwt = 'category_sty1.dwt';
    $ext = strrchr($cat_info['style'],'.');
    $style = substr($cat_info['style'], 0 , strlen($cat_info['style']) - strlen($ext));
    if(!empty($style)){
        $dwt = $style.'.dwt';
    }
} 
$smarty->assign('category_style',$style);
$page = 1;
if(isset($_REQUEST['page'])){
    $page = intval($_REQUEST['page']);
} else if(isset($filt['page']) && !empty($filt['page'])){
    $page = $filt['page'];
}

$page_size = 48;
$offset = ($page - 1) * $page_size;

$cat_nav['brand'] = get_catetory_brands($cat_id);
//获取该分类下的商品选择分类
$tag_name = $cat_info['cat_tag']."_filter";
$nav_filters = $GLOBALS['cidb']->select('cat_id,cat_name')->like("cat_tag",$tag_name,'after')->where("is_show = 0 ")->order_by('sort_order ASC')->get('category')->result_array();
if(!empty($nav_filters)){
    foreach($nav_filters as $n_filter){
        $cat_nav['id'][$n_filter['cat_id']] = $n_filter;
        $cat_nav['id'][$n_filter['cat_id']]['children'] = get_child_tree($n_filter['cat_id'],0);
        //选中filter_ids的父分类
        if(is_array($filter_ids)){
            $filt['selected_filter'][$n_filter['cat_id']] = array();  
            foreach($filter_ids as $temp){                
                if(array_key_exists($temp, $cat_nav['id'][$n_filter['cat_id']]['children'])){
                    $filt['selected_filter'][$n_filter['cat_id']][] = $temp;
                } 
            }
            if(empty($filt['selected_filter'][$n_filter['cat_id']])) {
                //如果没有选中分类下的子分类，则删除选中数组
                unset($filt['selected_filter'][$n_filter['cat_id']]);
            }
        }
    }
}
//获取左侧导航栏类目
$left_tag_name = $cat_info['cat_tag']."_filter_left";
$left_filter = $GLOBALS['cidb']->select('cat_id,cat_name')->where('cat_tag = "'.$left_tag_name.'" AND is_show = 0 ')->order_by('sort_order ASC')->get('category')->row_array();
if(!empty($left_filter)){
    $left_filter['children'] = get_child_tree($left_filter['cat_id'],0);
}
$smarty->assign("left_filter",$left_filter);

if($_REQUEST['act'] == 'ajax') {
    $filt = array();
    foreach($_POST as $pk=>$pv){
        $filt[$pk] = $pv;
    }
    //过滤filt的非零值
    if(isset($filt['filt'])){
        foreach($filt['filt'] as $fk => $fv){
            if(empty($fv)){
                unset($filt['filt'][$fk]);
            }
        }
    }
    if(isset($_REQUEST['brand_id']) && !empty($_REQUEST['brand_id'])){
        $filt['brand_id'] = intval($_REQUEST['brand_id']);
    }
    $page = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
    $offset = ($page - 1) * $page_size;
    $filt['page'] = $page;
    //获取某分类下的所有商品
    $return = get_goods($filt,$page_size,$offset);
    $total = $return['total'];
    $goods = $return['goods'];    
    $filt['brand_num'] = 1;
    $not_zero_brand = get_filt_brand_num($filt);  
    $pager = get_ajax_pager('getData', array(), $total, $page, $page_size, "#filter_bar");
    $smarty->assign('pager',$pager); 
    $smarty->assign('goods',$goods);
    $smarty->assign('total',$total);
    $content = $smarty->fetch('library/goods/category_goods_list.lbi', null, null, false);
    $top_page = $smarty->fetch('category/library/category_filter_bar_page.lbi', null, null, false);
    $result['content'] = $content;
    $result['able_brand'] = $not_zero_brand;
    $result['top_page'] = $top_page;
    $_SESSION['category_filt'] = $filt;    
    die(json_encode($result));
}

//获取选中分类的信息
if(!empty($filter_ids)){
    $filt['filt'] = $filter_ids;
}

$return = get_goods($filt,$page_size,$offset);
$smarty->assign('goods',$return['goods']);
$smarty->assign('total',$return['total']);
$smarty->assign('filt',$filt);
$pager = get_ajax_pager('getData', array(), $return['total'], $page, $page_size, "#filter_bar");
$smarty->assign('pager',$pager);
$smarty->assign('cat_nav',$cat_nav);
$smarty->assign("cat_info",$cat_info);
$smarty->assign("category",$cat_id);

//页面位置信息
$position = assign_ur_here($cat_id, $brand_name);
$smarty->assign('helps',            get_shop_help());              // 网店帮助
$smarty->assign('page_title',       $position['title']);  
$smarty->assign('ur_here',          $position['ur_here']);
$smarty->display('category/'.$dwt);
exit;

/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
$brand = isset($_REQUEST['brand']) && intval($_REQUEST['brand']) > 0 ? intval($_REQUEST['brand']) : 0;
$price_max = isset($_REQUEST['price_max']) && intval($_REQUEST['price_max']) > 0 ? intval($_REQUEST['price_max']) : 0;
$price_min = isset($_REQUEST['price_min']) && intval($_REQUEST['price_min']) > 0 ? intval($_REQUEST['price_min']) : 0;
$filter_attr_str = isset($_REQUEST['filter_attr']) ? htmlspecialchars(trim($_REQUEST['filter_attr'])) : '0';

$filter_attr_str = trim(urldecode($filter_attr_str));
$filter_attr_str = preg_match('/^[\d\.]+$/',$filter_attr_str) ? $filter_attr_str : '';
$filter_attr = empty($filter_attr_str) ? '' : explode('.', $filter_attr_str);


/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type   = 'sort_order, '.($_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update'));

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
FB::Warn($sort);
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
setcookie('ECS[display]', $display, gmtime() + 86400 * 7);
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 页面的缓存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' .
    $_CFG['lang'] .'-'. $brand. '-' . $price_max . '-' .$price_min . '-' . $filter_attr_str));

if (!$smarty->is_cached('category.dwt', $cache_id))
{
    /* 如果页面没有被缓存则重新获取页面的内容 */

    $children = get_children($cat_id);

    $cat = get_cat_info($cat_id);   // 获得分类的相关信息

    if (!empty($cat))
    {
        $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
        $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
        $smarty->assign('cat_style',   htmlspecialchars($cat['style']));
    }
    else
    {
        /* 如果分类不存在则返回首页 */
        ecs_header("Location: ./\n");

        exit;
    }

    /* 赋值固定内容 */
    if ($brand > 0)
    {
        $sql = "SELECT brand_name FROM " .$GLOBALS['ecs']->table('brand'). " WHERE brand_id = '$brand'";
        $brand_name = $db->getOne($sql);
    }
    else
    {
        $brand_name = '';
    }

    /* 获取价格分级 */
    if ($cat['grade'] == 0  && $cat['parent_id'] != 0)
    {
        $cat['grade'] = get_parent_grade($cat_id); //如果当前分类级别为空，取最近的上级分类
    }

    if ($cat['grade'] > 1)
    {
        /* 需要价格分级 */

        /*
            算法思路：
                1、当分级大于1时，进行价格分级
                2、取出该类下商品价格的最大值、最小值
                3、根据商品价格的最大值来计算商品价格的分级数量级：
                        价格范围(不含最大值)    分级数量级
                        0-0.1                   0.001
                        0.1-1                   0.01
                        1-10                    0.1
                        10-100                  1
                        100-1000                10
                        1000-10000              100
                4、计算价格跨度：
                        取整((最大值-最小值) / (价格分级数) / 数量级) * 数量级
                5、根据价格跨度计算价格范围区间
                6、查询数据库

            可能存在问题：
                1、
                由于价格跨度是由最大值、最小值计算出来的
                然后再通过价格跨度来确定显示时的价格范围区间
                所以可能会存在价格分级数量不正确的问题
                该问题没有证明
                2、
                当价格=最大值时，分级会多出来，已被证明存在
        */

        $sql = "SELECT min(g.shop_price) AS min, max(g.shop_price) as max ".
               " FROM " . $ecs->table('goods'). " AS g ".
               " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1  ';
               //获得当前分类下商品价格的最大值、最小值

        $row = $db->getRow($sql);

        // 取得价格分级最小单位级数，比如，千元商品最小以100为级数
        $price_grade = 0.0001;
        for($i=-2; $i<= log10($row['max']); $i++)
        {
            $price_grade *= 10;
        }

        //跨度
        $dx = ceil(($row['max'] - $row['min']) / ($cat['grade']) / $price_grade) * $price_grade;
        if($dx == 0)
        {
            $dx = $price_grade;
        }

        for($i = 1; $row['min'] > $dx * $i; $i ++);

        for($j = 1; $row['min'] > $dx * ($i-1) + $price_grade * $j; $j++);
        $row['min'] = $dx * ($i-1) + $price_grade * ($j - 1);

        for(; $row['max'] >= $dx * $i; $i ++);
        $row['max'] = $dx * ($i) + $price_grade * ($j - 1);

        $sql = "SELECT (FLOOR((g.shop_price - $row[min]) / $dx)) AS sn, COUNT(*) AS goods_num  ".
               " FROM " . $ecs->table('goods') . " AS g ".
               " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
               " GROUP BY sn ";

        $price_grade = $db->getAll($sql);

        foreach ($price_grade as $key=>$val)
        {
            $temp_key = $key + 1;
            $price_grade[$temp_key]['goods_num'] = $val['goods_num'];
            $price_grade[$temp_key]['start'] = $row['min'] + round($dx * $val['sn']);
            $price_grade[$temp_key]['end'] = $row['min'] + round($dx * ($val['sn'] + 1));
            $price_grade[$temp_key]['price_range'] = $price_grade[$temp_key]['start'] . '&nbsp;-&nbsp;' . $price_grade[$temp_key]['end'];
            $price_grade[$temp_key]['formated_start'] = price_format($price_grade[$temp_key]['start']);
            $price_grade[$temp_key]['formated_end'] = price_format($price_grade[$temp_key]['end']);
            $price_grade[$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_grade[$temp_key]['start'], 'price_max'=> $price_grade[$temp_key]['end'], 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

            /* 判断价格区间是否被选中 */
            if (isset($_REQUEST['price_min']) && $price_grade[$temp_key]['start'] == $price_min && $price_grade[$temp_key]['end'] == $price_max)
            {
                $price_grade[$temp_key]['selected'] = 1;
            }
            else
            {
                $price_grade[$temp_key]['selected'] = 0;
            }
        }

        $price_grade[0]['start'] = 0;
        $price_grade[0]['end'] = 0;
        $price_grade[0]['price_range'] = $_LANG['all_attribute'];
        $price_grade[0]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>0, 'price_max'=> 0, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
        $price_grade[0]['selected'] = empty($price_max) ? 1 : 0;

        $smarty->assign('price_grade',     $price_grade);

    }


    /* 品牌筛选 */

    $sql = "SELECT b.brand_id, b.brand_name, COUNT(*) AS goods_num ".
            "FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ".
                $GLOBALS['ecs']->table('goods') . " AS g LEFT JOIN ". $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
            "WHERE g.brand_id = b.brand_id AND ($children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), array_keys(cat_list($cat_id, 0, false))))) . ") AND b.is_show = 1 " .
            " AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
            "GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.sort_order, b.brand_id ASC";

    $brands = $GLOBALS['db']->getAll($sql);

    foreach ($brands AS $key => $val)
    {
        $temp_key = $key + 1;
        $brands[$temp_key]['brand_name'] = $val['brand_name'];
        $brands[$temp_key]['url'] = build_uri('category', array('cid' => $cat_id, 'bid' => $val['brand_id'], 'price_min'=>$price_min, 'price_max'=> $price_max, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

        /* 判断品牌是否被选中 */
        if ($brand == $brands[$key]['brand_id'])
        {
            $brands[$temp_key]['selected'] = 1;
        }
        else
        {
            $brands[$temp_key]['selected'] = 0;
        }
    }

    $brands[0]['brand_name'] = $_LANG['all_attribute'];
    $brands[0]['url'] = build_uri('category', array('cid' => $cat_id, 'bid' => 0, 'price_min'=>$price_min, 'price_max'=> $price_max, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
    $brands[0]['selected'] = empty($brand) ? 1 : 0;

    $smarty->assign('brands', $brands);


    /* 属性筛选 */
    $ext = ''; //商品查询条件扩展
    if ($cat['filter_attr'] > 0)
    {
        $cat_filter_attr = explode(',', $cat['filter_attr']);       //提取出此分类的筛选属性
        $all_attr_list = array();

        foreach ($cat_filter_attr AS $key => $value)
        {
            $sql = "SELECT a.attr_name FROM " . $ecs->table('attribute') . " AS a, " . $ecs->table('goods_attr') . " AS ga, " . $ecs->table('goods') . " AS g WHERE ($children OR " . get_extension_goods($children) . ") AND a.attr_id = ga.attr_id AND g.goods_id = ga.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND a.attr_id='$value'";
            if($temp_name = $db->getOne($sql))
            {
                $all_attr_list[$key]['filter_attr_name'] = $temp_name;

                $sql = "SELECT a.attr_id, MIN(a.goods_attr_id ) AS goods_id, a.attr_value AS attr_value FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods') .
                       " AS g" .
                       " WHERE ($children OR " . get_extension_goods($children) . ') AND g.goods_id = a.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
                       " AND a.attr_id='$value' ".
                       " GROUP BY a.attr_value";

                $attr_list = $db->getAll($sql);

                $temp_arrt_url_arr = array();

                for ($i = 0; $i < count($cat_filter_attr); $i++)        //获取当前url中已选择属性的值，并保留在数组中
                {
                    $temp_arrt_url_arr[$i] = !empty($filter_attr[$i]) ? $filter_attr[$i] : 0;
                }

                $temp_arrt_url_arr[$key] = 0;                           //“全部”的信息生成
                $temp_arrt_url = implode('.', $temp_arrt_url_arr);
                $all_attr_list[$key]['attr_list'][0]['attr_value'] = $_LANG['all_attribute'];
                $all_attr_list[$key]['attr_list'][0]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$temp_arrt_url), $cat['cat_name']);
                $all_attr_list[$key]['attr_list'][0]['selected'] = empty($filter_attr[$key]) ? 1 : 0;

                foreach ($attr_list as $k => $v)
                {
                    $temp_key = $k + 1;
                    $temp_arrt_url_arr[$key] = $v['goods_id'];       //为url中代表当前筛选属性的位置变量赋值,并生成以‘.’分隔的筛选属性字符串
                    $temp_arrt_url = implode('.', $temp_arrt_url_arr);

                    $all_attr_list[$key]['attr_list'][$temp_key]['attr_value'] = $v['attr_value'];
                    $all_attr_list[$key]['attr_list'][$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$temp_arrt_url), $cat['cat_name']);

                    if (!empty($filter_attr[$key]) AND $filter_attr[$key] == $v['goods_id'])
                    {
                        $all_attr_list[$key]['attr_list'][$temp_key]['selected'] = 1;
                    }
                    else
                    {
                        $all_attr_list[$key]['attr_list'][$temp_key]['selected'] = 0;
                    }
                }
            }

        }

        $smarty->assign('filter_attr_list',  $all_attr_list);
        /* 扩展商品查询条件 */
        if (!empty($filter_attr))
        {
            $ext_sql = "SELECT DISTINCT(b.goods_id) FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods_attr') . " AS b " .  "WHERE ";
            $ext_group_goods = array();

            foreach ($filter_attr AS $k => $v)                      // 查出符合所有筛选属性条件的商品id */
            {
                if (is_numeric($v) && $v !=0 &&isset($cat_filter_attr[$k]))
                {
                    $sql = $ext_sql . "b.attr_value = a.attr_value AND b.attr_id = " . $cat_filter_attr[$k] ." AND a.goods_attr_id = " . $v;
                    $ext_group_goods = $db->getColCached($sql);
                    $ext .= ' AND ' . db_create_in($ext_group_goods, 'g.goods_id');
                }
            }
        }
    }

    assign_template('c', array($cat_id));

    $position = assign_ur_here($cat_id, $brand_name);
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

    $smarty->assign('categories',       get_categories_tree($cat_id)); // 分类树
    $smarty->assign('helps',            get_shop_help());              // 网店帮助
    $smarty->assign('top_goods',        get_top10());                  // 销售排行
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('category',         $cat_id);
    $smarty->assign('brand_id',         $brand);
    $smarty->assign('price_max',        $price_max);
    $smarty->assign('price_min',        $price_min);
    $smarty->assign('filter_attr',      $filter_attr_str);
    $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-c$cat_id.xml" : 'feed.php?cat=' . $cat_id); // RSS URL

    if ($brand > 0)
    {
        $arr['all'] = array('brand_id'  => 0,
                        'brand_name'    => $GLOBALS['_LANG']['all_goods'],
                        'brand_logo'    => '',
                        'goods_num'     => '',
                        'url'           => build_uri('category', array('cid'=>$cat_id), $cat['cat_name'])
                    );
    }
    else
    {
        $arr = array();
    }

    $brand_list = array_merge($arr, get_brands($cat_id, 'category'));

    $smarty->assign('data_dir',    DATA_DIR);
    $smarty->assign('brand_list',      $brand_list);
    $smarty->assign('promotion_info', get_promotion_info());


    /* 调查 */
    $vote = get_vote();
    if (!empty($vote))
    {
        $smarty->assign('vote_id',     $vote['id']);
        $smarty->assign('vote',        $vote['content']);
    }

    $smarty->assign('best_goods',      get_category_recommend_goods('best', $children, $brand, $price_min, $price_max, $ext));
    $smarty->assign('promotion_goods', get_category_recommend_goods('promote', $children, $brand, $price_min, $price_max, $ext));
    $smarty->assign('hot_goods',       get_category_recommend_goods('hot', $children, $brand, $price_min, $price_max, $ext));

    $count = get_cagtegory_goods_count($children, $brand, $price_min, $price_max, $ext);
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }
    $goodslist = category_get_goods($children, $brand, $price_min, $price_max, $ext, $size, $page, $sort, $order);
    if($display == 'grid')
    {
        if(count($goodslist) % 2 != 0)
        {
            $goodslist[] = array();
        }
    }
    $smarty->assign('goods_list',       $goodslist);
    $smarty->assign('category',         $cat_id);
    $smarty->assign('script_name', 'category');

    assign_pager('category',            $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str); // 分页
    assign_dynamic('category'); // 动态内容

    $smarty->assign('sort' , $sort);
}

$smarty->display('category.dwt', $cache_id);

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得分类的信息
 *
 * @param   integer $cat_id
 *
 * @return  void
 */
function get_cat_info($cat_id)
{
    return $GLOBALS['db']->getRow('SELECT cat_name, keywords, cat_desc, style, grade, filter_attr, parent_id, cat_tag FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");
}

/**
 * 获得分类下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function category_get_goods($children, $brand, $min, $max, $ext, $size, $page, $sort, $order)
{
    // 获得 cat_id 和 parent_id
    $cat_id = preg_replace('|g\.cat_idIN\(\'(\d*)\'\)|Us', '$1', str_replace(' ', '', $children));
    if (is_numeric($cat_id)) {
        $sql = 'SELECT `parent_id` FROM ' . $GLOBALS['ecs']->table('category')
             . ' WHERE `cat_id` = ' . $cat_id;
        $parent_id = $GLOBALS['db']->getOne($sql);
    }

    $display = $GLOBALS['display'];
    $where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
            "g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

    if ($brand > 0)
    {
        $where .=  "AND g.brand_id=$brand ";
    }

    if ($min > 0)
    {
        $where .= " AND g.shop_price >= $min ";
    }

    if ($max > 0)
    {
        $where .= " AND g.shop_price <= $max ";
    }

    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
                "IFNULL(AVG(r.comment_rank),0) AS comment_rank,IF(r.comment_rank,count(*),0) AS  comment_count, ".#增加评价及评论数
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img, g.is_tryonebuy, g.is_buy_five, g.is_buy_seven, b.is_preferential_five, b.is_preferential_seven ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ' .
                "ON b.brand_id = g.brand_id " .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
    'LEFT JOIN  '. $GLOBALS['ecs']->table('comment') .' AS r '.#增加评价及评论数
    'ON r.id_value = g.goods_id AND comment_type = 0 AND r.parent_id = 0 AND r.status = 1 ' .#增加评价及评论数
    "WHERE $where $ext group by g.goods_id ORDER BY ";
    // @hero 品牌和奶粉配方分类按名称排，其它按荐排序
    if ($brand || $parent_id == 9) {
        $sql .= 'g.goods_name';
    } else {
        //@hero
        $sort = str_replace('sort_order', 'g.sort_order', $sort);
        $sql .= "$sort $order";#增加评价及评论数
    }
            //"WHERE $where $ext ORDER BY $sort $order";#原语句
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        /* 处理商品水印图片 */
        $watermark_img = '';

        if ($promote_price != 0)
        {
            $watermark_img = "watermark_promote_small";
        }
        elseif ($row['is_new'] != 0)
        {
            $watermark_img = "watermark_new_small";
        }
        elseif ($row['is_best'] != 0)
        {
            $watermark_img = "watermark_best_small";
        }
        elseif ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot_small';
        }

        if ($watermark_img != '')
        {
            $arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
        }

        $arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
        if($display == 'grid')
        {
            $arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
        $arr[$row['goods_id']]['name']             = $row['goods_name'];
        $arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
        $arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
        $arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']       = price_format($row['shop_price']);
        $arr[$row['goods_id']]['type']             = $row['goods_type'];
        $arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$row['goods_id']]['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $row['comment_rank']  = ceil($row['comment_rank']) == 0 ? 5 : ceil($row['comment_rank']);#增加评价及评论数
        $arr[$row['goods_id']]['comment_rank']=$row['comment_rank'];#增加评价及评论数
        $arr[$row['goods_id']]['comment_count']=$row['comment_count'];#增加评价及评论数

        $arr[$row['goods_id']]['is_tryonebuy']          = $row['is_tryonebuy'];
        $arr[$row['goods_id']]['is_buy_five']           = $row['is_buy_five'];
        $arr[$row['goods_id']]['is_buy_seven']          = $row['is_buy_seven'];
        $arr[$row['goods_id']]['is_preferential_five']  = $row['is_preferential_five'];
        $arr[$row['goods_id']]['is_preferential_seven'] = $row['is_preferential_seven'];
    }

    return $arr;
}

/**
 * 获得分类下的商品总数
 *
 * @access  public
 * @param   string     $cat_id
 * @return  integer
 */
function get_cagtegory_goods_count($children, $brand = 0, $min = 0, $max = 0, $ext='')
{
    $where  = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

    if ($brand > 0)
    {
        $where .=  " AND g.brand_id = $brand ";
    }

    if ($min > 0)
    {
        $where .= " AND g.shop_price >= $min ";
    }

    if ($max > 0)
    {
        $where .= " AND g.shop_price <= $max ";
    }

    /* 返回商品总数 */
    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g WHERE $where $ext");
}

/**
 * 取得最近的上级分类的grade值
 *
 * @access  public
 * @param   int     $cat_id    //当前的cat_id
 *
 * @return int
 */
function get_parent_grade($cat_id)
{
    static $res = NULL;

    if ($res === NULL)
    {
        $data = read_static_cache('cat_parent_grade');
        if ($data === false)
        {
            $sql = "SELECT parent_id, cat_id, grade ".
                   " FROM " . $GLOBALS['ecs']->table('category');
            $res = $GLOBALS['db']->getAll($sql);
            write_static_cache('cat_parent_grade', $res);
        }
        else
        {
            $res = $data;
        }
    }

    if (!$res)
    {
        return 0;
    }

    $parent_arr = array();
    $grade_arr = array();

    foreach ($res as $val)
    {
        $parent_arr[$val['cat_id']] = $val['parent_id'];
        $grade_arr[$val['cat_id']] = $val['grade'];
    }

    while ($parent_arr[$cat_id] >0 && $grade_arr[$cat_id] == 0)
    {
        $cat_id = $parent_arr[$cat_id];
    }

    return $grade_arr[$cat_id];

}

function get_goods($filt = array(),$page_size = 24 , $offset = 0){
    //获取某分类下的所有子分类
    $children = array_keys(cat_list($filt['category'],0,false));
    foreach($filt['filt'] as $fk=>$fv){
        if($fv == 0){
            unset($filt['filt'][$fk]);
        }
    }
    $extend_count = count($filt['filt']);
    $select = "SELECT g.*, og.sales";
    //获取搜索结果的总数
    $select1 = "SELECT g.goods_id ";
    $from = " FROM " . $GLOBALS['ecs']->table("goods") . " AS g ";
    $where = " WHERE 1 AND g.is_on_sale = 1 AND g.is_delete = 0 AND g.cat_id " . db_create_in($children);
    $order_by = " ORDER BY g.sort_order ASC ";
    $group_by .= " GROUP BY g.goods_id ";
    $having = $limit = "";
    
    $from .= " LEFT JOIN (SELECT SUM(goods_number) AS sales, goods_id FROM " . $GLOBALS['ecs']->table("order_goods") . " GROUP BY goods_id ) AS og ON og.goods_id = g.goods_id ";
    
    //如果选中品牌
    if(isset($filt['brand_id']) && $filt['brand_id'] > 0){
        $where .= " AND g.brand_id = " . $filt['brand_id'];
    }
    
    $price_min = floatval($filt['other_filter']['price_min']);
    $price_max = floatval($filt['other_filter']['price_max']);
    if($price_max < $price_min){
        $price_max = 0;
    }
    if($price_min > 0){
        $where .= " AND g.shop_price >= " . $price_min;
    }
    if( $price_max > 0){
        $where .= " AND g.shop_price <= " . $price_max;
    }
    if($extend_count > 0 ){
        $from .= " LEFT JOIN " . $GLOBALS['ecs']->table('goods_cat') . " AS gc ON gc.goods_id = g.goods_id ";
        $from .= " LEFT JOIN " . $GLOBALS['ecs']->table('brand') . " AS b ON b.brand_id = g.brand_id ";
        $where .= " AND gc.cat_id " . db_create_in($filt['filt']);       
        $having .= " HAVING count(*) = " . $extend_count;
    }
    
    /*商品排序*/
    $sort_by = $filt['other_filter']['sort_by'];
    $sort_order = $filt['other_filter']['sort_order'];
    if($sort_by == 'views'){
        $sort_by = 'g.click_count';
    } else if($sort_by == 'rank'){
        $sort_by = 'g.sort_order';
        if($sort_order == 'asc'){
            $sort_order = 'desc';
        } else {
            $sort_order = 'asc';
        }
    } else if($sort_by == 'price'){
        $sort_by = 'g.shop_price';
    } else if($sort_by == 'sales') {
        $sort_by = 'sales';
    } else {
        $sort_by = 'g.sort_order';
    }
    
    $order_by = " ORDER BY $sort_by $sort_order" . ", sort_order ASC";
    $limit .= " LIMIT $offset,$page_size ";
    $sql = $select1.$from.$where.$group_by.$having;
    $total = count($GLOBALS['db']->getAll($sql));
    $sql = $select.$from.$where.$group_by.$having.$order_by.$limit;
    $goods = $GLOBALS['db']->getAll($sql);
    //对商品信息进行格式化
    foreach($goods as $gk=>$gv){
        if ($gv['promote_price'] > 0)
        {
            $promote_price = bargain_price($gv['promote_price'], $gv['promote_start_date'], $gv['promote_end_date']);            
        }
        else
        {
            $promote_price = 0;
        }
        $goods[$gk]['goods_brief']      = $gv['goods_brief'];
        $goods[$gk]['goods_style_name'] = add_style($gv['goods_name'],$gv['goods_name_style']);
        $goods[$gk]['market_price']     = price_format($gv['market_price'],false);
        $goods[$gk]['shop_price']       = ($promote_price > 0) ? price_format($promote_price,false) : price_format($gv['shop_price'],false);
        $goods[$gk]['promote_price']    = ($promote_price > 0) ? price_format($promote_price,false) : '';
        $goods[$gk]['final_price']      = get_final_price($gv['goods_id']);
        $goods[$gk]['formated_final_price']      = price_format($goods[$gk]['final_price'],false);
        $goods[$gk]['goods_thumb']      = get_image_path($gv['goods_id'], $gv['goods_thumb'], true);
        $goods[$gk]['goods_img']        = get_image_path($gv['goods_id'], $gv['goods_img']);
        $goods[$gk]['url']              = build_uri('goods', array('gid'=>$gv['goods_id']), $gv['goods_name']);
        //$goods[$gk]['comment_rank']  = ceil($row['comment_rank']) == 0 ? 5 : ceil($row['comment_rank']);#增加评价及评论数
        $goods[$gk]['is_tryonebuy']          = $gv['is_tryonebuy'];
        $goods[$gk]['is_buy_five']           = $gv['is_buy_five'];
        $goods[$gk]['is_buy_seven']          = $gv['is_buy_seven'];
        $goods[$gk]['is_preferential_five']  = $gv['is_preferential_five'];
        $goods[$gk]['is_preferential_seven'] = $gv['is_preferential_seven'];
        //获取每件商品的评论数
        $comment_num = $GLOBALS['cidb']->select("count(*) as num")->where(array("id_value"=>$gv['goods_id'],"comment_type"=>0,"status"=>1))->get("comment")->result_array();
        $goods[$gk]['comment_num'] = $comment_num[0]['num'];
        //获取每件商品的销量
        $goods[$gk]['sales'] = get_buy_sum($gv['goods_id']);
        if(!empty($gv['goods_same_id'])){
            $sql = "SELECT goods_thumb, goods_img, original_img FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id = {$gv['goods_same_id']}";
            $same = $GLOBALS['db']->getRow($sql);
            $goods[$gk]['goods_img'] = $same['goods_img'];
            $goods[$gk]['goods_thumb'] = $same['goods_thumb'];
            $goods[$gk]['original_img'] = $same['original_img'];
        }
                  
    }
    $return['total'] = $total;
    $return['goods'] = $goods;
    return $return;
}

function get_buy_sum($goods_id) 
{
    $sql = "select sum(goods_number) from " . $GLOBALS['ecs']->table('order_goods') . " AS g ,".$GLOBALS['ecs']->table('order_info') . " AS o WHERE o.order_id=g.order_id and g.goods_id = " . $goods_id  ;
    $num = $GLOBALS['db']->getOne($sql);
    if(empty($num)){
        $num = 0;
    }
    return $num;
}

function get_filt_brand_num($filt = array()){
    //获取某分类下的所有子分类
    $children = array_keys(cat_list($filt['category'],0,false));
    foreach($filt['filt'] as $fk=>$fv){
        if($fv == 0){
            unset($filt['filt'][$fk]);
        }
    }
    $extend_count = count($filt['filt']);
    $GLOBALS['cidb']->select("goods.brand_id")->where_in("goods.cat_id",$children)->where(array("goods.is_on_sale"=>1,"goods.is_delete"=>0));
    $GLOBALS['cidb']->from("goods");
    if($extend_count > 0){
        $GLOBALS['cidb']->where_in("goods_cat.cat_id",$filt['filt'])->join("goods_cat","goods_cat.goods_id = goods.goods_id","left")->join("brand","brand.brand_id = goods.brand_id")->group_by("goods.goods_id")->having("count(*) = " . $extend_count);
    }
    $result = $GLOBALS['cidb']->get()->result_array();
    $return = array();
    if(!empty($result)){
        foreach($result as $brand){
            array_push($return, $brand['brand_id']);
        }
    }
    array_push($return,0);
    $return = array_unique($return);
    return $return;
}

?>
