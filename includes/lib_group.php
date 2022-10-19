<?php

/**
 * groupon库函数
 */


/*
 * 一个城市的最新一个团购的group_id
 * 用于意见反馈/2010.11.23
 */
function get_group_maxid($city_id)
{
    $sql = "select max(group_id) from ecs_group_activity where city_id=" . $city_id;
    return $max_group_id = $GLOBALS['db']->getOne($sql);
}

/*
 * 天气预报/tun/2010/11/11 棍节快乐 - -#
 */

function get_weather($city_id, $city_name)
{
    $time = gmtime();
    //先查询该城市天气是否过期
    $sql = "select * from " . $GLOBALS['ecs']->table('group_city') . " where city_id='$city_id' and $time-weather_time<=86400";
    $row = $GLOBALS['db']->getRow($sql);
    if (!$row)//没有数据就到sina上去抓取
    {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://php.weather.sina.com.cn/search.php?c=1&city=' . $city_name . '&dpc=1');
	curl_setopt($curl, CURLOPT_REFERER, 'http://php.weather.sina.com.cn');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
	$data = curl_exec($curl);
	$data = mb_convert_encoding($data, 'utf-8', 'gb2312');
	preg_match('/javascript:sent_to_vb\(\'(.*)\',\'(.*)：(.*)，(.*)，(.*)，(.*)\'\)/', $data, $weather_arr);
	preg_match('/class=\"icon_weather\" style=\"background: url\((.*)\) 0 0/', $data, $weather_img);
	$w = array();
	$w['weather'] = $weather_arr[3];
	$w['temp'] = str_replace('～', ' / ', $weather_arr[4]);
	$w['image'] = substr($weather_img[1], strrpos($weather_img[1], '/') + 1, str_len($weather_img[1]) - strrpos($weather_img[1], '/'));
	$w['weather_time'] = gmstr2time(date('Y-m-d'));
	$_CFG = load_config();
	$template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];
	if (!file_exists($filename = $template_dir . '/' . images . '/' . weather . '/' . $w['image']))
	{   //本地不存在图片就去下载
	    $weather_img[1] = str_replace('180_180', '78_78', $weather_img[1]);
	    curl_setopt($curl, CURLOPT_URL, $weather_img[1]);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    $img = curl_exec($curl);
	    $fp = fopen($filename, 'w+');
	    fwrite($fp, $img);
	    fclose($fp);
	}
	curl_close($curl);
	//更新天气信息
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('group_city'), $w, 'UPDADTE', 'city_id=' . $city_id);
	$w['city_name'] = $weather_arr[1];
	return $w;
    }
    else
    {
	return $row;
    }
}

/*
 * 团购信息
 */

function get_group_buy_info($group_buy_id = 0, $city_id = 0, $cat_id = 0)
{
    /* 取得团购活动信息 */
    $group_buy_id = intval($group_buy_id);
    $sql = "SELECT * , start_time AS start_date, end_time AS end_date " .
	    "FROM " . $GLOBALS['ecs']->table('group_activity');
    $now = gmtime();
    if ($group_buy_id <= 0)
    {
	$sql .= " WHERE  start_time <= '$now' AND is_finished ='0'";
	if ($city_id > 0)
	{
	    $sql .= " AND (city_id='$city_id' OR " . get_expand_city($city_id) . ")";
	}
	if ($cat_id > 0)
	{
	    $sql .= " AND cat_id='$cat_id'";
	}
	$sql .= " ORDER BY group_type ASC, start_time DESC LIMIT 1";
    }
    else
    {
	$sql .= " WHERE group_id = '$group_buy_id'";
    }
    $group_buy = $GLOBALS['db']->getRow($sql);
    /* 如果为空，返回空数组 */
    if (empty($group_buy))
    {
	return array();
    }

    $ext_info = unserialize($group_buy['ext_info']);
    $group_buy = array_merge($group_buy, $ext_info);

    /* 格式化时间 */
    $group_buy['formated_start_date'] = local_date('Y-m-d H:i:s', $group_buy['start_date']);
    $group_buy['formated_end_date'] = local_date('Y-m-d H:i:s', $group_buy['end_date']);
    $group_buy['formated_past_date'] = local_date('Y-m-d', $group_buy['past_time']);
    $group_buy['formated_past_date_start'] = local_date('Y-m-d', $group_buy['past_time_start']);

    /* 处理价格阶梯 */
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
    $group_buy['price_ladder'] = $price_ladder;
    $group_buy['group_price'] = $price_ladder[0]['formated_price'];
    //节省好多钱/tun
    $group_buy['lack_price'] = $group_buy['market_price'] - $group_buy['group_price'];
    //多少人购买/tun
    $group_buy['orders_num'] = get_group_orders($group_buy['group_id'], 2);

    $group_buy['group_rebate'] = number_format($price_ladder[0]['price'] / $group_buy['market_price'], 2, '.', '') * 10;
    $group_buy['formated_lack_price'] = group_price_format($group_buy['market_price'] - $group_buy['group_price']);
    $group_buy['formated_market_price'] = group_price_format($group_buy['market_price']);
    $group_buy['formated_group_price'] = group_price_format($group_buy['group_price']);
    $group_buy['formated_goods_rebate'] = group_price_format($group_buy['goods_rebate']);
    $group_buy['url'] = rewrite_groupurl('index.php', array('id' => $group_buy['group_id']));
    //用于email的urlencode
    $group_buy['url_urlencoded'] = urlencode(mb_convert_encoding('http://' . $_SERVER["HTTP_HOST"] . "/" . $group_buy['url'], 'gb2312', 'utf-8'));
    $group_buy['group_name_urlencoded'] = urlencode(mb_convert_encoding($group_buy['group_name'], 'gb2312', 'utf-8'));
    $group_buy['group_image_urlencoded'] = urlencode($GLOBALS['ecs']->get_domain() . '/' . $group_buy['group_image']);
    return $group_buy;
}

/*
 * 取得视频信息/tun/2010.11.08
 */

function get_video($group_id)
{
    $localtime = local_date($format, $time);
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('group_video') . " WHERE group_id=" . $group_id . " AND start_time <=" . (gmtime()) . " AND end_time >=" . (gmtime());
    return $GLOBALS['db']->getAll($sql);
}

/*
 * 今日团购列表
 */

function get_today_grouplist($group_id = 0, $city_id = 0, $cat_id = 0)
{
    /* 取得团购活动信息 */
    $sql = "SELECT * " .
	    "FROM " . $GLOBALS['ecs']->table('group_activity');
    $now = gmtime();
    $where = " WHERE start_time <= '$now' AND is_finished ='0' AND group_id <> '$group_id'";
    if ($city_id > 0)
    {
	$where .= " AND (city_id='$city_id' OR " . get_expand_city($city_id) . ")";
    }
    if ($cat_id > 0)
    {
	$where .= " AND cat_id='$cat_id'";
    }
    $sql .= $where . " ORDER BY start_time DESC, group_type ASC";
    if ($GLOBALS['_CFG']['left_group_num'] > 0)
    {
	$sql .= " LIMIT " . $GLOBALS['_CFG']['left_group_num'];
    }
    $res = $GLOBALS['db']->query($sql);
    $group_buy = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
	$row['url'] = rewrite_groupurl('index.php', array('id' => $row['group_id']));
	$group_buy[] = $row;
    }
    return $group_buy;
}

