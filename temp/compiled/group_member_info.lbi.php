<?php if ($this->_var['user_info']): ?>
<div class="logins">
    <ul class="links">
	<li class="username">欢迎您，<?php echo $this->_var['user_info']['username']; ?></li>
	<li class="account"><a class="account" id="myaccount" isuc="true" href="coupons.php">我的<?php echo $this->_var['group_shopname']; ?></a></li>
	<li class="logout"><a href="account.php?act=logout">退出</a></li>
    </ul>
    <div class="line islogin"></div>
    <div class="refer">
	<a href="invite.php">邀请好友购买返 <?php echo $this->_var['rebate']; ?> 元</a>
    </div>
</div>
<ul style="display: none;" id="myaccount-menu">
    <li><a href="coupons.php">我的<?php echo $this->_var['group_cardname']; ?></a></li>
    <li><a href="myorders.php">我的订单</a></li>
    <li><a href="myinvite.php">我的邀请</a></li>
    <li><a href="account.php?act=credit">账户余额</a></li>
    <li><a href="account.php?act=settings">账户设置</a></li>
    <li><a href="account.php?act=address">配送地址</a></li>
</ul>
<?php else: ?>
<div class="logins">
    <ul class="links">
	<li class="login"><a href="login.php">登录</a></li>
	<li class="signup"><a href="signup.php">注册</a></li>
    </ul>
    <div class="line "></div>
    <div class="refer">
	<a href="invite.php">邀请好友购买返 <?php echo $this->_var['rebate']; ?> 元</a>
    </div>
</div>
<?php endif; ?>