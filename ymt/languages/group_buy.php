<?php


/**
 * ECGROUPON 管理中心团购商品语言文件
 * ============================================================================
 * 网站地址: http://www.ecgroupon.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/

/* 当前页面标题及可用链接名称 */
$_LANG['group_buy_list'] = '团购活动列表';
$_LANG['add_group_buy'] = '添加团购活动';
$_LANG['edit_group_buy'] = '编辑团购活动';

/* 活动列表页 */
$_LANG['goods_name'] = '商品名称';
$_LANG['start_date'] = '开始时间';
$_LANG['end_date'] = '结束时间';
$_LANG['gift_integral'] = '赠送积分';
$_LANG['current_price'] = '团购价';
$_LANG['current_status'] = '状态';
$_LANG['view_order'] = '查看订单';
$_LANG['view_card'] = '查看团购卷';

/* 添加/编辑活动页 */
$_LANG['goods_cat'] = '商品分类';
$_LANG['all_cat'] = '所有分类';
$_LANG['label_goods_name'] = '团购商品：';
$_LANG['notice_goods_name'] = '请先搜索商品,在此生成选项列表...';
$_LANG['label_start_date'] = '开始时间：';
$_LANG['label_end_date'] = '结束时间：';
$_LANG['notice_datetime'] = '（年月日－时）';
$_LANG['label_restrict_amount'] = '限购数量：';
$_LANG['notice_restrict_amount']= '达到此数量，团购活动自动结束。0表示没有数量限制。';
$_LANG['label_gift_integral'] = '赠送积分数：';
$_LANG['label_price_ladder'] = '价格阶梯：';
$_LANG['notice_ladder_amount'] = '数量达到';
$_LANG['notice_ladder_price'] = '享受价格';
$_LANG['label_status'] = '活动当前状态：';
$_LANG['gbs'][GBS_PRE_START] = '未开始';
$_LANG['gbs'][GBS_UNDER_WAY] = '进行中';
$_LANG['gbs'][GBS_FINISHED] = '成功结束';
$_LANG['gbs'][GBS_SUCCEED] = '已成团';
$_LANG['gbs'][GBS_FAIL] = '团购失败';
$_LANG['gbs'][5] = '卖光啦';
$_LANG['label_order_qty'] = '已付款/全部';
$_LANG['label_goods_qty'] = '支付统计';
$_LANG['label_cur_price'] = '当前价：';
$_LANG['label_end_price'] = '最终价：';
$_LANG['label_handler'] = '操作：';
$_LANG['error_status'] = '当前状态不能执行该操作！';
$_LANG['invalid_time'] = '您输入了一个无效的团购时间。';
$_LANG['add_success'] = '添加团购活动成功。';
$_LANG['edit_success'] = '编辑团购活动成功。';
$_LANG['back_list'] = '返回团购活动列表。';
$_LANG['continue_add'] = '继续添加团购活动。';
/* 添加/编辑活动提交 */
$_LANG['error_price_ladder'] = '您没有输入有效的价格阶梯！';
$_LANG['error_restrict_amount'] = '限购数量不能小于价格阶梯中的最大数量';
$_LANG['error_group_restricted'] = '每人限购不能小于免运费数量！';
$_LANG['error_lower_orders'] = '团购下限必须大于0！';
$_LANG['error_upper_orders'] = '团购上限必须大于或者等于团购下限';
$_LANG['error_time'] = '请选择开始时间和结束时间';
$_LANG['error_past_time'] = '团购卷过期时间必须大于活动结束时间';
$_LANG['error_goods_name'] = '请输入商品名称，发短信时使用';
$_LANG['js_languages']['error_gift_integral'] = '您输入的赠送积分数不是整数！';
$_LANG['js_languages']['search_is_null'] = '没有搜索到任何商品，请重新搜索';
$_LANG['js_languages']['error_group_name'] = '请输入团购活动名称！';
$_LANG['js_languages']['error_market_price'] = '请输入商品市场价！';
$_LANG['js_languages']['error_start_time'] = '请输入活动开始时间！';
$_LANG['js_languages']['error_end_time'] = '请输入活动结束时间！';
$_LANG['js_languages']['error_past_time'] = '请输入团购卷过期时间！';
$_LANG['js_languages']['error_group_image'] = '请上传活动缩略图！';
$_LANG['js_languages']['error_goods_name'] = '请输入商品名称！';
$_LANG['js_languages']['error_suppliers_id'] = '请选择供货商！';
$_LANG['js_languages']['error_city_id'] = '请选择团购城市！';
$_LANG['js_languages']['error_lower_orders'] = '请输入团购下限！';
$_LANG['js_languages']['error_group_shipping'] = '请输入运费！';
$_LANG['js_languages']['error_group_price'] = '请输入团购价格！';
$_LANG['js_languages']['error_ladder_amount'] = '请设置数量达到1时,享受的价格是多少';

/* 删除团购活动 */

$_LANG['js_languages']['batch_drop_confirm'] = '您确定要删除选定的团购活动吗？';

$_LANG['error_exist_order'] = '该团购活动已经有订单，不能删除！';

