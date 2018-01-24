<?php
$json = file_get_contents('tz_data.json');

$callback=$_GET['jsoncallback'];
echo $callback."($json)";

?>