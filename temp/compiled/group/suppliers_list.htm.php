<!-- $Id: agency_list.htm 14216 2008-03-10 02:27:21Z testyang $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>

<form method="post" action="" name="listForm" onsubmit="return confirm(batch_drop_confirm);">
<div class="list-div" id="listDiv">
<?php endif; ?>

  <table cellpadding="3" cellspacing="1" id="list-table">
    <tr>
      <th> <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" />
          <a href="javascript:listTable.sort('suppliers_id'); "><?php echo $this->_var['lang']['record_id']; ?></a><?php echo $this->_var['sort_suppliers_id']; ?> </th>
      <th><a href="javascript:listTable.sort('suppliers_name'); "><?php echo $this->_var['lang']['suppliers_name']; ?></a><?php echo $this->_var['sort_suppliers_name']; ?></th>
       <th><?php echo $this->_var['lang']['linkman']; ?></th>
      <th><?php echo $this->_var['lang']['phone']; ?></th>
      <th><?php echo $this->_var['lang']['suppliers_check']; ?></th>
      <th><?php echo $this->_var['lang']['handler']; ?></th>
    </tr>
    <?php $_from = $this->_var['suppliers_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'suppliers');if (count($_from)):
    foreach ($_from AS $this->_var['suppliers']):
?>
  <tr id="<?php echo $this->_var['suppliers']['suppliers_id']; ?>" class="0">
      <td><input type="checkbox" name="checkboxes[]" value="<?php echo $this->_var['suppliers']['suppliers_id']; ?>" />
        <?php echo $this->_var['suppliers']['suppliers_id']; ?>
      <img src="images/menu_minus.gif" id="icon_<?php echo $this->_var['suppliers']['suppliers_id']; ?>" width="9" height="9" border="0" onclick="rowClicked(this)"/>
        </td>
      <td class="first-cell">
        <span onclick="javascript:listTable.edit(this, 'edit_suppliers_name', <?php echo $this->_var['suppliers']['suppliers_id']; ?>)"><?php echo htmlspecialchars($this->_var['suppliers']['suppliers_name']); ?>      </span></td>
          <td><?php echo $this->_var['suppliers']['linkman']; ?></td>
      <td><?php echo $this->_var['suppliers']['phone']; ?></td>
			<td align="center"><img src="images/<?php if ($this->_var['suppliers']['is_check'] == 1): ?>yes<?php else: ?>no<?php endif; ?>.gif" onclick="listTable.toggle(this, 'is_check', <?php echo $this->_var['suppliers']['suppliers_id']; ?>)" style="cursor:pointer;"/></td>
      <td align="center">
        <a href="suppliers.php?act=edit&id=<?php echo $this->_var['suppliers']['suppliers_id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><?php echo $this->_var['lang']['edit']; ?></a> |
        <a href="javascript:void(0);" onclick="listTable.remove(<?php echo $this->_var['suppliers']['suppliers_id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['remove']; ?>"><?php echo $this->_var['lang']['remove']; ?></a>      </td>
    </tr>
     <?php $_from = $this->_var['suppliers']['lower_suppliers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'lower');if (count($_from)):
    foreach ($_from AS $this->_var['lower']):
?>
  <tr id="<?php echo $this->_var['lower']['suppliers_id']; ?>" class="1">
      <td><input type="checkbox" name="checkboxes[]" value="<?php echo $this->_var['lower']['suppliers_id']; ?>" />
        <?php echo $this->_var['lower']['suppliers_id']; ?>
         <img src="images/menu_arrow.gif" id="icon_<?php echo $this->_var['lower']['suppliers_id']; ?>" width="9" height="9" border="0" onclick="rowClicked(this)"/>
        </td>
      <td class="first-cell">
        <span onclick="javascript:listTable.edit(this, 'edit_suppliers_name', <?php echo $this->_var['lower']['suppliers_id']; ?>)"><?php echo htmlspecialchars($this->_var['lower']['suppliers_name']); ?>      </span></td>
          <td><?php echo $this->_var['lower']['linkman']; ?></td>
      <td><?php echo $this->_var['lower']['phone']; ?></td>
			<td align="center"><img src="images/<?php if ($this->_var['lower']['is_check'] == 1): ?>yes<?php else: ?>no<?php endif; ?>.gif" onclick="listTable.toggle(this, 'is_check', <?php echo $this->_var['lower']['suppliers_id']; ?>)" style="cursor:pointer;"/></td>
      <td align="center">
        <a href="suppliers.php?act=edit&id=<?php echo $this->_var['lower']['suppliers_id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><?php echo $this->_var['lang']['edit']; ?></a> |
        <a href="javascript:void(0);" onclick="listTable.remove(<?php echo $this->_var['lower']['suppliers_id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['remove']; ?>"><?php echo $this->_var['lang']['remove']; ?></a>      </td>
    </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="4"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
<table id="page-table" cellspacing="0">
  <tr>
    <td>
      <input name="remove" type="submit" id="btnSubmit" value="<?php echo $this->_var['lang']['drop']; ?>" class="button" disabled="true" />
      <input name="act" type="hidden" value="batch" />
    </td>
    <td align="right" nowrap="true">
    <?php echo $this->fetch('page.htm'); ?>
    </td>
  </tr>
</table>

<?php if ($this->_var['full_page']): ?>
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
  
  //-->
  var imgPlus = new Image();
imgPlus.src = "images/menu_plus.gif";

/**
 * 折叠分类列表
 */
function rowClicked(obj)
{
  // 当前图像
  img = obj;
  // 取得上二级tr>td>img对象
  obj = obj.parentNode.parentNode;
  // 整个分类列表表格
  var tbl = document.getElementById("list-table");
  // 当前分类级别
  var lvl = parseInt(obj.className);
  // 是否找到元素
  var fnd = false;
  var sub_display = img.src.indexOf('menu_minus.gif') > 0 ? 'none' : (Browser.isIE) ? 'block' : 'table-row' ;
  // 遍历所有的分类
  for (i = 0; i < tbl.rows.length; i++)
  {
      var row = tbl.rows[i];
      if (row == obj)
      {
          // 找到当前行
          fnd = true;
          //document.getElementById('result').innerHTML += 'Find row at ' + i +"<br/>";
      }
      else
      {
          if (fnd == true)
          {
              var cur = parseInt(row.className);
              var icon = 'icon_' + row.id;
              if (cur > lvl)
              {
                  row.style.display = sub_display;
                  if (sub_display != 'none')
                  {
                      var iconimg = document.getElementById(icon);
                      iconimg.src = iconimg.src.replace('plus.gif', 'minus.gif');
                  }
              }
              else
              {
                  fnd = false;
                  break;
              }
          }
      }
  }

  for (i = 0; i < obj.cells[0].childNodes.length; i++)
  {
      var imgObj = obj.cells[0].childNodes[i];
      if (imgObj.tagName == "IMG" && imgObj.src != 'images/menu_arrow.gif')
      {
          imgObj.src = (imgObj.src == imgPlus.src) ? 'images/menu_minus.gif' : imgPlus.src;
      }
  }
}

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>