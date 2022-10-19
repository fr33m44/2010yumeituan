<div id="hdw">
    <div id="hd">
	<div id="logo"><a href="<?php echo $this->_var['index_url']; ?>" class="link"><img src="template/meituan/images/logo.gif" /></a></div>
	<div class="guides">
	    <div class="city">
		<h2 id="header-city" class="fold" sid="<?php echo $this->_var['city_info']['city_id']; ?>" sslug="" sname="<?php echo $this->_var['city_info']['city_name']; ?>">
		    <span><?php echo $this->_var['city_info']['city_name']; ?></span><em></em>
		</h2>
	    </div>
	    <div class="city-list" id="guides-city-list">
	    <ul>
	     <?php $_from = $this->_var['group_city']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?>
	     <li <?php if ($this->_var['city']['city_id'] == $this->_var['cityid']): ?>class="current"<?php endif; ?>><a href="<?php echo $this->_var['city']['url']; ?>" class="opened"><?php echo $this->_var['city']['city_name']; ?></a></li>
	    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	    <li><a href="sub.php?cityid=3355">开县</a></li>
	    <li><a href="sub.php?cityid=3328">永川</a></li>
	    <li><a href="sub.php?cityid=3344">綦江</a></li>
	    <li><a href="sub.php?cityid=3360">忠县</a></li>
	    </ul>
	    <div class="other"><a href="sub.php">其他城市？</a></div>
	    </div>
	</div>
	
	<a href="http://php.weather.sina.com.cn/search.php?city=<?php echo $this->_var['city_info']['city_name']; ?>&c=1&dpc=1" target="_blank">
	    <div class="weather">
		 <div style=" background: url(template/meituan/images/weather/<?php echo $this->_var['weather']['image']; ?>) 0 0  no-repeat;_background:none;_FILTER: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='template/meituan/images/weather/<?php echo $this->_var['weather']['image']; ?>', sizingMethod='crop')"></div>
		 <span class="temp"><?php echo $this->_var['weather']['temp']; ?></span> <br /><span class="desc"><?php echo $this->_var['weather']['weather']; ?></span>
	    </div>
	</a>
	<div id="header-subscribe-body" class="subscribe">
	    <form action="subscribe.php" method="post" id="header-subscribe-form">
		<label for="header-subscribe-email" class="text">想知道<?php echo $this->_var['city_info']['city_name']; ?>明天的团购是什么吗？</label>
		<input id="header-subscribe-email" value="输入Email，订阅每日团购信息" class="f-text" name="email" autocomplete="off" type="text">
		<input type="hidden" value="add_email" name="act" />
		<input type="hidden" value="add" name="do" />
		<input type="hidden" value="<?php echo $this->_var['cityid']; ?>" name="city_id" />
		<input class="commit" value="订阅" type="submit">
		<span class="sms" id="header-subscribe-sms">» 短信订阅，免费！</span>
		<span class="sms" id="header-unsubscribe-sms">» 取消手机订阅</span>
	    </form>
	</div>
	<div id="header-subscribe-auto" class="email-auto"><p class="email-title">请选择您的邮箱类型...</p><ul class="email-list"></ul></div>
	<ul class="nav cf">
	    <?php $_from = $this->_var['navigation']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('nav', 'navdesc');if (count($_from)):
    foreach ($_from AS $this->_var['nav'] => $this->_var['navdesc']):
?>
	    <li><a href="<?php echo $this->_var['navdesc']['url']; ?>" <?php if ($this->_var['nav'] == 'index'): ?> class="today"<?php endif; ?>><?php echo $this->_var['navdesc']['name']; ?></a></li>
	    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</ul>
	<?php 
$k = array (
  'name' => 'group_member_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
    </div>
</div>