/*
 * 取得某团购活动统计信息
 * @param   int     $group_buy_id   团购活动id
 * @param   float   $deposit        保证金
 * @return  array   统计信息
 *                  total_order     总订单数
 *                  total_goods     总商品数
 *                  valid_order     有效订单数
 *                  valid_goods     有效商品数
 */

function get_group_buy_stat($group_buy_id)
{
    $group_buy_id = intval($group_buy_id);

    /* 取得总订单数和总商品数 */
    $all_stat = array();
    $actual_stat = array();
    $sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " .
	    "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
	    $GLOBALS['ecs']->table('order_goods') . " AS g " .
	    " WHERE o.order_id = g.order_id " .
	    "AND o.extension_code = 'group_buy' " .
	    "AND o.extension_id = '$group_buy_id' " .
	    "AND g.goods_id = '$group_buy_id' " .
	    "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
    $all_stat = $GLOBALS['db']->getRow($sql);

    if ($all_stat['total_order'] == 0)
    {
	$all_stat['total_goods'] = 0;
    }

    $sql = "SELECT COUNT(*) AS actual_order, SUM(g.goods_number) AS actual_goods,SUM(o.bonus) AS actual_bonus,SUM(o.surplus) AS actual_surplus," .
	    "SUM(o.money_paid) AS actual_money,SUM(o.bonus+o.surplus+o.money_paid) AS actual_amount " .
	    "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
	    $GLOBALS['ecs']->table('order_goods') . " AS g " .
	    " WHERE o.order_id = g.order_id " .
	    "AND o.extension_code = 'group_buy' " .
	    "AND o.extension_id = '$group_buy_id' " .
	    "AND g.goods_id = '$group_buy_id' " .
	    " AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
	    " AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING));
    $actual_stat = $GLOBALS['db']->getRow($sql);
    if ($actual_stat['actual_order'] == 0)
    {
	$actual_stat['actual_goods'] = 0;
	$actual_stat['actual_surplus'] = 0;
	$actual_stat['actual_money'] = 0;
	$actual_stat['actual_bonus'] = 0;
	$actual_stat['actual_amount'] = 0;
    }
    $actual_stat['formated_actual_surplus'] = group_price_format($actual_stat['actual_surplus']);
    $actual_stat['formated_actual_money'] = group_price_format($actual_stat['actual_money']);
    $actual_stat['formated_actual_bonus'] = group_price_format($actual_stat['actual_bonus']);
    $actual_stat['formated_actual_amount'] = group_price_format($actual_stat['actual_amount']);
    $all_stat = array_merge($all_stat, $actual_stat);
    return $all_stat;
}

/**
 * 获得团购的状态
 *
 * @access  public
 * @param   array
 * @return  integer
 */
function get_group_buy_status($group_buy)
{
    $now = gmtime();
    if ($group_buy['is_finished'] == 0)
    {
	/* 未处理 */
	if ($now < $group_buy['start_time'])
	{
	    $status = GBS_PRE_START;
	}
	elseif ($now > $group_buy['end_time'])
	{
	    $status = GBS_FINISHED;
	}
	else
	{
	    if ($group_buy['succeed_time'] > 0)
	    {
		$status = GBS_SUCCEED;
	    }
	    else
	    {
		$status = GBS_UNDER_WAY;
	    }
	}
    }
    elseif ($group_buy['is_finished'] == GBS_FAIL)
    {
	/* 已处理，团购失败 */
	$status = GBS_FAIL;
    }
    elseif ($group_buy['is_finished'] == GBS_FINISHED)
    {
	$status = GBS_FINISHED;
    }
    else
    {
	$status = 5;
    }


    return $status;
}

/*
 * 团购网友评论信息
 */

function get_friend_comment($group_id)
{
    $fcomment = array();
    if ($group_id > 0)
    {
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('friend_comment') .
		" WHERE group_id = '$group_id'";
	$fcomment = $GLOBALS['db']->getAll($sql);
    }

    return $fcomment;
}

/*
 * gmt时间戳
 */

function insert_now_time()
{
    return gmtime();
}

/*
 * 团购订单列表
 */

function get_group_orders($group_buy_id, $group_need = 1)
{
    $sql = '';
    if ($group_need == 1)
    {
	$sql = "SELECT SUM(g.goods_number) AS total_goods " .
		"FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
		$GLOBALS['ecs']->table('order_goods') . " AS g " .
		" WHERE o.order_id = g.order_id " .
		"AND o.extension_code = 'group_buy' " .
		"AND o.extension_id = '$group_buy_id' " .
		"AND g.goods_id = '$group_buy_id' " .
		" AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
		" AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING));
    }
    elseif ($group_need == 2)
    {
	$sql = "SELECT COUNT(*) AS total_order " .
		"FROM " . $GLOBALS['ecs']->table('order_info') . " AS o" .
		" WHERE o.extension_code = 'group_buy'" .
		" AND o.extension_id = '$group_buy_id'" .
		" AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
		" AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING));
    }
    elseif ($group_need == 3)
    {
	$sql = "SELECT COUNT(DISTINCT o.user_id) AS total_order " .
		"FROM " . $GLOBALS['ecs']->table('order_info') . " AS o" .
		" WHERE o.extension_code = 'group_buy'" .
		" AND o.extension_id = '$group_buy_id'" .
		" AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
		" AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING));
    }
    if ($sql != '')
    {
	$orders_num = $GLOBALS['db']->getOne($sql);
    }
    $orders_num = $orders_num > 0 ? $orders_num : 0;
    return $orders_num;
}

function get_group_goods_order($id)
{
    $sql = "select oi.*, og.goods_name, og.goods_number,  goods_price* goods_number AS all_goods_price,  market_price* goods_number AS all_market_price, goods_price from " . $GLOBALS['ecs']->table('order_info') .
	    "oi LEFT JOIN " . $GLOBALS['ecs']->table('order_goods') . "og ON oi.order_id=og.order_id AND oi.order_id=$id";

    $row = $GLOBALS['db']->getRow($sql);
    $row['subtotal'] = $row['all_goods_price'];
    $row['formated_subtotal'] = group_price_format($row['all_goods_price']);
    $row['formated_goods_price'] = group_price_format($row['goods_price']);
    $row['market_price'] = group_price_format($row['market_price']);
    $row['goods_amount'] = $row['all_goods_price'];

    $ror['saving'] = group_price_format($row['all_market_price'] - $row['all_goods_price']);
    $row['formated_goods_amount'] = group_price_format($row['all_goods_price']);
    $row['formated_market_amount'] = group_price_format($row['all_market_price']);

    return $row;
}

/*
 * 团购产品信息
 */

