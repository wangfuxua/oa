
var check_chat_flag;
var my_name;
var SEND_MSG=[];
var SENDING_ID=[];
jQuery(function(){
	
	$(window).resize(
		function(){
			$("#taskbar").hasClass("offline")||$("#taskbar").width($(document).width()-31);
		}
	).resize();
	
	/*
	$("#tasktray>div>div.trayIcon").click(
		function(e){
			e.stopPropagation();
			$("#tasktray>div>div.trayPanelWrapper").hide();
			$(this).next().show().siblings(".tab_count").hide().text(0);
			var c=$(this).parent().attr('id');
			if(c=='trayFriend'){
				checkfriend();
			}
		}
	);
	
	
	$(document).click(
		function(){
			$("#tasktray>div>div.trayPanelWrapper").hide();
		}
	);
	
	$("#tasktray>div>div.trayPanelWrapper").click(
		function(e){
			e.stopPropagation();
		}
	);
	*/
	
	$("#chatbar>div>div.trayIcon").click(
		function(){
			$("#chatbar>div>div.trayPanelWrapper").hide();
			$(this).parent().removeClass("highlight").end().nextAll(".trayPanelWrapper").show();
			var e=$(this).nextAll(".trayPanelWrapper").find(".chatContent").parent();
			(function(){
				var b=e[0];
				setTimeout(
					function(){
						b.scrollTop=b.scrollHeight;
					}
				,100);
			})();
			$(this).siblings(".tab_count").hide().text(0);
		}
	);
	
	
	$("#chatbar>div>div.trayIcon").mousedown(function(e){
		if(e.which==2){
			$(this).parents(".chatting").hide();
			return false;
		}
	}
	);
	
	
	$(".trayPanelTitle").click(function(){
				$(this).parents(".trayPanelWrapper").hide();
			}).mouseover(function(){
				$(this).addClass("hover");
			}).mouseout(function(){
				$(this).removeClass("hover");
			});
	
	$(".trayPanelTitle>a").click(function(e){
			e.stopPropagation();
		}).mouseover(function(e){
			e.stopPropagation();
			$(this).parent().removeClass("hover");
		}).mousedown(function(e){
			e.stopPropagation();
		});
	$("#taskbar div.trayPanelWrapper>div.trayIcon").mousedown(function(e){
		e.preventDefault();
		e.which==2&&$(this).parents(".chatting").hide();
		e.which==1&&$(this).parents(".trayPanelWrapper").hide();
		return false;
	}
	);
	
	$(".trayCloseTab").click(function(){
		$(this).parents(".chatting").hide();
		if(!$("#chatbar").children("div[id!=chatting]:visible").length){
			clearInterval(check_chat_flag);
			check_chat_flag=0;
		}
	});
	
	my_name=$("#my_name").val();
	
	/*checkfriend();*/
	
	$(window).unload(function(){
		var a=[];
		$(".chatting:visible>.trayPanelWrapper>.trayIcon>span>strong.name").each(function(){
			a.push("{name:\""+$(this).text()+"\",open:"+$(this).parents(".trayPanelWrapper").is(":visible")+",chatmsg:\""+$(this).parents(".trayPanelWrapper").find("textarea").val()+"\"}");
			}
		);
		$.cookie("chatlist","["+a+"]",{path:'/'});
	}
	);
}
);


