<!-- $Id: group_buy_list.htm 14216 2008-03-10 02:27:21Z testyang $ -->



<?php if ($this->_var['full_page']): ?>

<?php echo $this->fetch('pageheader.htm'); ?>

<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,../js/transport.js,../js/region.js')); ?>

<script type="text/javascript" src="../js/calendar.php?lang=<?php echo $this->_var['cfg_lang']; ?>"></script>

<link href="../js/calendar/calendar.css" rel="stylesheet" type="text/css" />



<div class="form-div" style="padding:20px;">

<form action="javascript:searchGroup()" name="searchForm">

<?php echo $this->_var['lang']['label_group_name']; ?> 

<input type="text" name="keyword" size="40" />&nbsp;&nbsp;&nbsp;&nbsp;

<select name="suppliers_id">

<option value="0"><?php echo $this->_var['lang']['select_suppliers']; ?></option>

<?php echo $this->html_options(array('options'=>$this->_var['suppliers_list_name'],'selected'=>$_GET['suppliers_id'])); ?>

</select>&nbsp;&nbsp;&nbsp;&nbsp;

<select name="group_stat"><option value="-1" selected="selected"><?php echo $this->_var['lang']['select_stat']; ?></option>

<!--<?php $_from = $this->_var['lang']['gbs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('stat_id', 'stat');if (count($_from)):
    foreach ($_from AS $this->_var['stat_id'] => $this->_var['stat']):
?>-->

<option value="<?php echo $this->_var['stat_id']; ?>"><?php echo $this->_var['stat']; ?></option>

<!--<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>-->

</select>&nbsp;&nbsp;&nbsp;&nbsp;

<select name="cat_id"><option value="0"><?php echo $this->_var['lang']['goods_cat']; ?></option><?php echo $this->_var['cat_list']; ?></select>&nbsp;&nbsp;&nbsp;&nbsp;

<select name="city_id" style="border:1px solid #ccc;">

<option value="0"><?php echo $this->_var['lang']['please_select_city']; ?></option>

<!-- <?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?> -->

<option value="<?php echo $this->_var['city']['city_id']; ?>"><?php echo $this->_var['city']['city_name']; ?></option>

<!-- <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> -->

</select>

<br /><br />

<?php echo $this->_var['lang']['label_start_date']; ?><input name="start_time" type="text" id="start_time" size="22" value='<?php echo $this->_var['group_buy']['formated_start_date']; ?>' readonly="readonly" /><input name="selbtn1" type="button" id="selbtn1" onclick="return showCalendar('start_time', '%Y-%m-%d %H:%M', '24', false, 'selbtn1');" value="<?php echo $this->_var['lang']['btn_select']; ?>" class="button"/>&nbsp;&nbsp;&nbsp;&nbsp;

<?php echo $this->_var['lang']['label_end_date']; ?><input name="end_time" type="text" id="end_time" size="22" value='<?php echo $this->_var['group_buy']['formated_end_date']; ?>' readonly="readonly" /><input name="selbtn2" type="button" id="selbtn2" onclick="return showCalendar('end_time', '%Y-%m-%d %H:%M', '24', false, 'selbtn2');" value="<?php echo $this->_var['lang']['btn_select']; ?>" class="button"/>

<div style="padding-top:20px;"><input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" /></div>

</form>

</div>

<form method="post" action="group_buy.php?act=batch_drop" name="listForm" onsubmit="return confirm(batch_drop_confirm);">

<!-- start group_buy list -->

<div class="list-div" id="listDiv">

<?php endif; ?>



  <table cellpadding="3" cellspacing="1">

    <tr>

      <th style="width:50px;text-align:left;padding:0 10px;">

        <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" />

        <a href="javascript:listTable.sort('group_id'); "><?php echo $this->_var['lang']['record_id']; ?></a><?php echo $this->_var['sort_act_id']; ?>

      </th>

      <th><?php echo $this->_var['lang']['group_name']; ?></th>

      <th><?php echo $this->_var['lang']['current_status']; ?></th>

      <th><a href="javascript:listTable.sort('start_time'); "><?php echo $this->_var['lang']['start_date']; ?></a><?php echo $this->_var['sort_start_time']; ?></th>

      <th><a href="javascript:listTable.sort('end_time'); "><?php echo $this->_var['lang']['end_date']; ?></a><?php echo $this->_var['sort_end_time']; ?></th>

      <th><?php echo $this->_var['lang']['upper_orders']; ?></th>

      <!-- <th><a href="javascript:listTable.sort('gift_integral'); "><?php echo $this->_var['lang']['gift_integral']; ?></a><?php echo $this->_var['sort_gift_integral']; ?></th> -->

      <th align="left" style="padding-left:10px"><?php echo $this->_var['lang']['label_order_qty']; ?></th>

      <th><?php echo $this->_var['lang']['current_price']; ?></th>

      <th align="left" style="padding-left:10px;"><?php echo $this->_var['lang']['label_goods_qty']; ?></th>

      <th><?php echo $this->_var['lang']['handler']; ?></th>

    </tr>



    <?php $_from = $this->_var['group_buy_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'group_buy');if (count($_from)):
    foreach ($_from AS $this->_var['group_buy']):
?>

    <tr>

      <td><input value="<?php echo $this->_var['group_buy']['group_id']; ?>" name="checkboxes[]" type="checkbox"><?php echo $this->_var['group_buy']['group_id']; ?></td>

      <td><a href="../index.php?id=<?php echo $this->_var['group_buy']['group_id']; ?>" target="_blank"><?php echo htmlspecialchars($this->_var['group_buy']['group_name']); ?></a></td>

      <td width="54" align="center" style="color:#3A6EA5;"><?php echo $this->_var['group_buy']['cur_status']; ?></font></td>

      <td align="center" width="70"><?php echo $this->_var['group_buy']['start_time']; ?></td>

      <td align="center" width="70"><?php echo $this->_var['group_buy']['end_time']; ?></td>

      <td align="center" width="30"><?php echo $this->_var['group_buy']['upper_orders']; ?></td>

      <!-- <td align="right"><?php echo $this->_var['group_buy']['gift_integral']; ?></td> -->

      <td align="left" width="120">订单：<?php echo $this->_var['group_buy']['actual_order']; ?> / <?php echo $this->_var['group_buy']['total_order']; ?><br />商品：<?php echo $this->_var['group_buy']['actual_goods']; ?> / <?php echo $this->_var['group_buy']['total_goods']; ?></td>

      <td align="center" width="40"><?php echo $this->_var['group_buy']['cur_price']; ?></td>

      <td align="left" width="100">余额：<?php echo $this->_var['group_buy']['actual_surplus']; ?><br />红包：<?php echo $this->_var['group_buy']['actual_bonus']; ?><br />现金：<?php echo $this->_var['group_buy']['actual_money']; ?><br />总计：<?php echo $this->_var['group_buy']['actual_amount']; ?></td>

      <td align="right" width="36">

<a href="../index.php?id=<?php echo $this->_var['group_buy']['group_id']; ?>" target="_blank"><img src="images/icon_view.gif" title="<?php echo $this->_var['lang']['view_group']; ?>" border="0" height="16" width="16" /></a>

<a href="group_buy.php?act=edit&amp;id=<?php echo $this->_var['group_buy']['group_id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><img src="images/icon_edit.gif" border="0" height="16" width="16" /></a>

<a href="order.php?act=list&amp;group_buy_id=<?php echo $this->_var['group_buy']['group_id']; ?>"><img src="images/icon_view_d.gif" title="<?php echo $this->_var['lang']['view_order']; ?>" border="0" height="16" width="16" /></a>

<a href="order.php?act=get_excel_order&amp;group_buy_id=<?php echo $this->_var['group_buy']['group_id']; ?>"><img src="images/icon_down_d.gif" title="<?php echo $this->_var['lang']['down_order']; ?>" border="0" height="16" width="16" /></a>

<!--<?php if ($this->_var['group_buy']['goods_type'] == '1'): ?>-->

<a href="group_card.php?act=list&amp;group_id=<?php echo $this->_var['group_buy']['group_id']; ?>"><img src="images/icon_view_j.gif" title="<?php echo $this->_var['lang']['view_card']; ?>" border="0" height="16" width="16" /></a>

<a href="group_buy.php?act=get_excel&amp;group_id=<?php echo $this->_var['group_buy']['group_id']; ?>"><img src="images/icon_down_j.gif" title="<?php echo $this->_var['lang']['down_card']; ?>" border="0" height="16" width="16" /></a>

<!--<?php endif; ?>-->

<a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['group_buy']['group_id']; ?>,'<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['remove']; ?>"><img src="images/icon_drop.gif" border="0" height="16" width="16" /></a>

      </td>

    </tr>

    <?php endforeach; else: ?>

    <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_records']; ?></td></tr>

    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>

  </table>



  <table cellpadding="4" cellspacing="0">

    <tr>

      <td><input type="submit" name="drop" id="btnSubmit" value="<?php echo $this->_var['lang']['drop']; ?>" class="button" disabled="true" /></td>

      <td align="right"><?php echo $this->fetch('page.htm'); ?></td>

    </tr>

  </table>



<?php if ($this->_var['full_page']): ?>

</div>

<!-- end group_buy list -->

</form>



<script type="text/javascript" language="JavaScript">

  region.isAdmin = true;

  listTable.recordCount = <?php echo $this->_var['record_count']; ?>;

  listTable.pageCount = <?php echo $this->_var['page_count']; ?>;



  <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>

  listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';

  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>



  

  onload = function()

  {

    document.forms['searchForm'].elements['keyword'].focus();



    startCheckOrder();

  }



  /**

   * 搜索团购活动

   */

    function searchGroup()

    {

        listTable.filter['cat_id'] = document.forms['searchForm'].elements['cat_id'].value;

        listTable.filter['city_id'] = document.forms['searchForm'].elements['city_id'].value;

		listTable.filter['start_time'] = document.forms['searchForm'].elements['start_time'].value;

		listTable.filter['end_time'] = document.forms['searchForm'].elements['end_time'].value;

        listTable.filter['suppliers_id'] = document.forms['searchForm'].elements['suppliers_id'].value;

		listTable.filter['group_stat'] = document.forms['searchForm'].elements['group_stat'].value;

        listTable.filter['keyword'] = Utils.trim(document.forms['searchForm'].elements['keyword'].value);

        listTable.filter['page'] = 1;



        listTable.loadList();

    }

  

</script>

<?php echo $this->fetch('pagefooter.htm'); ?>

<?php endif; ?>

