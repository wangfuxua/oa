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
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/js.js" ></script>

<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/css/validator.css" />
<script src="/oa/Tpl/default/Public/js/formValidator.js" type="text/javascript" charset="UTF-8"></script>
<script src="/oa/Tpl/default/Public/js/formValidatorRegex.js" type="text/javascript" charset="UTF-8"></script>
<script language="javascript" src="/oa/Tpl/default/Public/js/DateTimeMask.js" type="text/javascript"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>	
	</head>

<body>
	<div class="KDStyle" id="main">
		<table>
		   <colgroup>
		     <col width="150"></col>
		     <col></col>
		   </colgroup>
		   <thead><tr><th colspan="2"></th></tr></thead>
			<tr>
				<td class="dm_zanal">发信时间：</td>
				<td><?php echo (is_array($row)?$row["SEND_TIME"]:$row->SEND_TIME); ?></td>
			</tr>
			<tr>
				<td class="dm_zanal"><?php echo (is_array($row)?$row["USER_DESC"]:$row->USER_DESC); ?></td>
				<td><?php echo (is_array($row)?$row["USER_NAME"]:$row->USER_NAME); ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo (is_array($row)?$row["CONTENT"]:$row->CONTENT); ?></td>
			</tr>
			<tfoot>
			<tr>
				<th colspan="2">
                 <button type="button" value="关闭" class="btnFnt" onClick="javascript:window.close();">关闭</button>
				</th>
			</tr>
			</tfoot>
			
		</table>
 </div>       
</body>
</html>