<!-- $Id: bonus_list.htm 14216 2008-03-10 02:27:21Z testyang $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,../js/transport.js,listtable.js')); ?>
<div class="form-div">
  <form action="javascript:searchCards()" name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    <?php echo htmlspecialchars($this->_var['lang']['card_sn']); ?><input name="card_sn" type="text" id="card_sn" size="15">
    <?php echo htmlspecialchars($this->_var['lang']['order_sn']); ?><input name="order_sn" type="text" id="order_sn" size="15">
    <?php echo $this->_var['lang']['group_name']; ?><input name="group_name" type="text" id="group_name" size="15">
    
    <?php echo $this->_var['lang']['all_status']; ?>
    <select name="status" id="status">
      <option value="-1"><?php echo $this->_var['lang']['select_please']; ?></option>
      <?php echo $this->html_options(array('options'=>$this->_var['status_list'])); ?>
    </select>
    <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
  </form>
</div>

<form method="POST" action="group_card.php?act=batch" name="listForm">
<div class="list-div" id="listDiv">
<?php endif; ?>

  <table cellpadding="3" cellspacing="1">
    <tr>
      <th>
        <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox">
        <a href="javascript:listTable.sort('card_id'); "><?php echo $this->_var['lang']['record_id']; ?></a><?php echo $this->_var['sort_card_id']; ?></th>
      <th><a href="javascript:listTable.sort('card_sn'); "><?php echo $this->_var['lang']['card_sn']; ?></a><?php echo $this->_var['sort_card_sn']; ?></th>
            <th><a href="javascript:listTable.sort('card_password'); "><?php echo $this->_var['lang']['card_password']; ?></a><?php echo $this->_var['sort_card_password']; ?></th>
   <th><a href="javascript:listTable.sort('send_num'); "><?php echo $this->_var['lang']['send_num']; ?></a><?php echo $this->_var['sort_send_num']; ?></th>
      <th><a href="javascript:listTable.sort('group_id'); "><?php echo $this->_var['lang']['group_name']; ?></a><?php echo $this->_var['sort_group_name']; ?></th>
      <th><a href="javascript:listTable.sort('order_sn'); "><?php echo $this->_var['lang']['order_sn']; ?></a><?php echo $this->_var['sort_order_sn']; ?></th>
      <th><a href="javascript:listTable.sort('user_id'); "><?php echo $this->_var['lang']['user_name']; ?></a><?php echo $this->_var['sort_user_id']; ?></th>
     <th><a href="javascript:listTable.sort('end_date'); "><?php echo $this->_var['lang']['end_date']; ?></a><?php echo $this->_var['sort_end_time']; ?></th>
      <th><a href="javascript:listTable.sort('is_used'); "><?php echo $this->_var['lang']['is_used']; ?></a><?php echo $this->_var['is_used']; ?></th>
      <th><?php echo $this->_var['lang']['handler']; ?></th>
    </tr>
    <?php $_from = $this->_var['group_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'group');if (count($_from)):
    foreach ($_from AS $this->_var['group']):
?>
    <tr>
      <td width="60"><span><input value="<?php echo $this->_var['group']['card_id']; ?>" name="checkboxes[]" type="checkbox"><?php echo $this->_var['group']['card_id']; ?></span></td>
      <td width="65"><?php echo $this->_var['group']['card_sn']; ?></td>
      <td width="65"><?php echo $this->_var['group']['card_password']; ?></td>
       <td width="65" align="center"><?php echo $this->_var['group']['send_num']; ?></td>
      <td><?php echo $this->_var['group']['group_name']; ?></td>
      <td align="center" width="95"><?php echo $this->_var['group']['order_sn']; ?></td>
      <td width="120"><?php echo $this->_var['group']['user_name']; ?></td>
      <td width="80" align="center"><?php echo $this->_var['group']['end_date']; ?></td>
      <td align="center" width="60"><?php if ($this->_var['group']['is_used'] == '0'): ?>否<?php else: ?>是<br /><?php echo $this->_var['group']['use_date']; ?><?php endif; ?></td>
      <td align="center" width="60">
    <a href="javascript:;" onclick="send_sms(<?php echo $this->_var['group']['card_id']; ?>)"><?php echo $this->_var['lang']['send_sms']; ?></a>
    &nbsp;&nbsp;<a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['group']['card_id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>', 'remove_card')"><?php echo $this->_var['lang']['remove']; ?></a>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="11"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>

  <table cellpadding="4" cellspacing="0">
    <tr>
      <td><input type="submit" name="drop" id="btnSubmit" value="<?php echo $this->_var['lang']['drop']; ?>" class="button" disabled="true" />
      <?php if ($this->_var['show_mail']): ?><input type="submit" name="mail" id="btnSubmit1" value="<?php echo $this->_var['lang']['send_mail']; ?>" class="button" disabled="true" /><?php endif; ?></td>
      <td align="right"><?php echo $this->fetch('page.htm'); ?></td>
    </tr>
  </table>

<?php if ($this->_var['full_page']): ?>
</div>
<!-- end user_bonus list -->
</form>

<script type="text/javascript" language="JavaScript">
  listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
  listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
  listTable.query = "query_card";

  <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
  listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

  
  function searchCards()
  {
        listTable.filter['card_sn'] = Utils.trim(document.forms['searchForm'].elements['card_sn'].value);
		listTable.filter['order_sn'] = Utils.trim(document.forms['searchForm'].elements['order_sn'].value);
        listTable.filter['group_name'] = Utils.trim(document.forms['searchForm'].elements['group_name'].value);
        listTable.filter['card_status'] = document.forms['searchForm'].elements['status'].value;
        listTable.filter['page'] = 1;
        listTable.loadList();
  }
  
  onload = function()
  {
    // 开始检查订单
    startCheckOrder();
    document.forms['listForm'].reset();
  }
  function send_sms(card_id)
  {
	 if (card_id > 0)
	 { 
  	   Ajax.call('group_card.php?is_ajax=1&act=send_sms', 'card_id=' + card_id, listTable.listCallback, "GET", "JSON");
	 }
  }
  
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>