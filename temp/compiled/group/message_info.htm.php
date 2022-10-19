<!-- $Id: message_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'validator.js')); ?>
<div class="main-div">
<form action="message.php" method="post" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
<table width="100%">
<?php if ($this->_var['action'] == "add"): ?>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['receiver_id']; ?></td>
    <td>
      <select name="receiver_id[]" size="5" multiple="true" style="width:40%">
      <option value="0" selected="true"><?php echo $this->_var['lang']['all_amdin']; ?></option>
      <?php $_from = $this->_var['admin_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?>
      <?php if ($this->_var['val']['user_name'] != $this->_var['admin_user']): ?>
        <option value="<?php echo $this->_var['val']['user_id']; ?>"><?php echo $this->_var['val']['user_name']; ?></option>
      <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      </select>
    </td>
  </tr>
<?php endif; ?>
<?php if ($this->_var['action'] == "reply"): ?>
<tr>
  <td class="label"><?php echo $this->_var['lang']['receiver_id']; ?></td>
  <td>
  <select name="receiver_id" style="width:30%">
   <option value="<?php echo $this->_var['msg_val']['sender_id']; ?>"><?php echo $this->_var['msg_val']['user_name']; ?></option>
  </select>
</td>
</tr>
<?php endif; ?>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['title']; ?></td>
    <td>
      <input type="text" name="title" maxlength="50" value="<?php echo $this->_var['msg_arr']['title']; ?>" size="40" />
   </td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['message']; ?>  </td>
    <td>
      <textarea name="message" cols="55" rows="8" /><?php echo $this->_var['msg_arr']['message']; ?></textarea>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="left">
      <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />&nbsp;&nbsp;&nbsp;
      <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
      <input type="hidden" name="act" value="<?php echo $this->_var['form_act']; ?>" />
      <input type="hidden" name="id" value="<?php echo $this->_var['msg_arr']['message_id']; ?>" />
    </td>
  </tr>
</table>
</form>
</div>
<script language="JavaScript">
<!--

document.forms['theForm'].elements['title'].focus();
/**
 * 检查表单输入的数据
 */
function validate()
{
    validator = new Validator("theForm");
    validator.required("title",      title_empty);
    validator.required("message",    message_empty);
    return validator.passed();
}

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}
//-->

</script>
<?php echo $this->fetch('pagefooter.htm'); ?>