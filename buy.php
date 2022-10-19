<?php

/**
 * 购买
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
include_once(ROOT_PATH . 'includes/cls_json.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/buy.php');
$json = new JSON;
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
if ($_SESSION['user_id'] <= 0)
{
    ecs_header("location:login.php\n");
}
else
{
    $user_id = $_SESSION['user_id'];
}
if (empty($_GET['a']))
{
    $_GET['a'] = 'cart';
}
assign_public($city_id);
assign_template();

if ($_REQUEST['a'] == 'cart')//'cart'
{
    include_once('includes/lib_order.php');
    include_once('includes/lib_transaction.php');
    $group_buy_id = intval($_GET['id']) > 0 ? $_GET['id'] : 0;
    //id过滤
    if ($group_buy_id == 0)
	exit;
    $group_buy = get_group_buy_info($group_buy_id);
    //状态过滤
    $status = get_group_buy_status($group_buy);
    if ($status != GBS_UNDER_WAY && $status != GBS_SUCCEED)
	exit;
    //余额
    $user_money = $db->getOne("SELECT user_money FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id='$user_id'");
    $user_money = empty($user_money) ? '0' : intval($user_money);
    /* 记录扩展信息 */
    $affiliate = unserialize($_CFG['affiliate']);
    if (isset($affiliate['on']) && $affiliate['on'] == 1 && $affiliate['config']['separate_by'] == 1)
    {
	//推荐订单分成
	$parent_id = get_affiliate();
	if ($user_id == $parent_id)
	{
	    $parent_id = 0;
	}
    }
    else
    {
	//分成功能关闭
	$parent_id = 0;
    }

    //判断是否订单已经存在
    $sql = "select order_id from " . $GLOBALS['ecs']->table('order_info') . " where extension_id=$group_buy_id and user_id=$user_id and  pay_status<>2 AND order_status = 0  ";
    $order_id_exists = $GLOBALS['db']->getOne($sql);

    $consignee = get_group_consignee($user_id);

    if ($order_id_exists == '')//新订单
    {
	/* 插入订单表 */
	$error_no = 0;
	do
	{
	    $order = array(
		'order_sn' => get_order_sn(),
		'user_id' => $user_id,
		'shipping_id' => intval($_CFG['group_shipping']),
		'shipping_fee' => $group_buy['group_freight'],
		'pay_id' => 0,
		'postscript' => '',
		'add_time' => gmtime(),
		'order_status' => OS_UNCONFIRMED,
		'shipping_status' => SS_UNSHIPPED,
		'pay_status' => PS_UNPAYED,
		'extension_code' => 'group_buy',
		'extension_id' => $group_buy['group_id'],
		'parent_id' => $parent_id,
		'goods_amount' => $group_buy['group_price'],
		'order_amount' => $group_buy['group_price'] + $group_buy['group_freight'],
		'surplus' => 0
	    );
	    if ($consignee)
	    {
		foreach ($consignee as $key => $value)
		{
		    $order[$key] = addslashes($value);
		}
	    }
	    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order, 'INSERT');

	    $error_no = $GLOBALS['db']->errno();

	    if ($error_no > 0 && $error_no != 1062)
	    {
		die($GLOBALS['db']->errorMsg());
	    }
	}
	while ($error_no == 1062); //如果是订单号重复则重新提交数据

	$new_order_id = $db->insert_id();
	/* 插入订单商品 */
	$sql = "INSERT INTO " . $ecs->table('order_goods') . "( " .
		"order_id, goods_id, goods_name, goods_sn, goods_number, market_price, " .
		"goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id) values" .
		" ($new_order_id, $group_buy[group_id], '$group_buy[group_name]','', 1, $group_buy[market_price], " .
		"$group_buy[group_price],'' , $group_buy[goods_type] , 'group_buy', $parent_id, 0 , '' )";
	$db->query($sql);
	//插入支付记录
	require_once('includes/lib_clips.php');
	insert_pay_log($new_order_id, $order['order_amount'], PAY_ORDER);
    }
    else
    {
	$new_order_id = $order_id_exists;
    }
    //团购信息
    $group_info = get_group_insert($group_buy_id, 1);
    //送货人
    //$consignee = get_group_consignee($user_id);
    $province_list = get_regions(1, 1);
    $city_list = get_regions(2, $consignee['province']);
    $district_list = get_regions(3, $consignee['city']);
    //订单信息
    $order_info = get_order_detail($new_order_id);
    $sql = "select * from " . $GLOBALS['ecs']->table('order_goods') . " where order_id=$new_order_id";
    $order_goods = $GLOBALS['db']->getRow($sql);

    //购物车信息
    $cart_info = array(
	'group_id' => $group_buy['group_id'],
	'goods_name' => $group_buy['group_name'],
	'goods_price' => $group_buy['group_price'],
	'subtotal' => $order_info['goods_amount'],
	'shipping_fee' => $group_buy['group_freight'],
	'goods_amount' => $order_info['goods_amount'] + $group_buy['group_freight'],
	'user_money' => $user_money,
	'order_id' => $new_order_id,
	'goods_number' => $order_goods['goods_number'],
	'postscript' => $order_info['postscript']
    );
    //assign_public($city_id);
    $smarty->assign('goods_type', $group_buy['goods_type']);
    $smarty->assign('consignee', $consignee);
    $smarty->assign('group_arr', $cart_info);
    $smarty->assign('province_list', $province_list);
    $smarty->assign('city_list', $city_list);
    $smarty->assign('district_list', $district_list);
    $smarty->display('group_cart.dwt');
    exit;
}
elseif ($_REQUEST['a'] == 'update') //'update'
{
    $num = intval($_POST['number']) > 0 ? intval($_POST['number']) : '1';
    $order_id = intval($_POST['order_id']);
    if ($order_id <= 0)
	exit;
    //验证订单所有者
    $user_id_db = $GLOBALS['db']->getOne("select user_id from  " . $GLOBALS['ecs']->table('order_info') . " where order_id=$order_id");
    if ($user_id_db != $user_id)
	exit;

    else
    { //更新order_goods
	$sql = "select * from " . $GLOBALS['ecs']->table('order_goods') . " where order_id=$order_id";
	$order_goods = $GLOBALS['db']->getRow($sql);
	$order_goods['goods_number'] = $num;
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_goods'), $order_goods, 'UPDADTE', 'order_id=' . $order_id);
	//更新order_info
	$sql = "select * from " . $GLOBALS['ecs']->table('order_info') . " where order_id=$order_id";
	$order_info = $GLOBALS['db']->getRow($sql);
	$order_info['goods_amount'] = $order_goods['goods_number'] * $order_goods['goods_price'];
	$order_info['order_amount'] = $order_info['goods_amount'] + $order_info['shipping_fee'] - $order_info['surplus'];
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order_info, 'UPDADTE', 'order_id=' . $order_id);
	//更新pay_log
	require_once('includes/lib_clips.php');
	update_pay_log($order_info['order_id'], $order_info['order_amount'], PAY_ORDER);
    }
}
elseif ($_REQUEST['a'] == 'address') //'address'
{
    $order_id = intval($_POST['orderid']);
    if ($order_id <= 0)
	exit;
    //验证订单所有者
    $user_id_db = $GLOBALS['db']->getOne("select user_id from  " . $GLOBALS['ecs']->table('order_info') . " where order_id=$order_id");
    if ($user_id_db != $user_id)
	exit;
    //验证address_id
    $address_id_post = intval($_POST['address_id']);
    $address_id_db = $GLOBALS['db']->getOne(" select address_id from " . $GLOBALS['ecs']->table('users') . " where user_id=$user_id");
    if ($address_id_db != $address_id_post)
	exit;


    $consignee = array(
	'address_id' => empty($_POST['address_id']) ? 0 : intval($_POST['address_id']),
	'consignee' => empty($_POST['adrName']) ? '' : trim($_POST['adrName']),
	'country' => 1,
	'province' => empty($_POST['province']) ? '' : $_POST['province'],
	'city' => empty($_POST['city']) ? '' : $_POST['city'],
	'district' => empty($_POST['district']) ? '' : $_POST['district'],
	'address' => empty($_POST['adrAddress']) ? '' : $_POST['adrAddress'],
	'zipcode' => empty($_POST['adrZipCode']) ? '' : make_semiangle(trim($_POST['adrZipCode'])),
	'mobile' => empty($_POST['mobile']) ? '' : make_semiangle(trim($_POST['mobile'])),
	'best_time' => empty($_POST['best_time']) ? '' : $_POST['best_time'],
    );
    //更新user_address表
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $consignee['user_id'] = $user_id;
    if (isset($_POST['address']))
    {
	save_consignee($consignee, true);
    }
    //更新order_info里面的地址信息
    $sql = "select * from " . $GLOBALS['ecs']->table('order_info') . " where order_id=$order_id";
    $order_info = $GLOBALS['db']->getRow($sql);
    unset($consignee['address_id']);
    $order_info['postscript'] = empty($_POST['deliveryComment']) ? '' : $_POST['deliveryComment'];

    foreach ($consignee as $key => $value)
    {
	$order_info[$key] = addslashes($value);
    }
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order_info, 'UPDADTE', 'order_id=' . $order_id);
    //
    ecs_header("location:check.php?id=$order_id\n");
}
elseif ($_GET['a'] == 'pay') //'pay'/余额付款
{
    $id = intval($_POST['orderid']);
    //验证订单用户非法
    $sql = "SELECT order_id,order_sn,pay_id,goods_amount, order_amount,bonus," .
	    "surplus,mobile,extension_id,pay_status,order_status,parent_id,user_id FROM " .
	    $ecs->table('order_info') . " WHERE order_id='$id' AND user_id='$user_id'";
    $order = $db->getRow($sql);
    if (empty($order))
    {
	ecs_header("Location: index.php\n");
	exit;
    }
    //验证 订单/付款 状态
    if ($order['order_status'] == OS_CANCELED || $order['order_status'] == OS_INVALID || $order['order_status'] == OS_RETURNED)
    {
	$msg = array(OS_CANCELED => '已取消', OS_INVALID => '无效', OS_RETURNED => '已退货');
	show_group_message2('此订单' . $msg[$order['order_status']] . ',您不能付款了!', '', "myorders.php", 'error');
	exit;
    }
    if ($order['pay_status'] == PS_PAYING || $order['pay_status'] == PS_PAYED)
    {
	show_group_message2('此订单已付款!', '', "myorders.php", 'error');
	exit;
    }
    //余额处理
    $user_money = $db->getOne("SELECT user_money FROM " . $ecs->table('users') . " WHERE user_id='$user_id'");
    $user_money = !empty($user_money) ? $user_money : '0';
    $user_money = min($order['order_amount'], $user_money);
    $order['order_amount'] = $order['order_amount'] - $user_money;
    $update_sql = '';
    if ($user_money > 0)
    {
	$surplus = $order['surplus'] + $user_money;
	$update_sql = ",surplus=$surplus";
    }
    if ($order['order_amount'] <= 0)
    {
	$order['order_status'] = OS_CONFIRMED;
	$order['confirm_time'] = gmtime();
	$order['pay_status'] = PS_PAYED;
	$order['pay_time'] = gmtime();
	$update_sql .= ",order_status='" . OS_CONFIRMED . "',confirm_time='" . gmtime() .
		"',pay_status='" . PS_PAYED . "',pay_time='" . gmtime() . "'";
    }
    //log
    $update_sql .= ",order_amount='$order[order_amount]'";
    $sql = "UPDATE " . $ecs->table('order_info') . " SET pay_id='$pay_id',pay_name='$pay_name' $update_sql WHERE order_id='$id'";
    $db->query($sql);
    $sql = "SELECT log_id FROM " . $ecs->table('pay_log') . " WHERE order_id='$id' LIMIT 1";
    $order['log_id'] = $db->getOne($sql);
    if ($user_id > 0 && $user_money > 0)
    {
	log_account_change($user_id, $user_money * (-1), 0, 0, 0, sprintf($_LANG['pay_order'], $order['order_sn']));
    }
    if ($order['order_amount'] == '0')
    {
	set_group_rebate($order['extension_id'], $order['parent_id'], $order['user_id'], $order['order_id'], $order['order_sn']);
	set_group_stats($order['extension_id']);
	$sql = "SELECT is_real FROM " . $ecs->table('order_goods') .
		" WHERE order_id='$id' AND goods_id='$order[extension_id]'";
	$is_real = $db->getOne($sql);
	if (($is_real == '1' && !$GLOBALS['_CFG']['make_group_card']) || $is_real == '3')
	{
	    $is_send = !$GLOBALS['_CFG']['send_group_sms'];
	    send_group_cards($order['order_id'], $order['order_sn'], $_SESSION['user_id'], $order['mobile'], $is_send);
	}
    }
    //得到用户的电话/tun
    $mobile_phone = $GLOBALS['db']->getOne("SELECT mobile_phone FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id=" . $user_id);

    $order['formated_order_amount'] = group_price_format($order['order_amount']);
    $group_buy = get_group_buy_info($order['extension_id']);
    $smarty->assign('order', $order);
    $smarty->assign('group_buy', $group_buy);
    $smarty->assign('mobile_phone', $mobile_phone);
    $smarty->assign('uid', $user_id);
    $smarty->assign('shop_url', $ecs->url());
    $smarty->display('respond.dwt');
}