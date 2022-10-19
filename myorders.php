<?php

/**
 * 我的订单
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];

$user_id = intval($_SESSION['user_id']);
if ($user_id <= '0')
{
    ecs_header("Location: login.php\n");
    exit;
}
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
/* 查看订单详情 */
if ($_GET['act'] == 'info')
{
    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 订单详情 */
    $order = get_orders_info($order_id, $user_id);
    //print_r($order);
    if ($order === false)
    {
	ecs_header("location:index.php\n");
    }

    /* 订单商品 */
    $sql = "SELECT og.rec_id, og.goods_id, ga.goods_name, og.goods_sn, og.market_price, og.goods_number, " .
	    "og.goods_price, og.goods_attr, og.is_real, og.parent_id, og.is_gift, " .
	    "og.goods_price * og.goods_number AS subtotal, og.extension_code,ga.group_image,ga.is_finished " .
	    "FROM " . $GLOBALS['ecs']->table('order_goods') . " AS og," . $GLOBALS['ecs']->table('group_activity') . " AS ga" .
	    " WHERE og.goods_id=ga.group_id AND og.order_id = '$order_id'";

    $res = $GLOBALS['db']->query($sql);
    $goods_list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
	$row['formated_market_price'] = group_price_format($row['market_price']);
	$row['formated_goods_price'] = group_price_format($row['goods_price']);
	$row['formated_subtotal'] = group_price_format($row['subtotal']);
	if ($row['is_real'] == 1)
	{
	    $sql = 'SELECT card_sn,card_password FROM ' . $ecs->table('group_card') .
		    " WHERE order_sn='$order[order_sn]' AND group_id='$row[goods_id]' AND is_saled='1'";
	    $row['group_cards'] = $db->getAll($sql);
	}
	$row['group_url'] = rewrite_groupurl('index.php', array('id' => $row['goods_id']));
	$goods_list[] = $row;
    }
    /* 订单 支付 配送 状态语言项 */
    $order['order_status'] = $_LANG['os'][$order['order_status']];
    $order['pay_status'] = $_LANG['ps'][$order['pay_status']];
    $order['shipping_status'] = $_LANG['ss'][$order['shipping_status']];
    $order['order_time'] = $order['add_time'];
    //var_dump($order);
    assign_public($city_id);
    $smarty->assign('order', $order);
    $smarty->assign('menu', 'order');
    $smarty->assign('goods_list', $goods_list);
    $smarty->display('group_myorders_info.dwt');
}

