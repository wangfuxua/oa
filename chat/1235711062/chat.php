<?php
//****************参数设置****************
//显示在线用户
include "../config.php";
$user=isset($_GET['username'])?$_GET['username']:'';
$create_room=isset($_GET['create_room'])?$_GET['create_room']:'';
$room_name=isset($_GET['room_name'])?$_GET['room_name']:'';
date_default_timezone_set(PRC);
$disonline = true;
//新登陆时显示最近内容的条数(默认为30条)
$leastnum = 30;
//默认的会议室名(默认是每天换一个文件)，如果去掉d，则是每月换一个文件
/*$zone=ini_get('date.timezone');
if($zone=='PRC'){
$time_zone = time();
}else{
$time_zone = time()+60*60*8;
}*/

$time_zone = time();
$room = date("Y-m-d,H",$time_zone);

$create_room=isset($_GET['create_room'])?$_GET['create_room']:'';
$chatid=isset($_GET['chatid'])?$_GET['chatid']:'';
$roomdir = "rooms/";
//编码方式
$charset = "UTF-8";
//客户端最大显示内容条数(建议不要太大)
$maxdisplay = 300;


//语言
$lang = array(
//会议室描述
"description"=>"通达办公系统——会议室",
//会议室标题
"title"=>"通达办公系统——会议室",
//第一个到会议室的欢迎
"firstone"=>"<span style='color:#16a5e9;'>欢迎进入通达办公系统——会议室!</span>",
//当信息有禁止内容时显示
"ban"=>"I am a pig!",
//关键字
"keywords"=>"通达办公系统会议室",
//发言提示
"hereyourwords" => "在这里发言!"
);

header("content-type:text/html; charset=utf-8");

$get_past_sec = 3; //如果发现丢话，可以适当调大这个值
$touchs = 10; //检查在线人数的时间间隔
$title = $lang["title"];
$earlier = 10;
$description = $lang["description"];
$origroom = $room;
$least = ($_GET["dis"])?intval($_GET["dis"]):$leastnum;
$touchme = $_POST['touchme'];
if (!is_dir($roomdir)) @mkdir($roomdir) or die("建立文件 $roomdir 发生错误");
$room = $_GET['room'];
if (!$room) $room = $_POST["room"];
$room = checkfilename($room);
if (!$room) $room = $origroom;
$filename = $roomdir.$room.".dat.php";
$datafile = $roomdir.$room.".php";
if (!file_exists($filename)) @file_put_contents($filename,'<?php die();?>'."\n".$time_zone."|".$lang["firstone"]."\n");
if (!file_exists($datafile)) @file_put_contents($datafile,'<?php die();?>'."\n");
$action = $_POST["action"];

if (!function_exists("file_get_contents"))
{
	function file_get_contents($path)
	{
		if (!file_exists($path)) return false;
		$fp=@fopen($path,"r");
		$all=fread($fp,filesize($path));
		fclose($fp);
		return $all;
	}
}

if (!function_exists("file_put_contents"))
{
	function file_put_contents($path,$val)
	{
		$fp=@fopen($path,"w");
		fputs($fp,$val);
		fclose($fp);
		return true;
	}
}

function checkfilename($file)
{
	if (!$file) return "";
	$file = trim($file);
	$a = substr($file,-1);
	$file = eregi_replace("^[.\\\/]*","",$file);
	$file = eregi_replace("[.\\\/]*$","",$file);
	$arr = array("../","./","/","\\","..\\",".\\");
	$file = str_replace($arr,"",$file);
	return $file;
}

