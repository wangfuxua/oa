<?php
$config	=	require '../config.php'; 
ob_start();
//error_reporting(E_ALL);
$link = mysql_connect($config["DB_HOST"], $config["DB_USER"], $config["DB_PWD"]);
if (!$link) {
	die('Could not connect: ' . mysql_error());
}
mysql_select_db($config["DB_NAME"],$link);
mysql_query('SET NAMES "utf8"',$link);

$forum_interface= array();
$forum_interface['refer']= '';
$forum_interface['timestamp']= time()+60*60*8;
$forum_interface['userinfo']['uid'] = 1;
$forum_interface['userinfo']['usergroupid'] = 1;
$forum_interface['userinfo']['username'] = 'eter';

$forum_interface['userinfo']['onlineip'] = '';

$inajax = isset($_REQUEST['inajax'])?intval($_REQUEST['inajax']):0;
$forum_config['gzipcompress'] = 0;
$forum_config['charset'] = 'utf-8';
$sql_admin_id_array=mysql_query("select * from `user` where `USER_PRIV`=1"); 
$admin_id_array=array(); 
while ($rows_array=mysql_fetch_assoc($sql_admin_id_array)) {
	$admin_id_array[]=$rows_array['uid'];
} 
$forum_config['adminid'] = $admin_id_array;
$forum_config['post_limit_time'] = 10;//发帖时间间隔



function cutstr($string, $length) {
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info);
        for($i=0; $i<count($info[0]); $i++) {
                $wordscut .= $info[0][$i];
                $j = ord($info[0][$i]) > 127 ? $j + 2 : $j + 1;
                if ($j > $length - 3) {
                        return $wordscut." ...";
                }
        }
        return join('', $info[0]);
}
for($i=0;$i<strlen($string);$i++)
{
echo cutstr($string,$i)."<br>";
}

function forum_get_forum_info($id){
	$rs = mysql_query("SELECT * FROM `forum` WHERE forumid='$id'");
	if(!$forum_info = mysql_fetch_assoc($rs)) {
		die('designated_election_it_does_not_exist');
	}
	//
	$forum_info['allowpost'] = 1;
	//是否允许查看
	$forum_info['allowview'] = 1;

	return $forum_info;
}

function forum_checkform($fid){
	$count = 0;
	$forum_info = array();
	if($fid) {
		$forum_info = forum_get_forum_info($fid);
		if($forum_info) {
			//判断是否关闭
			if($forum_info['close']) {
				forum_showmessage('mtag_close');
			}

			//是否允许发
			if(empty($forum_info['allowpost'])) {
				forum_showmessage('no_privilege');
			}
		}
	}
	if(empty($forum_info)) {
		forum_showmessage('first_select_a_mtag');
	}
	return $forum_info;
}


//删除话题
function forum_deletethreads($fid, $tids) {
	global $forum_interface,$forum_config;

	$tnums = $pnums = $delthreads = $newids = array();
	$allowmanage = in_array($forum_interface['userinfo']['usergroupid'],$forum_config['adminid']);

	//群主
	$wheresql = '';
	/*if(empty($allowmanage) && $fid) {
	$mtag = getmtag($fid);
	if($mtag['grade'] >=8) {
	$allowmanage = 1;
	$wheresql = " AND t.forumid='$fid'";
	}
	}*/

	$query = mysql_query("SELECT t.* FROM `thread` t WHERE t.tid IN("."'".implode("','", $tids)."'".") $wheresql");
	while ($value = mysql_fetch_assoc($query)) {
		if($allowmanage || $value['uid'] == $forum_interface['userinfo']['uid']) {
			$newids[] = $value['tid'];
			$value['isthread'] = 1;
			$delthreads[] = $value;
			$spaces[$value['uid']]++;
		}
	}
	if(empty($delthreads)) return array();

	//删除
	mysql_query("DELETE FROM `thread` WHERE tid IN("."'".implode("','", $newids)."'".")");
	mysql_query("DELETE FROM `post` WHERE tid IN("."'".implode("','", $newids)."'".")");

	//删除举报
	/*mysql_query("DELETE FROM `report` WHERE id IN ("."'".implode("','", $newids)."'".") AND idtype='thread'");*/

	/*//积分
	updatespaces($spaces, 'thread');*/

	return $delthreads;
}

