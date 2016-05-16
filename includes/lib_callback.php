<?php
/**
 * 回调函数
 *
 * @author ming
 */

/*注册回调函数*/
function reg_callback($type = 'web', $from = 'ecshop'){
    //获取搜索关键词
    $keywords = trace::get_keywords();
    $keyword = '';
    //默认获取第一个进来的关键词
    if(!empty($keywords)){
        $keyword = $keywords[0];
    }
    //百度统计自定义变量    
    /*
     * index：是自定义变量所占用的位置。取值为从1到5。该项必选。
     * name：是自定义变量的名字。该项必选。
     * value：就是自定义变量的值。该项必选。
     * opt_scope：是自定义变量的作用范围。该项可选。1为访客级别（对该访客始终有效），2为访次级别（在当前访次内生效），3为页面级别（仅在当前页面生效）。默认为3。
     */
    $value = $type."_".$keyword;
    $str = "_hmt.push(['_setCustomVar', 1, 'reg', '$value']);";
    return $str;
}