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

<script>
function delete_news(NEWS_ID)
{
 msg='确认要删除该项新闻么？';
 if(window.confirm(msg))
 {
  URL="/index.php/News/del/id/" + NEWS_ID;
  window.location=URL;
 }
}


function delete_all()
{
 msg='确认要删除所有新闻么？';
 if(window.confirm(msg))
 {
  URL="/index.php/News/delAll";
  window.location=URL;
 }
}
</script>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "新闻",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "查看已发布新闻", Url: "/index.php/News/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "添加新闻", Url: "/index.php/News/create", Cls: "", IconCls: "ico ico-add" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56); 
    
    });

</script>
<body>

<div id="main" class="KDStyle">

		<form method="post" action="">
		<table class="">
			<caption></caption>
			<colgroup>
				<col></col>
				<col width="100"></col>
				<col width="120"></col>
				<col width="60"></col>
				<col width="100"></col>
			</colgroup>
			<thead>
				<tr>
					<th>标题</th>
					<th>发布人</th>
					<th>发布时间</th>
					<th>点击次数</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody><?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
					<td><a href="/index.php/News/article/id/<?php echo (is_array($vo)?$vo["newId"]:$vo->newId); ?>"><?php echo (is_array($vo)?$vo["title"]:$vo->title); ?></a></td>
					<td class="tcenter"><?php echo (is_array($vo)?$vo["PROVIDER"]:$vo->PROVIDER); ?></td>
					<td class="tcenter"><?php echo (date("Y-m-d H:i:s",is_array($vo)?$vo["createTime"]:$vo->createTime)); ?></td>
					<td class="tcenter"><?php echo (is_array($vo)?$vo["CLICK_COUNT"]:$vo->CLICK_COUNT); ?></td>
					<td class="tcenter">
						<a href="/index.php/News/upload/id/<?php echo (is_array($vo)?$vo["newId"]:$vo->newId); ?>">修改</a> 
						<a href="javascript:delete_news('<?php echo (is_array($vo)?$vo["newId"]:$vo->newId); ?>');">删除</a>
					</td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5">
						<button name="Abutton1" onClick="delete_all()"><div><span>全部删除</span></div></button><?php echo ($page); ?>
					</th>
				</tr>
			</tfoot>
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>
</body>
</html>