//删除讨论
function forum_deleteposts($fid, $pids) {
	global $forum_interface,$forum_config;
	//统计
	$postnums = $mpostnums = $tids = $delposts = $newids = array();
	$allowmanage = in_array($forum_interface['userinfo']['usergroupid'],$forum_config['adminid']);

	//群主
	$wheresql = '';
	/*if(empty($allowmanage) && $fid) {
	$forum_info = forum_get_forum_info($fid);
	if($forum_info['grade'] >=8) {
	$allowmanage = 1;
	$wheresql = " AND p.forumid='$fid'";
	}
	}*/

	$query = mysql_query("SELECT p.* FROM `post` p WHERE p.pid IN ("."'".implode("','", $pids)."'".") $wheresql ORDER BY p.isthread DESC");
	while ($value = mysql_fetch_assoc($query)) {
		if($allowmanage || $value['uid'] == $forum_interface['userinfo']['uid']) {
			if($value['isthread']) {
				$tids[] = $value['tid'];
			} else {
				if(!in_array($value['tid'], $tids)) {
					$newids[] = $value['pid'];
					$delposts[] = $value;
					$postnums[$value['tid']]++;
					$spaces[$value['uid']]++;
				}
			}
		}
	}
	$delthreads = array();
	if($tids) {
		$delthreads = forum_deletethreads($fid, $tids);
	}
	if(empty($delposts)) {
		return $delthreads;
	}

	//整理
	$nums = com_renum($postnums);
	foreach ($nums[0] as $pnum) {
		mysql_query("UPDATE `thread` SET replynum=replynum-$pnum WHERE tid IN ("."'".implode("','", $nums[1][$pnum])."'".")");
	}

	//删除
	mysql_query("DELETE FROM `post` WHERE pid IN ("."'".implode("','", $newids)."'".")");

	/*//积分
	updatespaces($spaces, 'post');*/
	return $delposts;
}


//重新组建
function com_renum($array) {
	$newnums = $nums = array();
	foreach ($array as $id => $num) {
		$newnums[$num][] = $id;
		$nums[$num] = $num;
	}
	return array($nums, $newnums);
}


//分页
function com_multi($num, $perpage, $curpage, $mpurl) {
	$page = 5;
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&' : '?';
	$realpages = 1;
	if($num > $perpage) {
		$offset = 2;
		$realpages = @ceil($num / $perpage);
		$pages = $realpages;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="first">1 ...</a>' : '').
		($curpage > 1 ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="prev">上一页</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' :
			'<a href="'.$mpurl.'page='.$i.'">'.$i.'</a>';
		}
		$multipage .= ($curpage < $pages ? '<a href="'.$mpurl.'page='.($curpage + 1).'" class="next">下一页</a>' : '').
		($to < $pages ? '<a href="'.$mpurl.'page='.$pages.'" class="last">... '.$realpages.'</a>' : '');
		$multipage = $multipage ? ('<em>有'.$num.'个回复</em>'.$multipage):'';
	}
	$maxpage = $realpages;
	return $multipage;
}


