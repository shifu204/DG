<?php

/**
 * 路由解析类
 *
 * @author ming
 */
if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
class cls_router {
    //public $domain = 'test.com';
    public $domain = 'deebei.net';
    public $route_list = array();
    public $sub_domain;
    public $url = array();
    public $host;
    public $uri;
    function __construct() {
        $this->url_info();
        $this->sub_domain = $GLOBALS['sub_domain'];
    }
    
    /*解析url地址，获取url参数*/
    public function url_info(){
        $this->host = $_SERVER['HTTP_HOST'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] = 'on') ? "https":"http";
        if(!empty($_SERVER['QUERY_STRING'])){
            $queryParts = explode('&', $_SERVER['QUERY_STRING']); 
            foreach ($queryParts as $param) 
            { 
                $item = explode('=', $param); 
                $params[$item[0]] = $item[1]; 
            } 
        }
        $this->url['url'] = $http."://".$this->host.$this->uri;
        $this->url['host'] = $this->host;
        $this->url['uri'] = $this->uri;
        $this->url['params'] = $params;
    }
    
    /*路由跳转*/
    public function route(){
        if(!empty($this->sub_domain)){
            //获取二级域名
            $sub_domain = $this->get_sub_domain();
            if(array_key_exists($sub_domain,$this->sub_domain) && (empty($this->uri) || $this->uri == '/')){
                $this->display_topic($this->sub_domain[$sub_domain]);
            }
        }
    }
    
    private function get_sub_domain(){
        $sub_domain = rtrim(str_replace($this->domain, '', $this->host),'.');
        return $sub_domain;
    }
    
    function display_topic($topic_id = 0){
        if(empty($topic_id)){
            header("HTTP/1.0 404 Not Found");
        } else {
            $sql = "SELECT template FROM " . $GLOBALS['ecs']->table('topic') .
                    "WHERE topic_id = '$topic_id' and  " . gmtime() . " >= start_time and " . gmtime() . "<= end_time";
            $topic = $GLOBALS['db']->getRow($sql);
            if(empty($topic)){
                header("HTTP/1.0 404 Not Found");
                exit;
            }
            $templates = empty($topic['template']) ? 'topic.dwt' : $topic['template'];
            $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('topic') . " WHERE topic_id = '$topic_id'";
            $topic = $GLOBALS['db']->getRow($sql);
            
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
            $GLOBALS['smarty']->assign('topic_class', $topic_class);
            $GLOBALS['smarty']->assign('goods_list', $goods_list);
            /* 模板赋值 */
            assign_template();
            $position = assign_ur_here();
            $GLOBALS['smarty']->assign('page_title',       $topic['title'].'|'.$position['title']);       // 页面标题
            $GLOBALS['smarty']->assign('ur_here',          $position['ur_here'] . '> ' . $topic['title']);     // 当前位置
            $GLOBALS['smarty']->assign('show_marketprice', $_CFG['show_marketprice']);
            $GLOBALS['smarty']->assign('sort_goods_arr',   $sort_goods_arr);          // 商品列表
            $GLOBALS['smarty']->assign('topic',            $topic);                   // 专题信息
            $GLOBALS['smarty']->assign('keywords',         $topic['keywords']);       // 专题信息
            $GLOBALS['smarty']->assign('description',      $topic['description']);    // 专题信息
            $GLOBALS['smarty']->assign('title_pic',        $topic['title_pic']);      // 分类标题图片地址
            $GLOBALS['smarty']->assign('base_style',       '#' . $topic['base_style']);     // 基本风格样式颜色
            $GLOBALS['smarty']->assign('helps',           get_shop_help());       // 网店帮助
            $GLOBALS['smarty']->display($templates);
        }
        exit;
    }
}
