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

	$sql = "SELECT group_id,group_name, group_image,start_time,end_time,market_price,ext_info,city_id " .
            "FROM " . $GLOBALS['ecs']->table('group_activity') ;
	$now = gmtime();
	$where = " WHERE start_time <= '$now' AND is_finished ='0'";
	$sql .= $where . " ORDER BY group_type ASC, start_time DESC";
    $res = $GLOBALS['db']->query($sql);
	$weburl = $ecs->url();
    $xml = '<?xml version="1.0" encoding="utf-8"?><urlset>';
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
		$group_url = rewrite_groupurl('index.php',array('id' => $group_buy['group_id']));
        $xml .= '<url>';
        $xml .= '<loc>'.$weburl . $group_url .'</loc>';
        $xml .= '<data>';
        $xml .= '<display>';
        $xml .= '<website>'. $_CFG['group_shopname'] . '</website>';
        $xml .= '<siteurl>'. $weburl .'</siteurl>';
        $xml .= '<city>'. $group_buy['city_name'] .'</city>';
        $xml .= '<title>' . $group_buy['group_name'] . '</title>';
        $xml .= '<image>' . $weburl . $group_buy['group_image'] .'</image>';
        $xml .= '<startTime>'. $group_buy['start_time'] .'</startTime>';
        $xml .=  '<endTime>'. $group_buy['end_time'] .'</endTime>';
        $xml .= '<value>'. $group_buy['market_price'] .'</value>';
        $xml .= '<price>'.$group_buy['group_price'].'</price>';
        $xml .= '<rebate>'. $group_buy['group_rebate'].'</rebate>';
        $xml .= '<bought>'. $group_buy['orders_num'] . '</bought>';
        $xml .= '</display></data></url>';
	}
	$xml .= '</urlset>';
echo $xml;
?>