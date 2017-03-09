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
	</head>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);
		createHeader({
        Title: "工作日志",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 3,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "日志列表", Url: "/index.php/Diary/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "添加日志", Url: "/index.php/Diary/add", Cls: "", IconCls: "ico ico-add" },
            { Title: "日志查询", Url: "/index.php/Diary/search", Cls: "", IconCls: "ico ico-query" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56);
    
    });

</script>
<body>
	
<div class="KDStyle" id="main">
		<form method="post" action="/index.php/Diary/query" name="form1">
			<table>
				<colgroup>
					<col width="80"></col>
					<col></col>
				</colgroup>
				<tbody>
					<tr>
						<td>起始日期：</td>
						<td>
							<fieldset class="date">
        <input type="text" name="DIA_DATE_START" size="30" maxlength="30" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})">
        <img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="WdatePicker({el:$dp.$('DIA_DATE_START'),dateFmt:'yyyy-MM-dd'})"  />
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>截止日期：</td>
						<td>
							<fieldset class="date">
        <input type="text" name="DIA_DATE_END" size="30" maxlength="30" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})">
        <img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="WdatePicker({el:$dp.$('DIA_DATE_END'),dateFmt:'yyyy-MM-dd'})"  />
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>日志类型：</td>
						<td>
				        <select name="DIA_TYPE">
				         <option value="0" selected>所有类型</option>
				          <option value="1">工作日志</option>
          
				          <option value="3">工作周报</option>
				          <option value="4">工作月报</option>
				          <option value="5">年度总结</option>
				          				          
				          <option value="2">个人日志</option>
				        </select>
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
						<button type="submit" class="btnFnt" value="查询">查询</button>
						</th>
					</tr>
				</tfoot>
			</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</div>
	

</body>
</html>