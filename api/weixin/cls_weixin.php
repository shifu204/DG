<?php
/**
 * 微信接口类
 *
 * @author ming
 */
class debei_weixin {
    /*公众号相关*/
    public $token = 'deebei';
    public $encodingAESKey = 'qE8gKxucKD3nDG3hOnPDfJAUUlfcnz7UE9OvCndt7pT';
    /*微信开放平台相关*/
    public $appid = 'wx01af39a93a1f2179';
    public $secret = 'cad318695b3da37494d56ab160092c50';
    public $user_info;
    //access_token有效时间
    private $token_life_time = '7200';
    private $code = '';
    private $state = '';
    private $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    function __construct() {
        
    }
    
    /*微信服务器配置验证函数*/
    public function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array($this->token, $timestamp, $nonce);
        
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return $_GET['echostr'];
        }else{
            return false;
        }
    }  
    
    function web_respond(){
        if(isset($_GET['code']) && !empty($_GET['code'])){
            $this->code = trim($_GET['code']);
        }
        $this->state = trim($_GET['state']);
        //成功获取到access_token
        if($this->get_access_token()){
            //获取用户详细信息
            if($this->get_user_info()){
                return $this->user_info;
            }
        } else {
            return false;
        }
    }
    
    function curl_get_data($url){
        if(!empty($url)){
            //初始化
            $ch = curl_init();
            
            //设置选项，包括URL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
            curl_setopt($ch, CURLOPT_HEADER, 0);
            
            //执行并获取HTML文档内容
            $output = curl_exec($ch);
            //释放curl句柄
            curl_close($ch);
            
            $parms = json_decode($output);
            return $parms;
        } else {
            return false;
        }
    }
    
    /*获取access_token*/
    function get_access_token(){            
        $url = $this->access_token_url.'?appid='.$this->appid.'&secret='.$this->secret.'&code='.$this->code.'&grant_type=authorization_code';
        $res = $this->curl_get_data($url);
        if($res){
            if(!isset($res->errcode)){
                $this->user_info = $res;   
                return true;
            }
        }
        return false;
    }
    
    /*获取用户的基本信息*/
    function get_user_info(){
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$this->user_info->access_token}&openid={$this->user_info->openid}";
        $res = $this->curl_get_data($url);
        if($res){
            if(!isset($res->errcode)){
                $this->user_info->base_info = $res;
                return true;
            }
        }
        return false;
    }
    
    function  get_code(){
        return $this->code;
    }
    
    function get_state(){
        return $this->state;
    }
}
