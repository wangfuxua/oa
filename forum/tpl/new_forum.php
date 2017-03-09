<?
session_start();
?>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<div style="margin-left:30px; padding-top: 15px;">

<h2 class="title">
	<img src="image/forum.gif">讨论组
</h2>
<div class="tabs_header">
	<ul class="tabs">
		<li class="active"><a href="index.php"><span>讨论组首页</span></a></li>
		<?php if(in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){?><li class="null"><a href="index.php?new_forum=1">创建讨论组</a></li><?php }?>
		<?php if(in_array($_SESSION['LOGIN_UID'],$forum_config['adminid'])){?><li class="null"><a href="manage.php">讨论组管理</a></li><?php }?>
		<li style="margin-left:10px;"><form action="search.php" method="post">
主题关键字:<input type="text" size="35" name="keyword">&nbsp;<input type="submit" style="border:0px;width:68px;height:22px;background:url('image/search.gif') no-repeat;" value=""></form></li>
	</ul>
</div>
<form method="post" action="upload.php" enctype="multipart/form-data" onSubmit="return CheckForm();" name="upload">
<div id="ico_div" style="display:none;z-index:10;position:absolute;background:#fff;">
<table>
	<tr align="center"><td colspan="6"><h1>讨论组图标选择</h1></td></tr>
	 <tr align="center">
		<td><img src="ico/28.gif"></td><td><img src="ico/32.gif"></td><td><img src="ico/3.gif"></td><td><img src="ico/4.gif"></td><td><img src="ico/5.gif"></td><td><img src="ico/6.gif"></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="28" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="32" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="3" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="4" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="5" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="6" name="icon" onclick="javasrcipt:hidd(this.value);"></td>
	</tr>
	<tr align="center">
		<td><img src="ico/7.gif"></td><td><img src="ico/8.gif"></td><td><img src="ico/9.gif"></td><td><img src="ico/10.gif"></td><td><img src="ico/11.gif"></td><td><img src="ico/12.gif"></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="7" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="8" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="9" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="10" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="11" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="12" name="icon" onclick="javasrcipt:hidd(this.value);"></td>
	</tr>
	<tr align="center">
		<td><img src="ico/13.gif"></td><td><img src="ico/14.gif"></td><td><img src="ico/15.gif"></td><td><img src="ico/16.gif"></td><td><img src="ico/17.gif"></td><td><img src="ico/18.gif"></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="13" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="14" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="15" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="16" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="17" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="18" name="icon" onclick="javasrcipt:hidd(this.value);"></td>
	</tr>
	<tr align="center">
		<td><img src="ico/19.gif"></td><td><img src="ico/20.gif"></td><td><img src="ico/21.gif"></td><td><img src="ico/22.gif"></td><td><img src="ico/23.gif"></td><td><img src="ico/24.gif"></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="19" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="20" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="21" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="22" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="23" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="24" name="icon" onclick="javasrcipt:hidd(this.value);"></td>
	</tr>
	<tr align="center">
		<td><img src="ico/25.gif"></td><td><img src="ico/26.gif"></td><td><img src="ico/27.gif"></td><td><img src="ico/50.gif"></td><td><img src="ico/29.gif"></td><td><img src="ico/30.gif"></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="25" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="26" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="27" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="50" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="29" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="30" name="icon" onclick="javasrcipt:hidd(this.value);"></td>
	</tr>
	<tr align="center">
		<td><img src="ico/31.gif"></td><td><img src="ico/49.gif"></td><td><img src="ico/33.gif"></td><td><img src="ico/34.gif"></td><td><img src="ico/35.gif"></td><td><img src="ico/36.gif"></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="31" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="49" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="33" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="34" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="35" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="36" name="icon" onclick="javasrcipt:hidd(this.value);"></td>
	</tr>
	<tr align="center">
		<td><img src="ico/37.gif"></td><td><img src="ico/38.gif"></td><td><img src="ico/39.gif"></td><td><img src="ico/40.gif"></td><td><img src="ico/41.gif"></td><td><img src="ico/42.gif"></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="37" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="38" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="39" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="40" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="41" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="42" name="icon" onclick="javasrcipt:hidd(this.value);"></td>
	</tr>
	<tr align="center">
		<td><img src="ico/43.gif"></td><td><img src="ico/44.gif"></td><td><img src="ico/45.gif"></td><td><img src="ico/46.gif"></td><td><img src="ico/47.gif"></td><td><img src="ico/48.gif"></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="43" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="44" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="45" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="46" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="47" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="48" name="icon" onclick="javasrcipt:hidd(this.value);"></td>
	</tr>
	<tr align="center">
		<td><img src="ico/1.gif"></td><td><img src="ico/2.gif"></td><td><img src="ico/51.gif"></td><td></td><td></td><td></td>
	</tr>
	<tr align="center">
		<td><input type="radio" value="1" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="2" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td><input type="radio" value="51" name="icon" onclick="javasrcipt:hidd(this.value);"></td><td> </td><td> </td><td></td>
	</tr>

