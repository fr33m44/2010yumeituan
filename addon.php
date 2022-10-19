<?php
/**
 * 渝美团功能扩展
 * @author tunpishuang
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
require_once(ROOT_PATH . 'includes/cls_json.php');
$json=new json();
if($_REQUEST['act'] == 'get_geo')
{
    ini_set('max_execution_time',0);
    $sql="select * from ecs_region";
    $all=$GLOBALS['db']->getAll($sql);

    foreach($all as $addr)
    {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://maps.google.com/maps/api/geocode/json?address='.  urlencode($addr['region_name']) .'&sensor=false');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	$data=curl_exec($ch);
	/*
	$lat=$data['results'][0]['geometry']['location']['lat'];
	print_r($lat);
	$lng=$data['results'][0]['geometry']['location']['lng'];
	print_r(' '.$lng);
	$sql="update ecs_region set latitude='$lat' , longitude='$lng' where region_id=$addr[region_id]";
	$GLOBALS['db']->query($sql);
	curl_close($ch);
	echo $sql.'<br />';
	 * 
	 */
	/*
	$fp=fopen($addr['region_id'].'.txt','w+');
	fwrite($fp,$data);
	fclose($fp);
	 *
	 */
	print_r($data); 

    }
}
if($_REQUEST['act'] == 'remove_tips')
{
    setcookie('ECS[show_tips]', 0, gmtime() + 86400 * 7);
    $msg=array(
	'status'=>1,
	'msg'=>'操作成功'
    );
    echo $json->encode($msg);
    exit;
}
if($_REQUEST['act'] == 'account_check')
{
    $msg=array();
    $sql="select * from ".$GLOBALS['ecs']->table('users');
    $username=trim($_POST['username']);
    $email=trim($_POST['email']);
    if(isset($_POST['username']) && !isset($_POST['email']))
    {
	$sql.=" where user_name='".$_POST['username']."'";
	$res=$GLOBALS['db']->getAll($sql);
	if($res)
	{
	    $msg['status']=0;
	    $msg['msg']='该用户名已被占用，另取一个用户名吧';
	}
	elseif(strlen($username)>16)
	{
	    $msg['status']=0;
	    $msg['msg']='用户名太长，最多 16 个字符或 8 个汉字';
	}
	elseif(strlen($username)<2)
	{
	    $msg['status']=0;
	    $msg['msg']='用户名太短，最少 1 个汉字或 2 个字符';
	}
	elseif( !preg_match("/^([{\x{4e00}-\x{9fa5}]|[0-9a-zA-Z])+$/u",$username) )
	{
	    $msg['status']=0;
	    $msg['msg']='用户名只能使用中文、英文和数字';
	}
	else
	{
	    $msg['status']=1;
	    $msg['msg']='验证成功';
	}

    }
    if(!isset($_POST['username']) && isset($_POST['email']))
    {
	$sql.=" where email='".$_POST['email']."'";
	$res=$GLOBALS['db']->getAll($sql);
	if($res)
	{
	    $msg['status']=0;
	    $msg['msg']='该邮箱已注册，可直接<a href="login.php">登录</a>';
	}
	else
	{
	    $msg['status']=1;
	    $msg['msg']='验证成功';
	}
    }
    echo $json->encode($msg);
    exit;
    
}
?>
