<?php
include('uploadify.php');

if (!defined('DIRECTORY_SEPARATOR')) define('DIRECTORY_SEPARATOR','/');
$root = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$_POST['dir'];

$up = new uploadify;
$up->check($root);

switch($_POST['mk']){
	case 'file':
		$up->up_file($root);	
	case 'img':
		$up->up_img($root);	
	case 'imgs':
		$up->up_img($root);	
}

?>