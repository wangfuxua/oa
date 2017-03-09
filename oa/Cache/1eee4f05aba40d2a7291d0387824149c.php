<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="JKTD-D-丁亚娟" />
<title>工作流程设置</title>
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/style/default/css/style.css" />
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/style/default/css/table.css" />
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/style/default/css/form.css" />
</head>

<body>
<h2 class="dm_schedule"></h2>
 <div id="Div1" class="KDStyle">
		<form action="/index.php/ZworkModule/add"  method="post" name="form1" >	
		<table>
		    <caption>常用模型</caption>
		    <col width="50%" />
			<thead>
			<tr>
			<td class="dm_btnzan">模型名称</td>
			<td class="dm_btnzan">操作</td>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($zworkList)): ?><?php $i = 0;?><?php $__LIST__ = $zworkList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr class="tdcenter">
			<th><?php echo ($vo['modelName']); ?></th>
			<td><a href="/index.php/ZworkFlow/createNew/modelId/<?php echo ($vo['modelId']); ?>">快速创建工作</a></td>
			</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
			</tbody>
			<tfoot>
			    <tr><td colspan="2"></td></tr>
			</tfoot>
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</div>
</body>
</html>