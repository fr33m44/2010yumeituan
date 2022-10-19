<?php

/*
 * 华唐短信接口
 * author   tunpishuang
 * blog	    tunps.com
 */

/**
 * Description of cls_sms_huatang
 *
 * @author tunpishuang
 */
if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

require_once(ROOT_PATH . 'includes/cls_transport.php');
require_once(ROOT_PATH . 'includes/shopex_json.php');

class sms_huatang
{

    var $api_urls = array(
	'Reg' => 'http://www.ht3g.com/htWS/Reg.aspx', //注册
	'UpdPwd' => 'http://www.ht3g.com/htWS/UpdPwd.aspx', //更改密码
	'UpdReg' => 'http://www.ht3g.com/htWS/UpdReg.aspx', //修改注册信息
	'SelSum' => 'http://www.ht3g.com/htWS/SelSum.aspx', //查询余额
	'Send' => 'http://www.ht3g.com/htWS/Send.aspx', //发送短信
	'BatchSend' => 'http://www.ht3g.com/htWS/BatchSend.aspx', //群发短信
	'Get' => 'http://www.ht3g.com/htWS/Get.aspx', //帐号充值
	'ChargeUp' => 'http://www.ht3g.com/htWS/ChargeUp.aspx', //帐号充值
	'UnReg' => 'http://www.ht3g.com/htWS/UnReg.aspx'//注销
    );
//    var $smscfg=array(
//	$CoprID => '', //帐号
//	$Pwd => '', //密码
//	$CorpName => '', //企业名称
//	$LinkMan => '', //联系人
//	$Tel => '', //联系电话
//	$Mobile => '', //联系人手机
//	$Email => '', //邮件
//	$Memo => '' //备注*/
//    );
    var $smscfg=array();
    //数据库
    var $db = null;
    var $ecs = null;
    var $t = null;
    /*
      var $msg  = array('api'       => array('msg_no' => null, 'msg' => ''),
      'server'    => array('msg_no' => null, 'msg' => ''));
     */
    var $msg = '';

    function __construct($smscfg)
    {
	$this->sms_huatang($smscfg);
    }

    function sms_huatang($smscfg)
    {
	/* 由于要包含init.php，所以这两个对象一定是存在的，因此直接赋值 */
	$this->db = $GLOBALS['db'];
	$this->ecs = $GLOBALS['ecs'];
	$this->smscfg=$smscfg;
	/* 此处最好不要从$GLOBALS数组里引用，防止出错 */
	$this->t = new transport(-1, -1, -1, false);
    }
    
    function Reg($CorpID, $Pwd, $CorpName, $LinkMan, $Tel, $Mobile, $Email, $Memo)
    {
	$url = $this->api_urls[__FUNCTION__];
	$params = array(
	    'CorpID' => $CorpID,
	    'Pwd' => $Pwd,
	    'CorpName' => $CorpName,
	    'LinkMan' => $LinkMan,
	    'Tel' => $Tel,
	    'Mobile' => $Mobile,
	    'Email' => $Email,
	    'Memo' => $Memo
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(0):$this->msg = '注册成功';
		break;
	    case(-1):$this->msg = '帐号已经注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '帐号密码不匹配';
		break;
	    }
	return $this->msg;
    }

    function UpdPwd($CorpID, $Pwd, $NewPwd)
    {
	$url = $this->api_urls[__FUNCTION__];
	$params = array(
	    'CorpID' => $CorpID,
	    'Pwd' => $Pwd,
	    'NewPwd' => $NewPwd
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(0):$this->msg = '密码修改成功';
		break;
	    case(-1):$this->msg = '帐号未注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '密码错误';
		break;
	    }
	return $this->msg;
    }

    function UpdReg($CorpID, $Pwd, $CorpName, $LinkMan, $Tel, $Mobile, $Email, $Memo)
    {
	$url = $this->api_urls[__FUNCTION__];
	$params = array(
	    'CorpID' => $CorpID,
	    'Pwd' => $Pwd,
	    'CorpName' => $CorpName,
	    'LinkMan' => $LinkMan,
	    'Tel' => $Tel,
	    'Mobile' => $Mobile,
	    'Email' => $Email,
	    'Memo' => $Memo
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(0):$this->msg = '修改成功';
		break;
	    case(-1):$this->msg = '帐号未注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '密码错误';
		break;
	    }
	return $this->msg;
    }