function get_ip()
{
	global $_SERVER;
	if ($_SERVER)
	{
		if ( $_SERVER[HTTP_X_FORWARDED_FOR] )
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if ( $_SERVER["HTTP_CLIENT_ip"] )
			$realip = $_SERVER["HTTP_CLIENT_ip"];
		else
			$realip = $_SERVER["REMOTE_ADDR"];
	}
	else
	{
		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )
			$realip = getenv( 'HTTP_X_FORWARDED_FOR' );
		else if ( getenv( 'HTTP_CLIENT_ip' ) )
			$realip = getenv( 'HTTP_CLIENT_ip' );
		else
			$realip = getenv( 'REMOTE_ADDR' );
	}
	return $realip;
}

function array2json($arr)
{
	$keys = array_keys($arr);
	$isarr = true;
	$json = "";
	for($i=0;$i<count($keys);$i++)
	{
		if ($keys[$i] !== $i)
		{
			$isarr = false;
			break;
		}
	}
	$json = $space;
	$json.= ($isarr)?"[":"{";
	for($i=0;$i<count($keys);$i++)
	{
		if ($i!=0) $json.= ",";
		$item = $arr[$keys[$i]];
		$json.=($isarr)?"":$keys[$i].':';
		if (is_array($item))
			$json.=array2json($item);
		else if (is_string($item))
			$json.='"'.str_replace(array("\r","\n"),"",$item).'"';
		else $json.=$item;
	}
	$json.= ($isarr)?"]":"}";
	return $json;
}

function keeponline()
{
	global $disonline,$datafile;
	if (!$disonline) return;
	$name = $_POST['name'];
	$ip = get_ip();
	$onlines = @file_get_contents($datafile);
	$s1 = "|{$name}|{$ip}|";
	if (strpos($onlines,$s1) === false)
	{
		if (strpos($onlines,"|".$name."|") === false)
		{
			$fp = @fopen($datafile,"a+");
			if ($fp)
			{
				if (@flock($fp, LOCK_EX))
				{
					@fputs($fp,time()."|".time().$s1."\n");
					@flock($fp, LOCK_UN);
				}
				@fclose($fp);
			}
		}
		else
		{
			echo "NAME";
			die();
		}
	}
}

