<?php
if(!$_GET['id']) $json = '{"err":"no id"}';
else {
    if(!file_exists('stage'.$_GET['id'].'/base.json')) $json = '{"err":"url err"}';
    else $json = file_get_contents('stage'.$_GET['id'].'/base.json');
}

$callback=$_GET['jsoncallback'];
echo $callback."($json)";

?>