function get_group_goods()
{

    $sql = "SELECT *, goods_price*goods_number AS all_goods_price,market_price*goods_number AS all_market_price" .
	    " FROM " . $GLOBALS['ecs']->table('cart') . " " .
	    " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GROUP_BUY_GOODS . "'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
	if ($row['is_shipping'] == '1' || $row['is_real'] == '1')
	{
	    $row['is_fee_shipping'] = true;
	}
	else
	{
	    $row['is_fee_shipping'] = false;
	}
	//tun
	$row['extension_id'] = $row['goods_id'];
	$row['subtotal'] = $row['all_goods_price'];
	$row['formated_subtotal'] = group_price_format($row['all_goods_price']);
	$row['formated_goods_price'] = group_price_format($row['goods_price']);
	$row['market_price'] = group_price_format($row['market_price']);
	$row['goods_amount'] = $row['all_goods_price'];

	$ror['saving'] = group_price_format($row['all_market_price'] - $row['all_goods_price']);
	$row['formated_goods_amount'] = group_price_format($row['all_goods_price']);
	$row['formated_market_amount'] = group_price_format($row['all_market_price']);
    }
    else
    {
	$row = array();
    }
    return $row;
}

/*
 * 团购收货人
 */

function get_group_consignee($user_id)
{
    if (isset($_SESSION['flow_consignee']))
    {
	/* 如果存在session，则直接返回session中的收货人信息 */

	return $_SESSION['flow_consignee'];
    }
    else
    {
	/* 如果不存在，则取得用户的默认收货人信息 */
	$arr = array();

	if ($user_id > 0)
	{
	    /* 取默认地址 */
	    $sql = "SELECT ua.* " .
		    " FROM " . $GLOBALS['ecs']->table('user_address') . "AS ua, " . $GLOBALS['ecs']->table('users') . ' AS u ' .
		    " WHERE u.user_id='$user_id' AND ua.address_id = u.address_id limit 1";
	    $arr = $GLOBALS['db']->getRow($sql);
	}
	return $arr;
    }
}

/*
 * 团购状态 
 */

function insert_group_stats($arr)
{
    $need_cache = $GLOBALS['smarty']->caching;
    $need_compile = $GLOBALS['smarty']->force_compile;

    $GLOBALS['smarty']->caching = false;
    $GLOBALS['smarty']->force_compile = true;

    $group_buy_id = intval($arr['group_id']);

    /* 取得总订单数和总商品数 */
    $sql = "SELECT group_id,is_finished ,start_time,end_time,upper_orders,lower_orders,ext_info" .
	    ",group_restricted,group_need,market_price,succeed_time,group_stock,goods_type" .
	    " FROM " . $GLOBALS['ecs']->table('group_activity') . " WHERE group_id = '$group_buy_id' ";
    $group_buy = $GLOBALS['db']->getRow($sql);
    $orders_num = get_group_orders($group_buy_id, $group_buy['group_need']);
    $ext_info = unserialize($group_buy['ext_info']);
    $group_buy = array_merge($group_buy, $ext_info);
    /* 处理价格阶梯 */
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
    $group_buy['price_ladder'] = $price_ladder;
    $group_buy['group_price'] = $price_ladder[0]['formated_price'];
    $group_buy['group_rebate'] = number_format($price_ladder[0]['price'] / $group_buy['market_price'], 2, '.', '') * 10;
    $group_buy['formated_lack_price'] = group_price_format($group_buy['market_price'] - $group_buy['group_price']);
    $group_buy['formated_market_price'] = group_price_format($group_buy['market_price']);
    $group_buy['formated_group_price'] = group_price_format($group_buy['group_price']);
    if ($group_buy['is_finished'] == 0)
    {
	/* 未处理 */
	$now = gmtime();
	if ($now < $group_buy['start_time'])
	{
	    $status = GBS_PRE_START;
	}
	elseif ($now > $group_buy['end_time'])
	{
	    if ($group_buy['succeed_time'] > 0)
	    {
		$status = GBS_FINISHED;
		$sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') .
			" SET is_finished='$status',closed_time='$now' WHERE group_id='$group_buy_id'";
		$GLOBALS['db']->query($sql);
	    }
	    else
	    {
		$status = GBS_FAIL;
		$sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') .
			" SET is_finished='$status',closed_time='$now' WHERE group_id='$group_buy_id'";
		$GLOBALS['db']->query($sql);
	    }
	}
	else
	{
	    $status = GBS_UNDER_WAY;
	    if (empty($group_buy['succeed_time']) || $group_buy['succeed_time'] == '0')
	    {
		if ($orders_num >= $group_buy['lower_orders'])
		{

		    $add_time = get_success_time($group_buy_id);
		    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') . " SET succeed_time='$add_time' WHERE group_id='$group_buy_id'";
		    $GLOBALS['db']->query($sql);
		    $group_buy['succeed_time'] = $add_time;
		    if ($group_buy['goods_type'] == 1 && $orders_num > 0 && !$GLOBALS['_CFG']['make_group_card'])
		    {
			$is_send = !$GLOBALS['_CFG']['send_group_sms'];
			send_oldgroup_cards($group_buy_id, $is_send);
		    }
		}
	    }
	}
    }
    elseif ($group_buy['is_finished'] == GBS_FAIL)
    {
	/* 已处理，团购失败 */
	$status = GBS_FAIL;
    }
    elseif ($group_buy['is_finished'] == GBS_FINISHED)
    {
	$status = GBS_FINISHED;
    }
    else
    {
	$status = 5;
    }
    $group_buy['succeed_time_ymd'] = local_date('Y年m月d日', $group_buy['succeed_time']);
    $group_buy['succeed_time_his'] = local_date('H时i分s秒', $group_buy['succeed_time']);

    $group_buy['closed_time_date'] = local_date('H:i:s', $group_buy['closed_time']);
    $group_buy['status'] = $status;
    $group_buy['orders_num'] = $orders_num;
    if ($group_buy['succeed_time'] > 0)
    {
	$GLOBALS['smarty']->assign('is_succes', 1);
    }
    $GLOBALS['smarty']->assign('group_buy', $group_buy);
    $group_text = $GLOBALS['smarty']->fetch('library/group_status.lbi');
    $GLOBALS['smarty']->caching = $need_cache;
    $GLOBALS['smarty']->force_compile = $need_compile;

    return $group_text;
}

