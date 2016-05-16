<?php echo $this->fetch('library/user_header.lbi'); ?>
<div class="ect-pro-list user-order" style="border-bottom:none;">
    <ul id="J_ItemList">
       <li class="single_item"></li>
       <a href="javascript:;" style="text-align:center" class="get_more"></a>
    </ul>
</div>
</div>
<?php echo $this->fetch('library/search.lbi'); ?>
<?php echo $this->fetch('library/js_files.lbi'); ?>
<footer class="logo"><a href="http://www.ectouch.cn" title="ECTouch官网" target="_blank"><img src="__TPL__/images/copyright.png" width="176" height="60"></a></footer>
<script type="text/javascript">
<?php $_from = $this->_var['lang']['merge_order_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
get_asynclist('index.php?m=default&c=user&a=async_order_list&pay=<?php echo $this->_var['pay']; ?>' , '__TPL__/images/loader.gif');
</script>
<?php echo $this->fetch('library/page_footer.lbi'); ?></body></html>