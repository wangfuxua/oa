<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>供应商列表</title>

<link href="/oa/Tpl/default/Public/css/default.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.calendar{width:220px;height:100px}
.calendar td,.calendar th{padding:0px;height:16px;line-height:18px;text-align:center;cursor:pointer}

</style>
<script src ="/oa/Tpl/default/Public/js/crm_common.js"></script>
<script src="/oa/Tpl/default/Public/js/crm_data.js"></script>
</head>

<body>
	<ul class="dm_submenuul">
		<li class="dm_on"><a href="/index.php/Supplier/lists" title=""><span>供应商列表</span></a></li>
		<li><a href="/index.php/Supplier/create" title=""><span>新建供应商</span></a></li>
	</ul>

<form action="/index.php/Supplier/search" method="post">
<table>
	<caption class="nostyle">查找供应商</caption>
	<colgroup>
		<col width="15%" />
		<col width="20%" />
		<col width="15% " />
		<col width="15%" />
		<col width="15% " />
		<col width="20%" />
	</colgroup>
<tbody>
	<tr>
	<td>供应商名称：</td>
	<td><input name='name' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>供应商编码：</td>
	<td><input name='encode' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>供应商简称：</td>
	<td><input name='short_name' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	</tr>
	<tr>
	<td>省份：</td>
	<td><script>showprovince('province', 'city', "");</script></td>
	<td>城市：</td>
	<td><script>showcity('city', "", 'province');</script></td>
	<td>备注：</td>
	<td><input name='description' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	</tr>
</tbody>
	<tfoot>
		<tr>
			<th colspan="6">
				<button title="查找 [Alt+Q]" type="submit">查找</button>
				<button title="重置 [Alt+C]" type="reset" >重置</button>
			</th>
		</tr>
	</tfoot>
</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<form action="/index.php/Supplier/del" method='post' name='MassUpdate'  id="MassUpdate">
<table>
	<caption class="nostyle">供应商列表</caption>
	<colgroup>
		<col width="20" />
		<col width="" />
		<col width="30%" />
		<col width="" />
		<col width="15%" />
		<col width="15%" />
		<col width="15%" />
	
	</colgroup>
	<thead>
		<tr><td colspan="7"><?php echo ($page); ?></td></tr>
		<tr>
			<th><fieldset class="check"><input type='checkbox' class='checkbox' name='massall' value='' onclick='checkAll(document.MassUpdate, "mass[]", this.checked)' /></fieldset></th>
			<th><a href="<?php echo ($sort_url); ?>&field=encode">供应商编码</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=name">供应商名称</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=province">地区</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=phone">电话</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=fax">传真</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=mail">电子邮件</a></th>
		</tr>
	</thead>
	<?php if(is_array($supplier_record)): ?><?php $i = 0;?><?php $__LIST__ = $supplier_record?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
			<td><fieldset class="check"><input type='checkbox' class='checkbox' name='mass[]' value="<?php echo ($vo['id']); ?>" /></fieldset></td>
			<td ><?php echo ($vo['encode']); ?></td>
			<td><a href="/index.php/Supplier/view/id/<?php echo ($vo['id']); ?>" class="listViewTdLinkS1">&nbsp;<?php echo ($vo['name']); ?></td>
			<td ><?php echo ($vo['province']); ?>&nbsp;&nbsp;<?php echo ($vo['city']); ?></td>
			<td><?php echo ($vo['phone']); ?></td>
		    <td><?php echo ($vo['fax']); ?></td>
		    <td><?php echo ($vo['mail']); ?></td>
		</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
	<tfoot>
		<tr>
			<td colspan="7"><?php echo ($page); ?></td>
		</tr>
		<tr>
			<th colspan="7">
				<button onclick='checkAll(document.MassUpdate, "mass[]", 1);' type="button">全选</button>
				<button onclick='checkAll(document.MassUpdate, "mass[]", 0);' type="button">取消选中</button>
				<button type='submit' onclick="return del_records(this.form,'mass[]');">删除</button>
			</th>
		</tr>
	</tfoot>

</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

</body></html>