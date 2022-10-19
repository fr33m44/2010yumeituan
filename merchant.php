<?php

/**
 * 商家后台
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/merchant.php');

$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];

$action = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'settings';
if (in_array($action, array('coupons', 'group', 'settings', 'act_settings', 'logout', 'set_coupons')))
{
    $suppliers_id = $_SESSION['suppliers_id'];
    if ($suppliers_id <= '0')
    {
	ecs_header("Location: merchant.php?act=login \n");
	exit;
    }
    else
    {
	$smarty->assign('suppliers_id', $suppliers_id);
    }
}
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];

assign_public($city_id);
if ($action == 'login')
{
    $smarty->assign('action', 'login');
    $smarty->display('group_merchant.dwt');
}
elseif ($action == 'act_login')
{
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : 'merchant.php?act=group';
    if (empty($username) || empty($password))
    {
	show_group_message($_LANG['login_failure'], $_LANG['relogin_lnk'], 'merchant.php?act=login', 'error');
    }
    $sql = "SELECT suppliers_id,user_name,password FROM " . $ecs->table('suppliers') . " WHERE user_name='$username'";
    $suppliers = $db->getRow($sql);
    if (!empty($suppliers) && $suppliers['password'] == md5($password))
    {
	$_SESSION['suppliers_id'] = $suppliers['suppliers_id'];
	$_SESSION['suplliers_user'] = $suppliers['user_name'];
	ecs_header("Location: $back_act\n");
    }
    else
    {
	show_group_message($_LANG['login_failure'], $_LANG['relogin_lnk'], 'merchant.php?act=login', 'error');
    }
}
elseif ($action == 'get_coupons')
{
    $suppliers_id = $_SESSION['suppliers_id'] > 0 ? $_SESSION['suppliers_id'] : '0';
    $smarty->assign('suppliers_id', $suppliers_id);
    $smarty->display('group_card.dwt');
}
elseif ($action == 'set_coupons')
{
    include('includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => '0', 'result' => '', 'msg' => '');
    $card_sn = trim($_POST['card_sn']);
    $card_password = trim($_POST['card_pass']);
    $now = gmtime();
    if ($card_sn == '' || $card_password == '')
    {
	$result['error'] = 1;
	$result['msg'] = $_LANG['card_sn_empty'];
	//die($json->encode($result));
	echo $json->encode($result);
	exit;
    }
    $sql = "SELECT card_id,group_id,card_sn,card_password,is_used,end_date FROM " . $ecs->table('group_card') .
	    " WHERE card_sn='$card_sn' LIMIT 1";
    $group_card = $db->getRow($sql);
    $su_id = $db->getOne("SELECT suppliers_id FROM " . $ecs->table('group_activity') . " WHERE group_id = '$group_card[group_id]'");
    if ($su_id != $suppliers_id)
    {
	$result['msg'] = $_LANG['card_sn_error'];
	$result['error'] = 1;
	//die($json->encode($result));
	echo $json->encode($result);
	exit;
    }
    if ($group_card['end_date'] <= $now)
    {
	$result['msg'] = $_LANG['card_pass_end'];
	$result['error'] = 1;
	//die($json->encode($result));
	echo $json->encode($result);
	exit;
    }
    if ($group_card['is_used'] == 1)
    {
	$result['msg'] = $_LANG['card_used'];
	$result['error'] = 1;
	//die($json->encode($result));
	echo $json->encode($result);
	exit;
    }
    if ($group_card['card_password'] != $card_password)
    {
	$result['msg'] = $_LANG['card_pass_error'];
	$result['error'] = 1;
	//die($json->encode($result));
	echo $json->encode($result);
	exit;
    }

    $sql = "UPDATE " . $ecs->table('group_card') . " SET is_used = 1, use_date = '$now' WHERE card_id='$group_card[card_id]' LIMIT 1";
    $db->query($sql);
    $result['msg'] = $_LANG['card_success'];
    //die($json->encode($result));
	echo $json->encode($result);
	exit;
}
elseif ($action == 'settings')
{
    $sql = "SELECT * FROM " . $ecs->table('suppliers') . " WHERE suppliers_id='$suppliers_id'";
    $suppliers = $db->getRow($sql);
    $smarty->assign('suppliers', $suppliers);
    $smarty->assign('action', 'settings');
    $smarty->display('group_merchant.dwt');
}
elseif ($action == 'act_settings')
{
    $suppliers = array(
	'suppliers_desc' => trim($_POST['suppliers_desc']),
	'website' => trim($_POST['website']),
	'address' => trim($_POST['address']),
	'phone' => trim($_POST['phone']),
	'linkman' => trim($_POST['linkman']),
	'open_banks' => trim($_POST['open_banks']),
	'banks_user' => trim($_POST['banks_user']),
	'banks_account' => trim($_POST['banks_account']),
    );
    if (trim($_POST['password']) != '')
    {
	$suppliers['password'] = md5(trim($_POST['password']));
    }
    /* 保存供货商信息 */
    $db->autoExecute($ecs->table('suppliers'), $suppliers, 'UPDATE', "suppliers_id = '" . $suppliers_id . "'");
    show_group_message($_LANG['edit_suppliers'], $_LANG['view_suppliers'], 'merchant.php?act=settings');
}
elseif ($action == 'logout')
{
    unset($_SESSION['suppliers_id']);
    unset($_SESSION['suplliers_user']);
    ecs_header("Location: merchant.php \n");
}
elseif ($action == 'group')
{
    $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
    $size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

    $count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('group_activity') .
		    " WHERE suppliers_id='$suppliers_id'");

    $pager = get_pager('merchant.php', array('act' => $action), $count, $page, $size);

    $group_list = get_suppliers_group($suppliers_id, $pager['size'], $pager['start'], $where);
    $smarty->assign('pager', $pager);
    $smarty->assign('action', 'group');

    $smarty->assign('group_list', $group_list);
    $smarty->display('group_merchant.dwt');
}
else
{
    $action = 'coupons';
    $group_id = intval($_GET['id']);
    $card_sn = empty($_POST['card_sn']) ? '' : trim($_POST['card_sn']);
    $page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
    $size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
    $cache_id = sprintf('%X', crc32($suppliers_id . '-' . $action . '-' . $size . '-' . $page . '-' . $group_id . '-' . $card_sn));
    $where = !empty($card_sn) ? " AND gc.card_sn='$card_sn'" : '';
    $count = $db->getOne("SELECT count(*) FROM " .
		    $GLOBALS['ecs']->table('group_card') . " AS gc," .
		    $GLOBALS['ecs']->table('group_activity') . " AS ga " .
		    " WHERE ga.group_id=gc.group_id AND ga.suppliers_id= '$suppliers_id'");
    if ($group_id > 0)
    {
	$where = " AND ga.group_id='$group_id'";
	$act = array('act' => $action, 'id' => $group_id);
    }
    else
    {
	$act = array('act' => $action);
    }
    $pager = get_pager('merchant.php', $act, $count, $page, $size);
    $coupons_list = get_suppliers_coupons($suppliers_id, $pager['size'], $pager['start'], $where);
    $smarty->assign('pager', $pager);
    $smarty->assign('action', $action);

    $smarty->assign('coupons_list', $coupons_list);
    $smarty->display('group_merchant.dwt');
}

