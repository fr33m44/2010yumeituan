<?php

/*
  $i=0;
  while($i<65536)
  {
  $h=dechex($i);
  $i++;
  echo chr($h);
  }
 */

//echo chr(0xA3BA);
//echo chr(0xA3AC);
//
//
//function md10($str=''){
//	return substr(md5($str),10,10);
//}
//$pwd='jiongjiongMM520';
//
//echo md5(md5($pwd).md10($pwd));

/*
  $arr=  simplexml_load_string('<?xml version="1.0" encoding="utf-8"?>
  <string xmlns="http://tempuri.org/">1.0.2441.23041</string>');
  echo $arr->{0};
 */
/*
  $time=date('Y.m.d H:m:s','1289857690');
  print_r($time);
 *
 */

/*
  echo strtotime('2010-11-11 11:11:12');
  echo '<br />';
  echo date('Y-m-d H:i:s',strtotime('2010-11-11 11:1:6'))
 *
 */

//echo strlen('abc');
/*
  echo preg_match('/^[\d-\s]{11}$/', 'ddd');
 */
/*
  $region_name='颖上县';
  if(strpos($region_name, '县') >0)
  {
  $region_name=str_replace('县','',$region_name);
  }
  echo strlen('中文');
 *
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
function plus($num){
    $len=strlen($num);
    $num++;
    while(strlen($num)<$len)
    {
	$num="0".$num;
    }
    return $num;
}
echo plus('0011');
?>