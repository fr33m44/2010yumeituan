<!-- $Id: account_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>

<div class="main-div">
<form method="post" action="account_log.php" name="theForm" onsubmit="return validate()">
<table cellspacing="1" cellpadding="3" width="100%">
  <tr>
    <td class="label"><?php echo $this->_var['lang']['label_user_name']; ?></td>
    <td><?php echo $this->_var['user']['user_name']; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['label_change_desc']; ?></td>
    <td><textarea  name="change_desc" cols="60" rows="4"></textarea></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['label_user_money']; ?></td>
    <td><select name="add_sub_user_money" id="add_sub_user_money">
      <option value="1" selected="selected"><?php echo $this->_var['lang']['add']; ?></option>
      <option value="-1"><?php echo $this->_var['lang']['subtract']; ?></option>
    </select>
    <input name="user_money" type="text" id="user_money" style="text-align:right" value="0" size="10" />
    <?php echo $this->_var['lang']['current_value']; ?><?php echo $this->_var['user']['formated_user_money']; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['label_frozen_money']; ?></td>
    <td><select name="add_sub_frozen_money" id="add_sub_frozen_money">
      <option value="1" selected="selected"><?php echo $this->_var['lang']['add']; ?></option>
      <option value="-1"><?php echo $this->_var['lang']['subtract']; ?></option>
    </select>
      <input name="frozen_money" type="text" id="frozen_money" style="text-align:right" value="0" size="10" />
      <?php echo $this->_var['lang']['current_value']; ?><?php echo $this->_var['user']['formated_frozen_money']; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['label_rank_points']; ?></td>
    <td><select name="add_sub_rank_points" id="add_sub_rank_points">
      <option value="1" selected="selected"><?php echo $this->_var['lang']['add']; ?></option>
      <option value="-1"><?php echo $this->_var['lang']['subtract']; ?></option>
    </select>
      <input name="rank_points" type="text" id="rank_points" style="text-align:right" value="0" size="10" />
      <?php echo $this->_var['lang']['current_value']; ?><?php echo $this->_var['user']['rank_points']; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['label_pay_points']; ?></td>
    <td><select name="add_sub_pay_points" id="add_sub_pay_points">
      <option value="1" selected="selected"><?php echo $this->_var['lang']['add']; ?></option>
      <option value="-1"><?php echo $this->_var['lang']['subtract']; ?></option>
    </select>
      <input name="pay_points" type="text" id="pay_points" style="text-align:right" value="0" size="10" />
      <?php echo $this->_var['lang']['current_value']; ?><?php echo $this->_var['user']['pay_points']; ?></td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" class="button" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
      <input type="reset" class="button" value="<?php echo $this->_var['lang']['button_reset']; ?>" />
      <input type="hidden" name="act" value="insert" />
      <input type="hidden" name="user_id" value="<?php echo $this->_var['user']['user_id']; ?>" />
  </tr>
</table>
</form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>

<script language="JavaScript">
<!--
onload = function()
{
    // 开始检查订单
    startCheckOrder();
}
/**
 * 检查表单输入的数据
 */
function validate()
{
    validator = new Validator("theForm");
    validator.required("change_desc",  no_change_desc);
	validator.isNumber("user_money", user_money_not_number);
	validator.isNumber("frozen_money", frozen_money_not_number);
	validator.isInt("rank_points", rank_points_not_int);
	validator.isInt("pay_points", pay_points_not_int);
    return validator.passed();
}

//-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>