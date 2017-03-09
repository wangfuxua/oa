<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>客户反馈列表</title>
<link href="/oa/Tpl/default/Public/css/default.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.calendar{width:220px}
.calendar td,.calendar th{padding:0px;height:16px;line-height:18px;text-align:center;cursor:pointer}
</style>
<script type="text/javascript" src ="/oa/Tpl/default/Public/js/crm_common.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/crm_data.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/calendar.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/calendar-zh.js"></script>
<script type="text/javascript">
function selected(cal, date) {
  cal.sel.value = date; 
  if (cal.dateClicked && (cal.sel.id == "sel1" || cal.sel.id == "sel3"))
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
</head>

<body>
	<ul class="dm_submenuul">
		<li class="dm_on"><a href="/index.php/Report/lists" title=""><span>客户反馈列表</span></a></li>
		<li><a href="/index.php/Report/create" title=""><span>新建客户反馈</span></a></li>
	</ul>
<form action="/index.php/Report/search" method="post">
<table>
	<caption class="nostyle">查找客户反馈</caption>
	<colgroup>
		<col width="20%" />
		<col width="30%" />
		<col width="20% " />
		<col width="30%" />
	</colgroup>
	<tbody>
		<tr>
			<td>主题：</td>
			<td><input name='subject' tabindex='1' size='25' maxlength='25' type="text" value=""></td>
			<td>客户名称：</td>
			<td><input name='account_name' type="text" tabindex='2' size='20' maxlength='25' value=''></td>
		</tr>
	</tbody>
	<tbody id="searchAll" style="display:none;">
	<tr>
		<td>反馈编号：</td>
		<td><input name='id' type="text" tabindex='1' size='25' maxlength='25' value="">
		</td>
		<td>状态：</td>
		<td ><!--<script>change_selected('status', report_status, "");</script>-->
			<select name="status">
			<option value="">请选择</option>
			<?php if(is_array($xml_sta)): ?><?php $i = 0;?><?php $__LIST__ = $xml_sta?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo_sta): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo_sta['name']); ?>"><?php echo ($vo_sta['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</select></td>
	</tr>
	<tr>
		<td>优先级：</td>
		<td >
<!--		<script>
		change_selected('priority', priority, "");
		</script>-->
			<select name="priority">
			<option value="">请选择</option>
			<?php if(is_array($xml_pri)): ?><?php $i = 0;?><?php $__LIST__ = $xml_pri?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo_pri): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo_pri['name']); ?>"><?php echo ($vo_pri['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</select>
		</td>
		<td >最后更新：</td>
		<td >
		      <fieldset class="date">	<input type="text" name="date_from" id="sel1" size="30"><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="return showCalendar('sel1', '%Y-%m-%d');"  /><span> 到 </span><input type="text" name="date_to" id="sel3" size="30"><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="return showCalendar('sel3', '%Y-%m-%d');"  /></fieldset>
		</td>	
	</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="4">
			        <button tabindex='2' title="查找" class="button" type="submit">查找</button>
			        <button tabindex='2' title="重置"  class="button" type="reset" >重置</button>
			        <button tabindex='2' title="高级查找" class="button" type="button" onclick="displayOrNo('searchAll','block');">高级查找</button>
			</th>
		</tr>
	</tfoot>
</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<form action="/index.php/Report/del" method='post' name='MassUpdate'  id="MassUpdate">
 <table>
	<caption class="nostyle">客户反馈列表</caption>
	<colgroup>
		<col width="20" />
		<col width="8%" />
		<col width="" />
		<col width="" />
		<col width="15%" />
		<col width="15%" />
		<col width="10%" />
	
	</colgroup>
	<thead>
		<tr>
			<td colspan="7"><?php echo ($page); ?></td>
		</tr>
		<tr>
			<th><fieldset class="check"><input type='checkbox' class='checkbox' name='massall' value='' onclick='checkAll(document.MassUpdate, "mass[]", this.checked)'></fieldset></th>
			<th><a href="<?php echo ($sort_url); ?>&field=id">反馈编号&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=subject">主题&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=account_name">客户名称&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=priority">优先级&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=status">状态&nbsp;</a></th>
			<th>负责人&nbsp;</th>
		</tr>
	</thead>
	<?php if(is_array($report_res)): ?><?php $i = 0;?><?php $__LIST__ = $report_res?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
		<td><fieldset class="check"><input type='checkbox' class='checkbox' name='mass[]' value="<?php echo ($vo['id']); ?>"></fieldset></td>
		<td>&nbsp;<?php echo ($vo['id']); ?></td>
	            <td ><a href="/index.php/Report/view/id/<?php echo ($vo['id']); ?>" class="listViewTdLinkS1"> <?php echo ($vo['subject']); ?></a></td>
	            <td ><a href="/index.php/Account/view/id/<?php echo ($vo['account_id']); ?>" class="listViewTdLinkS1"><?php echo ($vo['account_name']); ?></a></td>
	            <td ><?php echo ($vo['priority']); ?></td>
	            <td ><?php echo ($vo['status']); ?></td>
	    	<td ><?php echo ($vo['user_name']); ?></td>
	</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
	<tfoot>
		<tr>
			<td colspan="7"><?php echo ($page); ?></td>
		</tr>
		<tr>
			<th colspan="7">
				<button type="button"  onclick='checkAll(document.MassUpdate, "mass[]", 1);'>全选</button>
				<button type="button"  onclick='checkAll(document.MassUpdate, "mass[]", 0);'>取消选中</button>
				<button type='submit'  onclick="return del_records(this.form,'mass[]');">删除</button>
			</th>
		</tr>
	</tfoot>
</table>

<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>


</body></html>