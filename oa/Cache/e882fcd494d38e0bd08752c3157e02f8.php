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

<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
<body>
<div class="KDStyle" id="KDMain">
	<form action="/index.php/Hrms/update" method="post" name="form" enctype="multipart/form-data">
				
				<table>
					<col width="160px" />
					<col />
					<col width="93px" />
				<caption>员工档案（<?php echo ($userRow['USER_NAME']); ?>）-<?php echo ($STATUS); ?></caption>
					<thead><tr><th colspan="3">基本信息</th></tr></thead>
					<tbody>

					<tr>
						<th class="dm_filetdl">工号：</th>
						<td><input name="NO" value="<?php echo ($hrmsRow['NO']); ?>" type="text" class="dm_number"/></td>
						<td rowspan="3" class="dm_filephoto">
						    <?php if($hrmsRow[PHOTO]): ?><a href="<?php echo ($hrmsRow[PHOTO]); ?>" target="_blank"><img src="<?php echo ($hrmsRow['PHOTO']); ?>" alt="" width="80" height="100"/></a>
							<?php else: ?>
							无照片<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th class="dm_filetdl">合同编号：</th>
						<td><input name="contract" value="<?php echo ($hrmsRow['contract']); ?>" type="text" class="dm_number"  /></td>
					</tr>
					<tr>
						<th class="dm_filetdl">部门：</th>
						<td><input name="DEPARTMENT" value="<?php echo ($hrmsRow['DEPARTMENT']); ?>" type="text" class="dm_number1" onBlur="this.className='dm_number1';" onFocus="this.className='dm_numfou1';" /></td>
					</tr>
					<?php if(is_array($hrmsList)): ?><?php $i = 0;?><?php $__LIST__ = $hrmsList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr><th><?php echo ($vo['name']); ?>：</th><td colspan="2"><?php echo ($vo['form']); ?></td></tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
					</tbody>
					<tfoot>
					<tr>
						<th colspan="3" class="dm_tabottom">
						
					        <input type="hidden" value="<?php echo ($OP); ?>" name="OP">
					        <input type="hidden" value="<?php echo ($userRow[USER_ID]); ?>" name="USER_ID">
					        
							
							<button type="submit">保存</button>
							
						</th>
					</tr>
					</tfoot>
				</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

</body>
</html>