function get_group_insert($group_buy_id, $number)
{
    $sql = "SELECT group_id,is_finished ,ext_info" .
	    ",pos_express,goods_rebate,goods_type,group_restricted,group_stock,market_price,group_name,group_need,upper_orders" .
	    " FROM " . $GLOBALS['ecs']->table('group_activity')
	    . " WHERE group_id = '$group_buy_id' ";

    $group_buy = $GLOBALS['db']->getRow($sql);
    $ext_info = unserialize($group_buy['ext_info']);
    $group_buy = array_merge($group_buy, $ext_info);
    $group_buy['number'] = $number;
    if ($group_buy['group_restricted'] > 0 && $group_buy['number'] > $group_buy['group_restricted'])
    {
	$group_buy['number'] = $group_buy['group_restricted'];
    }
    if ($group_buy['group_need'] == 1)
    {
	if ($group_buy['upper_orders'] > 0 && $group_buy['number'] > $group_buy['upper_orders'])
	{
	    $group_buy['number'] = $group_buy['upper_orders'];
	}
    }
    else
    {
	if ($group_buy['group_stock'] > 0 && $group_buy['number'] > $group_buy['group_stock'])
	{
	    $group_buy['number'] = $group_buy['group_stock'];
	}
    }
    if (($group_buy['pos_express'] > 0 && $group_buy['number'] >= $group_buy['pos_express']) || $group_buy['goods_type'] == '1')
    {
	$group_buy['is_shipping'] = 1;
    }
    else
    {
	$group_buy['is_shipping'] = 0;
    }
    /* 处理价格阶梯 */
    $group_price_arr = array();
    $price_ladder = $group_buy['price_ladder'];
    if (!is_array($price_ladder) || empty($price_ladder))
    {
	$price_ladder = array(array('amount' => 0, 'price' => 0));
	$group_buy['group_price'] = '0';
    }
    else
    {
	foreach ($price_ladder as $key => $amount_price)
	{
	    if ($group_buy['number'] >= $amount_price['amount'])
	    {
		$group_price_arr['amount'] = $amount_price['amount'];
		$group_price_arr['price'] = $amount_price['price'];
	    }
	}
    }
    if ($group_price_arr['amount'] == $group_buy['number'])
    {
	$group_buy['group_price'] = $group_price_arr['price'] / $group_price_arr['amount'];
    }
    else
    {
	$group_buy['group_price'] = $group_price_arr['price'] / $group_price_arr['amount'];
    }
    return $group_buy;
}

function get_success_time($group_buy_id)
{
    $sql = "SELECT o.add_time " .
	    "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o" .
	    " WHERE o.extension_code = 'group_buy'" .
	    " AND o.extension_id = '$group_buy_id'" .
	    " AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
	    " AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) .
	    " ORDER BY o.order_id DESC LIMIT 0,1";

    $add_time = $GLOBALS['db']->getOne($sql);

    return $add_time;
}

/*
 * 团购送货费用
 */

function get_shipping_free($group_id)
{
    if ($group_id > 0)
    {
	$sql = "SELECT group_freight FROM " . $GLOBALS['ecs']->table('group_activity') . " WHERE group_id='$group_id'";
	$shipping_fee = $GLOBALS['db']->getOne($sql);
    }
    else
    {
	$shipping_fee = 0;
    }
    return $shipping_fee;
}

/*
 * 团购会员信息
 */

function insert_group_member_info()
{
    $need_cache = $GLOBALS['smarty']->caching;
    $GLOBALS['smarty']->caching = false;

    if ($_SESSION['user_id'] > 0)
    {
	$GLOBALS['smarty']->assign('user_info', get_user_info());
    }
    $GLOBALS['smarty']->assign('group_shopname', $GLOBALS['_CFG']['group_shopname']);
    $output = $GLOBALS['smarty']->fetch('library/group_member_info.lbi');

    $GLOBALS['smarty']->caching = $need_cache;

    return $output;
}

function get_group_comment($id = 0, $num = 3)
{
    //修改为只调用答疑/不要求购转让
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('comment') .
	    " WHERE  comment_type = '2' AND status = 1 AND parent_id = 0";
    if ($id > 0)
    {
	$sql .= " AND id_value = '$id'";
    }
    $sql .= " ORDER BY comment_id DESC LIMIT $num";
    $res = $GLOBALS['db']->query($sql);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
	$arr[$row['comment_id']]['id'] = $row['comment_id'];
	$arr[$row['comment_id']]['email'] = $row['email'];
	$arr[$row['comment_id']]['username'] = $row['user_name'];
	$arr[$row['comment_id']]['content'] = str_replace('\r\n', '<br />', htmlspecialchars($row['content']));
	$arr[$row['comment_id']]['content'] = nl2br(str_replace('\n', '<br />', $arr[$row['comment_id']]['content']));
	$arr[$row['comment_id']]['rank'] = $row['comment_rank'];
	$arr[$row['comment_id']]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
	$arr[$row['comment_id']]['url'] = rewrite_groupurl('ask.php', array('gid' => $id));
    }
    return $arr;
}

function get_group_comment_count($city_id = '')
{
    $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('comment') .
	    " WHERE  comment_type = '2' AND status = 1 AND parent_id = 0";
    if ($city_id > 0)
    {
	$sql .= " AND city_id='$city_id'";
    }
    $count = $GLOBALS['db']->getOne($sql);

    return $count;
}

/*
 * 返回开放了团购的城市
 */

function get_group_city()
{
    $sql = 'SELECT city_id,city_name FROM ' . $GLOBALS['ecs']->table('group_city') . " WHERE is_open='1' ORDER BY city_sort DESC,city_id ASC";
    $res = $GLOBALS['db']->query($sql);
    $city_list = array();
    while ($row = $GLOBALS['db']->FetchRow($res))
    {
	$row['url'] = rewrite_groupurl('index.php', array('cityid' => $row['city_id']), true);
	$city_list[] = $row;
    }
    return $city_list;
}

/*
 * 返回城市信息
 */

function get_city_info($city_id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('group_city') . " WHERE city_id='$city_id'";
    $row = $GLOBALS['db']->getRow($sql);
    $row['city_name_urlencoded'] = urlencode(mb_convert_encoding($row['city_name'], 'gb2312', 'utf-8'));
    return $row;
}

/*
 * 字符串递增
 */

function plus($num)
{
    $len = strlen($num);
    $num++;
    while (strlen($num) < $len)
    {
	$num = "0" . $num;
    }
    return $num;
}

/*
 * 发送团购券
 */

