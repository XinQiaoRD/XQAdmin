<?php
//基本
header("Content-type: text/html; charset=utf-8");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
date_default_timezone_set("Asia/Shanghai");

//版本号
define('Version', '1.0.2');
define('VersionDate', '2017-06-07');
//1. preg_match 替代ereg (ccv.php)

//数据库
define("DbHost", "localhost");
define("DbNm",   "xinqiao");
define("DbRoot", "root");
define("DbPsw",  "1981588k");//
define("DbPost", "3306");
define("DbPre",  "xq_");


//地址解析
$U = explode('.php',$_SERVER['PHP_SELF']);
$Ui= explode('/',$U[0]);
$U = $U[1];//路由
$C = end($Ui);//入口名
array_pop($Ui);
$D = implode('/',$Ui);//子目录

define('D', $D);//子目录
define('C', $C);//入口名

//缓存//调试
if($Debug) {
	define("Debug", 1);
	define("Cache", 0);
}else{
	define("Debug", 0);
	define("Cache", 1);
}
if($Cache) define("CacheNm", '_'.$Cache);
else define("CacheNm", '');
?>