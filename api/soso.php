<?php 

define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require('./init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');

header('Content-Type: application/xml; charset=' . EC_CHARSET);
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Thu, 27 Mar 1975 07:38:00 GMT');
header('Last-Modified: ' . date('r'));
header('Pragma: no-cache');
$publishdate = date('Y-m-d');

	$sql = "SELECT group_id,group_name, group_image,start_time,end_time,market_price,ext_info,city_id,small_desc,cat_id,is_finished,group_keywords " .
            ",lower_orders FROM " . $GLOBALS['ecs']->table('group_activity') ;
	$now = gmtime();
	$where = " WHERE start_time <= '$now' AND is_finished ='0'";
	$sql .= $where . " ORDER BY group_type ASC, start_time DESC";
    $res = $GLOBALS['db']->query($sql);
	$weburl = $ecs->url();

    $xml = '<?xml version="1.0" encoding="utf-8"?>'.
	      '<sdd><provider>' . $_CFG['group_shopname'] .
		   '</provider><version>1.0</version><dataServiceId>1_1</dataServiceId><datalist>';
    while ($group_buy = $GLOBALS['db']->fetchRow($res))
	{
	     $ext_info = unserialize($group_buy['ext_info']);
         $group_buy = array_merge($group_buy, $ext_info);
         $price_ladder = $group_buy['price_ladder'];
         if (!is_array($price_ladder) || empty($price_ladder))
         {
            $price_ladder = array(array('amount' => 0, 'price' => 0));
         }
         else
        {
           foreach ($price_ladder as $key => $amount_price)
           {   
            $price_ladder[$key]['formated_price'] = $amount_price['price'];
           }
		}
		$group_buy['orders_num'] = get_group_orders($group_buy['group_id']);
        $group_buy['price_ladder'] = $price_ladder;
	    $group_buy['group_price'] = $price_ladder[0]['formated_price'];
	    $group_buy['group_rebate'] = number_format($price_ladder[0]['price']/$group_buy['market_price'], 2, '.', '');
	    $group_buy['lack_price'] = group_price_format($group_buy['market_price']- $group_buy['group_price']);
	    $group_buy['market_price']= group_price_format($group_buy['market_price']);
	    $group_buy['group_price']= group_price_format($group_buy['group_price']);
		if ($group_buy['city_id'] > 0)
		{
          $sql = 'SELECT region_name FROM' . $ecs->table('region') . " WHERE region_id='$group_buy[city_id]'";
		  $group_buy['city_name'] = $db->getOne($sql);
		}
		if ($group_buy['suppliers_id'] > 0)
		{
		  $sql = "SELECT suppliers_name FROM " . $ecs->table('suppliers') . " WHERE suppliers_id='$group_buy[suppliers_id]'";
		  $group_buy['suppliers_name'] = $db->getOne($sql); 
		}
		$group_url = rewrite_groupurl('index.php',array('id' => $group_buy['group_id']));
        $xml .= '<item>';
		$xml .= '<keyword>' . $group_buy['group_keywords'] . '</keyword>';
        $xml .= '<url>'. $weburl . $group_url .'</url>';
		$xml .= '<creator>' . $weburl . '</creator>';
	    $xml .= '<title>' . $group_buy['group_name'] . '</title>';
		$xml .= '<publishdate>'.$publishdate.'</publishdate>';
        $xml .= '<imageaddress1>' . $weburl . $group_buy['group_image'] . '</imageaddress1>';
	    $xml .= '<imagealt1>' . $group_buy['group_name'] . '</imagealt1>';
        $xml .= '<imagelink1>' . $weburl . $group_buy['group_image'] . '</imagelink1>';
	    $xml .= '<content1>' . $group_buy['group_name'] . '</content1>';
	    $xml .= '<linktext1>' . $group_buy['group_name'] . '</linktext1>';
        $xml .= '<linktarget1>'. $weburl . $group_url .'</linktarget1>';
		$xml .= '<content2>'. $group_buy['market_price'] .'</content2>';
        $xml .= '<content3>'.$group_buy['group_price'].'</content3>';
        $xml .= '<content4>'. $group_buy['group_rebate'].'</content4>';
		$sql = 'SELECT cat_name  FROM' . $ecs->table('category') . " WHERE cat_id='$group_buy[cat_id]'";
		$cat_name = $db->getOne($sql);
		$xml .= '<content5>'.$cat_name.'</content5>';
        $xml .= '<content6>' . $group_buy['city_name'] . '</content6>';
	    $xml .= '<linktext2>'. $_CFG['group_shopname'] . '</linktext2>';
	    $xml .= '<linktarget2>'.$weburl.'</linktarget2>';
		$xml .= '<content7>'. $group_buy['orders_num'] . '</content7>';
		$xml .= '<content10>'.$_CFG['group_shopname'].'</content10>';
		if($group_buy['is_finished'] ==0 )
		{
		$content11="进行";
		}elseif($group_buy['is_finished'] ==2)
		{
		$content11="成功";
		}
		$xml .= '<content11>'.$content11.'</content11>';
		$xml .= '<content12></content12>';
        $xml .= '<content13>'. $group_buy['lower_orders'] . '</content13>';
		$xml .= '<content14></content14>';
		$xml .= '<content15>' . $_CFG['group_phone'] . '</content15>';
		$start_time=local_date("Y-m-d G:i:s", $group_buy['start_time']);
		$end_time=local_date("Y-m-d G:i:s", $group_buy['end_time']);
		$xml .= '<content8>'. $start_time .'</content8>';
        $xml .= '<content9>'. $end_time .'</content9>';
		$xml .= '<valid>1</valid></item>';
	}
	$xml .= '</datalist></sdd>';
echo $xml;
?>
