<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" >
<meta http-equiv="Content-Language" content="zh-CN" />
<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/command.css" />
<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/index.css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/js/iframe.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>

<script type="text/javascript" src="/oa/Tpl/default/Public/js/jquery-1.2.6.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/index.js" defer="defer"></script>
<title><?php echo ($indexTitle); ?></title>
</head>

<body>

<table class="frame_default" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="">
		<col width="">
	</colgroup>
	<thead>
	<tr>
		<th colspan="2">
			<div class="TopPanel">
				<div class="logo"></div>
				<div id="toolbar">
					<ul>
						<li><a href="javascript:" onclick="tabCreate('desktop','我的桌面','/index.php/Mytable/index')" title="我的桌面"><img alt="我的桌面" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_promotion.gif" width="32"><span>桌面</span></a></li>	
						<li><a href="javascript:" onclick="tabCreate('sd2','邮件','/index.php/WebMail')" title=""><img alt="邮件" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_mail.gif" width="32"><span>邮件</span></a></li>
						<li><a href="javascript:" onclick="tabCreate('sd7','个人通讯薄','/index.php/Address/privateaddress')" title=""><img alt="个人通讯薄" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_users.gif" width="32"><span>通讯</span></a></li>
						<li><a href="javascript:" title="日程安排" onclick="tabCreate('sd5','日程安排','/index.php/Calendar')"><img alt="日程安排" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_calendar.gif" width="32"><span>日程</span></a></li>
						<!--<li><a href="/index.php/" title="考勤" target="icontent"><img alt="考勤管理" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_clock.gif" width="32"><span>考勤</span></a></li>-->
						<li><a href="javascript:" onclick="tabCreate('sd8','网盘','/index.php/File/index/file_sort/2')" title="网盘"><img alt="网盘" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_folder.gif" width="32"><span>文件</span></a></li>
						<li><a href="javascript:" onclick="tabCreate('sd55','交流论坛','/index.php/Forum')" title="交流论坛"><img alt="论坛" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_comment.gif" width="32"><span>讨论</span></a></li>
						<li><a href="javascript:" onclick="tabCreate('sd9','个人设置','/index.php/Personinfo')" title="个人设置"><img alt="个人设置" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_process.gif" width="32"><span>设置</span></a></li>
						<li><a href="javascript:" onclick="tabCreate('feedback','意见反馈','/index.php/Feedback')" title="意见反馈"><img alt="意见反馈" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_note.gif" width="32"><span>反馈</span></a></li>
						<li><a href="javascript:" onclick="tabCreate('verson','版本日志','/index.php/Development')" title="版本日志"><img alt="版本日志" height="32" src="/oa/Tpl/default/Public/images/ico/toolbar_version.gif" width="32"><span>版本</span></a></li>
					</ul>
				</div>
				<div class="topinfo">
					<div class="topSearch">
						<ul>
					    <li class="topLogout"><a title="" href="/index.php/Public/logout">退出</a></li>
						<li class="topUser">
							<span id="topUserID" style="cursor:pointer" class="usOnline"><em><?php echo ($LOGIN_USER_NAME); ?></em></span>
							<?php if($LOGIN_DEPT_NAME): ?><span>部门：<?php echo ($LOGIN_DEPT_NAME); ?></span><?php endif; ?>
							<span>身份：<strong><?php echo ($USER_PRIV); ?></strong></span>

                        </li>
					    </ul>
					</div>
					<ul>
						<li class="topWeather"><span class="loading">Loading...</span><iframe style="display:none" allowTransparency="true" src=""  marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="No" id="ifrWeather"></iframe></li>
						<li id="Clock" class="topTime"></li>
					</ul>

				</div>
				<!-- topinfo END -->
			</div>
			<!-- TopPanel END -->


		</th>
	</tr>
	</thead>
	<!-- frame_head END -->
	<tbody>
	<tr class="TabBg">
		<td class="" width="5px" id="indexSearch"> 
		<form action="javascript:">
			<fieldset>
				<label for="indexSearchKey">
					<input name="indexSearchKey" type="text" >
				</label>
				<button name="indexSearchSubmit" onclick="tabCreate('search','全局搜索','/index.php/index/indexSearch/')" type="submit">搜索</button>
			</fieldset>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form> 
		</td>
		<td>
		<div id="tabsBar" class="">
			<div id="tablist">
				<div class="tabsBox">
					<ul>
						<li class="current" id="tab_desktop">
							<span onclick='activeTabs("tab_desktop")'><a id="showMain1" class="" href="/index.php/Mytable/index" target="icontent" title="">桌面</a></span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		</td>
	</tr>

	<!-- content START -->
	<tr>
		<!-- LeftPanel START-->
		<td class="LeftPanel">
		<div id="leftTab" class="">
			<ul>
				<li id="tab1">
					<a id="left1" class="TabOn" href="javascript:" onClick="showTabs('menu')" title="">
					<span>主菜单</span></a>
				</li>
				<li id="tab2">
					<a id="left2" href="/index.php/userOnline/index" onClick="showTabs('online')" title="" target="userOnline">
					<span style="">部门(在线<em id="onlineNum"><?php echo ($count); ?></em>)</span></a>
				</li>
			</ul>
		</div>

                    <div class="LeftContent">
				<!-- LeftPanel START-->
				<div id="left1_con">
					<div class="sTree">
						<script type="text/javascript">
							<!--
							var d = new sTree('d');
							d.selectedNode=1010;
							d.selectedFound=false;
							// id, pid, name, url, title, target, icon, iconOpen, open, cls
							d.add(0,-1,'','javascript:test(\'jay\')','','','','','','tree-root');
							<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if($vo[subcount] == 1): ?>d.add(<?php echo ($vo[id]); ?>,<?php echo ($vo[pid]); ?>,'<?php echo ($vo[MENU_NAME]); ?>','javascript:','','','','','','menuBg');<?php endif; ?>
								<?php if(is_array($vo['sub'])): ?><?php $i = 0;?><?php $__LIST__ = $vo['sub']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$sub): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if($sub[sub2] != ''): ?>d.add(<?php echo ($sub[id]); ?>,<?php echo ($sub[pid]); ?>,'<?php echo ($sub[FUNC_NAME]); ?>','javascript:','','','','');
								  <?php else: ?>
								  d.add(<?php echo ($sub[id]); ?>,<?php echo ($sub[pid]); ?>,'<?php echo ($sub[FUNC_NAME]); ?>','/index.php/<?php echo ($sub[FUNC_CODE]); ?>','<?php echo ($sub[FUNC_NAME]); ?>','icontent');<?php endif; ?>
								    <?php if(is_array($sub['sub2'])): ?><?php $i = 0;?><?php $__LIST__ = $sub['sub2']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$sub2): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?>d.add(<?php echo ($sub2[id]); ?>,<?php echo ($sub2[pid]); ?>,'<?php echo ($sub2[FUNC_NAME]); ?>','/index.php/<?php echo ($sub2[FUNC_CODE]); ?>','<?php echo ($sub2[FUNC_NAME]); ?>','icontent');<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
							document.write(d);
							//-->
						</script>
					</div>
					<!-- Left Content 1 END -->
				</div>
				<div id="left2_con">
					<!-- 这里是在线人员的详细内容-->
					<script type="text/javascript">
				    		iframe('','100%','100%','userOnline');
				    	</script>
				</div>

			</div>
		<!--<div id="miniBar"><span class="miniBar-close"></span></div>-->
		</td>
		<!-- LeftPanel END -->
		<!-- Main START-->
                <td id="tdmain">
                    <div class="MainWrap" id="showMain1_con">
                    <iframe id="icontent" name="icontent" frameborder="0" src="/index.php/Mytable/index" style="width:100%">浏览器不支持嵌入式框架，或被配置为不显示嵌入式框架。</iframe>
			</div>






		</td>
		<!-- Main END -->
	</tr>
	</tbody>
	<!-- content END -->
	<!-- foot START -->
	<tfoot>
	<tr>
         <td class="" id="footBar">
		</td>
		<td class="BtnInfo" colspan="2"><p id="copyright">Copyright &copy; 2009 北京爱搜信息技术有限公司</p></td>
	</tr>
	</tfoot>
	<!-- foot END -->

