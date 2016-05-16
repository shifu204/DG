
<?php if ($this->_var['hot_goods']): ?>
<a href="<?php echo $this->_var['hot_goods']['url']; ?>">
    <img src="<?php echo $this->_var['hot_goods']['goods_img']; ?>" alt="<?php echo $this->_var['hot_goods']['name']; ?>">
    <?php if ($this->_var['hot_goods']['is_buy_nine']): ?>
    <img class="buy_more_send_x" src="<?php echo $this->_var['template_dir']; ?>/images/icons/buy_nine_give_two_orange.png"  />
    <?php elseif ($this->_var['hot_goods']['is_buy_six']): ?>
    <img class="buy_more_send_x" src="<?php echo $this->_var['template_dir']; ?>/images/icons/buy_six_give_one_yellow.png"  />
    <?php elseif ($this->_var['hot_goods']['is_total_four']): ?>
    <img class="buy_more_send_x" src="<?php echo $this->_var['template_dir']; ?>/images/icons/total_four_green.png"  />
    <?php endif; ?>
</a>
  <dl>
    <dt>
      <h4 class="title"><a href="<?php echo $this->_var['hot_goods']['url']; ?>"><?php echo $this->_var['hot_goods']['name']; ?></a></h4>
    </dt>
    <dd class="dd-price">
        <span class="pull-left">
            <strong>价格：<b class="ect-colory"><?php if ($this->_var['hot_goods']['promote_price']): ?><?php echo $this->_var['hot_goods']['promote_price']; ?><?php else: ?><?php echo $this->_var['hot_goods']['shop_price']; ?><?php endif; ?></b></strong>
        </span> 
        <span class="ect-pro-price"> 
      <?php if ($this->_var['hot_goods']['promotion']): ?> 
      <?php $_from = $this->_var['hot_goods']['promotion']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'promotion');if (count($_from)):
    foreach ($_from AS $this->_var['promotion']):
?> 
      <?php if ($this->_var['promotion']['type'] == 'group_buy'): ?><i class="label tuan"><?php echo $this->_var['lang']['group_buy_act']; ?></i> 
      <?php elseif ($this->_var['promotion']['act_type'] == 0): ?> <i class="label mz"> <?php echo $this->_var['lang']['favourable_mz']; ?></i> 
      <?php elseif ($this->_var['promotion']['act_type'] == 1): ?> <i class="label mj"> <?php echo $this->_var['lang']['favourable_mj']; ?></i> 
      <?php elseif ($this->_var['promotion']['act_type'] == 2): ?> <i class="label zk"> <?php echo $this->_var['lang']['favourable_zk']; ?></i> 
      <?php endif; ?> 
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
      <?php endif; ?> 
      </span>
    </dd>
    <!--<dd class="dd-num">
        <span class="pull-left<?php if ($this->_var['hot_goods']['mysc'] != 0): ?> ect-colory<?php endif; ?>">
            <i class="fa<?php if ($this->_var['hot_goods']['mysc'] != 0): ?> fa-heart<?php else: ?> fa-heart-o<?php endif; ?>"></i> 
            <?php echo $this->_var['hot_goods']['sc']; ?><?php echo $this->_var['lang']['like_num']; ?>
        </span>
        <span class="pull-right"><?php echo $this->_var['lang']['sort_sales']; ?><?php echo $this->_var['hot_goods']['sales_count']; ?><?php echo $this->_var['lang']['piece']; ?>
        </span> 
    </dd>-->
  </dl>
<?php endif; ?> 
 
