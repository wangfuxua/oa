<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp.php 10145 2008-11-26 03:05:39Z liguode $
*/

//ͨ���ļ�
include_once('./common.php');
include_once(S_ROOT.'./source/function_cp.php');

//����ķ���
$acs = array('space', 'doing', 'upload', 'comment', 'blog', 'album', 'relatekw', 'common', 'class',
	'swfupload', 'thread', 'mtag', 'poke', 'friend',
	'avatar', 'profile', 'theme', 'import', 'feed', 'privacy', 'pm', 'share', 'advance', 'invite','sendmail',
	'userapp', 'task', 'credit', 'password', 'domain');
$ac = (empty($_GET['ac']) || !in_array($_GET['ac'], $acs))?'avatar':$_GET['ac'];
$op = empty($_GET['op'])?'':$_GET['op'];

//Ȩ���ж�
if(empty($_SGLOBAL['supe_uid'])) {
	if($_SERVER['REQUEST_METHOD'] == 'GET') {
		ssetcookie('_refer', rawurlencode($_SERVER['REQUEST_URI']));
	} else {
		ssetcookie('_refer', rawurlencode('cp.php?ac='.$ac));
	}
	showmessage('to_login', 'do.php?ac='.$_SCONFIG['login_action']);
}

//�Ƿ�ر�վ��
if(!in_array($ac, array('common', 'pm'))) {
	checkclose();
}

//��ȡ�ռ���Ϣ
$space = getspace($_SGLOBAL['supe_uid']);
if(empty($space)) {
	showmessage('space_does_not_exist');
}

//�˵�
$actives = array($ac => ' class="active"');

include_once(S_ROOT.'./source/cp_'.$ac.'.php');

?>