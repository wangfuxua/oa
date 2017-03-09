<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Jay" />
<title>工资上报管理</title>

<link href="/oa/Tpl/default/Public/style/default/css/kdstyle.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KD.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/main.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
<script>
function CheckForm()
{
   if(document.form1.BEGIN_DATE.value=="")
   { alert("上报起始日期不能为空！");
     return (false);
   }

   if(document.form1.END_DATE.value=="")
   { alert("上报截止日期不能为空！");
     return (false);
   }

   return (true);
}
function delete_flow(FLOW_ID)
{
 msg='确认要删除该流程吗？通过该流程上报的工资数据将被删除且不可恢复！';
 if(window.confirm(msg))
 {
  URL="/index.php/salary/flowDel/FLOW_ID/" + FLOW_ID;
  window.location=URL;
 }
}

function stop_flow(FLOW_ID)
{
 msg='确认要终止该流程吗？将不可恢复为执行状态！';
 if(window.confirm(msg))
 {
  URL="/index.php/salary/flowStop/FLOW_ID/" + FLOW_ID;
  window.location=URL;
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
        Active: 2,
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

</head>
<body>

	<div class="KDStyle" id="main">
	<form action="flowAdd" method="post" name="form1" onsubmit="return CheckForm();">
		<table>
			<thead>
				<tr>
					<th colspan="2" >新建工资上报流程 </th>
				</tr>
			</thead>
			<tbody>
					<tr>
						<td width="20%">上报起始日期：</td><td><input name="BEGIN_DATE" type="text" class="dm_number5"   onfocus="WdatePicker({dateFmt:'yyyy-MM-dd'});"/>
						<img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="选择时间" style="cursor:hand" onclick="WdatePicker({el:$dp.$('BEGIN_DATE'),dateFmt:'yyyy-MM-dd'})"  />
						
						</td>
					</tr>
					<tr>
						<td>上报截止日期：</td><td><input name="END_DATE" type="text" class="dm_number5"   onfocus="WdatePicker({dateFmt:'yyyy-MM-dd'});"/>
						<img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="选择时间" style="cursor:hand" onclick="WdatePicker({el:$dp.$('END_DATE'),dateFmt:'yyyy-MM-dd'})"  />
						</td>
					</tr>
					<tr>
						<td>备注：</td><td><input name="CONTENT" type="text" value="" /></td>
					</tr>
    <tr>
      <td> 提醒：</td>
      <td>
        <input type="checkbox" name="SMS_REMIND" checked>使用短信息提醒相关人员上报工资数据
      </td>
    </tr>
    					
			</tbody>
			<tfoot>
					<tr>
						<th colspan="2" class="dm_btnzan">
						<button name="Abutton1" type="submit">新建</button></th>
					</tr>
			</tfoot>
		</table>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th colspan="6" >已定义的工资上报流程  </th>
				</tr>
			</thead>
			<tbody>
					<tr>
						<td>流程创建日期</td>
						<td>起始日期</td>
						<td>截止日期</td>
						<td>备注</td>
						<td>状态</td>
						<td>操作</td>
					</tr>
					<?php if(is_array($flowList)): ?><?php $i = 0;?><?php $__LIST__ = $flowList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
					    <td><?php echo (formatdate($vo['SEND_TIME'])); ?></td><td><?php echo (formatDate($vo['BEGIN_DATE'],'full')); ?></td><td><?php echo (formatDate($vo['END_DATE'],'full')); ?></td>
						<td><?php echo ($vo['CONTENT']); ?></td>
						<td>
						<?php $RUNNING=0?>
						<?php if($vo[BEGIN_DATE] > time()): ?>待执行
						<?php elseif($vo[END_DATE] > time()): ?>执行中<?php $RUNNING=1?>
						<?php elseif($vo[END_DATE] == 0): ?>执行中<?php $RUNNING=1?>
						<?php else: ?>已终止<?php endif; ?>
						</td>
						<td><a href="flowReport/FLOW_ID/<?php echo (is_array($vo)?$vo["FLOW_ID"]:$vo->FLOW_ID); ?>">工资报表</a>
						    <?php
						    if ($RUNNING==1){ 
						    ?>
					        <a href="javascript:stop_flow(<?php echo (is_array($vo)?$vo["FLOW_ID"]:$vo->FLOW_ID); ?>);">终止</a>    
					        <?php
						    }
					        ?>
					        <a href="javascript:delete_flow(<?php echo (is_array($vo)?$vo["FLOW_ID"]:$vo->FLOW_ID); ?>);">删除</a>
					    </td>
					</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>

			</tbody>
			<tfoot>
					<tr>
						<th colspan="6" class="dm_btnzan">
						<?php echo ($page); ?>
						</th>
					</tr>
			</tfoot>
		</table>

</div>
</body>
</html>