</table>
</div>
<table>
	<tr>
		<td colspan="2"><h1>创建讨论组</h1></td>
	</tr>
	<tr>
		<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
		<input type="hidden" name="ico" value="<?=$_REQUEST['icon']?>">
		<td>名称：</td><td><input type="text" id="new_forum" name="new_forum">(少于10字)</td>
	</tr>
	<tr><td valign="top">描述：</td><td><textarea name="announcement" cols="53" rows="6" id="announcement"></textarea>(少于30字)</td></tr>
	<tr><td valign="top">排序：</td><td><input type="text" id="fieldid" name="fieldid"></td></tr>
	<tr><td valign="top">图标：</td><td><input type="button" onclick="javascript:show();" value="点击选择系统内置图标" style="border:0px;background:#fff;color:#fc911a;"><img id="icoimg" name="icoimg" src="image/spacer.gif"><img id="previewpic" src="image/spacer.gif" onload="setpicWH(this,300,300)"></td></tr>
	<tr><td>上传图标：</td><td><input type="file" name="pic_forum" id="pic_forum" onchange="upload.previewpic.src=upload.pic_forum.value"></td></tr>
	<tr><td colspan="2">
<input type="submit"  value="创建讨论组" style="border:1px;background:#fc911a;color:#fff;"></td></tr>



</form>
</table>
<script language="javascript">
function show(){
	document.getElementById('ico_div').style.display ='block';
}
function hidd(val){
	document.getElementById('ico_div').style.display ='none';
	document.getElementById('icoimg').src="ico/" + val + ".gif";
}
function CheckForm()
{
	var new_forum = document.getElementById('new_forum').value;
	if (new_forum == "" || new_forum.length > 10)
	{
		alert("名称不能为空或大于10个汉字！");
		return (false);
	}
	if (document.getElementById('announcement').value == "" || document.getElementById('announcement').value.length> 30)
	{
		alert("描述不能为空或大于30个字符！");
		return (false);
	}
}

var flag=false;
function setpicWH(ImgD,w,h)
{
	var image=new Image();
	image.src=ImgD.src;
	if(image.width>0 && image.height>0)
	{
		flag=true;
		if(image.width/image.height>= w/h)
		{
			if(image.width>w)
			{
				ImgD.width=w;
				ImgD.height=(image.height*w)/image.width;
				ImgD.style.display="block";
			}else{
				ImgD.width=image.width;
				ImgD.height=image.height;
				ImgD.style.display="block";
			}
		}else{
			if(image.height>h)
			{
				ImgD.height=h;
				ImgD.width=(image.width*h)/image.height;
				ImgD.style.display="block";
			}else{
				ImgD.width=image.width;
				ImgD.height=image.height;
				ImgD.style.display="block";
			}
		}
	}
}
</script>