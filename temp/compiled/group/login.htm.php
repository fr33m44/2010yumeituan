<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $this->_var['lang']['cp_home']; ?><?php if ($this->_var['ur_here']): ?> - <?php echo $this->_var['ur_here']; ?><?php endif; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="styles/general.css" rel="stylesheet" type="text/css" />
<link href="styles/main.css" rel="stylesheet" type="text/css" />

<style type="text/css">
body {
  color: white;
}
.logintable td{padding:3px;color:#32639A;}
.logintable .txt{height:20px;line-height:20px;padding:0 1px;border:1px solid #32639A;}
</style>

<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>
<script language="JavaScript">
<!--
// 这里把JS用到的所有语言都赋值到这里
<?php $_from = $this->_var['lang']['js_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

if (window.parent != window)
{
  window.top.location.href = location.href;
}

//-->
</script>
</head>
<body style="background:#3A6EA5;">
<form method="post" action="privilege.php" name='theForm' onsubmit="return validate()">
<table cellspacing="0" cellpadding="0" style="margin-top: 100px" align="center">
<tr>
<td height="295" width="35"><img src="images/login-box-l.png" /></td>
<td style="width:390px;background:url(images/login-box-c.png) left top repeat-x;">
<div style="width:390px;height:295px;position:relative;">
<img src="images/ecshop_logo.gif" style="position:absolute;top:40px;left:0;" />
<div style="position:absolute;top:140px;left:5px;">
	<table cellspacing="0" cellpadding="0" class="logintable">
    	<tr><td align="right"><?php echo $this->_var['lang']['label_username']; ?></td><td><input type="text" name="username" class="txt" /></td></tr>
        <tr><td align="right"><?php echo $this->_var['lang']['label_password']; ?></td><td><input type="password" name="password" class="txt" /></td></tr>
        <?php if ($this->_var['gd_version'] > 0): ?>
        <tr><td align="right"><?php echo $this->_var['lang']['label_captcha']; ?></td><td><input type="text" name="captcha" class="capital txt" /></td></tr>
        <tr><td align="right"></td><td><img src="index.php?act=captcha&<?php echo $this->_var['random']; ?>" width="145" height="20" alt="CAPTCHA" border="1" onclick= this.src="index.php?act=captcha&"+Math.random() style="cursor: pointer;" title="<?php echo $this->_var['lang']['click_for_another']; ?>" /></td></tr>
        <?php endif; ?>
    </table>
</div>
<img src="images/login-line.gif" style="position:absolute;top:160px;left:275px;" />
<input type="submit" value="" class="button" style="padding:0;margin:0;cursor:pointer;position:absolute;top:168px;left:310px;width:62px;height:53px;border:none;background:url(images/login-but.png) no-repeat;" />
<div style="position:absolute;left:50px;bottom:13px;color:#32639A;"><input type="checkbox" value="1" name="remember" id="remember" /><label for="remember"><?php echo $this->_var['lang']['remember']; ?></label> | <a href="get_password.php?act=forget_pwd" style="color:#32639A;"><?php echo $this->_var['lang']['forget_pwd']; ?></a></div>
</div>
</td>
<td height="295" width="35"><img src="images/login-box-r.png" /></td>
</tr>
</table>
<input type="hidden" name="act" value="signin" />
</form>
<script language="JavaScript">
<!--
  document.forms['theForm'].elements['username'].focus();
  
  /**
   * 检查表单输入的内容
   */
  function validate()
  {
    var validator = new Validator('theForm');
    validator.required('username', user_name_empty);
    //validator.required('password', password_empty);
    if (document.forms['theForm'].elements['captcha'])
    {
      validator.required('captcha', captcha_empty);
    }
    return validator.passed();
  }
  
//-->
</script>
</body>