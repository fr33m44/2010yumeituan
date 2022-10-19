<?php

/**
 * ECSHOP RSS Feed 生成程序
 */
define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_rss.php');

header('Content-Type: application/xml; charset=' . EC_CHARSET);
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
header('Last-Modified: ' . date('r'));
header('Pragma: no-cache');

$ver = isset($_REQUEST['ver']) ? $_REQUEST['ver'] : '2.00';
$cat = isset($_REQUEST['cat']) ? ' AND ' . get_children(intval($_REQUEST['cat'])) : '';
$brd = isset($_REQUEST['brand']) ? ' AND g.brand_id=' . intval($_REQUEST['brand']) . ' ' : '';

$uri = $ecs->url();

$rss = new RSSBuilder(EC_CHARSET, $uri, htmlspecialchars($_CFG['group_shopname']), htmlspecialchars($_CFG['group_shopdesc']), $uri . 'animated_favicon.gif');
$rss->addDCdata('', 'http://www.ecshop.com', date('r'));
$now = gmtime();
$sql = 'SELECT * ' .
	"FROM " . $GLOBALS['ecs']->table('group_activity') ."ORDER BY group_id desc limit 1";
$res = $db->query($sql);
if ($res !== false)
{
    while ($row = $db->fetchRow($res))
    {
	$item_url = rewrite_groupurl('index.php', array('id' =>$row['group_id']));
	$separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
	$about = $uri . $item_url;
	$title = htmlspecialchars($row['group_name']);
	$link = $uri . $item_url . $separator . 'from=rss';
	$desc = htmlspecialchars($row['group_desc']);
	$subject = 'fdsafs';
	$date = local_date('r', $row['start_time']);
	$rss->addItem($about, $title, $link, $desc, $subject, $date);
    }

    $rss->outputRSS($ver);
}
?>