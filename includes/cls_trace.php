<?php

/**
 * Description of cls_trace
 *
 * @author ming
 */
if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
class trace {
    /*获取当前请求的地址*/
    public static function get_cur_url(){
        if(!empty($_SERVER["REQUEST_URI"])) 
        { 
            $scriptName = $_SERVER["REQUEST_URI"]; 
            $nowurl = $scriptName; 
        } 
        else 
        { 
            $scriptName = $_SERVER["PHP_SELF"]; 
            if(empty($_SERVER["QUERY_STRING"])) 
            { 
                $nowurl = $scriptName; 
            } 
            else 
            { 
                $nowurl = $scriptName."?".$_SERVER["QUERY_STRING"]; 
            } 
        } 
        return $nowurl; 
    }
    
    /*记录页面浏览轨迹*/
    public static function trace_browse($action = TRACE_NORMAL, $data = '', $from = TRACE_FROM_ECS, $force_record = false){
        if(!$force_record){
            //忽略ajax请求
            if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){ 
                return false;
            }
        }
        //目标url
        $target_url = self::get_cur_url();
        $reg_text = "/^(.*)\.(jpg|jpeg|bmp|png|gif|css|js)$/";
        //排除获取css,images等文件的操作
        if(preg_match($reg_text, $target_url)){
            return false;
        }
        //sessionkey
        $sess_id = SESS_ID;
        /* 来源url */   
        $referer_domain = $referer_url = '';
        $pos = strpos($_SERVER['HTTP_REFERER'], '/', 9);
        if ($pos !== false)
        {
            $referer_domain = substr($_SERVER['HTTP_REFERER'], 0, $pos);
            $referer_url = substr($_SERVER['HTTP_REFERER'], $pos);
            
            /* 来源关键字 */
            if (!empty($referer_domain) && !empty($referer_url))
            {
                $other_data = array();
                //当操作不是普通操作时，找到最初进来的来源url，将此次最终浏览结果反馈到最初来源url
                if($action != 0 || !empty($data)){
                    $other_data['action'] = $action;
                    $other_data['data'] = $data;   
                    $other_data['sesskey'] = $sess_id;
                }
                self::save_searchengine_data($referer_domain, $referer_url, $target_url, $other_data);
            }
        }
        else
        {
            $referer_domain = $referer_url = '';
        }
        
