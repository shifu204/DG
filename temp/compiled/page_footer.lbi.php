<div class="footerBlank"></div>
<div class="footer">
    <?php
        $cart_info = get_cart_info();
    ?>
    
    <div class="buoy" id="buoy">
        <div class="buoy_item_fixed">
            <div class="buoy_item_wrap">
                <div class="qr-code relative lower_buoy_item"><a>加入微信</a></div>
                <div class="qr-code-big buoy_item_fixed_show absolute uper_buoy_item"></div>
            </div>
        </div>
        <div class="buoy_item">
            <div class="buoy_item_wrap"> 
                <div class="online_consultant lower_buoy_item"><a>小贝咨询</a></div>
                <div class="online_consultant uper_buoy_item"><a target="_blank" href="tencent://message/?uin=2462814798&Site=www.deebei.net&Menu=yes">小贝咨询</a></div>
            </div>
        </div>
        <div class="buoy_item">
            <div class="buoy_item_wrap">              
                    <div class="member_center lower_buoy_item"><a href="javascript:get_user_info();">会员中心</a></div>
                    <div class="member_center2 uper_buoy_item"><a href="javascript:get_user_info();">会员中心</a></div>         
            </div>
        </div>
        <div class="buoy_item">
            <div class="buoy_item_wrap">
                <div class="buoy_cart lower_buoy_item"><a href="flow.php?step=cart">购物车</a></div>
                <div class="buoy_cart2 uper_buoy_item"><a href="flow.php?step=cart">购物车</a></div>
                <span id="buoy_cart_info_num" class="cart_num absolute"><?php echo $cart_info['number'];?></span>     
            </div>
        </div>
        <div class="buoy_item" onclick="go_to_top();">
            <div class="buoy_item_wrap">
                <div class="pack_up lower_buoy_item" onclick="go_to_top();"><a>回到顶部</a></div>
                <div class="pack_up2 uper_buoy_item" onclick="go_to_top();"><a>回到顶部</a></div>
            </div>
        </div>
    </div>
    
    
<div class="clearfix relative footer-footer">
    <div class="helper_top"></div>
    <div class="helper">
    <div class="helper_wrap wm01190 clearfix">
        <div class="helper_right clearfix">
            <div class="helper_ul clearfix">
                <div class="ul_left">
                <?php if ($this->_var['helps']): ?>
                <ul>
                    <?php $_from = $this->_var['helps']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'help');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['help']):
?>
                    <li>                       
                        <ul class="helper_1">
                            <li class="helper_title helper_title_<?php echo $this->_var['key']; ?>"><?php echo $this->_var['help']['cat_name']; ?></li>
                            <?php $_from = $this->_var['help']['article']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'article');if (count($_from)):
    foreach ($_from AS $this->_var['article']):
?>
                            <a href="<?php echo $this->_var['article']['url']; ?>"><li><?php echo $this->_var['article']['title']; ?></li></a>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </ul>
                    </li>
                    <li class="ul_b"><img src="<?php echo $this->_var['theme_path']; ?>images1/footer/nav_b.jpg"/></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>                
                </ul>
                <?php endif; ?>
               
                </div>
                <div class="img_right">
                    <div class="img_right_t clearfix">
                        <div class="img_97">
                            <div class="debei_weixin"></div>
                            <div class="img_span">官方微信</div>
                        </div>
                        <div class="img_97">
                            <div class="debei_shouji"></div>
                            <div class="img_span">手机网站</div>
                        </div>
                    </div>
                    
                    <div class="kefu">
                        客服热线:<span class="kefured">4000-397-006</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="border_bo"></div>
        <div class="guarantee">
            <ul class="clearfix">
                <li>
                    <img src="<?php echo $this->_var['theme_path']; ?>images1/footer/guarantee_1.png"/>
                </li>
                <li>
                    <img src="<?php echo $this->_var['theme_path']; ?>images1/footer/guarantee_2.png"/>
                </li>
                <li>
                    <img src="<?php echo $this->_var['theme_path']; ?>images1/footer/guarantee_3.png"/>
                </li>
                <li>
                    <img src="<?php echo $this->_var['theme_path']; ?>images1/footer/guarantee_4.png"/>
                </li>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
    <div class="helper_bottom"></div>
        <div class="helper_nav_warp">
            <div class="helper_nav clearfix">
                <ul class="clearfix">
                    <li><a href="/">首页</a></li>
                    <li><img src="<?php echo $this->_var['theme_path']; ?>images1/footer/bottom_b.jpg"/></li>
                    <li><a href="http://www.deebei.net/topic.php?topic_id=20" target="_blank">关于我们</a></li>
                    <li><img src="<?php echo $this->_var['theme_path']; ?>images1/footer/bottom_b.jpg"/></li>
                    <li><a href="http://www.deebei.net/topic.php?topic_id=20" target="_blank">正品保证</a></li>
                    <li><img src="<?php echo $this->_var['theme_path']; ?>images1/footer/bottom_b.jpg"/></li>
                    <li><a href="http://www.deebei.net/topic.php?topic_id=17" target="_blank">联系我们</a></li>
                    <li><img src="<?php echo $this->_var['theme_path']; ?>images1/footer/bottom_b.jpg"/></li>
                    <li><a href="http://test.deebei.net/article-9.html">帮助中心</a></li>
                    <li><img src="<?php echo $this->_var['theme_path']; ?>images1/footer/bottom_b.jpg"/></li>
                    <li>友情链接</li>
                    <li><img src="<?php echo $this->_var['theme_path']; ?>images1/footer/bottom_b.jpg"/></li>
                </ul>
            </div>
        </div>
    </div>
