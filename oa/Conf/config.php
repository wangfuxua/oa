<?php
if (!defined('THINK_PATH')) exit();
$config	=	require 'config.php';
$array = array(
 'MODULES_DIR'=>'DESKTOP'.'/system/modules/',
 'THEMES_DIR'=>'DESKTOP'.'resources/themes/',
 'WALLPAPERS_DIR'=>'DESKTOP'.'resources/wallpapers/'
);
return array_merge($config,$array);
?>