<?php

/**
 * 支付回调
 */
define('IN_ECS', true);

require_once(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_payment.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];

//tun
$user_id = $_SESSION['user_id'];
if ($user_id <= 0)
{
    ecs_header("location:index.php\n");
}

/* 支付方式代码 */
$pay_code = !empty($_REQUEST['code']) ? trim($_REQUEST['code']) : '';



/* 参数是否为空 */
if (empty($pay_code))
{
    $msg = $_LANG['pay_not_exist'];
}
else
{
    /* 检查code里面有没有问号 */
    if (strpos($pay_code, '?') !== false)
    {
	$arr1 = explode('?', $pay_code);
	$arr2 = explode('=', $arr1[1]);

	$_REQUEST['code'] = $arr1[0];
	$_REQUEST[$arr2[0]] = $arr2[1];
	$_GET['code'] = $arr1[0];
	$_GET[$arr2[0]] = $arr2[1];
	$pay_code = $arr1[0];
    }

    /* 判断是否启用 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
    if ($db->getOne($sql) == 0)
    {
	$msg = $_LANG['pay_disabled'];
    }
    else
    {
	$plugin_file = 'includes/modules/payment/' . $pay_code . '.php';

	/* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
	if (file_exists($plugin_file))
	{
	    /* 根据支付方式代码创建支付类的对象并调用其响应操作方法 */
	    include_once($plugin_file);

	    $payment = new $pay_code();
	    $msg = ($payment->respond()) ? $_LANG['pay_success'] : $_LANG['pay_fail'];
	}
	else
	{
	    $msg = $_LANG['pay_not_exist'];
	}
    }
}
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
assign_public($city_id);
assign_template();
$position = assign_ur_here();
//得到团购信息/tun
if ($pay_code == 'alipay')
{
    $group_buy_id = $GLOBALS['db']->getOne("SELECT extension_id FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_sn=" . $_REQUEST['out_trade_no']);
}
if($pay_code == 'tenpay')
{
    $group_buy_id = $GLOBALS['db']->getOne("SELECT extension_id FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_sn=" . $_REQUEST['sp_billno']);
}
$group_buy = get_group_buy_info($group_buy_id);
//得到用户的电话/tun
$mobile_phone = $GLOBALS['db']->getOne("SELECT mobile_phone FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id=" . $user_id);
if(urldecode($_REQUEST['subject']) == '渝美团充值')
{
    $smarty->assign('charge', 1);
}

$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here', $position['ur_here']); // 当前位置
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here', $position['ur_here']); // 当前位置
$smarty->assign('helps', get_shop_help());      // 网店帮助
$smarty->assign('group_buy', $group_buy);
$smarty->assign('mobile_phone', $mobile_phone);
$smarty->assign('uid', $user_id);
$smarty->assign('message', $msg);
$smarty->assign('shop_url', $ecs->url());
$smarty->display('respond.dwt');
?>