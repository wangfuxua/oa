<?php
header("Content-type: text/html;charset=utf-8");
include_once('./ini.php');
if(isset($_POST['keyword'])){
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;
$keyword=trim($_POST['keyword']);
$sql = "SELECT * FROM `thread` WHERE `subject` LIKE '%".$keyword."%'";
	$rs = mysql_query($sql);

	if(!$thread = mysql_fetch_assoc($rs)) {
		//var_dump($thread);
		echo "相关信息不存在";
		exit();
	}
	$fid = $thread['forumid'];

	$perpage = 10;
	$start = ($page-1)*$perpage;
	$count = $thread['replynum'];
	if($count % $perpage == 0) {
		$perpage = $perpage + 1;
	}

	$pid = empty($_GET['pid'])?0:intval($_GET['pid']);

	$list = array();

	$rs = mysql_query("SELECT * FROM `thread` WHERE `subject` LIKE '%".$keyword."%' ORDER BY dateline LIMIT $start,$perpage");
	while ($value = mysql_fetch_assoc($rs)) {
		$list[] = $value;
	}
	$multi = com_multi($count, $perpage, $page, "search.php");
}
?>
<?php
/* 模板载入 */
include_once('./tpl/header.php');
if(isset($_POST['keyword'])){
include_once('./tpl/search_list.php');
}else{
include_once('./tpl/search.php');
}
include_once('./tpl/footer.php');
?>
