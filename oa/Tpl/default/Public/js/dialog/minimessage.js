document.write("<div id='miniMessage' style='width:0px;height:0px;position:absolute;border:1px solid #60B0FF;display:none;overflow:hidden;background:#FFF;z-index:20'>\
<div id='miniBack' style='position:absolute;top:0px;left:0px;width:100%;height:100%;background:#FFF;-moz-opacity:0;filter:Alpha(Opacity = 0);display:none;'>&nbsp;</div>\
<div id='miniDrag' style='position:absolute;top:0px;left:0px;width:100%;height:25px;display:none;cursor:move;' onselectstart='return false'>&nbsp;</div>\
<div style='position:absolute;top:5px;right:10px;'><img src='./images/minmessage/msg_bbs.sina.com.cn.gif' width='65' height='16' alt='' /> <img id='miniImage' src='./images/minmessage/msg_min.gif' width='16' height='16'  style='cursor:pointer;' alt='' /> <img src='./images/minmessage/msg_close.gif' width='16' height='16' style='cursor:pointer;' onclick='miniMessage.close();' /></div>\
<div id='innerObj' style='width:100%;height:100%'></div>\
</div>");
var drag = function(evt){
	var move_obj = ET$Id("miniMessage");
	if (!evt){
		evt = window.event;
	}
	clearTimeout(window.__MINIMESSAGE);
	var mLeft = evt.clientX - parseInt(move_obj.style.left);
	var mTop = evt.clientY - parseInt(move_obj.style.top);
	document.onmousemove = function(){
		clearTimeout(window.__MINIMESSAGE);
		ET$Id("miniBack").style.display = "";
		evt = window.event || arguments[0];
		var _m_l = evt.clientX - mLeft;
		var _m_t = evt.clientY - mTop;
		var _m_l_max = miniMessage.dom.clientWidth + miniMessage.dom.scrollLeft - miniMessage.minibWidth - 2;	//边框1+1=2PX
		var _m_t_max = miniMessage.dom.clientHeight + miniMessage.dom.scrollTop - miniMessage.minibHeight - 2;
		if (_m_l <= miniMessage.dom.scrollLeft){
			_m_l = miniMessage.dom.scrollLeft;
		}
		else if (_m_l >= _m_l_max){
			_m_l = _m_l_max;
		}
		if (_m_t <= miniMessage.dom.scrollTop){
			_m_t = miniMessage.dom.scrollTop;
		}
		else if (_m_t >= _m_t_max){
			_m_t = _m_t_max;
		}
		move_obj.style.left = _m_l + "px";
		move_obj.style.top = _m_t + "px";
	}
	document.onmouseup = function(){
		document.onmousemove = null;
		ET$Id("miniBack").style.display = "none";
		miniMessage.miniTop = parseInt(move_obj.style.top) - miniMessage.dom.scrollTop;
		clearTimeout(window.__MINIMESSAGE);
		miniMessage.onscrolling();
	}
}
var miniMessage = {
	"minisWidth" : 280,		//小窗口宽高
	"minisHeight" : 195,
	"minibWidth" : 549,		//大窗口宽高
	"minibHeight" : 359,
	"miniTop" : 0,			//大窗口TOP边距
	"miniObj" : ET$Id("miniMessage"),
	"miniDrag": ET$Id("miniDrag"),
	"innerObj": ET$Id("innerObj"),
	"miniClose" : function(){},
	"miniType" : null,
	"miniFile" : null,
	"dom"	: document.documentElement ? document.documentElement : document.body,
	"showMiniMessage" : function(mFile, mType, closeButtonRun){
		clearTimeout(window.__MINIMESSAGE);
		this.miniObj.style.display = "";
		if (typeof(closeButtonRun) == "function"){
			this.miniClose = closeButtonRun;
		}
		if (!isNull(mFile)){
			this.miniFile = mFile;
		}
		if (!isNull(mType)){
			this.miniType = mType;
		}
		if (this.miniType == "big"){
			this.__big_message();
			var _top = this.dom.clientHeight / 2 - this.miniObj.offsetHeight / 2 + this.dom.scrollTop;
			var _left = this.dom.clientWidth / 2 - this.miniObj.offsetWidth  /2 + this.dom.scrollLeft;
			if (_top < 0){
				_top = 0;
			}
			if (_left < 0){
				_left = 0;
			}
			this.miniObj.style.top	= _top  + "px";
			this.miniObj.style.left	= _left  + "px";
			this.miniTop = parseInt(this.miniObj.style.top) - this.dom.scrollTop
			this.miniDrag.style.display = "";
			this.miniDrag.onmousedown = function(){
				drag(arguments[0]);
			}
		}
		else{
			this.miniType = "small";
			this.__small_message();
			this.miniObj.style.left	= this.dom.clientWidth - this.miniObj.offsetWidth + this.dom.scrollLeft  + "px";
		}
		if (!isNull(mFile)){
			this.innerObj.innerHTML = "<iframe width='100%' height='100%' scrolling='no' frameborder='0' src='" + mFile + "'></iframe>";
		}
		ET$Id("miniImage").src = "./images/minmessage/msg_min.gif";
		ET$Id("miniImage").onclick = function(){
			miniMessage.minimize();
		}
		this.onscrolling();
	},
	"__big_message" : function(){
		this.miniObj.style.width = this.minibWidth + "px";
		this.miniObj.style.height = this.minibHeight + "px";	
	},
	"__small_message" : function(){
		this.miniObj.style.width = this.minisWidth + "px";
		//this.miniObj.style.height = this.minisHeight + "px";
		var i = 2;
		window.__MINISHOW = setInterval(function(){
			var _mini_h = 26 * i;
			if (_mini_h >= 195){
				miniMessage.miniObj.style.height = "195px";
				clearInterval(window.__MINISHOW);
				return;
			}
			miniMessage.miniObj.style.height = _mini_h + "px";
			i++;
		},100);
	},
	"onscrolling" : function(){
		if (isNull(this.miniType) || this.miniType == "small" || arguments[0] == "minimize"){
			this.miniObj.style.top	= this.dom.clientHeight  - this.miniObj.offsetHeight  + this.dom.scrollTop + "px";
			this.miniObj.style.left	= this.dom.clientWidth - this.miniObj.offsetWidth   + this.dom.scrollLeft + "px";
		}
		else if (this.miniType == "big"){
			this.miniObj.style.top	= this.miniTop + this.dom.scrollTop  + "px";
			//this.miniObj.style.top	= this.dom.clientHeight / 2 - this.miniObj.offsetHeight / 2 + this.dom.scrollTop  + "px";
			//this.miniObj.style.left	= this.dom.clientWidth / 2 - this.miniObj.offsetWidth  /2 + this.dom.scrollLeft  + "px";
		}
		window.__MINIMESSAGE = setTimeout("miniMessage.onscrolling('" + arguments[0] + "')", 10);
	},
	"close" : function(){
		clearTimeout(window.__MINIMESSAGE);
		this.miniClose();
		this.reset();
	},
	"reset" : function(){
		this.miniFile = null;
		this.miniType = null;
		this.miniDrag.style.cursor = 'default';
		this.miniDrag.onmousedown = null;
		this.innerObj.innerHTML = "";
		this.miniObj.style.display = "none";
	},
	"minimize" : function(){
		clearTimeout(window.__MINIMESSAGE);
		if (this.miniType == "big"){
			this.miniDrag.style.display = "none";
			this.__mize_big_message();
			this.__mize_mouse();
		}
		else{
			this.__mize_small_message();
		}
		this.onscrolling("minimize");
		
	},
	"__mize_mouse" : function(){
		ET$Id("miniImage").src = "./images/minmessage/msg_max.gif";
		ET$Id("miniImage").onclick = function(){
			clearTimeout(window.__MINIMESSAGE);
			miniMessage.showMiniMessage();
		}
	},
	"__mize_big_message" : function(){
		with (this.miniObj.style){
			width	= this.minisWidth + "px";
			height	= "26px";
			top		= this.dom.clientHeight  - this.miniObj.offsetHeight  + this.dom.scrollTop + "px";
			left	= this.dom.clientWidth - this.miniObj.offsetWidth   + this.dom.scrollLeft + "px";
		}
	},
	"__mize_small_message" : function(){
		var i = 1;
		window.__MINISHOW = setInterval(function(){
			var _mini_h = 195 - 26 * i;
			if (_mini_h <= 26){
				miniMessage.miniObj.style.height = "26px";
				miniMessage.__mize_mouse();
				clearInterval(window.__MINISHOW);
				return;
			}
			miniMessage.miniObj.style.height = _mini_h + "px";
			i++;
		},100);
	}
}
function check_minimessage(){
	/*
	ajax("/ajax/minimessage.php?" + Math.random(), function(){
		var dom = ET$TagName("root", this.responseXML)[0];
		for (var i = 0; i < dom.childNodes.length; i++){
			if (dom.childNodes[i].nodeType == 1 && dom.childNodes[i].tagName){
				eval("var " + dom.childNodes[i].tagName + " = '" + dom.childNodes[i].firstChild.nodeValue + "'");
			}
		}
		if (result == "true"){
			new Cookie().setcookie("minimessage_" + msgid, true);
			miniMessage.showMiniMessage(msgurl, msgtype, function(){
				var d = new Date();
				var s = d.toLocaleTimeString().split(":");
				new Cookie().setcookie("minimessage_" + msgid, true, 24 * 60 * 60 - s[0] * 60 * 60 - s[1] * 60 - s[2], "/");
			});
		}
	});
	*/
	
	/* 以下为测试代码，正式的时候可以全部删除 */
	msgurl = "./minimessage/test.htm";
	msgtype= "small";//big/small
	msgid = 3912;
	result = "true";
	if (result == "true"){
		new Cookie().setcookie("minimessage_" + msgid, true);
		miniMessage.showMiniMessage(msgurl, msgtype, function(){
			var d = new Date();
			var s = d.toLocaleTimeString().split(":");
			new Cookie().setcookie("minimessage_" + msgid, true, 24 * 60 * 60 - s[0] * 60 * 60 - s[1] * 60 - s[2], "/");
		});
	}
}