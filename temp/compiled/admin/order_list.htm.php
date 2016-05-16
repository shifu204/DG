<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/jquery.min.js')); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/transport.js')); ?>
<!-- 订单搜索 -->
<div class="form-div">
  <form action="javascript:searchOrder()" name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    <?php echo $this->_var['lang']['order_sn']; ?><input name="order_sn" type="text" id="order_sn" size="15">
    <?php echo htmlspecialchars($this->_var['lang']['consignee']); ?><input name="consignee" type="text" id="consignee" size="15">
    <?php echo $this->_var['lang']['all_status']; ?>
    <select name="status" id="status">
      <?php echo $this->html_options(array('options'=>$this->_var['status_list'])); ?>
      <option value="-1" selected="selected"><?php echo $this->_var['lang']['select_please']; ?></option>
    </select>
    <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    <a href="order.php?act=list&composite_status=<?php echo $this->_var['os_unconfirmed']; ?>"><?php echo $this->_var['lang']['cs'][$this->_var['os_unconfirmed']]; ?></a>
    <a href="order.php?act=list&composite_status=<?php echo $this->_var['cs_await_pay']; ?>"><?php echo $this->_var['lang']['cs'][$this->_var['cs_await_pay']]; ?></a>
    <a href="order.php?act=list&composite_status=<?php echo $this->_var['cs_await_ship']; ?>"><?php echo $this->_var['lang']['cs'][$this->_var['cs_await_ship']]; ?></a>
  </form>
</div>
<!-- 订单列表 -->
<form method="post" action="order.php?act=operate" name="listForm" onsubmit="return check()">
  <div class="list-div" id="listDiv">
<?php endif; ?>

<table class="re-list-table">
  <tr>
    <th class="align-left">
      <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" /><a href="javascript:listTable.sort('order_sn', 'DESC'); "><?php echo $this->_var['lang']['order_sn']; ?></a><?php echo $this->_var['sort_order_sn']; ?>
    </th>
    <th><a href="javascript:listTable.sort('add_time', 'DESC'); "><?php echo $this->_var['lang']['order_time']; ?></a><?php echo $this->_var['sort_order_time']; ?></th>
    <th><a href="javascript:listTable.sort('consignee', 'DESC'); "><?php echo $this->_var['lang']['consignee']; ?></a><?php echo $this->_var['sort_consignee']; ?> （历史订单数）</th>
    <th><a href="javascript:listTable.sort('total_fee', 'DESC'); "><?php echo $this->_var['lang']['total_fee']; ?></a><?php echo $this->_var['sort_total_fee']; ?></th>
    <th><a href="javascript:listTable.sort('order_amount', 'DESC'); "><?php echo $this->_var['lang']['order_amount']; ?></a><?php echo $this->_var['sort_order_amount']; ?></th>
    <th><?php echo $this->_var['lang']['all_status']; ?></th>
    <th><?php echo $this->_var['lang']['handler']; ?></th>
  <tr>
  
  <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('okey', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['okey'] => $this->_var['order']):
