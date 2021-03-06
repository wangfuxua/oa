<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>供应商列表</title>

<link href="/oa/Tpl/default/Public/css/default.css" rel="stylesheet" type="text/css" />
<script src ="/oa/Tpl/default/Public/js/crm_common.js"></script>
<script src="/oa/Tpl/default/Public/js/crm_data.js"></script>
<script src="/oa/Tpl/default/Public/js/calendar.js"></script>
<script src="/oa/Tpl/default/Public/js/calendar-zh.js"></script>
<script type="text/javascript">
function selected(cal, date) {
  cal.sel.value = date; 
  if (cal.dateClicked)
    cal.callCloseHandler();
}

function closeHandler(cal) {
  cal.hide();         
  _dynarch_popupCalendar = null;
}

function showCalendar(id, format, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    _dynarch_popupCalendar.hide();                 
  } else {
    var cal = new Calendar(1, null, selected, closeHandler);
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  
    cal.setRange(1900, 2070);        
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    
  _dynarch_popupCalendar.parseDate(el.value);      
  _dynarch_popupCalendar.sel = el;                 

  _dynarch_popupCalendar.showAtElement(el.nextSibling, "Br");        

  return false;
}
</script>
<style type="text/css">
.calendar{width:220px;height:100px}
.calendar td,.calendar th{padding:0px;height:16px;line-height:18px;text-align:center;cursor:pointer}

</style>
</head>

<body>
<!--	<ul class="dm_submenuul">
		<li class="dm_on"><a href="/index.php/Contract/lists" title=""><span>合同记录列表</span></a></li>
		<li><a href="/index.php/Contract/create" title=""><span><?php if($update): ?>修改<?php else: ?>新建<?php endif; ?>合同记录</span></a></li>
	</ul>-->

<form action="/index.php/Contract/search" method="post">
<table>
	<caption class="nostyle">合同记录查找</caption>
	<colgroup>
		<col width="15%" />
		<col width="10%" />
		<col width="15% " />
		<col width="10%" />
		<col width="10% " />
		<col width="40%" />
	</colgroup>
<tbody>
	<tr>
	<td>合同编号：</td>
	<td><input name='code' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>合同名称：</td>
	<td><input name='name' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>客户名称：</td>
	<td><input name='account_name' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	</tr>
	<tr>
	<td>日期类型：<select name="time_type"><option value="star_time">生效日期</option><option value="end_time">终止日期</option><option value="time_create">创建日期</option></select></td>
	<td colspan="5"><fieldset class="date">
		      	<input type="text" name="date_from" id="date_from" /><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="return showCalendar('date_from', '%Y-%m-%d');"  /> <span>到</span> <input type="text" name="date_to" id="date_to" size="30" /><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="" onclick="return showCalendar('date_to', '%Y-%m-%d');"  />
		 </fieldset></td>
	</tr>
</tbody>
	<tfoot>
		<tr>
			<th colspan="6">
				<button title="查找 [Alt+Q]" type="submit">查找</button>
				<button title="重置 [Alt+C]" type="reset" >重置</button>
			</th>
		</tr>
	</tfoot>
</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<form action="/index.php/Contract/del" method='post' name='MassUpdate'  id="MassUpdate">
<table>
	<caption class="nostyle">合同记录列表</caption>
	<colgroup>
	<!--	<col width="20" />-->
		<col width="6%" />
		<col width="16%" />
		<col width="16%" />
		<col width="16%" />
		<col width="16%" />
		<col width="16%" />
		<col width="12%" />
	</colgroup>
	<thead>
		<tr><td colspan="8"><?php echo ($page); ?></td></tr>
		<tr>
<!--			<th><fieldset class="check"><input type='checkbox' class='checkbox' name='massall' value='' onclick='checkAll(document.MassUpdate, "mass[]", this.checked)' /></fieldset></th>-->
			<th><a href="<?php echo ($sort_url); ?>&field=code">合同编号</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=name">合同名称</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=account_name">客户名称</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=star_time">生效日期</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=end_time">终止日期</a></th>
			<th>已付款</th>
			<th>未付款</th>
		</tr>
	</thead>
	<?php if(is_array($contract_res)): ?><?php $i = 0;?><?php $__LIST__ = $contract_res?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
		<!--	<td><fieldset class="check"><input type='checkbox' class='checkbox' name='mass[]' value="<?php echo ($vo['id']); ?>" /></fieldset></td>-->
			<td ><a href="/index.php/Contract/view/id/<?php echo ($vo['id']); ?>" class="listViewTdLinkS1"><?php echo ($vo['code']); ?></a></td>
			<td ><a href="/index.php/Contract/view/id/<?php echo ($vo['id']); ?>" class="listViewTdLinkS1"><?php echo ($vo['name']); ?></a></td>
			<td><a href="/index.php/Account/view/id/<?php echo ($vo['account_id']); ?>" class="listViewTdLinkS1">&nbsp;<?php echo ($vo['account_name']); ?></a></td>
			<td ><?php echo ($vo['star_time']); ?></td>
			<td><?php echo ($vo['end_time']); ?></td>
		    <td style="color:red;"><?php echo ($vo['payment']); ?></td>
		    <td style="color:green;"><?php echo ($vo['no_payment']); ?></td>
		</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
	<tfoot>
		<tr>
			<td colspan="8"><?php echo ($page); ?></td>
		</tr>
<!--		<tr>
			<th colspan="9">
				<button onclick='checkAll(document.MassUpdate, "mass[]", 1);' type="button">全选</button>
				<button onclick='checkAll(document.MassUpdate, "mass[]", 0);' type="button">取消选中</button>
				<button type='submit' onclick="return del_records(this.form,'mass[]');">删除</button>
			</th>
		</tr>-->
	</tfoot>

</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

</body></html>