/* 取消订单 */
elseif ($_GET['act'] == 'cancel_order')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');

    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if (cancel_order($order_id, $user_id))
    {
	ecs_header("Location: myorders.php\n");
	exit;
    }
    else
    {
	$err->show($_LANG['order_list_lnk'], 'myorders.php');
    }
}
elseif ($action == 'affirm_received')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (affirm_received($order_id, $user_id))
    {
	ecs_header("Location: myorders.php\n");
	exit;
    }
    else
    {
	$err->show($_LANG['order_list_lnk'], 'myorders.php');
    }
}
else
{
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $act = isset($_REQUEST['act']) ? ($_REQUEST['act']) : 'all';
    if ($act == 'unpaid')
    {
	$p_status = 'AND pay_status = 0 ';
    }
    elseif ($act == 'paid')
    {
	$p_status = 'AND pay_status = 2 ';
    }
    else
    {
	$p_status = 'AND 1=1 ';
    }
    $record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('order_info') . " WHERE user_id = '$user_id' $p_status");

    $pager = get_pager('myorders.php', array('act' => $act), $record_count, $page);

    $orders = get_orders_list($user_id, $pager['size'], $pager['start'], $p_status);
    assign_public($city_id);

    //用户购买份数
    $bought_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('group_card') .
		    " WHERE user_id='$user_id'");
    //节省钱数
    $save_money = $db->getOne("
	SELECT sum(market_price)-sum(goods_price) FROM" . $ecs->table('order_goods') . " where
	order_id in (select order_id from" . $ecs->table('order_info') . "where user_id=$user_id and order_status=1 or order_status=2)
    ");
    $smarty->assign('bought_count', $bought_count);
    $smarty->assign('save_money', $save_money);

    $smarty->assign('menu', 'order');
    $smarty->assign('pager', $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('group_myorders.dwt');
}

/**
 *  获取用户指定范围的订单列表
 *
 * @access  public
 * @param   int         $user_id        用户ID号
 * @param   int         $num            列表最大数量
 * @param   int         $start          列表起始位置
 * @return  array       $order_list     订单列表
 */
function get_orders_list($user_id, $num = 10, $start = 0, $p_status)
{
    /* 取得订单列表 */
    $arr = array();
    $sql = "SELECT o.order_id, o.order_sn, o.card_id, o.order_status, o.shipping_status, o.pay_status, o.add_time, " .
	    "(o.goods_amount + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee + o.tax - o.discount) AS total_fee " .
	    ",ga.group_id,ga.end_time,ga.group_image,ga.group_name,ga.goods_name,og.goods_number,goods_type" .
	    " FROM " . $GLOBALS['ecs']->table('order_info') . " AS o" .
	    " LEFT JOIN " . $GLOBALS['ecs']->table('group_activity') . " AS ga ON o.extension_id = ga.group_id" .
	    " LEFT JOIN " . $GLOBALS['ecs']->table('order_goods') . " AS og ON og.order_id = o.order_id" .
	    " WHERE o.extension_code='group_buy' AND o.user_id = '$user_id'  $p_status  ORDER BY o.add_time DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);
    $now = gmtime();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
	$sql="select card_id from ".$GLOBALS['ecs']->table('group_card')." where order_sn = $row[order_sn]";
	$row['card_id'] = $GLOBALS['db']->getOne($sql);
	if ($row['order_status'] == OS_UNCONFIRMED)
	{
	    if ($row['end_time'] < $now)
	    {
		@$row['handler'] = '已结束';
	    }
	    else
	    {
		$row['handler'] = "<a href=\"check.php?id=" . $row['order_id'] . "\">付款</a>";
		$row['handler'] .= " <a href=\"myorders.php?act=cancel_order&id=" . $row['order_id'] . "\" onclick=\"if (!confirm('" . $GLOBALS['_LANG']['confirm_cancel'] . "')) return false;\">". 取消 ."</a>";
		if ($row['goods_type'] == 2)
		{
		    $row['handler'] .= "<a href=\"myorders.php?act=info&id=" . $row['order_id'] . '">'. 详情 .'</a>';
		}
	    }
	}
	else if ($row['order_status'] == OS_CONFIRMED)
	{
	    if ($row['pay_status'] == PS_UNPAYED)
	    {
		if ($row['end_time'] < $now)
		{
		    @$row['handler'] = '已结束';
		}
		else
		{
		    $row['handler'] = "<a href=\"check.php?id=" . $row['order_id'] . "\">付款</a>";
		}
	    }
	    else
	    {
		if ($row['goods_type'] == 2)
		{
		    $row['handler'] .= "<a href=\"myorders.php?act=info&id=" . $row['order_id'] . '">'. 查看订单信息 .'</a>';
		}
		if ($row['goods_type'] == 1)
		{
		    $row['handler'] = '<a href="coupons.php?id='. $row['card_id'] .'">查看渝美券</a>';
		}
		
	    }
	}
	else if ($row['order_status'] == OS_SPLITED)
	{
	    /* 对配送状态的处理 */
	    if ($row['shipping_status'] == SS_SHIPPED)
	    {
		@$row['handler'] = "<a href=\"myorders.php?act=affirm_received&id=" . $row['order_id'] . "\" onclick=\"if (!confirm('" . $GLOBALS['_LANG']['confirm_received'] . "')) return false;\">" . $GLOBALS['_LANG']['received'] . "</a>";
	    }
	    elseif ($row['shipping_status'] == SS_RECEIVED)
	    {
		@$row['handler'] = '<span style="color:red">' . $GLOBALS['_LANG']['ss_received'] . '</span>';
	    }
	    else
	    {
		if ($row['pay_status'] == PS_UNPAYED)
		{
		    if ($row['end_time'] < $now)
		    {
			@$row['handler'] = '<span style="color:red">已过期</span>';
		    }
		    else
		    {
			@$row['handler'] = "<a href=\"check.php?id=" . $row['order_id'] . "\">付款</a>";
		    }
		}
		else
		{
		    @$row['handler'] = "<a href=\"myorders.php?act=info&id=" . $row['order_id'] . '">' . $GLOBALS['_LANG']['view_order'] . '</a>';
		}
	    }
	}
	else
	{
	    $row['handler'] = '<span style="color:red">' . $GLOBALS['_LANG']['os'][$row['order_status']] . '</span>';
	}

	$row['shipping_status'] = ($row['shipping_status'] == SS_SHIPPED_ING) ? SS_PREPARING : $row['shipping_status'];
	//$row['order_status'] = $GLOBALS['_LANG']['os'][$row['order_status']] . '<br />' . $GLOBALS['_LANG']['ps'][$row['pay_status']]; //. ',' . $GLOBALS['_LANG']['ss'][$row['shipping_status']];
	$row['order_status'] = $GLOBALS['_LANG']['ps'][$row['pay_status']];

	$arr[] = array(
	    'order_id' => $row['order_id'],
	    'order_sn' => $row['order_sn'],
	    'group_url' => 'index.php?id=' . $row['group_id'],
	    'order_time' => local_date($GLOBALS['_CFG']['time_format'], $row['add_time']),
	    'order_status' => $row['order_status'],
	    'pay_status' => $row['pay_status'],
	    'total_fee' => $row['total_fee'],
	    'formated_total_fee' => group_price_format($row['total_fee']),
	    'handler' => $row['handler'],
	    'group_image' => $row['group_image'],
	    'group_name' => $row['group_name'],
	    'goods_name' => $row['goods_name'],
	    'goods_number' => $row['goods_number'],
	    'card_id'	=> $row['card_id']
	);
    }
    return $arr;
}

