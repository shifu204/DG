<?php echo $this->fetch('library/page_header.lbi'); ?> 
<?php echo $this->fetch('library/ur_here.lbi'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_var['theme_path']; ?>/category/common_cat.css" />
<script type="text/javascript" src="<?php echo $this->_var['theme_path']; ?>js/category.js"></script>
<input type="hidden" id="category" value="<?php echo $this->_var['category']; ?>" name="category" />
<div>
    <div id="left-side-buoy"></div>
    <div class="wm01190 clearfix filter-wrap" id="filter_area">
        <?php if ($this->_var['cat_nav']['id']): ?>
            <?php $_from = $this->_var['cat_nav']['id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'filter');if (count($_from)):
    foreach ($_from AS $this->_var['filter']):
?>
            <div class="cate_nav_item clearfix">
                <table class="filter-table" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="item-left"><?php echo $this->_var['filter']['cat_name']; ?></td>
                            <!--<td class="nav_all <?php if (! $this->_var['filt']['selected_filter'] [ $this->_var['filter']['cat_id'] ]): ?> nav_all_selected <?php endif; ?>" cat-id="<?php echo $this->_var['filter']['cat_id']; ?>"><a>全部</a></td>-->
                            <td class="item-right">
                                <div class="nav_all <?php if (! $this->_var['filt']['selected_filter'] [ $this->_var['filter']['cat_id'] ]): ?> nav_all_selected <?php endif; ?>" cat-id="<?php echo $this->_var['filter']['cat_id']; ?>"><a>全部</a></div>
                                <ul class="clearfix">
                                    <?php if ($this->_var['filter']['children']): ?>
                                        <?php $_from = $this->_var['filter']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'children');if (count($_from)):
    foreach ($_from AS $this->_var['children']):
?>
                                        <li data-value="<?php echo $this->_var['children']['id']; ?>" cat-id="<?php echo $this->_var['filter']['cat_id']; ?>" <?php if (in_array ( $this->_var['children']['id'] , $this->_var['filt']['selected_filter'] [ $this->_var['filter']['cat_id'] ] )): ?> class='selected' <?php endif; ?>>
                                            <a href="javascript:;"><?php echo $this->_var['children']['name']; ?></a>
                                        </li>
                                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <?php endif; ?>
        <?php if ($this->_var['cat_nav']['brand']): ?>
        <div class="cate_nav_item clearfix" data-name="brand_id">
            <table class="filter-table" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td class="item-left">品牌</td>
                        <!--<td class="nav_all <?php if (! $this->_var['filt']['brand_id']): ?> nav_all_selected <?php endif; ?>" data-value="0"><a>全部</a></td>-->
                        <td class="item-right">
                            <div class="nav_all <?php if (! $this->_var['filt']['brand_id']): ?> nav_all_selected <?php endif; ?>" data-value="0"><a>全部</a></div>
                            <ul class="clearfix">
                                <?php $_from = $this->_var['cat_nav']['brand']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'brand');if (count($_from)):
    foreach ($_from AS $this->_var['brand']):
?>
                                <li data-value="<?php echo $this->_var['brand']['brand_id']; ?>" <?php if ($this->_var['filt']['brand_id'] == $this->_var['brand']['brand_id']): ?>class='selected'<?php endif; ?>>
                                    <a href="javascript:;"><?php echo $this->_var['brand']['brand_name']; ?></a>
                                </li>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            </ul>
                            <div class="more">更多品牌<span class="icon-arrow icon-arrow-down"></span></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<div>
    <div class="filter-bar clearfix" id="filter_bar">
        <div class="clearfix f_l">
            <a  href="javascript:;" class="sort-order selected-order" data-value="default" order="asc">默认<span class="arrow-up"></span></a>
            <a  href="javascript:;" class="sort-order" data-value="sales" order="desc">销量<span class="arrow-down"></span></a>
            <a  href="javascript:;" class="sort-order" data-value="views" order="desc">人气<span class="arrow-down"></span></a>
            <a  href="javascript:;" class="sort-order" data-value="rank" order="desc">信用<span class="arrow-down"></span></a>
            <a  href="javascript:;" class="sort-order" data-value="price" order="desc">价格<span class="arrow-down"></span></a>
            <a class="goods-price">
                <input class="price-input" id="price_min" type="text"/> - 
                <input class="price-input" id="price_max" type="text"/>
                <input class="sure-btn" type="button" value="确定" onclick="getData();"/>
            </a>
        </div>
        <!--<div class="label-area f_l clearfix">
            <a class="filter-label">
                保险承保
            </a>
            <a class="filter-label">
                顺丰包邮
            </a>
            <a class="filter-label">
                支持货到付款
            </a>
            <a class="filter-label">
                买九送二
            </a>
            <a class="filter-label">
                买六送一
            </a>
        </div> -->
        <div class="view-area">
            <a></a>
            <a></a>
        </div>
        <div id="filter_bar_pager">
        <?php echo $this->fetch('category/library/category_filter_bar_page.lbi'); ?>
        </div>
    </div>
</div>
<div>
    <div class="wm01190">
        <?php echo $this->fetch('library/goods/category_goods_list.lbi'); ?>
    </div>
</div>
<?php echo $this->fetch('library/index/index_left_nav.lbi'); ?>
<script type="text/javascript">
    $(".category_good").ishover("hover");
    $.categoryNav({jump:false});
    $(".filter-bar").GoodsFilterBar();
    $(document).ready(function(){
        window.onkeydown=function(e){
            if(e.keyCode == 13){
               e.preventDefault();
               var jump_page = parseInt($("#jump_page").val());
               if(jump_page > 0 ){
                   selectPage();
               } else {                  
                   getData();
               }             
            }
       };
    });
</script>
<?php echo $this->fetch('library/page_footer.lbi'); ?></body></html>