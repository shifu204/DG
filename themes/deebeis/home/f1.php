<!--导购层开始-->
<div class="daogou wm01190">
    <div class="w1190 clearfix daogouwrap"> 
        <ul>
            <?php foreach($fv['cat_list'] as $ck=>$cv):?>
                <?php if($ck < 6):?>
                <li><a class="a<?php echo $ck+1;?>" href="javascript:void(0);"></a></li>
                <?php endif;?>
            <?php endforeach;?>
        </ul>
    </div>
</div>
<!--导购层结束-->
<!--楼层风格1开始-->
<div class="floor clearfix wm01190">
    <div class="w1190 floorwrap" name="floor1">
        <div class="f_l floor_l floorSty1 height560">
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
                    <?php if($bk < 10):?>
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
        <div class="f_r floor_r clearfix">
            <div class="frwrap pdtop5">
                <?php foreach ($fv['cat_list'] as $clk=>$clv):?>
                <div class="daogouDiv">
                    <?php foreach ($clv['goods_list'] as $glk=>$glv):?>                    
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
                    <?php endforeach;?>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
</div>
<!--楼层风格1结束-->
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
