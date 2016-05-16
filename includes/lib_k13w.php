<?php
if (!defined('IN_ECS'))die('Hacking attempt');


function kl3w_get_cat_info($cat_id){
    return $GLOBALS['db']->getRow('SELECT cat_name, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");
}

function kl3w_getshaidan($num=1,$islove=false){
	$msg = array();
	$orderby = $islove?'lovenum':'renum';
	 $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('shaidan');
	 $sql .= " WHERE parent_id = 0 AND msg_status = 1 ORDER BY {$orderby} DESC,msg_id DESC LIMIT ".$num;
	$shaidan = $GLOBALS['db']->getAll($sql);
	foreach ($shaidan AS $key => $rows){
        $msg[$rows['msg_id']]['msg_key']     = $rows['msg_id'] + 198400;
        $msg[$rows['msg_id']]['msg_content'] = nl2br(htmlspecialchars($rows['msg_content']));
        $msg[$rows['msg_id']]['msg_time']    = local_date($GLOBALS['_CFG']['time_format'], $rows['msg_time']);
        $msg[$rows['msg_id']]['msg_type']    = $GLOBALS['_LANG']['shaidantype'][$rows['msg_type']];
        $msg[$rows['msg_id']]['msg_title']   = nl2br(htmlspecialchars($rows['msg_title']));
        $msg[$rows['msg_id']]['shaidan_img'] = $rows['shaidan_img'];
        $msg[$rows['msg_id']]['goods_id'] = $rows['goods_id'];
        $msg[$rows['msg_id']]['msg_area'] = $rows['msg_area']?true:false;
	}
	return $shaidan;
}

/* 品牌筛选 */
function kl3w_get_brands($cat_id){
	$children = get_children($cat_id);
	$cat = kl3w_get_cat_info($cat_id);   // 获得分类的相关信息
    $sql = "SELECT b.brand_id, b.brand_name, b.brand_logo, COUNT(*) AS goods_num ".
            "FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ".
                $GLOBALS['ecs']->table('goods') . " AS g LEFT JOIN ". $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
            "WHERE g.brand_id = b.brand_id AND ($children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), array_keys(cat_list($cat_id, 0, false))))) . ") AND b.is_show = 1 " .
            " AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
            "GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.sort_order, b.brand_id ASC";
    $brands = $GLOBALS['db']->getAll($sql); $brandsarr = array();
	$price_min = $price_max = $filter_attr_str = '0';
    foreach ($brands AS $key => $val)
    {
        $temp_key = $key + 1;
        $brandsarr[$temp_key]['brand_id'] = $val['brand_id'];
        $brandsarr[$temp_key]['brand_name'] = $val['brand_name'];
        $brandsarr[$temp_key]['brand_logo'] = $val['brand_logo'];
        $brandsarr[$temp_key]['goods_num'] = $val['goods_num'];
        $brandsarr[$temp_key]['url'] = build_uri('category', array('cid' => $cat_id, 'bid' => $val['brand_id'], 'price_min'=>$price_min, 'price_max'=> $price_max, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

        /* 判断品牌是否被选中 */
        if ($brand == $brands[$key]['brand_id'])
        {
            $brandsarr[$temp_key]['selected'] = 1;
        }
        else
        {
            $brandsarr[$temp_key]['selected'] = 0;
        }
    }
	return $brandsarr;
}

/**
 * 获得某个分类下
 *
 * @access  public
 * @param   int     $cat
 * @return  array
 */
function kl3w_get_brands_goods($brand_id, $cate=0, $size=10, $sort='goods_id', $order='')
{
    $cate_where = ($cate > 0) ? 'AND ' . get_children($cate) : '';

    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand_id' $cate_where".
            "ORDER BY $sort $order LIMIT ". $size;

    //$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    $res = $GLOBALS['db']->getAll($sql);
	if(!$res)return array();

    $arr = array();
    //while ($row = $GLOBALS['db']->fetchRow($res))
	foreach ($res AS $key => $row)
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        $arr[$row['goods_id']]['brand_id']   = $brand_id;
        $arr[$row['goods_id']]['goods_id']   = $row['goods_id'];
		$arr[$row['goods_id']]['name']       = $row['goods_name'];
        if($GLOBALS['display'] == 'grid')
        {
            $arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
		//$arr[$row['goods_id']]['name']       = $cate_where;
		
        $arr[$row['goods_id']]['market_price']  = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
        $arr[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$row['goods_id']]['goods_brief']   = $row['goods_brief'];
        $arr[$row['goods_id']]['goods_thumb']   = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    }

    return $arr;
}

/**
 * 通过类型与传入的ID获取广告内容
 *
 * @param string $type
 * @param int $id
 * @return string
 */					
function get_adv($type, $id, $is_lazyload = true) {
	$sql = "select ap.ad_width,ap.ad_height,ad.ad_name,ad.ad_code,ad.ad_link from ".$GLOBALS['ecs']->table('ad_position')." as ap left join ".$GLOBALS['ecs']->table('ad')." as ad on ad.position_id = ap.position_id where ad.ad_name='".$type."_".$id."' and ad.media_type=0 and UNIX_TIMESTAMP()>ad.start_time and UNIX_TIMESTAMP()<ad.end_time and ad.enabled=1";
	$res = $GLOBALS['db']->getRow($sql);
	if($res) {
		$src = substr($res['ad_code'],0,7)=='http://'?$res['ad_code']:'data/afficheimg/'.$res['ad_code'];
        if ($is_lazyload == true) {
		    $html = "<a href='".$res['ad_link']."' target='_blank'><img class='lazy' data-original='".$src."' src='/images/loading.gif' width='".$res['ad_width']."' height='".$res['ad_height']."' /></a>";
        } else {
		    $html = "<a href='".$res['ad_link']."' target='_blank'><img src='".$src."' width='".$res['ad_width']."' height='".$res['ad_height']."' /></a>";
        }
        return $html;
	}else{
		//return '请添加名称为['.$type."_".$id.']的广告&nbsp;';
            return;
	}  
}

/**
 * 给所有小类都加上了 @ 前缀
 * 这真是超级奇葩的想法，为什么不直接用数组，要加什么前缀，尼玛的
 */
function kl3w_get_cat_id($arr,$ext=''){
    $cat = "";
    foreach($arr as $val){
            $cat .= $ext.$val['id'].'|'.$val['name'].',';
            if($val['cat_id'])$cat .= kl3w_get_cat_id($val['cat_id'],'@');#取下级类
    }
    return $cat;
}

function get_floors_content($categories_tree){
    $outarr = array();
    $i = 0;
    /*
    $toparr = array();
    $sql = "SELECT cat_id FROM ".$GLOBALS['ecs']->table('cat_recommend')
         . " WHERE recommend_type = '1'";
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res as $val){
		$toparr[] = $val['cat_id'];
    }*/
    foreach($categories_tree as $key => $val){
	$cat = '';
	$outarr[$key]['k']     = $i;
        $i++;
        $outarr[$key]['i']     = $i;
	$outarr[$key]['name']  = $val['name'];
	$outarr[$key]['catid'] = $val['id'];
	$outarr[$key]['cat_url'] = build_uri('category', array('cid' => $val['id'], $val['name']));
	if($val['cat_id']){
            $cat = kl3w_get_cat_id($val['cat_id'],'');
            $cat = rtrim($cat, ',');
            $cat_arr = explode(',',$cat);
            $cat_list = array();
            $cat_all = '';
            $j = 0;
            foreach($cat_arr as $k => $v){
                $v_arr = explode('|',$v);
                $v_arr = explode('|',$v);
                $cat_id   = $v_arr[0];
                $cat_name = $v_arr[1];
                // 如果 $cat_id 的第1位不是 @，那就是大类
                if(substr($cat_id, 0, 1) != '@') {
                    if($j < FLOOR_TAB_QTY) {
                        $goods = assign_cat_goods($cat_id, FLOOR_GOODS_QTY, 'wap');
                        $cat_list[] = array(
                            'id'         => $cat_id,
                            'name'       => $cat_name,
                            'url'        => build_uri('category', array('cid' => $cat_id, $cat_name)),
                            'left_ad'    => get_adv('home_fl' . $i . '_goods', $j),
                            'goods_list' => $goods['goods']
                        );
                    }
                    $j++;
                } else {
                    $cat_id = str_replace('@','',$cat_id);
                }

                // 到了这一处，$cat_id 已经有可能是上级的，也可能是下级的，下一
                // 一步是验证这个 cat_id 是否是热门分类（可以再弱一点吗？）
                // 将在后台分类推荐为热门的显示为下拉分类
                $sql = "SELECT cat_id FROM " . $GLOBALS['ecs']->table("cat_recommend")
                     . " WHERE recommend_type = 3 and cat_id = " . $cat_id;
                $cat_reid = $GLOBALS['db']->getOne($sql);
                
                if (!empty($cat_reid)) {
                    $cat_all .= '<li>·<a target="_blank" name="dac_index_lc00002" href="category.php?id=' . $cat_id
                             . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
                }
                                                
            }
            //返回一个长度$length的数组，原不足数组补值为$value，长度足够返回原数组。
            //$cat_list = array_pad($cat_list, 6, ' ');
            $outarr[$key]['cat_list'] = $cat_list;
            $outarr[$key]['cat_all']  = $cat_all;           
	}
    }
    //_P($outarr,1);
    return $outarr;
}

/**
* 用户留言       
* 
*/
function kl3w_get_user_messages($num = 10){
	$sql = 'SELECT msg_title, msg_content, msg_time  FROM '. $GLOBALS['ecs']->table('feedback') .
	//' WHERE parent_id = 0 AND `msg_area`=1 AND `msg_status` = 1'.
	' WHERE parent_id = 0 AND `msg_status` = 1'.
	' ORDER BY msg_time DESC';
	if ($num > 0) $sql .= ' LIMIT ' . $num;
	
	$res = $GLOBALS['db']->getAll($sql);
	
	$comments = array();
	foreach ($res AS $idx => $row)
	{
	$comments[$idx]['title']       = sub_str(str_replace('\r\n', '<br />', htmlspecialchars($row['msg_title'])), 30);  
	$comments[$idx]['url']        = "message.php";      
	}
	
	return $comments;
}

/**
 * 获得最新的文章列表。
 *
 * @access  private
 * @return  array
 */
function kl3w_get_new_articles()
{
    $sql = 'SELECT article_id, title, file_url, open_type FROM ' .
           $GLOBALS['ecs']->table('article') .
            ' WHERE cat_id = 12' .
            ' ORDER BY article_type DESC, add_time DESC LIMIT ' . $GLOBALS['_CFG']['article_number'];
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $arr[$idx]['id']          = $row['article_id'];
        $arr[$idx]['title']       = $row['title'];
        $arr[$idx]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ?
                                        sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
        $arr[$idx]['url']         = $row['open_type'] != 1 ?
                                        build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
    }

    return $arr;
}

function kl3w_get_homead(){
    $sql = 'SELECT ad_type, content, url, ad_minimg, ad_url1, ad_url2, ad_url3, ad_color FROM ' . $GLOBALS['ecs']->table("ad_custom") . ' WHERE ad_status = 1 ORDER BY ad_sort,add_time ASC';
    $ad = $GLOBALS['db']->getAll($sql);
    return $ad;
}
?>
