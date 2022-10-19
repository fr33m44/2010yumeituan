<?php

//意见反馈&提供团购信息

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];

$city_id = (isset($_REQUEST['cityid']) && intval($_REQUEST['cityid']) > 0) ? intval($_REQUEST['cityid']) : (isset($_COOKIE['ECS']['cityid']) ? $_COOKIE['ECS']['cityid'] : $_CFG['group_city']);
assign_public($city_id);

if($_SESSION['user_id']>0)
{
    $user_id=$_SESSION['user_id'];
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $user_info = get_profile($user_id);
    $sql = 'SELECT city_id FROM ' . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$user_id'";
    $user_info['city_id'] = $db->getOne($sql);
    $smarty->assign('profile', $user_info);
}



$type=$_GET['t'];
if ($type == 1)
{
    $title = '意见反馈';
    $desc = '请在这里留下您的宝贵意见，也可以给我们推荐您希望团购的商家。';
}
else
{
    $title = '提供团购信息';
    $desc = '如果您是商家、网上商城，想在渝美团组织团购，请填写：';
}


if ($_REQUEST['act'] == 'act_save')
{
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $info = array(
	'name' => $name,
	'contact' => $contact,
	'content' => $content,
	'type' => $type
    );
    if ($name == '')
    {
	$smarty->assign('msg', array('type' => 'error', 'content' => '提交失败，请输入您的称呼'));
	$smarty->assign('title', $title);
	$smarty->assign('desc', $desc);
	$smarty->assign('info', $info);
	$smarty->display('feedback.dwt');
    }
    elseif ($contact == '')
    {
	$smarty->assign('msg', array('type' => 'error', 'content' => '提交失败，请输入您的联系方式'));
	$smarty->assign('title', $title);
	$smarty->assign('desc', $desc);
	$smarty->assign('info', $info);
	$smarty->display('feedback.dwt');
    }
    elseif ($content == '')
    {
	$smarty->assign('msg', array('type' => 'error', 'content' => '提交失败，请输入您的内容'));
	$smarty->assign('title', $title);
	$smarty->assign('desc', $desc);
	$smarty->assign('info', $info);
	$smarty->display('feedback.dwt');
    }
    else
    {
	$sql = "insert into " . $GLOBALS['ecs']->table('comment') . "(comment_type, user_name, email, content) values('$type','$info[name]','$info[contact]','$info[content]') ";
	$GLOBALS['db']->query($sql);
	show_group_message2($title, '提交成功，谢谢您的支持。 <a href="/">&gt;&gt;返回首页</a>');
    }
}
else
{
    $smarty->assign('title', $title);
    $smarty->assign('desc', $desc);
    $smarty->assign('type', $type);
    $smarty->display('feedback.dwt');
}
?>
