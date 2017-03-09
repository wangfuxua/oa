<?php
session_start();
include_once('./ini.php');
//分页
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;
$tid = empty($_GET['tid'])?0:intval($_GET['tid']);

if($tid) {
	//话题
	$sql = "SELECT * FROM `thread` WHERE tid='$tid' LIMIT 1";
	$rs = mysql_query($sql);
	if(!$thread = mysql_fetch_assoc($rs)) {
		die('topic_does_not_exist');
	}
	$fid = $thread['forumid'];
	$forum_info = forum_get_forum_info($fid);
	if(empty($forum_info['allowview'])) {
		die('mtag_not_allow_to_do');
	}


	//帖子列表
	$perpage = 10;
	$start = ($page-1)*$perpage;
	$count = $thread['replynum'];
	if($count % $perpage == 0) {
		$perpage = $perpage + 1;
	}

	$pid = empty($_GET['pid'])?0:intval($_GET['pid']);
	$psql = $pid?"(isthread='1' OR pid='$pid') AND":'';

	$list = array();
	$postnum = $start;

	$rs = mysql_query("SELECT * FROM `post` WHERE $psql tid='".$thread['tid']."' ORDER BY dateline LIMIT $start,$perpage");
	while ($value = mysql_fetch_assoc($rs)) {
		$value['num'] = $postnum;
		$list[] = $value;
		$postnum++;
	}

	//取得内容
	if(isset($list[0]['isthread']) && $list[0]['isthread']) {
		$thread['content'] = $list[0];
		include_once('./bbcode.php');
		$thread['content']['message'] = bbcode_media($thread['content']['message']);

		unset($list[0]);
	} else {
		$thread['content'] = array();
	}

	//分页
	$multi = com_multi($count, $perpage, $page, "thread.php?tid=$tid");

	//访问统计,不是自己
	if($_SESSION['LOGIN_UID']!=$thread['uid']) {
		mysql_query("UPDATE `thread` SET viewnum=viewnum+1 WHERE tid='$tid'");
	}


}else{


}
?>
<?php
include_once('./tpl/header.php');
?>
<div style="margin-left:30px; padding-top: 15px;">
<h2 class="title">
	<img src="image/forum.gif">讨论组
</h2>

<div class="tabs_header">

	<ul class="tabs">
		<li><a href="index.php"><span>讨论组首页</span></a></li>
		<li><a href="index.php?fid=<?=$thread['forumid']?>"><span><?=$forum_info['forumname']?></span></a></li>
		<li class="active"><a><span>话题内容</span></a></li>
		<?php if ($forum_info['allowpost']) {
			echo '<li class="null"><a href="thread_misc.php?fid='.$forum_info['forumid'].'&username='.$_SESSION['LOGIN_USER_ID'].'&uid='.$_SESSION['LOGIN_UID'].'">发起新话题</a></li>';
		}
		?>
		<?php if($forum_info['allowpost']){?>
			<li class="null"><a href="#postform">回复此话题</a></li>
		<?php }?>
	</ul>
</div>

