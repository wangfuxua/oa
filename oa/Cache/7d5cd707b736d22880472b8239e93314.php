<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="JKTD-J-Jay" />
<title></title>
<link href="/oa/Tpl/default/Public/style/default/css/form.css" rel="stylesheet" type="text/css" />
<link href="/oa/Tpl/default/Public/style/default/css/table.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/style/default/css/style.css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KD.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js" ></script>
<script type="text/javascript">
$(function() { 
    createHeader({
        Title: "工作流",
        Icon: "",
        IconCls: "ico ico-head-news",
        Cls: "",
        Active: 0,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "/oa/Tpl/default/Public/style/default/images/ico/ico_help.gif", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false }
        ],
        Items: [
            { Title: "待办工作", Url: "/index.php/ZworkFlow/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "新建工作", Url: "/index.php/ZworkFlow/news", Cls: "", IconCls: "ico ico-add" },
            { Title: "工作监控", Url: "/index.php/ZworkFlow/workControl", Cls: "", IconCls: "ico ico-view" }
        ]
    });

});


</script>
</head>
<body>


	<div id="main" class="KDStyle">
		<table>
		<caption>流程信息</caption>
		<col width="80px" />
		<thead>
		<tr><th>步骤</th><th>流程名称</th><th>控制者</th><th>流程备注（例：转交提示）</th></tr>
		</thead>
		<tbody>
		<?php if(is_array($flowList)): ?><?php $i = 0;?><?php $__LIST__ = $flowList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr class="tdcenter"><td>第<?php echo ($vo['flowId']); ?>步</td><td><?php echo ($vo['flowName']); ?></td><td><?php echo ($vo['powerUser']); ?>[<?php echo (flowState($vo['state'])); ?>]<br></td><td><?php echo ($vo['tag']); ?></td></tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
		</tbody>
		<tfoot><tr><th colspan="4"></th></tr></tfoot>
		</table>


		<table>
		<caption>表单内容</caption>
		<thead>
		<tr>
		<th colspan="2" ><p class="wp1"><?php echo ($workmoduleRow['modelName']); ?></p></th>
		</tr>
		</thead>
		<tbody>
		<tr><th colspan="2"><span style="float:left">文号：<?php echo ($workRow['zworkName']); ?></span><span style="float:right">时间：<?php echo (formatdate($workRow['zworkTime'])); ?></span></th></tr>
		<tr><td colspan="2"><?php echo ($templeate); ?></td></tr>
		</tbody>
		<tfoot><tr><th align="2">
		<button type="submit" onclick="window.open('/index.php/WorkFlow/printView/workId/<?php echo ($workRow[zworkId]); ?>','','width=750,height=600')"><div><span>我要打印</span></div></button>&nbsp;
		</th>
		</tr></tfoot>		
  		</table>
  		
  		
  	</div>
</body>
</html>