<?php echo $this->fetch('library/user_header.lbi'); ?>
<div class="flow-consignee-list ect-bg-colorf">
  <section>
    <ul id="J_ItemList">
      <li class="ect-padding-tb checkout-add single_item "> </li>
      <a href="javascript:;" style="text-align:center" class="get_more"></a>
    </ul>
  </section>
</div>
<div class="ect-padding-lr ect-padding-tb ect-margin-tb"> <a href="<?php echo url('flow/consignee');?>" type="botton" class="btn btn-info ect-btn-info ect-colorf ect-bg"><?php echo $this->_var['lang']['add_address']; ?></a> </div>
</div>
<?php echo $this->fetch('library/search.lbi'); ?> 
<?php echo $this->fetch('library/js_files.lbi'); ?>
<script type="text/javascript" src="__PUBLIC__/js/jquery.more.js"></script> 
<script type="text/javascript">
get_asynclist('index.php?m=default&c=flow&a=consignee_list' , '__TPL__/images/loader.gif');</script>
<?php echo $this->fetch('library/page_footer.lbi'); ?></body></html>