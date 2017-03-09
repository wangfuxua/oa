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

<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "工资上报录入",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "执行中的工资上报流程", Url: "#", Cls: "", Icon: "", IconCls: "ico ico-list" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56); 
    
    });

</script>
<body>
	<div class="KDStyle" id="main">
		<table>
			<thead>
				<tr>
						<th>起始日期  </th><th>截止日期 </th><th>备注 </th><th>操作 </th>
				</tr>
			</thead>
			<tbody>  
			<?php if(is_array($salList)): ?><?php $i = 0;?><?php $__LIST__ = $salList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
						<td><?php echo (formatDate($vo['BEGIN_DATE'],'full')); ?> </td><td><?php echo (formatDate($vo['END_DATE'],'full')); ?> </td><td><?php echo ($vo['CONTENT']); ?> </td><td><a href="salIndex/FLOW_ID/<?php echo ($vo['FLOW_ID']); ?>"> 工资上报</a></td>
					</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
					<tr>
						<th colspan="4">
						</th>
					</tr>
			</tfoot>
		</table>
</div>
</body>
</html>