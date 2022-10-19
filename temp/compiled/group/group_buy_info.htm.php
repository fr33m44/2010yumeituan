<!-- $Id: group_buy_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'validator.js,../js/utils.js,../js/transport.js,../js/region.js,selectzone.js,colorselector.js,../js/calendar/Wdatepicker.js')); ?>

<script type="text/javascript" src="../js/calendar.php?lang=<?php echo $this->_var['cfg_lang']; ?>"></script>
<link href="../js/calendar/calendar.css" rel="stylesheet" type="text/css" />

<!-- 商品搜索 -->

<form method="post" action="group_buy.php?act=insert_update" name="theForm" onsubmit="return validate();" enctype="multipart/form-data">
    <div class="main-div">
	<div id="tabbar-div">
	    <p>
		<span class="tab-front" id="group-tab"><?php echo $this->_var['lang']['tab_group_name']; ?></span>
		<span class="tab-back" id="friend_comment-tab"><?php echo $this->_var['lang']['tab_friend_comment']; ?></span>
		<span class="tab-back" id="gallery-tab"><?php echo $this->_var['lang']['tab_gallery']; ?></span>
		<span class="tab-back" id="video-tab"><?php echo $this->_var['lang']['tab_video']; ?></span>
		<span class="tab-back" id="group_properties-tab"><?php echo $this->_var['lang']['label_group_properties']; ?></span>
		<span class="tab-back" id="tab_seo-tab"><?php echo $this->_var['lang']['tab_seo']; ?></span>
		<span class="tab-back" id="group_detail-tab"><?php echo $this->_var['lang']['tab_group_detail']; ?></span>
		<span class="tab-back" id="group_information-tab"><?php echo $this->_var['lang']['tab_other_information']; ?></span>
	    </p>
	</div>
	<div id="tabbody-div">
	    <style type="text/css">
		#group-table{border-collapse:collapse;}
		#group-table td{padding:10px;border:1px solid #eee;}
	    </style>
	    <table id="group-table"  width="90%">
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_group_name']; ?></td>
		    <td colspan="5"><textarea name="group_name" rows="4" style="width:600px"><?php echo $this->_var['group_buy']['group_name']; ?></textarea></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_goods_name']; ?></td>
		    <td colspan="5"><input type="text" name='goods_name' value="<?php echo $this->_var['group_buy']['goods_name']; ?>" size="30" /></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_group_image']; ?></td>
		    <td colspan="5"><input type="file" name='group_image'/><?php if ($this->_var['group_buy']['group_image']): ?><a href="../<?php echo $this->_var['group_buy']['group_image']; ?>" target="_blank"><img src="images/yes.gif" border="0" /></a><?php else: ?><img src="images/no.gif" /><?php endif; ?>  <?php echo $this->_var['lang']['notice_group_imge']; ?></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_market_price']; ?></td>
		    <td colspan="5"><input type="text" name="market_price" value="<?php echo $this->_var['group_buy']['market_price']; ?>" size="30" /></td>
		</tr>
		<?php $_from = $this->_var['group_buy']['price_ladder']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
		<?php if ($this->_var['key'] == 0): ?>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_price_ladder']; ?></td>
		    <td colspan="1" id='aa'><?php echo $this->_var['lang']['notice_ladder_amount']; ?> <input type="text" name="ladder_amount[]" value="<?php echo $this->_var['item']['amount']; ?>" size="8" />&nbsp;&nbsp;
			<?php echo $this->_var['lang']['notice_ladder_price']; ?> <input type="text" name="ladder_price[]" value="<?php echo $this->_var['item']['price']; ?>" size="8" />
			<a href="javascript:;" onclick="addLadder(this,'group-table')"><strong>[+]</strong></a></td>
		    <td  colspan="4"><font color="#FF0000">必须设置数量达到1时,享受的价格是多少</font></td>
		</tr>
		<?php else: ?>
		<tr>
		    <td class="label">&nbsp;</td>
		    <td colspan="5"><?php echo $this->_var['lang']['notice_ladder_amount']; ?> <input type="text" name="ladder_amount[]" value="<?php echo $this->_var['item']['amount']; ?>" size="8" />&nbsp;&nbsp;
			<?php echo $this->_var['lang']['notice_ladder_price']; ?> <input type="text" name="ladder_price[]" value="<?php echo $this->_var['item']['price']; ?>" size="8" />
			<a href="javascript:;" onclick="removeLadder(this,'group-table')"><strong>[-]</strong></a></td>
		</tr>
		<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_type_name']; ?>：</td>
		    <td width="330"><select name="group_type">
			    <!--<?php $_from = $this->_var['lang']['label_group_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('type_id', 'type_name');if (count($_from)):
    foreach ($_from AS $this->_var['type_id'] => $this->_var['type_name']):
?>-->
			    <option value="<?php echo $this->_var['type_id']; ?>" <?php if ($this->_var['group_buy']['group_type'] == $this->_var['type_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['type_name']; ?></option>
			    <!--<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>-->
			</select></td>
		    <td class="label" ><?php echo $this->_var['lang']['label_goods_type']; ?></td>
		    <td colspan="3"><select name="goods_type">
			    <!--<?php $_from = $this->_var['lang']['label_goods_type_name']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('type_id', 'type_name');if (count($_from)):
    foreach ($_from AS $this->_var['type_id'] => $this->_var['type_name']):
?>-->
			    <option value="<?php echo $this->_var['type_id']; ?>" <?php if ($this->_var['group_buy']['goods_type'] == $this->_var['type_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['type_name']; ?></option>
			    <!--<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>-->
			</select></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_start_date']; ?></td>
		    <td><input name="start_time" type="text" id="start_time" size="22" value='<?php echo $this->_var['group_buy']['formated_start_date']; ?>' onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" />
		    <td class="label"><?php echo $this->_var['lang']['label_end_date']; ?></td>
		    <td><input name="end_time" type="text" id="end_time" size="22" value='<?php echo $this->_var['group_buy']['formated_end_date']; ?>' onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" />
		    
		</tr>
		<tr>
		    <td class="label">团购券开始时间：</td>
		    <td id="selbtn4"><input name="past_time_start" type="text" id="past_time_start" size="22" value='<?php echo $this->_var['group_buy']['formated_past_date_start']; ?>' onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
		    <td class="label"><?php echo $this->_var['lang']['label_past_time']; ?></td>
		    <td id="selbtn3"><input name="past_time" type="text" id="past_time" size="22" value='<?php echo $this->_var['group_buy']['formated_past_date']; ?>' onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_city_name']; ?></td>
		    <td>
			<select name="city_id" style="border:1px solid #ccc;">
			    <option value="0"><?php echo $this->_var['lang']['please_select_city']; ?></option>
			    <!-- <?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?> -->
			    <option value="<?php echo $this->_var['city']['city_id']; ?>" <?php if ($this->_var['group_buy']['city_id'] == $this->_var['city']['city_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['city']['city_name']; ?></option>
			    <!-- <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> -->
			</select>
		    </td>
		    <td class="label">显示侧边栏</td>
		    <td>
			<select name="show_sidebar">
			    <option value="1" <?php if ($this->_var['group_buy']['show_sidebar']): ?> selected="selected" <?php endif; ?>>是</option>
			    <option value="0" <?php if ($this->_var['group_buy']['show_sidebar']): ?> selected="selected" <?php endif; ?>>否</option>
			</select>
		    </td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_expand_city']; ?></td>
		    <td colspan="5">
			<input type="button" value="<?php echo $this->_var['lang']['add']; ?>" onclick="addOtherCity(this.parentNode)" class="button" />
			<?php $_from = $this->_var['group_buy']['other_city']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city_id');if (count($_from)):
    foreach ($_from AS $this->_var['city_id']):
?>
			<select name="other_city[]" style="border:1px solid #ccc;">
			    <option value="0"><?php echo $this->_var['lang']['please_select_city']; ?></option>
			    <!-- <?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?> -->
			    <option value="<?php echo $this->_var['city']['city_id']; ?>" <?php if ($this->_var['city_id'] == $this->_var['city']['city_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['city']['city_name']; ?></option>
			    <!-- <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> -->
			</select>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		    </td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['lab_group_cat']; ?></td>
		    <td><select name="cat_id" onchange="hideCatDiv()" ><option value="0"><?php echo $this->_var['lang']['select_please']; ?></option><?php echo $this->_var['cat_list']; ?></select></td>
		    <td class="label"><?php echo $this->_var['lang']['label_suppliers']; ?></td>
		    <td colspan="3"><select name="suppliers_id" id="suppliers_id">
			    <option value="0"><?php echo $this->_var['lang']['suppliers_no']; ?></option>
			    <?php echo $this->html_options(array('options'=>$this->_var['suppliers_list_name'],'selected'=>$this->_var['group_buy']['suppliers_id'])); ?>
			</select></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_group_need']; ?></td>
		    <td colspan="5"><select name="group_need" style="border:1px solid #ccc;" onchange="changeRange(this.value)">
			    <!-- <?php $_from = $this->_var['lang']['group_need']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('need_id', 'need');if (count($_from)):
    foreach ($_from AS $this->_var['need_id'] => $this->_var['need']):
?> -->
			    <option value="<?php echo $this->_var['need_id']; ?>" <?php if ($this->_var['group_buy']['group_need'] == $this->_var['need_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['need']; ?></option>
			    <!-- <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> -->
			</select></td>
		</tr>
		<tr id="label_group_stock" <?php if ($this->_var['group_buy']['group_need'] == 1): ?>style="display:none"<?php endif; ?>>
		    <td class="label"><?php echo $this->_var['lang']['label_group_stock']; ?></td>
		    <td colspan="5"><input type="text" name="group_stock" value="<?php echo empty($this->_var['group_buy']['group_stock']) ? '0' : $this->_var['group_buy']['group_stock']; ?>" size="30" /></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_upper_orders']; ?></td>
		    <td><input type="text" name="upper_orders" value="<?php echo empty($this->_var['group_buy']['upper_orders']) ? '0' : $this->_var['group_buy']['upper_orders']; ?>" size="30" /><span class="notice-span" style="display:block"><?php echo $this->_var['lang']['notice_upper_orders']; ?></span></td>
		    <td class="label"><?php echo $this->_var['lang']['label_lower_orders']; ?></td>
		    <td colspan="3"><input type="text" name="lower_orders" value="<?php echo empty($this->_var['group_buy']['lower_orders']) ? '0' : $this->_var['group_buy']['lower_orders']; ?>" size="30" /><span class="notice-span" style="display:block"><?php echo $this->_var['lang']['notice_lower_orders']; ?></span></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['sel_group_restricted']; ?></td>
		    <td><input type="text" name="group_restricted" value="<?php echo empty($this->_var['group_buy']['group_restricted']) ? '0' : $this->_var['group_buy']['group_restricted']; ?>" size="30" /><span class="notice-span" style="display:block"><?php echo $this->_var['lang']['notice_group_restricted']; ?></span></td>
		    <td class="label"></td>
		    <td colspan="3"></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_group_rebate']; ?></td>
		    <td><input type="text" readonly="readonly" name="goods_rebate" value="<?php echo $this->_var['rebate']; ?>" size="30" /><span class="notice-span" style="display:block">如需修改，到“商店设置”</span></td>
		    <td class="label"><?php echo $this->_var['lang']['label_gift_integral']; ?></td>
		    <td colspan="3"><input type="text" name="gift_integral" value="<?php echo empty($this->_var['group_buy']['gift_integral']) ? '0' : $this->_var['group_buy']['gift_integral']; ?>" size="30" /></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_group_freight']; ?></td>
		    <td><input type="text" name="group_freight" value="<?php echo $this->_var['group_buy']['group_freight']; ?>" size="30" /></td>
		    <td class="label"><?php echo $this->_var['lang']['label_pos_express']; ?></td>
		    <td colspan="3"><input type="text" name="pos_express" value="<?php echo empty($this->_var['group_buy']['pos_express']) ? '0' : $this->_var['group_buy']['pos_express']; ?>" size="30" /><span class="notice-span" style="display:block"><?php echo $this->_var['lang']['notice_pos_express']; ?></span></td>
		</tr>
	    </table>
	    <table id="group_detail-table" width="80%" style="display:none">
		<tr>
		    <td><?php echo $this->_var['FCKeditor']; ?></td>
		</tr>
	    </table>
	    <table id="friend_comment-table" width="80%" style="display:none">
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['tab_friend_comment']; ?>：</td>
		    <td>
			<!--<?php if ($this->_var['friend_comment']): ?>-->
			<!--<?php $_from = $this->_var['friend_comment']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>-->
			<!--<?php if ($this->_var['key'] == 0): ?>-->
			<a href="javascript:;" onclick="addLadder(this,'friend_comment-table')"><strong>[+]</strong></a>
			<!--<?php else: ?>-->
			<a href="javascript:;" onclick="removeLadder(this,'friend_comment-table')"><strong>[-]</strong></a>
			<!--<?php endif; ?>-->
			<table>
			    <tr>
				<td>
				    <textarea name="friend_desc[]" cols="85" rows="6"><?php echo $this->_var['item']['friend_desc']; ?></textarea></td></tr>
			    <tr><td><?php echo $this->_var['lang']['friend_name']; ?><input name="friend_name[]" type="text" value="<?php echo $this->_var['item']['friend_name']; ?>"/><?php echo $this->_var['lang']['friend_url']; ?><input name="friend_url[]" type="text" value="<?php echo $this->_var['item']['friend_url']; ?>"/><?php echo $this->_var['lang']['friend_web']; ?><input name="friend_web[]" type="text" value="<?php echo $this->_var['item']['friend_web']; ?>"/></td>
			    </tr>
			</table>
			<!--<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>-->
			<!--<?php else: ?>-->
			<a href="javascript:;" onclick="addLadder(this,'friend_comment-table')"><strong>[+]</strong></a>
			<table>
			    <tr>
				<td>
				    <textarea name="friend_desc[]" cols="90" rows="6"><?php echo $this->_var['item']['friend_desc']; ?></textarea></td></tr>
			    <tr><td><?php echo $this->_var['lang']['friend_name']; ?><input name="friend_name[]" type="text" value="<?php echo $this->_var['item']['friend_name']; ?>"/>&nbsp;&nbsp;<?php echo $this->_var['lang']['friend_url']; ?><input name="friend_url[]" type="text" value="<?php echo $this->_var['item']['friend_url']; ?>"/>&nbsp;&nbsp;<?php echo $this->_var['lang']['friend_web']; ?><input name="friend_web[]" type="text" value="<?php echo $this->_var['item']['friend_web']; ?>"/></td>
			    </tr>
			</table>
			<!--<?php endif; ?>-->
		    </td>
		</tr>
	    </table>
	    <table id="tab_seo-table" width="80%" style="display:none">
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_group_seo']; ?></td>
		    <td><textarea name="group_keywords" cols="50" rows="5"><?php echo $this->_var['group_buy']['group_keywords']; ?></textarea></td>
		</tr>
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['label_group_seo_desc']; ?></td>
		    <td><textarea name="group_description" cols="50" rows="10"><?php echo $this->_var['group_buy']['group_description']; ?></textarea></td>
		</tr>
	    </table>

	    <table id="group_information-table" width="80%" style="display:none">
		<tr>
		    <td class="label"><?php echo $this->_var['lang']['lab_small_desc']; ?></td><td>
			<textarea name="small_desc" cols="50" rows="5"><?php echo $this->_var['group_buy']['small_desc']; ?></textarea>
		    </td>
		</tr>
		<tr>
		    <td class="label">打印券的提示：</td>
		    <td><?php echo $this->_var['goods_comment']; ?></td>
		</tr>
		<tr>
		    <td class="label">渝美团说：</td>
		    <td><?php echo $this->_var['group_comment']; ?></td>
		</tr>
		</tr>
		<tr>
		    <td class="label">轮转图片下部模板：</td>
		    <td><?php echo $this->_var['group_brief']; ?></td>
		</tr>

	    </table>
	    <!-- 属性与规格 -->
	    <?php if ($this->_var['group_attr_list']): ?>
	    <table width="90%" id="group_properties-table" style="display:none" align="center">
		<tr>
		    <td class="label" width="15%"><a href="javascript:('noticeGoodsType');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>"></a><?php echo $this->_var['lang']['lab_goods_type']; ?></td>
		    <td width="85%">
			<select name="group_attr" onchange="getAttrList(<?php echo $this->_var['group_buy']['group_id']; ?>)">
			    <option value="0"><?php echo $this->_var['lang']['sel_goods_type']; ?></option>
			    <?php echo $this->_var['group_attr_list']; ?>
			</select><br />
			<span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="noticeGoodsType"><?php echo $this->_var['lang']['notice_goods_type']; ?></span></td>
		</tr>
		<tr>
		    <td id="tbody-groupAttr" colspan="2" style="padding:0"><?php echo $this->_var['group_attr_html']; ?></td>
		</tr>
	    </table>
	    <?php endif; ?>
	    <table width="90%" id="gallery-table" style="display:none" align="center">
		<!-- 图片列表 -->
		<tr>
		    <td>
			<?php $_from = $this->_var['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('i', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['i'] => $this->_var['img']):
?>
			<div id="gallery_<?php echo $this->_var['img']['img_id']; ?>" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">
			    <a href="javascript:;" onclick="if (confirm('<?php echo $this->_var['lang']['drop_img_confirm']; ?>')) dropImg('<?php echo $this->_var['img']['img_id']; ?>')">[-]</a><br />
			    <a href="goods.php?act=show_image&amp;img_url=<?php echo $this->_var['img']['img_url']; ?>" target="_blank">
				<img src="../<?php echo $this->_var['img']['img_url']; ?>"  border="0" withd='100' height='100'/>
			    </a><br />
			    <input type="text" value="<?php echo htmlspecialchars($this->_var['img']['img_desc']); ?>" size="15" name="old_img_desc[<?php echo $this->_var['img']['img_id']; ?>]" />

			</div>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		    </td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<!-- 上传图片 -->
		<tr>
		    <td>
			<a href="javascript:;" onclick="addImg(this)">[+]</a>
			<?php echo $this->_var['lang']['img_desc']; ?> <input type="text" name="img_desc[]" size="20" />
			<?php echo $this->_var['lang']['img_url']; ?> <input type="file" name="img_url[]" />
			<input type="text" size="40" value="<?php echo $this->_var['lang']['img_file']; ?>" style="color:#aaa;" onfocus="if (this.value == '<?php echo $this->_var['lang']['img_file']; ?>'){this.value='http://';this.style.color='#000';}" name="img_file[]"/>(图片大小:460*290)
		    </td>
		</tr>

	    </table>
	    <!--视频/tun/2010/11/07-->
	    <table width="90%" id="video-table" style="display:none" align="center">
		<!-- 视频列表 -->
		<tr>
		    <td>
			<?php $_from = $this->_var['vid_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('i', 'vid');if (count($_from)):
    foreach ($_from AS $this->_var['i'] => $this->_var['vid']):
?>
			<div id="video_<?php echo $this->_var['vid']['vid_id']; ?>" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">
			    <a href="javascript:;" onclick="if (confirm('<?php echo $this->_var['lang']['drop_vid_confirm']; ?>')) dropVid('<?php echo $this->_var['vid']['vid_id']; ?>')">[-]</a><br />
				<?php echo $this->_var['vid']['vid_code']; ?><br />
			    视频描述：<input type="text" value="<?php echo htmlspecialchars($this->_var['vid']['vid_desc']); ?>" size="15" name="old_vid_desc[<?php echo $this->_var['vid']['vid_id']; ?>]" />
			    视频代码：<input type="text" value="<?php echo htmlspecialchars($this->_var['vid']['vid_code']); ?>" size="35" name="old_vid_code[<?php echo $this->_var['vid']['vid_id']; ?>]" /><br />
			    开始时间：<input  onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" type="text" value="<?php echo $this->_var['vid']['start_time']; ?>" size="22" id="old_v_start_time" name="old_v_start_time[<?php echo $this->_var['vid']['vid_id']; ?>]">
			    结束时间：<input  onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" type="text" value="<?php echo $this->_var['vid']['end_time']; ?>" size="22" id="old_v_end_time" name="old_v_end_time[<?php echo $this->_var['vid']['vid_id']; ?>]">
			</div>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		    </td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
		    <td>
			<a href="javascript:;" onclick="addVid(this)">[+]</a>
			<?php echo $this->_var['lang']['vid_desc']; ?>： <input type="text" name="vid_desc[]" size="20" />
			<?php echo $this->_var['lang']['vid_code']; ?>：<input type="text" size="40" maxlength="1024" name="vid_code[]"/>
			开始时间：<input onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" type="text" size="22" id="v_start_time" name="v_start_time[]">
			结束时间：<input onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" type="text" size="22" id="v_end_time" name="v_end_time[]">
		    </td>
		</tr>

	    </table>
	    <!--/视频-->
	    <table>
		<tr>
		    <td class="label">&nbsp;</td>
		    <td>
			<input name="group_id" type="hidden" id="group_id" value="<?php echo $this->_var['group_buy']['group_id']; ?>">
			<input name="old_end_time" type="hidden" id="old_end_time" value="<?php echo $this->_var['group_buy']['end_time']; ?>">
			<input type="submit" name="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
			<input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
		    </td>
		</tr>

	    </table>

	</div>
    </div>
</form>
<script language="JavaScript">
    <!--
    
    region.isAdmin = true;
    // 检查新订单
    startCheckOrder();

    /**
     * 检查表单输入的数据
     */
    function validate()
    {
	var msg = '';
	var eles = document.forms['theForm'].elements;
	if (eles['group_name'].value == '')
	{
	    msg +=  "- " + error_group_name + "\n";
	}

	if (eles['market_price'].value <= 0 || ! Utils.isNumber(eles['market_price'].value))
	{
	    msg +=  "- " + error_market_price + "\n";
	}
	/*if (eles['ladder_amount[]'].value != '1');
  {
	msg += '- 请设置数量达到1时,享受的价格是多少\n';
  }*/
	if(eles['ladder_price[]'].value == '')
	{
	    msg +=  "- " + error_group_price + "\n";
	}
	else
	{
	    var j = 0;
	    for(i=0; i < eles['ladder_price[]'].length; i++)
	    {
		if (eles['ladder_price[]'][i].value == '')
		{
		    j++;
		}
	    }
	    if (j == eles['ladder_price[]'].length)
	    {
		msg +=  "- " + error_group_price + "\n";
	    }
	}
	if (eles['start_time'].value == '')
	{
	    msg +=  "- " + error_start_time + "\n";
	}
	if (eles['end_time'].value == '')
	{
	    msg +=  "- " + error_end_time + "\n";
	}
	if (eles['goods_type'].value == 1)
	{
	    if (eles['past_time'].value == '')
	    {
		msg +=  "- " + error_past_time + "\n";
	    }
	    if (eles['goods_name'].value == '')
	    {
		msg +=  "- " + error_goods_name + "\n";
	    }
	}
	if (eles['suppliers_id'].value == 0)
	{
	    msg +=  "- " + error_suppliers_id + "\n";
	}
	if (eles['city_id'].value == 0)
	{
	    msg +=  "- " + error_city_id + "\n";
	}
	if (eles['lower_orders'].value <= 0)
	{
	    msg +=  "- " + error_lower_orders + "\n";
	}
	if (eles['group_freight'].value == '')
	{
	    msg +=  "- " + error_group_shipping + "\n";
	}

	if (msg != '')
	{
	    alert(msg);
	    return false;
	}
	else
	{
	    return true;
	}
    }
    function addOtherCity(conObj)
    {
	var sel = document.createElement("SELECT");
	var selCity = document.forms['theForm'].elements['city_id'];
	for (i = 0; i < selCity.length; i++)
	{
	    var opt = document.createElement("OPTION");
	    opt.text = selCity.options[i].text;
	    opt.value = selCity.options[i].value;
	    if (Browser.isIE)
	    {
		sel.add(opt);
	    }
	    else
	    {
		sel.appendChild(opt);
	    }
	}
	conObj.appendChild(sel);
	sel.name = "other_city[]";
	sel.onChange = function() {checkIsLeaf(this);};
    }

    /**
     * 新增一个价格阶梯
     */
    function addLadder(obj,table_obj, amount, price)
    {
	var src  = obj.parentNode.parentNode;
	var idx  = rowindex(src);
	var tbl  = document.getElementById(table_obj);
	var row  = tbl.insertRow(idx + 1);
	var cell = row.insertCell(-1);
	cell.innerHTML = '';
	var cell = row.insertCell(-1);
	cell.innerHTML = src.cells[1].innerHTML.replace(/(.*)(addLadder)(.*)(\[)(\+)/i, "$1removeLadder$3$4-");;
    }

    /**
     * 删除一个价格阶梯
     */
    function removeLadder(obj,table_obj)
    {
	var row = rowindex(obj.parentNode.parentNode);
	var tbl = document.getElementById(table_obj);

	tbl.deleteRow(row);
    }
    /**
     * 切换商品类型
     */
    function getAttrList(groupId)
    {
	var selGoodsType = document.forms['theForm'].elements['group_attr'];

	if (selGoodsType != undefined)
	{
	    var group_attr = selGoodsType.options[selGoodsType.selectedIndex].value;
	    Ajax.call('group_buy.php?is_ajax=1&act=get_attr', 'group_id=' + groupId + "&group_attr=" + group_attr, setAttrList, "GET", "JSON");
	}
    }

    function setAttrList(result, text_result)
    {
	document.getElementById('tbody-groupAttr').innerHTML = result.content;
    }

    /**
     * 新增一个规格
     */
    function addSpec(obj)
    {
	var src   = obj.parentNode.parentNode;
	var idx   = rowindex(src);
	var tbl   = document.getElementById('attrTable');
	var row   = tbl.insertRow(idx + 1);
	var cell1 = row.insertCell(-1);
	var cell2 = row.insertCell(-1);
	var regx  = /<a([^>]+)<\/a>/i;

	cell1.className = 'label';
	cell1.innerHTML = src.childNodes[0].innerHTML.replace(/(.*)(addSpec)(.*)(\[)(\+)/i, "$1removeSpec$3$4-");
	cell2.innerHTML = src.childNodes[1].innerHTML.replace(/readOnly([^\s|>]*)/i, '');
    }

    /**
     * 删除规格值
     */
    function removeSpec(obj)
    {
	var row = rowindex(obj.parentNode.parentNode);
	var tbl = document.getElementById('attrTable');

	tbl.deleteRow(row);
    }

    function addImg(obj)
    {
	var src  = obj.parentNode.parentNode;
	var idx  = rowindex(src);
	var tbl  = document.getElementById('gallery-table');
	var row  = tbl.insertRow(idx + 1);
	var cell = row.insertCell(-1);
	cell.innerHTML = src.cells[0].innerHTML.replace(/(.*)(addImg)(.*)(\[)(\+)/i, "$1removeImg$3$4-");
    }
    function addVid(obj)
    {
	var src  = obj.parentNode.parentNode;
	var idx  = rowindex(src);
	var tbl  = document.getElementById('video-table');
	var row  = tbl.insertRow(idx + 1);
	var cell = row.insertCell(-1);
	cell.innerHTML = src.cells[0].innerHTML.replace(/(.*)(addVid)(.*)(\[)(\+)/i, "$1removeVid$3$4-");
	cell.innerHTML = cell.innerHTML.replace(/selbtn_start_new/i, "selbtn_start_new"+tbl.rows.length);
	cell.innerHTML = cell.innerHTML.replace(/selbtn_end_new/i, "selbtn_end_new"+tbl.rows.length);
	//alert(tbl.rows.length);
    }

    /**
     * 删除图片上传
     */
    function removeImg(obj)
    {
	var row = rowindex(obj.parentNode.parentNode);
	var tbl = document.getElementById('gallery-table');

	tbl.deleteRow(row);
    }
    function removeVid(obj)
    {
	var row = rowindex(obj.parentNode.parentNode);
	var tbl = document.getElementById('video-table');

	tbl.deleteRow(row);
    }
    /**
     * 删除图片
     */
    function dropImg(imgId)
    {
	Ajax.call('group_buy.php?is_ajax=1&act=drop_image', "img_id="+imgId, dropImgResponse, "GET", "JSON");
    }
    function dropVid(vidId)
    {
	Ajax.call('group_buy.php?is_ajax=1&act=drop_video', "vid_id="+vidId, dropVidResponse, "GET", "JSON");
    }
    function dropImgResponse(result)
    {
	if (result.error == 0)
	{
	    document.getElementById('gallery_' + result.content).style.display = 'none';
	}
    }
    function dropVidResponse(result)
    {
	if (result.error == 0)
	{
	    document.getElementById('video_' + result.content).style.display = 'none';
	}
    }
    
    function changeRange(rangeId)
    {
	var row = document.getElementById('label_group_stock');
	if (rangeId == 1)
	{
	    row.style.display = 'none';
	}
	else
	{
	    row.style.display = '';
	}
    }
    //-->
    
</script>
<?php echo $this->smarty_insert_scripts(array('files'=>'tab.js')); ?>


<?php echo $this->fetch('pagefooter.htm'); ?>