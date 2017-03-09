var _suds_clickmap_js_ver_="clickmap_sample_iframe_session_ref_btn_notop_common_enum_reload:6.19";
var _suds_cmp_cp_s="";
var _suds_cmp_cp_mp_rf="http://beacon.sina.com.cn/b.gif";
var _suds_cmp_cp_n=0,_SUDS_CMP_CP_N_MAX=3,_SUDS_CMP_CP_C_MAX=1;
var _suds_cmp_c_d_u;
var _suds_cmp_c_d_c;
var _suds_cmp_c_d_s;
var _SUDS_CMP_SAMPLE_PER=100;
var _SUDS_CMP_PAGE_ID=0;
var _SUDS_CMP_PAGE_WIDTH=965;
var _SUDS_CMP_PAGE_ALIGN=2;
var _SUDS_CMP_PAGE_SCROLLEFT=0;
var _SUDS_CMP_PAGE_SCROLLTOP=0;

var _SUDS_CMP_COOKIESESSION="Apache";
var _SUDS_CMP_COOKIEUSERID="UNIPROU";

var _SUDS_CMP_CLICKDOMAINROOT="";
var _SUDS_CMP_CLICKSESSIONID="";
var _SUDS_CMP_CLICKMAPUNIPROU="";
var _SUDS_CMP_CLICKMAPPAGEREF=window.document.referrer;
var _SUDS_CMP_CLICKMAPPAGEURL=window.document.URL;

if(_SUDS_CMP_CP_C_I>=0){_SUDS_CMP_CP_C_I=_SUDS_CMP_CP_C_I;}else{var _SUDS_CMP_CP_C_I=0;}
if(""==_SUDS_CMP_CLICKMAPPAGEREF) _SUDS_CMP_CLICKMAPPAGEREF = "newpage";
var _S_SinaKey= new Array(".sina.",".51uc.");

function getScrollValue()
{
	_SUDS_CMP_PAGE_SCROLLEFT=window.document.body.scrollLeft + window.document.documentElement.scrollLeft;
	_SUDS_CMP_PAGE_SCROLLTOP=window.document.body.scrollTop  + window.document.documentElement.scrollTop;
}


function getSudsClickMapDomainRoot()
{
	if (""!=_SUDS_CMP_CLICKDOMAINROOT) return _SUDS_CMP_CLICKDOMAINROOT;
	
	_suds_cmp_domainRoot="";
	_suds_cmp_pageUrl = _SUDS_CMP_CLICKMAPPAGEURL;
	_suds_cmp_pageUrl = _suds_cmp_pageUrl.toLowerCase()
	
	pe=_suds_cmp_pageUrl.indexOf(".sina.");
	if (pe>0)
	{
		_suds_cmp_domainRoot="sina.com.cn";
	}
	else
	{
		ps=_suds_cmp_pageUrl.indexOf(".");
		if(ps>0) { ps=ps+1; }
		else { return ""; }
		
		pe=_suds_cmp_pageUrl.indexOf("/",ps);
		if(pe<0){ pe = _suds_cmp_pageUrl.length;}
		
		_suds_cmp_domainRoot = _suds_cmp_pageUrl.substring(ps, pe);
	}
	_SUDS_CMP_CLICKDOMAINROOT=_suds_cmp_domainRoot;
	return _suds_cmp_domainRoot;
}

function getSudsClickMapCookie(ckName)
{
	var start = document.cookie.indexOf(ckName+"=");
	if (-1 == start)
	{
		return "";
	}
	start = document.cookie.indexOf("=", start)+1;
	var end = document.cookie.indexOf(";", start);
	if (0 >= end)
	{
		end = document.cookie.length;
	}
	ckValue = document.cookie.substring(start, end);
	return ckValue;	
}
function setSudsClickMapCookie(ckName, ckValue, expires)
{ 
	if (ckValue != null)
	{
		_suds_cmp_domainRoot = getSudsClickMapDomainRoot();
		if (("undefined" == expires)||(null == expires))
		{
			document.cookie = ckName + "=" + ckValue + "; domain="+_suds_cmp_domainRoot+"; path=/" ;
		}
		else
		{
			var now = new Date();
			var time = now.getTime();
			time = time + 86400000 * expires;
			now.setTime(time);
			time = now.getTime();
			document.cookie = ckName + "=" + ckValue + "; domain="+_suds_cmp_domainRoot+"; expires="+now.toUTCString()+ "; path=/";
		}
	}
}

