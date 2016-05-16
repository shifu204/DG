<?php
define('IN_ECS', true);

require('../api/init.php');
require_once('../includes/lib_goods.php');
require_once('../includes/cls_simplexml_ext.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : "default";

if($act == 'make_full_index'){
    //feed数据格式版本号
    $version = '1.0';
    //修改时间
    $modified = local_date("Y-m-d H:i:s");
    //商家名称
    $seller_id = $GLOBALS['_CFG']['shop_name'];
    //商品总数量
    $total = total_goods();
    //feed xml文件路径
    $dir = "http://feed.deebei.net/weigou/item";
    //根据商品总数量计算需要生成的商品文件数量
    $files_num = ceil($total / 1000);
    
    //开始生成XML文件
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><root />');
    $root = $xml;
    $root->addChild("version", $version);
    $root->addChild("modified", $modified);
    $root->addChild("seller_id", $seller_id);
    $root->addChild("total", $total);
    $root->addChild("dir", $dir);
    $item_ids = $root->addChild("item_ids");
    if($files_num > 0){
        $i = 1;
        while($i <= $files_num){           
            $outer_id = $item_ids->addChild("outer_id", $i);
            $outer_id->addAttribute("action", "upload");
            $i++;
        }
    }
    //写入文件
    if(!is_dir('./weigou')){
        mk_folder('./weigou');
    }
    file_put_contents('./weigou/fullindex.xml', $xml->asXML());
}
else if($act == 'make_items_index'){   
    $seller_id = $GLOBALS['_CFG']['shop_name'];
    //每个商品文件记录的商品数
    $item_size = 1000;
    $i = 1;
    while(($items = get_item_goods(($i-1)*$item_size, $item_size)) !== false){
        $xml = new ExSimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><items />');
        foreach($items as $goods){
            $item = $xml->addChild('item');
            //商家名称
            $item->addChild('seller_id', $seller_id);
            //商家商品索引号
            $item->addChild('outer_id', $i);
            //商品标题
            $item->addChild('title', $goods['goods_name']);
            //货号
            $item->addChild('product_id', $goods['goods_id']);
            //<!--0 代表缺货，1 代表有货--> 
            $item->addChild('available', 1);
            // <!--商品价格，格式：5.00；单位：元；精确到：分；取值范围：0-1 千万--> 
            $item->addChild('price', $goods['shop_price']);
            //判断是否在促销期内
            $promote_price = bargain_price($goods['promote_price'], $goods['promote_start_date'], $goods['promote_end_date']);
            if($promote_price > 0){
                $discount = $item->addChild('discount');
                //<!--折后价, 商品打折后的价格--> 
                $discount->addChild('dprice', $promote_price);
                //<!--打折信息描述--> 
                $discount->addChild('ddesc', '优惠降价，机不可失，失不再来');
            }
            //商品简单描述 
            $item->addChild('desc', $goods['goods_name']);
            //自营商品或平台商家商品 。0 代表自营商品，1 代表平台商家商品
            $item->addChild('selforopen', 0);
            //商品品牌 
            $item->addChild('brand', $goods['brand_name']);
            //商品标签 
            $item->addChild('tags', '');
            //商品主图地址 
            $item->addChild('image', $goods['original_img']);
            //商品更多图片
            $detail_images = $item->addChild('detail_images');
            if(!empty($goods['detail_images'])){
                foreach($goods['detail_images'] as $picture){
                    $detail_images->addChild('img', $picture);
                }
            }
            // <!--商品链接绝对地址--> 
            $item->addChildCData('href', $goods['href']);
            $item->addChildCData('wireless_link', $goods['wireless_link']);
            $item->addChildCData('hd_wireless_link', $goods['hd_wireless_link']);
            // <!--销量数字，精确到整数--> 
            $item->addChild('sale_volume', $goods['sale_volume']);
            // <!--评论数量，该商品的总评论数--> 
            $item->addChild('comment_count', $goods['comment_count']);
            //商品评分
            $item->addChild('saled_score', '5.0');
            // <!--面包屑。格式：生鲜食品/新鲜水果，用/符号隔开。即：此商品通过你网站访问的最深路径。此事例指此商品是生鲜食品>新鲜水果下的一个产品。除首页网站的“你的位置”中相关信息--> 
            $item->addChild('bread_crumb', $goods['category_root']);  //暂时为空
        }
        if(!is_dir('./weigou/item')){
            mk_folder('./weigou/item');
        }
        $xml->asXML('./weigou/item'.'/'.$i.'.xml');
        $i++;
    }
}

/**
 * 获取上架的实物商品总数量
 * @return int
 */
function total_goods(){
    $sql = "SELECT COUNT(*) " . 
           " FROM " . $GLOBALS['ecs']->table('goods') . 
           " WHERE is_real = 1 AND is_on_sale = 1 and is_delete = 0 " . 
           " ORDER BY sort_order ASC ";
    return $GLOBALS['db']->getOne($sql);
}

function mk_folder($folder){
    if(!is_readable($folder)){
        mk_folder( dirname($folder) );
        if(!is_file($folder)){
            mkdir($folder,0777);
        }
    }
}

/**
 * 获取实物商品
 * @return array
 */
function get_item_goods($start = 0, $size = 1000, $host = 'http://www.deebei.net/', $wireless_host = 'http://m.deebei.net/', $hd_wireless_host = 'http://m.deebei.net/'){
    $sql = "SELECT g.goods_id, g.cat_id, g.goods_sn, g.goods_name, g.shop_price, g.promote_price,g.promote_start_date, g.promote_end_date, g.original_img, g.sort_order, " .
           " c.comment_count, og.sale_volume, b.brand_name " . 
           " FROM " . $GLOBALS['ecs']->table('goods') . " AS g " . 
           " LEFT JOIN " . $GLOBALS['ecs']->table('brand') . " AS b ON b.brand_id = g.brand_id " . 
           //评论数
           " LEFT JOIN (SELECT COUNT(comment_id) as comment_count ,id_value FROM " . $GLOBALS['ecs']->table('comment') ." GROUP BY id_value ) AS c ON c.id_value = g.goods_id " .  
           //销量
           " LEFT JOIN (SELECT SUM(goods_number) as sale_volume, goods_id FROM " . $GLOBALS['ecs']->table('order_goods') . " GROUP BY goods_id) AS og ON og.goods_id = g.goods_id" . 
           " WHERE g.is_real = 1 AND g.is_on_sale = 1 and g.is_delete = 0 " . 
           " GROUP BY g.goods_id " . 
           " ORDER BY g.sort_order ASC " . 
           " LIMIT $start, $size";
    $res = $GLOBALS['db']->query($sql);
    $result = array();
    while($row = $GLOBALS['db']->fetchRow($res)){
        //修正图片地址
        $row['original_img'] = $host.$row['original_img'];
        //商品相册
        $pictures = get_goods_gallery($row['goods_id']); 
        if(!empty($pictures)){
            foreach($pictures as $picture){
                $row['detail_images'][] = $host.$picture['img_url'];
            }
        }
        //商品连接地址
        $row['href'] =$host . 'goods.php?id=' . $row['goods_id'] . '&wd=baiduweigou';
        //无线端商品链接 
        $row['wireless_link'] = $row['hd_wireless_link'] = $wireless_host . '?c=goods&a=index&id=' . $row['goods_id'] . '&wd=baiduweigou';
        //获取商品分类路径
        $row['category_root'] = get_category_root($row['cat_id']);
        //修正商品品牌
        if(empty($row['brand'])){
            $row['brand'] = '德贝';
        }
        $result[] = $row;
    }
    if(!empty($result)){
        return $result;
    }
    return false;
}

/**
 * 获取商品的分类树
 * @staticvar string $cat_str
 * @param int $cat_id
 * @return string
 */
function get_category_root($cat_id = 0, &$cat_str = ''){
    if(!empty($cat_id)){       
        $sql = 'SELECT cat_name, parent_id FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = ". $cat_id;
        $category = $GLOBALS['db']->getRow($sql);
        if(!empty($category)){
            if(!empty($cat_str)){
                $cat_str = $category['cat_name'].'/'. $cat_str;
            } else {
                $cat_str = $category['cat_name'];
            } 
            if($category['parent_id'] != 0){               
                get_category_root($category['parent_id'], $cat_str);
            }
        }
    }
    return $cat_str;
}