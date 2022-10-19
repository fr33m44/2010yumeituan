<?php

/**
 * 登录
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];
//定义常量
$max_fail_count = 5; //可以尝试扥登录的次数
$max_reset_count = 3;
$suspend_time = 1800; //帐号冻结时间/秒




/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
assign_public($city_id);

if ($_POST['act'] == 'act_login')//登录
{
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';
    $sql = "select * from " . $ecs->table('users') . " where user_name='$username'";
    $ck = $db->getRow($sql);
    if ($user->login($username, $password, isset($_POST['remember'])))
    {
	if ($ck['fail_count'] >= $max_fail_count && gmtime() - $ck['first_fail_time'] < $suspend_time)
	{
	    $user->logout();
	    show_group_message2("信息提示", "抱歉，尝试次数过多，请30分钟后再试。<a href=\"login.php?act=get_password\">忘记密码</a>");
	}
	else
	{
	    $ck['fail_count'] = 0;
	    $db->autoExecute($ecs->table('users'), $ck, 'UPDATE', "user_name = '$username'");
	    update_user_info();
	    recalculate_price();
	    $url = rewrite_groupurl('index.php');
	    ecs_header("Location: $url\n");
	    exit;
	}
    }
    else
    {
	if ($ck['fail_count'] == '0')
	{
	    $ck['fail_count'] = 1;
	    $ck['first_fail_time'] = gmtime();
	    $db->autoExecute($ecs->table('users'), $ck, 'UPDATE', "user_name = '$username'");
	    $smarty->assign('username', $username);
	    $smarty->assign('password', $password);
	    $smarty->assign('msg', array('type' => 'error', 'content' => 'Email或密码错误，请重新输入'));
	    $smarty->display('login.dwt');
	    exit;
	}
	elseif ($ck['fail_count'] >= $max_fail_count)
	{
	    show_group_message2("信息提示", "抱歉，尝试次数过多，请30分钟后再试。<a href=\"login.php?act=get_password\">忘记密码</a>");
	}
	else
	{
	    $ck['fail_count']++;
	    $db->autoExecute($ecs->table('users'), $ck, 'UPDATE', "user_name = '$username'");
	    $smarty->assign('username', $username);
	    $smarty->assign('password', $password);
	    $smarty->assign('msg', array('type' => 'error', 'content' => 'Email或密码错误，请重新输入'));
	    $smarty->display('login.dwt');
	}
    }
}
/* 密码找回-->修改密码界面 */
elseif ($_GET['act'] == 'get_password')
{
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    if (isset($_GET['code']) && isset($_GET['uid'])) //从邮件处获得的act
    {
	$code = trim($_GET['code']);
	$uid = intval($_GET['uid']);

	/* 判断链接的合法性 */
	$user_info = $user->get_profile_by_id($uid);
	if (empty($user_info) || ($user_info && md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']) != $code))
	{
	    $smarty->assign('msg', $msg = array('type' => 'error', 'content' => '抱歉，重置密码链接错误，请检查链接是否完整或稍后重新申请重置。'));
	    $smarty->display('group_team.dwt');
	}

	$smarty->assign('uid', $uid);
	$smarty->assign('code', $code);
	$smarty->assign('action', 'reset_password');
	$smarty->display('login.dwt');
    }
    else
    {
	//显示用户名和email表单
	$smarty->assign('action', 'get_password');
	$smarty->display('login.dwt');
    }
}

/* 发送密码修改确认邮件 */
elseif ($_POST['act'] == 'send_pwd_email')
{
    /* 初始化会员用户名和邮件地址 */
    //$user_name = !empty($_POST['user_name']) ? trim($_POST['user_name']) : '';
    $email = !empty($_POST['email']) ? trim($_POST['email']) : '';

    //用户名和邮件地址是否匹配
    $sql = "select * from " . $GLOBALS['ecs']->table('users') . " where email='$email'";
    $user_info = $GLOBALS['db']->getRow($sql);
    if (is_email($email))
    {
	if (!$user_info)//邮件不存在
	{
	    $smarty->assign('email', $email);
	    $smarty->assign('action', 'get_password');
	    $smarty->assign('msg', $msg = array('type' => 'error', 'content' => '抱歉，这个Email地址不存在或未被验证。'));
	    $smarty->display('login.dwt');
	    exit;
	}
	else//邮件存在
	{
	    //生成code
	    $code = md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']);
	    if ($user_info['resetpwd_count'] == 0)
	    {
		$user_info['resetpwd_count'] = 1;
		$user_info['first_resetpwd_time'] = gmtime();
		$GLOBALS['db']->autoExecute($ecs->table('users'), $user_info, 'UPDATE', "email = '$email'");
		send_password_email($user_info['user_id'], $user_info['user_name'], $email, $code);
		show_group_message2('找回密码', '操作成功！请到 ' . $email . '查阅邮件，点击邮件中的链接重设您的密码。', array('type' => 'success', 'content' => '操作成功'));
	    }
	    //重设密码次数超过3次，停止重设密码30分钟
	    elseif (gmtime() - $user_info['first_resetpwd_time'] <= 30 * 60 && $user_info['resetpwd_count'] > 3)
	    {
		show_group_message2('重设密码', '抱歉，尝试次数过多，请30分钟后再试。');
	    }
	    elseif (gmtime() - $user_info['first_resetpwd_time'] > 30 * 60)
	    {
		$user_info['resetpwd_count'] = 1;
		$user_info['first_resetpwd_time'] = gmtime();
		$GLOBALS['db']->autoExecute($ecs->table('users'), $user_info, 'UPDATE', "email = '$email'");
		send_password_email($user_info['user_id'], $user_info['user_name'], $email, $code);
		show_group_message2('找回密码', '操作成功！请到 ' . $email . '查阅邮件，点击邮件中的链接重设您的密码。', array('type' => 'success', 'content' => '操作成功'));
	    }
	    else
	    {
		$user_info['resetpwd_count']++;
		$GLOBALS['db']->autoExecute($ecs->table('users'), $user_info, 'UPDATE', "email = '$email'");
		send_password_email($user_info['user_id'], $user_info['user_name'], $email, $code);
		show_group_message2('找回密码', '操作成功！请到 ' . $email . '查阅邮件，点击邮件中的链接重设您的密码。', array('type' => 'success', 'content' => '操作成功'));
	    }
	}
    }
    else
    {
	$smarty->assign('email', $email);
	$smarty->assign('action', 'get_password');
	$smarty->assign('msg', $msg = array('type' => 'error', 'content' => '您输入了错误的邮箱地址'));
	$smarty->display('login.dwt');
    }
}
elseif ($_POST['act'] == 'act_edit_password')
{
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password2 = isset($_POST['password2']) ? trim($_POST['password2']) : '';
    $user_id = isset($_POST['uid']) ? intval($_POST['uid']) : $user_id;
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';

    if (strlen($password) < 6)
    {
	$smarty->assign('msg',array('type'=>'error','content'=>'密码太短，为保证您的账号安全，请至少设置6个字符'));
	$smarty->assign('password',$password);
	$smarty->assign('password2',$password2);
	$smarty->assign('uid',$user_id);
	$smarty->assign('code',$code);
	$smarty->assign('action', 'reset_password');
	$smarty->display('login.dwt');
	exit;
    }
    if($password != $password2)
    {
	$smarty->assign('msg',array('type'=>'error','content'=>'两次输入的密码不一致'));
	$smarty->assign('password',$password);
	$smarty->assign('password2',$password2);
	$smarty->assign('uid',$user_id);
	$smarty->assign('code',$code);
	$smarty->assign('action', 'reset_password');
	$smarty->display('login.dwt');
	exit;
    }
    $user_info = $user->get_profile_by_id($user_id); //论坛记录
    
    if (($user_info && (!empty($code) && md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']) == $code)) || ($_SESSION['user_id'] > 0 && $_SESSION['user_id'] == $user_id && $user->check_user($_SESSION['user_name'], $password)))
    {
	if ($user->edit_user(array('username' => (empty($code) ? $_SESSION['user_name'] : $user_info['user_name']), 'old_password' => $password, 'password' => $password2), empty($code) ? 0 : 1))
	{
	    //$user->logout();
	    $user->login($user_info['user_name'], $password);
	    update_user_info();
	    recalculate_price();
	    //show_group_message($_LANG['edit_password_success'], $_LANG['relogin_lnk'], 'login.php?act=login', 'info');
	    show_group_message2('重设密码', '操作成功，系统将在<span style="color:#64cbd0">3</span>秒后自动跳转…',0,3,'index.php');
	}
	else
	{
	    //show_group_message($_LANG['edit_password_failure'], $_LANG['back_page_up'], 'login.php', 'info');
	    show_group_message2('重设密码', '操作失败！请<a href="javascript:history.back()">返回</a>登录。');
	}
    }
    else
    {
	//show_group_message($_LANG['edit_password_failure'], $_LANG['back_page_up'], 'login.php', 'info');
	show_group_message2('重设密码', '操作失败！请<a href="javascript:history.back()">返回</a>登录。');
    }
}
else
{
    if (empty($back_act))
    {
	if (empty($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
	{
	    $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'login.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
	}
	else
	{
	    $back_act = 'login.php';
	}
    }
    $smarty->assign('action', 'login');
    $smarty->display('login.dwt');
}

function send_password_email($uid, $user_name, $email, $code)
{
    if (empty($uid) || empty($user_name) || empty($email) || empty($code))
    {
	ecs_header("Location: login.php?act=get_password\n");

	exit;
    }

    /* 设置重置邮件模板所需要的内容信息 */
    $template = get_mail_template('send_password');
    $reset_email = $GLOBALS['ecs']->url() . 'login.php?act=get_password&uid=' . $uid . '&code=' . $code;

    $GLOBALS['smarty']->assign('user_name', $user_name);
    $GLOBALS['smarty']->assign('reset_email', $reset_email);
    $GLOBALS['smarty']->assign('shop_name', $GLOBALS['_CFG']['shop_name']);
    $GLOBALS['smarty']->assign('send_date', date('Y-m-d'));
    $GLOBALS['smarty']->assign('sent_date', date('Y-m-d'));

    $content = $GLOBALS['smarty']->fetch('str:' . $template['template_content']);

    /* 发送确认重置密码的确认邮件 */
    if (send_mail($user_name, $email, $template['template_subject'], $content, $template['is_html']))
    {
	return true;
    }
    else
    {
	return false;
    }
}

?>