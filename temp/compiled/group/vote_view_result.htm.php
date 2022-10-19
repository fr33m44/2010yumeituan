<!-- $Id: vote_option.htm 16902 2009-12-18 03:56:55Z sxc_shop $ -->
<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>


<!-- start option list -->
<div class="list-div" id="listDiv">
			<?php endif; ?>

    <table cellspacing='1' cellpadding='3' id='listTable'>
	<tr>
	    <th>问题</th>
	    <th>答案</th>
	</tr>
	<?php $_from = $this->_var['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
	<tr align="center">
	    <td align="left" class="first-cell">
		<span><?php echo $this->_var['list']['question']; ?></span>
	    </td>
	    <td><span><?php echo $this->_var['list']['answer']; ?></span></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </table>

<?php if ($this->_var['full_page']): ?>
</div>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>