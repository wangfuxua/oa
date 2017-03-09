/*
 * KDOA Index JavaScript v1.1.1
 *
 * http://www.7e73.com
 * 
 * Copyright (c) 2009 7e73.com All rights reserved.
 *
 * Date:2009-02-16 0:42
 * Revision:1
 * Author: Jay
 *
 */
 
var intervalRefresh = 0;
var intervalTime = 0;
var tipsTitleSwitch = 0;
var tipsTitleID = 0;
var tipsTitle;

function setTab(name, cursel, n) {
    for (i = 1; i <= n; i++) {
        var tabs = document.getElementById(name + i);
        var tabsCon = document.getElementById(name + i + "_con");
        tabs.className = i == cursel ? "TabOn" : "";
        tabsCon.style.display = i == cursel ? "block" : "none";
    }
}
//////////////////////////////////////////

// 信息窗口
function openTips(type, id, obj) {
    var tips = $("li." + type);
    $("#quickbox").show("normal").css({ top: tips.offset().top, left: tips.offset().left });
    $("#quickHead span").attr("class", "").addClass(type).text(tips.find("em").text());
    if (type == "tipsico_msg") {
        $("#quickTalk").hide();
        $("#quickMsg").show().attr("src", "/index.php/Sms/msghtml");
    } else if (type == "tipsico_talk") {
        $("#quickMsg").hide();
        $("#quickTalk").show();
		var tid = $("#quicktips ul .tipsico_talk").attr("id");
        var tname = tips.find("em").text();
		activeTalk(tid, tname);
        tConSroll();
    } else { 
    
    }
}
function closeTips(obj) { $("#quickbox").hide("normal"); }
// 检测/添加信息提示
function checkTips(type,id, tit,con,num) {
    var type = arguments[0]; var id = arguments[1]; var tit, name = arguments[2]; var con = arguments[3]; var num = arguments[4];
    
    var talkHtml = '<li class="tipsico_talk" id=' + id + '><a title="" href="javascript:openTips(\'tipsico_talk\',\'' + id + '\',\''+name+'\')" hidefocus="hidefocus">与<em>' + name + '</em>对话中</a></li>';
    var msgHtml = '<li class="tipsico_msg" id=' + id + '><a title="" href="javascript:openTips(\'tipsico_msg\',\'' + id + '\',' + num + ')" hidefocus="hidefocus">有<em>(' + num + ')</em>条新信息</a></li>';
    var box = $("#quicktips ul");
    
    var msgObj = box.find(".tipsico_msg"); //$("li.tipsico_msg");
    var talkObj = box.find(".tipsico_talk"); //$("li.tipsico_talk");

    if (type == "msg") {
        if (msgObj.length > 0 && num > 0) {
			var tips = $("#quicktips li.tipsico_msg");
            msgObj.find("em").text("(" + num + ")");
			$("#quickHead span.tipsico_msg").text(tips.text());
        } else if (num > 0) {
            box.append(msgHtml);
         } else {
            msgObj.remove();
        }
    } else if (type == "talk") {
        if (talkObj.length > 0 && con != "" && tit !=null) {
            talkObj.find("em").text(name).end()
                        .attr("id",id);           
        } else if (con != "" && tit !=null) {
            box.append(talkHtml);
        } else {
            talkObj.remove();
        }
    } else { 
        
    }
}

/* 短信息 ////////////////////////////////////////// */

/* Check MSG End */

function weather() { $("#ifrWeather").attr("src","http://m.weather.com.cn/m/p6/weather1.htm");}

/* WebIM /////////////////////////////////////////// 

1.激活时加载数据，并发送激活id，php将除此id外将所有用户设为未读
2.发送后先把自己发的内容写入DOM，然后入库，更新时再显示最新的，读取新消息时，如果当前处于激活则直接插入最新的记录
3.Cookie存储
*/
///////////////////////////
function talkOnclick(tid, name) {
    checkTips("talk", tid, name);
    openTips("tipsico_talk", tid);
    checkTalkUser(tid,name);
	activeTalk(tid, name);
	tConSroll();
}
var oAmsg = new Object;
oAmsg.close = function() {
    closeTips();
};
function RefreshMsg() {
    Refresh();
}

///////////////////////////
var KIM = function() {
    this.b = $("#quickTalk");
};
// 添加聊天对象
function creatTalk(tid, tName) {
    var box = $("#quickTalkPannel ul");
    var user = '<li id="' + tid + '" class="" onclick=activeTalk(\'' + tid + '\',\''+tName+'\')><span onclick=removeTalk("'+tid+'")></span><em>' + tName + '</em></li>';
    box.append(user);
}

