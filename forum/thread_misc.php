<?php
session_start();
header("Content-type: text/html;charset=utf-8");
include_once('./ini.php');

$post_type= isset($_POST['post_type'])?$_POST['post_type']:'';
if($post_type=='threadsubmit'){
	$tid = $_POST['tid'] = intval($_POST['tid']);
	$fid = empty($_POST['fid'])?0:intval($_POST['fid']);
	$uid = $_POST['uid'];
	$username = $_POST['username'];

	$subject = forum_cut_str($_POST['subject'], 80, 1, 1, 1);
	if(strlen($subject) < 2&&strlen($subject) > 80) forum_showmessage('标题不可少于两个字，或大于80个字');
	$_POST['message'] = forum_checkhtml($_POST['message'],0);

	$_POST['message'] = forum_cut_str($_POST['message'],0,1,0,1,1);
	$_POST['message'] = preg_replace("/\<div\>\<\/div\>/i", '', $_POST['message']);

	$message = $_POST['message'];

	//没有填写任何东西
	$ckmessage = preg_replace("/(\<div\>|\<\/div\>|\s)+/is", '', $message);
	if(strlen($ckmessage) < 2) {
		forum_showmessage('内容不可少于两个字');
	}

	//添加slashes
	$message = addslashes($message);

	if(empty($_POST['tid'])) {

		$setarr = array(
		'forumid' => $fid,
		//'uid' => $forum_interface['userinfo']['uid'],
		//'username' => $forum_interface['userinfo']['username'],
		'uid' => $uid,
		'username' => $username,
		'dateline' => $forum_interface['timestamp'],
		'subject' => $subject,
		'lastpost' => $forum_interface['timestamp'],
		'lastauthor' => $_SESSION['LOGIN_USER_ID'],
		'lastauthorid' => $_SESSION['LOGIN_UID']
		);
		$tid = db_inserttable('thread', $setarr, 1);
		$psetarr = array(
		'forumid' => $fid,
		'tid' => $tid,
		//'uid' => $forum_interface['userinfo']['uid'],
		//'username' => $forum_interface['userinfo']['username'],
		'uid' => $_POST['uid'],
		'username' => $username,
		'ip' => com_getonlineip(),
		'dateline' => $forum_interface['timestamp'],
		'message' => $message,
		'isthread' => 1
		);
		//添加
		db_inserttable('post', $psetarr);



	} else {

		$setarr = array(
		'forumid' => $fid,
		'dateline' => $forum_interface['timestamp'],
		'subject' => $subject
		);
		db_updatetable('thread', $setarr, array('tid'=>$tid, 'uid'=>$uid));

		$psetarr = array(
		'forumid' => $fid,
		'ip' => com_getonlineip(),
		'message' => $message,
		'pic' => ''
		);
		//if(checkperm('edittrail')) {
			//$message = $message.saddslashes(cplang('thread_edit_trail', array($_SGLOBAL['supe_username'], sgmdate('Y-m-d H:i:s'))));
		//}
		db_updatetable('post', $psetarr, array('tid'=>$tid, 'isthread'=>1, 'uid'=>$uid));
	}

	forum_showmessage('发布成功', "thread.php?tid=$tid", 3);
} elseif($post_type=='posteditsubmit'){
	$pid = empty($_POST['pid'])?0:intval($_POST['pid']);

	$message = $_POST['message'];
	//处理网络图片
	if(!empty($_POST['pics'])) {
		foreach($_POST['pics'] as $key => $pic) {
			$picurl = forum_getpicurl($pic);
			if(!empty($picurl)) {
				$message .= "\n[img]".$picurl."[/img]";
			}
		}
	}
	$message = forum_cut_str($message, 0, 1, 1,2);
	if(strlen($message) < 2){
		forum_showmessage('内容不可少于两个字');
	}

	//开启编辑记录
	/*if(checkperm('edittrail')) {
		$message = $message.saddslashes(cplang('thread_edit_trail', array($_SGLOBAL['supe_username'], sgmdate('Y-m-d H:i:s'))));
	}*/

	//内容
	db_updatetable('post', array('message'=>$message), array('pid'=>$pid, 'uid'=>$_SESSION['LOGIN_UID']));

	forum_showmessage('编辑成功', $pid, 0);

} elseif($post_type=='postsubmit') {
	//获得话题
	$tid = empty($_POST['tid'])?0:intval($_POST['tid']);
	$fid = empty($_POST['fid'])?0:intval($_POST['fid']);
	$thread = array();
	if($tid) {
		$rs = mysql_query("SELECT * FROM `thread` WHERE tid='$tid' LIMIT 1");
		$thread = mysql_fetch_assoc($rs);
	}
	if(empty($thread)) {
		forum_showmessage('the_discussion_topic_does_not_exist');
	}
	$message = $_POST['message'];
	//处理网络图片
	if(!empty($_POST['pics'])) {
		foreach($_POST['pics'] as $key => $pic) {
			$picurl = forum_getpicurl($pic);
			if(!empty($picurl)) {
				$message .= "\n[img]".$picurl."[/img]";
			}
		}
	}
	$message = forum_cut_str($message, 0, 1, 1, 2);
	if(strlen($message) < 2) {
		forum_showmessage('信息不能少于两个汉字！');
	}

	//摘要
	$summay = forum_cut_str($message, 150, 1, 1);

	$setarr = array(
		'forumid' => intval($thread['forumid']),
		'tid' => $tid,
		'uid' => $_SESSION['LOGIN_UID'],
		'username' => $_SESSION['LOGIN_USER_ID'],
		'ip' => com_getonlineip(),
		'dateline' => $forum_interface['timestamp'],
		'message' => $message,
		'pic' => forum_getpicurl($_POST['pic'])
	);
	$pid = db_inserttable('post', $setarr,1);
	//更新统计数据
	mysql_query("UPDATE `thread`
		SET replynum=replynum+1, lastpost='".$forum_interface['timestamp']."', lastauthor='".$_SESSION['LOGIN_USER_ID']."', lastauthorid='".$_SESSION['LOGIN_UID']."'
		WHERE tid='$tid'");
	//跳转
	forum_showmessage('发布成功，查看自己的帖子', "thread.php?tid=$tid&pid=$pid&username='".$_SESSION['LOGIN_USER_ID']."'&uid='".$_SESSION['LOGIN_USER_ID']."'", 0);
}

$pid = empty($_GET['pid'])?0:intval($_GET['pid']);
$tid = empty($_GET['tid'])?0:intval($_GET['tid']);
$fid = empty($_GET['fid'])?0:intval($_GET['fid']);
$thread = $post = array();
if(isset($_GET['act']) && $_GET['act'] == 'delete') {
	if($post_type=='postdeletesubmit') {
		if($delposts = forum_deleteposts($fid, array($pid))) {
			$post = $delposts[0];
			if($post['isthread']) {
				$url = "forum.php?fid=".$fid;//$post['forumid'];
			} else {
				$url = "thread.php?tid=".$tid;//$_POST['refer'];
			}
			forum_showmessage('删除成功', $url,'');
		} else {
			forum_showmessage('no_privilege');
		}
	}
}else if(isset($_GET['act']) && $_GET['act'] == 'edit') {
	$query = mysql_query("SELECT * FROM `post` WHERE pid='$pid' AND uid='".$_SESSION['LOGIN_UID']."'");
	if(!$post = mysql_fetch_assoc($query)) {
		forum_showmessage('no_privilege');
	}
	//移除编辑记录
	$post['message'] = preg_replace("/<ins class=\"modify\".+?<\/ins>/is", '',$post['message']);

	//检查权限
	$fid = $post['forumid'];
	$forum_info = forum_checkform($post['forumid']);

	//主题帖
	if($post['isthread']) {
		$query = mysql_query("SELECT * FROM `thread` WHERE tid='".$post['tid']."'");
		$thread = mysql_fetch_assoc($query);
	}

	if($thread) {
		$post['message'] = str_replace('&amp;', '&amp;amp;', $post['message']);
		$post['message'] = forum_htmlspecialchars($post['message']);

		$_GET['act'] = '';
		/*$albums = getalbums($_SGLOBAL['supe_uid']);*/
		if($post['pic']) {
			$post['message'] .= "<div><img src=\"".$post['pic']."\"></div>";
		}
	} else {
		include_once('./bbcode.php');
		$post['message'] = html2bbcode($post['message']);//显示用
	}

}else if(isset($_GET['act']) && $_GET['act'] == 'reply') {

	$query = mysql_query("SELECT * FROM `post` WHERE pid='$pid' AND uid='".$_GET['uid']."'");
	if(!$post = mysql_fetch_assoc($query)) {
		forum_showmessage('no_privilege');
	}
	//移除编辑记录
	$post['message'] = preg_replace("/<ins class=\"modify\".+?<\/ins>/is", '',$post['message']);

	//检查权限
	$fid = $post['forumid'];
	$forum_info = forum_checkform($post['forumid']);

	//主题帖
	if($post['isthread']) {
		$query = mysql_query("SELECT * FROM `thread` WHERE tid='".$post['tid']."'");
		$thread = mysql_fetch_assoc($query);
	}

	if($thread) {
		$post['message'] = str_replace('&amp;', '&amp;amp;', $post['message']);
		$post['message'] = forum_htmlspecialchars($post['message']);

		$_GET['act'] = '';
		/*$albums = getalbums($_SGLOBAL['supe_uid']);*/
		if($post['pic']) {
			$post['message'] .= "<div><img src=\"".$post['pic']."\"></div>";
		}
	} else {
		include_once('./bbcode.php');
		$post['message'] = html2bbcode($post['message']);//显示用
	}

}elseif(isset($_GET['act']) && $_GET['act'] == 'top'){
	mysql_query("UPDATE `thread` SET displayorder = 1	WHERE tid='".$_GET['tid']."'");
	$forum_info = forum_get_forum_info($fid);
	forum_showmessage('更新成功', "thread.php?tid=$tid&pid=$pid&username='".$_SESSION['LOGIN_USER_ID']."'&uid='".$_SESSION['LOGIN_USER_ID']."'", 0);

}
elseif(isset($_GET['act']) && $_GET['act'] == 'untop'){
	mysql_query("UPDATE `thread` SET displayorder = 0	WHERE tid='".$_GET['tid']."'");
	$forum_info = forum_get_forum_info($fid);
	forum_showmessage('更新成功', "thread.php?tid=$tid&pid=$pid&username='".$_SESSION['LOGIN_USER_ID']."'&uid='".$_SESSION['LOGIN_USER_ID']."'", 0);
}
elseif(isset($_GET['act']) && $_GET['act'] == 'undigest'){
	mysql_query("UPDATE `thread` SET digest = 0	WHERE tid='".$_GET['tid']."'");
	$forum_info = forum_get_forum_info($fid);
	forum_showmessage('更新成功', "thread.php?tid=$tid&pid=$pid&username='".$_SESSION['LOGIN_USER_ID']."'&uid='".$_SESSION['LOGIN_USER_ID']."'", 0);

}
elseif(isset($_GET['act']) && $_GET['act'] == 'digest'){
	mysql_query("UPDATE `thread` SET digest = 1	WHERE tid='".$_GET['tid']."'");
	$forum_info = forum_get_forum_info($fid);
	forum_showmessage('更新成功', "thread.php?tid=$tid&pid=$pid&username='".$_SESSION['LOGIN_USER_ID']."'&uid='".$_SESSION['LOGIN_USER_ID']."'", 0);

}
else{
	$fid = empty($_GET['fid'])?0:intval($_GET['fid']);
	$forum_info = forum_get_forum_info($fid);
}
?>


<?php
/* 模板载入 */
include_once('./tpl/header.php');

if(isset($_GET['act']) && $_GET['act']=='edit'){

	include_once('./tpl/thread_edit.php');

}elseif(isset($_GET['act']) && $_GET['act']=='reply'){

	include_once('./tpl/post_reply.php');

}elseif(isset($_GET['act']) && $_GET['act']=='delete'){

	include_once('./tpl/thread_delete.php');

} elseif($post_type=='newthread'){



}else{
	include_once('./tpl/thread_new.php');
}
include_once('./tpl/footer.php');
?>