function send_group_cards($order_id, $order_sn, $user_id, $mobile, $is_send = true)
{
    $sql = 'SELECT g.goods_id,ga.group_id AS group_id, ga.goods_name, goods_number AS num, ga.succeed_time,ga.past_time FROM ' .
	    $GLOBALS['ecs']->table('order_goods') . " AS g ," . $GLOBALS['ecs']->table('group_activity') . " AS ga " .
	    " WHERE ga.group_id=g.goods_id AND g.order_id = '$order_id' AND g.extension_code = 'group_buy' AND g.is_real = 1";
    $group_buy = $GLOBALS['db']->getRow($sql);
    if (!empty($group_buy))
    {
	if ($is_send)
	{
	    include_once(ROOT_PATH . 'includes/cls_sms.php');
	    $sms = new sms();
	    $tpl = get_sms_template('send_sms');
	}
	$add_date = gmtime();
	$group_id = $group_buy['goods_id'];
	$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('group_card') .
		" WHERE group_id = '$group_id' AND user_id = '$user_id' AND order_sn='$order_sn'";
	$group_card = $GLOBALS['db']->getAll($sql);

	if (!empty($group_card))
	{
	    if ($is_send)
	    {
		foreach ($group_card AS $row)
		{
		    if ($GLOBALS['_CFG']['send_sms_num'] > 0 && $row['send_num'] >= $GLOBALS['_CFG']['send_sms_num'])
		    {
			continue;
		    }
		    $GLOBALS['smarty']->assign('group_name', $group_buy['goods_name']);
		    $GLOBALS['smarty']->assign('card_sn', $row['card_sn']);
		    $GLOBALS['smarty']->assign('card_password', $row['card_password']);
		    $GLOBALS['smarty']->assign('past_time', local_date('Y-m-d', $row['end_date']));
		    $msg = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
		    if ($sms->send($mobile, $msg, 0))
		    {
			$sql = "UPDATE " . $GLOBALS['ecs']->table('group_card') . " SET send_num=send_num+1 WHERE card_id='$row[card_id]'";
			$GLOBALS['db']->query($sql);
		    }
		}
	    }
	}
	else
	{
	    $is_saled = 0;
	    if ($group_buy['succeed_time'] > 0)
	    {
		$is_saled = 1;
	    }
	    $end_date = $group_buy['past_time'];
	    srand((double) microtime() * 1000000);
	    $randval = rand();
	    $new_cards = array();
	    for ($i = 0; $i < $group_buy['num']; $i++)
	    {
		//将group_card表里面的第1个card_sn作为基数来递增
		$sql = "select MAX(card_sn) from " . $GLOBALS['ecs']->table('group_card') . " where group_id=" . $group_buy['group_id'];
		$cards_sn = $GLOBALS['db']->getOne($sql);
		if ($cards_sn)
		{
		    $card_sn = plus($cards_sn);
		}
		else
		{
		    //$card_sn = str_pad(mt_rand(0, 99999999), 6, '0', STR_PAD_LEFT);
		    $card_sn = generate_word(8);
		}
		//$card_password = get_rndcode();
		$card_password = generate_word(8);

		$sql = "INSERT INTO " . $GLOBALS['ecs']->table('group_card') .
			"(group_id,card_sn,card_password,add_date,order_sn,user_id,end_date,is_saled)" .
			"VALUES('$group_id', '$card_sn','$card_password','$add_date','$order_sn','$user_id','$end_date','$is_saled')";
		$GLOBALS['db']->query($sql);
		$card_id = $GLOBALS['db']->insert_id();
		$cards = array('card_id' => $card_id, 'card_sn' => $card_sn, 'card_password' => $card_password);
		$new_cards[] = $cards;
	    }
	    if ($is_send && $group_buy['succeed_time'] > 0)
	    {
		foreach ($new_cards as $cards)
		{
		    $GLOBALS['smarty']->assign('group_name', $group_buy['goods_name']);
		    $GLOBALS['smarty']->assign('card_sn', $cards['card_sn']);
		    $GLOBALS['smarty']->assign('card_password', $cards['card_password']);
		    $GLOBALS['smarty']->assign('past_time', local_date('Y-m-d', $end_date));
		    $msg = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
		    if ($sms->send($mobile, $msg, 0))
		    {
			$card_id = $cards['card_id'];
			$sql = "UPDATE " . $GLOBALS['ecs']->table('group_card') . " SET send_num=1 WHERE card_id='$card_id'";
			$GLOBALS['db']->query($sql);
		    }
		}
	    }
	}
    }
    return true;
}

function set_group_stats($group_buy_id)
{
    $sql = "SELECT group_id,is_finished ,start_time,end_time,upper_orders,lower_orders" .
	    ",group_restricted,group_stock,succeed_time,goods_type,group_need FROM " .
	    $GLOBALS['ecs']->table('group_activity') .
	    " WHERE group_id = '$group_buy_id' AND is_finished = 0";
    $group_buy = $GLOBALS['db']->getRow($sql);
    $orders_num = get_group_orders($group_buy_id, $group_buy['group_need']);
    if (!empty($group_buy))
    {
	$now = gmtime();
	if ($now < $group_buy['start_time'])
	{
	    $status = GBS_PRE_START;
	}
	elseif ($now > $group_buy['end_time'])
	{
	    if ($group_buy['succeed_time'] > 0)
	    {
		$status = GBS_FINISHED;
		$sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') .
			" SET is_finished='$status',closed_time='$now' WHERE group_id='$group_buy_id'";
		$GLOBALS['db']->query($sql);
	    }
	    else
	    {
		$status = GBS_FAIL;
		$sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') .
			" SET is_finished='$status',closed_time='$now' WHERE group_id='$group_buy_id'";
		$GLOBALS['db']->query($sql);
	    }
	}
	else
	{
	    $status = GBS_UNDER_WAY;
	    if (empty($group_buy['succeed_time']) || $group_buy['succeed_time'] == '0')
	    {
		if ($orders_num >= $group_buy['lower_orders'])
		{
		    $add_time = get_success_time($group_buy_id);
		    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') . " SET succeed_time='$add_time' WHERE group_id='$group_buy_id'";
		    $GLOBALS['db']->query($sql);
		    $group_buy['succeed_time'] = $add_time;
		    if ($group_buy['goods_type'] == 1 && $orders_num > 0 && !$GLOBALS['_CFG']['make_group_card'])
		    {
			$is_send = !$GLOBALS['_CFG']['send_group_sms'];
			send_oldgroup_cards($group_buy_id, $is_send);
		    }
		}
	    }
	    if ($group_buy['upper_orders'] > 0 && $orders_num >= $group_buy['upper_orders'])
	    {
		if ($now < $group_buy['end_time'])
		{
		    $status = 5;
		    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') .
			    " SET is_finished='$status',closed_time='$now' WHERE group_id='$group_buy_id'";
		    $GLOBALS['db']->query($sql);
		}
		else
		{
		    $status = GBS_FINISHED;
		    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') .
			    " SET is_finished='$status',closed_time='$now' WHERE group_id='$group_buy_id'";
		    $GLOBALS['db']->query($sql);
		}
	    }
	    if ($group_buy['group_need'] != 1 && $group_buy['group_stock'] > 0)
	    {
		$sql = "SELECT  SUM(g.goods_number) AS total_goods " .
			"FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
			$GLOBALS['ecs']->table('order_goods') . " AS g " .
			" WHERE o.order_id = g.order_id " .
			"AND o.extension_code = 'group_buy' " .
			"AND o.extension_id = '$group_buy_id' " .
			"AND g.goods_id = '$group_buy_id' " .
			"AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
			" AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING));
		$group_stock = $GLOBALS['db']->getOne($sql);
		if ($group_stock >= $group_buy['group_stock'])
		{
		    if ($now < $group_buy['end_time'])
		    {
			$status = 5;
			$sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') .
				" SET is_finished='$status',closed_time='$now' WHERE group_id='$group_buy_id'";
			$GLOBALS['db']->query($sql);
		    }
		    else
		    {
			$status = GBS_FINISHED;
			$sql = 'UPDATE ' . $GLOBALS['ecs']->table('group_activity') .
				" SET is_finished='$status',closed_time='$now' WHERE group_id='$group_buy_id'";
			$GLOBALS['db']->query($sql);
		    }
		}
	    }
	}
    }
    return $status;
}

