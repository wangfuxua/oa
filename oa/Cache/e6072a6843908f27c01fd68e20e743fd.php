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
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({

        Active: 2,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
				<?php if($USER_ID != ''): ?>{ Title: "日志列表", Url: "/index.php/Diary/infolist/USER_ID/<?php echo ($USER_ID); ?>", Cls: "", Icon: "", IconCls: "ico ico-view" },
            { Title: "日志查询", Url: "/index.php/Diary/infosearch/USER_ID/<?php echo ($USER_ID); ?>", Cls: "", IconCls: "ico ico-query" },
            { Title: "周报", Url: "/index.php/Diary/statistics/USER_ID/<?php echo ($USER_ID); ?>/type/week", Cls: "", IconCls: "ico ico-list" },
            { Title: "月报", Url: "/index.php/Diary/statistics/USER_ID/<?php echo ($USER_ID); ?>/type/month", Cls: "", IconCls: "ico ico-list2" }<?php endif; ?>
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56); 
    
    });

</script>
<body>
<div class="KDStyle" id="main">

		<form method="post" action="/index.php/Diary/infolist" name="form1">
			<table>
			<caption class="nostyle"><?php echo ($desc); ?></caption>
				<colgroup>
					<col width="80"></col>
					<col></col>
				</colgroup>
				<tbody>
					<tr>
						<td>起始日期：</td>
						<td>
							<fieldset>
								<input name="DIA_DATE_START" type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
								<img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="选择时间" style="cursor:hand" onclick="WdatePicker({el:$dp.$('DIA_DATE_START'),dateFmt:'yyyy-MM-dd'})"  />
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>截止日期：</td>
						<td>
							<fieldset>
								<input name="DIA_DATE_END" type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
								<img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="选择时间" style="cursor:hand" onclick="WdatePicker({el:$dp.$('DIA_DATE_END'),dateFmt:'yyyy-MM-dd'})"  />
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>关键词1：</td>
						<td><input name="key1" type="text" />&nbsp;</td>
					</tr>
					<tr>
						<td>关键词2：</td>
						<td><input name="key2" type="text" /></td>
					</tr>
					<tr>
						<td>关键词3：</td>
						<td><input name="key3" type="text" /></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="2">
						<input type="hidden" name="USER_ID" value="<?php echo ($USER_ID); ?>">
						<button type="submit" title="查询">查询</button>
						</th>
					</tr>
				</tfoot>
			</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>

</body>
</html>