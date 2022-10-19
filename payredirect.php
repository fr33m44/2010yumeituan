<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/modules/payment/alipay.php');
$payment = payment_info(4);//支付宝
$payconfig=unserialize_config($payment['pay_config']);

unset($_POST['sign']);
unset($_POST['sign_type']);
$_POST['paymethod'] = 'bankPay';
print_r($_POST);
ksort($_POST);
reset($_POST);
$param = '';
$sign  = '';
foreach ($_POST AS $key => $val)
{
    $param .= "$key=" .urlencode($val). "&";
    $sign  .= "$key=$val&";
}
$param = substr($param, 0, -1);
$sign  = substr($sign, 0, -1). $payconfig['alipay_key'];
$param.="&sign=".md5($sign)."&sign_type=MD5";
header("location: https://www.alipay.com/cooperate/gateway.do?$param \n");


	
?>