?>
    <tr <?php if ($this->_var['order']['order_status'] == 2 || $this->_var['order']['order_status'] == 3): ?> class="order_canceled"<?php endif; ?>>
      <td valign="top" nowrap="nowrap"><input type="checkbox" name="checkboxes" value="<?php echo $this->_var['order']['order_sn']; ?>" /><a href="order.php?act=info&order_id=<?php echo $this->_var['order']['order_id']; ?>" id="order_<?php echo $this->_var['okey']; ?>"><?php echo $this->_var['order']['order_sn']; ?><?php if ($this->_var['order']['extension_code'] == "group_buy"): ?><br /><div align="center"><?php echo $this->_var['lang']['group_buy']; ?></div><?php elseif ($this->_var['order']['extension_code'] == "exchange_goods"): ?><br /><div align="center"><?php echo $this->_var['lang']['exchange_goods']; ?></div><?php endif; ?></a>
          <?php if ($this->_var['order']['is_mobile']): ?><br /><div style="padding:3px 0 0 20px;"><img src="images/icon_mobile_order.png" alt="手机订单" /></div><?php endif; ?>
          <?php if ($this->_var['order']['search_keyword']): ?><br /><div style="color:red;"><?php echo $this->_var['order']['search_keyword']; ?></div><?php endif; ?>
      </td>
    <td>
        <?php if ($this->_var['order']['user_type']): ?><img src="/images/login/login<?php echo $this->_var['order']['user_type']; ?>.png" /><?php endif; ?>
        <?php if ($this->_var['order']['user_id'] > 0): ?><a href="users.php?act=edit&id=<?php echo $this->_var['order']['user_id']; ?>"><?php echo htmlspecialchars($this->_var['order']['buyer']); ?></a><?php else: ?><?php echo htmlspecialchars($this->_var['order']['buyer']); ?><?php endif; ?>
        <br /><?php echo $this->_var['order']['short_order_time']; ?></td>
    <td align="left" valign="top">
        <?php if ($this->_var['order']['count_history_order'] > 0 && $this->_var['order']['user_id'] > 0): ?><a href="order.php?act=list&user_id=<?php echo $this->_var['order']['user_id']; ?>"><?php echo htmlspecialchars($this->_var['order']['consignee']); ?> (<?php echo $this->_var['order']['count_history_order']; ?>)</a>
        <?php else: ?><?php echo htmlspecialchars($this->_var['order']['consignee']); ?><?php endif; ?>
        <?php if ($this->_var['order']['tel']): ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['order']['tel']); ?><?php endif; ?> 
        <?php if ($this->_var['order']['pay_note'] == '使用一听试购，货到付款'): ?>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red"><?php echo htmlspecialchars($this->_var['order']['pay_note']); ?></span><?php endif; ?>
        <br /><?php echo $this->_var['order']['region']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['order']['address']); ?>
    </td>
    <td align="right" valign="top" nowrap="nowrap"><?php echo $this->_var['order']['formated_total_fee']; ?></td>
    <td align="right" valign="top" nowrap="nowrap"><?php echo $this->_var['order']['formated_order_amount']; ?></td>
    <td align="left" valign="top" nowrap="nowrap">
        <?php echo $this->_var['lang']['os'][$this->_var['order']['order_status']]; ?>, <?php echo $this->_var['lang']['ps'][$this->_var['order']['pay_status']]; ?>,
        <?php if ($this->_var['lang']['ss'] [ $this->_var['order']['shipping_status'] ] == '发货中'): ?>
        <span style="color:#F00;"><?php echo $this->_var['lang']['ss'][$this->_var['order']['shipping_status']]; ?></span> 
        <?php else: ?>
        <?php echo $this->_var['lang']['ss'][$this->_var['order']['shipping_status']]; ?>
        <?php endif; ?>
    <?php if ($this->_var['order']['order_status'] != 0 && $this->_var['order']['order_status'] != 2 && $this->_var['order']['order_status'] != 3 && $this->_var['order']['order_status'] != 4 && $this->_var['order']['pay_status'] == 0 && $this->_var['order']['pay_id'] != 3): ?>
        <span id="pay_remind_<?php echo $this->_var['order']['order_id']; ?>">
        <!--<a style="color:#F00;" title="尊敬的客户<?php echo $this->_var['order']['consignee']; ?>：你有一笔单号为 <?php echo $this->_var['order']['order_sn']; ?> 的订单还没支付,我们将根据支付顺序优先安排发货,请尽快完成支付,谢谢！www.deebei.net" href="javascript:void(0);" onclick="paySMS(<?php echo $this->_var['order']['order_id']; ?>, '<?php echo $this->_var['order']['tel']; ?>', this.title ); return false;">提醒付款</a>-->
        <a style="color:#F00;" href="javascript:void(0);" onclick="paySMS('<?php echo $this->_var['order']['tel']; ?>',1,<?php echo $this->_var['order']['order_id']; ?>)">提醒付款</a>
        <?php if ($this->_var['order']['is_remind_pay']): ?>
        <em>已提醒</em>
        <?php endif; ?>
        </span>
    <?php endif; ?>
    <?php if ($this->_var['order']['shipping_status'] > 0 && $this->_var['order']['un_invoice_no']): ?>
        <br />未发货单:
        <?php $_from = $this->_var['order']['un_invoice_no']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'no');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['no']):
        $this->_foreach['no']['iteration']++;
