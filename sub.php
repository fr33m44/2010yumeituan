<?php

/**
 * 邮件页面订阅
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
require_once(ROOT_PATH . 'includes/cls_json.php');
require_once(ROOT_PATH . 'includes/cls_sms_huatang.php');
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
$json = new json();
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
assign_public($city_id);
if ($_REQUEST['act'] == 'add_mobile')
{
    include_once('includes/cls_captcha.php');
    $validator = new captcha();

    $do = $_REQUEST['do'];
    $mobile = trim($_POST['mobile']);
    $captcha = trim($_POST['captcha']);
    if ($do == 'add')
    {
	if(!isset($_POST['code']))
	{
	    //检查是否已经订阅
	    $sql = "select * from " . $GLOBALS['ecs']->table('sms_list') . " where mobile='$mobile' and stat=1 ";
	    $ck = $GLOBALS['db']->getRow($sql);
	    if ($ck)
	    {
		echo $json->encode(array('status' => 0, 'msg' => '该手机已经订阅', 'data' => array('needCaptcha' => true)));
		exit;
	    }
	    //检查验证码
	    elseif (!$validator->check_word($captcha))
	    {
		echo $json->encode(array('status' => 0, 'msg' => '验证码错误', 'data' => array('needCaptcha' => true)));
		exit;
	    }
	    else
	    {
		//插入新纪录/更新一条未确认的记录
		$hash = generate_word();
		//查看是否需要更新
		$sql = "select * from " . $GLOBALS['ecs']->table('sms_list') . " where mobile='$mobile' and stat=0";
		$ck = $GLOBALS['db']->getRow($sql);
		if ($ck)
		{
		    $ck['hash'] = $hash;
		    $db->autoExecute($ecs->table('sms_list'), $ck, 'UPDATE', "id = '$ck[id]'");
		}
		else
		{
		    $sql = "insert into " . $GLOBALS['ecs']->table('sms_list') . "(city_id,mobile,stat,hash) values($city_id,'$mobile',0,'$hash')";
		    $GLOBALS['db']->query($sql);
		}

		//再发送一条短信
		$tpl = get_sms_template('send_sms_auth');
		$GLOBALS['smarty']->assign('authcode', $hash);
		$msg = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
		$sms->Send($smscfg['CorpID'], $smscfg['Pwd'], $mobile, $msg, '');
		echo $json->encode(array('status' => 1, 'msg' => '验证码正确', 'data' => array('subscribed' => false)));
		exit;
	    }
	}
	else//认证码输入
	{
	    $code = $_POST['code'];
	    $mobile = $_POST['mobile'];
	    $sql = "select * from " . $GLOBALS['ecs']->table('sms_list') . " where hash='$code' and mobile='$mobile'";
	    $ck = $GLOBALS['db']->getRow($sql);
	    if ($ck)
	    {
		$ck['stat']=1;
		$db->autoExecute($ecs->table('sms_list'), $ck, 'UPDATE', "id = '$ck[id]'");
		echo $json->encode(array('status' => 1, 'msg' => '订阅成功'));
		exit;
	    }
	    else
	    {
		echo $json->encode(array('status' => 0, 'msg' => '短信认证码错误，请重试'));
		exit;
	    }
	}
    }
    if($do =='del')
    {
	if(!isset($_POST['code']))
	{
	    //检查是否是否订阅
	    $sql = "select * from " . $GLOBALS['ecs']->table('sms_list') . " where mobile='$mobile' and stat=1 ";
	    $ck = $GLOBALS['db']->getRow($sql);
	    if (!$ck)
	    {
		echo $json->encode(array('status' => 0, 'msg' => '该手机没有订阅', 'data' => array('needCaptcha' => true)));
		exit;
	    }
	    //检查验证码
	    elseif (!$validator->check_word($captcha))
	    {
		echo $json->encode(array('status' => 0, 'msg' => '验证码错误', 'data' => array('needCaptcha' => true)));
		exit;
	    }
	    else
	    {
		$hash = generate_word();
		//再发送一条短信
		$tpl = get_sms_template('send_sms_auth_unsubscribe');
		$GLOBALS['smarty']->assign('authcode', $hash);
		//更新hash
		$sql = "select * from " . $GLOBALS['ecs']->table('sms_list') . " where mobile='$mobile' and stat=1 ";
		$ck = $GLOBALS['db']->getRow($sql);
		$ck['hash']=$hash;
		$db->autoExecute($ecs->table('sms_list'), $ck, 'UPDATE', "id = '$ck[id]'");
		$msg = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
		$sms->Send($smscfg['CorpID'], $smscfg['Pwd'], $mobile, $msg, '');
		echo $json->encode(array('status' => 1, 'msg' => '验证码正确', 'data' => array('subscribed' => false)));
		exit;
	    }
	}
	else//认证码输入
	{
	    $code = $_POST['code'];
	    $mobile = $_POST['mobile'];
	    $sql = "select * from " . $GLOBALS['ecs']->table('sms_list') . " where hash='$code' and mobile='$mobile'";
	    $ck = $GLOBALS['db']->getRow($sql);
	    if ($ck)
	    {
		$sql="delete from ".$GLOBALS['ecs']->table('sms_list') ." where mobile='$mobile'";
		$GLOBALS['db']->query($sql);
		echo $json->encode(array('status' => 1, 'msg' => '取消订阅成功'));
		exit;
	    }
	    else
	    {
		echo $json->encode(array('status' => 0, 'msg' => '短信认证码错误，请重试'));
		exit;
	    }
	}
    }
}
if ($_REQUEST['act'] == 'add_email')
{
    $do = $_REQUEST['do'];
    $city_id = trim($_POST['cityid']);
    $email = trim($_REQUEST['email']);
    $email = htmlspecialchars($email);
    if (!is_email($email))
    {
	$smarty->assign('msg',array('type'=>'error','content'=>'邮件地址格式错误，请检查。'));
	
    }
    $ck = $db->getRow("SELECT * FROM " . $ecs->table('email_list') . " WHERE email = '$email'");
    if ($do == 'add')
    {
	if (empty($ck))
	{
	    $hash = substr(md5(time()), 1, 10);
	    $sql = "INSERT INTO " . $ecs->table('email_list') . " (email, stat, hash, city_id) VALUES ('$email', 0, '$hash','$city_id')";
	    $db->query($sql);
	    $info = $_LANG['email_check'];
	    $url = $ecs->url() . "subscribe.php?act=add_email&do=add_check&hash=$hash&email=$email";
	    send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['group_shopname'], $url, $url, $_CFG['group_shopname'], local_date('Y-m-d')), 1);
	}
	elseif ($ck['stat'] == 1)
	{
	    $info = sprintf($_LANG['email_alreadyin_list'], $email);
	    echo $json->encode(array('status' => 0, 'msg' => $info));
	    exit;
	}
	else
	{
	    $hash = substr(md5(time()), 1, 10);
	    $sql = "UPDATE " . $ecs->table('email_list') . "SET hash = '$hash' WHERE email = '$email'";
	    $db->query($sql);
	    $info = $_LANG['email_re_check'];
	    $url = $ecs->url() . "subscribe.php?act=add_email&do=add_check&hash=$hash&email=$email";
	    send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['group_shopname'], $url, $url, $_CFG['group_shopname'], local_date('Y-m-d')), 1);
	}
    }
    elseif ($do == 'del')
    {
	if (empty($ck))
	{
	    $info = sprintf($_LANG['email_notin_list'], $email);
	}
	elseif ($ck['stat'] == 1)
	{
	    $hash = substr(md5(time()), 1, 10);
	    $sql = "UPDATE " . $ecs->table('email_list') . "SET hash = '$hash' WHERE email = '$email'";
	    $db->query($sql);
	    $info = $_LANG['email_check'];
	    $url = $ecs->url() . "subscribe.php?act=add_email&do=del_check&hash=$hash&email=$email";
	    send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['group_shopname'], $url, $url, $_CFG['group_shopname'], local_date('Y-m-d')), 1);
	}
	else
	{
	    $info = $_LANG['email_not_alive'];
	}
    }
    elseif ($do == 'add_check')
    {
	if (empty($ck))
	{
	    $info = sprintf($_LANG['email_notin_list'], $email);
	}
	elseif ($ck['stat'] == 1)
	{
	    $info = $_LANG['email_checked'];
	}
	else
	{
	    if ($_GET['hash'] == $ck['hash'])
	    {
		$sql = "UPDATE " . $ecs->table('email_list') . "SET stat = 1 WHERE email = '$email'";
		$db->query($sql);
		$info = $_LANG['email_checked'];
	    }
	    else
	    {
		$info = $_LANG['hash_wrong'];
	    }
	}
    }
    elseif ($do == 'del_check')
    {
	if (empty($ck))
	{
	    $info = sprintf($_LANG['email_invalid'], $email);
	}
	elseif ($ck['stat'] == 1)
	{
	    if ($_GET['hash'] == $ck['hash'])
	    {
		$sql = "DELETE FROM " . $ecs->table('email_list') . "WHERE email = '$email'";
		$db->query($sql);
		$info = $_LANG['email_canceled'];
	    }
	    else
	    {
		$info = $_LANG['hash_wrong'];
	    }
	}
	else
	{
	    $info = $_LANG['email_not_alive'];
	}
    }
    //show_group_message($info, $_LANG['back_home_lnk'], rewrite_groupurl('index.php'));
    echo $json->encode(array('status' => 1, 'msg' => $info));
    exit;
}
else
{
    $get_city_id = isset($_GET['cityid'])?$_GET['cityid']:0;
    $city_name=$GLOBALS['db']->getOne("select region_name from ".$GLOBALS['ecs']->table('region'). " where region_id=$get_city_id");
    //print_r($city_name);
    $smarty->assign('get_city_id',$get_city_id);
    $smarty->assign('city_name',$city_name);
    $smarty->assign('where', 'subscribe');

    $smarty->assign('group_city', get_group_city());
    $smarty->assign('shop_notice', $_CFG['group_notice']);
    $smarty->display('group_subscribe.dwt');
}

?>