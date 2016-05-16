 
<div class="gboxWrap w1190" id="content_area">
    <div class="goods_box">          
        <?php if(!empty($this->_var['goods'])):?>
        <?php foreach($this->_var['goods'] as $gk=>$gv):?>
        <div class="category_good relative rounded
             <?php 
                if($gk%4 == 0){
                    echo "first_goods";
                }
             ?>
        ">
            <a href="<?php echo $gv['url']?>" target="_blank">
                <img src="<?php echo $gv['goods_thumb']?>" />
                <div class="goodsName"><?php echo $gv['goods_name']?></div>
                <div class="goodsPrice">                  
                    <?php echo $gv['formated_final_price']?>
                </div>
                <?php if($gv['is_total_two']):?>
                <div class="goodsGive absolute total_two"></div>
                <?php endif;?>
                <?php if($gv['is_total_four']):?>
                <div class="goodsGive absolute total_four"></div>
                <?php endif;?>
                <?php if($gv['is_buy_six']):?>
                <div class="goodsGive absolute six_give_one"></div>
                <?php endif;?>                
                <?php if($gv['is_buy_nine']):?>
                <div class="goodsGive absolute nine_give_two"></div>
                <?php endif;?>
                <?php if($gv['goods_sub_title'] != null):?>
                    <div class="simple-description simple-description-bg"></div>
                    <div class="simple-description"><?php echo $gv['goods_sub_title']?></div>
                <?php endif;?>
            </a>
            <?php ?>
        </div>
        
        <?php endforeach;?>
        <?php else:?>
        <div class="rounded" style="margin-top: 16px; background: #FFF; font-size: 14px; height: 100px; line-height: 100px; padding-left: 20px;">
            <?php echo $this->_var['lang']['err_change_attr']; ?>
        </div>
        <?php endif;?>     
        <?php echo $this->fetch('library/ajax_pages.lbi'); ?>
    </div>
</div>
 <script type="text/javascript">
    $(".category_good").ishover("hover");
    $('.category_good').hover(
        function(){
            $(this).find(".simple-description").css("display","block");
        },
        function(){
            $(this).find(".simple-description").css("display","none");
        }
    )
 </script>


