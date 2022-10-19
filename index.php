<?php

/**
 * 首页
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];
/* 取得参数：团购活动id */
$group_buy_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
//购买指南
$smarty->assign('show_tips',$_COOKIE['ECS']['show_tips']);

$city_id = (isset($_REQUEST['cityid']) && intval($_REQUEST['cityid']) > 0) ? intval($_REQUEST['cityid']) : (isset($_COOKIE['ECS']['cityid']) ? $_COOKIE['ECS']['cityid'] : $_CFG['group_city']);
$cat_id = 0;
setcookie('ECS[cityid]', $city_id, gmtime() + 86400 * 7);
/* 缓存id：语言，团购活动id，状态，（如果是进行中）当前数量和是否登录 */
$cache_id = $_CFG['lang'] . '-' . $group_buy_id . '-' . $_SESSION['user_rank'] . '-' . $city_id . '-' . $cat_id . date('z');
$cache_id = sprintf('%X', crc32($cache_id));  
/* 如果没有缓存，生成缓存 */
if (!$smarty->is_cached('group_team_noside.dwt', $cache_id))
{
    $group_buy = get_group_buy_info($group_buy_id, $city_id, $cat_id);
    if (empty($group_buy))
    {
	$url = rewrite_groupurl('subscribe.php');
	ecs_header("Location: $url\n");
	exit;
    }
    assign_public($city_id);
    $vote = get_vote();
    if (!empty($vote))
    {
	$smarty->assign('vote_id', $vote['id']);
	$smarty->assign('vote', $vote['content']);
    }
    $smarty->assign('group_buy', $group_buy);
    $sql = "SELECT * FROM " . $ecs->table('suppliers') . " WHERE suppliers_id='$group_buy[suppliers_id]'";
    $suppliers_arr = $db->getRow($sql);
    $address_img = 'template/meituan/images/temp-ditu.gif';
    $suppliers_arr['address_img'] = empty($suppliers_arr['address_img']) ? $address_img : $suppliers_arr['address_img'];
    $smarty->assign('suppliers_arr', $suppliers_arr);
    $smarty->assign('group_comment', get_group_comment());
    $sql = "SELECT * FROM " . $ecs->table('group_gallery') . " WHERE group_id = '$group_buy[group_id]'";
    $img_list = $db->getAll($sql);
    $smarty->assign('img_list', $img_list);
    $img_count = array();
    for ($i = 1; $i <= count($img_list); $i++)
    {
	$img_count[] = $i;
    }
    $smarty->assign('img_count', $img_count);
    $smarty->assign('where', 'index');
    $smarty->assign('today_group', $today_group=get_today_grouplist($group_buy['group_id'], $city_id, $cat_id));
    foreach($today_group as $tg)
    {
	$today_g[]=get_group_buy_info($tg['group_id']);
    }
    $smarty->assign('today_g',$today_g);
    $smarty->assign('comment_num', get_group_comment_count($city_id));
    $smarty->assign('friend_comment', get_friend_comment($group_buy['group_id']));
    //求购转让
    $smarty->assign('ask_url', rewrite_groupurl('ask.php', array('gid' => $group_buy['group_id'])));
    $smarty->assign('ask_url_exchange', rewrite_groupurl('ask.php', array('city_id' => $city_id)));
    $smarty->assign('invite_url', rewrite_groupurl('invite.php', array('gid' => $group_buy['group_id'])));
    //倒计时
    $diff=$group_buy['end_time']-gmtime();
    if($diff<=0)
	$diff=0;
    $smarty->assign('diff',$diff);
    //session
    $smarty->assign('uid',$_SESSION['user_id']);
}
if($group_buy['show_sidebar'] == 1)
{
    $smarty->display('group_team.dwt', $cache_id);
}
else
{
    $smarty->display('group_team_noside.dwt', $cache_id);
}
?>