function get_orders_info($order_id, $user_id)
{
    /* 取得订单列表 */
    $arr = array();

    $sql = "SELECT o.order_id, o.order_sn, o.order_status, o.shipping_status, o.pay_status, o.add_time,o.consignee,o.province,o.city,o.district,o.address,o.mobile,o.postscript, " .
	    "(o.goods_amount + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee + o.tax - o.discount) AS total_fee " .
	    ",ga.end_time" .
	    " FROM " . $GLOBALS['ecs']->table('order_info') . " AS o" .
	    " LEFT JOIN " . $GLOBALS['ecs']->table('group_activity') . " AS ga ON o.extension_id = ga.group_id" .
	    " WHERE o.extension_code='group_buy' AND o.user_id = '$user_id' AND order_id = '$order_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if ($row)
    {
	$now = gmtime();
	$row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
	$row['order_amount'] = $row['total_fee'];
	$row['formated_order_amount'] = group_price_format($row['total_fee']);
	$row['province'] = ($GLOBALS['db']->getOne("select region_name from " . $GLOBALS['ecs']->table('region') . " where region_id=$row[province]"));
	$row['city'] = ($GLOBALS['db']->getOne("select region_name from " . $GLOBALS['ecs']->table('region') . " where region_id=$row[city]"));
	$row['district'] = ($GLOBALS['db']->getOne("select region_name from " . $GLOBALS['ecs']->table('region') . " where region_id=$row[district]"));
    }
    return $row;
}

?>