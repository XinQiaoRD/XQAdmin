<?php
class uploads
{

public function up_cut($file_src, $updir, $width, $height, $sw=0, $sh=0){

    $file_src = Root.$file_src;
    $updir = Root.$updir;

    $file = getimagesize($file_src);
    $file_width  = $file[0];//图片宽
    $file_height = $file[1];//图片高
    $file_mime   = $file['mime'];//图片格式

//    if($file_width<$width) echo"<script language=javascript>alert('宽小于要求分辨率的最低要求，保留原图大小。');history.back();</script>";
//    if($file_height<$height) echo"<script language=javascript>alert('高小于要求分辨率的最低要求，保留原图大小。');history.back();</script>";

    $whb = $width/$height;//最终宽高比
    $whb_now = $file_width/$file_height;//目前宽高比

    switch ($file_mime){
        case 'image/gif':
            $image = imagecreatefromgif($file_src);

            $thumb = imagecreatetruecolor($width , $height);

            if($whb_now>$whb){//太宽
                $width_new = $file_height*$whb;
                $dst_x = ($file_width-$width_new)/2;

                imagecopyresampled($thumb,$image,$sw,$sh,$dst_x,0,$width,$height,$width_new,$file_height);
            }else{
                $height_new = $file_width/$whb;
                $dst_y = ($file_height-$height_new)/2;

                imagecopyresampled($thumb,$image,$sw,$sh,0,$dst_y,$width,$height,$file_width,$height_new);
            }
            imagegif($thumb,$updir,100);
            break;

        case 'image/jpeg':
            $image = imagecreatefromjpeg($file_src);

            $thumb = imagecreatetruecolor($width , $height);

            if($whb_now>$whb){//太宽
                $width_new = $file_height*$whb;
                $dst_x = ($file_width-$width_new)/2;

                imagecopyresampled($thumb,$image,$sw,$sh,$dst_x,0,$width,$height,$width_new,$file_height);
            }else{
                $height_new = $file_width/$whb;
                $dst_y = ($file_height-$height_new)/2;

                imagecopyresampled($thumb,$image,$sw,$sh,0,$dst_y,$width,$height,$file_width,$height_new);
            }
            imagejpeg($thumb, $updir , 100);
            break;

        case 'image/png':
            $image = imagecreatefrompng($file_src);

            $thumb = imagecreatetruecolor($width , $height);
            imagealphablending($thumb,false);//这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
            imagesavealpha($thumb,true);//这里很重要,意思是不要丢了$thumb图像的透明色;

            if($whb_now>$whb){//太宽
                $width_new = $file_height*$whb;
                $dst_x = ($file_width-$width_new)/2;

                imagecopyresampled($thumb,$image,$sw,$sh,$dst_x,0,$width,$height,$width_new,$file_height);
            }else{
                $height_new = $file_width/$whb;
                $dst_y = ($file_height-$height_new)/2;

                imagecopyresampled($thumb,$image,$sw,$sh,0,$dst_y,$width,$height,$file_width,$height_new);
            }
            imagepng($thumb,$updir,9);
            break;

        default:
            return false;
            break;
    }
}

/*
上传文件
$input_fi_nm : 上传file控件名称
$updir : 上传目录
*/
function up_file($input_fi_nm, $updir ,$tp=""){

	//是否存在文件
	if(!$_FILES[$input_fi_nm]['name']) return false;
    if (!is_uploaded_file($_FILES[$input_fi_nm]["tmp_name"])) return false;// die("{$err01}上传文件错误或不存在{$err02}");
	$err01 = '<a href="#" onclick="history.go(-1);">';
	$err02 = '</a>';
	
	if(substr($updir,strlen($updir)-1,1)!='/') $updir.= '/';
	$updir = Root.$updir;


	//上传目录检查
    if(!file_exists($updir)) die("{$err01}上传目录不正确{$updir}{$err02}");
	
	$file = $_FILES[$input_fi_nm];
	//更名
	$file_new_nm = date('YmdHis').rand(100,999);
    $file_name   = $file['tmp_name'];
   	$file_size = getimagesize($file_name);
	$file_info = pathinfo($file['name']);
	$file_type = $file_info["extension"];
    if($tp){
        $destination = $updir."preview.".$file_type;
    }else{
        $destination = $updir.$file_new_nm.".".$file_type;
    }


	//拷贝处理
    if(!move_uploaded_file ($file_name, $destination)) die("{$err01}文件上传失败{$err02}");

	//成功
    if($tp){
        $arr['nm'] = "preview.".$file_type;
    }else{
        $arr['nm'] = $file_new_nm.".".$file_type;
    }
	$arr['file'] = $file['name'];
    $arr['type'] = strtolower($file_type);
    $arr['size'] = $file['size'];

    return $arr;
}

function del_file($url){
	if (file_exists(Root."/$url")) {
		unlink(Root."/$url");
		return true;
	}
	return false;
}

}
?>