<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/../includes/init.php');

$typeCom = $_GET["com"];
$typeNu = $_GET["nu"];
//载入快递公司代码
include_once("kuaidi_company.php");
if(isset($typeCom)&&isset($typeNu)){
    $AppKey = '110238';
    $AppSecret = 'b59dfe6764e7c471f745df9129875c83';
    //key=appkey,order=快递单号,id=快递公司代码,ord=排序,show=数据格式
    //$url = "http://www.aikuaidi.cn/rest/?key=$AppKey&order=$typeNu&id=$typeCom&ord=asc&show=json";
    $url = "http://api.ickd.cn/?id=$AppKey&secret=$AppSecret&com=$typeCom&nu=$typeNu&type=json&encode=utf8&ord=asc";
    //优先使用curl模式发送数据
    if (function_exists('curl_init') == 1){
      $curl = curl_init();
      curl_setopt ($curl, CURLOPT_URL, $url);
      curl_setopt ($curl, CURLOPT_HEADER,0);
      curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
      curl_setopt ($curl, CURLOPT_TIMEOUT,10);
      $get_content = curl_exec($curl);
      curl_close ($curl);
    }else{
        $get_content = file_get_contents($url);
    }
    $formated_content = json_decode($get_content);
    //格式化查询结果
    $return = '<table class="kuaidi-table"><tbody>';
    $return .='<tr>';
    $return .='<td>快递单号：'.$typeNu.'</td>';
    $return .='</tr>';
    if($formated_content->errCode == 0){        
        foreach($formated_content->data as $row){
            $return .='<tr>';
            $return .= "<td class='time'>{$row->time}</td><td>{$row->context}</td>";
            $return .='</tr>';
        }
    }
    $return .='</tbody></table>';
    echo $return;
}else{
	echo '查询失败，请重试';
}
exit();