$_LANG['batch_drop_success'] = '成功删除了 %s 条团购活动记录（已经有订单的团购活动不能删除）。';

$_LANG['no_select_group_buy'] = '您现在没有团购活动记录！';



/* 操作日志 */

$_LANG['log_action']['group_buy'] = '团购商品';

$_LANG['label_type_name'] = '首页位置';

$_LANG['label_group_type'][1] = '左侧';

$_LANG['label_group_type'][2] = '右侧';

$_LANG['label_upper_orders'] = '团购上限：';

$_LANG['upper_orders'] = '团购上限';

$_LANG['notice_upper_orders']= '达到此数量，团购活动自动结束。0表示没有数量限制。';

$_LANG['label_lower_orders'] = '团购下限：';

$_LANG['notice_lower_orders']= '达到此数量，表示此次团购活动成功，并可以继续购买，直到团购上限为止。';


$_LANG['label_all_orders'] = '团购订单数：';

$_LANG['notice_all_orders']= '此次团购所有的订单数(包含付款和未付款)。';



$_LANG['label_actual_orders'] = '团购有效订单数：';

$_LANG['notice_actual_orders']= '此次团购付款的订单数。';

$_LANG['label_city_name'] = '团购所在地区：';
$_LANG['label_expand_city'] = '团购扩展地区：';

$_LANG['please_select_city'] = '请选择所在城市';

$_LANG['label_pos_express'] = '免邮费：';

$_LANG['notice_pos_express'] = '当商品数量达到此值时，订单免邮费,0为不免邮费';

$_LANG['label_group_name'] = '团购活动名称：';

$_LANG['group_name'] = '团购活动名称';

$_LANG['tab_gallery'] = '商品图片';

$_LANG['tab_video'] = '商品视频';

$_LANG['img_desc'] = '图片描述';

$_LANG['vid_desc'] = '视频描述';

$_LANG['img_url'] = '上传文件';

$_LANG['vid_code']='视频代码';

$_LANG['drop_vid_confirm']='删除此视频？';

$_LANG['drop_img_confirm']='删除此图片？';

$_LANG['img_file'] = '或者输入外部图片链接地址';

$_LANG['view_group'] = '预览';

$_LANG['tab_group_name'] = '团购活动基本信息';

$_LANG['tab_group_detail'] = '团购详细信息';

$_LANG['label_group_image'] = '活动缩略图：';
$_LANG['notice_group_imge'] = '此图用以右侧团购列表和往期团购中使用(图片大小：206*130)';

$_LANG['label_market_price'] = '市场价：';

$_LANG['label_suppliers'] = '选择供货商：';

$_LANG['suppliers_no'] = '选择供货商';

$_LANG['tab_other_information'] = '其他信息';

$_LANG['tab_friend_comment'] = '网友点评';

$_LANG['friend_name'] = '用户名称：';

$_LANG['friend_url'] = '点评网址：';

$_LANG['friend_web'] = '网站名称：';

$_LANG['tab_seo'] = 'SEO推广';

$_LANG['label_group_rebate'] = '商品返利：';

$_LANG['label_goods_type'] = '商品类型：';

$_LANG['label_goods_type_name'][1] = '团购券，序列号+密码';

$_LANG['label_goods_type_name'][2] = '实体商品，需要配送';

$_LANG['label_group_seo'] = 'SEO关键词：';

$_LANG['label_group_seo_desc'] = 'SEO描述:';

$_LANG['label_goods_comment'] = '商品点评：';

$_LANG['label_group_comment'] = '本站点评：';

$_LANG['label_group_brief'] = '商品简介：';

$_LANG['lab_group_cat'] = '团购分类：';

$_LANG['select_please'] = '请选择';

$_LANG['label_group_stock'] = '商品库存：';

$_LANG['label_group_stock'] = '商品库存：';

$_LANG['label_group_properties'] = '商品属性';

$_LANG['spec_num'] = '属性数量';

$_LANG['spec_price'] = '属性价格';

$_LANG['lab_goods_type'] = '商品属性：';

$_LANG['sel_goods_type'] = '请选择商品属性';

$_LANG['sel_group_restricted'] = '每人限购：';

$_LANG['notice_group_restricted'] = '0表示不限数量';

$_LANG['lab_small_desc'] = '一句话描述：';

$_LANG['label_past_time'] = '团购卷过期时间：';

$_LANG['card_sn'] = '团购卷号';

$_LANG['card_password'] = '团购密码';

$_LANG['user_name'] = '用户';

$_LANG['user_mobile'] = '手机号';

$_LANG['label_goods_name'] = '商品名称：';

$_LANG['down_order'] = '下载订单';

$_LANG['down_card'] = '下载团购卷';

$_LANG['label_group_stat'] = '团购状态：';

$_LANG['select_stat'] = '请选择状态 ';

$_LANG['select_suppliers'] = '请选择供货商';

$_LANG['label_group_need'] = '成团条件：';

$_LANG['label_group_freight'] = '运费：';

$_LANG['group_need'] = array('1' => '以产品购买数量成团', '2'=>'以付款订单数成团','3'=>'以购买成功人数成团');

?>