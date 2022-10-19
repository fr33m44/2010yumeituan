<?php

/**
 * 调查
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_group.php');
$smarty->tmp_dir = 'template/' . $_CFG['formwork'] . '/';
$smarty->template_dir = ROOT_PATH . 'template/' . $_CFG['formwork'];

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$user_id = $_SESSION['user_id'];
$city_id = isset($_COOKIE['ECS']['cityid']) && intval($_COOKIE['ECS']['cityid']) > 0 ? intval($_COOKIE['ECS']['cityid']) : $_CFG['group_city'];
assign_public($city_id);
if ($_REQUEST['act'] == 'save_vote')
{
    //保存调查结果
    save_vote();
    show_group_message2('用户调查', '提交成功，感谢您的参与。 <a href="/">»返回首页</a>');
    exit;
}
else
{
    $smarty->assign('votelist', get_votelist());
    $smarty->display("vote.dwt");
}

/* ------------------------------------------------------ */
//-- PRIVATE FUNCTION
/* ------------------------------------------------------ */

/**
 * 检查是否已经提交过投票
 *
 * @access  private
 * @param   integer     $vote_id
 * @param   string      $ip_address
 * @return  boolean
 */
function vote_already_submited($vote_id, $ip_address)
{
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('vote_log') . " " .
	    "WHERE ip_address = '$ip_address' AND vote_id = '$vote_id' ";

    return ($GLOBALS['db']->GetOne($sql) > 0);
}

/**
 * 保存投票结果信息/tun
 *
 * @access  public
 * @param   integer     $vote_id
 * @param   string      $ip_address
 * @param   string      $option_id
 * @return  void
 */
function save_vote()
{
    //print_r($_POST);
    //用户id
    $sql = "select max(user_id) from  " . $GLOBALS['ecs']->table('vote_result');
    $maxid = $GLOBALS['db']->getOne($sql);
    if ($maxid == null)
	$maxid = 0;
    else
	$maxid+=1;
    //post数据
    foreach ($_POST as $k => $post)
    {
	if (substr($k, 0, 1) == 'v')
	{   //问题
	    //print_r($post);
	    $voteinfo = get_vote_info(str_replace('v', '', $k));
	    //选项
	    $answer_name=array();
	    foreach ($post as $k => $answerid)
	    {
		if (substr($answerid, 0, 2) != 'ct')
		{
		    $answer = get_option_info($answerid);
		    if ($answer['option_type'] == 3)//文本（其他）
		    {
			$other=explode(' ',$_POST['ct'.$answerid]);
			//print_r($answerid);
			$answer_name=array_merge($answer_name, $other);
		    }
		    else
		    {
			$answer_name[] = $answer['option_name'];
		    }
		}
	    }
	    $answer_name_s = serialize($answer_name);
	    $sql = "insert into " . $GLOBALS['ecs']->table('vote_result') . "(user_id,question,answer) values('$maxid','$voteinfo[vote_name]','$answer_name_s' )";
	    $GLOBALS['db']->query($sql);
	    unset($voteinfo);
	}
    }
}

function get_vote_info($vid)
{
    $sql = "select * from  " . $GLOBALS['ecs']->table('vote') . " where vote_id =" . $vid;
    return $GLOBALS['db']->getRow($sql);
}

function get_option_info($option_id)
{
    $sql = "select * from  " . $GLOBALS['ecs']->table('vote_option') . " where option_id =" . $option_id;
    return $GLOBALS['db']->getRow($sql);
}

/* 获取在线调查数据列表 */

function get_votelist()
{
    /* 查询数据 */
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('vote') . ' ORDER BY vote_id';
    $votelist = $GLOBALS['db']->getAll($sql);
    foreach ($votelist as $k => $vote)
    {
	$newvotelist[$k] = $votelist[$k];
	$newvotelist[$k]['option'] = get_optionlist($vote['vote_id']);
    }
    return $newvotelist;
}

/* 获取调查选项列表 */

function get_optionlist($id)
{
    $list = array();
    $sql = 'SELECT option_id, vote_id, option_name, option_type, option_count, option_order' .
	    ' FROM ' . $GLOBALS['ecs']->table('vote_option') .
	    " WHERE vote_id = '$id' ORDER BY option_order ASC, option_id asc";
    $res = $GLOBALS['db']->query($sql);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
	$list[] = $rows;
    }

    return $list;
}

?>