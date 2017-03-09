
<div id="<?=$pid?>" <?php if(!$inajax){?>class="c_form"<?php }else{?>class="inajax"<?php }?>>
<?php if($inajax){?>
<h1>编辑</h1>
<a href="javascript:hideMenu();" class="float_del" title="关闭">关闭</a>
<div class="popupmenu_inner">
<?php }?>
	<form id="editpostform_<?=$pid?>" name="editpostform_<?=$pid?>" method="post" action="thread_misc.php?act=edit&pid=<?=$pid?>&fid=<?=$fid?>">
		<table>
			<tr>
				<th style="vertical-align: top;"><label for="message">内容：</label></th>
				<td>
					<a href="###" id="face_<?=$pid?>" onclick="showFace(this.id, 'message_<?=$pid?>');"><img src="image/facelist.gif" align="absmiddle" /></a>
					<img src="image/zoomin.gif" onmouseover="this.style.cursor='pointer'" onclick="zoomTextarea('message', 1)">
					<img src="image/zoomout.gif" onmouseover="this.style.cursor='pointer'" onclick="zoomTextarea('message', 0)"><br/>
					<textarea id="message_<?=$pid?>" name="message"  style="width:98%;" onkeydown="ctrlEnter(event, 'posteditsubmit_btn');" <?php if($post['isthread']){?>rows="18"<?php }else{?>rows="8"<?php }?>><?=$post['message']?></textarea>
				</td>
			</tr>
			<tbody id="editwebimg">
				<tr>
					<th>图片：</th>
					<td>
						<input class="t_input" type="text" onfocus="if(this.value == 'http://')this.value='';" onblur="if(this.value=='')this.value='http://'" name="pics[]" value="http://" size="40" />&nbsp;
						<a href="javascript:;" onclick="insertWebImg(this.previousSibling.previousSibling)">插入</a> &nbsp;
						<a href="javascript:;" onclick="delRow(this, 'editwebimg')">删除</a>
					</td>
				</tr>
			</tbody>
			<tr>
				<th>&nbsp;</th>
				<td>
					<a href="javascript:;" onclick="copyRow('editwebimg')">+增加图片</a> <span class="gray">只支持 .jpg、.gif、.png为结尾的URL地址</span>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td>
				<input type="hidden" name="pid" value="<?=$pid?>">
				<input type="hidden" name="refer" value="<?=$forum_interface['refer']?>" />
				<input type="hidden" name="post_type" value="posteditsubmit" />
				<?php if($inajax){?>
				<input type="button" name="posteditsubmit_btn" id="posteditsubmit_btn" value="提交" class="submit" onclick="ajaxpost('editpostform_<?=$pid?>', '<?=$pid?>', 'post_edit')" />&nbsp;
				<?php }else{?>
				<input type="submit" name="posteditsubmit_btn" id="posteditsubmit_btn" value="提交" class="submit" />&nbsp;
				<?php }?>
				</td>
			</tr>
		</table>
	</form>
<?php if($inajax){?></div><?php }?>
</div>