function get_suppliers_coupons($suppliers_id, $num = 10, $start = 0, $where = '')
{
    /* 取得订单列表 */
    $arr = array();
    $sql = "SELECT gc.*,o.order_sn FROM " .
	    $GLOBALS['ecs']->table('group_card') . " AS gc," .
	    $GLOBALS['ecs']->table('order_info') . " AS o," .
	    $GLOBALS['ecs']->table('group_activity') . " AS ga " .
	    " WHERE gc.is_saled = 1 AND o.order_sn=gc.order_sn " .
	    "AND ga.group_id=gc.group_id AND ga.suppliers_id= '$suppliers_id' $where ORDER BY gc.card_id DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);
    $now = gmtime();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
	if ($row['end_date'] > $now)
	{
	    if ($row['is_used'] == 0)
	    {
		$row['card_stat'] = '0';
	    }
	    else
	    {
		$row['card_stat'] = '1';
	    }
	}
	else
	{
	    $row['card_stat'] = 2;
	}
	$row['card_stat_name'] = $GLOBALS['_LANG']['card_stat'][$row['card_stat']];
	$row['end_date'] = local_date($GLOBALS['_CFG']['time_format'], $row['end_date']);
	$row['use_date'] = local_date($GLOBALS['_CFG']['time_format'], $row['use_date']);
	$row['group_url'] = rewrite_groupurl('index.php', array('id' => $row['group_id']));
	$arr[] = $row;
    }

    return $arr;
}

function get_suppliers_group($suppliers_id, $num = 10, $start = 0, $where = '')
{
    /* 取得订单列表 */
    $arr = array();

    $sql = "SELECT ga.group_id,ga.group_name,ga.start_time,ga.end_time,ga.market_price," .
	    "ga.is_finished,ga.upper_orders,ga.lower_orders,ga.ext_info FROM " .
	    $GLOBALS['ecs']->table('group_activity') . " AS ga " .
	    "WHERE ga.suppliers_id= '$suppliers_id' ORDER BY ga.group_id DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);
    $group_list = array();
    while ($arr = $GLOBALS['db']->fetchRow($res))
    {
	$ext_info = unserialize($arr['ext_info']);
	$stat = get_group_buy_stat($arr['group_id']);
	$arr = array_merge($arr, $stat, $ext_info);

	/* 处理价格阶梯 */
	$price_ladder = $arr['price_ladder'];
	if (!is_array($price_ladder) || empty($price_ladder))
	{
	    $price_ladder = array(array('amount' => 0, 'price' => 0));
	}
	else
	{
	    foreach ($price_ladder AS $key => $amount_price)
	    {
		$price_ladder[$key]['formated_price'] = group_price_format($amount_price['price']);
	    }
	}

	/* 计算当前价 */
	$cur_price = $price_ladder[0]['price'];    // 初始化

	$arr['cur_price'] = $cur_price;
	$arr['formated_cur_price'] = group_price_format($cur_price);
	$arr['formated_market_price'] = group_price_format($arr['market_price']);
	$status = get_group_buy_status($arr);
	$stat = get_group_buy_stat($arr['group_id']);
	$arr = array_merge($arr, $stat);
	$arr['start_time'] = local_date($GLOBALS['_CFG']['date_format'], $arr['start_time']);
	$arr['end_time'] = local_date($GLOBALS['_CFG']['date_format'], $arr['end_time']);
	$arr['cur_status'] = $GLOBALS['_LANG']['gbs'][$status];
	$arr['group_url'] = rewrite_groupurl('index.php', array('id' => $arr['group_id']));
	$group_list[] = $arr;
    }
    return $group_list;
}

?>