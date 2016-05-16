<div class="f_l relative navAllCat">
    <a class="all-cat-name" href="javascript:;">全部商品分类</a>
    <div class="all-downlist absolute clearfix">
        <div class="left-area">
            <ul class="left-nav clearfix">
                <?php $_from = $this->_var['all_catlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'top_cat');if (count($_from)):
    foreach ($_from AS $this->_var['top_cat']):
?>
                <li class="top-li clearfix">
                    <a href="javascript:;">
                        <div class="top-nav"><?php echo $this->_var['top_cat']['name']; ?></div>
                        <label>></label>
                    </a>                             
                </li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </div>
        <div class="right-area clearfix">
            <div class="f_l" >
            <?php $_from = $this->_var['all_catlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'top_cat');if (count($_from)):
    foreach ($_from AS $this->_var['top_cat']):
?>
            <div class="right-area-list"style="width: 990px;">
                <?php $_from = $this->_var['top_cat']['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child_cat');$this->_foreach['child_cat_foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['child_cat_foo']['total'] > 0):
    foreach ($_from AS $this->_var['child_cat']):
        $this->_foreach['child_cat_foo']['iteration']++;
?>
                <?php if (($this->_foreach['child_cat_foo']['iteration'] - 1) == 1): ?>
                
                <div class="child-cat-item clearfix">
                    <div class="child-cat-name"><span class="category-icons <?php echo $this->_var['top_cat']['cat_ico']; ?>-brand"></span>品牌</div>                                 
                    <div class="child-cat-list clearfix">
                        <?php $_from = $this->_var['top_cat']['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
                        <a href="category.php?id=<?php echo $this->_var['top_cat']['id']; ?>&brand=<?php echo $this->_var['brand']['brand_id']; ?>"><div class="child-cat-child"><?php echo $this->_var['brand']['brand_name']; ?></div></a>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </div>                                                                   
                </div>
                <?php endif; ?>
                <div class="child-cat-item clearfix">
                    <div class="child-cat-name"><span class="<?php if ($this->_var['child_cat']['cat_ico']): ?>category-icons <?php echo $this->_var['child_cat']['cat_ico']; ?><?php endif; ?>"></span><?php echo $this->_var['child_cat']['name']; ?></div>
                    <div class="child-cat-list clearfix">                                                                     
                        <?php $_from = $this->_var['child_cat']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat_array');if (count($_from)):
    foreach ($_from AS $this->_var['cat_array']):
?>
                        <?php if ($this->_var['child_cat']['cat_tag'] == 'discount_suit'): ?>
                        <a href="category.php?id=<?php echo $this->_var['top_cat']['id']; ?>&filter_id=<?php echo $this->_var['cat_array']['id']; ?>,<?php echo $this->_var['child_cat']['id']; ?>"><div class="child-cat-child"><?php echo $this->_var['cat_array']['name']; ?></div></a>
                        <?php else: ?>
                        <a href="category.php?id=<?php echo $this->_var['top_cat']['id']; ?>&filter_id=<?php echo $this->_var['cat_array']['id']; ?>"><div class="child-cat-child"><?php echo $this->_var['cat_array']['name']; ?></div></a>
                        <?php endif; ?>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </div>                                    
                </div>              
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>                               
            </div>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </div>
            <div class="f_r nav-img-frame">
                <a href="http://nutrilon.deebei.net" target="_blank"><img src="<?php echo $this->_var['theme_path']; ?>images1/nav_zt/nutrilon.jpg" class="nav-img"/></a>
                <a href="http://cowgate.deebei.net" target="_blank"><img src="<?php echo $this->_var['theme_path']; ?>images1/nav_zt/cowgate.jpg" class="nav-img"/></a>
                <a href="http://aptamil.deebei.net" target="_blank"><img src="<?php echo $this->_var['theme_path']; ?>images1/nav_zt/aptamil.jpg" class="nav-img"/></a>
                <a href="http://friso.deebei.net" target="_blank"><img src="<?php echo $this->_var['theme_path']; ?>images1/nav_zt/friso.jpg" class="nav-img"/></a>
                <a href="http://hipp.deebei.net" target="_blank"><img src="<?php echo $this->_var['theme_path']; ?>images1/nav_zt/hipp.jpg" class="nav-img"/></a>
                <a href="http://neocate.deebei.net" target="_blank"><img src="<?php echo $this->_var['theme_path']; ?>images1/nav_zt/neocate.jpg" class="nav-img"/></a>
                <a href="http://www.deebei.net/category.php?id=131&brand=50" target="_blank"><img src="<?php echo $this->_var['theme_path']; ?>images1/nav_zt/aptamil_yg.jpg" class="nav-img"/></a>
            </div>
        </div>
    </div>
</div>
