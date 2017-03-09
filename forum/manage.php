<?php
session_start();
include_once('./ini.php');
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;

	$perpage = 6;
	$start = ($page-1)*$perpage;
	$theurl = "manage.php?";
	$list = $tagids = array();

	$sql_count = "SELECT COUNT(*) FROM `forum` main WHERE close!='1'";
	$rs_count = mysql_query($sql_count);
	$row=mysql_fetch_row($rs_count);
	$count = isset($row[0]) ? $row[0]:0;
	if($count) {
		$rs = mysql_query("SELECT main.* FROM `forum` main
			WHERE close!='1' ORDER BY fieldid DESC,forumid ASC  LIMIT $start,$perpage");
		while ($value = mysql_fetch_assoc($rs)) {
			if(empty($value['pic'])) {
				$value['pic'] = $value['moderator'];
			}
			$list[] = $value;
		}
	}

	//分页
	$multi = com_multi($count, $perpage, $page, $theurl);
$forum=isset($_GET['act'])?$_GET['act']:'';
$forumid=isset($_GET['forumid'])?$_GET['forumid']:'';
if($forum=='delete'){
mysql_query("delete from `forum` where forumid='".$forumid."'");
}
$field = isset($_GET['action'])?$_GET['action']:"";
$fieldid = isset($_POST['fieldid'])?$_POST['fieldid']:"";
if($field=='field'){
	$rs = mysql_query("update `forum` set (`fieldid`) values ('".$fieldid."') where `forumid`='".$forumid."'");
}
?>
<?php
/*模板载入 */
include_once('./tpl/header.php');
if($forumid&&$forum!='delete'){
include_once('./tpl/forum_edit.php');
}elseif($forum=='delete'){
include_once('./tpl/manage_tip.php');
}else{
include_once('./tpl/manage.php');
}
include_once('./tpl/footer.php');
?>

