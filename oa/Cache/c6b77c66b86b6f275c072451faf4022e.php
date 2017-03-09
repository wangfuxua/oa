<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ($curtitle); ?></title>
		
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="author" content="Jay" />
		
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/style/default/css/kdstyle.css" />
        <script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KD.js" ></script>
        <script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js" ></script>		
	</head>
<script language="JavaScript">
function CheckForm()
{
   if (document.form1.ATTACHMENT.value!="")
   {
     var file_temp=document.form1.ATTACHMENT.value,ext_name;
     var Pos;
     Pos=file_temp.lastIndexOf(".")+1;
     ext_name=file_temp.substring(Pos,file_temp.length);
     if(ext_name!="gif"&&ext_name!="GIF")
     {
     	  alert("头像文件只能是gif!");
     	  return false;
     }     
     document.form1.ATTACHMENT_NAME.value="<?php echo (is_array($row)?$row["USER_ID"]:$row->USER_ID); ?>."+ext_name;
   }

  form1.submit();
}
function delete_avatar()
{
 msg='确认要删除头象吗？';
 if(window.confirm(msg))
 {
  URL="/index.php/Personinfo/avatardelete";
  window.location=URL;
 }
}

</script>

<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "个人设置",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 2,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "个人资料", Url: "/index.php/Personinfo/index", Cls: "", Icon: "", IconCls: "ico ico-userInfo" },
            { Title: "个性设置", Url: "/index.php/Personinfo/set", Cls: "", IconCls: "ico ico-userCustom" },
            { Title: "个人网址", Url: "/index.php/Personinfo/url", Cls: "", IconCls: "ico ico-userUrl" },
            { Title: "修改密码", Url: "/index.php/Personinfo/pass", Cls: "", IconCls: "ico ico-userPassword" }
        ]
    });		
		
		var _imgInput=$("[name='AVATAR']:checked");
		var _userImg=$("img[name^='user_']");
		$("img[name='user_"+_imgInput.val()+"']").addClass("checked");
});


    $(window).resize(function() { 
        setDomHeight("main", 56); 
    
    });
