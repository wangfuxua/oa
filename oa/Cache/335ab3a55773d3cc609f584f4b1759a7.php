<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>投票管理</title>
<meta name="author" content="Jay" />
<link href="/oa/Tpl/default/Public/style/default/css/kdstyle.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KD.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js"></script>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "投票",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "已发布的投票", Url: "/index.php/vote/voteIndex", Cls: "", Icon: "", IconCls: "ico ico-voteList" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56); 
    
    });

</script>
</head>

<body>

	<div class="KDStyle" id="main">
		<table>
			<col width="80px" />
			<col />
			<col width="60px" />
			<col width="110px" />
			<col width="110px" />
			<col width="30px" />
			<thead>
				<tr>
					<th>发送人</th>
					<th>标题</th>
					<th>匿名</th>
					<th>生效日期</th>
					<th>终止日期</th>
					<th>投票</th>
				</tr>
			</thead>
			<tbody><?php if(is_array($voteList)): ?><?php $i = 0;?><?php $__LIST__ = $voteList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
					<td class="tcenter"><?php echo (getUsername(is_array($vo)?$vo["FROM_ID"]:$vo->FROM_ID)); ?></td>
					<td class="tleft"><a href="/index.php/vote/voteRead/VOTE_ID/<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>"><?php echo (is_array($vo)?$vo["SUBJECT"]:$vo->SUBJECT); ?></a></td>
					<td class="tcenter"><?php if($vo[ANONYMITY] == 'on'): ?>允许<?php else: ?>不允许<?php endif; ?></td>
					<td class="tcenter"><?php echo (is_array($vo)?$vo["BEGIN_DATE"]:$vo->BEGIN_DATE); ?></td>
					<td class="tcenter"><?php if($vo[END_DATE] == '0000-00-00'): ?><?php else: ?><?php echo (is_array($vo)?$vo["END_DATE"]:$vo->END_DATE); ?><?php endif; ?></td>
					<td class="tcenter"><a href="/index.php/vote/voteRead/VOTE_ID/<?php echo (is_array($vo)?$vo["VOTE_ID"]:$vo->VOTE_ID); ?>">投票</a></td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
			 <tr>
			 <th colspan="6"><?php echo ($page); ?></th>
			 </tr>
			</tfoot>
		</table>
	</div>
</body>
</html>