
<div style="margin-left:30px; padding-top: 15px;">

<h2 class="title"><img src="image/forum.gif">讨论组</h2>
<div class="tabs_header">
	<ul class="tabs">
		<li class="active"><a href="index.php"><span>讨论组首页</span></a></li>
		<?php if(in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){?><li class="null"><a href="index.php?new_forum=1">创建讨论组</a></li><?php }?>
		<?php if(in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){?><li class="null"><a href="manage.php">讨论组管理</a></li><?php }?>
		<li style="margin-left:10px;"><form action="search.php" method="post">
主题关键字:<input type="text" size="35" name="keyword">&nbsp;<input type="submit" style="border:0px;width:68px;height:22px;background:url('image/search.gif') no-repeat;" value=""></form></li>
	</ul>
</div>

<div id="content">
<table width="100%">
<tr><td colspan="4" align="center">讨论组管理<td></tr>
	<tr>
		<td>id</td>
		<td>排序</td>
		<td>讨论组名称</td>
		<td>讨论组描述</td>
		<td>讨论组图标</td>
		<td>管理</td>
	</tr>
<?php if($list&&in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){
	foreach ($list as $value){
	?>
		<tr>
		<td><?=$value['forumid']?></td>
		<td><?=$value['fieldid']?></td>
		<td>
			<a href="index.php?fid=<?=$value['forumid']?>"><?=$value['forumname']?></a>
		</td>
		<td><?=$value['announcement']?></td>
		<td><img src="<?=$value['pic']?>" style="width:60px;height:55px;"></td>
		<td><a href="manage.php?forumid=<?=$value['forumid']?>&forumname=<?=$value['forumname']?>&pic=<?=$value['pic']?>&announcement=<?=$value['announcement']?>&fieldid=<?=$value['fieldid']?>&moderator=<?=$value['moderator']?>">编辑</a>|
		<a href="manage.php?forumid=<?=$value['forumid']?>&act=delete" onclick="javascipt:return(confirm('您确定要删除这个讨论组？'))">删除</a>
		</td>
		 </tr>
	<?php }?>
	 <tr><td colspan="4"><div class="page"><?=$multi?></div></td></tr>
	<?php }else{?>
	 <tr><td colspan="4">还没有讨论组</td></tr>
	<?php }?>

</table>
</div>
</div>