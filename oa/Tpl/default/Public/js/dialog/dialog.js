var URL_PUBLIC = "/oa/Tpl/default/Public";
var dlg_def_width	= 600;
var dlg_def_height	= 400;
var dlg_def_enter	= new Image();
var dlg_def_cancel	= new Image();
var dlg_def_wait	= new Image();
dlg_def_enter.src	= URL_PUBLIC+"/js/dialog/images/dlg_enter.gif";
dlg_def_cancel.src	= URL_PUBLIC+"/js/dialog/images/dlg_cancel.gif";
dlg_def_wait.src	= URL_PUBLIC+"/js/dialog/images/loading.gif";
var dlg_time_out;

function SelectUser(url,one_id,ids_id,names_id,w,h)
{
	var url = url+'/UserCategorySearch/userSelect/one_id/'+one_id+'/ids_id/'+ids_id+'/names_id/'+names_id;
	show_frame(url,w,h);
}

function OtherSelect(url,to_id,to_name,w,h)
{
	var url = url+'/to_id/'+to_id+'/to_name/'+to_name;
	show_frame(url,w,h);
}

function isNull(_sVal){
	return (_sVal === "" || _sVal == null || _sVal == "undefined");
}
function Cookie()
{
	this.setcookie = function(n, v, e, p){
		var cookiestr = n + "=" + escape(v) + "; ";
		if (!isNull(e) && !isNaN(e)){
			var d=new Date();
			d.setTime(d.getTime() + e * 1000);
			cookiestr += "expires=" + d.toGMTString() + "; ";
		}
		cookiestr += "domain=localhost; ";
		if (!isNull(p)){
			cookiestr += "path=" + p + "; ";
		}
		else{
			cookiestr += "path=/; ";
		}
		document.cookie = cookiestr;
	}
	this.getcookie = function(n){
		var aCookie = document.cookie.split("; ");
		for (var i=0; i < aCookie.length; i++){
			var aCrumb = aCookie[i].split("=");
			if (n == aCrumb[0]){
				return unescape(aCrumb[1]);
			}
		}
		return null;
	}
	this.delcookie = function(n){
		document.cookie = n + "=" + null + "; omain=localhost; path=/; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
	}
}
function ET$Id(s){
	if(document.getElementById){
		return document.getElementById(s);
	}
	else{
		return document.all[s];
	}
}
function ET$TagName(s, o){
	if (o == null){
		return document.getElementsByTagName(s);
	}
	else{
		return o.getElementsByTagName(s);
	}
}

