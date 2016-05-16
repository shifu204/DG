<?php echo $this->fetch('library/page_header.lbi'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'flexSlider/jquery.flexslider-min.js')); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'jqueryui/jquery-ui.min.js')); ?>
<link rel="stylesheet" type="text/css" href="js/flexSlider/flexslider.css" />
        
        <div class="navAd">
            <!--<div class="wm01190 navAdwrap clear">-->
            <div class="navAdwrap clear">
                <div class="flexslider" style="border:none;">
                    <ul class="slides">
                        <?php foreach($this->_var['home_ad'] as $hk=>$hv):?>                    
                        <li class="ad_div_img" tag="<?php echo $hk;?>" data-img="/<?php echo $hv['content'];?>">
                            <a href="<?php echo $hv['url'];?>" target="_blank" style="display:block; width: 100%; height: 100%;"></a>
                        </li>                      
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
        
        
        <div class="dayPromote clearfix f1nobg" id="dayPromote">
            <div class="w1190 dayPromotewrap">
               <!-- <div class="dayPromoteTitle">
                    <span class="f_l dayPromoteTitleLeft"><a href="/">天天特价</a></span>
                    倒计时
                    <span class="f_r dayPromoteTitleRight">每天10点更新</span>
                </div> -->
               <?php if(!empty($this->_var['day_promote'])):?>
               <?php @include $this->_var['theme_path'].'home/day_promote.php';?>                
               <?php endif;?>
            </div>
        </div>
        
        <?php foreach($this->_var['floors'] as $fk=>$fv):?>
            <?php
                if($fv['i'] == 1){
                    @include $this->_var['theme_path'].'home/f1.php';
                } else if($fv['i'] == 2){
                    @include $this->_var['theme_path'].'home/f2.php';
                } else if($fv['i'] == 3){
                    @include $this->_var['theme_path'].'home/f3.php';
                } else if($fv['i'] == 4){
                    @include $this->_var['theme_path'].'home/f4.php';
                } else if($fv['i'] == 5){
                    @include $this->_var['theme_path'].'home/f5.php';
                }
                
            ?>
        <?php endforeach;?>        
                    
        
        <?php echo $this->fetch('home/hot_topic.lbi'); ?>
        <div class="floorBlank"></div>      
        <script type="text/javascript" src="<?php echo $this->_var['theme_path']; ?>js/index.js"></script>
        <?php  if($_GET['pos'] == 'dayPromote'):?>
        <script type="text/javascript">
            jump_position('dayPromote');
        </script>
        <?php endif;?>
        <?php echo $this->fetch('library/index/index_left_nav.lbi'); ?>
        <?php echo $this->fetch('library/page_footer.lbi'); ?> 
    </body>
</html>
