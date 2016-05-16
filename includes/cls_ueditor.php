<?php
if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

if (!defined('EC_CHARSET'))
{
    define('EC_CHARSET', 'utf-8');
}
class ueditor {
    var $base_path = '';
    var $instance_name;
    var $html = '';
    var $width;
    var $height;
    
    function __construct($instance_name) {
        $this->instance_name = $instance_name;
        $this->width = "100%";
        $this->height = '200';
    }
    
    function create_html_editor($html=''){
        $this->html = $html;
        $return = "<script id='$this->instance_name' type='text/plain' style='width:{$this->width};height:{$this->height}px;'>$this->html</script>";
        $return .="<script type='text/javascript'>UE.getEditor('$this->instance_name');</script>";
        return $return;
    }
}
?>