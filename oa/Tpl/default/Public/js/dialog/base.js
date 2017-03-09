document.domain = "sina.com.cn" 
String.prototype.trim = function(){
	return this.replace(/(^[ |¡¡]*)|([ |¡¡]*$)/g, "");
}
String.prototype.getQuery = function(name){
	var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	var r = this.substr(this.indexOf("\?")+1).match(reg);
	if(r!=null){
		return unescape(r[2]);
	}
	return null;
}
String.prototype.cn_length = function(){
	var i, sum;
	sum = 0;
	for(i=0; i < this.length; i++){
		sum ++;
		if (this.charCodeAt(i) > 255){
	  		sum ++;
	  	}
	}
	return sum;
}
String.prototype.cn_substring = function(len){
	var a = 0;
	var tmp = "";
	for (var i = 0; i < len; i++){
		if (this.charCodeAt(i) > 255){
			a += 2;
		}
		else{
			a++;
		}
		if(a > len){
			return tmp;
		}
		tmp += this.charAt(i); 
	}
	return tmp;
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
		cookiestr += "domain=sina.com.cn; ";
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
		document.cookie = n + "=" + null + "; omain=sina.com.cn; path=/; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
	}
}


function setCopy(_sTxt){
	try {
		clipboardData.setData('Text',_sTxt)
		return true;
	}
	catch(e){
		return false;
	}
}
function $(s){
	if(document.getElementById){
		return document.getElementById(s);
	}
	else{
		return document.all[s];
	}
}
function $n(s, o){
	if (o == null){
		return document.getElementsByName(s);
	}
	else{
		return o.getElementsByName(s);
	}
}
function $t(s, o){
	if (o == null){
		return document.getElementsByTagName(s);
	}
	else{
		return o.getElementsByTagName(s);
	}
}
function $r(s){
	for (var i = 0; i < $n(s).length; i++){
		if ($n(s)[i].checked)
			return $n(s)[i]; 
	}
}
function $$(s){
	return document.frames?document.frames[s]:$(s).contentWindow;
}
function $c(s){
	return document.createElement(s);
}
function exist(s){
	return s != null;
}
function dw(s){
	document.write(s);
}
function hidden(obj){
	obj.style.display = (obj.style.display == 'none') ? '' : 'none';
}

function getXY(obj){
	var o		= new Object();
	o.left		= 0;
	o.top		= 0;
	o.right		= 0;
	o.bottom		= 0;
	var oWidth	= obj.offsetWidth;
	var oHeight	= obj.offsetHeight;
	while(obj){
		o.left += obj.offsetLeft;
		o.top += obj.offsetTop;
		obj = obj.offsetParent;
	}
	o.right = o.left + oWidth;
	o.bottom = o.top + oHeight;
	return o;
}
function isNull(_sVal){
	return (_sVal === "" || _sVal == null || _sVal == "undefined");
}
function removeNode(s){
	if(exist(s)){
		s.removeNode?s.removeNode(true):s.parentNode.removeChild(s);
	}
}

function _ajax(){
	var req		= null;
	var method	= "get";
	var sync	= true;
	var query	= Array();
	var argu	= [];
	var parser	= null;
	var res = {responseXML:null,responseText:null};
	this.init = function(){
		if(window.ActiveXObject){
			req = new ActiveXObject("Microsoft.XMLHTTP");
			return true;
		}
		else if(window.XMLHttpRequest){
			parser = new DOMParser();
			req = new XMLHttpRequest();
			return true;
		}
		else{
			return false;
		}
	}
	this.setVal = function(n, v){
		if (typeof(n) == "object"){
			query = query.concat(n);
		}
		else{
			query.push(n + "=" + v);
		}
	}
	this.setPost = function(){
		method = "post";
	}
	this.setSync = function(){
		sync = false;
	}
	this.setArgu = function(a){
		argu = a
		if (isNull(a))
			argu = [];
	}
	this.open = function(_url, func){
		req.open(method, _url, sync);
		req.onreadystatechange = function(){
			if (req.readyState == 4 && req.status == 200)
			{
				res.responseText	= req.responseText;
				res.responseXML	= req.responseXML;
				if (!isNull(parser)){
					res.responseXML = parser.parseFromString(req.responseText,"text/xml");
				}
				eval(func).apply(res, argu);
			}
		};
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
	}
	this.send = function(){
		if (query.length){
			req.send(query.join("&"));
		}
		else{
			req.send(null);
		}
	}
}
function ajax(_url, func, argu, sync){
	var req = new _ajax();
	req.init();
	if (!isNull(sync) && isNull == false){
		req.setSync();
	}
	req.setArgu(argu);
	req.open(_url, func);
	req.send(null);
}