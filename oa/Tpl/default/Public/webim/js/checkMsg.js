var g_blinkswitch=0;
var g_blinktitle;
var g_onlineuser="";
var g_notice_num=0;
var checkChatId;
jQuery(function () {
	setInterval(checkNewMsg,40000);
	if(CHAT_CONF_CLOSE!='1') {
		checkChatId=setInterval(checkNoOpenChat,15000);
	}
	g_blinktitle=document.title
});
function checkNoOpenChat() {
	var d=APP+"/WebimChat/checkNoOpenChat";
	var e='';
	$("#chatbar").children("div[id!=chatting]:visible").each(function (i) {
		e+='id['+i+']='+$(this).attr("id").substr(10)+'&'
	});
	$.post(d,e,function (b) {
		if(b) {
			for(var i=0;i<b.length;i++) {
				/**
				var a='&nbsp;'
				var c='<li id="'+b[i]["uid"]+'"><img class="img" src="'+b[i]['user_face']+'" alt="'+b[i]["disply_name"]+'" /><span class="name">'+b[i]["disply_name"]+'</span><span class="info">'+a+'</span></li>';
				openChat($(c)[0],true);
				**/
				openChat2(b[i]["uid"],b[i]["disply_name"],b[i]["user_face"],true);
			}
			noticeMusic();
			g_blinkid=setInterval(blinkNewChat,1000);
		}
	},"json")
}
function blinkNewChat() {
	document.title=g_blinkswitch%2 ? "○你有新的聊天消息 - "+g_blinktitle:"●你有新的聊天消息 - "+g_blinktitle;
	g_blinkswitch++;
	(g_blinkswitch>10)?stopBlinkNewMsg():''
}
function checkNewMsg() {
	var c=APP+"/WebimChat/checkMsg";
	var b=$("#online_num").html();
	var d='';
	d+="lastOnCount="+b;
	$.post(c,{
		lastOnCount:b
	},function (a) {
		if(a) {
			if(a.flag==2) {
				noticeMusic();
				g_notice_num=a.info;
				$("#notify_tab_count").show().text(g_notice_num);
				g_blinkid=setInterval(blinkNewMsg,1000)
			}else if(a.flag==3) {
				noticeMusic();
				g_onlineuser=a.info;
				$("#online_num").html(parseInt(b)+1);
				g_blinkid=setInterval(blinkOnline,1000)
			}
		}
	},"json")
}
function blinkNewMsg() {
	document.title=g_blinkswitch%2?"【　　　】 - "+g_blinktitle:"【"+g_notice_num+"条新消息】 - "+g_blinktitle;
	g_blinkswitch++;
	(g_blinkswitch>10)?stopBlinkNewMsg():''
}
function blinkOnline() {
	document.title=g_blinkswitch%2?"【　　      　】 - "+g_blinktitle:"【"+g_onlineuser+"上线了！】 - "+g_blinktitle;
	g_blinkswitch++;
	(g_blinkswitch>10)?stopBlinkNewMsg():''
}
function stopBlinkNewMsg() {
	if(g_blinkid) {
		clearInterval(g_blinkid);
		g_blinkid=0;
		g_blinkswitch=0;
		document.title=g_blinktitle
	}
}
function noticeMusic() {
	$("#play_sound").html('');
	var a='<embed height="0px" width="0px" src="'+APP_PUBLIC+'/img/sound.swf" scale="ShowAll" loop="loop" menu="menu" wmode="Window" quality="1" type="application/x-shockwave-flash"></embed>';
	$("#play_sound").html(a)
}