<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ($curtitle); ?></title>
		
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="author" content="Jay" />
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/default.css" />
				<script src="/oa/Tpl/default/Public/js/js.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/css/validator.css"></link>
<script src="/oa/Tpl/default/Public/js/formValidator.js" type="text/javascript" charset="UTF-8"></script>
<script src="/oa/Tpl/default/Public/js/formValidatorRegex.js" type="text/javascript" charset="UTF-8"></script>
<script language="javascript" src="/oa/Tpl/default/Public/js/DateTimeMask.js" type="text/javascript"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>	
	</head>

<style type="text/css">
	/*body{ background:#edf4fe}*/
	table.msginfobox{margin:0;padding:0;width:100%}
	.MsgText{width:100%;height:128px;overflow:auto}	
</style>
<script Language="JavaScript">
function CheckForm()
{
   if(document.form1.CONTENT.value=="")
   { alert("短信内容不能为空！");
     return (false);
   }

   if(document.form1.CONTENT.value.length>100)
   { alert("短信内容最多100个字！");
     return (false);
   }

   return (true);
}

function CheckSend()
{
  if(event.keyCode==10)
  {
    if(CheckForm())
       document.form1.submit();
  }
}

</script>

<form method="post" action="/index.php/Sms/msgsend" name="form1">
<table class="msginfobox">
	<thead>
		<tr>
			<th colspan="2">回复短信息</th>
		</tr>
	</thead>
	<tbody>
		<tr>
		    <td>收信人：</td>
			<td>
			<input type="hidden" name="TO_ID" value="<?php echo ($TO_ID); ?>">
            <input name="TO_NAME" class="BigStatic" readonly value="<?php echo ($TO_NAME); ?>">
			</td>
		</tr>
		<tr>
			<td colspan="2"><fieldset class="textContent">
			<textarea cols="30" name="CONTENT"  style="height:192px" wrap="on"></textarea>
			</fieldset></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="2">
			    <input type="hidden" name="SMS_ID" value="<?php echo ($SMS_ID); ?>">
				<button type="submit" name="Abutton1">发送</button>
				<button type="button" name="Abutton1" onclick="document.form1.CONTENT.value='';">清空</button>
				<button type="button" name="Abutton1" onclick="javascript:setTimeout('parent.window.oAmsg.close()', 500);">关闭</button>
			</th>
		</tr>
	</tfoot>
</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</body>
</html>