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
				<col width="100"></col>
				<col></col>
			</colgroup>
			<thead><tr><th colspan="2"></th></tr></thead>
			<tr>
				<th>发布人：</th>
				<td><?php echo (getUsername($row['FROM_ID'])); ?> </td>
			</tr>
			<tr>
				<th>发布范围：</th>
				<td>
				<?php if($row['allname']): ?><?php echo ($row['allname']); ?>
				<?php else: ?>
				全体<?php endif; ?></td>
			</tr>			
			<tr>
				<th>发布时间：</th>
				<td><?php echo (date('Y-m-d H:i:s',is_array($row)?$row["BEGIN_DATE"]:$row->BEGIN_DATE)); ?></td>
			</tr>
			<tr>
				<th>标题：</th>
				<td><?php echo ($row['SUBJECT']); ?></td>
			</tr>
			<tr>
				<th>附件：</th>
				<td>
						<?php if($row[ATTACHMENT_NAME] == ''): ?>无附件<?php else: ?>
						<?php if(is_array($listatt)): ?><?php $i = 0;?><?php $__LIST__ = $listatt?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><img src="/oa/Tpl/default/Public/images/email_atta.gif">
						<a href="/index.php/Attach/view/attid/<?php echo ($vo[attid]); ?>"><?php echo ($vo[filename]); ?></a>&nbsp;&nbsp;(<?php echo ($vo[filesize]); ?>字节)<br><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endif; ?>
										
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<p>
						<?php echo ($row['CONTENT']); ?>
					</p>
				</td>
			</tr>
			<tfoot>
				<tr>
					<th colspan="2">
						<button name="Abutton1" onClick="window.close()">关闭</button>
					</th>
				</tr>
			</tfoot>
		</table>
	</div>

</body>
</html>