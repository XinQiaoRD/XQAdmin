<?php
class uploadify
{

//token
//参数：验证
function check($root)
{
	$token = $_POST['token'];
	$verifyToken = md5('mv3c_cc_' . $_POST['timestamp']);
	
	if($token != $verifyToken) die('{err:"1", err_info:"上传不合法！"}');
	if(empty($_FILES)) die('{err:"1", err_info:"上传文件丢失！"}');
	
	$dir = $_POST['dir'];
	
	if (!file_exists($root)) die('{err:"1", err_info:"上传目录'.$dir.'不存在！"}');
}

function up_file($root)
{
	$dir = $_POST['dir'];
	$updir = $root.'/';
	$file = $_FILES['Filedata'];
	//更名
	$file_new_nm = date('YmdHis').rand(100,999);
    $file_name   = $file['tmp_name'];
   	$file_size = getimagesize($file_name);
	$file_info = pathinfo($file['name']);
	$file_type = $file_info["extension"];
	$destination = $updir.$file_new_nm.".".$file_type;
	
	//拷贝处理
    if(!move_uploaded_file ($file_name, $destination)) die('{err:"1", err_info:"文件上传失败！"}');
	
	//成功
	$arr['nm'] = $file_new_nm.".".strtolower($file_type);
	$arr['file'] = $file['name'];
    $arr['type'] = strtolower($file_type);
    $arr['size'] = $file['size'];
	
	die('{
		err:"0",
		nm:"'.$arr['nm'].'",
		file:"'.$arr['file'].'",
		type:"'.$arr['type'].'",
		size:"'.$arr['size'].'",
	}');
	
}

function up_img($root)
{
	$dir = $_POST['dir'];
	$updir = $root.'/';
	$file = $_FILES['Filedata'];
	//更名
	$file_new_nm = date('YmdHis').rand(100,999);
    $file_name   = $file['tmp_name'];
   	$file_size = getimagesize($file_name);
	$file_info = pathinfo($file['name']);
	$file_type = $file_info["extension"];
	$destination = $updir.$file_new_nm.".".$file_type;
	
	//拷贝处理
    if(!move_uploaded_file ($file_name, $destination)) die('{err:"1", err_info:"文件上传失败！"}');
	
	//成功
	$arr['nm'] = $file_new_nm.".".strtolower($file_type);
	$arr['file'] = $file['name'];
    $arr['type'] = strtolower($file_type);
    $arr['size'] = $file['size'];
	
	die('{
		err:"0",
		nm:"'.$arr['nm'].'",
		file:"'.$arr['file'].'",
		uploads:"'.$dir.'",
		type:"'.$arr['type'].'",
		size:"'.$arr['size'].'",
		id:"'.$_POST['id'].'",
	}');
	
}

}
?>