function checkImg(val){
	var _img=$("img[name='user_"+val+"']");
	var _in=$("input[name='AVATAR']").filter("[value='"+val+"']");
	_in.end().removeAttr("checked");
	_in.attr("checked","checked");
	//alert(_img.length);
	$("img[name^='user_']").removeClass("checked");
	_img.addClass("checked");
}
</script>
<style type="text/css">
	.userImg { margin:5px 10px 5px 10px}
	.userImg li { width:50px; height:50px; float:left; display:inline; margin:5px 5px 0 5px}
	.userImg img { width:50px; height:50px; border:1px #FFF solid; cursor:pointer}
	.userImg img.checked {border:1px #ffd37f solid; background:#fff2d4}
	.userImg input {display:none}
	.userImg li.clearfix { clear:left; display:block; height:1%; width:100%; line-height:0px; font-size:0px; float:none}
</style>
<body>

<div class="person KDStyle" id="main">
<form enctype="multipart/form-data" action="/index.php/Personinfo/setsubmit"  method="post" name="form1">
 <table>
   			<colgroup>
				<col width="100"></col>
				<col></col>
			</colgroup>
			<!--
  <tr>
      <td> 昵称（用于讨论区交流等）：</td>
      <td>
        <input type="text" name="NICK_NAME" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["NICK_NAME"]:$row->NICK_NAME); ?>">
      </td>
    </tr>
    -->
<?php
if($row[AVATAR]!=$LOGIN_USER_ID)
{
?>
    <tr>
      <th valign="top"> 头像：</th>
      <td width="600">
	  <ul class="userImg">
<?php
      for($I=1;$I<=10;$I++)
      {
?>
<li>
<input type="radio" name="AVATAR" value="<?php echo $I?>" class="radio" style="display:none" <?php if($I==$row[AVATAR])echo "checked='checked'"; ?>>
<img src="/oa/Tpl/default/Public/images/avatar/<?php echo $I?>.gif" onClick="checkImg('<?php echo $I?>')" name="user_<?php echo $I?>">
</li>
<?php
        if($I%5==0)
           echo "<li class='clearfix'>&nbsp; </li>";
      }
?>      </ul>
      </td>
    </tr>
<?php
}
if($LOGIN_AVATAR!=$LOGIN_USER_ID)
{
?>
    <tr>
      <th valign="top">自定义头像：</th>
      <td> 
        <input type="file" name="ATTACHMENT" size="30" class="BigInput" title="选择附件文件" value="<?php echo ($ATTACHMENT); ?>"><br>
        注：头像文件只能是gif格式，建议头像大小不超过50*50像素。
      </td>
    </tr>
<?php
}
else
{
?>
    <tr>
      <td valign="top">自定义头像：</td>
      <td>
        <img src="/oa/Tpl/default/Public/images/avatar/<?php echo ($LOGIN_AVATAR); ?>.gif" align="absmiddle">&nbsp;&nbsp;&nbsp;
        <button  onclick="javascript:delete_avatar();">删除</button>
      </td>
    </tr>
    <tr>
      <td valign="top">更改自定义头像：</td>
      <td> 
        <input type="file" name="ATTACHMENT" size="22" class="BigInput" title="选择附件文件" value="<?php echo ($ATTACHMENT); ?>"><br>
        注：头像文件只能是gif格式，建议头像大小不超过30*40像素。
      </td>
    </tr>
<?php
}
?>
<!--
    <tr>
      <td valign="top">讨论区签名档：</td>
      <td> 
        <textarea cols=35 name="BBS_SIGNATURE" rows=3 class="BigInput" wrap="off"><?php echo (is_array($row)?$row["BBS_SIGNATURE"]:$row->BBS_SIGNATURE); ?></textarea>
      </td>
    </tr>
    <tr>
      <td>IE浏览器工具栏：</td>
      <td>
        <select name="MENU_TYPE">
          <option value="1" <?php if($row[MENU_TYPE] == 1): ?>selected<?php endif; ?>>显示</option>
          <option value="2" <?php if($row[MENU_TYPE] == 2): ?>selected<?php endif; ?>>不显示</option>
        </select>
        需重新登录才能生效
      </td>
    </tr>
    <tr>
      <td>左侧菜单自动隐藏：</td>
      <td>
        <select name="MENU_HIDE">
          <option value="1" <?php if($row[MENU_HIDE] == 1): ?>selected<?php endif; ?>>是</option>
          <option value="2" <?php if($row[MENU_HIDE] == 2): ?>selected<?php endif; ?>>否</option>
        </select>
        需重新登录才能生效
      </td>
    </tr>
    <tr>
      <td>短信息提醒窗口弹出方式：</td>
      <td>
        <select name="SMS_ON">
          <option value="1" <?php if($row[SMS_ON] == 1): ?>selected<?php endif; ?>>自动</option>
          <option value="0" <?php if($row[SMS_ON] == 0): ?>selected<?php endif; ?>>手动</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>短信息提示音：</td>
      <td>
        <select name="CALL_SOUND">
          <option value="1" <?php if($row[CALL_SOUND] == 1): ?>selected<?php endif; ?>>语音1</option>
          <option value="8" <?php if($row[CALL_SOUND] == 8): ?>selected<?php endif; ?>>语音2</option>
          <option value="2" <?php if($row[CALL_SOUND] == 2): ?>selected<?php endif; ?>>激光</option>
          <option value="3" <?php if($row[CALL_SOUND] == 3): ?>selected<?php endif; ?>>水滴</option>
          <option value="4" <?php if($row[CALL_SOUND] == 4): ?>selected<?php endif; ?>>手机</option>
          <option value="5" <?php if($row[CALL_SOUND] == 5): ?>selected<?php endif; ?>>电话</option>
          <option value="6" <?php if($row[CALL_SOUND] == 6): ?>selected<?php endif; ?>>鸡叫</option>
          <option value="7" <?php if($row[CALL_SOUND] == 7): ?>selected<?php endif; ?>>OICQ</option>
          <option value="0" <?php if($row[CALL_SOUND] == 0): ?>selected<?php endif; ?>>无</option>
        </select>
      </td>
    </tr>

    <tr>
      <td>登录后显示的左侧面板：</td>
      <td>
        <select name="PANEL">
          <option value="1" <?php if($row[PANEL] == 1): ?>selected<?php endif; ?>>主菜单</option>
          <option value="2" <?php if($row[PANEL] == 2): ?>selected<?php endif; ?>>在线人员</option>
          <option value="3" <?php if($row[PANEL] == 3): ?>selected<?php endif; ?>>全部人员</option>
          <option value="4" <?php if($row[PANEL] == 4): ?>selected<?php endif; ?>>快捷组</option>
          <option value="5" <?php if($row[PANEL] == 5): ?>selected<?php endif; ?>>短信箱</option>
          <option value="6" <?php if($row[PANEL] == 6): ?>selected<?php endif; ?>>收藏夹</option>
        </select>
        需重新登录才能生效
      </td>
    </tr>
    -->
    <tfoot>
      <th colspan="2" nowrap>
        <input type="hidden" value="<?php echo ($ATTACHMENT_NAME); ?>" name="ATTACHMENT_NAME">
        <button type="button" value="保存修改" class="btnFnt" onClick="CheckForm();">保存修改</button>
      </th>
    </tfoot>
  </table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>

</body>
</html>