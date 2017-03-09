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


<div class="addr-main">
	<ul class="dm_submenuul">
	    <?php if($USER_ID != ''): ?><li><a href="/index.php/Diary/infolist/USER_ID/<?php echo ($USER_ID); ?>" title=""><span>日志列表</span></a></li>
		<li><a href="/index.php/Diary/infosearch/USER_ID/<?php echo ($USER_ID); ?>" title=""><span>日志查询</span></a></li>
        <li class="dm_on"><a href="#" title=""><span>查看日志</span></a></li><?php endif; ?>        		
	</ul>
<div>
<table>
<caption class="nostyle"></caption>
				<thead>
					<tr>
						<th><?php echo ($USER_NAME); ?>&nbsp;<?php echo ($row[DIA_DATE]); ?>&nbsp;日志</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo ($row[CONTENT]); ?></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td><button title="返回" onclick="javascript:window.history.back();">返回</button></td>
					</tr>
				</tfoot>				
</table>					
</div>
</div>
</body>
</html>