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
	.MsgText{width:100%;height:138px;overflow:auto}	
</style>
<script type="text/javascript">
    function iknow() {
        //parent.window.oAmsg.close();
        window.location = "/index.php/Sms/msgsubmit/SMS_ID/<?php echo ($row[SMS_ID]); ?>/DEL/0";
    }
    function msgRe(){
    	window.location="/index.php/Sms/msgbacks/SMS_ID/<?php echo ($row[SMS_ID]); ?>"
    }
    function msgDel(){
    	window.location = "/index.php/Sms/msgsubmit/SMS_ID/<?php echo ($row[SMS_ID]); ?>/DEL/1";
    }
	 function msgClose() {
     window.parent.oAmsg.close();
 }
</script>

<form method="post" action="">
<table class="msginfobox">
	<thead>
		<tr>
			<th colspan="2">共<?php echo ($count); ?>条新短信，请到 <a href="/index.php/Sms" target="icontent" onclick="msgClose()" style="color:#CC0000">短信箱</a> 查看</th>
		</tr>
	</thead>
	<tbody>
		<tr>
		    <td>短信类型：</td>
			<td><?php if($row[SMS_TYPE] == 0): ?><img src="/oa/Tpl/default/Public/images/avatar/<?php echo (getAvatar($row[FROM_ID])); ?>.gif" width="15" height="15"><?php else: ?><img src="/oa/Tpl/default/Public/images/sms_type<?php echo ($row[SMS_TYPE]); ?>.gif"><?php endif; ?><?php echo (getSmsType(is_array($row)?$row["SMS_TYPE"]:$row->SMS_TYPE)); ?></td>
		</tr>
		<tr>
		    <td>来自：</td>
			<td><u title="部门：<?php echo (getDeptname(is_array($row)?$row["DEPT_ID"]:$row->DEPT_ID)); ?>" style="cursor:hand"><?php echo (getUsername(is_array($row)?$row["FROM_ID"]:$row->FROM_ID)); ?></u></td>
		</tr>
		<tr>
		    <td>时间：</td>
			<td><?php echo (is_array($row)?$row["SEND_TIME"]:$row->SEND_TIME); ?></td>
		</tr>
		<tr>
			<td colspan="2"><div class="MsgText"><?php echo (is_array($row)?$row["CONTENT"]:$row->CONTENT); ?></div></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="2">
				<button name="Abutton1" onclick="iknow()">我知道了</button>
				<?php if($row[SMS_TYPE] < 3): ?><button name="Abutton1" onclick="msgRe()">回复</button><?php endif; ?>
				<button name="Abutton1" onclick="msgDel()">删除</button>
			</th>
		</tr>
	</tfoot>
</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</body>
</html>