function checkfriend(){
	$.post(APP+'/WebimChat/getOnlineFriends',null,
		function(f){
			if(f && f.length>0){
				$(".trayFriendList").html("");
				$(".online_num").text(f.length);
				
				for(var i=0;i<f.length;i++){
					/*if(f[i]["doing_content"]){
						var e=''+f[i]["doing_content"]+'<span>('+f[i]["doing_uTime"]+').</span>'
					}else{
						var e='&nbsp;'
					}*/
					var e='&nbsp;';
					
					var c='	<li id="'+f[i]["uid"]+'" class="online" onclick="openChat(this)">	<img class="img" src="'+f[i]['user_face']+'" alt="'+f[i]["disply_name"]+'" />	<span class="name">'+f[i]["disply_name"]+'</span>	<span class="info">'+e+'</span>	</li>';
					$(".trayFriendList").append(c);
				};
				
				$.cookie("chatlist",null);
			
				var g="";
				var d=$.cookie("chatlist")?$.cookie("chatlist"):"[]";
				$.each(eval("("+d+")"),function(b,a){
					$(".trayFriendList>li:contains("+a.name+")").click();
					a.open&&(g=a.name);
					$(".trayPanelWrapper>.trayIcon:contains("+a.name+")").parent().hide().find("textarea").val(a.chatmsg);
				});
			
				g&&$(".trayPanelWrapper>.trayIcon:contains("+g+")").parent().prevAll(".trayIcon").click();
				$.cookie("chatlist",null,{path:'/'});
		
			}else{
				
				$(".online_num").text(0);
				$(".trayFriendList").html("<center>你的好友都离线了</center>");
			}
		},
		"json"
	);
}
function openChat(e,d){
	var uid=$(e).attr("id");
	var user_name=$(".name",e).text();
	
	
	var g=$("#chat_panel"+uid);
	if(g[0]){
		if(!g.is(":visible")){
			g.find(".trayPanelTitleGray").html($(".info",e).html()).end().appendTo($("#chatbar"));
		}
	}else{
		//alert($(".chatting:visible").length);
		/*总共打开了 $(".chatting:visible").length+1 个对话框*/
		if($(".chatting:visible").length>((document.body.clientWidth-400)/150)){
			alert("您打开的聊天窗太多了，放不下了。");
			return false;
		}
		$("#chatting").clone(true).attr("id","chat_panel"+uid)
		.find(".trayPanelTitle>a,.trayIcon strong").text(user_name).attr("id","df_name"+uid).end()
		.find(".df_img").attr('src',$(".img",e).attr("src")).end()
		.find(".trayPanelTitleGray").html($(".info",e).html()).end()
		.find(".dateDivider").text(new Date().toString()).end()
		/*.find(".title_name").attr("href",APP+"/space/"+uid).end()
		.find(".title_img").attr("href",APP+"/space/"+uid).end()*/
		.find(".face_bnt").attr("id","face_bnt"+uid).attr("rel",uid).end()
		.find(".msg").attr("id","chat_msg"+uid).attr("rel",uid).end()
		.find(".bnt_send").attr("id","chat_send_msg"+uid).attr("rel",uid).end()
		.find(".chatContent").attr("id","chat_content"+uid).end()
		.find('.just_receive').attr("id","just_receive"+uid).end()
		.find('.clearChat').attr("id","clear_chat"+uid).attr("rel",uid).end()
		.find('.trayPanelWrapper').attr("id","panel_wrap"+uid).end()
		.find('.tab_count').attr("id","tab_count"+uid).end()
		.appendTo($("#chatbar")).show();
		
		
		g=$("#chat_panel"+uid);
		
		/*给每个笑脸插入按钮增加操作*/
		$("#face_bnt"+uid).click(function(){
			showFace("face_bnt"+uid, 'chat_msg'+uid);
			return false;
		});
		
		/* 控制输入框高度：当小于150高度的时候不显示滚动条 */
		$(".chatInput>textarea").scroll(function()
		{
			if($(this).height()<150)
			{
				$(this).height($(this).height()+14*(this.scrollTop!=0));
				if($(this).height()<150){
					this.scrollTop=0;
				}
			}
		}
		);
		
		/*获取聊天记录*/
		checkRecord(uid);
		
		$('#chat_msg'+uid).keydown(function enterDown(a){
			var b=$(this).val()||"";
			var c=$(this).attr("rel");
			if((a.ctrlKey&&a.which==13)){
				if(b.length){
					sendChat(c,b);
				}
				return false;
			}
		});
		

		$('#chat_send_msg'+uid).click(function(){
			var b=$('#chat_msg'+uid).val()||"";
			var c=$('#chat_msg'+uid).attr("rel");
			if(b.length){
				sendChat(c,b);
			}
			return false;
		});
		
		
		
		$("#clear_chat"+uid).click(function(){
			var b=$(this).attr("rel");
			$.post(APP+"/WebimChat/delRecord",{uid:b},function(a){
				if(a){
					$("#chat_content"+b).children().slice(1).remove();
					$("#just_receive"+b).val("2");
				}else{
					alert("清除失败,请稍后再试!");
				}
			});
		});
	};
	if(!d){
		g.show().find(".trayIcon").click();
		$("#chat_msg"+uid).focus();
	}else{
		g.show().find(".trayPanelTitle").click();
		var h=$("#tab_count"+uid).text()-0+1;
		$("#tab_count"+uid).text(h).show().parent().addClass("highlight");
		tipUpdown($("#tab_count"+uid));
	}
	check_chat_flag?1:check_chat_flag=setInterval(checkChat,4000);
}