        //添加时间
        $add_time = gmtime();
        $ip = real_ip();          
        //用户浏览器
        $user_agent = get_user_browser();
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('trace_browse') . 
               " (`from`, `sesskey`, `referer_domain`, `referer_url`, `target_url`, `user_agent`, `ip`, `area`, `area_id`, `action`, `data`, `add_time`) VALUES " . 
               " ('$from', '$sess_id', '$referer_domain', '$referer_url', '$target_url', '$user_agent', '$ip', ' ', ' ', '$action', '$data', '$add_time')";
        $GLOBALS['db']->query($sql);
        $trace_id =  $GLOBALS['db']->insert_id();
        //记录用户的IP详细信息
        self::update_user_ipdata($trace_id,$ip);
    }
    
    /*更新用户ip信息（ip数据来源：淘宝ip）*/
    public static function update_user_ipdata($trace_id,$ip){
        if($ip != '127.0.0.1' && $ip !='localhost'){
            $result = '';
            $url = "http://ip.taobao.com/service/getIpInfo.php?ip=$ip";
            $fp = fopen($url, 'r');  
            //返回请求流信息（数组：请求状态，阻塞，返回值是否为空，返回值http头等）
            stream_get_meta_data($fp);  
            while(!feof($fp)) {  
                $result .= fgets($fp, 1024);  
            }  
            fclose($fp);
            $area_data = json_decode($result);
            if($area_data->code == 0){
                $area = $area_data->data->country.' '.$area_data->data->region.' '.$area_data->data->city.' '.$area_data->data->county.' '.$area_data->data->isp;
                $area_id = $area_data->data->country_id.'_'.$area_data->data->region_id.'_'.$area_data->data->city_id.'_'.$area_data->data->county_id.'_'.$area_data->data->isp_id;
                $sql = " UPDATE " . $GLOBALS['ecs']->table('trace_browse') . "SET area = '$area', area_id = '$area_id' WHERE trace_id = $trace_id";
                $GLOBALS['db']->query($sql);
            }
        }
    }
    
    /*保存搜索引擎数据*/
    public static function save_searchengine_data($referer_domain, $referer_url, $target_url = '', $other_data = array()){
        $time = gmtime();
        $sql = $engine =  '';
        $sess_id = SESS_ID; 
        $engine = self::get_engine_type($referer_domain);
        $table = 'engine_baidu';
        if($engine != 'ecshop' && $engine != 'ectouch'){
            //在session中记录此次来源的搜索引擎
            $_SESSION['search_engine'] = $engine;                
            $referer_params = $target_params =  array();
            //解析url参数
            $referer_query = parse_url($referer_url);
            $referer_params = self::convert_url_query($referer_query['query']);
            $target_query = parse_url($target_url);
            $target_params = self::convert_url_query($target_query['query']);             
            if ($engine == 'baidu')
            {                       
                if(isset($referer_params['word']) && empty($referer_params['wd'])){
                    $referer_params['wd'] = $referer_params['word'];
                }
                if(!preg_match("(utf8|utf-8)", strtolower($referer_url))){
                    $referer_params['wd'] = mb_convert_encoding($referer_params['wd'], 'utf-8', 'gb2312');
                }
                $referer_params['wd'] = urldecode($referer_params['wd']); 
                $search_data = serialize($target_params);
            }
            elseif ($engine == 'haosou'){
                if(!preg_match("(utf8|utf-8)", strtolower($referer_url))){
                    $referer_params['q'] = mb_convert_encoding($referer_params['q'], 'utf-8', 'gb2312');
                }
                $referer_params['q'] = urldecode($referer_params['q']);
                $referer_params['wd'] = $referer_params['q'];
                unset($referer_params['q']);
                $search_data = serialize($referer_params);
            }
            elseif ($engine == 'sogou'){
                $referer_params['wd'] = $referer_params['keyword'];
                if(isset($referer_params['query']) && empty($referer_params['keyword'])){
                    $referer_params['wd'] = $referer_params['query'];
                }          
                if(!preg_match("(utf8|utf-8)", strtolower($referer_url))){
                    $referer_params['wd'] = mb_convert_encoding($referer_params['wd'], 'utf-8', 'gb2312');
                }
                unset($referer_params['query']);
                unset($referer_params['keyword']);
                $referer_params['wd'] = urldecode($referer_params['wd']);
                $search_data = serialize($referer_params);               
            }
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table($table) .
                       " (`sesskey`, `from`, `wd`, `target_url`, `time`, `search_data`) " .
                       " VALUES ('$sess_id', '$engine', '{$referer_params['wd']}', '$target_url', $time, '$search_data')"; 
            $GLOBALS['db']->query($sql);
        }
//        if(isset($_SESSION['search_engine']) && !empty($other_data)){
//            //如果有数据要记录，则写入引擎数据记录表
//            //查找本次浏览的engine_id
//            $sql = "SELECT id FROM " . $GLOBALS['ecs']->table($table) . " WHERE sesskey = '$sess_id'";           
//            $engine_id = $GLOBALS['db']->getOne($sql);
//            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('engine_data') .
//                    " (`engine`, `engine_id`, `action`, `data`)" .
//                    " VALUES ('{$_SESSION['search_engine']}', '$engine_id', '{$other_data['action']}', '{$other_data['data']}')";
//            $GLOBALS['db']->query($sql);
//        }
    }
    
    /*将url的查询字段转换成参数数组*/
    public static function convert_url_query($query)
    { 
        $queryParts = explode('&', $query); 
        $params = array(); 
        foreach ($queryParts as $param) 
        { 
            $item = explode('=', $param); 
            $params[$item[0]] = $item[1]; 
        } 
        return $params; 
    }
    
    /*将参数数组转换成url查询字段*/
    public static function get_url_query($array_query)
    {
        $tmp = array();
        foreach($array_query as $k=>$param)
        {     
            $tmp[] = $k.'='.$param;
        }
        $params = implode('&',$tmp);
        return $params;
    }
    
    /*判断来源域名*/
    public static function get_engine_type($domain = ''){
        $engine = 'ecshop';
        if(!empty($domain)){
            if(strpos($domain, 'baidu.') !== false)
            {
                $engine = 'baidu';
            }
            elseif(strpos($domain, 'google.') !== false){
                $engine = 'google';
            }
            elseif(strpos($domain, 'soso.com') !== false){
                $engine = 'soso';
            }
            elseif(strpos($domain, 'sogou.com') !== false){
                $engine = 'sogou';
            }
            elseif(strpos($domain, 'yahoo.') !== false){
                $engine = 'yahoo';
            }
            //360好搜
            elseif(strpos($domain, 'haosou.') !== false){
                $engine = 'haosou';
            }
        }
        return $engine;
    }
      
    /*获取搜索引擎的订单关键词*/
    function get_order_keyword($order_sn = ''){
        $keyword = '';
        if(!empty($order_sn)){
            $sql = "SELECT eb.wd, eb.from FROM ". $GLOBALS['ecs']->table('trace_browse') . " AS tb " . 
                   " LEFT JOIN " . $GLOBALS['ecs']->table('engine_baidu') . " AS eb ON eb.sesskey = tb.sesskey " . 
                   " WHERE tb.action = " . TRACE_ORDER_SUBMIT . " AND data = '$order_sn'";
            $res = $GLOBALS['db']->getRow($sql);          
            if(!empty($res['from']) && !empty($res['wd'])){
                return $res['from'] . '：' . $res['wd'];
            }
        }
        return $keyword;
    }
    
    /*获取会员注册的关键词*/
    function get_user_keyword($user_id = 0){
        $keyword = '';
        if(!empty($user_id)){
            $sql = "SELECT eb.wd, eb.from FROM " . $GLOBALS['ecs']->table('trace_browse') . " AS tb " . 
                   " LEFT JOIN " . $GLOBALS['ecs']->table('engine_baidu') . " AS eb ON eb.sesskey = tb.sesskey " . 
                   " WHERE tb.action = " . TRACE_USER_REGISTER . " AND data = '$user_id'";
            $res = $GLOBALS['db']->getRow($sql);
            $res = $GLOBALS['db']->getRow($sql);          
            if(!empty($res['from']) && !empty($res['wd'])){
                return $res['from'] . '：' . $res['wd'];
            }
        }
        return $keyword;
    }
    
    /*获取本次浏览搜索引擎的关键词*/
    public static function get_keywords(){
        $sess_id = SESS_ID;
        $result = array();
        if($sess_id != 'SESS_ID'){
            $sql = "SELECT wd FROM " . $GLOBALS['ecs']->table("engine_baidu") . " WHERE sesskey = '$sess_id' GROUP BY wd";
            $res = $GLOBALS['db']->getAll($sql);
            if(!empty($res)){
                foreach($res as $key => $row){
                    $result[$key] = $row['wd'];
                }
            }
        }
        return $result;
    } 
}
