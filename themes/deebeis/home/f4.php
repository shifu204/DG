<!--楼层风格2开始-->
<div class="floor clearfix">
    <div class="w1190 floorwrap">
        <div class="f_l floor_l floorSty4 height620">
            <div class="brandsTitle">
                <div class="floorName"></div>
                <div class="floorImg"></div>
                <p>
                <span class="span1"><?php echo $this->_var['floors'][$fk]['name'];?></span>
                <span class="span2">Brands</span>
                </p>
            </div>
            <div class="brandsList">
                <ul>
                    <?php if(!empty($this->_var['brands_info'][$fk])):?>
                    <?php foreach ($this->_var['brands_info'][$fk] as $bk=>$bv):?>
                    <?php if($bk < 12):?>
                    <li data-brand="<?php echo $bv['brand_descImg'];?>">
                        <a href='<?php echo build_uri('category', array('cid'=>$fk,'bid' => $bv['brand_id'], $bv['brand_name']));?>'>
                            <img  width="110" height="40" src="<?php echo '/data/brandlogo/'.$bv['brand_logo']?>" data-original="<?php echo ROOT_PATH.'data/brandlogo/'.$bv['brand_logo']?>" alt="<?php echo $bv['brand_name']?>" title="<?php echo $bv['brand_name']?>" />
                        </a>
                    </li>
                    <?php endif;?>
                    <?php endforeach;?>
                    <?php endif; ?>
                </ul>
                <div class="clear"></div>
            </div>
        </div>
        <div class="f_r floor_r floorSty2 clearfix">
            <div class="frwrap">
                <div class="floorNav">
                    <ul>
                        <?php foreach($fv['cat_list'] as $clk=>$clv):?>
                        <li><a><?php echo $clv['name']?></a></li>
                        <?php endforeach;?>
                    </ul>
                </div>
                <?php $echo_ad = true;?>
                <?php foreach($fv['cat_list'] as $clk=>$clv):?>  
                <?php if($echo_ad):?>
                <div class="floorItem floorad floorad1">
                    <div class="f4flexslider" style="border:none;">
                        <ul class="slides">
                            <?php foreach($this->_var['F4ad'] as $hk=>$hv):?>
                            <li>
                                <a href="<?php echo $hv['ad_link'];?>" target="_blank">
                                    <div class="ad_div_img" style="background:url(/data/afficheimg/<?php echo $hv['ad_code'];?>) center no-repeat;"></div>
                                </a>
                            </li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
                <?php endif;?>
                <?php $echo_ad = false;?>
                <div class="floorContent">
                    <?php foreach($clv['goods_list'] as $glk=>$glv):?> 
                    <?php if($glk < 6):?>                   
                        <div class="floorItem floorItem1">
                            <a href="<?php echo $glv['url'];?>" target="_blank">
                                <img class="lazy" src="/images/loading.gif"  
                                     data-original="/<?php 
                                                        if(!empty($glv['goods_index_img'])){
                                                            echo $glv['goods_index_img'];
                                                        } else {
                                                            echo $glv['thumb'];
                                                        }
                                                     ?>" 
                                     title="<?php echo $glv['name'];?>" />
                                <div class="goodsName" title="<?php echo $glv['name'];?>">
                                    <?php echo $glv['name'];?>
                                </div>
                                <div class="goodsPrice">
                                    <?php echo $glv['shop_price'];?>
                                </div>
                            </a>
                        </div>                   
                    <?php endif;?>
                    <?php endforeach;?>
                </div>              
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
<!--楼层风格2结束-->
<?php if(!empty($this->_var['floor_bottom_ad'][$fv['i']-1])):?>
<div class="floorAd">
    <div class="floorAdwrap wm01190">
        <a href="<?php echo $this->_var['floor_bottom_ad'][$fv['i']-1]['ad_link'];?>" target="_blank">
            <img src="/data/afficheimg/<?php echo $this->_var['floor_bottom_ad'][$fv['i']-1]['ad_code'];?>" />
        </a>
    </div>
</div>
<?php else:?>
<div class="floorBlank"></div>
<?php endif; ?>
<script type="text/javascript">
$(".f4flexslider").flexslider({
    animation: "fade",
    slideshow: true,
    directionNav:false,
    animationSpeed:500
});
</script>