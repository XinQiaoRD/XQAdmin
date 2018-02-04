<?php
$json = file_get_contents('db_data.json');

$callback=$_GET['jsoncallback'];
echo $callback."($json)";

?>