<?php
class upload
{

function file_clear($upload, $fi, $fis)
{
	if(!$fis) return;
	if($fi) $fis = str_replace('|'.$fi.'|', '', $fis);
	$fis = str_replace('||', ',', $fis);
	$fis = str_replace('|', '', $fis);
	if($fi) $fis = str_replace($fi, '', $fis);
	$fis = explode(',',$fis);
	$root = dirname(dirname(dirname(__FILE__)));
	$url = $root.'/uploads/'.$upload.'/';
	
	foreach($fis as $fi)
		if($fi) if (file_exists($url.$fi)) unlink($url.$fi);
}

function file_clears($upload, $fi, $fis)
{
	if($fis) {
		if($fi) {
			$fit = str_replace('||', ',', $fi);
			$fit = str_replace('|', '', $fit);
			$fi = explode(',', $fit);
			
			foreach ($fi as $v){
				$fis = str_replace("|{$v}|", '', $fis);
			}
		}
		
		$fis = str_replace('||', ',', $fis);
		$fis = str_replace('|', '', $fis);
		$fis = explode(',',$fis);
		$root = dirname(dirname(dirname(__FILE__)));
		$url = $root.'/uploads/'.$upload.'/';
		
		foreach($fis as $v){
			if($v) if (file_exists($url.$v)) unlink($url.$v);
		}
	}
	
}

function file_del($upload, $fi)
{
	if($fi){
		$root = dirname(dirname(dirname(__FILE__)));
		$url = $root.'/uploads/'.$upload.'/';
		if (file_exists($url.$fi)) unlink($url.$fi);
	}
}

}
?>