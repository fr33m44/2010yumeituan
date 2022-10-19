<!DOCTYPE HTML>
<html>
    <head>
<meta name="Generator" content="渝美团 v1.0.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->_var['group_shoptitle']; ?>,<?php echo $this->_var['group_shopdesc']; ?>| <?php echo $this->_var['city_info']['city_title']; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
	<link rel="shortcut icon" href="favicon.ico">
	<link href="template/meituan/style.css" rel="stylesheet" type="text/css" />
	<link href="template/meituan/slides.css" rel="stylesheet" type="text/css" />
	<link href="template/meituan/container.css" rel="stylesheet" type="text/css" />
	<meta name="description" content="<?php echo $this->_var['city_info']['city_desc']; ?>" />
	<meta name="keywords" content="<?php echo $this->_var['city_info']['city_keyword']; ?>" />
	
	
	<?php echo $this->smarty_insert_scripts(array('files'=>'groupontime.js,jquery.min.js,dialog-min.js,slides.js,group.js')); ?>
    </head>
    <body class="bg-alt ">
	<div id="doc">
	    <?php echo $this->fetch('library/group_header.lbi'); ?>
	    <?php if ($this->_var['msg']): ?>
	    <div id="sysmsg-<?php echo $this->_var['msg']['type']; ?>" class="sysmsgw"><div class="sysmsg">
		    <p><?php echo $this->_var['msg']['content']; ?></p>
		    <span class="close">关闭</span>
		</div>
	    </div>
	    <?php endif; ?>
	    <div id="bdw" class="bdw">
		<div id="bd" class="cf">
		    <div id="maillist">
			<div id="content">
			    <div class="box">
				<div class="box-top"></div>
				<div class="box-content welcome">
				    <div class="head">
					<h2>
					    <?php if ($this->_var['get_city_id'] == 0): ?>
					    没有您所在的城市？
					    <?php else: ?>
					    <?php echo $this->_var['group_shopname']; ?><?php echo $this->_var['city_name']; ?>站，即将推出
					    <?php endif; ?>
					</h2>
				    </div>
				    <div class="sect">
                                        <div class="enter-address">
					    <p class="enter-top welcome-title">
						<?php if ($this->_var['get_city_id'] == 0): ?>
						告诉我们您在哪个城市吧，下一步我们会在呼声最高的城市推出精品团购服务。
						<?php else: ?>
						邮件预定<?php echo $this->_var['city_name']; ?>每日最新团购信息，服务开通时第一时间通知您。
						<?php endif; ?>
					    </p>
					    <div class="enter-address-c">
						<form id="enter-address-form" action="" method="post"  >

						    <div class="mail">
							<label>邮件地址：</label>
							<input id="enter-address-mail" name="email" class="f-input f-mail" type="text"   size="20" />
							<span class="tip">请放心，我们和您一样讨厌垃圾邮件</span>
						    </div>
						    <div class="city">
							<label id="enter-address-city-label">选择您的城市：</label>

							<select name="cityid" class="f-city" style="display:inline-block">
							    <?php $_from = $this->_var['group_city']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?>
							    <option value="<?php echo $this->_var['city']['city_id']; ?>" <?php if ($this->_var['city']['city_id'] == $this->_var['city_info']['city_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['city']['city_name']; ?></option>
							    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							    <option <?php if ($this->_var['get_city_id'] == 3355): ?>selected="selected"<?php endif; ?> value="3355">开县</option>
							    <option <?php if ($this->_var['get_city_id'] == 3328): ?>selected="selected"<?php endif; ?> value="3328">永川</option>
							    <option <?php if ($this->_var['get_city_id'] == 3344): ?>selected="selected"<?php endif; ?>  value="3344">綦江</option>
							    <option <?php if ($this->_var['get_city_id'] == 3360): ?>selected="selected"<?php endif; ?>  value="3360">忠县</option>
							</select>
							<input type="hidden" name="act" value="add_email" />
							<input type="hidden" name="do" value="add" />
							<input type="hidden" name="do" value="add" />
							<input id="enter-address-city" autocomplete="off" name="cityname" class="f-input f-cityname" type="text"   style="display:none" />
							<!--<input type="submit" onclick="return false;" value="隐藏下面提交按钮的黑色边框，不要删" style="width:0;height:0;" />-->
							&nbsp;&nbsp;<input id="enter-address-commit" type="submit" class="formbutton" value="订阅" /><span class="tip"><a id="subscribe-other" href="/maillist/subscribe/new">没有您所在的城市？</a></span>                            </div>
						</form>


					    </div>
					    <div class="clear"></div>
					</div>
					<div class="intro">
					    <p>每日精品团购包括：</p>
					    <p>餐厅、酒吧、KTV、SPA、美发、健身、瑜伽、演出、影院等。</p>
					</div>
                                    </div>
				</div>
				<div class="box-bottom"></div>
			    </div>
			</div>
			<div id="sidebar">
			    
			    <div class="sbox side-box">
				<div class="sbox-top"></div>
				<div class="sbox-content">
				    <div class="tip marketing">
					<h3>市场合作</h3>
					<p>如果您想加盟美团或洽谈市场合作，请联系：<br>
					    <span>yumeituan@163.com</span>
					</p>
				    </div>
				</div>
				<div class="sbox-bottom"></div>
			    </div>
			    <div class="sbox side-business-tip">
				<div class="sbox-top"></div>
				<div class="sbox-content">
				    <div class="tip">
					<h2>商务合作</h2>
					<p class="text">希望在美团网组织团购么？请<a href="feedback.php?t=5">提交团购信息</a></p>
				    </div>
				</div>
				<div class="sbox-bottom"></div>
			    </div>
			</div>
		    </div>
		</div> 
	    </div> 

	    <div id="ftw">
		<?php echo $this->fetch('library/group_footer.lbi'); ?>
	    </div>

	</div> 

	<?php echo $this->smarty_insert_scripts(array('files'=>'mt/utilities.js,mt/core.js,mt/app-deal.js,mt/container-min.js,mt/app-misc.js')); ?>
	<script type="text/javascript">
	    YAHOO.util.Event.onDOMReady(function(){
		MT.app.Core.init();
	    });
	</script>
	<script type="text/javascript" charset="utf-8">
	    YAHOO.util.Event.onDOMReady(function(){
		MT.app.Subscribe.init();
	    });
	</script>

    </body>
</html>