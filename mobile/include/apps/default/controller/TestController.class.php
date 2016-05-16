<?php

/**
 * Description of TestController
 *
 * @author ming
 */
class TestController extends CommonController{
    function index(){
        $this->display('test.dwt');
    }
    
    function test(){
        $this->display('test/reduce_price.dwt');
    }
    
    function test1(){
        $this->display('test/activity_61.dwt');
    }
    function wangwang_down(){
        $this->display('test/wangwang_down_20150611.dwt');
    }
}
