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

<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/css/validator.css"></link>
<script src="/oa/Tpl/default/Public/js/formValidator.js" type="text/javascript" charset="UTF-8"></script>
<script src="/oa/Tpl/default/Public/js/formValidatorRegex.js" type="text/javascript" charset="UTF-8"></script>
<script language="javascript" src="/oa/Tpl/default/Public/js/DateTimeMask.js" type="text/javascript"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/js.js"></script>	
	</head>

<script type="text/javascript">
$(document).ready(function(){
	//$.formValidator.initConfig({formid:"form1",onerror:function(){alert("校验没有通过，具体错误请看错误提示")}});
	$.formValidator.initConfig({formid:"form1",onerror:function(msg){alert(msg)}});
	$("#modelName").formValidator({onshow:"请输入模型名称",onfocus:"模型名称不能为空",oncorrect:"模型名称合法"}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"模型名称两边不能有空符号"},onerror:"模型名称不能为空,请确认"});;
	$("#modelTableName").formValidator({onshow:"请输入模型（英文名称）",onfocus:"模型（英文名称）不能为空",oncorrect:"模型（英文名称）合法"}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"模型（英文名称）两边不能有空符号"},onerror:"模型（英文名称）不能为空,请确认"}).regexValidator({regexp:"username",datatype:"enum",onerror:"模型（英文名称）格式不正确，只能输入字母或数字"});
    });
</script>
<body>
<script type="text/javascript">
	createHeader({
        Title: "工作流设置",
        Icon: "",
        IconCls: "ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
                { Title: "帮助", Url: "http://help.7e73.com", Icon: "/oa/Tpl/default/Public/style/default/images/ico/ico_help.gif", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false }
            ],
        Items: [
                { Title: "模型设置", Url: "/index.php/ZworkModule/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
                { Title: "更新缓存", Url: "/index.php/ZworkModule/cache", Cls: "", Icon: "", IconCls: "ico ico-list" }
            ]
    });
</script>
	<div id="main" class="KDStyle">
		<form action="/index.php/ZworkModule/add"  method="post" name="form1" id="form1">	
		<table style="width:80%">
		    <caption>新建模型</caption>
		    <tbody>
			<tr>
			    <th class="dm_zanal">模型名称：</th>
			    <td width="300"><input name="modelName" id="modelName" type="text" class="dm_inputsen"/></td>
			    <td><div id="modelNameTip" ></div></td>
			</tr>
			<tr>
			    <th class="dm_zanal">模型（英文名称）：</th>
			    <td><input name="modelTableName" id="modelTableName" type="text" class="dm_inputsen" /></td>
			    <td><div id="modelTableNameTip" ></div></td>
			</tr>
			<tr>
			    <th class="dm_zanal">归类：</th><td><select name="category">
			    <?php if(is_array($category)): ?><?php $k = 0;?><?php $__LIST__ = $category?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$v): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><option value="<?php echo ($k); ?>"><?php echo ($v); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			    </select></td>
			    <td></td>
			</tr>
			</tbody>
			<tfoot>
			<tr>
				<th colspan="3">
					 <button name="Abutton1" type="submit"><div><span>确定</span></div></button>
				</th>
			</tr>
			</tfoot>
			
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	<h2 class="dm_schedule"></h2>

		<table style="width:80%">
		    <caption>管理模型</caption>
		    <thead>
			<tr>
				<th>模型名称</th>
				<th>操作</th>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($workModuleList)): ?><?php $i = 0;?><?php $__LIST__ = $workModuleList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
				<td><div align="center"><?php echo ($vo['modelName']); ?></div></td>
				<td><div align="center"><a href="/index.php/ZworkModule/edit/modelId/<?php echo ($vo['modelId']); ?>">编辑模型名称</a>&nbsp;/&nbsp;<a href="/index.php/ZworkModule/filedIndex/modelId/<?php echo ($vo['modelId']); ?>">模型控件定义</a>&nbsp;/&nbsp;<a href="/index.php/ZworkModule/flow/modelId/<?php echo ($vo['modelId']); ?>">流程定义</a>&nbsp;/&nbsp;<a href="/index.php/ZworkModule/layout/modelId/<?php echo ($vo['modelId']); ?>">编辑模板样式</a>&nbsp;/&nbsp;<a href="/index.php/ZworkModule/del/modelId/<?php echo ($vo['modelId']); ?>">删除</a></div></td>
			</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
			</tbody>
			<tfoot>
			<tr><td colspan="2"><?php echo ($page); ?></td></tr>
			</tfoot>
		</table>
	</div>
</body>
</html>