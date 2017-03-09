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

<body>
<script type="text/javascript">
   
   createHeader({
        Title: "工作流",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "/oa/Tpl/default/Public/style/default/images/ico/ico_help.gif", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false }
        ],
        Items: [
            { Title: "待办工作", Url: "/index.php/ZworkFlow/index", Cls: "", Icon: "/oa/Tpl/default/Public/style/default/images/ico/clock.png", IconCls: "ico ico-list" },
            { Title: "新建工作", Url: "news", Cls: "", IconCls: "ico ico-add" },
            { Title: "工作监控", Url: "workControl", Cls: "", IconCls: "ico ico-list2" }
        ]
    });
    

</script>


<div id="main" class="KDStyle">
		<table >
			<col width="60px" />
			<col width="80px" />
			<col width="300px" />
			<col />
			<col width="100px" />
			<col width="80px" />
			<col />
			
		    <thead>
			<tr>
				<th>工作等级</th>
				<th>工作创建时间</th>
				<th>文号/说明</th>
				<th>目前步骤</th>
				<th>查看进程</th>
				<th>执行</th>
				<th>备注</th>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($workRow)): ?><?php $i = 0;?><?php $__LIST__ = $workRow?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr class="tdcenter">
				<td><?php echo (workgrade($vo['grade'])); ?></td> 
				<td><?php echo (formatDate($vo['zworkTime'])); ?></td>
				<td><?php echo ($vo['zworkName']); ?></td>
				<td>第<?php echo ($vo['state']); ?>步</td>
				<td><a href="/index.php/ZworkFlow/flowView/workId/<?php echo ($vo['zworkId']); ?>">查看工作情况</a></td>
				<td><a href="/index.php/ZworkFlow/execute/workId/<?php echo ($vo['zworkId']); ?>">办理</a></td>
				<td><?php echo ($vo['tag']); ?></td>
			</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
			</tbody>
			<tfoot>
			<tr><td colspan="7"><?php echo ($page); ?></td></tr>
			</tfoot>
		</table>

</div>

	</div>
</body>
</html>