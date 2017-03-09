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

<script>
function delete_dept(DEPT_ID)
{
 msg='确认要删除该部门吗？';
 if(window.confirm(msg))
 {
  URL="/index.php/Dept/delete/DEPT_ID/" + DEPT_ID;
  window.location=URL;
 }
}

</script>
<body>
<h2>部门管理</h2>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dm_tablezana">
		<form action="/index.php/Dept/update"  method="post" name="form1" >	
			<tr>
				<td valign="top" class="dm_zanal">部门名称：</td>
				<td>
					<input name="DEPT_NAME" value="<?php echo ($deptRow['DEPT_NAME']); ?>" type="text" class="dm_blur3" />
				</td>
			</tr>
		<tr>
				<td valign="top" class="dm_zanal">部门排序：</td>
				<td>
					<input name="DEPT_NO" value="<?php echo ($deptRow['DEPT_NO']); ?>" type="text" class="dm_blur3" style="width:30px"/>
				</td>
			</tr>			
			<tr>
				<td valign="top">电话：</td>
				<td class="dm_datetd">
					<input name="TEL_NO" value="<?php echo ($deptRow['TEL_NO']); ?>" type="text" class="dm_blur3"/>
				</td>
			</tr>
			<tr>
				<td valign="top" class="dm_zanal">传真：</td>
				<td class="dm_datetd">
					<input name="FAX_NO" value="<?php echo ($deptRow['FAX_NO']); ?>" type="text" class="dm_blur3" />
				</td>
			</tr>
			<tr>
				<td valign="top">上级部门：</td>
				<td>
					<select name="DEPT_PARENT">
						<option value="0">无</option>
						<?php echo ($my_dept_tree); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="dm_btnzan">
				    <input type="hidden" name="DEPT_ID" value="<?php echo ($deptRow[DEPT_ID]); ?>">
					<button name="submit" type="submit" value="更新" />更新</button>
					<button name="button" type="button" value="删除" onclick="javascript:delete_dept('<?php echo ($deptRow[DEPT_ID]); ?>')"/>删除</button>
				</td>
			</tr><?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
		</table>
		
</body>
</html>