//获取字符串
function forum_cut_str($string, $length, $in_slashes=0, $out_slashes=0, $bbcode=0, $html=0) {
	$charset = 'gb2312';
	$string = trim($string);

	if($in_slashes) {
		//传入的字符有slashes
		$string = forum_stripslashes($string);
	}

	if($html < 0) {
		//去掉html标签
		$string = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $string);
		$string = forum_htmlspecialchars($string);
	} elseif ($html == 0) {
		//转换html标签
		$string = forum_htmlspecialchars($string);
	}

	if($length && strlen($string) > $length) {
		//截断字符
		$wordscut = '';
		if(strtolower($charset) == 'utf-8') {
			//utf8编码
			$n = 0;
			$tn = 0;
			$noc = 0;
			while ($n < strlen($string)) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1;
					$n++;
					$noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2;
					$n += 2;
					$noc += 2;
				} elseif(224 <= $t && $t < 239) {
					$tn = 3;
					$n += 3;
					$noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4;
					$n += 4;
					$noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5;
					$n += 5;
					$noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6;
					$n += 6;
					$noc += 2;
				} else {
					$n++;
				}
				if ($noc >= $length) {
					break;
				}
			}
			if ($noc > $length) {
				$n -= $tn;
			}
			$wordscut = substr($string, 0, $n);
		} else {
			for($i = 0; $i < $length - 1; $i++) {
				if(ord($string[$i]) > 127) {
					$wordscut .= $string[$i].$string[$i + 1];
					$i++;
				} else {
					$wordscut .= $string[$i];
				}
			}
		}
		$string = $wordscut;
	}

	if($bbcode) {
		include_once('./bbcode.php');
		$string = bbcode($string, $bbcode);
	}

	if($out_slashes) {
		$string = forum_addslashes($string);
	}

	return trim($string);

}


//屏蔽html
function forum_checkhtml($html,$allowhtml=0) {
	$html = stripslashes($html);
	if(!$allowhtml) {

		preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

		$searchs[] = '<';
		$replaces[] = '&lt;';
		$searchs[] = '>';
		$replaces[] = '&gt;';

		if($ms[1]) {
			$allowtags = 'img|a|font|div|table|tbody|caption|tr|td|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote|object|param|embed';//允许的标签
			$ms[1] = array_unique($ms[1]);
			foreach ($ms[1] as $value) {
				$searchs[] = "&lt;".$value."&gt;";
				$value = forum_htmlspecialchars($value);
				$value = str_replace(array('\\','/*'), array('.','/.'), $value);
				$value = preg_replace(array("/(javascript|script|eval|behaviour|expression)/i", "/(\s+|&quot;|')on/i"), array('.', ' .'), $value);
				if(!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
					$value = '';
				}
				$replaces[] = empty($value)?'':"<".str_replace('&quot;', '"', $value).">";
			}
		}
		$html = str_replace($searchs, $replaces, $html);
	}
	$html = addslashes($html);

	return $html;
}

function forum_addslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = forum_addslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}
//去掉slassh
function forum_stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = forum_stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}
//取消HTML代码
function forum_htmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = forum_htmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
		str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}

//处理网络图片链接
function forum_getpicurl($picurl, $maxlenth='200') {
	$picurl = forum_htmlspecialchars(trim($picurl));
	if($picurl) {
		if(preg_match("/^http\:\/\/.{5,$maxlenth}\.(jpg|gif|png)$/i", $picurl)) return $picurl;
	}
	return '';
}


//添加数据
function db_inserttable($tablename, $insertsqlarr, $returnid=0, $replace = false) {
	global $link;
	$insertkeysql = $insertvaluesql = $comma = '';
	foreach ($insertsqlarr as $insert_key => $insert_value) {
		$insertkeysql .= $comma.'`'.$insert_key.'`';
		$insertvaluesql .= $comma.'\''.$insert_value.'\'';
		$comma = ', ';
	}
	$method = $replace?'REPLACE':'INSERT';
	mysql_query($method.' INTO `'.$tablename.'` ('.$insertkeysql.') VALUES ('.$insertvaluesql.')',$link);
	if($returnid && !$replace) {
		return mysql_insert_id();
	}
}
//更新数据
function db_updatetable($tablename, $setsqlarr, $wheresqlarr) {
	global $link;
	$setsql = $comma = '';
	foreach ($setsqlarr as $set_key => $set_value) {
		$setsql .= $comma.'`'.$set_key.'`'.'=\''.$set_value.'\'';
		$comma = ', ';
	}
	$where = $comma = '';
	if(empty($wheresqlarr)) {
		$where = '1';
	} elseif(is_array($wheresqlarr)) {
		foreach ($wheresqlarr as $key => $value) {
			$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
			$comma = ' AND ';
		}
	} else {
		$where = $wheresqlarr;
	}
	$sql = 'UPDATE `'.$tablename.'` SET '.$setsql.' WHERE '.$where;
	mysql_query($sql,$link);
}

