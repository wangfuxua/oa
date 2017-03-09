<?php
session_start();
include_once('./ini.php');

$act = empty($_GET['act'])?'':$_GET['act'];

if($act == 'post') {

	$pid = empty($_GET['pid'])?0:intval($_GET['pid']);

	if($pid) {
		$pidsql = "pid='".$pid."'";
		$ajax_edit = 1;
	} else {
		$pidsql = "uid='".$_SESSION['LOGIN_UID']."'";
		$ajax_edit = 0;
	}

	//评论
	$list = array();
	$rs = mysql_query("SELECT * FROM `post` WHERE $pidsql  ORDER BY dateline DESC LIMIT 0,1");
	while ($value = mysql_fetch_assoc($rs)) {

		$list[] = $value;
	}
}
if($act == 'reply') {

	$pid = empty($_GET['pid'])?0:intval($_GET['pid']);

	if($pid) {
		$pidsql = "pid='$pid' AND";
		$ajax_edit = 1;
	} else {
		$pidsql = '';
		$ajax_edit = 0;
	}

	//评论
	$list = array();
	$rs = mysql_query("SELECT * FROM `post` WHERE $pidsql uid='".$_SESSION['LOGIN_UID']."' ORDER BY dateline DESC LIMIT 0,1");
	while ($value = mysql_fetch_assoc($rs)) {

		$list[] = $value;
	}
}

?>

<?php
/* 模板载入 */
include_once('./tpl/header.php');
if($act=='post'){
	foreach($list as $value){
		include_once('./tpl/post_li.php');
	}
}
include_once('./tpl/footer.php');
?>