function checkSudsClickMapSessionId()
{
	ckTmp = getSudsClickMapCookie("Apache");
	if("" == ckTmp)
	{
		var now = new Date();
		ckTmp = now.getTime()+Math.random();
		setSudsClickMapCookie("Apache",ckTmp);
	}
	return ckTmp;
}

function getSudsClickMapSessionInfo()
{
	if (""==_SUDS_CMP_CLICKSESSIONID)   _SUDS_CMP_CLICKSESSIONID=checkSudsClickMapSessionId(_SUDS_CMP_COOKIESESSION);
	if (""==_SUDS_CMP_CLICKMAPUNIPROU)  _SUDS_CMP_CLICKMAPUNIPROU=getSudsClickMapCookie(_SUDS_CMP_COOKIEUSERID);
}

function randomSampleSudsClickMap()
{
	_SUDS_CMP_CP_C_I=_SUDS_CMP_CP_C_I+1;
	var r_num=Math.floor(Math.random()*100);
	if((r_num < _SUDS_CMP_SAMPLE_PER)&&(_SUDS_CMP_CP_C_I<=1))
	{
		getSudsClickMapSessionInfo();
		
		_suds_cmp_c_d_u=window.onunload;
		_suds_cmp_c_d_c=document.onclick;
		_suds_cmp_c_d_s=document.onstop;
		
		document.onclick=sudsclickmapLinkclick;
		window.onunload=sudsclickmapUnload;
		document.onstop=sudsclickmapStop;
		document.write("<div id=sudsclickdiv name=sudsclickdiv style='position:absolute;width:80;top:-300;left:400;visibility:hidden;z-index: 1'></div>");
		window.setTimeout("sudssubFrameClick()",500);
	}
	else
	{ return 0; }
}

function putSudsClickMapClick()
{
	var strSudsClickMapQuest="";
	if (""!=_suds_cmp_cp_s)
	{
		strSudsClickMapQuest=_SUDS_CMP_CLICKMAPPAGEURL+"|*|"+_suds_cmp_cp_s+"|*|"+_SUDS_CMP_CLICKSESSIONID+"|*|"+_SUDS_CMP_CLICKMAPUNIPROU+"|*|"+_SUDS_CMP_CLICKMAPPAGEREF+"|*|"+_SUDS_CMP_PAGE_ID;
		var _sl_mcsd=document.getElementById("sudsclickdiv");
		if(null!=_sl_mcsd)
		{ _sl_mcsd.innerHTML="<img src='"+_suds_cmp_cp_mp_rf+"?"+strSudsClickMapQuest+"' border='0' alt='' />";}
		if("1"!=_SUDS_CMP_CLICKMAPPAGEREF) { _SUDS_CMP_CLICKMAPPAGEREF="1"; }
		_suds_cmp_cp_s="";
		_suds_cmp_cp_n=0;
	}
}

function collectSudsClickMapClick( clickS )
{
	_suds_cmp_cp_s=_suds_cmp_cp_s+clickS;
	_suds_cmp_cp_n++;
	if(_suds_cmp_cp_n>=_SUDS_CMP_CP_C_MAX)
	{
		putSudsClickMapClick(); 
		if (_SUDS_CMP_CP_C_MAX<_SUDS_CMP_CP_N_MAX)
			_SUDS_CMP_CP_C_MAX++;
	}
}

function getSudsClickMapClickSrcUrl(s)
{
	var ps=s.indexof("http");
	if (ps<0) return "";
	var pe=s.indexof(" ",ps);
	if (pe<0) 
	{
		pe=s.indexof(">",ps);
		if (pe<0) return "";
	}
	
	return substring(ps, pe);
}

function sudsclickmapUnload()
{ if(_suds_cmp_c_d_u) {_suds_cmp_c_d_u();} putSudsClickMapClick(); }
function sudsclickmapStop()
{ if(_suds_cmp_c_d_s) {_suds_cmp_c_d_s();} putSudsClickMapClick(); }