//获取在线IP
function com_getonlineip($format=0) {
	global $forum_interface;

	if(empty($forum_interface['userinfo']['onlineip'])) {
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$onlineip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$onlineip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$onlineip = $_SERVER['REMOTE_ADDR'];
		}
		preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
		$forum_interface['userinfo']['onlineip'] = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
	}
	if($format) {
		$ips = explode('.', $forum_interface['userinfo']['onlineip']);
		for($i=0;$i<3;$i++) {
			$ips[$i] = intval($ips[$i]);
		}
		return sprintf('%03d%03d%03d', $ips[0], $ips[1], $ips[2]);
	} else {
		return $forum_interface['userinfo']['onlineip'];
	}
}


//调整输出
function ob_out() {
	global $forum_config,$inajax;

	$content = ob_get_contents();

	$preg_searchs = $preg_replaces = $str_searchs = $str_replaces = array();

	/*if($forum_config['allowrewrite']) {
	$preg_searchs[] = "/\<a href\=\"space\.php\?(uid|do)+\=([a-z0-9\=\&]+?)\"/ie";
	$preg_searchs[] = "/\<a href\=\"space.php\"/i";
	$preg_searchs[] = "/\<a href\=\"network\.php\?ac\=([a-z0-9\=\&]+?)\"/ie";
	$preg_searchs[] = "/\<a href\=\"network.php\"/i";

	$preg_replaces[] = 'rewrite_url(\'space-\',\'\\2\')';
	$preg_replaces[] = '<a href="space.html"';
	$preg_replaces[] = 'rewrite_url(\'network-\',\'\\1\')';
	$preg_replaces[] = '<a href="network.html"';
	}*/
	//外部链接显示导航条
	if($forum_config['linkguide']) {
		$preg_searchs[] = "/\<a href\=\"http\:\/\/(.+?)\"/ie";
		$preg_replaces[] = 'iframe_url(\'\\1\')';
	}

	if($inajax) {
		$preg_searchs[] = "/([\x01-\x09\x0b-\x0c\x0e-\x1f])+/";
		$preg_replaces[] = ' ';

		$str_searchs[] = ']]>';
		$str_replaces[] = ']]&gt;';
	}

	if($preg_searchs) {
		$content = preg_replace($preg_searchs, $preg_replaces, $content);
	}
	if($str_searchs) {
		$content = trim(str_replace($str_searchs, $str_replaces, $content));
	}

	obclean();
	if($inajax) {
		xml_out($content);
	} else{
		if($forum_config['charset']) {
			@header('Content-Type: text/html; charset='.$forum_config['charset']);
		}
		echo $content;
	}
}

function xml_out($content) {
	global $forum_config;
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
	@header("Content-type: application/xml; charset=".$forum_config['charset']."");
	echo '<'."?xml version=\"1.0\" encoding=\"".$forum_config['charset']."\"?>\n";
	echo "<root><![CDATA[".trim($content)."]]></root>";
	exit();
}
function obclean() {
	global $forum_config;
	ob_end_clean();
	if ($forum_config['gzipcompress'] && function_exists('ob_gzhandler')) {
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}
}

//对话框
function forum_showmessage($msgkey, $url_forward='', $second=1, $values=array()) {
	global $inajax,$forum_config;
	obclean();

	if(!$inajax && $url_forward && empty($second)) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: $url_forward");
	} else {
		$message = $msgkey;

		if($inajax) {
			if($url_forward) {
				$message = "<a href=\"$url_forward\">$message</a><ajaxok>";
			}
			if(isset($_GET['popupmenu_box']) && $_GET['popupmenu_box']) {
				$message = "<h1>&nbsp;</h1><a href=\"javascript:;\" onclick=\"hideMenu();\" class=\"float_del\">X</a><div class=\"popupmenu_inner\">$message</div>";
			}
			echo $message;
			ob_out();
		} else {
			if($url_forward) {
				$message = "<a href=\"$url_forward\">$message</a><script>setTimeout(\"window.location.href ='$url_forward';\", ".($second*1000).");</script>";
			}
			include_once('./tpl/showmessage.php');
		}
	}
	exit();
}



?>