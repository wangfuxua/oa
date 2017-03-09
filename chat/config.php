<?php
$config	=	require '../config.php';
$link = mysql_connect($config["DB_HOST"], $config["DB_USER"], $config["DB_PWD"]);
if (!$link) {
	die('Could not connect: ' . mysql_error());
}
mysql_select_db($config["DB_NAME"],$link);
mysql_query('SET NAMES "utf8"',$link);
 

?>