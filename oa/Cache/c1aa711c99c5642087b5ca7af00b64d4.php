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

        Active: 1,
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
<div class="KDStyle" id="main" >

    <table>
			<caption class="nostyle"><?php echo ($desc); ?></caption>
			<colgroup>
				<col width="120"></col>
				<col width=""></col>
			</colgroup>
			<thead>
				<tr>
					<th>日期</th>
					<th>日志标题</th>
				</tr>
			</thead>
			
			<tbody>
			<?php if(is_array($list)): ?><?php $k = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
					<td><?php echo (is_array($vo)?$vo["DIA_DATE"]:$vo->DIA_DATE); ?></td>
					<td><a href="/index.php/Diary/infodetail/DIA_ID/<?php echo (is_array($vo)?$vo["DIA_ID"]:$vo->DIA_ID); ?>"><?php echo (csubstr(strip_tags(is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT),0,50)); ?> </a></td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2"><?php echo ($page); ?></th>
				</tr>
			</tfoot>
    </tr>
    
   </table>


</div>
</div>

</body>
</html>