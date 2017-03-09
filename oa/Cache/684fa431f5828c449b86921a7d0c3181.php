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
        Title: "公告通知",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 2,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "全体通知", Url: "/index.php/manage/notifyAll", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "部门通知", Url: "/index.php/manage/notifyDept", Cls: "", IconCls: "ico ico-list2" }
        ]
    });		   
});
</script>
<body>

	<div class="KDStyle" id="main">
		<table>
			<col width="80px" />
			<col />
			<col width="110px" />
		  <thead>
			<tr>
				<th>发布人</th>
				<th>标题</th>
				<th>发布时间</th>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
				<td class="dm_zanal"><?php echo (getUsername(is_array($vo)?$vo["FROM_ID"]:$vo->FROM_ID)); ?></td>
				<td class="dm_zanare"><a href="viewUser/NOTIFY_ID/<?php echo (is_array($vo)?$vo["NOTIFY_ID"]:$vo->NOTIFY_ID); ?>/from/dept" title=""><?php echo (is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT); ?></a></td>
				<td><?php echo (date('Y-m-d H:i:s',is_array($vo)?$vo["BEGIN_DATE"]:$vo->BEGIN_DATE)); ?></td>
			</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
			<tr>
				<th colspan="3"><?php echo ($page); ?></th>
			</tr>			  
			</tfoot>			
		</table>
	</div>
</body>
</html>