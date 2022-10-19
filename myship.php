<?php

/**
 * 团购券
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');
/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');
require_once(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/shopping_flow.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];
$smarty->assign('lang', $_LANG);

$action = trim($_GET['act']);
$user_id = $_SESSION['user_id'];
if ($user_id <= '0')
{
    $url = rewrite_groupurl('index.php');
    ecs_header("Location: $url\n");
    exit;
}
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
assign_public($city_id);
$action = trim($_POST['act']);
if ($action == 'act_shipset')
{
    $address = array(
	'user_id' => $user_id,
	'default' => 1,
	'address_id' => intval($_POST['address_id']),
	'country' => isset($_POST['country']) ? intval($_POST['country']) : 0,
	'province' => isset($_POST['province']) ? intval($_POST['province']) : 0,
	'city' => isset($_POST['city']) ? intval($_POST['city']) : 0,
	'district' => isset($_POST['district']) ? intval($_POST['district']) : 0,
	'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
	'consignee' => isset($_POST['consignee']) ? trim($_POST['consignee']) : '',
	'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
	'tel' => isset($_POST['tel']) ? make_semiangle(trim($_POST['tel'])) : '',
	'mobile' => isset($_POST['mobile']) ? make_semiangle(trim($_POST['mobile'])) : '',
	'best_time' => isset($_POST['best_time']) ? trim($_POST['best_time']) : '',
	'sign_building' => isset($_POST['sign_building']) ? trim($_POST['sign_building']) : '',
	'zipcode' => isset($_POST['zipcode']) ? make_semiangle(trim($_POST['zipcode'])) : '',
    );
    if ($address['consignee'] == '' || strlen($address['consignee']) < 6 || strlen($address['consignee']) > 45)
    {
	$msg = array(
	    'type' => 'error',
	    'content' => '请正确填写姓名，最少不低于2个字，最多不能超过15个字'
	);
    }
    elseif ($address['country'] == '')
    {
	$msg = array(
	    'type' => 'error',
	    'content' => '请选择国家'
	);
    }
    elseif ($address['province'] == '')
    {
	$msg = array(
	    'type' => 'error',
	    'content' => '请选择省'
	);
    }
    elseif ($address['city'] == '')
    {
	$msg = array(
	    'type' => 'error',
	    'content' => '请选择市'
	);
    }
    elseif ($address['district'] == '')
    {
	$msg = array(
	    'type' => 'error',
	    'content' => '请选择区'
	);
    }
    elseif ($address['address'] == '' || is_numeric($address['address']) || strlen($address['address']) < 15 || strlen($address['address']) > 180)
    {
	$msg = array(
	    'type' => 'error',
	    'content' => '请填写街道地址，最少5个字，最多不能超过60个字，不能全部为数字'
	);
    }
    elseif ($address['zipcode'] == '' || strlen($address['zipcode']) != '6' || !is_numeric($address['zipcode']))
    {
	$msg = array(
	    'type' => 'error',
	    'content' => '邮政编码填写有误，请输入6位邮政编码'
	);
    }
    elseif ($address['mobile'] == '' || strlen($address['mobile']) < 7)
    {
	$msg = array(
	    'type' => 'error',
	    'content' => '电话号码不能少于7位'
	);
    }
    elseif (update_address($address))
    {
	$msg = array(
	    'type' => 'success',
	    'content' => '操作成功'
	);
    }
    else
    {
	
    }
    $smarty->assign('msg', $msg);
    $smarty->assign('consignee', $address);
    $consignee_list = get_consignee_list($_SESSION['user_id']);
    $smarty->assign('name_of_region', array($_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']));
    $smarty->assign('consignee_list', $consignee_list);
    /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
    $smarty->assign('country_list', get_regions());
    $smarty->assign('shop_country', $_CFG['shop_country']);
    $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));
    /* 获得用户所有的收货人信息 */
    if ($_SESSION['user_id'] > 0)
    {
	$consignee_list = get_consignee_list($_SESSION['user_id']);

	if (count($consignee_list) < 5)
	{
	    /* 如果用户收货人信息的总数小于 5 则增加一个新的收货人信息 */
	    $consignee_list[] = array('country' => $_CFG['shop_country'], 'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '');
	}
    }
    else
    {
	if (isset($_SESSION['flow_consignee']))
	{
	    $consignee_list = array($_SESSION['flow_consignee']);
	}
	else
	{
	    $consignee_list[] = array('country' => $_CFG['shop_country']);
	}
    }
    /* 取得每个收货地址的省市区列表 */
    $province_list = array();
    $city_list = array();
    $district_list = array();
    foreach ($consignee_list as $region_id => $consignee)
    {
	$consignee['country'] = isset($consignee['country']) ? intval($consignee['country']) : 0;
	$consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
	$consignee['city'] = isset($consignee['city']) ? intval($consignee['city']) : 0;

	$province_list[$region_id] = get_regions(1, $consignee['country']);
	$city_list[$region_id] = get_regions(2, $consignee['province']);
	$district_list[$region_id] = get_regions(3, $consignee['city']);
    }
    $smarty->assign('province_list', $province_list);
    $smarty->assign('city_list', $city_list);
    $smarty->assign('district_list', $district_list);
}
else
{
    //用户配送信息
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_address') .
	    " WHERE user_id = '$user_id'";
    $consignee = $db->getRow($sql);
    $smarty->assign('consignee', $consignee);
    $smarty->assign('name_of_region', array($_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']));
    $consignee_list = get_consignee_list($_SESSION['user_id']);
    $smarty->assign('consignee_list', $consignee_list);
    /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
    $smarty->assign('country_list', get_regions());
    $smarty->assign('shop_country', $_CFG['shop_country']);
    $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));
    /* 取得每个收货地址的省市区列表 */
    $province_list = array();
    $city_list = array();
    $district_list = array();
    foreach ($consignee_list as $region_id => $consignee)
    {
	$consignee['country'] = isset($consignee['country']) ? intval($consignee['country']) : 0;
	$consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
	$consignee['city'] = isset($consignee['city']) ? intval($consignee['city']) : 0;

	$province_list[$region_id] = get_regions(1, $consignee['country']);
	$city_list[$region_id] = get_regions(2, $consignee['province']);
	$district_list[$region_id] = get_regions(3, $consignee['city']);
    }
    $smarty->assign('province_list', $province_list);
    $smarty->assign('city_list', $city_list);
    $smarty->assign('district_list', $district_list);
}
$smarty->assign('menu', 'ship');
$smarty->display('group_myship.dwt');
?>