?>
        <span style="color:#F00"><?php echo $this->_var['no']['invoice_no']; ?></span> <a href="order.php?act=delivery_info&delivery_id=<?php echo $this->_var['no']['delivery_id']; ?>">去发货</a>
        <?php if (! ($this->_foreach['no']['iteration'] == $this->_foreach['no']['total'])): ?><br /><?php endif; ?>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php endif; ?>
    <?php if ($this->_var['order']['shipping_status'] > 0 && $this->_var['order']['invoice_no']): ?>
        <br />发货单:<span style="color:#0FA326"><?php echo $this->_var['order']['invoice_no']; ?></span>
        <span id="remind_<?php echo $this->_var['order']['order_id']; ?>">
        <?php if ($this->_var['order']['is_remind_express']): ?>
        已提醒发货
        <?php else: ?>
        <!--<a title="尊敬的客户<?php echo $this->_var['order']['consignee']; ?>，您选购的商品已发出，<?php echo $this->_var['order']['shipping_name']; ?>单号（<?php echo $this->_var['order']['invoice_no']; ?>）感谢您的支持，祝您生活愉快,宝宝健康" href="javascript:void(0);" onclick="expressSMS(<?php echo $this->_var['order']['order_id']; ?>, '<?php echo $this->_var['order']['tel']; ?>', this.title ); return false;">提醒发货</a>-->
        <a title="亲爱的用户，“德贝小箱”已载满商品，快马加鞭地朝您所在地狂奔而去了；好的话记得晒单哟！<?php echo $this->_var['order']['shipping_name']; ?>单号：<?php echo $this->_var['order']['invoice_no']; ?>" href="javascript:void(0)" onclick="expressSMS(<?php echo $this->_var['order']['order_id']; ?>,'<?php echo $this->_var['order']['tel']; ?>',this.title); return false;">提醒发货</a>
        <?php endif; ?>
        </span>
    <?php endif; ?>
    </td>
    <td align="center" valign="top"  nowrap="nowrap">
     <a href="order.php?act=info&order_id=<?php echo $this->_var['order']['order_id']; ?>"><?php echo $this->_var['lang']['detail']; ?></a>
     <a href="trace.php?act=order_trace&order_id=<?php echo $this->_var['order']['order_id']; ?>" target="_blank">下单跟踪</a>
     <?php if ($this->_var['order']['can_remove']): ?>
     <br /><a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['order']['order_id']; ?>, remove_confirm, 'remove_order')"><?php echo $this->_var['lang']['remove']; ?></a>
     <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
<script type="text/javascript">
    
    function paySMS(mobile,template,order_id){
        $.ajax({ 
            type:"post",
            url:'/api/sms.php?act=paysms&type=pay&mobile='+mobile+'&template='+template+'&order_id='+order_id,
            dataType:"text",
            success:function(data){
                if(data == "0"){
                    document.getElementById("pay_remind_"+order_id).innerHTML = '已提醒付款';
                }else{
                    alert("付款提醒发送失败:"+data);
                }
            }
        }); 
    };
    
    function expressSMS(order_id,mobile,content){
        $.ajax({
           type:"post",
           url:"/api/sms.php?act=expressSMS&type=express&order_id="+order_id+"&mobile="+mobile+"&content="+content+"",
           dataType:"text",
           success:function(data){
               if(data == "0"){
                    document.getElementById("remind_" + order_id).innerHTML = '已提醒发货';
                }else{
                    alert("发货提醒发送失败："+data);
                }
           }
        });
    };
    
    
</script>

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
  <div>
    <input name="confirm" type="submit" id="btnSubmit" value="<?php echo $this->_var['lang']['op_confirm']; ?>" class="button" disabled="true" onclick="this.form.target = '_self'" />
    <input name="invalid" type="submit" id="btnSubmit1" value="<?php echo $this->_var['lang']['op_invalid']; ?>" class="button" disabled="true" onclick="this.form.target = '_self'" />
    <input name="cancel" type="submit" id="btnSubmit2" value="<?php echo $this->_var['lang']['op_cancel']; ?>" class="button" disabled="true" onclick="this.form.target = '_self'" />
    <input name="remove" type="submit" id="btnSubmit3" value="<?php echo $this->_var['lang']['remove']; ?>" class="button" disabled="true" onclick="this.form.target = '_self'" />
    <input name="print" type="submit" id="btnSubmit4" value="<?php echo $this->_var['lang']['print_order']; ?>" class="button" disabled="true" onclick="this.form.target = '_blank'" />
    <input name="batch" type="hidden" value="1" />
    <input name="order_id" type="hidden" value="" />
  </div>
