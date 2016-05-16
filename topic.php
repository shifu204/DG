<?php

/**
 * ECSHOP 专题前台
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * @author:     webboy <laupeng@163.com>
 * @version:    v2.1
 * ---------------------------------------------
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$topic_id  = empty($_REQUEST['topic_id']) ? 0 : intval($_REQUEST['topic_id']);

$sql = "SELECT template FROM " . $ecs->table('topic') .
        "WHERE topic_id = '$topic_id' and  " . gmtime() . " >= start_time and " . gmtime() . "<= end_time";

$topic = $db->getRow($sql);

if(empty($topic))
{
    /* 如果没有找到任何记录则跳回到首页 */
    ecs_header("Location: ./\n");
    exit;
}

$templates = empty($topic['template']) ? 'topic.dwt' : $topic['template'];

$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang'] . '-' . $topic_id));

if (!$smarty->is_cached($templates, $cache_id))
{
    $sql = "SELECT * FROM " . $ecs->table('topic') . " WHERE topic_id = '$topic_id'";

    $topic = $db->getRow($sql);
    //获取专题分类
    $sql = "SELECT * from " . $GLOBALS['ecs']->table('topic_class') . " WHERE topic_id = $topic_id ORDER BY sort_order ASC, class_id ASC";
    $class_res = $GLOBALS['db']->query($sql);
    $topic_class = array();
    while($class_row = $GLOBALS['db']->fetchRow($class_res)){
        $topic_class[$class_row['class_name']] = $class_row;
    }

    $sql = "SELECT tg.*, g.goods_name, g.shop_price, g.is_buy_six, g.is_buy_nine, g.is_total_four, tc.class_name, c.cat_name FROM " . $GLOBALS['ecs']->table('topic_goods') . " AS tg " . 
           " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON g.goods_id = tg.goods_id" . 
           " LEFT JOIN " . $GLOBALS['ecs']->table('topic_class') . " AS tc ON tc.class_id = tg.class_id " .
           " LEFT JOIN " . $GLOBALS['ecs']->table('category') . " AS c on c.cat_id = g.cat_id" . 
           " WHERE tg.topic_id = $topic_id  ORDER BY sort_order ASC, id ASC ";
    
    $res = $GLOBALS['db']->query($sql);
    $goods_list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $row['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $row['promote_price'] = '';
        }

        if ($row['shop_price'] > 0)
        {
            $row['formated_shop_price'] = price_format($row['shop_price']);
        }
        else
        {
            $row['shop_price'] = '';
        }

        $row['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $row['goods_style_name'] = add_style($row['goods_name'], $row['goods_name_style']);
        $row['short_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $row['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $row['short_style_name'] = add_style($row['short_name'], $row['goods_name_style']);
        $row['real_price'] = $row['price'] <= 0 ? $row['shop_price'] : $row['price'];
        $row['formated_real_price'] = price_format($row['real_price']);
        $row['formated_shop_price'] = price_format($row['shop_price']);
        if($row['is_buy_nine'] == 0){
            unset($row['is_buy_nine']);
        }
        if($row['is_buy_six'] == 0){
            unset($row['is_buy_six']);
        }
        if($row['is_total_four'] == 0){
            unset($row['is_total_four']);
        }
        $row['topic_name'] = $row['topic_goods_name'];
        if(empty($row['topic_goods_name'])){
            $row['topic_name'] = $row['goods_name'];
        }
        $goods_list[] = $row;
        $topic_class[$row['class_name']]['goods_list'][] = $row;
    }
    
    $smarty->assign('topic_class', $topic_class);
    $smarty->assign('goods_list', $goods_list);
    /* 买五送一 */
    if ($topic_id == 6) {
        $sql = 'SELECT GROUP_CONCAT(`brand_id`) FROM ' . $GLOBALS['ecs']->table('brand')
             . ' WHERE `is_preferential_five` = 1';
        $brand_id = $GLOBALS['db']->getOne($sql);
        FB::info($brand_id);

        if ($brand_id) {
            $sql = 'SELECT `goods_id`, `goods_name`, g.`brand_id`, `shop_price`,'
                 . ' `goods_thumb`, b.`brand_name`'
                 . ' FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g'
                 . ' LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b'
                 . ' ON b.brand_id = g.brand_id'
                 . ' WHERE g.`brand_id` in (' . $brand_id . ') AND g.`is_on_sale` = 1'
                 . ' AND g.is_alone_sale = 1 AND g.`is_delete` = 0'
                 . ' ORDER BY b.`sort_order`, g.goods_name';
            $goods = $GLOBALS['db']->getAll($sql);

            foreach ($goods as $key => $item) {
                $item['url']              = build_uri('goods', array('gid'=>$item['goods_id']), $item['goods_name']);
                $item['shop_price'] = price_format($item['shop_price']);
                $goods_list[$item['brand_id']][] = $item;
            }
            $smarty->assign('goods_list',       $goods_list);
        }
    }

    /* 买6罐/买12罐 */
    if ($topic_id == 8) {
        $sql = 'SELECT GROUP_CONCAT(`brand_id`) FROM ' . $GLOBALS['ecs']->table('brand')
             . ' WHERE `is_promotion1` = 1';
        $brand_id = $GLOBALS['db']->getOne($sql);
        FB::info($brand_id);

        if ($brand_id) {
            $sql = 'SELECT `goods_id`, `goods_name`, g.`brand_id`, `shop_price`,'
                 . ' `goods_thumb`, b.`brand_name`, g.goods_same_id, g.goods_short_name'
                 . ' FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g'
                 . ' LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b'
                 . ' ON b.brand_id = g.brand_id'
                 . ' WHERE g.`brand_id` in (' . $brand_id . ') AND g.`is_on_sale` = 1'
                 . ' AND g.is_alone_sale = 1 AND g.`is_delete` = 0'
                 . ' ORDER BY b.`sort_order`, g.goods_name';
            FB::log($sql);
            $goods = $GLOBALS['db']->getAll($sql);

            foreach ($goods as $key => $item) {
                $item['url']              = build_uri('goods', array('gid'=>$item['goods_id']), $item['goods_name']);
                $item['shop_price'] = price_format($item['shop_price']);
                $num_index = intval($item['goods_short_name']);
                $item['single_price'] = price_format(str_replace('¥ ', '', $item['shop_price']) / $num_index);
                $item['unit'] = preg_replace('/\d*/', '', $item['goods_short_name']);
                $goods_list[$item['brand_id']][$item['goods_same_id']][$num_index] = $item;
            }
            FB::info($goods_list[18]);
            $smarty->assign('goods_list',       $goods_list);
        }
    }
    
    /*一听试购*/
    if($topic_id == 10){
        $where = array();
        $where['is_tryonebuy'] = $where['is_on_sale'] = 1;
        $goods = $GLOBALS['cidb']->select("goods_id,goods_name,shop_price,promote_price,promote_start_date,promote_end_date,brand_id,goods_thumb")->where($where)->order_by("sort_order")->get("goods")->result_array();
        $tmp_goods = array();
        if(!empty($goods)){
            foreach($goods as $gk=>$gv){
                /* 修正促销价格 */
                if ($gv['promote_price'] > 0)
                {
                    $promote_price = bargain_price($gv['promote_price'], $gv['promote_start_date'], $gv['promote_end_date']);
                }
                else
                {
                    $promote_price = 0;
                }
                $gv['promote_price_org'] =  $promote_price;
                $gv['promote_price'] =  price_format($promote_price);
                if($promote_price > 0){
                    $gv['shop_price'] = $promote_price;
                }
                $gv['shop_price'] = price_format($gv['shop_price'],false);
                $tmp_goods[] = $gv;
            }
        }
        $smarty->assign('goods_list',  $tmp_goods);
    }

    /* 模板赋值 */
    assign_template();
    $position = assign_ur_here();
    $smarty->assign('page_title',       $topic['title'].'|'.$position['title']);       // 页面标题
    $smarty->assign('ur_here',          $position['ur_here'] . '> ' . $topic['title']);     // 当前位置
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('sort_goods_arr',   $sort_goods_arr);          // 商品列表
    $smarty->assign('topic',            $topic);                   // 专题信息
    $smarty->assign('keywords',         $topic['keywords']);       // 专题信息
    $smarty->assign('description',      $topic['description']);    // 专题信息
    $smarty->assign('title_pic',        $topic['title_pic']);      // 分类标题图片地址
    $smarty->assign('base_style',       '#' . $topic['base_style']);     // 基本风格样式颜色
    $smarty->assign('helps',           get_shop_help());       // 网店帮助

    FB::Warn($topic['template']);
    $template_file = empty($topic['template']) ? 'topic.dwt' : $topic['template'];
}
/* 显示模板 */
$smarty->display($templates, $cache_id);

?>
