
<?php if ($this->_var['pager']['record_count'] > 0): ?>
<div class="clear"></div>
    <div class="ajax-pages">
        <?php if ($this->_var['pager']['page_prev']): ?><a class="prev" href="<?php if ($this->_var['pager']['jump']): ?><?php echo $this->_var['pager']['jump']; ?><?php else: ?>javascript:void(0);<?php endif; ?>" data-href="<?php echo $this->_var['pager']['page_prev']; ?>" onclick="<?php echo $this->_var['pager']['page_prev']; ?>" title="<?php echo $this->_var['lang']['page_prev']; ?>"><b></b><?php echo $this->_var['lang']['page_prev']; ?></a><?php else: ?><span class="prev"><?php echo $this->_var['lang']['page_prev']; ?></span><?php endif; ?>        
        <?php if ($this->_var['pager']['page_count'] != 1): ?> 
        <?php $_from = $this->_var['pager']['page_number']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?> 
        <?php if ($this->_var['pager']['page'] == $this->_var['key']): ?> 
        <a href="javascript:void(0);" class="current"><?php echo $this->_var['key']; ?></a> 
        <?php else: ?> 
        <a href="<?php if ($this->_var['pager']['jump']): ?><?php echo $this->_var['pager']['jump']; ?> <?php else: ?>javascript:void(0);<?php endif; ?>" data-href="<?php echo $this->_var['item']; ?>" onclick="<?php echo $this->_var['item']; ?>"><?php echo $this->_var['key']; ?></a> 
        <?php endif; ?> 
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
        <?php endif; ?>        
        <?php if ($this->_var['pager']['page_next']): ?><a href="<?php if ($this->_var['pager']['jump']): ?><?php echo $this->_var['pager']['jump']; ?><?php else: ?>javascript:void(0);<?php endif; ?>" data-href="<?php echo $this->_var['pager']['page_next']; ?>" onclick="<?php echo $this->_var['pager']['page_next']; ?>" title="<?php echo $this->_var['lang']['page_next']; ?>" class="next"><b></b><?php echo $this->_var['lang']['page_next']; ?></a><?php else: ?><span class="next"><?php echo $this->_var['lang']['page_next']; ?></span><?php endif; ?> 
        <div class="select-page">
            <label>到第</label>
            <input type="text" id="jump_page"/>
            <label>页</label>
            <a href="javascript:selectPage()">确定</a>
        </div>
    </div>
<div class="clearfix"></div>
 

<script type="text/javascript">   
    function selectPage(){
        var page = $("#jump_page").val();
        if(parseInt(page) > 0){
            <?php echo $this->_var['pager']['url']; ?>(page);          
        }
    }
</script>
<?php endif; ?>
