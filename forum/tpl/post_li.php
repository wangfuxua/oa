
<?php
if (empty($ajax_edit)) {
?>
<div id="post_<?=$value['pid']?>_li">
<?php }?>
<?php
$uid=isset($_GET['uid'])?$_GET['uid']:1;
$username=isset($_GET['username'])?$_GET['username']:'';
?>
<ul class="line_list">
	<li>
	<table width="100%">
	<tr>
		<td>
			<div class="title">
				<div class="r_option">
					<?php if($value['username']==$username){?>
					<a href="thread_misc.php?act=edit&pid=<?=$value['pid']?>&p_num=<?=$value['num']?>" id="p_<?=$value['pid']?>_edit" onclick="ajaxmenu(event, this.id, 99999,'' , -1)">编辑</a>
					<?php }else{?>
					<a href="thread_misc.php?act=reply&pid=<?=$value['pid']?>&uid=<?=$value['uid']?>&tid=<?=$value['tid']?>" id="p_<?=$value['pid']?>_edit" onclick="ajaxmenu(event, this.id, 99999, '', -1)">回复</a>
					<?php }?>
					<?php if($value['username']==$username||in_array($uid,$forum_config['adminid'])){?>
					<a href="thread_misc.php?act=delete&pid=<?=$value['pid']?>&fid=<?=$value['forumid']?>" id="p_<?=$value['pid']?>_delete" onclick="ajaxmenu(event, this.id, 99999)">删除</a>
					<?php }?>

					<span class="time"> </span>
				</div>
				<?=$value['username']?>
				<span class="time"><?=date('Y-m-d H:i',$value['dateline'])?></span>
			</div>
			<div class="detail" id="detail_<?=$value['pid']?>">
				<?=isset($value['message'])?$value['message']:''?>
				<?php if($value['pic']){?><div><a href="<?=$value['pic']?>" target="_blank"><img src="<?=$value['pic']?>" class="resizeimg" /></a></div><?php }?>
			</div>
		</td>
	</tr>
	</table>
	</li>
</ul>
<?php
if (empty($ajax_edit)) {
?>
</div>
<?php }?>