</div>
        <div class="copyright clearfix">
            
            <div id="footer" class="block950">
             <div class="text">
                <?php if($this->_var['stats_code']):?>
                    <div><?php echo $this->_var['stats_code'];?></div>
                <?php endif;?>
                <span style="display:none">
                    <?php echo $this->_var['copyright']; ?>
                </span>                 
                <!--<div><?php echo $this->_var['shop_address']; ?></div>-->
                <div><?php echo $this->_var['shop_postcode']; ?></div>            
             <?php if ($this->_var['service_email']): ?>
                  E-mail:<?php echo $this->_var['service_email']; ?>
             <?php endif; ?>         
             <?php $_from = $this->_var['qq']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'im');if (count($_from)):
    foreach ($_from AS $this->_var['im']):
?>
                  <?php if ($this->_var['im']): ?>
                  <?php echo $this->_var['im']; ?>
                  <?php endif; ?>
                  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                  <?php $_from = $this->_var['ww']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'im');if (count($_from)):
    foreach ($_from AS $this->_var['im']):
?>
                  <?php if ($this->_var['im']): ?>
                  <a href="http://amos1.taobao.com/msg.ww?v=2&uid=<?php echo urlencode($this->_var['im']); ?>&s=2" target="_blank"><img src="http://amos1.taobao.com/online.ww?v=2&uid=<?php echo urlencode($this->_var['im']); ?>&s=2" width="16" height="16" border="0" alt="淘宝旺旺" /><?php echo $this->_var['im']; ?></a>
                  <?php endif; ?>
                  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                  <?php $_from = $this->_var['msn']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'im');if (count($_from)):
    foreach ($_from AS $this->_var['im']):
?>
                  <?php if ($this->_var['im']): ?>
                  <img src="themes/deebeis/images/msn.gif" width="18" height="17" border="0" alt="MSN" /> <a href="msnim:chat?contact=<?php echo $this->_var['im']; ?>"><?php echo $this->_var['im']; ?></a>
                  <?php endif; ?>
                  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                  <?php $_from = $this->_var['skype']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'im');if (count($_from)):
    foreach ($_from AS $this->_var['im']):
?>
                  <?php if ($this->_var['im']): ?>
                  <img src="http://mystatus.skype.com/smallclassic/<?php echo urlencode($this->_var['im']); ?>" alt="Skype" /><a href="skype:<?php echo urlencode($this->_var['im']); ?>?call"><?php echo $this->_var['im']; ?></a>
                  <?php endif; ?>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_5454931'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s19.cnzz.com/stat.php%3Fid%3D5454931%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script></span>&nbsp;
              <?php if ($this->_var['icp_number']): ?>
              <div style="line-height:34px;">
              <?php echo $this->_var['lang']['icp_number']; ?>:<a href="http://www.miitbeian.gov.cn/" target="_blank" style="line-height:34px;"><?php echo $this->_var['icp_number']; ?></a> &nbsp;&nbsp;
              <!--<img src="themes/deebeis/images/tel.png" style="vertical-align:middle;" /> -->
              <span>  
            
              </div>
              <?php endif; ?>
              <span style="display:none"><?php 
$k = array (
  'name' => 'query_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?><br /></span>
              <div><a href="http://se.360.cn/" target="_blank" >360安全浏览器</a></div>             
              <div align="left"></div>
                <div class="subFooterx" style="padding-top:18px;">                      
                <span title="支付宝特约商家"><img src="themes/deebeis/images/ShoppingArea/Common/morelogo1.png" alt="支付宝特约商家" /></span>&nbsp;&nbsp;
                <span title="银联特约商家"><img src="themes/deebeis/images/ShoppingArea/Common/morelogo2.png" alt="银联特约商家" /></span>&nbsp;&nbsp;
                <!--<span title="平安保险承保"><img src="themes/deebeis/images/ShoppingArea/Common/pingan.jpg" alt="平安保险承保" style="height: 35px;" /></span>&nbsp;&nbsp;-->
                
                <script src="http://kxlogo.knet.cn/seallogo.dll?sn=e1311134406004339876ee000000&size=4"></script>
                
                <!--<span title="诚信网站"><img src="themes/deebeis/images/ShoppingArea/Common/morelogo4.png" alt="诚信网站" /></span>-->&nbsp;&nbsp;
                <span><a href="http://webscan.360.cn/index/checkwebsite/url/www.deebei.net"><img border="0" height="35" src="http://img.webscan.360.cn/status/pai/hash/59d99388c81b2bbedaa84c32074d6580"/></a></span>                  
                </div>
                <div class="blank"></div> 
             </div>
            </div>
            <div style="text-align:center; height:30px;">&nbsp;</div> 
            <script type="text/javascript">
                function get_user_info(){
                    $.getJSON('user.php?act=get_user_login_state',function(data){
                        if(data.error == ""){
                            window.location.href = "user.php";
                        } else {
                            user_login_dialog();
                        }
                    });
                }
                side_bar_fixed($("#buoy"),'right','undefined','top','undefined',0);
                window.onresize = function(){
                    side_bar_fixed($("#buoy"),'right','undefined','top','undefined',0);
                };
            </script>           
        </div>
    </div>

<link rel="stylesheet" type="text/css" href="<?php echo $this->_var['theme_path']; ?>reset.css" />