document.write('<div id="dlg_back" onselectstart="return false" style="position:absolute;top:0px;left:0px;background:#DFDFDF;-moz-opacity:0.2;filter:Alpha(Opacity = 20);z-index:1900;">\
</div>');
document.write('\
<div id="dlg" onselectstart="return false" style="border:#E4E9EF 1px solid;border-top:0px;background:#FFF;width:' + dlg_def_width + 'px;height:' + dlg_def_height + 'px;position:absolute;overflow:hidden;display:none;z-index:1999;">\
	<iframe style="width:100%;height:100%;position:absolute;top:0px;left:0px;" frameborder="0" scrolling="no" src=""></iframe>\
	<div id="dlg_zone" style="width:100%;height:100%;position:absolute;top:0px;left:0px;display:none;">\
		<div id="dlg_title" style="height:23px;font-size:12px;width:100px;color:#013298;padding-left:15px;padding-top:5px;text-align:left;">信息提示</div>\
		<div id="dlg_string" style="width:170px;height:50px;font-size:12px;margin:20px auto;line-height:150%;"></div>\
		<div id="dlg_button" style="width:200px;height:30px;text-align:center;margin:20px auto;"></div>\
	</div>\
	<div style="border:1px #FFF solid;">\
		<div style="height:23px;border:1px #9AC2F5 solid;text-align:left;background:url('+URL_PUBLIC+'/js/dialog/images/dlg_title_bg.gif) repeat-x;overflow:hidden;"><img src="'+URL_PUBLIC+'/js/dialog/images/dlg_title_bg2.gif" /></div>\
	</div>\
	<div id="dlg_move" onmousedown="dialog.dlg_move(event);" style="width:80px;height:23px;position:absolute;top:0px;right:30px;cursor:move;">&nbsp;</div>\
	<div style="width:16px;height:16px;position:absolute;top:5px;right:10px;"><img src="'+URL_PUBLIC+'/js/dialog/images/dlg_close.gif" onclick="dialog.reset();" alt="关闭" /></div>\
	<div id="dlg_move_back" style="position:absolute;top:0px;left:0px;width:100%;height:100%;background:#FFF;-moz-opacity:0;opacity:0;filter:Alpha(Opacity = 0);display:none;"></div>\
	<div id="dlg_html" style="width:100%;height:100%;position:absolute;display:none;"></div>\
</div>\
');
document.write('<div id="dlg_wait" onselectstart="return false" style="position:absolute;top:0px;left:0px;width:' + dlg_def_wait.width + 'px;height:' + dlg_def_wait.height + 'px;display:none;z-index:191;"><img src="' + dlg_def_wait.src + '" border="0" alt="" /></div>');
document.write('<div id="dlg_load" onselectstart="return false" style="position:absolute;top:0px;left:0px;width:16px;height:16px;display:none;z-index:192;"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="26" height="26"><param name="wmode" value="transparent" /><param name="movie" value="./loading.swf" /><param name="quality" value="high" /><embed src="./loading.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent" width="26" height="26"></embed></object></div>');
var dialog = new Object();
dialog.__FUNC;
dialog.alert = function(str, func, bkgrd, full){	//对话框字符串；执行语句；是否全屏;是否带透明背景
	if (isNull(func)){
		func = "";
	}
	dialog.__FUNC = "dialog.reset();" + func;
	var but = '<input type="image" src="' + dlg_def_enter.src + '" onclick="eval(dialog.__FUNC)" width="56" height="21" alt="确定" />';
	ET$Id("dlg").style.display		= "";
	ET$Id("dlg_zone").style.display	= "";
	ET$Id("dlg_string").innerHTML = str;
	ET$Id("dlg_button").innerHTML = but;
	dialog.dlg_make(ET$Id("dlg"), full, bkgrd);
}
dialog.confirm = function(str, func, bkgrd, full){
	if (isNull(func)){
		func = "";
	}
	dialog.__FUNC = "dialog.reset();" + func;
	var but = '<input type="image" src="' + dlg_def_enter.src + '" onclick="eval(dialog.__FUNC)" width="56" height="21" alt="确定" /> <input type="image" src="' + dlg_def_cancel.src + '" onclick="dialog.reset();" width="56" height="21" alt="取消" />';
	ET$Id("dlg").style.display		= "";
	ET$Id("dlg_zone").style.display	= "";
	ET$Id("dlg_string").innerHTML	= str;
	ET$Id("dlg_button").innerHTML	= but;
	dialog.dlg_make(ET$Id("dlg"), full, bkgrd);
}
dialog.iframe = function(sUrl, sWidth, sHeight, bkgrd, sScroll, full){
	if (isNull(sScroll)){
		sScroll = "no";
	}
	var s = '<iframe width="100%" height="100%" scrolling="' + sScroll + '" src="' + sUrl + '" frameborder="0"  allowtransparency="true"></iframe>';
	dialog.html(s, sWidth, sHeight, bkgrd, full)
}
dialog.html = function(str, sWidth, sHeight, bkgrd, full){
	ET$Id("dlg").style.display	= "";
	if (!isNull(sWidth)){
		ET$Id("dlg").style.width = sWidth + "px";
	}
	if (!isNull(sHeight)){
		ET$Id("dlg").style.height = sHeight + "px";
	}
	ET$Id("dlg_move").style.width = parseInt(ET$Id("dlg").style.width) - 184 + "px";
	ET$Id("dlg_html").style.display	= "";
	ET$Id("dlg_html").innerHTML 	= str;
	dialog.dlg_make(ET$Id("dlg"), full, bkgrd);
}
dialog.resize = function(w, h, obj){
	var obj = isNull(obj) ? ET$Id("dlg") : obj;
	if (obj.style.display == "none"){
		return;
	}
	if (isNull(w)){
		w = parseInt(obj.style.width);
	}
	if (isNull(h)){
		h = parseInt(obj.style.height);
	}
	obj.style.width = w  + "px";
	obj.style.height = h + "px";
	var sDcument	= document.documentElement ? document.documentElement : document.body;
	with (sDcument){
		obj.style.top		= clientHeight/2 - h/2 + scrollTop + "px";
		obj.style.left		= clientWidth/2 - w/2 + scrollLeft + "px";
	}
}
dialog.wait = function(_timeout_str){
	ET$Id("dlg_wait").style.display = "";
	dialog.dlg_make(ET$Id("dlg_wait"), false, true);
	_timeout_str = isNull(_timeout_str) ? "连接超时，请重试！" : _timeout_str;
	dlg_time_out = setTimeout(function(){
		dialog.reset();
		dialog.alert(_timeout_str);
	}, 20000);
}
dialog.load = function(_timeout_str){
	ET$Id("dlg_load").style.display = "";
	dialog.dlg_make(ET$Id("dlg_load"), false, true);
	_timeout_str = isNull(_timeout_str) ? "连接超时，请重试！" : _timeout_str;
	dlg_time_out = setTimeout(function(){
		dialog.reset();
		dialog.alert(_timeout_str);
	}, 20000);
}
dialog.title = function(tit){
	ET$Id("dlg_title").innerHTML	= tit;
}
dialog.dlg_make = function(obj, full, bkgrd){
	var sDcument	= document.documentElement ? document.documentElement : document.body;
	with (sDcument){
		if (full){
			obj.style.width		= clientWidth + "px";
			obj.style.height	= clientHeight + "px";
			obj.style.top		= "-3px";
			obj.style.left		= "-3px";
		}
		else{
			dialog.resize(null, null, obj);
		}
		if(isNull(bkgrd) || bkgrd){
			ET$Id("dlg_back").style.display = "";
			ET$Id("dlg_back").style.width = scrollWidth + "px";
			ET$Id("dlg_back").style.height = scrollHeight + "px";
		}
	}
	try{
		$t("input", ET$Id("dlg_button"))[0].focus();
	}catch(E){}
}
dialog.dlg_move = function(evt){
	var mLeft = evt.clientX - parseInt(ET$Id("dlg").style.left);
	var mTop = evt.clientY - parseInt(ET$Id("dlg").style.top);
	document.onmousemove = function(){
		ET$Id("dlg_move_back").style.display = "";
		evt = arguments[0] ? arguments[0] : window.event;
		ET$Id("dlg").style.left = evt.clientX - mLeft  + "px";
		ET$Id("dlg").style.top = evt.clientY - mTop + "px";
	};
	document.onmouseup =  function(){
		ET$Id("dlg_move_back").style.display = "none";
		document.onmouseup = null;
		document.onmousemove = null;
	};
}
dialog.reset = function(){
	clearTimeout(dlg_time_out);
	
	/**************JQUERY写法：***************/
	/*
	$('#dlg_move_back').hide();//display= "none"
	$("#dlg").width(dlg_def_width);
	$("#dlg").height(dlg_def_height);
	$("#dlg_html").html('');
	$("#dlg_title").html('信息提示');
	$("#dlg_back").hide();
	$("#dlg_zone").hide();
	$("#dlg_html").hide();
	$("#dlg_wait").hide();
	$("#dlg_load").hide();
	$("#dlg").hide();
	*/
	ET$Id("dlg_move_back").style.display = "none";
	ET$Id("dlg").style.width			= dlg_def_width + "px";
	ET$Id("dlg").style.height			= dlg_def_height + "px";
	ET$Id("dlg_html").innerHTML			= "";
	ET$Id("dlg_title").innerHTML		= "信息提示！";
	ET$Id("dlg_back").style.display		= "none";
	ET$Id("dlg_zone").style.display		= "none";
	ET$Id("dlg_html").style.display		= "none";
	ET$Id("dlg_wait").style.display		= "none";
	ET$Id("dlg_load").style.display		= "none";
	ET$Id("dlg").style.display			= "none";
	
}

//按照以前使用习惯封装到函数中
function reset(){
	dialog.reset();
}
function resize(w, h){
	dialog.resize(w, h);
}
function show_error(str, func){
	reset()
	dialog.alert(str, func);
}

/* 弹出一个满屏的窗口，一般应用于 show_frame中的页面内使用。*/
function show_win(str, func){
	reset()
	dialog.alert(str, func, true, true);
}
function show_confirm(str, func){
	reset()
	dialog.confirm(str, func);
}
function show_send(_s){
	reset()
	dialog.wait(_s);
}
function show_loading(_s){
	reset()
	dialog.load(_s);
}
function show_frame(Url, sWidth, sHeight, bkgrd, sScroll, full){
	reset()
	dialog.iframe(Url, sWidth, sHeight, bkgrd, sScroll, full);
}
