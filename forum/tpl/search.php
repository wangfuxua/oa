<?
session_start();
?>
<div style="margin-left:30px; padding-top: 15px;">

<h2 class="title"><img src="image/forum.gif">讨论组</h2>
<div class="tabs_header">
	<ul class="tabs">
		<li class="active"><a href="index.php"><span>讨论组首页</span></a></li>
		<?php if(in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){?><li class="null"><a href="index.php?new_forum=1">创建讨论组</a></li><?php }?>
		<?php if(in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){?><li class="null"><a href="manage.php">讨论组管理</a></li><?php }?>
		<li class="null"><a href="search.php">搜索</a></li>
	</ul>
</div>
<div id="content">
<form action="search.php" method="post">
<table>
	<tr><td colspan="2"><h1>讨论组主题搜索</h1></td></tr>
	<tr>
		<td>关键字：<input type="text" size="55" name="keyword"></td><td><input type="submit" style="width:80px;border:1px" value="搜索"></td>
	</tr>
</table>
</form>
</div>
</div>