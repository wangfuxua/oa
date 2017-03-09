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
<script>
function delete_diary(DIA_ID)
{
 msg='确认要删除该日志么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Diary/delete/DIA_ID/" + DIA_ID;
  window.location=URL;
 }
}

function CheckForm()
{
   if(document.form1.BEGIN_DATE.value=="")
   { alert("起始日期不能为空！");
     return (false);
   }

   if(document.form1.END_DATE.value=="")
   { alert("截止日期不能为空！");
     return (false);
   }
   
   return true;
}

function td_calendar(fieldname)
{
  myleft=document.body.scrollLeft+event.clientX-event.offsetX-80;
  mytop=document.body.scrollTop+event.clientY-event.offsetY+140;
  window.showModalDialog("/inc/calendar.php?FIELDNAME="+fieldname,self,"edge:raised;scroll:0;status:0;help:0;resizable:1;dialogWidth:280px;dialogHeight:205px;dialogTop:"+mytop+"px;dialogLeft:"+myleft+"px");
}
</script>
<script type="text/javascript">
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "工作日志",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
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
</script>
<body>

<div class="KDStyle" id="main">
    <table>
			<caption></caption>
			<colgroup>
				<col width="110"></col>
				<col width="60"></col>
				<col width=""></col>
				<col width="60"></col>
			</colgroup>
			<thead>
				<tr>
					<th>日期</th>
					<th>日志类型</th>
					<th>日志标题</th>
					<th>操作</th>
				</tr>
			</thead>
			
			<tbody>
			<?php if(is_array($list)): ?><?php $k = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
					<td><?php echo (is_array($vo)?$vo["DIA_DATE"]:$vo->DIA_DATE); ?></td>
					<td><?php if($vo[DIA_TYPE] == 1): ?>工作日志<?php elseif($vo[DIA_TYPE] == 2): ?>个人日志<?php elseif($vo[DIA_TYPE] == 3): ?>工作周报<?php elseif($vo[DIA_TYPE] == 4): ?>工作月报<?php elseif($vo[DIA_TYPE] == 5): ?>年度总结<?php endif; ?></td>
					<td><a href="/index.php/Diary/edit/DIA_ID/<?php echo (is_array($vo)?$vo["DIA_ID"]:$vo->DIA_ID); ?>"><?php echo (csubstr(strip_tags(is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT),0,80)); ?> </a></td>
					<td>
					    <a href="/index.php/Diary/edit/DIA_ID/<?php echo (is_array($vo)?$vo["DIA_ID"]:$vo->DIA_ID); ?>">修改</a>
						<a href="javascript:delete_diary(<?php echo (is_array($vo)?$vo["DIA_ID"]:$vo->DIA_ID); ?>);"> 删除</a>
					</td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="4"><?php echo ($page); ?></th>
				</tr>
			</tfoot>
    
   </table>
</div>
</body>
</html>