if ($action == "write")
{
	$color = $_POST["color"];
	if (!eregi("[0-9a-fA-F]{6}",$color) || $color == "#000000") $color = "";
	$color = "#".$color;
	$size = $_POST["size"];
	$name = str_replace(array("\n","\r"),"",$_POST['name']);
	if (!$name) die("No Name!!");
	$ip = get_ip();
	keeponline();

	$s = "";
	$style = "";
	$font = $_POST["font"];
	if ($font == "songti") $font = "宋体";
	else if ($font == "heiti") $font = "黑体";
	else if ($font == "kaiti") $font = "楷体_GB2312";
	else $font = "";
	$style .= (!$font)?"":"font-family:".$font.";";
	$style .= (!$_POST["bold"])?"":"font-weight:bold;";
	$style .= (!$color || $color == "#")?"":"color:{$color};";
	$style .= (!$size || $size == "16")?"":"font-size:{$size}px;";
	$t = $time_zone;
	$arr = explode("\n",$_POST['content']);
	for($i = 0;$i<count($arr);$i++)
	{
		$content = $arr[$i];
		$content = trim($content);
		$content = str_replace(array("\n","\r"),"",$content);
		if (!$content) continue;
		$content = htmlspecialchars($content);
		$content = preg_replace("~\[img\](http:\/\/[a-zA-Z0-9\.-_\+%\?]*)\[\/img\]~i", "<img src='$1' />", $content);
		$content = ($style)?"<span style='{$style}'>{$content}</span>":$content;
		$s.= $t."|".$name.":".$content."\n";
	}

	if (!$s) die("No Content!!");
	$fp = @fopen($filename,"a+");
	if (!$fp) die("repeat");
	$re_time = 0;
	while(!@flock($fp, LOCK_EX))
	{
		sleep(1);
		$re_time++;
		if ($re_time >=4) break;
	}
	if ($re_time <4)
	{
		@fputs($fp,$s);
		@flock($fp, LOCK_UN);
	}
	else die("repeat");
	@fclose($fp);
	echo "OK";
}
else if ($action == "read")
{
	$first = $_POST["first"];
	$lastmod = intval($_POST["lastmod"]) - $get_past_sec; //得到两秒以内的所有发言，
	$alastmod = @filemtime($filename);
	$name = $_POST['name'];
	$name = str_replace("\n","",$name);
	$ip = get_ip();
	$json = array();
	$json["lastmod"] = time();
	$item = array();
	$newonline = array();
	$offline = array();

	$fp = @fopen($filename,'r');
	flock($fp,LOCK_EX);
	$s = fread($fp,filesize($filename));
	flock($fp,LOCK_UN);
	fclose($fp);
	$lines = explode("\n",$s);

	if ($alastmod >= $lastmod && !$first)
	{
		foreach($lines as $l)
		{
			$item2 = array();
			$l = str_replace(array("\n","\r"),"",$l);
			if (strpos($l,"|") === false) continue;
			$arr = explode("|",$l);
			$t = intval($arr[0]);
			if ($t >= $lastmod)
			{
				$item2["time"] = date("H:i:s",$t);
				$item2["word"] = addslashes($arr[1]);
				$item[] = $item2;
			}
		}
	}
	else if ($first)
	{
		$item = array();
		$total = count($lines);
		for($i=$total-1;$i>=$total-$least;$i--)
		{
			if ($i<=0) break;
			$item2 = array();
			$l = str_replace(array("\n","\r"),"",$lines[$i]);
			if (strpos($l,"|") === false) continue;
			$arr = explode("|",$l);
			$t = intval($arr[0]);
			$item2["time"] = (date("m-d",time()) == date("m-d",$t))?date("H:i:s",$t):date("m-d H:i",$t);
			$item2["word"] = addslashes($arr[1]);
			$item[] = $item2;
		}
		$item = array_reverse($item);
	}

	$s = "";
	$nt = time();
	$onlines = array();
	if($disonline && $touchme)
	{
		$users = @file($datafile);
		foreach($users as $l)
		{
			$l = str_replace(array("\r","\n"),"",$l);
			if (strpos($l,"|") === false)
			{
				$s.=$l."\n";
				continue;
			}
			$arr = explode("|",$l);
			if ($nt - intval($arr[1]) < $touchs*3)
			{
				if (trim($name) == trim($arr[2]))
				{
					$s.= $arr[0]."|".time()."|".$name."|".get_ip()."|\n";
				}
				else $s.=$l."\n";
				$onlines [] = $arr[2];
			}
		}
		@file_put_contents($datafile,$s);
		$json["onlines"] = $onlines;
	}
	$json["lines"] = $item;
	echo array2json($json);
}
else if ($action == "keep" )
{
	keeponline();
	echo "keep ok";
}
else if ($action == "quit")
{
	$name = $_POST['name'];
	if($disonline)
	{
		$users = @file($datafile);
		foreach($users as $l)
		{
			$l = str_replace(array("\r","\n"),"",$l);
			if (strpos($l,"|") === false)
			{
				$s.=$l."\n";
				continue;
			}
			$arr = explode("|",$l);
			if (trim($name) == trim($arr[2])) continue;
			else $s.=$l."\n";
		}
		@file_put_contents($datafile,$s);
		echo "OK";
	}
	die();
}
else
{
?>

<html>
<head>
 <title> <?php echo $title;?></title>
 <meta http-equiv='Pragma' content='no-cache' />
 <meta http-equiv=Content-Type content="text/html; charset=<?php echo $charset;?>" />
 <meta name="keywords" content="<?php echo $lang["keywords"];?>">
 <meta name="description" content="Mini AJAX Chatroom By Longbill. <?php echo $description;?>">

<style type='text/css'>
body	{ text-align:center; color:#333333; font-size:12px; font-family:宋体;
SCROLLBAR-FACE-COLOR: #fcfcfc;
 SCROLLBAR-HIGHLIGHT-COLOR: #6c6c90;
 SCROLLBAR-SHADOW-COLOR: #fcfcfc;
 SCROLLBAR-3DLIGHT-COLOR: #fcfcfc;
 SCROLLBAR-ARROW-COLOR: #240024;
 SCROLLBAR-TRACK-COLOR: #fcfcfc;
 SCROLLBAR-DARKSHADOW-COLOR: #48486c;
 SCROLLBAR-BASE-COLOR: #fcfcfc;
}
a	{ text-decoration:none; color:#a2b700; }
.mydiv	{ text-align:left; margin:5px; padding:5px; border:1px solid #bccadc;   width:100%; background:url('../images/menubg.gif');}
.mydiv2	{ text-align:left; margin:5px; padding:5px; border:1px solid #bccadc;   width:100%; background:#eff5f9; }
.mydiv_online	{ text-align:left; margin:5px; padding:5px; border:1px solid #bccadc;   width:100%; background:#eff5f9; }
.mydiv3	{ text-align:left; margin:5px; padding:5px; border:1px solid #bccadc;   width:100%; height:10px;background:url('../images/menubg.gif');}
.mydiv4	{ text-align:left; margin:5px; padding:5px; border:1px solid #bccadc;   width:100%; background:#eff5f9;}
.inputtext	{ border:0px; border-bottom:1px solid #bfccdd; background-color:transparent;}
.submit	{ border:1px solid #999999; background-color:transparent; padding-top:3px;width:100%;height:80px;}
.contents	{ border:1px solid #999999;margin:5px; margin-top:10px;background-color:#ffffff; overflow:auto;word-break:break-all;word-wrap :break-word;}
.bg	{ background-color:#ffffff; }
.content	{ border:0px;background-color:transparent;width:auto; font-size:16px; font-family:Fixedsys; margin:2px; padding:1px; }
.time	{ color:#aaaaaa; font-size:10px; font-family:Arial;}
.online	{ margin:5px; padding:0px; display:inline; overflow:auto;font-size:12px;}
.mybut	{ width:20px; height:20px; background-color:#bccadc; text-align:center; font-size:18px; color: #333333;}
.online_tab{ margin:5px;width:45%;height:20px;background-color:#bccadc;text-align:center;font-size:14px;background:url('../images/menubg.gif');padding:2px;}
</style>

<script>
function $(obj)
{
	return document.getElementById(obj);
}

function setCookie(name,value,t)
{
	var cookieexp = 5*30*24*60*60*1000; //5 months
	var cookiestr=name+"="+escape(value)+";";
	var expires = "";
	var d = new Date();
	var t2=(!t)?cookieexp:t*60*1000;
	d.setTime( d.getTime() + cookieexp);
	expires = "expires=" + d.toGMTString()+";";
	document.cookie = cookiestr+ expires;
}

function getCookie(name)
{
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) return "";
	if ( start == -1 ) return "";
	var end = document.cookie.indexOf( ";", len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}

function createAJAX()
{
	if (window.XMLHttpRequest)
	{
		var oHttp = new XMLHttpRequest();
		return oHttp;
	}
	else if (window.ActiveXObject)
	{
		var versions = [
			"MSXML2.XmlHttp.6.0",
			"MSXML2.XmlHttp.3.0"
		];

		for (var i = 0; i < versions.length; i++)
		{
			try {
				var oHttp = new ActiveXObject(versions[i]);
				return oHttp;
			} catch (error) {}
		}
    	}
	throw new Error("Your browser doesn't support XMLHttpRequest");
}

function pickColor()
{
	if (!window.isIE) return;
	var sColor = $('dlgHelper').ChooseColorDlg();
	var color = sColor.toString(16);
	while (color.length<6) color="0"+color;
	window.color = color;
	color = "#"+color;
	$('div_color').style.backgroundColor = color;
	$('div_color').value = color;
}

var isIE = (document.all && window.ActiveXObject) ? true : false;
</script>
</head>
<body >
<table width="100%" cellpadding="0" cellspacing="0"><tr><td colspan="2">
<div class="mydiv3" id='div_description'>
会议室名：<font color="green"><?=$room_name?></font>
</div></td></tr>
	<tr>
		<td width="80%"><div class="mydiv2 rooms" id='div_msg'>
<div class='contents' style='height:300px;' id='div_contents'>连接中...</div>
</div></td>
		<td><div class="mydiv_online"><span class="online_tab" onclick="document.getElementById('div_online').style.display='block';document.getElementById('div_rooms').style.display='none';this.style.background='#2f598e'" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='#bccadc'">在线列表</span><span class="online_tab" onclick="document.getElementById('div_rooms').style.display='block';document.getElementById('div_online').style.display='none';this.style.background='#2f598e'" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='#bccadc'">会议室列表</span><div class='contents' style='height:260px;'><br><div class='online'  id='div_rooms' style="display:none;">
		<?
		$chat_list = mysql_query("select * from `chatroom` order by `CHAT_ID`");
		 echo "<table>";
		while($rows = mysql_fetch_array($chat_list)){
		 echo "<tr><td><a href=\"chat_pro.php?create_room=3&username=".$user."&room_name=".$rows['SUBJECT']."\">".$rows['SUBJECT']."</a></td></tr>";
		}
		 echo "</table>";
		 ?></div><div class='online' style='display:<?php if (!$disonline) echo "none";?>;font-size:12px;' id='div_online'>刷新在线...</div> </div>

</div></td>
	</tr>
	<tr>
		<td colspan="2"><div class="mydiv login" id='div_name' style='display:block;height:20px;'>
名字:<input type=text class="inputtext bg" size=8 id='chat_user' value='<?=isset($_GET['username'])?$_GET['username']:''?>' />&nbsp;
<OBJECT id=dlgHelper CLASSID="clsid:3050f819-98b5-11cf-bb82-00aa00bdce0b" WIDTH="0px" HEIGHT="0px"></OBJECT>
<input class="inputtext" style='width:50px;cursor:hand;10px;background-color:#000000;color:#ffffff;' id='div_color' onClick="pickColor()" value="" onBlur="this.style.backgroundColor=this.value;window.color=this.value.replace('#','');" />
&nbsp;字号:<select id='input_size' class='inputtext bg' style='width:70px;'>
<option value='12'>12px</option>
<option value='16' selected>16px</option>
<option value='18'>18px</option>
<option value='26'>26px</option>
<option value='32'>32px</option>
<option value='48'>48px</option>
<option value='56'>56px</option>
</select>
<!--<input class="inputtext bg" type=text style='width:20px' maxlength=3 id='input_size' value='16' />(px)-->
&nbsp;字体:<select id='input_font' class='inputtext bg' style='width:70px;'>
<option value='Fixedsys'>Fixedsys</option>
<option value='heiti'>黑体</option>
<option value='songti'>宋体</option>
<option value='kaiti'>楷体</option>
</select>
加粗:<input type=checkbox id='input_bold' class='inputtext' style='border-bottom:0px;' />
<!-- 快捷键： <a href='#' onclick="document.getElementById('chat_word').onkeydown='return check_send(event)');">[Enter]</a>
<a href='#' onclick="document.getElementById('chat_word').onkeydown='return ctrlEnter(event)';">[Ctrl+Enter]</a> -->
&nbsp;<a class='mybut' style='width:45px;font-size:12px;border:1px solid #666;padding:3px;' href='#' onclick='clearAll()'>清屏</a>
&nbsp;<a class='mybut' style='width:75px;font-size:12px;border:1px solid #666;padding:3px;' href="../chat_pro.php?create_room=1">创建会议室</a>
</div>
<div class="mydiv4 login" id='div_word'>
<div id="textarea1" style="display:block;width:80%;float:left;">
<textarea   class="inputtext bg" rows=5 scrolling=yes style='overflow:hidden;width:100%;' id='chat_word' onFocus="if (this.value == '<?php echo $lang["hereyourwords"];?>') this.value='';" onkeydown='return ctrlEnter(event)'><?php echo $lang["hereyourwords"];?></textarea>
</div>
<div style="float:right;width:19%;"><input type=button class=submit value='   发送(Ctrl+Enter)   ' id="send" onClick="chat_send();" onFocus="this.blur();"/></div>
</div></td>
	</tr>
</table>
<script>
var debug = 0;
var lastmod = <?php echo time()-$earlier*60;?>;
var login = 1;
var loading = false;
var olduser = getCookie('chatusername');
if (olduser != "") $('chat_user').value = olduser;
var room = "<?php echo $room;?>";
var first = 1;
var dis = "<?php echo $least;?>";
var lastword;
var color='';
var touchs = <?php echo $touchs;?>;
var dotouch = true;
var maxdisplay = <?php echo $maxdisplay;?>;
var nowdisplay = 1;
var sending = 0;
var loaded_lines = [];
function encode(s)
{
	return  (encodeURIComponent)? encodeURIComponent(s):s;
}

var keep_ajax;
function keeponline()
{
	var name = $('chat_user').value;
	if (!name) return;
	keep_ajax = createAJAX();
	keep_ajax.open('POST','<?php echo basename(__FILE__);?>',1);
	keep_ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	keep_ajax.onreadystatechange = function ()
	{
		if (keep_ajax.readyState == 4 && keep_ajax.status == 200)
		{
			//alert(keep_ajax.responseText);
		}
	}
	keep_ajax.send("action=keep&name="+encode(name));
}
setInterval("keeponline()",touchs*1000);

function quitroom()
{

		var ajax = createAJAX();
		ajax.open('POST','<?php echo basename(__FILE__);?>',0);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("action=quit&name="+encode($('chat_user').value));
		//alert("sending close  action=quit&name="+encode($('chat_user').value));
		//alert("response:"+ajax.responseText);
		setCookie("chatusername",'');

}
document.body.onbeforeunload =  quitroom;

setInterval(" load_word()",(debug)?6000:1000);

var load_word_ajax;

//下载完成后的处理函数
function load_word_change()
{
	if (load_word_ajax.readyState == 4)
	{
		if (load_word_ajax.status != 200)
		{
			load_word_error();
			return;
		}
		window.loading = false;
		var body = $('div_contents');

		try {
			if (debug) alert(load_word_ajax.responseText);
			eval("var arr = "+load_word_ajax.responseText);
		} catch(e)
		{
			alert('Error 101\nJSON syntax error!\n\n'+load_word_ajax.responseText);
			return;
		}
		if (!arr || !arr.lastmod || typeof(arr.lastmod) == "undefined" )
		{
			return;
		}

		var html = "";
		var line = arr.lines;
		var i = 0;
		var v1 = 0;
		var div_online = $('div_online');
		if (window.first)
		{
			body.innerHTML = "";
			window.first = false;
		}

		if (arr.onlines)
		{
			$('div_online').innerHTML = "";
			for(var i=0;i<arr.onlines.length;i++) addonline(arr.onlines[i]);
		}
		for(var i=0;i<line.length;i++)
		{
			var linekey = line[i].word.substring(line[i].word.length-20,line[i].word.length)+line[i].time;
			if (window.loaded_lines[linekey] === true)
			{
				if (debug) alert("jump:"+linekey);
				continue;
			}
			var div1 = document.createElement("div");
			window.nowdisplay ++;
			if (window.nowdisplay > window.maxdisplay) window.nowdisplay = 1;
			if ($("contentitem"+window.nowdisplay)) body.removeChild($("contentitem"+window.nowdisplay));
			div1.className = "content";
			div1.id = "contentitem"+window.nowdisplay;
			div1.innerHTML = line[i].word+" <span class='time'>("+line[i].time+")</span>";
			body.appendChild(div1);

			window.loaded_lines[linekey] = true;
			body.scrollTop = 655350;
			v1 = 1;
		}

		if (v1)
		{
			window.focus();
			document.body.focus();
			window.lastmod = arr.lastmod;
			if(debug) alert("lastmod = "+arr.lastmod + " \nwindow.lastmod="+window.lastmod);
			if ($('chat_word').disabled == false) $('chat_word').focus();
		}
	}
}

function load_word_error()
{
	window.loading = false;
	window.status = 'Error 102:while loading words';
	setTimeout("window.status = '';",5000);
}

function load_word()
{
	load_word_ajax = createAJAX();
	if (window.loading)
	{
		try
		{
			load_word_ajax.abort();
			window.loading = false;
		}catch(e)	{}
	}
	if (!window.lastmod)
	{
		alert("window.lastmod="+window.lastmod);
		return;
	}

	load_word_ajax.open('POST','<?php echo basename(__FILE__);?>',true);
	load_word_ajax.onreadystatechange = load_word_change;

	var urlstring = '';
	urlstring += "lastmod="+window.lastmod;
	urlstring+= "&room="+room;
	urlstring+= "&action=read";
	urlstring+= "&name="+encode($('chat_user').value);

	if (window.first)
	{
		urlstring+= "&first=true";
		urlstring += "&dis="+dis;
	}
	//如果到了取得在线用户的时间
	if (window.dotouch)
	{
		urlstring+= "&touchme=true";
		window.dotouch = false;
		//垃圾内存回收
		try { CollectGarbage(); } catch(e) {}
	}

	window.loading = true;
	if (debug) alert("sending:"+urlstring);
	load_word_ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	load_word_ajax.send(urlstring);
}

function touchme()
{
	window.dotouch = true;
	setTimeout("touchme()",window.touchs*1000);
}

function showalert(a,n)
{
	if (!n) n=0;
	if (n>3) return;
	if (!a)
	{
		a = 0;
		b = 1;
	}
	else
	{
		a = 1;
		b = 0;
	}
	document.title = mytitle[a];
	setTimeout("showalert("+b+","+(n+1)+");",500);
}

function addonline(name)
{
	if ($(name)) return;
	var d1 = document.createElement("div");
	d1.id = name;
	d1.innerHTML = name;
	d1.className = "online";
	$('div_online').appendChild(d1);
}

touchme();

function check_send(e)
{
	if (!e) e = window.event;
	var obj = $('chat_word');
	if (isIE) obj.style.height = obj.scrollHeight+3;
	if (e.keyCode == 13)
	{
		if ((!e.shiftKey && !e.altKey && !e.ctrlKey) || !isIE)
		{
			chat_send();
			obj.style.height = 20;
			return false;
		}
		else if (isIE) obj.style.height = obj.scrollHeight+18;
	}
	return true;
}

var send_ajax;


send_ajax_change  = function()
{
	if (send_ajax.readyState == 4)
	{
		if (send_ajax.status != 200)
		{
			send_ajax_error();
			return;
		}
		if (debug) alert("send_ajax response:"+send_ajax.responseText);
		if (send_ajax.responseText.indexOf("NAME")!=-1)
		{
			alert('已经有人使用你的昵称了');
			$('chat_user').value = "";
			$('chat_user').focus();
		}
		else if (send_ajax.responseText.indexOf("repeat")!=-1)
		{
			$('chat_word').value = window.lastcontent;
		}

		on_send_ok();

		if (!window.loading)
		{
			window.dotouch = true;
			load_word();
		}
	}
}

function on_send_begin()
{
	with($('chat_word'))
	{
		disabled = true;
		style.backgroundColor = "#eeeeee";
	}
	window.sending = 1;
}

function on_send_ok()
{
	window.sending = 0;
	with($('chat_word'))
	{
		value = '';
		disabled = false;
		focus();
		style.backgroundColor = "#ffffff";
	}
}

function on_send_error()
{
	window.sending = 0;
	with($('chat_word'))
	{
		disabled = false;
		focus();
		style.backgroundColor = "#ffffff";
	}
}

function send_ajax_error()
{
	alert('发送失败\n请重新发送!');
	$('chat_word').value = window.lastcontent;
	window.sending = 0;
	on_send_error();
}

function chat_send()
{
	send_ajax = createAJAX();
	send_ajax.open('POST','<?php echo basename(__FILE__);?>',true);
	send_ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	send_ajax.onreadystatechange = send_ajax_change;
	var urlstring = '';
	var name = $('chat_user').value.replace("\n","");
	var content = $('chat_word').value;
	var bold = ($('input_bold').checked)?"bold":"";
	var size = $('input_size').value;
	var font = $('input_font').value;

	if (name == "")
	{
		alert('请输入你的昵称!!');
		$('chat_user').focus();
		return;
	}

	if (content == "" || content == "\n" || content == "\n\n" || content == "\n\n\n")
	{
		alert('发言内容不能为空!');
		$('chat_word').focus();
		$('chat_word').value = "";
		return;
	}
	if (size>100) size = 100;
	else if (size<0) size = 1;

	urlstring+= "action=write";
	urlstring+= "&name="+encode(name);
	urlstring+= "&content="+encode(content);
	urlstring+= "&bold="+bold;
	urlstring+= "&color="+window.color;
	urlstring+= "&size="+size;
	urlstring+= "&font="+font;
	urlstring+= "&room="+room;

	window.sending = 1;
	window.lastcontent = content;
	on_send_begin();
	if (debug) alert("sending:"+urlstring);

	send_ajax.send(urlstring);
	setTimeout("if (window.sending) send_ajax.abort(); on_send_error();",5000);
	setCookie("chatusername",$('chat_user').value);
}

function resize(s)
{
	var o = $('div_contents').style;
	var h = parseInt(o.height);
	h = (s)?h+50:h-50;
	if (h<=50 || h>=3000) return;
	o.height = h;
	$('div_contents').scrollTop = 655350;
}

function clearAll()
{
	$('div_contents').innerHTML = "";
}
//===============================ctrl+enter
function isKeyTrigger(e,keyCode){
    var argv = isKeyTrigger.arguments;
    var argc = isKeyTrigger.arguments.length;
    var bCtrl = false;
    if(argc > 2){
        bCtrl = argv[2];
    }
    var bAlt = false;
    if(argc > 3){
        bAlt = argv[3];
    }

    var nav4 = window.Event ? true : false;

    if(typeof e == 'undefined') {
        e = event;
    }

    if( bCtrl &&
        !((typeof e.ctrlKey != 'undefined') ?
            e.ctrlKey : e.modifiers & Event.CONTROL_MASK > 0)){
        return false;
    }
    if( bAlt &&
        !((typeof e.altKey != 'undefined') ?
            e.altKey : e.modifiers & Event.ALT_MASK > 0)){
        return false;
    }
    var whichCode = 0;
    if (nav4) whichCode = e.which;
    else if (e.type == "keypress" || e.type == "keydown")
        whichCode = e.keyCode;
    else whichCode = e.button;

    return (whichCode == keyCode);
}

function ctrlEnter(e){
    var ie =navigator.appName=="Microsoft Internet Explorer"?true:false;
    if(ie){
        if(event.ctrlKey && window.event.keyCode==13){doSomething();}
    }else{
        if(isKeyTrigger(e,13,true)){doSomething();}
    }
}
function doSomething(){document.getElementById('send').click(); }
//=============================
</script>


</body>
</html>
<?php
}
?>