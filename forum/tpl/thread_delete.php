
<div id="<?=$pid?>" <?php if(!$inajax){?>class="inpage"<?php }?>>
<form id="delete_postform_<?=$pid?>" name="delete_postform_<?=$pid?>" method="post" action="thread_misc.php?act=delete&pid=<?=$pid?>&fid=<?=$fid?>">
	<h1>确定删除指定的帖子吗？</h1>
	<p class="btn_line">
		<input type="hidden" name="refer" value="<?$forum_interface['refer']?>" />
		<?php if($inajax){?>
		<input type="hidden" name="post_type" value="postdeletesubmit" />
		<input type="button" name="postdeletesubmit_btn" value="提交" class="submit" onclick="ajaxpost('delete_postform_<?=$pid?>', '<?=$pid?>', 'post_delete')" />&nbsp;
		<button name="btncancel" type="button" class="button" onclick="hideMenu()">取消</button>
		<?php }else{?>
		<input type="submit" name="postdeletesubmit" value="提交" class="submit" />&nbsp;
		<?php }?>
	</p>
</form>
</div>