function send_oldgroup_cards($group_buy_id, $is_send = true)
{
    if ($is_send)
    {
	$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('group_card') .
		" WHERE group_id = '$group_buy_id' AND is_saled = 0";
	$res = $GLOBALS['db']->getAll($sql);
	include_once(ROOT_PATH . 'includes/cls_sms.php');
	$sms = new sms();
	if ($res)
	{
	    $sql = "UPDATE " . $GLOBALS['ecs']->table('group_card') . " SET is_saled='1' WHERE group_id='$group_buy_id'";
	    $GLOBALS['db']->query($sql);
	    $sql = "SELECT goods_name FROM " . $GLOBALS['ecs']->table('group_activity') . " WHERE group_id ='$group_buy_id'";
	    $group_name = $GLOBALS['db']->getOne($sql);
	    $tpl = get_sms_template('send_sms');
	    foreach ($res AS $row)
	    {
		$GLOBALS['smarty']->assign('group_name', $group_name);
		$GLOBALS['smarty']->assign('card_sn', $row['card_sn']);
		$GLOBALS['smarty']->assign('card_password', $row['card_password']);
		$GLOBALS['smarty']->assign('past_time', local_date('Y-m-d', $row['end_date']));
		$msg = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
		$sql = 'SELECT mobile FROM ' . $GLOBALS['ecs']->table('order_info') . " WHERE order_sn = '$row[order_sn]'";
		$mobile = $GLOBALS['db']->getOne($sql);
		if ($sms->send($mobile, $msg, 0))
		{
		    $sql = "UPDATE " . $GLOBALS['ecs']->table('group_card') . " SET send_num=1 WHERE card_id='$row[card_id]'";
		    $GLOBALS['db']->query($sql);
		}
	    }
	}
    }
    else
    {
	$sql = "UPDATE " . $GLOBALS['ecs']->table('group_card') . " SET is_saled='1' WHERE group_id='$group_buy_id'";
	$GLOBALS['db']->query($sql);
    }
    return true;
}

/*
 * 随机密钥生成
 */

function get_rndcode()
{
    $str = 'ABCDEFGHIJKLMNOPGRSTUVWXYZ';
    $rndstr = '';
    for ($i = 0; $i < 6; $i++)
    {
	$rndcode = rand(0, 25);
	$rndstr.=$str[$rndcode];
    }
    return $rndstr;
}

/*
 * 短信认证码生成/tunpishuang
 */

function gen_authcode()
{
    $str = '0123456789';
    $rndstr = '';
    for ($i = 0; $i < 4; $i++)
    {
	$rndcode = rand(0, 9);
	$rndstr.=$str[$rndcode];
    }
    return $rndstr;
}

/*
 * 推广日志？
 */

function set_affiliate_log($order_id, $uid, $invitee_id, $money, $point = 0, $separate_by = 1)
{
    $time = gmtime();
    $username = $GLOBALS['db']->getOne("SELECT user_name FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id='$uid'");
    $invitee_name = $GLOBALS['db']->getOne("SELECT user_name FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_id='$invitee_id'");
    $sql = "select * from " . $GLOBALS['ecs']->table('affiliate_log') . " where user_id=$uid and invitee_id=$invitee_id";
    if ($GLOBALS['db']->getAll($sql))
    {
	$sql = "UPDATE " . $GLOBALS['ecs']->table('affiliate_log') . " SET separate_type = 1 where user_id=$uid and invitee_id=$invitee_id";
    }
    else
    {
	$sql = "INSERT INTO " . $GLOBALS['ecs']->table('affiliate_log') .
		"( order_id, user_id, user_name,invitee_id, invitee_name, time, money, point, separate_type)" .
		" VALUES ( '$order_id', '$uid', '$username','$invitee_id', '$invitee_name', '$time', '$money', '$point', $separate_by)";
    }
    $GLOBALS['db']->query($sql);
}

/*
 * 短信模板
 */

function get_sms_template($tpl_name)
{
    $sql = 'SELECT template_subject, is_html, template_content FROM ' .
	    $GLOBALS['ecs']->table('mail_templates') . " WHERE template_code = '$tpl_name'";

    return $GLOBALS['db']->getRow($sql);
}

/*
 * smarty公共赋值函数
 */

function assign_public($city_id)
{
    $city_info = get_city_info($city_id);
    $shop_notice = $GLOBALS['_CFG']['group_notice'] != '' ? $GLOBALS['_CFG']['group_notice'] : $city_info['city_notice'];
    $shop_qq = $city_info['city_qq'] != '' ? $city_info['city_qq'] : $GLOBALS['_CFG']['group_qq'];

    $links = get_weblinks();
    $GLOBALS['smarty']->assign('img_links', $links['img']);
    $GLOBALS['smarty']->assign('txt_links', $links['txt']);
    $GLOBALS['smarty']->assign('data_dir', DATA_DIR);       // 数据目录
    $GLOBALS['smarty']->assign('cityid', $city_id);
    $GLOBALS['smarty']->assign('group_city', get_group_city());
    $GLOBALS['smarty']->assign('city_info', $city_info);
    $GLOBALS['smarty']->assign('weburl', $GLOBALS['ecs']->get_domain() . '/');
    $GLOBALS['smarty']->assign('group_help', get_group_help());
    $GLOBALS['smarty']->assign('navigation', set_navigation());

    $GLOBALS['smarty']->assign('group_cardname', $GLOBALS['_CFG']['group_cardname']);
    $GLOBALS['smarty']->assign('group_notice', $shop_notice);
    $GLOBALS['smarty']->assign('group_qq', $shop_qq);
    $GLOBALS['smarty']->assign('index_url', rewrite_groupurl('index.php'));
    $GLOBALS['smarty']->assign('group_logo', $GLOBALS['_CFG']['group_logo']);
    $GLOBALS['smarty']->assign('group_phone', $GLOBALS['_CFG']['group_phone']);
    $GLOBALS['smarty']->assign('group_email', $GLOBALS['_CFG']['group_email']);
    $GLOBALS['smarty']->assign('group_shopname', $GLOBALS['_CFG']['group_shopname']);
    $GLOBALS['smarty']->assign('group_statscode', $GLOBALS['_CFG']['group_statscode']);
    $GLOBALS['smarty']->assign('group_shoptitle', $GLOBALS['_CFG']['group_shoptitle']);
    $GLOBALS['smarty']->assign('group_shopdesc', $GLOBALS['_CFG']['group_shopdesc']);
    $GLOBALS['smarty']->assign('group_shopaddress', $GLOBALS['_CFG']['group_shopaddress']);
    //天气预报
    //$GLOBALS['smarty']->assign('weather', get_weather($city_info['city_id'], $city_info['city_name']));
    //系统返利
    $GLOBALS['smarty']->assign('rebate', $GLOBALS['_CFG']['rebate']);
    //意见反馈,不玩儿了搜
    //$GLOBALS['smarty']->assign('max_ask_url', rewrite_groupurl('ask.php', array('gid' => get_group_maxid($city_id))));
    //验证码
    $GLOBALS['smarty']->assign('random', mt_rand());
}

/*
 * 团购帮助
 */