function sudsclickmapLinkclick(e)
{
	var sLeft=0;
	var sTop=0;
	if(_suds_cmp_c_d_c) {_suds_cmp_c_d_c(e);}
	try{
		var o=event.srcElement;
		var eObj=window.event;
		sLeft=window.screenLeft;
		sTop=window.screenTop;
	}catch(ex){
		var o=e.target;
		var eObj=e;
		sLeft=window.screenX;
		sTop=window.screenY;
	}
	var MouseX="";
	var MouseY="";
	var ckTitle="";
	
	if ((o!=null)&&(o!=document)){
		if ("A" == o.tagName)
			ckTitle = "t=txt,s="+o.innerHTML+",h="+o.href;
		if(("FONT" == o.tagName)||("B" == o.tagName)||("STRONG"==o.tagName)){
			getScrollValue();
			MouseX=_SUDS_CMP_PAGE_SCROLLEFT + eObj.screenX - sLeft;
			MouseY=_SUDS_CMP_PAGE_SCROLLTOP  + eObj.screenY - sTop;

			ckTitle = "t=txt,s="+o.innerHTML+",h=";
			for(i=0;i<4;i++)			
			{
				try{
					o=o.parentElement;
					if(o==document) return ;
					if ("A" == o.tagName) break;
				}catch(ex){return ;}
			}
			if(i>=4)
			{ return; }
			else
			{ ckTitle = ckTitle+o.href; }
		}
		if ("IMG" == o.tagName) 
			ckTitle = "t=img,s=,h="+o.src ;

		if ("INPUT" == o.tagName){
			getScrollValue();
			if(("image"==o.type)||("submit"==o.type)){
				MouseX=_SUDS_CMP_PAGE_SCROLLEFT + eObj.screenX - sLeft;
				MouseY=_SUDS_CMP_PAGE_SCROLLTOP  + eObj.screenY - sTop;
				
				ckTitle = "t=submit,s=,h=";
				for(i=0;i<4;i++){
					o=o.parentElement;
					if(o==document) return ;
					if ("FORM" == o.tagName) break;
				}
				if(i>=4)
				{ return; }
				else
				{ ckTitle = ckTitle+o.action; }
			}
			else if("button"==o.type)
			{
				ckTitle = "t=button,s="+o.value+",h=name_"+o.name;
			} 
		}

		if (""!=ckTitle)
		{
			if(""==MouseX){
				getScrollValue();
				MouseX=_SUDS_CMP_PAGE_SCROLLEFT + eObj.screenX - sLeft;
				MouseY=_SUDS_CMP_PAGE_SCROLLTOP  + eObj.screenY - sTop;
			}
			if(2== _SUDS_CMP_PAGE_ALIGN){
				if (window.document.body.offsetWidth>_SUDS_CMP_PAGE_WIDTH+12)
				{
					MouseX=MouseX-Math.ceil((window.document.body.offsetWidth-_SUDS_CMP_PAGE_WIDTH)/2);
				}
			}

			if(3==_SUDS_CMP_PAGE_ALIGN){
				if (window.document.body.offsetWidth>_SUDS_CMP_PAGE_WIDTH+12)
				{MouseX=MouseX-Math.ceil((window.document.body.offsetWidth-_SUDS_CMP_PAGE_WIDTH));}
			}
			
			var now = new Date();
			var ct = now.getTime();
			ckTitle=ckTitle+",ct="+ct+",x="+MouseX+",y="+MouseY+"|";
			window.collectSudsClickMapClick(ckTitle); 
		}
	}
}

