<?php 
define('IN_ECS', true);

require(dirname(__FILE__) . '/../includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');

$group_buy = get_group_buy_info($group_buy_id,$city_id,$cat_id);

//header('Content-Type: text/xml;');

echo 
'<?xml version="1.0" encoding="utf-8"?>

<urlset>

<!--循环开始    循环出所有正在进行中的团购-->
	<url>
		<loc>http://t.yoti.cn/index.php?id=' . $group_buy['group_id'] '</loc><!--团购地址-->
		<data>
			<display>
				<website>{$group_shopname}</website><!--网站名称-->

				<siteurl>http://t.yoti.cn</siteurl><!--网站地址-->

				<city>北京</city><!--团购城市-->

				<title>' . $group_buy['group_name'] . '</title><!--团购标题-->

				<image>'. $group_buy['group_image'] .'</image><!--团购图片地址-->

				<starttime>'. $group_buy['start_time'] .' </starttime><!--开始时间-->

				<endtime>'. $group_buy['end_time'] .'</endtime><!--结束时间-->

				<value>'. $group_buy['market_price'] .'</value><!--市场价-->

				<price>'.$group_buy['group_price'].'</price><!--团购价格-->

				<rebate>'. $group_buy['group_rebate'].'</rebate><!--折扣-->

				<bought>'. $group_buy['orders_num'] ."
                </bought><!--当前购买人数-->
			</display>
		</data>
	</url>
<!--循环结束-->   

</urlset>";
?>