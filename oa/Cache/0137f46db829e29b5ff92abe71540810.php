<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="JKTD-D-丁亚娟" />
<title>单位管理</title>
<link href="/oa/Tpl/default/Public/css/default.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<div class="dm_conone">
		<h2>单位管理</h2>
		<form action="update"  method="post" name="form1">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dm_tablezana">
		
			<tr>
				<td valign="top" class="dm_zanal">单位名称：</td>
				<td>
					<input name="UNIT_NAME" type="text" class="dm_blur3" onblur="this.className='dm_blur3';" onfocus="this.className='dm_foucs3';" value="<?php echo ($row['UNIT_NAME']); ?>" />
				</td>
			</tr>
			<tr>
				<td valign="top">电话：</td>
				<td class="dm_datetd">
					<input name="TEL_NO" type="text" class="dm_blur3" onblur="this.className='dm_blur3';" onfocus="this.className='dm_foucs3';" value="<?php echo ($row['TEL_NO']); ?>" />
				</td>
			</tr>
			<tr>
				<td valign="top" class="dm_zanal">传真：</td>
				<td class="dm_datetd">
					<input name="FAX_NO" type="text" class="dm_blur3" onblur="this.className='dm_blur3';" onfocus="this.className='dm_foucs3';" value="<?php echo ($row['FAX_NO']); ?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">地址：</td>
				<td>
					<input name="ADDRESS" type="text" class="dm_blur3" onblur="this.className='dm_blur3';" onfocus="this.className='dm_foucs3';" value="<?php echo ($row['ADDRESS']); ?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">网站：</td>
				<td>
					<input name="URL" type="text" class="dm_blur3" onblur="this.className='dm_blur3';" onfocus="this.className='dm_foucs3';" value="<?php echo ($row['URL']); ?>" />
				</td>
			</tr>
			<tr>
				<td valign="top">电子信箱：</td>
				<td>
					<input name="EMAIL" type="text" class="dm_blur3" onblur="this.className='dm_blur3';" onfocus="this.className='dm_foucs3';" value="<?php echo ($row['EMAIL']); ?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">开户行：</td>
				<td>
					<input name="BANK_NAME" type="text" class="dm_blur3" onblur="this.className='dm_blur3';" onfocus="this.className='dm_foucs3';" value="<?php echo ($row['BANK_NAME']); ?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top">帐号：</td>
				<td>
					<input name="BANK_NO" type="text" class="dm_blur3" onblur="this.className='dm_blur3';" onfocus="this.className='dm_foucs3';" value="<?php echo ($row['BANK_NO']); ?>"/>
				</td>
			</tr>
			<tfoot>
			<tr>
				<td colspan="2">
					<button type="submit" />确定</button>
				</td>
			</tr>
			</tfoot>
			
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>
</body>
</html>