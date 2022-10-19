<?php

/**
 * 我的邀请
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];

$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
assign_public($city_id);

$user_id = $_SESSION['user_id'];

if($user_id<=0)
{
    ecs_header("location:login.php\n");
}

if ($user_id > 0)
{
    $size = 16;
    $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
    $cache_id = $_CFG['lang'] . '-' . $size . '-' . $page . '-' . $city_id . '-' . $cat_id;
    $cache_id = sprintf('%X', crc32($cache_id));
    //动作/tun
    $act=isset($_GET['act'])?$_GET['act']:'all';
    if($act == 'all')
    {
	$where=" AND 1=1 ";
    }
    elseif($act == 'pending')
    {
	$where=" AND separate_type=0 ";
    }
    elseif($act == 'done')
    {
	$where=" AND separate_type=1 ";
    }
    else{}
    $smarty->assign('action',$act);
    /* 如果没有缓存，生成缓存 */

    $sql = 'SELECT goods_rebate FROM ' . $GLOBALS['ecs']->table('group_activity') . " WHERE group_id='$gid'";
    $goods_rebate = $GLOBALS['db']->getOne($sql);
    if (empty($goods_rebate))
    {
	$goods_rebate = 0;
    }
    $count = get_invite_count($user_id,$where);
    if ($count > 0)
    {
	$invite_user = get_invite_user($user_id, $size, $page,$where);
	$smarty->assign('invite_user', $invite_user);
	/* 设置分页链接 */
	$pager = get_group_pager('invite.php', array(), $count, $page, $size);
	$smarty->assign('pager', $pager);
    }
    $config = unserialize($GLOBALS['_CFG']['affiliate']);
    if ($config['on'] == 1)
    {
	if (!empty($config['config']['expire']))
	{
	    if ($config['config']['expire_unit'] == 'hour')
	    {
		$c = 1;
	    }
	    elseif ($config['config']['expire_unit'] == 'day')
	    {
		$c = 24;
	    }
	    elseif ($config['config']['expire_unit'] == 'week')
	    {
		$c = 24 * 7;
	    }
	    else
	    {
		$c = 1;
	    }
	    $group_hours = $config['config']['expire'] * $c;
	    $smarty->assign('group_hours', $group_hours);
	}
    }
    $smarty->assign('goods_rebate', $goods_rebate);
    $smarty->assign('uid', $user_id);
    $smarty->assign('is_check_rebate', $GLOBALS['_CFG']['group_rebate']);
    $smarty->assign('menu', 'invite');
    $smarty->assign('intvite_url', $ecs->url() . rewrite_groupurl('team.php', array('u' => $user_id), true));
    $smarty->display('group_myinvite.dwt');
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
    $smarty->display('group_invite.dwt');
}

function get_invite_count($user_id,$where)
{
    $user_num = 0;
    if ($user_id > 0)
    {
	/*$sql = 'SELECT COUNT(*)  FROM ' . $GLOBALS['ecs']->table('order_info') . " AS o" .
		" WHERE o.parent_id = '$user_id' AND o.extension_code = 'group_buy' AND is_separate=1" .
		" AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED, OS_SPLITING_PART)) .
		" AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING));*/
	$sql="SELECT COUNT(*)  FROM " . $GLOBALS['ecs']->table('affiliate_log')." WHERE 1=1 " .$where ;
	
	$user_num = $GLOBALS['db']->getOne($sql);
    }
    return $user_num;
}

function get_invite_user($user_id, $size, $page,$where)
{
    /*
    $sql = 'SELECT o.extension_id,o.add_time,o.user_id,ga.goods_rebate,ga.group_id,ga.group_name FROM ' .
	    $GLOBALS['ecs']->table('order_info') . " AS o," . $GLOBALS['ecs']->table('group_activity') . " AS ga " .
	    " WHERE ga.group_id=o.extension_id AND o.parent_id = '$user_id' AND o.extension_code = 'group_buy' " .
	    " AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED, OS_SPLITING_PART)) .
	    " AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING));
     */
    $sql="select * from " .$GLOBALS['ecs']->table('affiliate_log')." where user_id=$user_id".$where;
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
	$sql = "SELECT user_name FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_id='$row[user_id]'";
	$user_name = $GLOBALS['db']->getOne($sql);
	if ($user_name != '')
	{
	    //$row['user_name'] = $user_name;
	    $row['formated_add_time'] = local_date('Y-m-d', $row['add_time']);
	    $row['formated_goods_rebate'] = group_price_format($row['goods_rebate']);
	    $arr[] = $row;
	}
    }
    return $arr;
}

?>