// 检查聊天对象是否存在存在则激活，否则添加后再激活
function checkTalkUser(tid, name) {
    var b = $("#quickTalkPannel ul");
    if (b.find("#" + tid).length>0) {
        // 如果存在则激活
        //alert("...");
        activeTalk(tid,name);
    } else {
    // 否则添加后再激活
        creatTalk(tid, name);
        activeTalk(tid, name);
    }
    // 设置滚动条滚动到相应对象的位置
    var a = b.find("li").index($("#" + tid));
    b.parent().scrollTop(a * 40);
}
// 激活聊天对象
function activeTalk(tid,name) {
    var user = $("#quickTalkPannel ul");
    var tips = $("#quicktips li.tipsico_talk");
    //var box = $("#quickTalkCon");
    user.find("li").removeClass("userOn").end()
          .find("#" + tid).attr("class", "").addClass("userOn").find("em").text(name);
    // 显示与该用户的聊天内容
    loadTalkCon(tid);
    $("#toID").val(tid);
    $("#toName").val(name);
    //$("#talkcontent").val(tid);
    $("#quickHead span").text(name);
    checkTips("talk", tid, name);
	tConSroll();
}
// 删除聊天对象
function removeTalk(tid) {
    $("#quickTalkPannel #" + tid).remove();
    if ($("#quickTalkPannel li").length == 0) {
        $("li.tipsico_talk").remove(); 
        closeTips();
    }
}

// 发送聊天内容
function sendTalk(tid,name,tCon) {
    var box = $("#talkcontent");
    var user = $("#toID").val();
    var name = $("#toName").val();
    addTalkCon(user,name, box.val(), 1);
    postTalk(user,name,box.val());
    box.val("");
    tConSroll();
}
// 向php中发送聊天数据
function postTalk(tid,name,tcon,back) {
    var url = "/index.php/WebimChat/sendMsg";
    var date = {msg: tcon, to_user_name: tid, send: 1};
    $.post(url, date, function(d) {
        if (d.flag == 1) {
            //成功
        } else if (d.flag == 0) {
        $("#quickTalkCon").append("<p class='gray'>由于服务器超时,您刚才发送的<span> "+tcon+" </span>未发送成功!</p>")
        }
    }, "json");
}
// 返回聊天记录
function loadTalkCon(tid) {
    var url = "/index.php/WebimChat/sendMsg";
    $("#quickTalkCon").load(url, { to_user_name: tid }, tConSroll());
}
// 添加对话的内容 如果type=1则是添加自己的对话内容
function addTalkCon(tid,name,con,type) {
    var box = $("#quickTalkCon");
    var date = new Date();
    var talkTime = date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    if (type == 1) {
        box.append("<p><span class='green'>我</span> <span class='talkTime'>" + talkTime + "</span><br />" + con + "</p>");
        // 写入数据库
    } else {
        box.append("<p><span class='blue'>" + name + "</span> <span class='talkTime'>" + talkTime + "</span><br />" + con + "</p>");
    }
    tConSroll();
}

// 滚动条
function tConSroll() {
    var b=$("#quickTalkCon")[0];
    setTimeout(function() {
	    b.scrollTop = b.scrollHeight;
	}, 100);
}
// 有新消息 if 激活状态则把最新信息写到聊天窗口内；else 显示新信息的提示
function hasNewTalk(tid,name,tcon) {
    var b = $("#quickTalkPannel ul");
    if (b.find("li.userOn").is("#" + tid)) {
        addTalkCon(tid,name, tcon);
    } else if (b.find("li").is("#" + tid)) { 
        b.find("#" + tid).attr("class","").addClass("newTalk");
    } else {
        creatTalk(tid,name);
    }
	tipsTitleID = setInterval(tipsTitleNewTalk, 5000);
}

// 消息提示效果：title变化
function tipsTitleNewTalk() {
    document.title = tipsTitleSwitch % 2 ? "○你有新的聊天消息 - " + tipsTitle : "●你有新的聊天消息 - " + tipsTitle;
    tipsTitleSwitch++;
    (tipsTitleSwitch > 10) ? tipsTitleStopNewTalk() : ''
}
function tipsTitleStopNewTalk() {
    if (tipsTitleID) {
        clearInterval(tipsTitleID);
        tipsTitleID = 0;
        tipsTitleSwitch = 0;
        document.title = tipsTitle;
    }
}
/* ////////////////////////////////////////////////////////// */

$(document).ready(function() {
    /* 1 */$("#talkcontent").keyup(function(e) { var ie = navigator.appName == "Microsoft Internet Explorer" ? true : false; if (ie) { if (event.ctrlKey && window.event.keyCode == 13) { sendTalk(); } } else { if (isKeyTrigger(e, 13, true)) { sendTalk(); } } });

    /* 2 */$("body,iframe").contents().click(function() {
        var a = $(this).attr("id");
        if (a != "quickbox" && a !="quicktips" && a !="onlineTree") {
            closeTips();
        }
    });
    /* 3 */$("#topUserID").mouseover(function() { userState(); });
    /* 4 */$("#showMenu").click(function() { elastic(); });
    /* 5 */$.post("/index.php/index/setStatus", { "status": "" }, function(data) {
        if (data == "" || data == 0) {
            $("#topUserID").addClass("usOnline");
        } else {
            $("#topUserID").attr("class", "").addClass(data);
        }
    }, "string");
    /* 6 */$("#btn_refresh").click(function() { document.icontent.location.reload(); });
    /* 7 */setFrame();
	Refresh();
    /* 8 */intervalRefresh = setInterval("Refresh()", 15000);
	
    /* 9 */
    /*10*/$("#quickHead").click(function() { closeTips(); });
    /*11*/ setTimeout("time_weather()",5000);

    /*12*/tipsTitle = document.title;
});