function openChat2(uid,user_name,face,d){
	var uid=uid;
	var user_name=user_name;
	
	var g=$("#chat_panel"+uid);
	
	if(g[0]){
		if(!g.is(":visible")){
			//g.find(".trayPanelTitleGray").html($(".info",e).html()).end().appendTo($("#chatbar"));
			g.find(".trayPanelTitleGray").html("&nbsp;").end().appendTo($("#chatbar"));
		}
	}else{
		//alert($(".chatting:visible").length);
		/*总共打开了 $(".chatting:visible").length+1 个对话框*/
		if($(".chatting:visible").length>((document.body.clientWidth-400)/150)){
			alert("您打开的聊天窗太多了，放不下了。");
			return false;
		}
		$("#chatting").clone(true).attr("id","chat_panel"+uid)
		.find(".trayPanelTitle>a,.trayIcon strong").text(user_name).attr("id","df_name"+uid).end()
		.find(".df_img").attr('src',face).end()
		.find(".trayPanelTitleGray").html("&nbsp;").end()
		.find(".dateDivider").text(new Date().toString()).end()
		/*.find(".title_name").attr("href",APP+"/space/"+uid).end()
		.find(".title_img").attr("href",APP+"/space/"+uid).end()*/
		.find(".face_bnt").attr("id","face_bnt"+uid).attr("rel",uid).end()
		.find(".msg").attr("id","chat_msg"+uid).attr("rel",uid).end()
		.find(".bnt_send").attr("id","chat_send_msg"+uid).attr("rel",uid).end()
		.find(".chatContent").attr("id","chat_content"+uid).end()
		.find('.just_receive').attr("id","just_receive"+uid).end()
		.find('.clearChat').attr("id","clear_chat"+uid).attr("rel",uid).end()
		.find('.trayPanelWrapper').attr("id","panel_wrap"+uid).end()
		.find('.tab_count').attr("id","tab_count"+uid).end()
		.appendTo($("#chatbar")).show();
		
		
		g=$("#chat_panel"+uid);
		
		/*给每个笑脸插入按钮增加操作*/
		$("#face_bnt"+uid).click(function(){
			showFace("face_bnt"+uid, 'chat_msg'+uid);
			return false;
		});
		
		/* 控制输入框高度：当小于150高度的时候不显示滚动条 */
		$(".chatInput>textarea").scroll(function()
		{
			if($(this).height()<150)
			{
				$(this).height($(this).height()+14*(this.scrollTop!=0));
				if($(this).height()<150){
					this.scrollTop=0;
				}
			}
		}
		);
		
		/*获取聊天记录*/
		checkRecord(uid);
		
		$('#chat_msg'+uid).keydown(function enterDown(a){
			var b=$(this).val()||"";
			var c=$(this).attr("rel");
			if((a.ctrlKey&&a.which==13)){
				if(b.length){
					sendChat(c,b);
				}
				return false;
			}
		});
		

		$('#chat_send_msg'+uid).click(function(){
			var b=$('#chat_msg'+uid).val()||"";
			var c=$('#chat_msg'+uid).attr("rel");
			if(b.length){
				sendChat(c,b);
			}
			return false;
		});
		
		
		
		$("#clear_chat"+uid).click(function(){
			var b=$(this).attr("rel");
			$.post(APP+"/WebimChat/delRecord",{uid:b},function(a){
				if(a){
					$("#chat_content"+b).children().slice(1).remove();
					$("#just_receive"+b).val("2");
				}else{
					alert("清除失败,请稍后再试!");
				}
			});
		});
	};
	if(!d){
		g.show().find(".trayIcon").click();
		$("#chat_msg"+uid).focus();
	}else{
		g.show().find(".trayPanelTitle").click();
		var h=$("#tab_count"+uid).text()-0+1;
		$("#tab_count"+uid).text(h).show().parent().addClass("highlight");
		tipUpdown($("#tab_count"+uid));
	}
	check_chat_flag?1:check_chat_flag=setInterval(checkChat,4000);
}
function sendChat(i,b) {
	var to_user_name=$("#df_name"+i).text();
	
	var d=str2html(b);
		b=str2bq(d);
	var e=document.getElementById("chat_content"+i);
	var f=new Date();
	var g=f.getHours()+":"+f.getMinutes();
	var c=$("#just_receive"+i).val();
	if(c==1||c==2){
		var h='<h5 class="self"><span class="timeStamp">'+g+'</span>'+my_name+'	</h5><p class="p_other">'+b+'</p>';
	}else{
		var h='<p class="p_other">'+b+'</p>';
	}
	if(!SEND_MSG[i]){
		SEND_MSG[i]='';
	}
	SEND_MSG[i]+=d+"<br>";
	$("#just_receive"+i).val(0);
	
	/* 即使显示刚发送的信息 [em:2:] */
	var hh = h.replace(/(\[em:(\w+):\])/g,"<img src=\""+APP_PUBLIC+"/img/face/\$2.gif\" class=\"face\">");
	$("#chat_content"+i).append(hh);
	
	
	$("#chat_msg"+i).val("");
	$(e).parent()[0].scrollTop=$(e).parent()[0].scrollHeight;
	if(!SENDING_ID[i]){
		/*alert(SEND_MSG[i]);*/
		SENDING_ID[i]=setTimeout(function(){
			$.post(APP+"/WebimChat/sendMsg",{
				msg:SEND_MSG[i],to_uid:i,to_user_name:to_user_name,just_receive:c
			},function(a){
				if(a==1){
					
				}else if(a=2){
					$("#chat_content"+i).append('<p class="p_other">由于服务器超时,您刚才发送的"<span class="error">'+b+'"</span>未发送成功!</a>');
					$(e).parent()[0].scrollTop=$(e).parent()[0].scrollHeight;
				}
				$('#chat_msg'+i).height(60);
			});
			SENDING_ID[i]=false;
			SEND_MSG[i]='';
		},1000);
	}
}