    function SelSum($CorpID, $Pwd)
    {
	$url = $this->api_urls[__FUNCTION__];
	$params = array(
	    'CorpID' => $CorpID,
	    'Pwd' => $Pwd,
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(-1):$this->msg = '帐号未注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '密码错误';
		break;
	    default:$this->msg = $response['body'];
	    }
	return $this->msg;
    }

    /*
     * 发送短信
     * @param $Moble 
     * @param $SendTime 固定14位长度字符串，比如：20060912152435代表2006年9月12日15时24分35秒，可为空)
     */

    function Send($CorpID, $Pwd, $Mobile, $Content, $Cell, $SendTime='')
    {
	$url = $this->api_urls[__FUNCTION__];
	$params = array(
	    'CorpID' => $CorpID,
	    'Pwd' => $Pwd,
	    'Mobile' => $Mobile,
	    'Content' => iconv("UTF-8",'GB2312',$Content),
	    'Cell' => $Cell,
	    'SendTime' => $SendTime
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(0):$this->msg = '发送成功';
		break;
	    case(-1):$this->msg = '帐号未注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '密码错误';
		break;
	    case(-4):$this->msg = '手机号格式不对';
		break;
	    case(-5):$this->msg = '余额不足';
		break;
	    case(-6):$this->msg = '定时发送时间不是有效的时间格式';
		break;
	    case(-7):$this->msg = '禁止10小时以内向同一手机号发送相同短信';
		break;
	    default:$this->msg = $response['body'];
	    }
	return $this->msg;
    }

    /*
     * 群发
     */

    function BatchSend($CopID, $Pwd, $Mobile, $Content, $Cell, $SendTime)
    {
	$url = $this->api_urls[__FUNCTION__];
	$params = array(
	    'CorpID' => $CorpID,
	    'Pwd' => $Pwd,
	    'Mobile' => $Mobile,
	    'Content' => $Content,
	    'Cell' => $Cell,
	    'SendTime' => $SendTime
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(0):$this->msg = '发送成功进入审核阶段';
	    case(1):$this->msg = '直接发送成功';
	    case(-1):$this->msg = '帐号未注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '密码错误';
		break;
	    case(-4):$this->msg = '一次提交信息不能超过600个手机号码';
		break;
	    case(-5):$this->msg = '余额不足';
		break;
	    case(-6):$this->msg = '定时发送时间不是有效的时间格式';
		break;
	    case(-7):$this->msg = '发送短信内容包含黑字典关键字';
		break;
	    case(-8):$this->msg = '发送内容需在3到250个字之间';
		break;
	    case(-9):$this->msg = '发送号码为空';
		break;
	    default:$this->msg = $response['body'];
	    }
	return $this->msg;
    }

    /*
     * 接受短信
     * 输出短信内容字符串
     */

    function Get($CorpID, $Pwd)
    {
	$url = $this->api_urls[__FUNCTION__];
	$params = array(
	    'CorpID' => $CorpID,
	    'Pwd' => $Pwd
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(-1):$this->msg = '帐号未注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '密码错误';
		break;
	    default:$this->msg = $response['body'];
	    }
	return $this->msg;
    }

    function ChargeUp($CorpID, $Pwd, $CardNo, $CardPwd)
    {
	$url = $this->api_urls[__FUNCTION__];
	$params=array(
	'CorpID' => $CorpID,
	'Pwd' => $Pwd,
	'CardNo' => $CardNo,
	'CardPwd' =>$CardPwd
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(0):$this->msg='充值成功';
		break;
	    case(-1):$this->msg = '帐号未注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '密码错误';
		break;
	    case(-7):$this->msg = '充值失败（可能原因：充值卡号密码不匹配或者卡已经被使用）';
		break;
	    default:$this->msg = $response['body'];
	    }
	return $this->msg;
    }
    function UnReg($CorpID,$Pwd)
    {
	$url = $this->api_urls[__FUNCTION__];
	$params=array(
	'CorpID' => $CorpID,
	'Pwd' => $Pwd
	);
	$response = $this->t->request($url, $params);
	switch ($response['body'])
	    {
	    case(0):$this->msg='注销成功';
		break;
	    case(-1):$this->msg = '帐号未注册';
		break;
	    case(-2):$this->msg = '其他错误';
		break;
	    case(-3):$this->msg = '密码错误';
		break;
	    default:$this->msg = $response['body'];
	    }
	return $this->msg;
    }

}

?>