function sudsclickmapFrmLinkclick(e)
{
	var flag=false;
	var sLeft=0;
	var sTop=0;
	try{
		var frm = window.frames;
		for (var i=0; i < frm.length; i++){
			try{
				if(document.frames[i].event!=null){
					flag=true;break;
				}
			}catch(e){flag=false};
		}
		var o=document.frames[i].event.srcElement;
		var eObj=document.frames[i].event;
		sLeft=window.screenLeft;
		sTop=window.screenTop;
	}catch(ex){
		var o=e.target;
		var eObj=e;
		flag=true;
		sLeft=window.screenX;
		sTop=window.screenY;
	}

	if(flag==true){
		var MouseX="";
		var MouseY="";
		var ckTitle="";
		if ("A" == o.tagName)
			ckTitle = "t=txt,s="+o.innerHTML+",h="+o.href;
		if(("FONT" == o.tagName)||("B" == o.tagName)||("STRONG"==o.tagName)||("SPAN"==o.tagName)){
			getScrollValue();		
			MouseX=_SUDS_CMP_PAGE_SCROLLEFT+eObj.screenX - sLeft;
			MouseY=_SUDS_CMP_PAGE_SCROLLTOP+eObj.screenY - sTop;

			ckTitle = "t=txt,s="+o.innerHTML+",h=";
			for(i=0;i<4;i++){
				try{o=o.parentElement;}
				  catch(ex){return ;}
				if(o==document) return ;
				if ("A"==o.tagName) break;
			}
			if(i>=4)
			{ return; }
			else
			{ ckTitle = ckTitle+o.href; }
		}
		if ("IMG" == o.tagName) 
			ckTitle = "t=img,s=,h="+o.src ;

		if ("INPUT" == o.tagName){
			if(("image"==o.type)||("submit"==o.type)){
				getScrollValue();
				MouseX=_SUDS_CMP_PAGE_SCROLLEFT+eObj.screenX - sLeft;
				MouseY=_SUDS_CMP_PAGE_SCROLLTOP+eObj.screenY - sTop;
				
				ckTitle = "t=submit,s=,h=";
				for(i=0;i<4;i++)			
				{
					o=o.parentElement;
					if(o==document) return ;
					if ("FORM" == o.tagName) break;
				}
				if(i>=4)
				{ return; }
				else
				{ ckTitle = ckTitle+o.action; }				
			} else if("button"==o.type){
				ckTitle = "t=button,s="+o.value+",h=name_"+o.name;
			} 
		}
	
		if (""!=ckTitle){
			if(""==MouseX){
				getScrollValue();
				MouseX=_SUDS_CMP_PAGE_SCROLLEFT+eObj.screenX - sLeft;
				MouseY=_SUDS_CMP_PAGE_SCROLLTOP+eObj.screenY - sTop;
			}
			if(2== _SUDS_CMP_PAGE_ALIGN){
				if (window.document.body.offsetWidth>_SUDS_CMP_PAGE_WIDTH+12)
				{
					MouseX=MouseX-Math.ceil((window.document.body.offsetWidth-_SUDS_CMP_PAGE_WIDTH)/2);
				}
			}
			if(3==_SUDS_CMP_PAGE_ALIGN){
				if (window.document.body.offsetWidth>_SUDS_CMP_PAGE_WIDTH+12)
				{MouseX=MouseX-Math.ceil((window.document.body.offsetWidth-_SUDS_CMP_PAGE_WIDTH));}
			}
			var now = new Date();
			var ct = now.getTime();
			ckTitle=ckTitle+",ct="+ct+",x="+MouseX+",y="+MouseY+"|";
			window.collectSudsClickMapClick(ckTitle); 
		}
	}
	
}
function sudssubFrameClick()
{
	var frm = window.frames;
	for (var i=0; i < frm.length; i++){
		try{
			if(frm[i].location!=""){
				if(frm[i].document!=null){
					frm[i].document.onclick=sudsclickmapFrmLinkclick;
				}
			}
		}catch(e){}
	}
}
function _S_IsSinaPage()
{
	var strDomain=document.location.host;
	strDomain = strDomain.toLowerCase();
	for(i=0;i<_S_SinaKey.length;i++)
	{
		if(strDomain.indexOf(_S_SinaKey[i])>=0){return true;}
	}
	return false;
}
function suds_init(pid,per,pwidth,palign)
{
	 var argsLen=arguments.length;
	 var reg=new RegExp("^\\d{1,4}|[1-9]\d*\.\d*|0\.\d*[1-9]\d*$"); 

	 if(argsLen>0)
	 {
		for(var i=0;i<argsLen;i++){
			if(reg.test(arguments[i]))
			{
			   if(i==0)
			   _SUDS_CMP_PAGE_ID=arguments[i];
			   else if(i==1)
				   _SUDS_CMP_SAMPLE_PER=arguments[i];
			   else if(i==2)
				   _SUDS_CMP_PAGE_WIDTH=arguments[i];
			   else if(i==3){
				   if(arguments[i]<3)
					 _SUDS_CMP_PAGE_ALIGN=arguments[i];
			   }
			}
		}
	}
	if(_S_IsSinaPage()==true)
	{ randomSampleSudsClickMap(); }
}