$(window).unload(function() {
    clearTimeout(intervalRefresh);
    clearInterval(intervalRefresh);   
    /* 补充：清除匿名timeout方法：clearTimeout(setTimeout("0") - 1); */
});
//让人每次改变页面窗口的大小时很郁闷的方法:
$(window).resize(function() { setFrame(); });
/* WebIM END */////////////////////////////
function time_weather(){
intervalTime = setInterval(function() {
        $("#Clock").text(new Date().toLocaleString() + ' 星期' + '日一二三四五六'.charAt(new Date().getDay()));
    }, 1000);
weather();
}
/* 用户状态选择 //////////////////////////////////////// */

function userState() {
    var wrap = $("#userStateWrap");
    var user = $("#topUserID");
    var left = user.offset().left - 5;
    var url = "/index.php/index/setStatus";

    wrap.show().css({ "margin-left": left });
    user.mouseout(function() { wrap.mouseover(function() { wrap.show(); }).mouseout(function() { wrap.hide(); }); wrap.hide(); });
    $("#userState li").click(function() {
        user.attr("class", "").addClass($(this).children("span").attr("class"));
        $.post(url, { "status": $(this).attr("id") }, function() { wrap.hide(); }, "json");
        wrap.hide();
    });
}

/* 关闭/展开左侧菜单////////////// */

function elastic() {
    var tab = $("#leftTab ul li");
    var con = $(".LeftContent")
    var foot = $("#footBar ul");
    var btn=$("#showMenu span");
    var btncss = $("#showMenu span").attr("class");
    var column = $(".LeftPanel");
    var winWidth = $(window).width();

    if (btn.attr("class") != "showleft") {
        foot.hide();
        con.css("display", "none");
        column.css("background", "#EDF4FE");
        $("#tab1,#tab2").hide();
        btn.attr("class", "").addClass("showleft");
        $("#mainDesktop").width(winWidth - 30);
    } else {
        foot.show();
        con.css("display", "block");
        column.css("background", "#FFF");
        $("#tab1,#tab2").show();
        btn.removeClass("showleft").addClass("closeleft");
        $("#mainDesktop").width(winWidth - 200);
    }   
}
// 2009-02-12 ////////////////////////////////////////
/* Refresh */
function Refresh() {
    // DateTime
    $.post("/index.php/Refresh/index", { test: "" }, function(s) {
		    //$("#left3_con").css("word-break", "break-all").text(s);
        //$("#left3_con").css("word-break", "break-all").text(s.msg.mNum);
		if (s.msg.tCon != "" && s.msg.tCon !=null && s.msg.tCon!=0) {
			checkTips("talk", s.msg.tId, s.msg.tName, s.msg.tCon);
           hasNewTalk(s.msg.tId, s.msg.tName, s.msg.tCon);
        }
		//if (s.msg.mNum != "" && s.msg.mNum !=null && s.msg.mNum !=0 ){}
		checkTips("msg", s.msg.mId, "", s.msg.mCon, s.msg.mNum);
		
    }, "json");
}

/* 设置中间iframe宽度 */
function setFrame() {
    var t = $("#tdmain");
    var f = $("#mainDesktop");
    f.width(t.width() - 10);
}

/* 检查用户名称函数 /////////////////////////// */

function checkUserName(id) {
    //$("").contents().find("")
    var userName;
    return userName;
}

/* 检查用户名称函数 ENd /////////////////////// */

/* Cookie 函数 //////////////////////////////////// */
function addCookie(name, value, expireHours) {
    var cookieString = name + "=" + escape(value);
    //判断是否设置过期时间
    if (expireHours > 0) {
        var date = new Date();
        date.setTime(date.getTime + expireHours * 3600 * 1000);
        cookieString = cookieString + "; expire=" + date.toGMTString();
    }
    document.cookie = cookieString;   
}
function getCookie(name) {
    var strCookie = document.cookie;
    var arrCookie = strCookie.split("; ");
    for (var i = 0; i < arrCookie.length; i++) {
        var arr = arrCookie[i].split("=");
        if (arr[0] == name) return arr[1];
    }
    return "";
}
function delCookie(name) {
    var date = new Date();
    date.setTime(date.getTime() - 10000);
    document.cookie = name + "=v; expire=" + date.toGMTString();
}

/* Cookie 函数 End //////////////////////////////////// */