</table>
<div id="userStateWrap" style="display:none">
	<div id="userState">
		<ul>
			<li id="usOnline"><span class="usOnline"></span>在线</li>
			<li id="usBusy"><span class="usBusy"></span>忙碌</li>
			<li id="usLeave"><span class="usLeave"></span>离开</li>
		</ul>
	</div>
	<div class="userStateBg"></div>
	<iframe></iframe>
</div>

<div id="Msg">
	<!-- MsgBox START-->
	<div id="MsgTips" onClick="oAmsg.click()" style="display:none">
		 <img src="/oa/Tpl/default/Public/images/ico/newMsg.gif" alt="您有新的短信息" id="MsgIco"><span class="MsgNew" id="MsgNum"></span>
		<span class="MsgClose"></span>
	</div>
	<div id="MsgBox">
			<iframe id="MsgInfo" name="ifrMsgShow" frameborder="0" src="/index.php/Sms/msghtml"></iframe>
	</div>
                    <!-- MsgBox END -->
</div>
<!--iBubble-->
<div id="quicktips"><ul></ul></div>


<!--消息框/快捷对话框-->
<div id="quickbox">
	<div id="quickHead"><span></span></div>
	<div id="quickBody">
		<!-- for talk -->
		<div id="quickTalk">
			<div id="quickTalkCon"><!--<p class="gray">系统提示：请勿使用非法用于！</p>--></div>
			<div id="quickTalkBar"><p>按 Ctr + Enter 发送</p></div>
			<div id="quickTalkAction">
				<form action="javascript:">
					<textarea cols="20" rows="4" id="talkcontent"></textarea>
					<input type="hidden" id="toID" name="toUserID">
					<input type="hidden" id="toName" name="toUserName">
					<button type="submit" onClick="sendTalk()" name="sendBtn">发送</button>
				<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
			</div>
			<div id="quickTalkPannel"><ul></ul></div>
		</div>
		<!-- for talk END -->
		<!-- for msg -->
		<iframe id="quickMsg" scrolling="no"></iframe>
	</div>
	<div id="quickBg"></div>
	<!--[if IE 6]> <iframe id="quickHack"></iframe> <![endif]-->
</div>

<!--信息提示框-->
<div id="tipsBox"><ul></ul></div>
<div id="ProgressBar"><span> 数据加载中，请稍后...</span></div>
<div id="mark"></div>
</body>

</html>