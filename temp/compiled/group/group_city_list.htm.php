<!-- $Id: brand_list.htm 15898 2009-05-04 07:25:41Z liuhui $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,../js/transport.js')); ?>
<!-- 品牌搜索 -->
<div class="form-div">
  <form action="javascript:search_city()" name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    <?php echo $this->_var['lang']['keyword']; ?> <input type="text" name="city_name" size="15" />
    <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
  </form>
</div>
<form method="post" action="" name="listForm">
<!-- start brand list -->
<div class="list-div" id="listDiv">
<?php endif; ?>

  <table cellpadding="3" cellspacing="1">
    <tr>
      <th><?php echo $this->_var['lang']['city_name']; ?></th>
      <th><?php echo $this->_var['lang']['city_desc']; ?></th>
      <th><?php echo $this->_var['lang']['is_open']; ?></th>
      <th><?php echo $this->_var['lang']['is_select']; ?></th>
      <th><a href="javascript:listTable.sort('city_sort'); "><?php echo $this->_var['lang']['group_sort']; ?></a></th>
      <th><?php echo $this->_var['lang']['handler']; ?></th>
    </tr>
    <?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?>
    <tr>
      <td class="first-cell">
        <?php echo htmlspecialchars($this->_var['city']['city_name']); ?></span>
      </td>
      <td align="left"><?php echo sub_str($this->_var['city']['city_desc'],36); ?></td>
      <td align="center"><img src="images/<?php if ($this->_var['city']['is_open']): ?>yes<?php else: ?>no<?php endif; ?>.gif" onclick="listTable.toggle(this, 'toggle_open', <?php echo $this->_var['city']['city_id']; ?>)" /></td>
         <td align="center">
         <img src="images/<?php if ($this->_var['city']['city_id'] == $this->_var['city_id']): ?>yes<?php else: ?>no<?php endif; ?>.gif" onclick="select_city(<?php echo $this->_var['city']['city_id']; ?>);" /></td>
             <td align="center"><span onclick="listTable.edit(this, 'edit_sort_order', <?php echo $this->_var['city']['city_id']; ?>)"><?php echo $this->_var['city']['city_sort']; ?></span></td>
      <td align="center">
        <a href="group_city.php?act=edit&id=<?php echo $this->_var['city']['city_id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><?php echo $this->_var['lang']['edit']; ?></a> |
        <a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['city']['city_id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['edit']; ?>"><?php echo $this->_var['lang']['remove']; ?></a> 
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <tr>
      <td align="right" nowrap="true" colspan="6">
      <?php echo $this->fetch('page.htm'); ?>
      </td>
    </tr>
  </table>

<?php if ($this->_var['full_page']): ?>
<!-- end brand list -->
</div>
</form>

<script type="text/javascript" language="javascript">
  <!--
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
  
  function search_city()
  {
        listTable.filter['city_name'] = Utils.trim(document.forms['searchForm'].elements['city_name'].value);
        listTable.filter['page'] = 1;
        
        listTable.loadList();
 }
  function select_city(city_id)
  {
	 if (city_id > 0)
	 { 
  	   Ajax.call('group_city.php?is_ajax=1&act=select_city', 'city_id=' + city_id, listTable.listCallback, "GET", "JSON");
	 }
  }

  //-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>