function get_group_help()
{
    //页面底部文章导航全部为置顶a.article_type=1 && c.show_in_nav=1 /tun/2010.11.6
    $sql = 'SELECT c.cat_id, c.cat_name, c.sort_order, a.article_id, a.title, a.file_url,a.link, a.open_type ' .
	    'FROM ' . $GLOBALS['ecs']->table('article') . ' AS a,' .
	    $GLOBALS['ecs']->table('article_cat') . ' AS c ' .
	    'WHERE a.cat_id = c.cat_id AND a.is_open = 1 AND c.parent_id=13 AND a.article_type=1 AND c.show_in_nav=1 ' .
	    'ORDER BY c.sort_order ASC, a.article_id';
    $res = $GLOBALS['db']->query($sql);
    $arr = array();
    $idx = 0;
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
	$arr[$row['cat_id']]['cat_name'] = $row['cat_name'];
	$arr[$row['cat_id']]['article'][$idx]['article_id'] = $row['article_id'];
	$arr[$row['cat_id']]['article'][$idx]['title'] = $row['title'];
	if ($row['link'] != 'http://' && $row['link'] != '')
	{
	    $arr[$row['cat_id']]['article'][$idx]['url'] = $row['link'];
	}
	else
	{
	    $arr[$row['cat_id']]['article'][$idx]['url'] = rewrite_groupurl('help.php', array('id' => $row['article_id']));
	}
	$idx++;
    }
    return $arr;
}

/*
 * 友情链接
 */

function get_weblinks()
{
    $sql = 'SELECT link_logo, link_name, link_url FROM ' . $GLOBALS['ecs']->table('friend_link') . ' ORDER BY show_order';
    $res = $GLOBALS['db']->getAll($sql);
    $links['img'] = $links['txt'] = array();
    foreach ($res AS $row)
    {
	if (!empty($row['link_logo']))
	{
	    $links['img'][] = array('name' => $row['link_name'],
		'url' => $row['link_url'],
		'logo' => $row['link_logo']);
	}
	else
	{
	    $links['txt'][] = array('name' => $row['link_name'],
		'url' => $row['link_url']);
	}
    }
    return $links;
}

/*
 * 团购价格格式化
 */

function group_price_format($price)
{
    return sprintf($GLOBALS['_CFG']['group_format'], $price);
}

/*
 * rewrite url
 */

function rewrite_groupurl($url, $param = array(), $is_use = false)
{
    $param_url = '';
    if ($GLOBALS['_CFG']['group_rewrite'] == 1)
    {
	$url = substr_replace($url, '', -4);
	$url_suffix = '.html';
	if (!empty($param))
	{
	    $url .= '-';
	    if ($is_use)
	    {
		foreach ($param AS $key => $value)
		{
		    $param_url .= $key . '-' . $value . '-';
		}
		$param_url = trim($param_url, '-');
	    }
	    else
	    {
		$param_url = join('-', $param);
	    }
	}
    }
    else
    {
	$url_suffix = '';
	if (!empty($param))
	{
	    $param_url = '?';
	    foreach ($param AS $key => $value)
	    {
		$param_url .= $key . '=' . $value . '&';
	    }
	    $param_url = trim($param_url, '&');
	}
    }
    $url .= $param_url . $url_suffix;
    return $url;
}

/*
 * 分页函数
 */

function get_group_pager($url, $param, $record_count, $page = 1, $size = 10)
{

    $size = intval($size);
    if ($size < 1)
    {
	$size = 10;
    }
    $page = intval($page);
    if ($page < 1)
    {
	$page = 1;
    }
    $record_count = intval($record_count);
    $page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
    if ($page > $page_count)
    {
	$page = $page_count;
    }

    /* 分页样式 */

    $pager['styleid'] = isset($GLOBALS['_CFG']['page_style']) ? intval($GLOBALS['_CFG']['page_style']) : 0;
    $page_prev = ($page > 1) ? $page - 1 : 1;
    $page_next = ($page < $page_count) ? $page + 1 : $page_count;
    /* 将参数合成url字串 */
    if ($GLOBALS['_CFG']['group_rewrite'] == 1)
    {
	$url = substr_replace($url, '', -4);
	$param_url = '';
	if (!empty($param))
	{
	    $param_url = join('_', $param);
	}
    }
    else
    {
	$param_url = '?';
	if (!empty($param))
	{
	    foreach ($param AS $key => $value)
	    {
		$param_url .= $key . '=' . $value . '&';
	    }
	}
    }
    $pager['url'] = $url;
    $pager['start'] = ($page - 1) * $size;
    $pager['page'] = $page;
    $pager['size'] = $size;
    $pager['record_count'] = $record_count;
    $pager['page_count'] = $page_count;
    if ($pager['styleid'] == 0)
    {
	if ($GLOBALS['_CFG']['group_rewrite'] == 1)
	{
	    $pager['page_first'] = $url . '-' . $param_url . '-1.html';
	    $pager['page_prev'] = $url . '-' . $param_url . '-' . $page_prev . '.html';
	    $pager['page_next'] = $url . '-' . $param_url . '-' . $page_next . '.html';
	    $pager['page_last'] = $url . '-' . $param_url . '-' . $page_count . '.html';
	}
	else
	{
	    $pager['page_first'] = $url . $param_url . 'page=1';
	    $pager['page_prev'] = $url . $param_url . 'page=' . $page_prev;
	    $pager['page_next'] = $url . $param_url . 'page=' . $page_next;
	    $pager['page_last'] = $url . $param_url . 'page=' . $page_count;
	}
	$pager['array'] = array();
	for ($i = 1; $i <= $page_count; $i++)
	{
	    $pager['array'][$i] = $i;
	}
    }
    else
    {
	$_pagenum = 10;     // 显示的页码
	$_offset = 2;       // 当前页偏移值
	$_from = $_to = 0;  // 开始页, 结束页
	if ($_pagenum > $page_count)
	{
	    $_from = 1;
	    $_to = $page_count;
	}
	else
	{
	    $_from = $page - $_offset;
	    $_to = $_from + $_pagenum - 1;
	    if ($_from < 1)
	    {
		$_to = $page + 1 - $_from;
		$_from = 1;
		if ($_to - $_from < $_pagenum)
		{
		    $_to = $_pagenum;
		}
	    }
	    elseif ($_to > $page_count)
	    {
		$_from = $page_count - $_pagenum + 1;
		$_to = $page_count;
	    }
	}
	if ($GLOBALS['_CFG']['group_rewrite'] == 1)
	{

	    $url_format = $param_url != '' ? $url . '-' . $param_url . '-' : $url . '-';
	    $url_suffix = '.html';
	    $pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? $url_format . 1 . $url_suffix : '';
	    $pager['page_prev'] = ($page > 1) ? $url_format . $page_prev . $url_suffix : '';
	    $pager['page_next'] = ($page < $page_count) ? $url_format . $page_next . $url_suffix : '';
	    $pager['page_last'] = ($_to < $page_count) ? $url_format . $page_count . $url_suffix : '';
	}
	else
	{
	    $url_format = $url . $param_url . 'page=';
	    $url_suffix = '';
	    $pager['page_first'] = ($page - $_offset > 1 && $_pagenum < $page_count) ? $url_format . 1 : '';
	    $pager['page_prev'] = ($page > 1) ? $url_format . $page_prev : '';
	    $pager['page_next'] = ($page < $page_count) ? $url_format . $page_next : '';
	    $pager['page_last'] = ($_to < $page_count) ? $url_format . $page_count : '';
	}
	$pager['page_kbd'] = ($_pagenum < $page_count) ? true : false;
	for ($i = $_from; $i <= $_to; ++$i)
	{
	    $pager['page_number'][$i] = $url_format . $i . $url_suffix;
	}
    }
    return $pager;
}

