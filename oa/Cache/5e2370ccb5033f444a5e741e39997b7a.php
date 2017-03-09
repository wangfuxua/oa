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
	function delete_vote(VOTE_ID)
	{
		 	msg='确认要删除该项投票吗？';
		 	if(window.confirm(msg))
		 	{
		  	URL="/index.php/vote/del/VOTE_ID/" + VOTE_ID;
		 	 window.location=URL;
		 	}
 		
   }
	function stop_vote(VOTE_ID)
	{
		 	msg='确认要终止该项投票吗？';
		 	if(window.confirm(msg))
		 	{
		  	URL="/index.php/vote/stopvote/VOTE_ID/" + VOTE_ID;
		 	 window.location=URL;
		 	}
 		
   }
      
</script>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "投票管理设置",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "投票管理", Url: "/index.php/vote/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "新建投票", Url: "/index.php/vote/add", Cls: "", IconCls: "ico ico-add" }
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
		<col width="80px" />
		<col width="110px" />
		<col />
		<col width="60px" />
		<col width="60px" />
		<col width="110px" />
		<col width="110px" />
		<col width="60px" />
		<col width="170px" />
			<thead>
				<tr>
					<th>发送人</th>
					<th>发送范围</th>
					<th>标题</th>
					<th>类型</th>
					<th>匿名</th>
					<th>生效日期</th>
					<th>终止日期</th>
					<th>状态</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody><?php if(is_array($voteList)): ?><?php $i = 0;?><?php $__LIST__ = $voteList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
					<td><?php echo (getUsername(is_array($vo)?$vo["FROM_ID"]:$vo->FROM_ID)); ?></td>
					<td><?php if($vo[TO_ID] == ''): ?>所有部门<?php elseif($vo[TO_ID] == 'ALL_DEPT,'): ?>所有部门<?php elseif($vo[TO_ID] == 'all'): ?>所有部门<?php else: ?><?php echo (getList_name(is_array($vo)?$vo["TO_ID"]:$vo->TO_ID)); ?><?php endif; ?></td>
					<td><a href="show/VOTE_ID/<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>"><?php echo (is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT); ?></a></td>
					<td class="tcenter"><?php if($vo[TYPE] == 0): ?>单选<?php else: ?>多选<?php endif; ?></td>
					<td class="tcenter"><?php if($vo[ANONYMITY] == 'on'): ?>允许<?php else: ?>不允许<?php endif; ?></td>
					<td><?php echo (is_array($vo)?$vo["BEGIN_DATE"]:$vo->BEGIN_DATE); ?></td>
					<td><?php if($vo[END_DATE] == '0000-00-00'): ?><?php else: ?><?php echo (is_array($vo)?$vo["END_DATE"]:$vo->END_DATE); ?><?php endif; ?></td>
					<td class="tcenter"><?php echo (FormatTimeStatusStr($vo['BEGIN_DATE'],$vo['END_DATE'])); ?></td>
					<td class="tcenter">
						<a href="/index.php/vote/item/VOTE_ID/<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>" title="">投票项目</a>
						<a href="/index.php/vote/update/VOTE_ID/<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>" title="">修改</a>
						<a href="javascript:delete_vote('<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>');" title="">删除</a>
						
						<?php if(FormatTimeStatus($vo[BEGIN_DATE],$vo[END_DATE]) == 1): ?><a href="/index.php/vote/setvote/VOTE_ID/<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>/OP/1" title="">立即生效</a> 
						<?php elseif(FormatTimeStatus($vo[BEGIN_DATE],$vo[END_DATE]) == 2): ?>
						<a href="/index.php/vote/setvote/VOTE_ID/<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>/OP/2" title="">立即终止</a>
						<?php elseif(FormatTimeStatus($vo[BEGIN_DATE],$vo[END_DATE]) == 3): ?>
						<a href="/index.php/vote/setvote/VOTE_ID/<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>/OP/3" title="">恢复生效</a><?php endif; ?>
						
					</td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
			<tr>
			<td colspan="9"><?php echo ($page); ?></td>
			</tr>
			</tfoot>
		</table>
	</div>
</body>
</html>