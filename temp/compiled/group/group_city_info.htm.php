<!-- $Id: brand_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/transport.js,../js/region.js')); ?>

<div class="main-div">
<form method="post" action="group_city.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
<table cellspacing="1" cellpadding="3" width="100%">
  <tr>
    <td class="label"><?php echo $this->_var['lang']['city_name']; ?></td>
    <td>
      <!--<?php if ($this->_var['form_action'] == 'updata'): ?>-->
        <input name="city_name" value="<?php echo $this->_var['city']['city_name']; ?>" disabled="disabled" />
       <!--<?php else: ?>-->
      <select name="country" id="selCountries" onchange="region.changed(this, 1, 'selProvinces')" style="border:1px solid #ccc;">
        <option value="0"><?php echo $this->_var['lang']['please_select_country']; ?></option>
        <!-- <?php $_from = $this->_var['country_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'country');if (count($_from)):
    foreach ($_from AS $this->_var['country']):
?> -->
        <option value="<?php echo $this->_var['country']['region_id']; ?>" <?php if ($this->_var['shop_country'] == $this->_var['country']['region_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['country']['region_name']; ?></option>
        <!-- <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> -->
      </select>
      <select name="province" id="selProvinces" onchange="region.changed(this, 2, 'selCities')" style="border:1px solid #ccc;">
        <option value="0"><?php echo $this->_var['lang']['please_select_province']; ?></option>
        <option value="1"><?php echo $this->_var['lang']['country_name']; ?></option>
        <!-- <?php $_from = $this->_var['province_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'province');if (count($_from)):
    foreach ($_from AS $this->_var['province']):
?> -->
        <option value="<?php echo $this->_var['province']['region_id']; ?>" <?php if ($this->_var['group_buy']['province_id'] == $this->_var['province']['region_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['province']['region_name']; ?></option>
        <!-- <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> -->
      </select>
      <select name="city_id" id="selCities" style="border:1px solid #ccc;">
        <option value="0"><?php echo $this->_var['lang']['please_select_city']; ?></option>
               <option value="1"><?php echo $this->_var['lang']['country_name']; ?></option>
        <!-- <?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city_0_35390000_1292811034');if (count($_from)):
    foreach ($_from AS $this->_var['city_0_35390000_1292811034']):
?> -->
        <option value="<?php echo $this->_var['city_0_35390000_1292811034']['region_id']; ?>" <?php if ($this->_var['group_buy']['city_id'] == $this->_var['city_0_35390000_1292811034']['region_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['city_0_35390000_1292811034']['region_name']; ?></option>
        <!-- <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> -->
      </select>
     <!--<?php endif; ?>--> 
</td>
  </tr>
    <tr>
    <td class="label"><?php echo $this->_var['lang']['city_title']; ?></td>
    <td><input name='city_title' value='<?php echo $this->_var['city']['city_title']; ?>' type='text'></input></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['city_keyword']; ?></td>
    <td><input name='city_keyword' value='<?php echo $this->_var['city']['city_keyword']; ?>' type='text'></input></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['city_qq']; ?></td>
    <td><input name='city_qq' value='<?php echo $this->_var['city']['city_qq']; ?>' type='text'></input></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['city_sort']; ?></td>
    <td><input name='city_sort' value='<?php echo $this->_var['city']['city_sort']; ?>' type='text'></input></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['city_desc']; ?></td>
    <td><textarea  name="city_desc" cols="60" rows="4"  ><?php echo $this->_var['city']['city_desc']; ?></textarea></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['city_notice']; ?></td>
    <td><?php echo $this->_var['city_notice']; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['is_open']; ?></td>
    <td><input type="radio" name="is_open" value="1" <?php if ($this->_var['city']['is_open'] == 1): ?>checked="checked"<?php endif; ?> /> <?php echo $this->_var['lang']['yes']; ?>
        <input type="radio" name="is_open" value="0" <?php if ($this->_var['city']['is_open'] == 0): ?>checked="checked"<?php endif; ?> /> <?php echo $this->_var['lang']['no']; ?>
    </td>
  </tr>
    <tr>
    <td class="label"><?php echo $this->_var['lang']['is_select']; ?></td>
    <td><input type="radio" name="is_select" value="1" <?php if ($this->_var['city']['is_select'] == 1): ?>checked="checked"<?php endif; ?> /> <?php echo $this->_var['lang']['yes']; ?>
        <input type="radio" name="is_select" value="0" <?php if ($this->_var['city']['is_select'] == 0): ?>checked="checked"<?php endif; ?> /> <?php echo $this->_var['lang']['no']; ?>
    </td>
  </tr>

  <tr>
    <td colspan="2" align="center"><br />
      <input type="submit" class="button" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
      <input type="reset" class="button" value="<?php echo $this->_var['lang']['button_reset']; ?>" />
      <input type="hidden" name="old_city_name" value="<?php echo $this->_var['city']['city_name']; ?>" />
      <input type="hidden" name="act" value="<?php echo $this->_var['form_action']; ?>" />
      <input type="hidden" name="id" value="<?php echo $this->_var['city']['city_id']; ?>" />
    </td>
  </tr>
</table>
</form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>

<script language="JavaScript">
<!--
region.isAdmin = true;

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
	var city_id = document.forms['theForm']['city_id'].value;
    if (document.forms['theForm']['selProvinces'].value == 1)
	{
		city_id = 1;
		document.forms['theForm']['city_id'].value = 1;
	}
	if (city_id == 0)
	{
	    alert(no_cityid);
		return false;
	}
	return true;
}
//-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>