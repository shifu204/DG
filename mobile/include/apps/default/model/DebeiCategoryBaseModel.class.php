<?php

/**
 * ECTouch Open Source Project
 * ============================================================================
 * Copyright (c) 2012-2015 http://www.deebei.net All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：DebeiBaseModel.class.php
 * ----------------------------------------------------------------------------
 * 功能描述：德贝商城 分类基础数据模型
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */

/* 访问控制 */
defined('IN_ECTOUCH') or die('Deny Access');

class DebeiCategoryBaseModel extends CategoryBaseModel {
    /**
     *  获取商品选择分类
     * @access  public
     * @return  array
     */
    function get_filter_category($cat_id = 0){
        $cat = '';
        if(!empty($cat_id)){
            $cat = " AND cat_id = " . $cat_id;
        }
        $sql = " SELECT cat_id, cat_tag, cat_name as name " .
               "FROM " .$this->pre."category ".
               "WHERE is_show = 1 AND parent_id = 0 " .$cat.
               " ORDER BY sort_order ASC";
        $res = $this->query($sql);
        if(!empty($res)){
            $temp_res = array();
            foreach($res as $row){
                $tag = $row['cat_tag']."_filter";
                $sql = "SELECT c.cat_id, c.cat_tag, c.cat_name as name, t.show_nav, t.m_tag  FROM ".$this->pre."category AS c".
                        " LEFT JOIN ".$this->pre."touch_category as t ON t.cat_id = c.cat_id "
                        . " WHERE c.is_show = 0 AND c.cat_tag LIKE '$tag%'";
                $filters = $this->query($sql);
                $temp_res[$row['cat_id']] = $row;
                $temp_res[$row['cat_id']]['id'] = $row['cat_id'];
                if(!empty($filters)){
                    $temp_filters = array();
                    foreach($filters as $filter){
                        $temp_filters[$filter['cat_id']] = $filter;
                        $temp_filters[$filter['cat_id']]['id'] = $filter['cat_id'];
                        $temp_filters[$filter['cat_id']]['cat_id'] = $this->get_child_tree($filter['cat_id'],0);     
                        
                    }
                }
                $temp_res[$row['cat_id']]['cat_id'] = $temp_filters;
                if($row['cat_tag'] == 'powder'){
                    $temp_res[$row['cat_id']]['brands'] = $this->get_catetory_brands($row['cat_id']);
                }
            }
            return $temp_res;
        }else {
            return array();
        }  
    }
    
    function get_child_tree($tree_id = 0,$is_show = 1) {
        $three_arr = array();
        $sql = 'SELECT count(*) FROM ' . $this->pre . "category WHERE parent_id = '$tree_id' AND is_show = ".$is_show;
        if ($this->row($sql) || $tree_id == 0) {
            $child_sql = 'SELECT c.cat_id, c.cat_name, c.parent_id, c.is_show, t.cat_image, t.m_tag, t.show_nav ' .
                    'FROM ' . $this->pre . 'category as c ' .
                    'left join ' . $this->pre . 'touch_category as t on t.cat_id = c.cat_id ' .
                    "WHERE c.parent_id = '$tree_id' AND c.is_show = $is_show ORDER BY c.sort_order ASC, c.cat_id ASC";
            $res = $this->query($child_sql);
            foreach ($res AS $row) {
                if ($row['is_show'])
                $three_arr[$row['cat_id']]['id'] = $row['cat_id'];
                $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
                $three_arr[$row['cat_id']]['cat_image'] = empty($row['cat_image']) ? __PUBLIC__ . '/' . C('no_picture') : $row['cat_image'];
                $three_arr[$row['cat_id']]['url'] = build_uri('category/index', array('id' => $row['cat_id'],'sort'=>'sort_order'));
                $three_arr[$row['cat_id']]['show_nav'] = $row['show_nav'];               
                if (isset($row['cat_id']) != NULL) {
                    $three_arr[$row['cat_id']]['cat_id'] = $this->get_child_tree($row['cat_id']);
                }
            }
        }
        return $three_arr;
    }
    
    //获取分类下的所有品牌
    function get_catetory_brands($cat_id = 0){
        $sql = '';
        $where = ' WHERE b.is_show = 1 AND g.is_on_sale = 1';
        $select = "SELECT b.brand_id, b.brand_name, b.brand_logo, b.brand_desc, COUNT(*) goods_num ";
        if($cat_id != 0){
            $children = array_keys(cat_list($cat_id,0,false));
            $children_str = '';
            if(!empty($children)){
                $children_str = '('.implode(',', $children).')';
                $where .= " AND g.cat_id IN ".$children_str;
            }           
        }
        $join = " LEFT JOIN {$this->pre}goods AS g ON g.brand_id = b.brand_id";
        $sql = $select ."FROM {$this->pre}brand AS b".$join.$where." GROUP BY b.brand_id";
        $brands = $this->query($sql);
        foreach($brands as $key => $row){
            $brands[$key]['url'] = build_uri('category/index', array('id' => $cat_id,'brand'=>$row['brand_id'],'sort'=>'sort_order'));
        }
        return $brands;
    }
}