/*
 * 设定团购导航
 */

function set_navigation()
{
    $url_arr = array(
	'index' => array(
	    'name' => '今日团购',
	    'url' => rewrite_groupurl('index.php')
	),
	'stage' => array(
	    'name' => '往期团购',
	    'url' => rewrite_groupurl('stage.php')
	),
	'help' => array(
	    'name' => '如何团购',
	    'url' => rewrite_groupurl('help.php?id=55')
	),
	'subscribe' => array(
	    'name' => '常见问题',
	    'url' => rewrite_groupurl('help.php?id=48')
	)
    );
    return $url_arr;
}

/*
 * 团购商品属性
 */

function get_group_properties($group_id, $group_attr_id = array())
{
    /* 对属性进行重新排序和分组 */
    $sql = "SELECT attr_group " .
	    "FROM " . $GLOBALS['ecs']->table('goods_type') . " AS gt, " . $GLOBALS['ecs']->table('group_activity') . " AS ga " .
	    "WHERE ga.group_id='$group_id' AND gt.cat_id=ga.group_attr";
    $grp = $GLOBALS['db']->getOne($sql);
    if (!empty($grp))
    {
	$groups = explode("\n", strtr($grp, "\r", ''));
    }

    /* 获得商品的规格 */
    $sql = "SELECT a.attr_id, a.attr_name, a.attr_group, a.is_linked, a.attr_type, " .
	    "g.group_attr_id, g.attr_value, g.attr_price " .
	    'FROM ' . $GLOBALS['ecs']->table('group_attr') . ' AS g ' .
	    'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
	    "WHERE g.group_id = '$group_id' " .
	    'ORDER BY a.sort_order, g.attr_price, g.group_attr_id';
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();     // 规格

    foreach ($res AS $row)
    {
	$is_selected = '0';
	if (!empty($group_attr_id) && in_array($row['group_attr_id'], $group_attr_id))
	{
	    $is_selected = 1;
	}
	$row['attr_value'] = str_replace("\n", '<br />', $row['attr_value']);

	$arr[$row['attr_id']]['attr_type'] = $row['attr_type'];
	$arr[$row['attr_id']]['name'] = $row['attr_name'];
	$arr[$row['attr_id']]['values'][] = array(
	    'label' => $row['attr_value'],
	    'price' => $row['attr_price'],
	    'format_price' => group_price_format(abs($row['attr_price'])),
	    'id' => $row['group_attr_id'],
	    'selected' => $is_selected
	);
    }
    return $arr;
}

function get_group_attr_info($arr)
{
    $attr = '';

    if (!empty($arr))
    {
	$fmt = "%s:%s[%s] \n";

	$sql = "SELECT a.attr_name, ga.attr_value, ga.attr_price " .
		"FROM " . $GLOBALS['ecs']->table('group_attr') . " AS ga, " .
		$GLOBALS['ecs']->table('attribute') . " AS a " .
		"WHERE " . db_create_in($arr, 'ga.group_attr_id') . " AND a.attr_id = ga.attr_id";
	$res = $GLOBALS['db']->query($sql);

	while ($row = $GLOBALS['db']->fetchRow($res))
	{
	    $attr_price = round(floatval($row['attr_price']), 2);
	    $attr .= sprintf($fmt, $row['attr_name'], $row['attr_value'], $attr_price);
	}

	$attr = str_replace('[0]', '', $attr);
    }

    return $attr;
}

/*
 * 系统信息
 */

function show_group_message($title, $content, $desc, $redirect_time=0, $redirect_page=0)
{
    $redirect = array(
	'time' => $redirect_time,
	'page' => $redirect_page
    );
    $GLOBALS['smarty']->assign('redirect', $redirect);
    $message['title'] = $title;
    $message['content'] = $content;
    $message['desc'] = $desc;
    $GLOBALS['smarty']->assign('message', $message);
    $GLOBALS['smarty']->display('message.dwt');
    exit;
}

function show_group_message2($title, $content, $msg=0, $redirect_time=0, $redirect_page=0)
{
    if (is_array($msg))
    {
	$GLOBALS['smarty']->assign('msg', $msg);
    }
    $redirect = array(
	'time' => $redirect_time,
	'page' => $redirect_page
    );
    $GLOBALS['smarty']->assign('redirect', $redirect);
    $message['title'] = $title;
    $message['content'] = $content;
    $GLOBALS['smarty']->assign('message', $message);
    $GLOBALS['smarty']->display('message2.dwt');
    exit;
}

/*
 * 获取扩展城市信息
 */

function get_expand_city($city_id)
{
    $expand_city_array = '';
    $sql = 'SELECT group_id FROM ' . $GLOBALS['ecs']->table('expand_city') . " WHERE city_id='$city_id'";
    $expand_city_array = $GLOBALS['db']->getCol($sql);
    return db_create_in($expand_city_array, 'group_id');
}

/*
 * 随机认证码生成
 */

function generate_word($length = 4)
{
    $chars = '0123456789';

    for ($i = 0, $count = strlen($chars); $i < $count; $i++)
    {
	$arr[$i] = $chars[$i];
    }

    mt_srand((double) microtime() * 1000000);
    shuffle($arr);

    return substr(implode('', $arr), 0, $length);
}

function set_group_rebate($group_id, $parent_id, $user_id, $order_id, $order_sn)
{
    if (!$GLOBALS['_CFG']['group_rebate'] && $parent_id > 0 && $user_id > 0 && $group_id > 0 && $order_id > 0 && $order_sn > 0)
    {
	$sql = 'SELECT goods_rebate,goods_type FROM ' . $GLOBALS['ecs']->table('group_activity') .
		" WHERE group_id='$group_id'";
	$group_buy = $GLOBALS['db']->getRow($sql);
	$sql = "SELECT COUNT(*)  FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " .
		" WHERE  o.extension_code = 'group_buy' " .
		" AND o.parent_id='$parent_id' AND o.user_id='$user_id'" .
		" AND o.order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
		" AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING));
	if ($GLOBALS['db']->getOne($sql) == 1 && $group_buy['goods_rebate'] > 0)
	{
	    $info = sprintf('邀请返利', $order_sn, $group_buy['goods_rebate'], 0);
	    log_account_change($parent_id, $group_buy['goods_rebate'], 0, 0, 0, $info);
	    set_affiliate_log($order_id, $parent_id, $_SESSION['user_id'], $group_buy['goods_rebate']);
	    $sql = "UPDATE " . $GLOBALS['ecs']->table('order_info') .
		    " SET is_separate = 1" . " WHERE order_id = '$order_id'";
	    $GLOBALS['db']->query($sql);
	}
    }
}

?>