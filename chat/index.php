<?php
header("Content-type: text/html;charset=utf-8");
include "./config.php"; 
?>
<style type="text/css">
<!--
body {
	margin:0px;
	font-size:12px;
	}
a:link { font-size: 12px; text-decoration: none;color:#2f598e;}
a:visited { font-size: 12px; text-decoration: none;color:#2f598e;}
a:hover { font-size: 12px; text-decoration: none;color:green;}
a:active { font-size: 12px; text-decoration: none;color:blue}
#box{
	height:442px;
	width:100%;
}
#t1{
	margin-top:100px;
	padding:5px;
	width:400px;
	font-size:12px;
	z-index:12;
	background:#d6dde9;
	border:#2f598e 1px solid;
}
#t1 p{font-size:20px;font-weight:bold;}

.a_button{
	border:1px solid #2e558a;background:#fff;color:#2e558a;padding:2px;height:20px;
}
-->
</style>
<div id="box">
<table id="t1" align="center" > 
	<tr><td height="12px;"></td></tr>
	<tr>
		<td colspan="2" align="center"><p>凯达OA会议室</p></td>
	</tr>
	<tr><td height="12px;"></td></tr>
	<tr>
		<td colspan="2" align="center" background="images/menubg.gif" height="24">会议室列表</td>
	</tr>
	<tr>
		<td colspan="2" align="center"></td>
	</tr>
	<tr>
		<td align="center">编号</td><td align="center">会议室名称</td>
	</tr>
<tr><td colspan="2"><div style="height:200px;overflow:auto;"><table width="100%">
<?php
		$chat_list = mysql_query("select * from `chatroom` order by `CHAT_ID`");
		$i=1;
		while($rows = mysql_fetch_array($chat_list)){
		 echo "<tr><td align=\"center\">".$i."</td><td align=\"center\"><a href=\"".$rows['ROOM_DIR']."/chat_pro.php?create_room=3&room_name=".$rows['SUBJECT']."\">".$rows['SUBJECT']."</a></td></tr>";
			$i++;
		}
?></div></table>
</td></tr>
	<tr>
		<td align="center" colspan="2"><a class="a_button" href="chat.php">公共会议室</a>&nbsp;&nbsp;&nbsp;<a class="a_button" href="chat_pro.php?create_room=1">&nbsp;创建会议室&nbsp;</a>&nbsp;&nbsp;&nbsp;<a  class="a_button" href="chat_pro.php?create_room=5">&nbsp;管理会议室&nbsp;</a></td>
	</tr>
	<tr><td height="12px;"></td></tr>
</table>
</div>