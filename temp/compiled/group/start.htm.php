<!-- $Id: start.htm 17110 2010-04-15 07:47:51Z sxc_shop $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<!-- directory install start -->
<ul id="lilist" style="padding:0; margin: 0; list-style-type:none; color: #CC0000;">
  <?php $_from = $this->_var['warning_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'warning');if (count($_from)):
    foreach ($_from AS $this->_var['warning']):
?>
  <li style="border: 1px solid #CC0000; background: #FFFFCC; padding: 10px; margin-bottom: 5px;" ><?php echo $this->_var['warning']; ?></li>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>
<ul style="padding:0; margin: 0; list-style-type:none; color: #CC0000;">
 <!-- <script type="text/javascript" src="http://bbs.ecshop.com/notice.php?v=1&n=8&f=ul"></script>-->
</ul>
<!-- directory install end -->
<!-- start personal message -->
<?php if ($this->_var['admin_msg']): ?>
<div class="list-div" style="border: 1px solid #CC0000">
  <table cellspacing='1' cellpadding='3'>
    <tr>
      <th><?php echo $this->_var['lang']['pm_title']; ?></th>
      <th><?php echo $this->_var['lang']['pm_username']; ?></th>
      <th><?php echo $this->_var['lang']['pm_time']; ?></th>
    </tr>
    <?php $_from = $this->_var['admin_msg']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'msg');if (count($_from)):
    foreach ($_from AS $this->_var['msg']):
?>
      <tr align="center">
        <td align="left"><a href="message.php?act=view&id=<?php echo $this->_var['msg']['message_id']; ?>"><?php echo sub_str($this->_var['msg']['title'],60); ?></a></td>
        <td><?php echo $this->_var['msg']['user_name']; ?></td>
        <td><?php echo $this->_var['msg']['send_date']; ?></td>
      </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  </div>
<br />
<?php endif; ?>
<!-- end personal message -->
<!-- start order statistics -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title"><?php echo $this->_var['lang']['order_stat']; ?></th>
  </tr>
  <tr>
    <td width="20%"><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['await_ship']; ?>"><?php echo $this->_var['lang']['await_ship']; ?></a></td>
    <td width="30%"><strong style="color: red"><?php echo $this->_var['order']['await_ship']; ?></strong></td>
    <td width="20%"><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['unconfirmed']; ?>"><?php echo $this->_var['lang']['unconfirmed']; ?></a></td>
    <td width="30%"><strong><?php echo $this->_var['order']['unconfirmed']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['await_pay']; ?>"><?php echo $this->_var['lang']['await_pay']; ?></a></td>
    <td><strong><?php echo $this->_var['order']['await_pay']; ?></strong></td>
    <td><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['finished']; ?>"><?php echo $this->_var['lang']['finished']; ?></a></td>
    <td><strong><?php echo $this->_var['order']['finished']; ?></strong></td>
  </tr>
</table>
</div>
<!-- end order statistics -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title"><?php echo $this->_var['lang']['acess_stat']; ?></th>
  </tr>
  <tr>
    <td width="20%"><?php echo $this->_var['lang']['acess_today']; ?></td>
    <td width="30%"><strong><?php echo $this->_var['today_visit']; ?></strong></td>
    <td width="20%"><?php echo $this->_var['lang']['online_users']; ?></td>
    <td width="30%"><strong><?php echo $this->_var['online_users']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="comment_manage.php?act=list"><?php echo $this->_var['lang']['new_comments']; ?></a></td>
    <td><strong><?php echo $this->_var['comment_number']; ?></strong></td>
    <td></td><td></td>
  </tr>
