<div id="ft">
    <p class="contact"><a  href="feedback.php?t=1">意见反馈</a></p>
    <ul class="nav cf">
	<?php $_from = $this->_var['group_help']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'help');if (count($_from)):
    foreach ($_from AS $this->_var['help']):
?>
	<li class="col">
	    <h3><?php echo $this->_var['help']['cat_name']; ?></h3>
	    <ul class="sub-list">
		<?php $_from = $this->_var['help']['article']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'article');if (count($_from)):
    foreach ($_from AS $this->_var['article']):
?>
		<li><a href="<?php echo $this->_var['article']['url']; ?>"><?php echo $this->_var['article']['title']; ?></a></li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	    </ul>
	</li>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<li class="col end">
	    <div class="logo-footer">
		<a href="<?php echo $this->_var['index_url']; ?>"><img src="template/meituan/images/logo-footer.gif" alt=""></a>
	    </div>
	</li>
    </ul>
    <div class="copyright">
	<p> &copy; <span title="I:-; Q:0; S:0; C:0; F:0; T:6.86; H:v03">2010</span> yumeituan.com 渝ICP备070791号 </p>
    </div>
    <ul class="cert cf">
            <li class="itrust">网上交易保障"</li>
	    <li class="hlwxh">互联网协会信任网站"</li>
	    <li class="alipay">支付宝特约商家"</li>
	    <li class="tenpay">财付通特约商家"</li>
    </ul>
</div>