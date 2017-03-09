<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<form method="post" action="">
<input type="text" name="new_forum"><input type="submit" value="创建讨论组">
</form>
<b><a href="forum.php">返回</a></b>
<?php
include_once('./ini.php');
$forum_name=isset($_POST['new_forum'])?$_POST['new_forum']:'';
if($forum_name!=''){
$sql="INSERT INTO `forum` ( `forumname` , `fieldid` , `close` , `announcement` , `viewperm` , `moderator` )
VALUES ( '".$forum_name."', '0', '0', '', '0', '')";
mysql_query($sql);
}

?>