</table>
</div>
<!-- end access statistics -->
<br />
<!-- start system information -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title"><?php echo $this->_var['lang']['system_info']; ?></th>
  </tr>
  <tr>
    <td width="20%"><?php echo $this->_var['lang']['os']; ?></td>
    <td width="30%"><?php echo $this->_var['sys_info']['os']; ?> (<?php echo $this->_var['sys_info']['ip']; ?>)</td>
    <td width="20%"><?php echo $this->_var['lang']['web_server']; ?></td>
    <td width="30%"><?php echo $this->_var['sys_info']['web_server']; ?></td>
  </tr>
  <tr>
    <td><?php echo $this->_var['lang']['php_version']; ?></td>
    <td><?php echo $this->_var['sys_info']['php_ver']; ?></td>
    <td><?php echo $this->_var['lang']['mysql_version']; ?></td>
    <td><?php echo $this->_var['sys_info']['mysql_ver']; ?></td>
  </tr>
  <tr>
    <td><?php echo $this->_var['lang']['safe_mode']; ?></td>
    <td><?php echo $this->_var['sys_info']['safe_mode']; ?></td>
    <td><?php echo $this->_var['lang']['safe_mode_gid']; ?></td>
    <td><?php echo $this->_var['sys_info']['safe_mode_gid']; ?></td>
  </tr>
  <tr>
    <td><?php echo $this->_var['lang']['socket']; ?></td>
    <td><?php echo $this->_var['sys_info']['socket']; ?></td>
    <td><?php echo $this->_var['lang']['timezone']; ?></td>
    <td><?php echo $this->_var['sys_info']['timezone']; ?></td>
  </tr>
  <tr>
    <td><?php echo $this->_var['lang']['gd_version']; ?></td>
    <td><?php echo $this->_var['sys_info']['gd']; ?></td>
    <td><?php echo $this->_var['lang']['zlib']; ?></td>
    <td><?php echo $this->_var['sys_info']['zlib']; ?></td>
  </tr>
  <tr>
    <td><?php echo $this->_var['lang']['ip_version']; ?></td>
    <td><?php echo $this->_var['sys_info']['ip_version']; ?></td>
    <td><?php echo $this->_var['lang']['max_filesize']; ?></td>
    <td><?php echo $this->_var['sys_info']['max_filesize']; ?></td>
  </tr>
  <tr>
    <td><?php echo $this->_var['lang']['ecs_version']; ?></td>
    <td><?php echo $this->_var['ecs_version']; ?> RELEASE <?php echo $this->_var['ecs_release']; ?></td>
    <td><?php echo $this->_var['lang']['install_date']; ?></td>
    <td><?php echo $this->_var['install_date']; ?></td>
  </tr>
  <tr>
    <td><?php echo $this->_var['lang']['ec_charset']; ?></td>
    <td><?php echo $this->_var['ecs_charset']; ?></td>
    <td></td>
    <td></td>
  </tr>
</table>
</div>

<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js')); ?>
<script type="Text/Javascript" language="JavaScript">
<!--
onload = function()
{
  /* ???????????? */
  startCheckOrder();
}
  Ajax.call('index.php?is_ajax=1&act=main_api','', start_api, 'GET', 'TEXT','FLASE');

   function start_api(result)
    {
      apilist = document.getElementById("lilist").innerHTML;
      document.getElementById("lilist").innerHTML =result+apilist;
      if(document.getElementById("mar") != null)
      {
        var Mar = document.getElementById("mar");
        lis = Mar.getElementsByTagName('div');
        //alert(lis.length); //??????li???????????????
        if(lis.length>3)
        {
          api_styel();
        }      
      }


    }
      function api_styel()
      {
        if(document.getElementById("mar") != null)
        {
            var Mar = document.getElementById("mar");
            if (Browser.isIE)
            {
              Mar.style.height = "52px";
            }
            else
            {
              Mar.style.height = "36px";
            }
            
            var child_div=Mar.getElementsByTagName("div");

        var picH = 16;//????????????
        var scrollstep=2;//????????????,????????????
        var scrolltime=30;//????????????(??????)????????????
        var stoptime=4000;//????????????(??????)
        var tmpH = 0;
        
        function start()
        {
          if(tmpH < picH)
          {
            tmpH += scrollstep;
            if(tmpH > picH )tmpH = picH ;
            Mar.scrollTop = tmpH;
            setTimeout(start,scrolltime);
          }
          else
          {
            tmpH = 0;
            Mar.appendChild(child_div[0]);
            Mar.scrollTop = 0;
            setTimeout(start,stoptime);
          }
        }
        setTimeout(start,stoptime);
        }
      }
//-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