function checkChat(){
	var l=new Array();
	var j='';
	var m;
	$("#chatbar").children("div[id!=chatting]:visible").each(function(i)
	{
		j+='id['+i+']='+$(this).attr("id").substr(10)+'&';
	}
	);
	$.post(APP+"/WebimChat/receiveMsg",j,function(a){
		if(a){
			for(var k=0;k<a.length;k++){
				m=a[k]['id'];
				var g=$("#df_name"+m).text();
				var e=document.getElementById("chat_content"+m);
				var c=$("#just_receive"+m).val();
				if(c==0||c==2){
					$("#just_receive"+m).val("1");
					var i=new Date();
					var f=i.getHours()+":"+i.getMinutes();
					var h='	<h5 class="other">	<span class="timeStamp">'+f+'</span>	<a href="javascript:void(0)">'+g+'</a></h5><p class="p_other">'+str2bq(a[k]['msg'])+'</p>';
				}else{
					h='<p class="p_other">'+str2bq(a[k]['msg'])+'</p>';
				}
				$("#chat_content"+m).append(h);
				(function(){var b=$(e).parent()[0];setTimeout(function(){b.scrollTop=b.scrollHeight;},100);})();
				if($("#panel_wrap"+m).is(":hidden"))
				{
					var d=$("#tab_count"+m).text()-0+1;
					$("#tab_count"+m).text(d).show().parent().addClass("highlight");
					tipUpdown($("#tab_count"+m));
				}
			}
		}
	}
	,"json");
}
function checkRecord(g){
	var c;
	var f=$("#df_name"+g).text();
	var d;
	var e=document.getElementById("chat_content"+g);
	$.post(APP+"/WebimChat/receiveRecord",{to_uid:g},function (a){
		if(a){
			for(var i=a.length-1;i>=0;i--){
				d=(a[i]['uid']==g)?'<a href="#">'+f+'</a>':my_name;
				from_class=(a[i]['uid']==g)?"other":"self";
				c='	<h5 class="'+from_class+'">	<span class="timeStamp">'+a[i]['dis_time']+'</span>	'+d+'</h5><p class="p_other">'+a[i]['msg'].replace(/\\\\/igm,"\\").replace(/\\\'/igm,"\'")+'</p>';
				$("#chat_content"+g).append(c);
			}(function () {
				var b=$(e).parent()[0];
				setTimeout(function () {
					b.scrollTop=b.scrollHeight;
				},100);
			})();
		}
	},"json");
}


function str2html(a){
	return a.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/ /g,"&nbsp;").replace(/\"/g,"&quot;").replace(/\'/g,"&apos;").replace(/\n/g,"<br />");
}
					
function str2bq(a){
	if(a){
	return a.replace(/\&gt;:\(/g,"<span></span><span class='s9 sm'>&gt;:(</span>").replace(/:\)/g,"<span></span><span class='s1 sm'>:)</span>").replace(/:\(/g,"<span></span><span class='s2 sm'>:(</span>").replace(/:p/g,"<span></span><span class='s3 sm'>:p</span>").replace(/:D/g,"<span></span><span class='s4 sm'>:D</span>").replace(/:o/g,"<span></span><span class='s5 sm'>:o</span>").replace(/;\)/g,"<span></span><span class='s6 sm'>;)</span>").replace(/8\)/g,"<span></span><span class='s7 sm'>8)</span>").replace(/:&apos;\(/g,"<span></span><span class='s11 sm'>:'(</span>").replace(/:-\*/g,"<span></span><span class='s14 sm'>:-*</span>").replace(/\&lt;3/g,"<span></span><span class='s15 sm'>&lt;3</span>");
}else{
	return '';	
}
}


function tipUpdown(a) {
	var c=a.css("bottom").replace(/px/,"")-0;
	var d=a[0];
	for(var i=0;i<10;i++) {
		(function () {
			var b=i;
			setTimeout(function () {
				d.style.bottom=b*1+c+"px";
			},b*20);
			setTimeout(function () {
				d.style.bottom=b*1+c+"px";
			},400-b*20);
			setTimeout(function () {
				d.style.bottom=b*1+c+"px";
			},b*20+1000);
			setTimeout(function () {
				d.style.bottom=b*1+c+"px";
			},400-b*20+1000);
			setTimeout(function () {
				d.style.bottom=b*1+c+"px";
			},b*20+2000);
			setTimeout(function () {
				d.style.bottom=b*1+c+"px";
			},400-b*20+2000);
		})();
	}
};
jQuery.cookie=function (k,d,a) {
	if(typeof(d)!='undefined') {
		a=a||{
		};
		if(d===null) {
			d='';
			a.expires=-1;
		}var g='';
		if(a.expires&&(typeof(a).expires=='number'||a.expires.toUTCString)) {
			var f;
			if(typeof(a).expires=='number') {
				f=new Date();
				f.setTime(f.getTime()+(a.expires*24*60*60*1000));
			}else {
				f=a.expires;
			}g='; expires='+f.toUTCString();
		}var b=a.path?'; path='+(a.path):'';
		var e=a.domain?'; domain='+(a.domain):'';
		var l=a.secure?'; secure':'';
		document.cookie=[k,'=',encodeURIComponent(d),g,b,e,l].join('');
	}else {
		var h=null;
		if(document.cookie&&document.cookie!='') {
			var c=document.cookie.split(';');
			for(var i=0;i<c.length;i++) {
				var j=jQuery.trim(c[i]);
				if(j.substring(0,k.length+1)==(k+'=')) {
					h=decodeURIComponent(j.substring(k.length+1));
					break;
				}
			}
		}
		return h;
	}
};


