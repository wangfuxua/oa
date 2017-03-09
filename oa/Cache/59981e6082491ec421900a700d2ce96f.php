<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>列表</title>

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
		<li  class="dm_on"><a href="/index.php/Product/lists" title=""><span>产品列表</span></a></li>
		<li><a href="/index.php/Product/create" title=""><span><?php if($update): ?>修改<?php else: ?>新建<?php endif; ?>产品</span></a></li>
		<li><a href="/index.php/Product/slists" title=""><span>服务型产品列表</span></a></li>
		<li><a href="/index.php/Product/screate" title=""><span><?php if($update): ?>修改<?php else: ?>新建<?php endif; ?>服务型产品</span></a></li>
	</ul>

<form action="/index.php/Product/search" method="post">
<table>
	<caption class="nostyle">查找</caption>
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
	<td>产品编号：</td>
	<td><input name='encode' type="text" tabindex='2' size='20' maxlength='30' value='' /></td>
	<td>产品名称：</td>
	<td><input name='name' type="text" tabindex='2' size='20' maxlength='30' value='' /></td>
	<td>供应商名称：</td>
	<td><input name='supplier_name' type="text" tabindex='2' size='20' maxlength='30' value='' /></td>
	</tr>
	<tr>
	<td>产品类型：</td>
	<td><select name="category_name">
		<option value="">请选择</option>
		<?php if(is_array($xml_type)): ?><?php $i = 0;?><?php $__LIST__ = $xml_type?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$cate_vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($cate_vo['name']); ?>"><?php echo ($cate_vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
		</select></td>
	<td>产品型号：</td>
	<td><input name='code' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>产品描述：</td>
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
<form action="/index.php/Product/del" method='post' name='MassUpdate'  id="MassUpdate">
<table>
	<caption class="nostyle">列表</caption>
	<colgroup>
		<col width="20" />
		<col width="20%" />
		<col width="10%" />
		<col width="10%" />
		<col width="15%" />
		<col width="10%" />
		<col width="15%" />
		<col width="15%" />
	
	</colgroup>
	<thead>
		<tr><td colspan="8"><?php echo ($page); ?></td></tr>
		<tr>
			<th><fieldset class="check"><input type='checkbox' class='checkbox' name='massall' value='' onclick='checkAll(document.MassUpdate, "mass[]", this.checked)' /></fieldset></th>
			<th><a href="<?php echo ($sort_url); ?>&field=name">产品名称</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=encode">产品编码</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=category_name">产品类别</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=count_unit">计量单位</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=cost_price">成本价</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=sale_price">出售价</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=supplier_name">供应商</a></th>
		</tr>
	</thead>
	<?php if(is_array($product_res)): ?><?php $i = 0;?><?php $__LIST__ = $product_res?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
			<td><fieldset class="check"><input type='checkbox' class='checkbox' name='mass[]' value="<?php echo ($vo['id']); ?>" /></fieldset></td>
			<td><a href="/index.php/Product/view/id/<?php echo ($vo['id']); ?>" class="listViewTdLinkS1">&nbsp;<?php echo ($vo['name']); ?></td>
			<td ><?php echo ($vo['encode']); ?></td>
			<td><?php echo ($vo['category_name']); ?></td>
		    <td><?php echo ($vo['count_unit']); ?></td>
		    <td><?php echo ($vo['cost_price']); ?></td>
			<td ><?php echo ($vo['sale_price']); ?></td>
			<td ><?php echo ($vo['supplier_name']); ?></td>
		</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
	<tfoot>
		<tr>
			<td colspan="8"><?php echo ($page); ?></td>
		</tr>
		<tr>
			<th colspan="8">
				<button onclick='checkAll(document.MassUpdate, "mass[]", 1);' type="button">全选</button>
				<button onclick='checkAll(document.MassUpdate, "mass[]", 0);' type="button">取消选中</button>
				<button type='submit' onclick="return del_records(this.form,'mass[]');">删除</button>
			</th>
		</tr>
	</tfoot>

</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

</body></html>