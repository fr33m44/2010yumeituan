<?php

/**
 * 邀请
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];
/* 取得参数：团购活动id */
$group_buy_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
$cat_id = 0;
$user_id = $_SESSION['user_id'];
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
assign_public($city_id);

if ($user_id > 0)
{
    $group_buy = get_group_buy_info(7, $city_id, $cat_id);
    if (empty($group_buy))
    {
	$url = rewrite_groupurl('subscribe.php');
	ecs_header("Location: $url\n");
	exit;
    }
    $smarty->assign('group_buy', $group_buy);
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $gid = intval($_GET['gid']);
    $sql = 'SELECT goods_rebate FROM ' . $GLOBALS['ecs']->table('group_activity') . " WHERE group_id='$gid'";
    $sql = 'SELECT VALUE FROM ' . $GLOBALS['ecs']->table('shop_config') . "   WHERE CODE='rebate' ";
    
    $goods_rebate = $GLOBALS['db']->getOne($sql);
    if (empty($goods_rebate))
    {
	$goods_rebate = 0;
    }
    $smarty->assign('rebate', $goods_rebate);
    $smarty->assign('action', 'order');
    $smarty->assign('uid', $user_id);
    $smarty->assign('intvite_url', $ecs->url() . rewrite_groupurl('index.php', array('u' => $user_id), true));
    $smarty->display('invite.dwt');
}
else
{
    $sql = 'SELECT goods_rebate FROM ' . $GLOBALS['ecs']->table('group_activity') . " WHERE group_id='$gid'";
    $goods_rebate = $GLOBALS['db']->getOne($sql);
    if (empty($goods_rebate))
    {
	$goods_rebate = 0;
    }
    $smarty->assign('goods_rebate', $goods_rebate);
    $smarty->assign('uid', '0');
    $smarty->display('invite.dwt');
}
?>