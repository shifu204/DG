<!-- $Id: goods_list.htm 17126 2010-04-23 10:30:26Z liuhui $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,../js/jquery.min.js,listtable.js')); ?>

<!-- 商品搜索 -->
<div class="form-div">
  <form action="javascript:searchGoods()" name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />

    <!-- 分类 -->
    <select name="cat_id"><option value="0"><?php echo $this->_var['lang']['goods_cat']; ?></option><?php echo $this->_var['cat_list']; ?></select>
    <!-- 品牌 -->
    <select name="brand_id"><option value="0"><?php echo $this->_var['lang']['goods_brand']; ?></option><?php echo $this->html_options(array('options'=>$this->_var['brand_list'])); ?></select>
    <!-- 关键字 -->
    <?php echo $this->_var['lang']['keyword']; ?> <input type="text" name="keyword" size="15" />
    <select name="goods_type">
            <option value="0">先选择商品类型</option> 
            <?php $_from = $this->_var['goods_type_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'type');if (count($_from)):
    foreach ($_from AS $this->_var['type']):
?>
                <option value="<?php echo $this->_var['type']['cat_id']; ?>"><?php echo $this->_var['type']['cat_name']; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </select>
    <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />   
  </form>
</div>


<script language="JavaScript">
    function searchGoods()
    {
        listTable.filter['cat_id'] = document.forms['searchForm'].elements['cat_id'].value;
        listTable.filter['brand_id'] = document.forms['searchForm'].elements['brand_id'].value;
        listTable.filter['keyword'] = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
        listTable.filter['goods_type'] = document.forms['searchForm'].elements['goods_type'].value;
        listTable.filter['page'] = 1;
        listTable.query = 'attr_query';
        listTable.loadList();
    }
</script>

<!-- 商品列表 -->
<form method="post" action="" name="listForm" onsubmit="return confirmSubmit(this)">
  <!-- start goods list -->
  <div class="list-div" id="listDiv">
<?php endif; ?>
<table cellpadding="3" cellspacing="1">
  <tr>
    <th>
      <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" />
      <a href="javascript:listTable.sort('goods_id'); "><?php echo $this->_var['lang']['record_id']; ?></a><?php echo $this->_var['sort_goods_id']; ?>
    </th>
    <th><a href="javascript:listTable.sort('goods_short_name'); "><?php echo $this->_var['lang']['goods_short_name']; ?></a><?php echo $this->_var['sort_goods_short_name']; ?></th>
    <th><a href="javascript:listTable.sort('goods_name'); "><?php echo $this->_var['lang']['goods_name']; ?></a><?php echo $this->_var['sort_goods_name']; ?></th>
    <th><?php echo $this->_var['lang']['goods_sub_title']; ?></th>
    <th><a href="javascript:listTable.sort('sort_order'); "><?php echo $this->_var['lang']['sort_order']; ?></a><?php echo $this->_var['sort_sort_order']; ?></th>
    <?php if ($this->_var['goods_type']): ?>
        <?php $_from = $this->_var['goods_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'type');if (count($_from)):
    foreach ($_from AS $this->_var['type']):
?>
        <th><?php echo $this->_var['type']['attr_name']; ?></th>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php endif; ?>
  <tr>
  <?php if(!empty($this->_var['goods_list'])):?>
    <?php foreach($this->_var['goods_list'] as $goods):?>
    <tr>
      <td><input type="checkbox" name="checkboxes[]" value="<?php echo $this->_var['goods']['goods_id']; ?>" /><?php echo $goods['goods_id'];?></td>
      <td><span onclick=""><?php echo $goods['goods_short_name'];?></span></td>
      <td><span onclick=""><?php echo $goods['goods_name'];?></span></td>
      <td align="center">
          <div onclick="listTable.edit(this, 'edit_goods_sub_title', '<?php echo $goods['goods_id'];?>')" style="min-width:50px;"><?php echo $goods['goods_sub_title'];?>&nbsp;</div>
      </td>
      <td><?php echo $goods['sort_order'];?></td>
      <?php if(!empty($this->_var['goods_type'])):?>
        <?php foreach($this->_var['goods_type'] as $type):?>
        <?php
            $attr_value = $goods['attrs'][$type['attr_id']]['attr_value'];
            if(empty($attr_value)){
                $attr_value = '<div style="width:30px;">&nbsp;</div>';
            }
            $goods_attr_id = $goods['attrs'][$type['attr_id']]['goods_attr_id'];
            if(empty($goods_attr_id)){
                $goods_attr_id = 0;
            }
        ?>
        <td align="center" style="width: 120px;"><span onclick="listTable.editJquery(this, 'edit_goods_attr', {attr_id:<?php echo $type['attr_id'];?>,goods_id:<?php echo $goods['goods_id'];?>,goods_attr_id:<?php echo $goods_attr_id;?>},1)"><?php echo $attr_value;?></span>           
        </td>
        <?php endforeach;?>
      <?php endif;?>
    </tr>
    <?php endforeach;?>
  <?php else:?>
  <tr><td class="no-records" colspan="11"><?php echo $this->_var['lang']['no_records']; ?></td></tr> 
  <?php endif;?>
</table>
<!-- end goods list -->

<!-- 分页 -->
<table id="page-table" cellspacing="0">
  <tr>
    <td align="right" nowrap="true">
    <?php echo $this->fetch('page.htm'); ?>
    </td>
  </tr>
</table>

<?php if ($this->_var['full_page']): ?>
</div>

</form>

<script type="text/javascript">
  listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
  listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
  listTable.query = 'attr_query';
  <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
  listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
