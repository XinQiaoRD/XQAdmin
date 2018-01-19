<?php
//路由处理
if($U){ 
	$Ui = explode('/',$U);
	$M = $Ui[1];
	$A = $Ui[2];
}else{
	$M = $_GET['m'];
	$A = $_GET['a'];
}
if(!$M) $M = 'index';
if(!$A) $A = 'index';

//嵌入样式处理函数
$Pt = substr($A,strlen($A)-3,3);
if( $Pt!='_op' && $Pt!='_cc' && $Pt!='_mm' ) {
	include(Root.'/mv3c/cc.php');
	include(Root.'/mv3c/ccv.php');
}else{
	if( $Pt=='_op' ){
		header("Cache-control:no-cache,no-store,must-revalidate");
		header("Pragma:no-cache");
		header("Expires:0");
	}
	if( $Pt=='_op' || $Pt=='_cc' ) include(Root.'/mv3c/cc.php');
}


//文件嵌入
$model_path = Root."/{$C}/model/{$M}.php";
if(!is_file($model_path))  die("{$model_path} 文件没有建立！");
include($model_path);

//常量设置
define('M', $M);//model名
define('A', $A);//action名

//执行动作
$Class_nm = 'mv3c_'.$M;
$CC = new $Class_nm;
$CC->$A();
?>