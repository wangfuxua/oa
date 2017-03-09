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


<script language="JavaScript">
function CheckForm()
{
   if(document.form1.ITEM_NAME.value=="")
   { alert("工资项目名称不能为空！");
     return (false);
   }
}

</script>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "工资上报管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "工资项目定义", Url: "/index.php/salary/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "工资上报流程管理", Url: "/index.php/salary/flowIndex", Cls: "", IconCls: "ico ico-list2" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56); 
    
    });

</script>
<body>

	<div class="KDStyle" id="main">
	<form action="/index.php/salary/itemUpdate"  method="post" name="form1" onSubmit="return CheckForm();">
		<table>
			<col width="100px" />
			<thead>
				<tr>
					<th colspan="2" >工资项目<strong class="font-red"><?php echo ($salRow['ITEM_NAME']); ?></strong>编辑</th>
				</tr>
			</thead>
			<tbody>
					<tr>
						<th>序号</th>
						<td> <input name="ITEM_ORDER" value="<?php echo ($salRow['ITEM_ORDER']); ?>" type="text" class="dm_inputsen" onBlur="this.className='dm_blur';" onFocus="this.className='dm_foucs';" /></td>
					</tr>			
					<tr>
						<th>工资项目名称</th>
						<td><input type="hidden" name="ITEM_ID" value="<?php echo ($salRow['ITEM_ID']); ?>"> <input name="ITEM_NAME" value="<?php echo ($salRow['ITEM_NAME']); ?>" type="text" class="dm_inputsen" onBlur="this.className='dm_blur';" onFocus="this.className='dm_foucs';" /></td>
					</tr>
					
			</tbody>
			<tfoot>
					<tr>
						<th colspan="2" class="dm_btnzan">
						<button name="Abutton1" type="submit">修改</button><button name="Abutton1" onClick="history.back();">返回</button>
						</th>
					</tr>
			</tfoot>
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>
</body>
</html>