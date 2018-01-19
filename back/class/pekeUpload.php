<?php
class pekeUpload
{
    function upImg($filename, $targetFolder){
        if (!empty($_FILES)) {

            $tempFile = $_FILES[$filename]['tmp_name'];

            $targetPath = Root . $targetFolder;
            $file_new_nm = date('YmdHis').rand(100,999);

            $fileParts = pathinfo($_FILES[$filename]['name']);
            $file_type = $fileParts["extension"];

            $targetFile = rtrim($targetPath,'/') . '/' . $file_new_nm.".".$file_type;

            // Validate the file type
            $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
            $response = array ();
            if (in_array($file_type , $fileTypes)) {

                if(move_uploaded_file($tempFile , $targetFile)){
                    $response['success'] = 1;
                    $response['name'] = $file_new_nm.".".$file_type;
                    $response['size'] = $_FILES[$filename]["size"];
                    foreach ($_POST as $key => $value){
                        $response[$key] = $value;
                    }
                    return json_encode($response);
                }else{
                    $response['success'] = 0;
                    $response['error'] = '上传失败';
                    return json_encode($response);
                }

            } else {
                $response['success'] = 0;
                $response['error'] = '类型错误';
                return json_encode($response);
            }
        }else{
            $response['success'] = 0;
            $response['error'] = '上传文件丢失';
            return json_encode($response);
        }
    }

    function upMp3($filename, $targetFolder){
        if (!empty($_FILES)) {

            $tempFile = $_FILES[$filename]['tmp_name'];

            $targetPath = Root . $targetFolder;
            $file_new_nm = date('YmdHis').rand(100,999);

            $fileParts = pathinfo($_FILES[$filename]['name']);
            $file_type = $fileParts["extension"];

            $targetFile = rtrim($targetPath,'/') . '/' . $file_new_nm.".".$file_type;

            // Validate the file type
            $fileTypes = array('mp3'); // File extensions
            $response = array ();
            if (in_array($file_type , $fileTypes)) {

                if(move_uploaded_file($tempFile , $targetFile)){
                    $response['success'] = 1;
                    $response['name'] = $file_new_nm.".".$file_type;
                    $response['size'] = $_FILES[$filename]["size"];
                    foreach ($_POST as $key => $value){
                        $response[$key] = $value;
                    }
                    return json_encode($response);
                }else{
                    $response['success'] = 0;
                    $response['error'] = '上传失败';
                    return json_encode($response);
                }

            } else {
                $response['success'] = 0;
                $response['error'] = '类型错误';
                return json_encode($response);
            }
        }else{
            $response['success'] = 0;
            $response['error'] = '上传文件丢失';
            return json_encode($response);
        }
    }

    function upVideo($filename, $targetFolder){
        if (!empty($_FILES)) {

            $tempFile = $_FILES[$filename]['tmp_name'];

            $targetPath = Root . $targetFolder;
            $file_new_nm = date('YmdHis').rand(100,999);

            $fileParts = pathinfo($_FILES[$filename]['name']);
            $file_type = $fileParts["extension"];

            $targetFile = rtrim($targetPath,'/') . '/' . $file_new_nm.".".$file_type;

            // Validate the file type
            $fileTypes = array('mp4'); // File extensions
            $response = array ();
            if (in_array($file_type , $fileTypes)) {

                if(move_uploaded_file($tempFile , $targetFile)){
                    $response['success'] = 1;
                    $response['name'] = $file_new_nm.".".$file_type;
                    $response['size'] = $_FILES[$filename]["size"];
                    foreach ($_POST as $key => $value){
                        $response[$key] = $value;
                    }
                    return json_encode($response);
                }else{
                    $response['success'] = 0;
                    $response['error'] = '上传失败';
                    return json_encode($response);
                }

            } else {
                $response['success'] = 0;
                $response['error'] = '类型错误';
                return json_encode($response);
            }
        }else{
            $response['success'] = 0;
            $response['error'] = '上传文件丢失';
            return json_encode($response);
        }
    }

    function delFile($url, $file=""){
        if (file_exists(Root.$url.$file)) {
            unlink(Root.$url.$file);
            return true;
        }
        return false;
    }

    function delFolder($folder){
        if (file_exists(Root.$folder)) {
            rmdir(Root . $folder);
            return true;
        }else return false;
    }

    function createFolder($folder){
        if (!file_exists(Root.$folder)) {
            mkdir(Root . $folder);
            return true;
        }else return false;
    }

    function moveFile($file1 , $file2){
        $file1 = Root.$file1;
        $file2 = Root.$file2;
        if(!file_exists($file1)) return false;
        if(file_exists($file2)) return false;
        copy($file1 , $file2);
        unlink($file1);
    }

    public function sqliCache($g, $key, $forder, $rs, &$cc, $mk=""){
        if($rs[$key]==$g) return;
        if($mk=="strs"){
            if($g) $this->moveFile("/uploads/cache/".$g , "/uploads/{$forder}/".$g);
            if($rs[$key]) $this->delFile("/uploads/{$forder}/" , $rs[$key]);
            $cc->sqli($key, $g, "strs");
        }else{
            if($g) {
                $this->moveFile("/uploads/cache/".$g , "/uploads/{$forder}/".$g);
                if($rs[$key]) $this->delFile("/uploads/{$forder}/" , $rs[$key]);
                $cc->sqli($key, $g);
            }
        }

    }

    public function createJson($url , $fi, $json){
        $url = Root.$url.$fi;
        $file = fopen($url, "w");
        fwrite($file, $json);
        fclose($file);
    }

}
?>