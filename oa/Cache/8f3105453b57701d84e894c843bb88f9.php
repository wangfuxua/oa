<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>客户来源</title>

<link href="/oa/Tpl/default/Public/style/default/css/kdstyle.css" rel="stylesheet" type="text/css" />
<script src ="/oa/Tpl/default/Public/js/crm_common.js"></script>
<script>
function categoryAdd(){
	document.getElementById('new_one').innerHTML = '<form action="/index.php/XmlFile/save/dis/<?php echo ($dis); ?>/to_id/<?php echo ($to_id); ?>/to_name/<?php echo ($to_name); ?>" method="POST" onsubmit="return idNull(\'new_name\',\'new_name_msg\',\'客户来源不为空!\');"><table><tr><td colspan="2">客户来源: <input type="text" id="new_name" name="name" size="4" maxlength="25" onblur="return idNull(\'new_name\',\'new_name_msg\',\'客户来源不为空!\');" /><span style="color:red;">*</span><span id="new_name_msg"></span>&nbsp;&nbsp;</td></tr><tr><td colspan="2">排 序 号: <input type="text" name="sort" id="sort" size="4" maxlength="3" onblur="checkNumber(\'sort\',\'sort_msg\',\'请填写数字!\');" /><span id="sort_msg"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit">添加</button></td></tr></table><?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>';
}

function categoryEdit(id,name,sort){
	document.getElementById('new_one').innerHTML = '<form action="/index.php/XmlFile/update/dis/<?php echo ($dis); ?>/to_id/<?php echo ($to_id); ?>/to_name/<?php echo ($to_name); ?>" method="POST" onsubmit="return idNull(\'new_name\',\'new_name_msg\',\'客户来源不为空!\');"><table><tr><td colspan="2">客户来源: <input type="text" id="new_name" name="name" size="4" value="'+name+'" maxlength="25" onblur="return idNull(\'new_name\',\'new_name_msg\',\'客户来源不为空!\');" /><span style="color:red;">*</span><span id="new_name_msg"></span>&nbsp;&nbsp;</td></tr><tr><td colspan="2">排 序 号: <input type="hidden" name="current_id" value="'+id+'" maxlength="3" /><input type="text" name="sort" id="sort" size="4" value="'+sort+'" maxlength="3" onblur="checkNumber(\'sort\',\'sort_msg\',\'请填写数字!\');" /><span id="sort_msg"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit">修改</button></td></tr></table><?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>';
}

function noPriv() {
	document.getElementById('new_one').innerHTML = '<h4 style = "color:red;">对不起，您没有这项操作的权限</h4>';
}
</script>
</head>

<body>
<div id="KDMain" class="KDStyle">
<span id="new_one">
</span>
<table>
	<caption class="nostyle">客户来源列表 | <?php if($userPriv != '' ): ?><a href="javascript:void(0);" onclick="categoryAdd();">
<?php else: ?><a href="javascript:void(0);" onclick="noPriv();"><?php endif; ?>新建客户来源</a></caption>
	<colgroup>
		<col width="40%" />
		<col width="20%" />
		<col width="38%" />
	</colgroup>
	</thead>
	<thead>
		<tr>
			<th>客户来源</th>
			<th>排序号</th>
			<th>操作</th>
		</tr>
	</thead>
	<?php if(is_array($result)): ?><?php $i = 0;?><?php $__LIST__ = $result?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
			<td><a href="javascript:void(0);" onclick="setParentValue(Array('<?php echo ($to_id); ?>','<?php echo ($to_name); ?>'), Array('<?php echo ($vo['id']); ?>','<?php echo ($vo['name']); ?>'));"><?php echo ($vo['name']); ?></a></td>
			<td><?php echo ($vo['sort']); ?></td>
			<td>&nbsp;&nbsp;&nbsp;
<?php if($userPriv != '' ): ?><a href="javascript:void(0);" onclick="categoryEdit('<?php echo ($vo['id']); ?>','<?php echo ($vo['name']); ?>','<?php echo ($vo['sort']); ?>');">
<?php else: ?><a href="javascript:void(0);" onclick="noPriv();"><?php endif; ?>编辑</a>&nbsp;&nbsp;&nbsp;
<?php if($userPriv != '' ): ?><a href="/index.php/XmlFile/del/dis/<?php echo ($dis); ?>/id/<?php echo ($vo['id']); ?>/to_id/<?php echo ($to_id); ?>/to_name/<?php echo ($to_name); ?>">
<?php else: ?><a href="javascript:void(0);" onclick="noPriv();"><?php endif; ?>删除</a></td>
		</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	

</table>
</div>
</body></html>