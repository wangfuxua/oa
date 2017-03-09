<div style="margin-left:30px; padding-top: 15px;">
<script language="javascript" src="editor/editor_function.js"></script>
<script type="text/javascript">
function validate(obj) {
	var subject = document.getElementById('subject');
	if (subject) {
		var slen = strlen(subject.value);
		if (slen < 1 || slen > 80) {
			alert("标题长度(1~80字符)不符合要求");
			subject.focus();
			return false;
		}
	}

	uploadEdit(obj);
	document.getElementById('forum_thread_submit').submit();
}

function edit_album_show(id) {
		var obj = $('uchome-edit-'+id);
		if(obj.style.display == '') {
			obj.style.display = 'none';
		} else {
			obj.style.display = '';
		}
	}


function startUpload(){
      document.getElementById('f1_upload_process').style.display = 'block';
      document.getElementById('f1_upload_form').style.display = 'none';
      return true;
}
function stopUpload(success,tname){
      var result = '';
      if (success == 1){
         result = '<span class="msg">上传成功！<a href="javascript:" onclick="insertImage(\''+tname+'\');">插入图片</a><\/span><br/><br/>';
	  }
      else {
         result = '<span class="emsg">There was an error during file upload!<\/span><br/><br/>';
      }
      document.getElementById('f1_upload_process').style.display = 'none';
      document.getElementById('f1_upload_form').innerHTML = result + '<label>上传图片: <input name="myfile" type="file" size="30" /><\/label><label><input type="submit" name="submitBtn" class="sbtn" value="上传" style="0px"/><\/label>';
      document.getElementById('f1_upload_form').style.display = 'block';
      return true;
}
</script>
<h2 class="title">
	<img src="image/forum.gif">讨论组
</h2>
<div class="tabs_header">
	<ul class="tabs">
		<li><a href="index.php"><span>讨论组首页</span></a></li>
		<li><a href="index.php?fid=<?=$forum_info['forumid']?>"><span><?=$forum_info['forumname']?></span></a></li>
		<?php if(isset($thread) && $thread){?>
		<li class="active"><a><span>编辑话题</span></a></li>
		<?php }else{?>
		<li class="active"><a><span>发表新话题</span></a></li>
		<?php }?>
	</ul>
</div>


<style>

#f1_upload_process{
   display:none;
}
.userData {behavior:url(#default#userdata);}
</style>

<div class="c_form">
	<form method="post" action="thread_misc.php" enctype="multipart/form-data" id="forum_thread_submit" name="form1">
	<input type="hidden" name="fid" value="<?=$fid?>" />
		<table cellspacing="4" cellpadding="4" width="100%" class="infotable">

			<tr>
				<td><input type="text" class="t_input" id="subject" name="subject" value="<?=isset($thread['subject'])?$thread['subject']:''?>" size="60" /></td>
			</tr>
			<tr>
				<td>
				<textarea class="userData" name="message" id="forum-ttHtmlEditor" style="height:100%;width:100%;display:none;border:0px"><?=isset($post['message'])?$post['message']:''?></textarea>
				<iframe src="editor.php?allowhtml=0" name="forum-ifrHtmlEditor" id="forum-ifrHtmlEditor" scrolling="no" border="0" frameborder="0" style="width:100%;border: 1px solid #C5C5C5;" height="200"></iframe>
				</td>
			</tr>
		</table>

		<input type="hidden" name="tid" value="<?=isset($thread['tid'])?$thread['tid']:''?>" />
		<input type="hidden" name="uid" value="<?=isset($_GET['uid'])?$_GET['uid']:''?>" />
		<input type="hidden" name="username" value="<?=isset($_GET['username'])?$_GET['username']:''?>" />
		<input type="hidden" name="post_type" value="threadsubmit" />
		<input type="button" id="threadbutton" name="threadbutton" value="提交发布" onclick="validate(this);" style="display: none;" />
	</form>
	<table cellspacing="4" cellpadding="4" width="100%" class="infotable">
		<tr><td>
		<input type="button" name="clickbutton[]" value="插入图片" class="button" onclick="edit_album_show('pic')">
		</td></tr>
	</table>

	<table cellspacing="4" cellpadding="4" width="100%" id="uchome-edit-pic" class="infotable" style="display:none;">
		<tr>
			<td>
				<table summary="Upload" cellspacing="2" cellpadding="0">
					<tbody id="attachbodyhidden" >
						<tr>
							<td>
							<form action="upload_pic.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUpload();" >
							 <p id="f1_upload_process">Loading...<br/><img src="image/loader.gif" /><br/></p>
							 <p id="f1_upload_form" align="center"><br/>
								 <label>上传图片:
									  <input name="myfile" class="myfile_pic" type="file" size="30"/>
								 </label>
								 <label>
									 <input type="submit" name="submitBtn" class="sbtn" value="上传"/>
								 </label>
							 </p>

							 <iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
							</form>

							</td>
						</tr>
					</tbody>
					<tbody id="attachbody"></tbody>
				</table>
			</td>
		</tr>
	</table>
	 <table cellspacing="4" cellpadding="4" width="100%" class="infotable">
		<tr>
			<td><input type="button" id="issuance" onclick="document.getElementById('threadbutton').click();" value="保存发布" class="submit" />
			<?php if(isset($thread) && $thread){?>
			<input type="button" onclick="window.location.href='thread.php?tid=<?=$thread['tid']?>'" value="取消&返回" class="submit" />
			<?php }?>
			</td>
		</tr>
	</table>
</div>
</div>