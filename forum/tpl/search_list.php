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
<div id="content">
<?php
if($list){
?>
<div class="topic_list">
	<table cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td class="subject">主题</td>
				<td class="author">作者 (回应/阅读)</td>
				<td class="lastpost">最后更新(作者/时间)</td>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($list as $key=>$value){
		?>
			<tr <?php if($key%2==1){ echo 'class="alt"';}?>>
				<td class="subject">
				<?php if($value['displayorder']){ echo '<font color="green">[顶]</font>';}?>
				<?php if($value['digest']){ echo '<font color="red">[精]</font>';}?>
				<a href="thread.php?tid=<?=$value['tid']?>&username=<?=isset($_SESSION['LOGIN_USER_ID'])?$_SESSION['LOGIN_USER_ID']:''?>&uid=<?=$_SESSION['LOGIN_UID']?$_SESSION['LOGIN_UID']:''?>"><?=$value['subject']?></a>
				</td>
				<td class="author"><?=$value['uid']?>(<em><?=$value['replynum']?>/<?=$value['viewnum']?></em>)</td>
				<td class="lastpost"><?=$value['lastauthor']?><br><?=date('m-d H:i',$value['lastpost']);?></td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
</div>
<div class="page"><?=$multi?></div>
<?php
}
?>
</div>
</div>