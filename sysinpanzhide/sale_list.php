<?php

/**
 * ECSHOP 销售明细列表程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: sale_list.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang', $_LANG);

if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'query' ||  $_REQUEST['act'] == 'download'))
{
    /* 检查权限 */
    check_authz_json('sale_order_stats');
    if (strstr($_REQUEST['start_date'], '-') === false)
    {
        $_REQUEST['start_date'] = local_date('Y-m-d', $_REQUEST['start_date']);
        $_REQUEST['end_date'] = local_date('Y-m-d', $_REQUEST['end_date']);
    }
    /*------------------------------------------------------ */
    //--Excel文件下载
    /*------------------------------------------------------ */
    if ($_REQUEST['act'] == 'download')
    {
        channelexl();
        exit;

        $file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
        $goods_sales_list = get_sale_list(false);
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name.xls");

        /* 文件标题 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['sales_time']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['order_sn']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['user_name']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['consignee']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['tel']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['goods_name']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['sales_price']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['goods_num']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['sales_amount']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['invoice_no']) . "\t";
        echo "\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['sales_time']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['order_sn']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['user_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['consignee']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['tel']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['sales_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_num']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['sales_amount']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['invoice_no']) . "\t";
            echo "\n";
        }
        exit;
    }
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('sale_list.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}
/*------------------------------------------------------ */
//--商品明细列表 'list'
/*------------------------------------------------------ */
else
{
    /* 权限判断 */
    admin_priv('sale_order_stats');
    /* 时间参数 */
    if (!isset($_REQUEST['start_date']))
    {
        $start_date = local_strtotime('-7 days');
    }
    if (!isset($_REQUEST['end_date']))
    {
        $end_date = local_strtotime('today');
    }
    
    $sale_list_data = get_sale_list();
    /* 赋值到模板 */
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('ur_here',          $_LANG['sell_stats']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    $smarty->assign('ur_here',      $_LANG['sale_list']);
    $smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('action_link',  array('text' => $_LANG['down_sales'],'href'=>'#download'));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('sale_list.htm');
}
/*------------------------------------------------------ */
//--获取销售明细需要的函数
/*------------------------------------------------------ */
/**
 * 取得销售明细数据信息
 * @param   bool  $is_pagination  是否分页
 * @return  array   销售明细数据
 */
function get_sale_list($is_pagination = true, $is_downloads = 0){

    global $_LANG;
    /* 时间参数 */
    $filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime('-7 days') : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime('today') : local_strtotime($_REQUEST['end_date']);
  
    /* 查询数据的条件 */
    $where = " WHERE 1 ". order_query_sql('shipped_or_finished', 'oi.') .
             " AND oi.add_time >= '".$filter['start_date']."' AND oi.add_time < '" . ($filter['end_date'] + 86400) . "'";
    
    $sql = 'SELECT COUNT(og.goods_id) ' .
           'FROM ' . $GLOBALS['ecs']->table('order_goods') . ' AS og '.
           'LEFT JOIN ' . $GLOBALS['ecs']->table('order_info') . ' AS oi ON og.order_id = oi.order_id ' . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql = 'SELECT og.goods_id, og.goods_sn, og.goods_name, og.goods_number AS goods_num, og.goods_price '.
           'AS sales_price, oi.add_time AS sales_time, oi.order_id, oi.order_sn, oi.consignee, oi.tel, oi.mobile,'.
           'oi.pay_status, oi.pay_name, oi.shipping_name, oi.shipping_note, oi.invoice_no, oi.shipping_fee, '.
           'u.user_id, u.user_name '.
           'FROM ' . $GLOBALS['ecs']->table('order_goods') . ' AS og '.
           'LEFT JOIN ' . $GLOBALS['ecs']->table('order_info') . ' AS oi ON og.order_id = oi.order_id '.
           'LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON oi.user_id = u.user_id ' .
           $where. " ORDER BY sales_time, goods_num DESC";
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }

    $sale_list_data = $GLOBALS['db']->getAll($sql);

    foreach ($sale_list_data as $key => $item)
    {
        $sale_list_data[$key]['sales_amount'] = number_format($sale_list_data[$key]['sales_price'] * $item['goods_num'], 2, '.', '');
        $sale_list_data[$key]['sales_price'] = number_format($sale_list_data[$key]['sales_price'], 2, '.', '');
        $sale_list_data[$key]['sales_time']  = local_date($GLOBALS['_CFG']['time_format'], $item['sales_time']);
        $sale_list_data[$key]['pay_status'] = $_LANG['ps'][$item['pay_status']];
        $sale_list_data[$key]['tel'] = !empty($item['mobile']) ? $item['mobile'] : $item['tel'];
        if ($item['shipping_name'] == '普通快递') {
            $sale_list_data[$key]['shipping_name'] = '普通-' . $item['shipping_note'];
        }
        if ($is_downloads == 1) {
            $sale_list_data[$key]['invoice_no'] = str_replace('<br>', ',', $item['invoice_no']);
        }
        if ($item['order_id'] == $sale_list_data[$key - 1]['order_id'] && $item['shipping_fee'] > 0) {
            $sale_list_data[$key]['shipping_fee'] = 'UP';
        }
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

function channelexl2($result=array(),$xlsfile=''){
    require($_SERVER['DOCUMENT_ROOT'].'/plugins/phpexcel/PHPExcel.php');
    require($_SERVER['DOCUMENT_ROOT'].'/plugins/phpexcel/PHPExcel/Writer/Excel2007.php');
    $objExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objExcel);

    $objProps = $objExcel->getProperties();
    //$objProps->setCreator("hnn@chujian")->setLastModifiedBy(hnn@chujian12)->setKeywords(hnn@chujian12)->setCategory("report");
    $sheet1 = $objExcel->createSheet();
    $objExcel->setActiveSheetIndex(1);
    $objActSheet = $objExcel->getActiveSheet();
    $objActSheet->setTitle('数据表');      
    $objActSheet->getDefaultColumnDimension()->setWidth(12);

    $count=count($result)+10; 
    //$objActSheet->getRowDimension(3)->setRowHeight(16);
    $objActSheet->getStyle('A2:L'.$count)->getAlignment()->setWrapText(true);
    $objActSheet->getStyle('A2:L'.$count)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objActSheet->getStyle('A2:L4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objActSheet->getStyle('A2:L4')->getFill()->getStartColor()->setRGB('CCCCCC');
    $objActSheet->getStyle('A2:L'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objActSheet->setCellValue('A2', '渠道激活数据统计')->mergeCells('A2:L2');
    $objActSheet->setCellValue('A3', '统计时间')->mergeCells('A3:A4');
    $objActSheet->setCellValue('B3', '平台')->mergeCells('B3:B4');
    $objActSheet->setCellValue('C3', '渠道名称')->mergeCells('C3:C4');
    $objActSheet->setCellValue('D3', 'SID')->mergeCells('D3:D4');
    $objActSheet->setCellValue('E3', '新增使用用户')->mergeCells('E3:E4');
    $objActSheet->setCellValue('F3', '独立登录用户数')->mergeCells('F3:F4');
    $objActSheet->setCellValue('G3', '新增注册会员')->mergeCells('G3:G4');
    $objActSheet->setCellValue('H3', '独立登录会员总数')->mergeCells('H3:H4');
    $objActSheet->setCellValue('I3', '新增登录IM会员数')->mergeCells('I3:I4');
    $objActSheet->setCellValue('J3', '独立登录IM会员数')->mergeCells('J3:J4');
    $objActSheet->setCellValue('K3', '使用总次数')->mergeCells('K3:K4');
    $objActSheet->setCellValue('L3', '注册7天会员数')->mergeCells('L3:L4');
    //取数据
    $sum1=0;$sum2=0;$sum3=0;$sum4=0;$sum5=0;$sum6=0;$sum7=0;$sum8=0;
    if(!empty($result)){
        for($i=0;$i<sizeof($result);$i++){
            $n=$i+4;
            $objActSheet->setCellValue('A'.$n,$result[$i]['day']);
            $objActSheet->setCellValue('B'.$n,$result[$i]['platform']);
            $objActSheet->setCellValue('C'.$n,$result[$i]['name']);
            $objActSheet->setCellValue('D'.$n,$result[$i]['channel']);
            $objActSheet->setCellValue('E'.$n,$result[$i]['ITEM1']);
            $objActSheet->setCellValue('F'.$n,$result[$i]['ITEM48']);
            $objActSheet->setCellValue('G'.$n,$result[$i]['ITEM2']);
            $objActSheet->setCellValue('H'.$n,$result[$i]['ITEM4']);
            $objActSheet->setCellValue('I'.$n,$result[$i]['ITEM56']);
            $objActSheet->setCellValue('J'.$n,$result[$i]['ITEM57']);
            $objActSheet->setCellValue('K'.$n,$result[$i]['ITEM50']);
            $objActSheet->setCellValue('L'.$n,$result[$i]['ITEM51']);
            $sum1+=$result[$i]['ITEM1'];
            $sum2+=$result[$i]['ITEM48'];
            $sum3+=$result[$i]['ITEM2'];
            $sum4+=$result[$i]['ITEM4'];
            $sum5+=$result[$i]['ITEM56'];
            $sum6+=$result[$i]['ITEM57'];
            $sum7+=$result[$i]['ITEM50'];
            $sum8+=$result[$i]['ITEM51'];

        }
        $num=count($result)+5;
        $objActSheet->setCellValue('D'.$num, '合计');
        $objActSheet->setCellValue('E'.$num, $sum1);
        $objActSheet->setCellValue('F'.$num, $sum2);
        $objActSheet->setCellValue('G'.$num, $sum3);
        $objActSheet->setCellValue('H'.$num, $sum4);
        $objActSheet->setCellValue('I'.$num, $sum5);
        $objActSheet->setCellValue('J'.$num, $sum6);
        $objActSheet->setCellValue('K'.$num, $sum7);
        $objActSheet->setCellValue('L'.$num, $sum8);
    }
    $outputExcel ="D:\log.xls";
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($outputExcel);
    return true;
}

function channelexl($result=array(),$xlsfile=''){
    $file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';

    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    //date_default_timezone_set('Europe/London');

    if (PHP_SAPI == 'cli')
        die('This example should only be run from a Web Browser');

    /** Include PHPExcel */
    require($_SERVER['DOCUMENT_ROOT'].'/plugins/phpexcel/PHPExcel.php');


    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                 ->setLastModifiedBy("Maarten Balliauw")
                                 ->setTitle("Office 2007 XLSX Test Document")
                                 ->setSubject("Office 2007 XLSX Test Document")
                                 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                 ->setKeywords("office 2007 openxml php")
                                 ->setCategory("Test result file");

    $objPHPExcel->getActiveSheet()->getStyle('B')->getNumberFormat()
        ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
    $objPHPExcel->getActiveSheet()->getStyle('J')->getNumberFormat()
        ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
    $objPHPExcel->getActiveSheet()->getStyle('G')->getNumberFormat()
        ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    $objPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()
        ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(11);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(11);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);


    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', $_REQUEST['start_date'].' ~ '.$_REQUEST['end_date'] . ' 出货记录汇总表')->mergeCells('A1:N1');
    $objStyleA1 = $objPHPExcel->getActiveSheet()->getStyle('A1');  
    $objAlignA1 = $objStyleA1->getAlignment();  
    $objAlignA1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    //左右居中  
    //$objPHPExcel->getActiveSheet()->getStyle('A1')->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    // Add some data
    global $_LANG;
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A2', $_LANG['sales_time'])
                ->setCellValue('B2', $_LANG['order_sn'])
                ->setCellValue('C2', $_LANG['user_name'])
                ->setCellValue('D2', $_LANG['consignee'])
                ->setCellValue('E2', $_LANG['tel'])
                ->setCellValue('F2', $_LANG['goods_name'])
                ->setCellValue('G2', $_LANG['sales_price'])
                ->setCellValue('H2', $_LANG['goods_num'])
                ->setCellValue('I2', $_LANG['pay_name'])
                ->setCellValue('J2', $_LANG['pay_status'])
                ->setCellValue('K2', $_LANG['shipping_name'])
                ->setCellValue('L2', $_LANG['sales_amount'])
                ->setCellValue('M2', $_LANG['invoice_no'])
                ->setCellValue('N2', $_LANG['shipping_fee']);

    $goods_sales_list = get_sale_list(false, 1);
    foreach ($goods_sales_list['sale_list_data'] AS $key => $value) {
        $n = $key + 3;
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $n, $value['sales_time'])
                    ->setCellValue('B' . $n, $value['order_sn'])
                    ->setCellValue('C' . $n, $value['user_name'])
                    ->setCellValue('D' . $n, $value['consignee'])
                    ->setCellValue('E' . $n, $value['tel'])
                    ->setCellValue('F' . $n, $value['goods_name'])
                    ->setCellValue('G' . $n, $value['sales_price'])
                    ->setCellValue('H' . $n, $value['goods_num'])
                    ->setCellValue('I' . $n, $value['pay_name'])
                    ->setCellValue('J' . $n, $value['pay_status'])
                    ->setCellValue('K' . $n, $value['shipping_name'])
                    ->setCellValue('L' . $n, $value['sales_amount'])
                    ->setCellValue('M' . $n, $value['invoice_no'])
                    ->setCellValue('N' . $n, $value['shipping_fee']);
    }
 

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Simple');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $file_name . '.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}
?>
