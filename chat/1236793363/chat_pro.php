<?php
header("Content-type: text/html;charset=utf-8");
include "../config.php";

/*复制文件函数*/
function xCopy($source, $destination, $child){

    if(!is_dir($source)){
    echo("源路径不存在");
    return 0;
    }
    if(!is_dir($destination)){
    mkdir($destination,0777);
    }


    $handle=dir($source);
    while($entry=$handle->read()) {
        if(($entry!=".")&&($entry!="..")){
            if(is_dir($source."/".$entry)){
                if($child)    xCopy($source."/".$entry,$destination."/".$entry,$child);
            }else{
                copy($source."/".$entry,$destination."/".$entry);
            }
        }
    }
    return true;
}

$user=isset($_GET['username'])?$_GET['username']:'';
$create_room=isset($_GET['create_room'])?$_GET['create_room']:'';
if($create_room=='1'){
?>
<script language="javascript">
function CheckForm()
{
   if (document.c_room.room_name.value=='')
   {
	  alert("会议室名不能为空");
     return true;
   }
}
</script>
<table style="font-size:12px;border:1px solid #2e558a;background:#d2dbeb;margin-top:100px;width:400px;height:160px;" align="center">
<tr><td colspan="2" align="center" height="20"><td><tr>
<tr><td colspan="2" align="center"> <font size="4"><b>创建会议室<b></font> <br><hr><td><tr>
<form  name="c_room" action="chat_pro.php?create_room=2&username=<?=$user?>" method="post">
<tr><td align="right" width="40%">会议室名：</td><td align="left"><input type="text" name="room_name" size="14" onblur="CheckForm()"></td></tr>
<tr><td align="right">密码：</td><td align="left"><input type="password" name="room_pass" size="16"></td></tr>
<tr><td colspan="2" align="center"><input type="hidden" name="room_dir" value="<?=time()?>">
<input type="submit"   value="创建会议室" style="border:1px solid #2e558a;background:#fff;color:#2e558a;padding:2px;height:20px;"></td></tr>
<input type="button"   value="返回" style="border:1px solid #2e558a;background:#fff;color:#2e558a;padding:2px;height:20px;" onclick="javascript:history.go(-1)"></form>
<tr><td colspan="2" align="center" height="20"><td><tr>
</table>
<?php
/*创建会议室*/
}elseif($create_room=='2'){
		$room_name=isset($_POST['room_name'])?$_POST['room_name']:'';
		$room_pass=isset($_POST['room_pass'])?$_POST['room_pass']:'';
		$room_dir=isset($_POST['room_dir'])?$_POST['room_dir']:'';
		if(!empty($room_name)){
			$sql=mysql_query("insert into `chatroom` (`SUBJECT`,`PASSWORD`,`ROOM_DIR`) values('".$room_name."','".md5($room_pass)."','".$room_dir."')");
				/*复制chat到指定文件*/
				$from_dir="./chat_bat";
				$to_dir="./$room_dir";
				if(xCopy($from_dir,$to_dir,1)){
?>

<table style="font-size:12px;border:1px solid #2e558a;background:#d2dbeb;margin-top:100px;width:400px;height:160px;" align="center">
<tr><td colspan="2" align="center" height="20"><td><tr>
<tr><td colspan="2" align="center"><a href="./<?=$to_dir?>/chat.php?username=<?=$user?>&room_name=<?=$room_name?>">创建成功！进入会议室</a><td><tr>
<tr><td colspan="2" align="center"><a href="chat.php?username=<?=$user?>">返回</a><td><tr>
<tr><td colspan="2" align="center" height="20"><td><tr>
</table>
<?php
	}
		}
}elseif($create_room=='3'){
	$room_name=isset($_GET['room_name'])?$_GET['room_name']:'';
?>

<table style="font-size:12px;border:1px solid #2e558a;background:#d2dbeb;margin-top:100px;width:400px;height:160px;" align="center">
<tr><td colspan="2" align="center" height="20"><td><tr>
<tr><td colspan="2" align="center"><form action="../chat_pro.php?create_room=4&username=<?=$user?>" method="post">
	请输入会议室密码：<input type="password" name="pword">
	<input type="hidden" name="room_name" value="<?=$room_name?>">
	<input type="submit" value="提交"  style="border:1px solid #2e558a;background:#fff;color:#2e558a;padding:2px;height:20px;"><input type="button"   value="返回" style="border:1px solid #2e558a;background:#fff;color:#2e558a;padding:2px;height:20px;" onclick="javascript:history.go(-1)">
	</form><td><tr>
<tr><td colspan="2" align="center" height="20"><td><tr>
</table>

<?php
}elseif($create_room=='4'){
	$room_password=isset($_POST['pword'])?$_POST['pword']:'';
	$room_pass=md5($room_password);
	$room_name=isset($_POST['room_name'])?$_POST['room_name']:'';
	$pass_sql=mysql_query("select * from `chatroom` where `PASSWORD`='".$room_pass."' and `SUBJECT`='".$room_name."'");
	if(mysql_num_rows($pass_sql)>0){
		while($rows=mysql_fetch_array($pass_sql)){
			//echo "<a href=\"".$rows['ROOM_DIR']."/chat.php?create_room=3&username=$user&room_name=".$rows['SUBJECT']."\">".$rows['SUBJECT']."</a>";
		echo "<meta content=\"1;url=".$rows['ROOM_DIR']."/chat.php?create_room=3&username=$user&room_name=".$rows['SUBJECT']."\" http-equiv=\"refresh\" />";
		}
	}else{
?>
<table style="font-size:12px;border:1px solid #2e558a;background:#d2dbeb;margin-top:100px;width:400px;height:160px;" align="center">
<tr><td colspan="2" align="center" height="20"><td><tr>
<tr><td colspan="2" align="center"><a href="javascript:history.go(-1)">密码错误</a><td><tr>
<tr><td colspan="2" align="center" height="20"><td><tr>
</table>
<?
	}

}elseif($create_room=='5'){
	$user_priv=mysql_query("select * from `user` where `USER_NAME`='$user'");
	$chat_list = mysql_query("select * from `chatroom` order by `CHAT_ID`");
	 while($rows=mysql_fetch_array($user_priv)){
		 if($rows['USER_PRIV']=='1'){
				echo "<table style=\"font-size:12px;border:1px solid #2e558a;background:#d2dbeb;margin-top:100px;width:400px; \" align=\"center\"><tr><td align=\"center\" colspan=\"2\">会议室管理<br><hr></td></tr><tr><td align=center>会议室名</td><td align=center>操作</td></tr>";
			while($rows2 = mysql_fetch_array($chat_list)){
				echo "<tr><td align=center><a href=\"".$rows2['ROOM_DIR']."/chat_pro.php?create_room=3&username=$user&room_name=".$rows2['SUBJECT']."\"><font color=\"green\">".$rows2['SUBJECT']."</font></a></td><td align=center><a href=\"chat_pro.php?room_id=".$rows2['CHAT_ID']."&create_room=6&username=".$user."\" onclick=\"javascipt:return(confirm('您确定要删除这个讨论组？'))\"><font color=\"red\">删除</font></a></td></tr>";
}?>
<tr><td align="center" colspan="2"><hr> <input type="button"   value="返回" style="border:1px solid #2e558a;background:#fff;color:#2e558a;padding:2px;height:20px;" onclick="javascript:history.go(-1)"></td></tr>
<?
		 }else{
?>
			<table style="font-size:12px;border:1px solid #2e558a;background:#d2dbeb;margin-top:100px;width:400px;height:160px;" align="center">
			<tr><td colspan="2" align="center" height="20"><td><tr>
			<tr><td colspan="2" align="center"><a href="javascript:history.go(-1)">您没有这个权限！点击返回</a><td><tr>
			<tr><td colspan="2" align="center" height="20"><td><tr>
			</table>
<?php
		 }
	 }
}elseif($create_room=='6'){
	 $room_id=isset($_GET['room_id'])?$_GET['room_id']:'';
	 if($room_id!=''){
		 $chatroom_del=mysql_query("delete from `chatroom` where `CHAT_ID`='".$room_id."'");
		 if($chatroom_del){
?>
<table style="font-size:12px;border:1px solid #2e558a;background:#d2dbeb;margin-top:100px;width:400px;height:160px;" align="center">
<tr><td colspan="2" align="center" height="20"><td><tr>
<tr><td colspan="2" align="center"><a href="chat.php?create_room=5&username=<?=$user?>">删除成功！</a><td><tr>
<tr><td colspan="2" align="center" height="20"><td><tr>
</table>
<?php
		 }
	}

}else{
		/*会议室列表*/
		$chat_list = mysql_query("select * from `chatroom` order by `CHAT_ID`");
		 echo "<table><tr><td>会议室列表</td></tr><tr><td><a href=\"chatroom/chat.php?username=$user\">进入开放会议室</a></td></tr>";
		while($rows = mysql_fetch_array($chat_list)){
		 echo "<tr><td><a href=\"chat.php?create_room=3&username=$user&room_name=".$rows['SUBJECT']."\">".$rows['SUBJECT']."</a></td></tr>";
		}
		 echo "<tr><td><a href=\"?create_room=1\">创建会议室</a></td></tr></table>";
}


?>