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
<style type="text/css">
.calendar{width:220px;height:100px}
.calendar td,.calendar th{padding:0px;height:16px;line-height:18px;text-align:center;cursor:pointer}

</style>
</head>

<body>
	<ul class="dm_submenuul">
		<li class="dm_on"><a href="/index.php/Sale/lists/type/1" title=""><span>产品销售记录列表</span></a></li>
		<li><a href="/index.php/Sale/lists" title=""><span>服务销售记录列表</span></a></li>
<!--		<li><a href="/index.php/Sale/create/type/1" title=""><span><?php if($update): ?>修改<?php else: ?>新建<?php endif; ?>产品销售记录</span></a></li>
		<li><a href="/index.php/Sale/create" title=""><span><?php if($update): ?>修改<?php else: ?>新建<?php endif; ?>服务销售记录</span></a></li>-->
	</ul>

<form action="/index.php/Sale/search/type/1" method="post">
<table>
	<caption class="nostyle">产品销售记录查找</caption>
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
	<td>产品名称：</td>
	<td><input name='product_name' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>合同名称：</td>
	<td><input name='account_name' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>销售日期：</td>
	<td>		<fieldset class="date">
		      	<input type="text" name="date_from" id="sel1" /><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="return showCalendar('sel1', '%Y-%m-%d');"  /> <span>到</span> <input type="text" name="date_to" id="sel3" size="30" /><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="" onclick="return showCalendar('sel3', '%Y-%m-%d');"  />
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
<form action="/index.php/Sale/del/type/1" method='post' name='MassUpdate'  id="MassUpdate">
<table>
	<caption class="nostyle">产品销售记录列表</caption>
	<colgroup>
		<col width="20" />
		<col width="12%" />
		<col width="16%" />
		<col width="12%" />
		<col width="10%" />
		<col width="10%" />
		<col width="16%" />
		<col width="14%" />
		<col width="14%" />
	</colgroup>
	<thead>
		<tr><td colspan="9"><?php echo ($page); ?></td></tr>
		<tr>
			<th><fieldset class="check"><input type='checkbox' class='checkbox' name='massall' value='' onclick='checkAll(document.MassUpdate, "mass[]", this.checked)' /></fieldset></th>
			<th><a href="<?php echo ($sort_url); ?>&field=id">产品销售记录</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=product_name">产品名称</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=account_name">合同名称</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=sale_price">销售单价</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=sale_num">销售数量</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=time_sale">销售日期</a></th>
			<th>已回款</th>
			<th>未回款</th>
		</tr>
	</thead>
	<?php if(is_array($sale_res)): ?><?php $i = 0;?><?php $__LIST__ = $sale_res?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
			<td><fieldset class="check"><input type='checkbox' class='checkbox' name='mass[]' value="<?php echo ($vo['id']); ?>" /></fieldset></td>
			<td ><a href="/index.php/Sale/view/id/<?php echo ($vo['id']); ?>/type/1" class="listViewTdLinkS1"><?php echo ($vo['id']); ?></a></td>
			<td ><a href="/index.php/Product/view/id/<?php echo ($vo['product_id']); ?>" class="listViewTdLinkS1"><?php echo ($vo['product_name']); ?></a></td>
			<td><!--<a href="/index.php/Account/view/id/<?php echo ($vo['account_id']); ?>" class="listViewTdLinkS1">&nbsp;</a>--><?php echo ($vo['account_name']); ?></td>
			<td ><?php echo ($vo['sale_price']); ?></td>
			<td><?php echo ($vo['sale_num']); ?></td>
		    <td><?php echo ($vo['time_sale']); ?></td>
		    <td <?php if(($vo['payment'])  >  "0"): ?>style="color:red;"<?php endif; ?>><?php echo ($vo['payment']); ?></td>
		    <td <?php if(($vo['no_payment'])  >  "0"): ?>style="color:green;"<?php endif; ?>><?php echo ($vo['no_payment']); ?></td>
		</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
	<tfoot>
		<tr>
			<td colspan="9"><?php echo ($page); ?></td>
		</tr>
		<tr>
			<th colspan="9">
				<button onclick='checkAll(document.MassUpdate, "mass[]", 1);' type="button">全选</button>
				<button onclick='checkAll(document.MassUpdate, "mass[]", 0);' type="button">取消选中</button>
				<button type='submit' onclick="return del_records(this.form,'mass[]');">删除</button>
			</th>
		</tr>
	</tfoot>

</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

</body></html>