<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="../Public/css/default.css" rel="stylesheet" type="text/css" />
<title></title>
</head>
<body >
<div style="width:800px;algin:center;">
<?php
include_once 'php-ofc-library/open_flash_chart_object.php';
$parent = $_GET['p_t']; // 父统计范围
$parent_cond = urldecode($_GET['name']); // 父统计条件
$ch = $_GET['c_t'];  // 子统计条件
if(count($_GET)==1){
	open_flash_chart_object( 500, 300, 'lines_data.php?p_t='.$parent, false );
	echo "<div><a href='pie.php?p_t=".$parent."'>饼状统计图</a>&nbsp;&nbsp;&nbsp;<a href='lines.php?p_t=".$parent."'>柱状统计图</a>&nbsp;&nbsp;&nbsp;<a href='javascript:history.go(-1);'>返回上一页</a></div>";
}
else{ 
	open_flash_chart_object( 500, 300, 'lines_data.php?p_t='.$parent.'&name='.urlencode($parent_cond).'&c_t='.$ch, false );
	echo "<div><a href='pie.php?p_t=".$parent."&name=".urlencode($parent_cond)."&c_t=".$ch."'>饼状统计图</a>&nbsp;&nbsp;&nbsp;<a href='lines.php?p_t=".$parent."&name=".urlencode($parent_cond)."&c_t=".$ch."'>柱状统计图</a>&nbsp;&nbsp;&nbsp;<a href='javascript:history.go(-1);'>返回上一页</a></div>";
}
?>
</div>
</body></html>