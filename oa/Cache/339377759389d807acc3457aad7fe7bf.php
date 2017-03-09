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

	<script>
	function open_notify(NOTIFY_ID)
	{
	 	URL="/index.php/manage/view/NOTIFY_ID/"+NOTIFY_ID;
	 	myleft=(screen.availWidth-500)/2;
	 	window.open(URL,"read_notify","height=400,width=550,status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
	}
	function delete_notify(NOTIFY_ID)
	{
		 	msg='确认要删除该项公告通知么？';
		 	if(window.confirm(msg))
		 	{
		  	URL="/index.php/manage/delete/NOTIFY_ID/" + NOTIFY_ID;
		 	 window.location=URL;
		 	}
 		
   }

	function delete_all()
	{
		 	msg='确认要删除所有公告通知么？';
		 	if(window.confirm(msg))
		 	{
		  	 URL="/index.php/manage/deleteAll";
		 	 window.location=URL;
		 	}
	}
	
</script>
<script type="text/javascript">
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "公告通知管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "管理已发布的公告", Url: "/index.php/manage/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "添加新公告", Url: "/index.php/manage/news", Cls: "", IconCls: "ico ico-add" }
        ]
    });		   
});
</script>
<body>

	<div class="KDStyle" id="main">
		<form method="post" action="">
		<table>
			<caption></caption>
			<colgroup>
				<col width="60"></col>
				<col width="120"></col>
				<col></col>
				<col width="110"></col>
				<col width="110"></col>
				<col width="110"></col>
				<col width="60"></col>
				<col width="60px"></col>
			</colgroup>
			<thead>
				<tr>
					<th>发送人</th>
					<th>发送范围</th>
					<th>标题</th>
					<th>创建时间 </th>
					<th>生效日期</th>
					<th>终止日期</th>
					<th>当前状态</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
			<?php if(is_array($notify)): ?><?php $i = 0;?><?php $__LIST__ = $notify?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
					<td class="tcenter"><?php echo (getUsername(is_array($vo)?$vo["FROM_ID"]:$vo->FROM_ID)); ?></td> 
					<td><?php echo (is_array($vo)?$vo["allname"]:$vo->allname); ?></td>
					<td><a href="javascript:open_notify('<?php echo ($vo['NOTIFY_ID']); ?>');"><?php echo ($vo['SUBJECT']); ?></a></td>
					<td><?php echo (date("Y-m-d H:i:s",$vo['SEND_TIME'])); ?></td>
					<td><?php echo (date("Y-m-d H:i:s",$vo['BEGIN_DATE'])); ?></td>
					<td><?php echo (date("Y-m-d H:i:s",$vo['END_DATE'])); ?></td>
					<td class="tcenter"><?php echo (timeStatusStr($vo['BEGIN_DATE'],$vo['END_DATE'])); ?></td>
					<td class="tcenter">
						<a href="edit/NOTIFY_ID/<?php echo ($vo['NOTIFY_ID']); ?>" title="">修改</a>
						<a href="javascript:delete_notify('<?php echo ($vo['NOTIFY_ID']); ?>');" title="">删除</a>
					</td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="8">
						<button name="Abutton1" onClick="javascript:delete_all();">全部删除</button>
						<?php echo ($page); ?>
					</th>
				</tr>
			</tfoot>
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
	</div>

</body>
</html>