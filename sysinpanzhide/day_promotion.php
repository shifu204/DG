<?php

/**
 * ECSHOP 管理中心天天特价活动管理
 * $Author: ming
 * $Id: day_promotion.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_goods.php');
//$exc = new exchange($ecs->table('favourable_activity'), $db, 'act_id', 'act_name');

if($_REQUEST['act'] == 'list') {
    $smarty->assign('full_page',1);  
    //页脚查询时间等信息
    assign_query_info();
    $smarty->display('day_promotion/day_promotion_list.htm');
}

else if($_REQUEST['act'] == 'add'){
    $smarty->assign('full_page',1);
    $smarty->display('day_promotion/day_promotion_add.htm');
}

else if($_REQUEST['act'] == 'add_do'){
    $promotion['act_name'] = empty($_REQUEST['act_name'])?'':trim($_REQUEST['act_name']);
    $promotion['act_desc'] = empty($_REQUEST['act_desc'])?'':trim($_REQUEST['act_desc']);
    $promotion['start_time'] = $_REQUEST['start_time'];
    $promotion['end_time'] = $_REQUEST['end_time'];
    $promotion['loop'] = $_REQUEST['loop'];
    $promotion['is_finished'] = $_REQUEST['is_finished'];
    $promotion['sort_order'] = $_REQUEST['sort_order'];
    $promotion['act_type'] = 9;
    $promotion['ext_info'] = '';
    $promotion['goods_id'] = 0;
    $promotion['goods_name'] = '';
    $promotion['last_time'] = 0;
    $promotion['product_id'] = 0;
    if(!empty($promotion['act_name'])) {
        //插入活动
        $result = $GLOBALS['cidb']->insert('goods_activity',$promotion);
        if($result) {
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }
    echo 0;
    exit;
}

else if($_REQUEST['act'] == 'delete_do'){
    if(!empty($_REQUEST['deleted'])){
        foreach($_REQUEST['deleted'] as $dk=>$dv){
            $act_id = $dv['act_id'];
            //删除goods_activity表数据
            $GLOBALS['cidb']->where('act_id = '.$act_id.' AND act_type=9')->limit(1)->delete("goods_activity");
            //删除day_promote数据
            $GLOBALS['cidb']->where('act_id = '.$act_id)->delete("day_promote");
        }
    }
    echo json_encode(1);
}

else if($_REQUEST['act'] == "get_promotion"){
    $page = is_int($_REQUEST['page'])?$_REQUEST['page']:1;
    $rows = is_int($_REQUEST['rows'])?$_REQUEST['rows']:20;
    $total = $GLOBALS['cidb']->where(array('act_type'=>9))->get('goods_activity')->num_rows();
    $rows = $GLOBALS['cidb']->where(array('act_type'=>9))->limit($rows,($page-1)*rows)->get('goods_activity')->result_array();
    foreach($rows as $rk=>$rv){
        if(!empty($rv['last_time'])) {
            $rows[$rk]['last_time'] = date('Y-m-d H:i',$rv['last_time']);
        } else {
            $rows[$rk]['last_time'] = '从未活动';
        }
    }
    echo json_encode(array('total'=>$total,'rows'=>$rows));
}


else if($_REQUEST['act'] == "promotion_edit"){
    $act_id = $_REQUEST['act_id']?(int)$_REQUEST['act_id']:'';
    if(empty($act_id)) {
        echo "找不到相关活动";
        exit;
    } else {
        $promotion = $GLOBALS['cidb']->where('act_id = '.$act_id)->get('goods_activity')->row_array();
        if(!empty($promotion['last_time'])){
            $promotion['last_time'] = date('Y-m-d H:i',$promotion['last_time']);
        } else {
            $promotion['last_time'] = '从未活动';
        }
        $smarty->assign('promotion',$promotion);
        $smarty->display('day_promotion/day_promotion_edit.htm');
    }
    
}

else if($_REQUEST['act'] == "select_goods"){
    $smarty->display('day_promotion/day_promotion_select_goods.htm');
}

else if($_REQUEST['act'] == "get_goods"){
   // require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
    $page = isset($_REQUEST['page'])?(int)$_REQUEST['page']:1;
    $pagesize = isset($_REQUEST['rows'])?(int)$_REQUEST['rows']:20;
    $offset = ($page-1) * $pagesize;
    $keyword = trim($_REQUEST['keyword']);
    $goods_con['is_real'] = 1;
    $goods_con['is_delete'] = 0;
    $goods_con['is_on_sale'] = 1;
    $total = $GLOBALS['cidb']->select("goods_id")->where($goods_con)->like('goods_name',$keyword)->get('goods')->num_rows();
    $goods = $GLOBALS['cidb']->select("goods_id,cat_id,goods_sn,goods_name,market_price,shop_price,promote_price,goods_brief,goods_thumb,goods_number")->where($goods_con)->like('goods_name',$keyword)->limit($pagesize,$offset)->get('goods')->result_array();
    $result['total'] = $total;
    $result['rows'] = $goods;
    echo json_encode($result);
}

else if($_REQUEST['act'] == "edit_do"){
    if(isset($_REQUEST['act_id']) && !empty($_REQUEST['act_id'])) {
        $act_id = trim($_REQUEST['act_id']);
        $act_data['act_name'] = trim($_REQUEST['act_name']);
        $act_data['act_desc'] = trim($_REQUEST['act_desc']);
        $act_data['start_time'] = $_REQUEST['start_time'];
        $act_data['end_time'] = $_REQUEST['end_time'];
        $act_data['loop'] = $_REQUEST['loop'];
        $act_data['is_finished'] = $_REQUEST['is_finished'];
        $act_data['sort_order'] = $_REQUEST['sort_order'];
        $GLOBALS['cidb']->where("act_id = ".$act_id)->limit(1)->update('goods_activity',$act_data);
        if(!empty($_REQUEST['inserted'])){
            $inserted = $_REQUEST['inserted'];
            foreach($inserted as $ik=>$iv){
                $insert_data['act_id'] = $act_id;
                $insert_data['goods_id'] = $iv['goods_id'];
                $insert_data['goods_sn'] = $iv['goods_sn'];
                $insert_data['shop_price'] = $iv['shop_price'];
                $insert_data['promote_price'] = $iv['promote_price'];
                //$insert_data['promote_img'] = $iv['promote_img'];
                $GLOBALS['cidb']->insert('day_promote',$insert_data);
            }
        }
        if(!empty($_REQUEST['updated'])){
            $updated = $_REQUEST['updated'];
            foreach($updated as $uk=>$uv){
                $dp_id = $uv['dp_id'];
                $up_data['act_id'] = $uv['act_id'];
                $up_data['goods_id'] = $uv['goods_id'];
                $up_data['goods_sn'] = $uv['goods_sn'];
                $up_data['shop_price'] = $uv['shop_price'];
                $up_data['promote_price'] = $uv['promote_price'];
                //$up_data['promote_img'] = $uv['promote_img'];
                $GLOBALS['cidb']->where('dp_id = '.$dp_id)->limit(1)->update('day_promote',$up_data);
                //检测这个活动是否在进行中，如果在进行中，则需要更新商品的促销价格及促销日期
                $ug_data['promote_price'] = $up_data['promote_price'];
                if( $act_data['is_finished'] == 99){
                    $today = strtotime("today");
                    $start_hour = intval($act_data['start_time']);
                    $end_hour = intval($act_data['end_time']);
                    $ug_data['promote_start_date'] = $today +  $start_hour * 3600;
                    if($start_hour == 0){
                        $ug_data['promote_start_date'] = $today;
                    } else if($end_hour == 0){
                        $ug_data['promote_end_date'] = $today + 24*3600;                  
                    } else if($end_hour > $start_hour){                                  
                        $ug_data['promote_end_date'] = $today + $end_hour * 3600;
                    } else {
                        $ug_data['promote_end_date'] = 0;
                    }
                } else {
                    $ug_data['promote_start_date'] = 0;
                    $ug_data['promote_end_date'] = 0;
                }
                $GLOBALS['cidb']->where('goods_id = '.$up_data['goods_id'])->update('goods',$ug_data);
            }
        }
        if(!empty($_REQUEST['deleted'])){
            $deleted = $_REQUEST['deleted'];
            foreach($deleted as $dk=>$dv){
                $dp_id = $dv['dp_id'];
                $GLOBALS['cidb']->where('dp_id = '.$dp_id)->limit(1)->delete('day_promote');
            }
        }
        echo json_encode(1);
    }
}

else if($_REQUEST['act'] == "get_promotion_goods"){
    $act_id = $_REQUEST['act_id'];
    if(!empty($act_id)) {
        $goods = $GLOBALS['cidb']->select("day_promote.*,goods.goods_name,goods.goods_number")->where('act_id = '.$act_id)->join('goods','day_promote.goods_id = goods.goods_id')->get('day_promote')->result_array();
        echo json_encode($goods);
    }
}

else if($_REQUEST['act'] == "upload_files"){
    $result['res'] = '0';
    //一次只能上传一张图片
    if(!empty($_FILES)) {
        $path = ROOT_PATH."images/day_promotion";
        if(!file_exists($path)) 
        { 
        //检查是否有该文件夹，如果没有就创建，并给予最高权限 
            mkdir($path, 0700); 
        }
        $now_time = time();
        $file_path = $path."/".date('Ymd',$now_time);
        if(!file_exists($file_path)) 
        { 
        //检查是否有该文件夹，如果没有就创建，并给予最高权限 
            mkdir($file_path, 0700); 
        }
        
        $image_type = array("image/gif","image/pjpeg","image/jpeg","image/png","image/bmp"); 
        foreach($_FILES as $key=>$value){
            if($value['error'] > 0) {
                //上传出错
                $result['res'] = '0';
                $result['error'] = $value['error'];
            } else {
                if(in_array($value['type'], $image_type)) {
                    //获取文件格式
                    $typename = explode("/",$value['type']);
                    $ext = '.'.$typename[1];
                    $filename = $file_path.'/'.str_replace("files_", "goods_", $key).'_'.date('YmdHis',$now_time).$ext;
                    $result=move_uploaded_file($value['tmp_name'],$filename); 
                    if($result) {
                        //上传成功，更新数据库数据
                        $goods_info = explode("_", $key);
                        $dp_id = $goods_info[1];
                        $GLOBALS['cidb']->where("dp_id = ".$dp_id)->limit(1)->update('day_promote',array('promote_img'=>  str_replace(ROOT_PATH, '/', $filename)));
                        $result = 1;
                    }
                } else {
                    $result['res'] = '0';
                    $result['msg'] = "文件格式不对";
                }
            }
        }
    }
    echo $result;
}
