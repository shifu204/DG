<?php

/**
 * ECSHOP 商品详情
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: goods.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);

$_REQUEST['act'] = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';



function creat_thumbnail($img,$w,$h,$path){
	
	$org_info = getimagesize($img); //获得图像大小且是通过post传递过来的

	$orig_w = $org_info[0]; //图像宽度
	$orig_h = $org_info[1]; //图像高度
	$orig_type = $org_info[2]; //图片类别即后缀 1 = GIF，2 = JPG，3 = PNG，
	$imgform = $org_info['mime'];
	
	//是真彩色图像
	if (function_exists("imagecreatetruecolor")){
		switch($orig_type){
			//从给定的gif文件名中取得的图像
			case 1  : $thumb_type = ".gif"; $_creatImage = "imagegif"; $_function = "imagecreatefromgif";
			break;
			//从给定的jpeg,jpg文件名中取得的图像
			case 2  : $thumb_type = ".jpg"; $_creatImage = "imagejpeg"; $_function = "imagecreatefromjpeg";
			break;
			//从给定的png文件名中取得的图像
			case 3  : $thumb_type = ".png"; $_creatImage = "imagepng"; $_function = "imagecreatefrompng";
			break;
		}
	}
	//如果从给定的文件名可取得的图像
	if(function_exists($_function))$orig_image = $_function($img); //从给定的$img文件名中取得的图像
	
	/* 原始图片以及缩略图的尺寸比例 */
	$scale_org      = $orig_w / $orig_h;
	/* 处理只有缩略图宽和高有一个为0的情况，这时背景和缩略图一样大 */
	if ($w == 0)$w = $h * $scale_org;
	if ($h == 0)$h = $w / $scale_org;
	
	if ($orig_w / $w > $orig_h / $h){
		$dst_w  = $w;
		$dst_h  = $w / $scale_org;
	}else{
		/* 原始图片比较高，则以高度为准 */
		$dst_w  = $h * $scale_org;
		$dst_h = $h;
	}
	
	$dst_x = ($w  - $dst_w)  / 2;
	$dst_y = ($h - $dst_h) / 2;
 
	$sm_image = imagecreatetruecolor($dst_w, $dst_h); //创建真彩色图片
	//重采样拷贝部分图像并调整大小
	if(function_exists('imagecopyresampled')){
        imagecopyresampled($sm_image, $orig_image, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $orig_w, $orig_h);
    }else{
        imagecopyresized($sm_image, $orig_image, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $orig_w, $orig_h);
    }
	
	$tnpath = $path."/".basename($img); //缩略图的路径
	$thumbnail = @$_creatImage($sm_image, $tnpath, 90); //生成图片,成功返回true(或1)
	header("content-type:$imgform");  
	@$_creatImage($sm_image, '', 90); //在浏览器输出图像
	imagedestroy($sm_image); //销毁图像
}


if(isset($_REQUEST['getimg'])){
	$dir = 'data/shaidanimg/';
	$file = $dir.$_REQUEST['getimg'];
	$outdir = ROOT_PATH.$dir.'min/';	
	if (!file_exists($outdir))make_dir($outdir);/* 如果目标目录不存在，则创建它 */
	if(is_file(ROOT_PATH.$file)){
		creat_thumbnail(ROOT_PATH.$file,200,0,ROOT_PATH.$dir.'min/');
	}
	die();
}

if($_REQUEST['act']=='shaidan_love'){
	include('includes/cls_json.php');
	$json   = new JSON;
	$res    = array('err_no'=>1, 'err_msg' => '', 'id' => 0, 'number' => 0);
	
	$s_id = intval($_REQUEST['s_id']);
	if($s_id){
		$db->query('UPDATE ' . $ecs->table('shaidan') . " SET lovenum = lovenum + 1 WHERE msg_id = '$s_id'");
		$sql = "SELECT lovenum FROM " .$GLOBALS['ecs']->table('shaidan'). " WHERE msg_id = '$s_id'";
		$res['number'] = $GLOBALS['db']->getOne($sql);
		$res['id'] = $s_id;
	}
	
	die($json->encode($res));
	die();
}

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

$act = array();	
$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
	
    include_once(ROOT_PATH . 'includes/lib_shaidan.php');

	$where = '';
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);
    if ($order_id) {
        $act['order_id'] = $order_id;
    }
    $order_info = array();
	
	$s_case = intval($_GET['s_case']);
	if($s_case){
		$where = ' AND msg_type='.$s_case;
		$act['s_case'] = $s_case;
	}
	

    /* 获取用户晒单的数量 */
    if ($order_id){
        $sql = "SELECT COUNT(*) FROM " .$ecs->table('shaidan')." WHERE parent_id = 0 AND order_id = '$order_id' AND msg_status = 1".$where;
    }else{
        $sql = "SELECT COUNT(*) FROM " .$ecs->table('shaidan')." WHERE parent_id = 0 AND msg_status = 1".$where;
    }

    $record_count = $db->getOne($sql);

    /* 获取晒单的数量 */
    $pager = get_pager('shaidan_list.php', $act, $record_count, $page, 30);
    $smarty->assign('shaidan_list', get_shaidan_list($_SESSION['user_id'], $_SESSION['user_name'], $pager['size'], $pager['start'], $order_id, true, $where));
    $smarty->assign('pager',        $pager);
	$smarty->assign('brand_list', get_brand_list());
	

    assign_template();
    $position = assign_ur_here(0, '我要晒单');
    $smarty->assign('page_title', $position['title']);    // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
    $smarty->assign('helps',      get_shop_help());       // 网店帮助

    $smarty->assign('categories', get_categories_tree()); // 分类树
    $smarty->assign('top_goods',  get_top10());           // 销售排行
    $smarty->assign('cat_list',   cat_list(0, 0, true, 2, false));
    $smarty->assign('brand_list', get_brand_list());
    $smarty->assign('promotion_info', get_promotion_info());

$smarty->assign('is_shaidan_list',  true);

$smarty->display('shaidan_list.dwt');
?>
