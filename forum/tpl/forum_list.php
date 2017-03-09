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
		<li style="margin-left:10px;"><form action="search.php" method="post">
主题关键字:<input type="text" size="35" name="keyword">&nbsp;<input type="submit" style="border:0px;width:68px;height:22px;background:url('image/search.gif') no-repeat;" value=""></form></li>
	</ul>
</div>

<div id="">
	<?php if($list){?>
	<div class="box">
	<ul class="thread_list">
	<?php
	foreach ($list as $value){
	?>
		<li>
			<div class="threadimg60"><a href="index.php?fid=<?=$value['forumid']?>"><img width="60px" height="55px" src="<?=$value['pic']?>"></a></div>
			<font size=2>名称:</font><a href="index.php?fid=<?=$value['forumid']?>"><b><?=cutstr($value['forumname'],20)?></b></a><br><p><font size="2" color="#000000">描述:</font><?=cutstr($value['announcement'],60)?></p>
		</li>
	<?php }?>
	</ul>
	<div class="page"><?=$multi?></div>
	</div>
	<?php }else{?>
	<div class="c_form">还没有群组。</div>
	<?php }?>

</div>
</div>