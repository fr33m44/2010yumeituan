<?php

/**
 * ECGROUPON 团购商品前台文件
 * ============================================================================
 * 网站地址: http://www.ecgroupon.com；
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$size = 16;
$page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
$cat_id = isset($_REQUEST['catid']) && intval($_REQUEST['catid']) > 0 ? intval($_REQUEST['catid']) : 0;
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
$cache_id = $_CFG['lang'] . '-' . $size . '-' . $page . '-' . $city_id . '-' . $cat_id;
$cache_id = sprintf('%X', crc32($cache_id));

/* 如果没有缓存，生成缓存 */
if (!$smarty->is_cached('group_stage.dwt', $cache_id))
{
    $count = group_buy_count($city_id, $cat_id);

    if ($count > 0)
    {
	/* 取得当前页的团购活动 */
	$group_list = group_buy_list($city_id, $cat_id, $size, $page);
	$smarty->assign('group_list', $group_list);
	/* 设置分页链接 */
	$pager = get_group_pager('stage.php', array(), $count, $page, $size);
	$smarty->assign('pager', $pager);
    }
    else
    {
	$url = rewrite_groupurl('subscribe.php');
	ecs_header("Location: $url\n");
	exit;
    }
    /* 模板赋值 */
    assign_public($city_id);
    $smarty->assign('where', 'stage');
    $smarty->assign('today_group', get_today_grouplist('0', $city_id, $cat_id));
}
$smarty->display('group_stage.dwt', $cache_id);

function group_buy_count($city_id = 0, $cat_id = 0)
{
    $now = gmtime();
    $sql = "SELECT COUNT(*) " . "FROM " . $GLOBALS['ecs']->table('group_activity');
    $where = " WHERE start_time <= '$now'";
    if ($city_id > 0)
    {
	$where .= " AND (city_id='$city_id' OR " . get_expand_city($city_id) . ")";
    }
    if ($cat_id > 0)
    {
	$where .= " AND cat_id='$cat_id'";
    }
    $sql .= $where;

    return $GLOBALS['db']->getOne($sql);
}

function group_buy_list($city_id = 0, $cat_id = 0, $size, $page)
{
    /* 取得团购活动 */
    $group_list = array();
    $now = gmtime();
    $sql = "SELECT group_id,group_name,group_image,group_need,ext_info,market_price,is_finished,start_time,end_time " .
	    "FROM " . $GLOBALS['ecs']->table('group_activity');
    $where = " WHERE start_time <= '$now'";
    if ($city_id > 0)
    {
	$where .= " AND (city_id='$city_id' OR " . get_expand_city($city_id) . ")";
    }
    if ($cat_id > 0)
    {
	$where .= " AND cat_id='$cat_id'";
    }
    $sql .= $where . " ORDER BY start_time DESC,is_finished ASC ";
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    while ($group_buy = $GLOBALS['db']->fetchRow($res))
    {
	$ext_info = unserialize($group_buy['ext_info']);
	$group_buy = array_merge($group_buy, $ext_info);
	/* 处理价格阶梯 */
	$price_ladder = $group_buy['price_ladder'];
	if (!is_array($price_ladder) || empty($price_ladder))
	{
	    $price_ladder = array(array('amount' => 0, 'price' => 0));
	}
	else
	{
	    foreach ($price_ladder as $key => $amount_price)
	    {
		$price_ladder[$key]['formated_price'] = group_price_format($amount_price['price']);
	    }
	}
	$group_buy['orders_num'] = get_group_orders($group_buy['group_id'], $group_buy['group_need']);
	$group_buy['price_ladder'] = $price_ladder;
	$group_buy['formated_start_date'] = local_date('Y年m月d日', $group_buy['start_time']);
	$group_buy['cur_price'] = $price_ladder[0]['price'];
	$group_buy['formated_cur_price'] = $price_ladder[0]['formated_price'];
	/* 处理链接 */
	$group_buy['rebate_price'] = $group_buy['market_price'] - $price_ladder[0]['price'];
	$group_buy['formated_market_price'] = group_price_format($group_buy['market_price'], false);
	$group_buy['formated_rebate_price'] = group_price_format($group_buy['rebate_price'], false);
	$group_buy['group_image'] = get_image_path('0', $group_buy['group_image'], true);
	$group_buy['rebate'] = number_format($price_ladder[0]['price'] / $group_buy['market_price'], 2, '.', '') * 10;
	/* 团购状态/tun */
	$group_buy['status']=get_group_buy_status($group_buy);
	/* 加入数组 */
	$group_buy['url'] = rewrite_groupurl('index.php', array('id' => $group_buy['group_id']));
	$group_list[] = $group_buy;
    }
    return $group_list;
}

?>