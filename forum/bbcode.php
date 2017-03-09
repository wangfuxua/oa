<?php
//处理模块
function bbcode($message, $parseurl=0) {
	if(empty($search_exp)) {
		$search_exp = array(
		"/\s*\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s*/is",
		"/\[url\]\s*(https?:\/\/|ftp:\/\/|gopher:\/\/|news:\/\/|telnet:\/\/|rtsp:\/\/|mms:\/\/|callto:\/\/|ed2k:\/\/){1}([^\[\"']+?)\s*\[\/url\]/i",
		"/\[em:(.+?):]/is",
		);
		$replace_exp = array(
		"<div class=\"quote\"><span class=\"q\">\\1</span></div>",
		"<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",
		"<img src=\"image/face/\\1.gif\" class=\"face\">"
		);
		$search_str = array('[b]', '[/b]','[i]', '[/i]', '[u]', '[/u]');
		$replace_str = array('<b>', '</b>', '<i>','</i>', '<u>', '</u>');
	}

	if($parseurl==2) {//深度解析
		$search_exp[] = "/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies";
		$replace_exp[] = 'bb_img(\'\\1\')';
		$message = parseurl($message);
	}
	@$message = str_replace($search_str, $replace_str,preg_replace($search_exp, $replace_exp, $message));
	return nl2br(str_replace(array("\t", '   ', '  '), array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'), $message));
}

//自动解析url
function parseurl($message) {
	return preg_replace("/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/)([a-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/i", "[url]\\1\\3[/url]", ' '.$message);
}

//html转化为bbcode
function html2bbcode($message) {
	if(empty($html_s_exp)) {
		$html_s_exp = array(
		"/\<div class=\"quote\"\>\<span class=\"q\"\>(.*?)\<\/span\>\<\/div\>/is",
		"/\<a href=\"(.+?)\".*?\<\/a\>/is",
		"/(\r\n|\n|\r)/",
		"/<br.*>/siU",
		"/\s*\<img src=\"image\/face\/(.+?).gif\".*?\>\s*/is",
		"/\s*\<img src=\"(.+?)\".*?\>\s*/is"
		);
		$html_r_exp = array(
		"[quote]\\1[/quote]",
		"\\1",
		'',
		"\n",
		"[em:\\1:]",
		"\n[img]\\1[/img]\n"
		);
		$html_s_str = array('<b>', '</b>', '<i>','</i>', '<u>', '</u>', '&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;', '&lt;', '&gt;', '&amp;');
		$html_r_str = array('[b]', '[/b]','[i]', '[/i]', '[u]', '[/u]', "\t", '   ', '  ', '<', '>', '&');
	}

	@$message = str_replace($html_s_str, $html_r_str,
	preg_replace($html_s_exp, $html_r_exp, $message));

	$message = forum_htmlspecialchars($message);

	return trim($message);
}

function bb_img($url) {
	$url = addslashes($url);
	return "<img src=\"$url\">";
}

//视频标签处理
function bbcode_media($message) {
	$message = preg_replace("/\[flash\=?(media|real)*\](.+?)\[\/flash\]/ie", "bbcode_flash('\\2', '\\1')", $message);
	return $message;
}
//视频
function bbcode_flash($swf_url, $type='') {
	$width = '520';
	$height = '390';
	if ($type == 'media') {
		$html = '<object classid="clsid:6bf52a52-394a-11d3-b153-00c04f79faa6" width="'.$width.'" height="'.$height.'">
			<param name="autostart" value="0">
			<param name="url" value="'.$swf_url.'">
			<embed autostart="false" src="'.$swf_url.'" type="video/x-ms-wmv" width="'.$width.'" height="'.$height.'" controls="imagewindow" console="cons"></embed>
			</object>';
	} elseif ($type == 'real') {
		$html = '<object classid="clsid:cfcdaa03-8be4-11cf-b84b-0020afbbccfa" width="'.$width.'" height="'.$height.'">
			<param name="autostart" value="0">
			<param name="src" value="'.$swf_url.'">
			<param name="controls" value="Imagewindow,controlpanel">
			<param name="console" value="cons">
			<embed autostart="false" src="'.$swf_url.'" type="audio/x-pn-realaudio-plugin" width="'.$width.'" height="'.$height.'" controls="controlpanel" console="cons"></embed>
			</object>';
	} else {
		$html = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="'.$width.'" height="'.$height.'">
			<param name="movie" value="'.$swf_url.'">
			<param name="allowscriptaccess" value="always">
			<param name="wmode" value="transparent">
			<embed src="'.$swf_url.'" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowfullscreen="true" wmode="transparent" allowscriptaccess="always"></embed>
			</object>';
	}
	return $html;
}
?>