</form>
<script language="JavaScript">
listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>


    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
    }

    /**
     * 搜索订单
     */
    function searchOrder()
    {
        listTable.filter['order_sn'] = Utils.trim(document.forms['searchForm'].elements['order_sn'].value);
        listTable.filter['consignee'] = Utils.trim(document.forms['searchForm'].elements['consignee'].value);
        listTable.filter['composite_status'] = document.forms['searchForm'].elements['status'].value;
        listTable.filter['page'] = 1;
        listTable.loadList();
    }

    function check()
    {
      var snArray = new Array();
      var eles = document.forms['listForm'].elements;
      for (var i=0; i<eles.length; i++)
      {
        if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on')
        {
          snArray.push(eles[i].value);
        }
      }
      if (snArray.length == 0)
      {
        return false;
      }
      else
      {
        eles['order_id'].value = snArray.toString();
        return true;
      }
    }
    /**
     * 显示订单商品及缩图
     */
    var show_goods_layer = 'order_goods_layer';
    var goods_hash_table = new Object;
    var timer = new Object;

    /**
     * 绑定订单号事件
     *
     * @return void
     */
    function bind_order_event()
    {
        var order_seq = 0;
        while(true)
        {
            var order_sn = Utils.$('order_'+order_seq);
            if (order_sn)
            {
                order_sn.onmouseover = function(e)
                {
                    try
                    {
                        window.clearTimeout(timer);
                    }
                    catch(e)
                    {
                    }
                    var order_id = Utils.request(this.href, 'order_id');
                    show_order_goods(e, order_id, show_goods_layer);
                }
                order_sn.onmouseout = function(e)
                {
                    hide_order_goods(show_goods_layer)
                }
                order_seq++;
            }
            else
            {
                break;
            }
        }
    }
    listTable.listCallback = function(result, txt) 
    {
        if (result.error > 0) 
        {
            alert(result.message);
        }
        else 
        {
            try 
            {
                document.getElementById('listDiv').innerHTML = result.content;
                bind_order_event();
                if (typeof result.filter == "object") 
                {
                    listTable.filter = result.filter;
                }
                listTable.pageCount = result.page_count;
            }
            catch(e)
            {
                alert(e.message);
            }
        }
    }
    /**
     * 浏览器兼容式绑定Onload事件
     *
     */
    if (Browser.isIE)
    {
        window.attachEvent("onload", bind_order_event);
    }
    else
    {
        window.addEventListener("load", bind_order_event, false);
    }

    /**
     * 建立订单商品显示层
     *
     * @return void
     */
    function create_goods_layer(id)
    {
        if (!Utils.$(id))
        {
            var n_div = document.createElement('DIV');
            n_div.id = id;
            n_div.className = 'order-goods';
            document.body.appendChild(n_div);
            Utils.$(id).onmouseover = function()
            {
                window.clearTimeout(window.timer);
            }
            Utils.$(id).onmouseout = function()
            {
                hide_order_goods(id);
            }
        }
        else
        {
            Utils.$(id).style.display = '';
        }
    }

    /**
     * 显示订单商品数据
     *
     * @return void
     */
    function show_order_goods(e, order_id, layer_id)
    {
        create_goods_layer(layer_id);
        $layer_id = Utils.$(layer_id);
        $layer_id.style.top = (Utils.y(e) + 12) + 'px';
        $layer_id.style.left = (Utils.x(e) + 12) + 'px';
        if (typeof(goods_hash_table[order_id]) == 'object')
        {
            response_goods_info(goods_hash_table[order_id]);
        }
        else
        {
            $layer_id.innerHTML = loading;
            Ajax.call('order.php?is_ajax=1&act=get_goods_info&order_id='+order_id, '', response_goods_info , 'POST', 'JSON');
        }
    }

    /**
     * 隐藏订单商品
     *
     * @return void
     */
    function hide_order_goods(layer_id)
    {
        $layer_id = Utils.$(layer_id);
        window.timer = window.setTimeout('$layer_id.style.display = "none"', 500);
    }

    /**
     * 处理订单商品的Callback
     *
     * @return void
     */
    function response_goods_info(result)
    {
        if (result.error > 0)
        {
            alert(result.message);
            hide_order_goods(show_goods_layer);
            return;
        }
        if (typeof(goods_hash_table[result.content[0].order_id]) == 'undefined')
        {
            goods_hash_table[result.content[0].order_id] = result;
        }
        Utils.$(show_goods_layer).innerHTML = result.content[0].str;
    }
</script>


<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
