<?php
session_start();
header("Content-type: text/html; charset=utf-8");

//登陆判断
//地址解析
$U = explode('.php',$_SERVER['PHP_SELF']);
$Ui= explode('/',$U[0]);
$U = $U[1];//路由
$C = end($Ui);//入口名
array_pop($Ui);
$D = implode('/',$Ui);//子目录

if (!isset($_SESSION['mv3c_admin']) 
&& $_SERVER['PHP_SELF']!="{$D}/back.php/login"
&& $_SERVER['PHP_SELF']!="{$D}/back.php/login/login_op"
){
	echo '<script language="javascript">';
	//echo 'alert("请先登陆");';
	echo "document.location.href=\"{$D}/back.php/login\";";
	echo '</script>';
	exit;
};
?>