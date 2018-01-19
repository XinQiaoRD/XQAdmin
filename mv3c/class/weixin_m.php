<?php
$wx = wx_run();

if($wx_menu_re) wx_menu_re($wx_menu);

//模式配置
$A = 'index';
$T = $wx->MsgType;
if($wx->MsgType=='event'){
	switch($wx->Event){
		case 'subscribe':
			$T = 'login';
			break;
		case 'unsubscribe':
			$T = 'login';
			$A = 'out';
			break;
		case 'CLICK':
			$T = 'click';
			$A = 'click_'.$wx->EventKey;
			break;
	}
	
}

////文件嵌入
$model_path = Root."/{$C}/model/{$T}.php";
if(!is_file($model_path)) wx_err($wx, "/model/{$T} 文件没有建立！");
include($model_path);

//执行动作
$Class_nm = ucwords($C).'_'.$T;
$CC = new $Class_nm;

$CC->$A($wx);


function wx_run($err=0){
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	if (!empty($postStr)) {
		$wx  = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		if($err) wx_err($wx, $postStr);
		$wx->wx = $wx->ToUserName;
		$wx->user = $wx->FromUserName;
		return $wx;
	}else return false;
}

//text, image, location, link, event

function wx_menu_re($menu){
	$access_token = wx_access_token();
	if($access_token){
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;
		wx_curl_get($url);
		
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		wx_curl_post($url, $menu);
	}
}

function wx_menu($menu){
	$access_token = wx_access_token();
	if($access_token){
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		wx_curl_post($url, $menu);
	}
}
	
function wx_menu_del(){
	$access_token = wx_access_token();
	if($access_token){
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;
		wx_curl_get($url);
	}
}
	
function wx_access_token(){
	$json = wx_curl_get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".AppId."&secret=".AppSecret);
	$json = json_decode($json, true);
	return $json['access_token'];
}

	
function wx_curl_get($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// 3. 执行并获取HTML文档内容
	$output = curl_exec($ch);
	// 4. 释放curl句柄
	curl_close($ch);
	return $output;
}

function wx_curl_post($url, $data){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// 3. 执行并获取HTML文档内容
	$output = curl_exec($ch);
	// 4. 释放curl句柄
	curl_close($ch);
	return $output;
}

function wx_err($wx, $content){
	$FromUserName = $wx->FromUserName;
	$ToUserName = $wx->ToUserName;
	
	$content = str_replace("><", ">\n<", $content); 
	$content = str_replace("[", "【", $content); 
	$content = str_replace("]", "】", $content); 
	
	//文本回复
	$fromUsername = $FromUserName;
	$toUsername = $ToUserName;
	$time = time();
	
	$xml = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[{$content}]]></Content>
</xml>";
	$xml = sprintf($xml, $fromUsername, $toUsername, $time);
	echo $xml;
	exit;
}
?>