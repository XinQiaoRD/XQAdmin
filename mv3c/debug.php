<?php
//debug性能测试（简略）
$DeBug = array('time'=>0,'cache'=>0);
function debug8k($k=''){//$k标记
	global $DeBug;
	$htm = '<div style=" font-size:13px;margin:3px;padding:0px 15px;border:1px solid #333;background-color:#efefef;z-index:99999"><h2>'.$k.'</h2>';
	if($DeBug['time']){
		$time = microtime(true);
		$timex = ($time-$DeBug["time"]);
		$DeBug["time"] = $time;
		$htm.= '<p>当前时间戳:'.$DeBug['time'].'</p>';
		$htm.= "<p>消耗时间值:{$timex}<p>";
	}else{
		$DeBug["time"] = microtime(true);
		$htm.= '<p>当前时间戳:'.$DeBug["time"].'<p>';
	}
	
	if($DeBug["cache"]){
		$cache = memory_get_usage();
		$cachex = $cache-$DeBug["cache"];
		$DeBug["cache"] = $cache;
		$htm.= '<p>(全部)占用内存:'.$DeBug['cache'].'(约'.(int)($DeBug["cache"]/1024).'kb)<p>';
		$htm.= "<p>(距上面)消耗内存:{$cachex}(约".(int)($cachex/1024).'kb)<p>';
	}else{
		$DeBug["cache"] = memory_get_usage();
		$htm.= '<p>(全部)占用内存:'.$DeBug["cache"].'(约'.(int)($DeBug["cache"]/1024).'kb)<p>';
	}
	$htm.= '</div>';
	echo $htm;
}
if(@$_GET['debug8k']) debug8k('web start');
?>