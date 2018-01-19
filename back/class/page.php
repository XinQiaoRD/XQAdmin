<?php
class page
{

//获得底部代码
function bot($url, $pt){
	if(!$url) die('地址未设置');
	
	$page = $pt['page_now'];
	$num = $pt['page_line_num'];
	$page_max = $pt['page_max'];
	$page_num = 5;
	
	if($num==0) return '<li class="disabled"><a href="#">未找到您要的数据！</a></li>';
	if($page_max<=1) return '<li class="disabled"><a href="#">记录数：'.$num.'</a></li>';
	
	if(!strpos($url,'?')) $url.='?';
	$url.= $para;
	
	$page_n = ceil($page_num/2);
	$page_p = $page_max-$page_n;
	
	if ($page!=1) $pre = '<li><a href="'.$url.'&p='.($page-1).'">上一页</a></li>';
	else $pre = '<li class="disabled"><a href="javascript:void(0)">上一页</a></li>';
	
	if ($page!=$page_max)  $next = '<li><a href="'.$url.'&p='.($page+1).'">下一页</a></li>';
	else $next = '<li class="disabled"><a href="javascript:void(0)">下一页</a></li>';
	
	if($page_num>=$page_max) {
		$rt = 1;
		$re = $page_max;
	}elseif($page<=$page_n){
		$rt = 1;
		$re = $page_num;
	}elseif($page>$page_p){
		$rt = $page_max - $page_num + 1;
		$re = $page_max;
	}else{
		$rt = $page-$page_n+1;
		$re = $rt+$page_num-1;	
	}
	
	$list = '';	
	for ($i=$rt; $i<=$re; $i++) {
		if($page==$i) $list.= '<li class="active"><a href="javascript:void(0)">'.$i.'</a></li>';
		else $list.= '<li><a href="'.$url.'&p='.$i.'">'.$i.'</a></li>';
	}
	
	$info = '<li class="disabled"><a href="#">记录'.$num.'条，页面'.$page.'/'.$page_max.'</a></li>';
	
	return $pre.$list.$next.$info;
}

}
?>