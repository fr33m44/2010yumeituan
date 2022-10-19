<?php

$file_name = "dump.sql"; //要导入的SQL文件名
$dbhost = "localhost"; //数据库主机名
$dbuser = "yumeituan"; //数据库用户名
$dbpass = "uyr4974uir"; //数据库密码
$dbname = "yumeituan"; //数据库名
set_time_limit(0); //设置超时时间为0，表示一直执行。当php在safe mode模式下无效，此时可能会导致导入超时，此时需要分段导入
$fp = @fopen($file_name, "r") or die("不能打开SQL文件 $file_name"); //打开文件
mysql_connect($dbhost, $dbuser, $dbpass) or die("不能连接数据库 $dbhost"); //连接数据库


$sql = "DROP DATABASE $dbname";
mysql_query($sql);
$sql = "CREATE DATABASE $dbname";
mysql_query($sql);

mysql_select_db($dbname) or die("不能打开数据库 $dbname"); //打开数据库

echo "正在执行导入操作
";
while ($SQL = GetNextSQL())
{
    if (!mysql_query($SQL))
    {
	echo "执行出错：" . mysql_error() . "
";
	echo "SQL语句为：
" . $SQL . "
";
    };
}
echo "导入完成";

fclose($fp) or die("Can't close file $file_name"); //关闭文件
mysql_close();

//从文件中逐条取SQL
function GetNextSQL()
{
    global $fp;
    $sql = "";
    while ($line = @fgets($fp, 40960))
    {
	$line = trim($line);
//以下三句在高版本php中不需要
	$line = str_replace("\\\\", "\\", $line);
	$line = str_replace("\'", "'", $line);
	$line = str_replace("\\r\\n", chr(13) . chr(10), $line);
// $line = stripcslashes($line);
	if (strlen($line) > 1)
	{
	    if ($line[0] == "-" && $line[1] == "-")
	    {
		continue;
	    }
	}
	$sql.=$line . chr(13) . chr(10);
	if (strlen($line) > 0)
	{
	    if ($line[strlen($line) - 1] == ";")
	    {
		break;
	    }
	}
    }
    return $sql;
}

?>