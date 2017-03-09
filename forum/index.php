<?php
include_once('./ini.php');
//分页
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;
$id = empty($_GET['id'])?0:intval($_GET['id']);
$forumid = empty($_GET['fid'])?0:intval($_GET['fid']);
$new_forum = isset($_GET['new_forum'])?$_GET['new_forum']:'';
if($forumid){
	$forum_info = forum_get_forum_info($forumid);

	$perpage = 10;
	$start = ($page-1)*$perpage;

	$list = array();
	$count = 0;


	if($forum_info['allowview']) {
		$sql = "SELECT COUNT(*) as count FROM `thread` main WHERE main.forumid='$forumid'";
		$rs = mysql_query($sql);
		$row = mysql_fetch_assoc($rs);
		$count = $row['count'];
		if($count) {
			$rs = mysql_query("SELECT main.* FROM `thread` main WHERE main.forumid='$forumid' ORDER BY main.displayorder DESC, main.lastpost DESC
					LIMIT $start,$perpage");
			while ($value = mysql_fetch_assoc($rs)) {
				$list[] = $value;
			}
		}
		//分页
		$multi = com_multi($count, $perpage, $page, "index.php?fid=$forumid");
	}




}else{/* 讨论区列表 */
	$perpage = 16;
	$start = ($page-1)*$perpage;
	$theurl = "index.php?";
	$list = $tagids = array();

	$sql_count = "SELECT COUNT(*) FROM `forum` main WHERE close!='1'";
	$rs_count = mysql_query($sql_count);
	$row=mysql_fetch_row($rs_count);
	$count = isset($row[0]) ? $row[0]:0;
	if($count) {
		$rs = mysql_query("SELECT main.* FROM `forum` main
			WHERE close!='1' ORDER BY fieldid DESC LIMIT $start,$perpage");
		while ($value = mysql_fetch_assoc($rs)) {
			if(empty($value['pic'])) {
				$value['pic'] = $value['moderator'];
			}
			$list[] = $value;
		}
	}

	//分页
	$multi = com_multi($count, $perpage, $page, $theurl);
}

?>

<?php
/* 模板载入 */
include_once('./tpl/header.php');

if($forumid){
	include_once('./tpl/thread_list.php');
}elseif($new_forum==1){
	include_once('./tpl/new_forum.php');
}
else{
	include_once('./tpl/forum_list.php');
}
include_once('./tpl/footer.php');
?>
