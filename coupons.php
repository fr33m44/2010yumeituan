<?php

/**
 * 团购券
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
require_once(ROOT_PATH . 'includes/cls_json.php');
$json = new json();
/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];

$action = trim($_GET['act']);
$id = intval($_GET['id']);

$user_id = $_SESSION['user_id'];
if ($user_id <= '0')
{
    $url = rewrite_groupurl('index.php');
    ecs_header("Location: $url\n");
    exit;
}
$now = gmtime();
$where = '';
if ($id)
{
    $where = " AND card_id = $id ";
    $action = 'all';
    //判断券的拥有人
    $user_id_db = $GLOBALS['db']->getOne("SELECT user_id FROM " . $GLOBALS['ecs']->table('group_card') . " WHERE card_id=" . $id);
    if ($user_id_db != $user_id)
    {
	$url = rewrite_groupurl('index.php');
	ecs_header("Location: $url\n");
    }
}
else
{
    if ($action == 'all')
    {
	//$where = " AND is_used = '0'";
    }
    elseif ($action == 'used')
    {
	$where = " AND is_used = '1'";
    }
    elseif ($action == 'expiring')
    {
	$where = " AND is_used = '0' AND end_date >= '$now'";
    }
    elseif ($action == 'expired')
    {
	$where = " AND end_date < '$now'";
    }
    elseif ($action == 'comment')
    {
	$card_id = intval($_GET['id']);
	$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
	assign_public($city_id);
	$smarty->assign('card_id', $card_id);
	$smarty->display('group_comment.dwt');
	exit;
    }
    elseif ($action == 'act_comment')
    {
	$comment = trim($_POST['comment']);
	$card_id = trim($_POST['card_id']);
	if ($comment == '')
	{
	    echo $json->encode(array('status' => 0, 'msg' => '内容不能为空'));
	    exit;
	}
	else
	{
	    $sql = "update" . $GLOBALS['ecs']->table('group_card') . " set comment='$comment' where card_id=$card_id ";
	    $GLOBALS['db']->query($sql);
	    echo $json->encode(array('status' => 1, 'msg' => '评论成功'));
	    exit;
	}
    }
    elseif ($action == 'print')
    {
	$card_id = trim($_GET['card_id']);
	$sql = "SELECT card_password,card_sn,group_id,order_sn,user_id FROM " . $ecs->table('group_card') .
		" WHERE card_id='$card_id' AND user_id='$user_id' AND is_used='0'";
	$card_arr = $db->getRow($sql);
	//得到用户名
	require_once (ROOT_PATH . 'includes/lib_transaction.php');
	$card_arr['userinfo'] = get_profile($card_arr['user_id']);
	if (!empty($card_arr))
	{
	    $order_sn = $card_arr['order_sn'];
	    $sql = "SELECT consignee,pay_status,mobile,consignee,tel,email FROM " . $ecs->table('order_info') .
		    " WHERE order_sn='$order_sn' AND user_id='$user_id'";
	    $order = $db->getRow($sql);
	    $group_id = $card_arr['group_id'];
	    $sql = "SELECT * FROM " . $ecs->table('group_activity') . " WHERE group_id='$group_id'";
	    $group_arr = $db->getRow($sql);
	    $group_arr['past_time'] = local_date('Y-m-d', $group_arr['past_time']);
	    $sql = "SELECT * FROM " . $ecs->table('suppliers') . " WHERE suppliers_id='$group_arr[suppliers_id]'";
	    $suppliers_arr = $db->getRow($sql);
	    $smarty->assign('suppliers_arr', $suppliers_arr);
	    $smarty->assign('card_arr', $card_arr);
	    $smarty->assign('order', $order);
	    $smarty->assign('group_arr', $group_arr);
	}
	$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
	assign_public($city_id);
	$smarty->display('group_print.dwt');
	exit;
    }
    elseif ($action == 'send_sms')
    {
	require_once(ROOT_PATH . 'includes/cls_json.php');
	require_once(ROOT_PATH . 'includes/cls_sms_huatang.php');
	$json = new json();
	$smscfg = array(
	    'CorpID' => $GLOBALS['_CFG']['sms_user_name'], //帐号
	    'Pwd' => $GLOBALS['_CFG']['sms_password'], //密码
	    'CorpName' => '', //企业名称
	    'LinkMan' => '', //联系人
	    'Tel' => '', //联系电话
	    'Mobile' => '', //联系人手机
	    'Email' => '', //邮件
	    'Memo' => '' //备注
	);
	$sms = new sms_huatang($smscfg);

	$card_id = trim($_GET['card_id']);
	$sql = "SELECT card_password,card_sn,group_id,order_sn,user_id,end_date,send_num FROM " . $ecs->table('group_card') .
		" WHERE card_id='$card_id' AND user_id='$user_id' AND is_used='0'";
	$card_arr = $db->getRow($sql);

	if (!empty($card_arr))
	{
	    $sql = "SELECT last_sendsms_time FROM " . $ecs->table('users') .
		    " WHERE  user_id='$user_id'";
	    $last_sendsms_time = $db->getOne($sql);
	    if ($GLOBALS['_CFG']['send_sms_num'] > 0 && $card_arr['send_num'] >= $GLOBALS['_CFG']['send_sms_num'])
	    {
		echo $json->encode(array('status' => 0, 'msg' => '已达到发送条数最高限制'));
		exit;
	    }
	    if (gmtime() - $last_sendsms_time <= 15 * 60)
	    {
		echo $json->encode(array('status' => 0, 'msg' => '发送过于频繁'));
		exit;
	    }
	    $order_sn = $card_arr['order_sn'];
	    $sql = "SELECT mobile FROM " . $ecs->table('order_info') .
		    " WHERE order_sn='$order_sn' AND user_id='$user_id'";
	    $mobile = $db->getOne($sql);
	    if (!empty($_GET['mobile']) && $_GET['mobile'] != $mobile)
	    {
		$mobile = trim($_GET['mobile']);
		$sql = "UPDATE " . $ecs->table('order_info') . " SET mobile='$mobile' WHERE order_sn='$order_sn' AND user_id='$user_id'";
		$db->query($sql);
	    }
	    $group_id = $card_arr['group_id'];
	    $sql = "SELECT *  FROM " . $ecs->table('group_activity') . " WHERE group_id='$group_id'";
	    $group_info = $db->getRow($sql);
	    $tpl = get_sms_template('send_sms');
	    $GLOBALS['smarty']->assign('group_name', $group_info['goods_name']);
	    $GLOBALS['smarty']->assign('card_sn', $card_arr['card_sn']);
	    $GLOBALS['smarty']->assign('card_password', $card_arr['card_password']);
	    $GLOBALS['smarty']->assign('past_time', local_date('Y-m-d', $card_arr['end_date']));
	    //商家电话
	    $sql = "select phone from " . $ecs->table('suppliers') . " where suppliers_id=" . $group_info['suppliers_id'];
	    $mer_mobile = $db->getOne($sql);
	    $GLOBALS['smarty']->assign('mer_mobile', $mer_mobile);
	    $msg = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
	    if ($mobile != '')
	    {
		//$sms->Send($smscfg['CorpID'], $smscfg['Pwd'], $mobile, $msg, '');
		$sql = "UPDATE " . $GLOBALS['ecs']->table('group_card') . " SET send_num=send_num+1 WHERE card_id='$card_id'";
		$GLOBALS['db']->query($sql);
		$sql = "UPDATE " . $GLOBALS['ecs']->table('users') . " SET last_sendsms_time=" . gmtime() . " WHERE user_id='$user_id'";
		$GLOBALS['db']->query($sql);
		echo $json->encode(array('status' => 1, 'msg' => '发送成功'));
		exit;
	    }
	    else
	    {
		echo $json->encode(array('status' => 0, 'msg' => '发送失败'));
		exit;
	    }
	}
	else
	{

	}
    }
    elseif ($action == 'make_sms')
    {
	$card_id = trim($_GET['card_id']);
	$sql = "SELECT order_sn FROM " . $ecs->table('group_card') .
		" WHERE card_id='$card_id' AND user_id='$user_id' AND is_used='0'";
	$order_sn = $db->getOne($sql);
	$sql = "SELECT mobile FROM " . $ecs->table('order_info') .
		" WHERE order_sn='$order_sn' AND user_id='$user_id'";
	$mobile = $db->getOne($sql);
	$smarty->assign('mobile', $mobile);
	$smarty->assign('card_id', $card_id);
	$smarty->display('group_pmess.dwt');
	exit;
    }
    else
    {
	$action = 'coupons';
	$where = " AND is_used = '0' AND end_date >= $now";
    }
}

$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
$page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
$size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

$record_count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('group_card') .
		" WHERE is_saled = 1 AND user_id='$user_id' $where");

$pager = get_pager('coupons.php', array('act' => $action), $record_count, $page, $size);

$coupons_list = get_coupons_list($user_id, $pager['size'], $pager['start'], $where);
//用户信息/tun
$user_id = $_SESSION['user_id'];
$sql = "select mobile_phone from " . $GLOBALS['ecs']->table('users') . " where user_id='$user_id' ";
$mobile = $GLOBALS['db']->getOne($sql);
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

$smarty->assign('mobile', $mobile);
/////////////
$smarty->assign('pager', $pager);
$smarty->assign('action', $action);
$smarty->assign('menu', 'coupons');
assign_public($city_id);
$smarty->assign('coupons_list', $coupons_list);
$smarty->display('group_coupons.dwt');

/**
 *  获取用户指定范围的订单列表
 *
 * @access  public
 * @param   int         $user_id        用户ID号
 * @param   int         $num            列表最大数量
 * @param   int         $start          列表起始位置
 * @return  array       $order_list     订单列表
 */
function get_coupons_list($user_id, $num = 10, $start = 0, $where = '')
{
    /* 取得订单列表 */
    $arr = array();

    $sql = "SELECT gc.*,ga.group_id,ga.group_image,is_finished,ga.goods_name FROM " . $GLOBALS['ecs']->table('group_card') . " AS gc,"
	    . $GLOBALS['ecs']->table('group_activity') . " AS ga" .
	    " WHERE gc.user_id = '$user_id' AND gc.is_saled = 1 AND gc.group_id=ga.group_id $where ORDER BY card_id DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $num, $start);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
	$row['add_date'] = getdate($row['add_date'] + $GLOBALS['_CFG']['timezone'] * 3600);
	$row['end_date'] = getdate($row['end_date'] + $GLOBALS['_CFG']['timezone'] * 3600);
	$row['use_date'] = getdate($row['use_date'] + $GLOBALS['_CFG']['timezone'] * 3600);
	$row['group_url'] = rewrite_groupurl('index.php', array('id' => $row['group_id']));
	$arr[] = $row;
    }
    return $arr;
}

?>