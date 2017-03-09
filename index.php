<?php 
define('THINK_PATH', './ThinkPHP');
define('APP_NAME', 'oa');
define('APP_PATH', './oa');

define('DESKTOP','/oa/Tpl/default/Public');
define('MOD_PATH','/oa/Tpl/default');

define('CUR_TIME_INT',time());
define('FILE_UPLOAD_PATH','/oa/Uploads');
define('LF', "\n");
define('CRLF', "\r\n");


//print_r($_REQUEST);
//print_r($_SERVER);
require(THINK_PATH."/ThinkPHP.php");
$App = new App(); 
$App->run();
?>