<div id="div_post">
	<div class="board">
		<?php
		if($thread['content']){
		?>

		<div id="post_<?=$thread[content][pid]?>_li">
			<ul class="line_list">
				<li>
				<table width="100%">
				<tr>
					<td>
						<div class="title">
							<div class="r_option">
								<?php if($thread['uid']==$_SESSION['LOGIN_UID']){?>
								<a href="thread_misc.php?act=edit&pid=<?=$thread['content']['pid']?>&fid=<?=$thread['forumid']?>&uid=<?=$thread['uid']?>">编辑</a>
								<?php }?>
								<?php if($thread['uid']==$_SESSION['LOGIN_UID']||in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){?>
								<a href="thread_misc.php?act=delete&pid=<?=$thread['content']['pid']?>&fid=<?=$thread['forumid']?>" id="p_<?=$thread['content']['pid']?>_delete" onclick="ajaxmenu(event, this.id, 99999)">删除</a>
								<?php }?>
								<?php if($forum_info['allowpost']){?>
								<a href="#postform">回复</a>
								<?php }?>
								<?php if(in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){?>
									<?php if($thread['displayorder']){?>
									<a href="thread_misc.php?act=untop&fid=<?=$thread['forumid']?>&tid=<?=$thread['tid']?>" id="t_<?=$thread['tid']?>_top" >取消置顶</a>
									<?php }else{?>
									<a href="thread_misc.php?act=top&fid=<?=$thread['forumid']?>&tid=<?=$thread['tid']?>" id="t_<?=$thread['tid']?>_top"  >置顶</a>
									<?php }?>
									<?php if($thread['digest']){?>
									<a href="thread_misc.php?act=undigest&fid=<?=$thread['forumid']?>&tid=<?=$thread['tid']?>" id="t_<?=$thread['tid']?>_digest" >取消精华</a>
									<?php }else{?>
									<a href="thread_misc.php?act=digest&fid=<?=$thread['forumid']?>&tid=<?=$thread['tid']?>" id="t_<?=$thread['tid']?>_digest" >精华</a>
									<?php }?>
								<?php }?>
							</div>
							<h1><?=$thread['subject']?></h1>
							<?=$thread['username']?>
							<span class="time"><?=date('Y-m-d H:i',$thread['dateline'])?></span>
						</div>
						<div class="detail" id="detail_0">

							<?=$thread['content']['message']?>
							<?php if($thread['content']['pic']){?>
							<div><a href="<?=$thread['content']['pic']?>" target="_blank"><img src="<?=$thread['content']['pic']?>" alt="" class="resizeimg" /></a></div>
							<?php }?>
						</div>
					</td>
				</tr>
				</table>
				</li>
			</ul>
		</div>
		<?php
		}
		?>

		<div id="post_ul">
			<?php if($pid){?>
			<div class="notice">
				当前只显示与你操作相关的单个帖子，<a href="thread.php?tid=<?=$thread['tid']?>">点击此处查看全部回帖</a>
			</div>
			<?php }?>

			<?php
			foreach ($list as $value){
				include('./tpl/post_li.php');
			}
			?>
		</div>

		<div class="page"><?=$multi?></div>

		<?php if($forum_info['allowpost']){?>
		<div class="quickpost" id="postform">
			<form method="post" action="thread_misc.php?1=1" class="quickpost" id="quickpostform_{<?=$thread['tid']?>}" name="quickpostform_{<?=$thread['tid']?>}">
				<table>
					<tr>
						<td>
							<a href="###" id="quickpost" onclick="showFace(this.id, 'message');"><img src="image/facelist.gif" align="absmiddle" border="0" /></a><br>
							<textarea id="message" name="message" onkeydown="ctrlEnter(event, 'postsubmit_btn');" col="50" rows="6" style="width: 450px; height: 6em;"></textarea>
							<input type="hidden" name="tid" value="<?=$thread['tid']?>" />
							<input type="hidden" name="post_type" value="postsubmit" />
							<input type="hidden" name="username" value="<?=$_SESSION['LOGIN_USER_ID']?>" />
							<input type="hidden" name="uid" value="<?=$_SESSION['LOGIN_UID']?>" />
							</form>
						</td>
					</tr>
					<tr>
						<td>
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
							 <p id="f1_upload_process" style="display:none;">Loading...<br/><img src="image/loader.gif" /><br/></p>
							 <p id="f1_upload_form">
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
						</td>
					</tr>

					<tr>
						<td>
							<input type="button" id="postsubmit_btn" name="postsubmit_btn" value="回复" class="submit" onclick="ajaxpost('quickpostform_{<?=$thread['tid']?>}', 'post_status', 'post_add');document.getElementById('uchome-edit-pic').style.display = 'none';" />
							<div id="post_status"></div>
						</td>
					</tr>
				</table>

		</div>
		<?php }else{?>
		<div class="c_form">
			<?php if($_SESSION['LOGIN_UID']==0){?>
				您还没有登陆，不能参与讨论。
			<?php }?>
		</div>
		<?php }?>
	</div>
</div>

<script type="text/javascript">
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
      document.getElementById('f1_upload_form').innerHTML = result + '<label>上传图片: <input name="myfile" type="file" class="myfile_pic" size="30" /><\/label><label><input type="submit" name="submitBtn" class="sbtn" value="上传" /><\/label>';
      document.getElementById('f1_upload_form').style.display = 'block';
      return true;
}
resizeImg('div_post','600');
</script>
</div>
</body>
</html>