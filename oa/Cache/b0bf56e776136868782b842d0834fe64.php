<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ($curtitle); ?></title>
		
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<meta http-equiv="Content-Language" content="zh-CN" />
		<meta name="author" content="Jay" />
		<link rel="stylesheet" type="text/css" href="/oa/Tpl/default/Public/css/default.css" />
				<script src="/oa/Tpl/default/Public/js/js.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/css/validator.css"></link>
<script src="/oa/Tpl/default/Public/js/formValidator.js" type="text/javascript" charset="UTF-8"></script>
<script src="/oa/Tpl/default/Public/js/formValidatorRegex.js" type="text/javascript" charset="UTF-8"></script>
<script language="javascript" src="/oa/Tpl/default/Public/js/DateTimeMask.js" type="text/javascript"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>	
	</head>
 
<title>销售记录</title>
<link href="/oa/Tpl/default/Public/css/addcentcater.css" rel="stylesheet" type="text/css" />  
<link href="/oa/Tpl/default/Public/css/default.css" rel="stylesheet" type="text/css" />
<link href="/oa/Tpl/default/Public/style/default/css/KDailog.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/oa/Tpl/default/Public/js/crm_data.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/crm_common.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/dialog/dialog.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KDailog.js"></script>

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
<!--		<li><a href="/index.php/Sale/lists/type/1" title=""><span>产品销售记录列表</span></a></li>
		<li><a href="/index.php/Sale/lists" title=""><span>服务销售记录列表</span></a></li>-->
		<li class="dm_on"><a href="/index.php/Sale/create/type/1" title=""><span><?php if($update): ?>修改<?php else: ?>新建<?php endif; ?>产品销售记录</span></a></li>
		<li><a href="/index.php/Sale/create" title=""><span><?php if($update): ?>修改<?php else: ?>新建<?php endif; ?>服务销售记录</span></a></li>
	</ul>

<form method="POST" name="form1" action='<?php if($update): ?>/index.php/Sale/update/type/1<?php else: ?>/index.php/Sale/save/type/1<?php endif; ?>' onSubmit="return idNull(Array('record_num','product_name','sale_price','sale_num','account_name','sel1','manager_name'),Array('record_num_msg','product_name_msg','sale_price_msg','sale_num_msg','account_name_msg','sel1_msg','manager_name_msg'),Array('记录号不为空!','产品名不能为空!','售价不为空!','销售数量不为空!','合同名不为空!','销售日期不为空!','销售员不为空!'));">		
<table>
<colgroup>
	<col width="15%" />
	<col width="" />
</colgroup>
<thead>
	<tr>
		<td colspan="4" class="tdleft">
			<input type="hidden" name="sale_id" value="<?php echo ($sale_res['id']); ?>" />
			<button type="submit" >保存</button>
			<button  type="reset" >重置</button>
			<label>*代表必填项</label>

		</td>
	</tr>
</thead>
	<tr><th class="tdtit" colspan="4"><h4>基本信息<font size="1" style="color:red;">(记录建立后基本信息中必选项不能修改,请认真填写)</font></h4></th></tr>
	<tr>
      <td>记录号：<span class="required" style="color:red;">*</span></td>
      <td>
      	<input type="text" name="record_num" id="record_num" value="<?php echo ($sale_res['record_num']); ?>" <?php if($sale_res['id']): ?>readonly<?php endif; ?>><span id="record_num_msg" style="color:red;"></span>
      </td>
		<td>产品：<span class="required" style="color:red;">*</span></td>
		<td >       
		<input type="hidden" name="product_id" id="product_id" value="<?php echo ($sale_res['product_id']); ?>">
        <input type="text" name="product_name" id="product_name" value="<?php echo ($sale_res['product_name']); ?>" readonly>
       <?php if($sale_res['id']): ?><?php else: ?><button type="button" onClick="KDwin('/index.php/Product/lists/to_id/product_id/to_name/product_name',500,350,'选择产品');">选择</button><?php endif; ?><span id="product_name_msg" style="color:red;"></span></td>
	</tr>	
   <tr>
      <td>单价：<span class="required" style="color:red;">*</span></td>
      <td>
      	<input type="text" name="sale_price" id="sale_price" value="<?php echo ($sale_res['sale_price']); ?>" <?php if($sale_res['id']): ?>readonly /><span id="sale_price_msg" style="display:none;"></span><?php else: ?>onblur="is_float('sale_price','sale_price_msg','请输入正确的浮点数!');"  /><span id="sale_price_msg" ></span><?php endif; ?>
      </td>
      <td>数量：<span class="required" style="color:red;">*</span></td>
      <td>
      	<input type="text" name="sale_num" id="sale_num" value="<?php echo ($sale_res['sale_num']); ?>" <?php if($sale_res['id']): ?>readonly /><span id="sale_num_msg" style="display:none;"></span><?php else: ?> onblur="is_float('sale_num','sale_num_msg','请输入正确的浮点数!');" /><span id="sale_num_msg" ></span><?php endif; ?>
      </td>
   </tr>	
    <tr>
      <td>合同名称：<span class="required" style="color:red;">*</span></td>
      <td>
		<input type="hidden" name="account_id" id="account_id" value="<?php echo ($sale_res['account_id']); ?>">
        <input type="text" name="account_name" id="account_name" value="<?php echo ($sale_res['account_name']); ?>" readonly>
        <?php if($sale_res['id']): ?><?php else: ?><button type="button" onClick="KDwin('/index.php/Contract/lists/to_id/account_id/to_name/account_name',500,350,'选择合同');">选择</button><?php endif; ?><span id="account_name_msg" style="color:red;"></span>
      </td>
      <td>销售日期：<span class="required" style="color:red;">*</span></td>
      <td>
      	<input type="text" name="time_sale" id="sel1" value="<?php echo ($sale_res['time_sale']); ?>" readonly /><?php if($sale_res['id']): ?><?php else: ?><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="return showCalendar('sel1', '%Y-%m-%d');"  /><?php endif; ?><span id="sel1_msg" style="color:red;"></span>
      </td>
   </tr>
    <tr>
      <td>销售员：<span class="required" style="color:red;">*</span></td>
      <td  colspan="3">
      	<input type="hidden" id="one_or_more" value="y">
      	<input type="hidden" name="seller_id" id="manager_id" value="<?php echo ($sale_res['uid']); ?>">
      	<input type="text" name="seller_name" id="manager_name" value="<?php echo ($sale_res['USER_NAME']); ?>" readonly>
      	<?php if($sale_res['id']): ?><?php else: ?><button title="选择" type="button"  onclick="setInput('manager_id','manager_name','right_show1','true')"/>选择</button><?php endif; ?><span id="manager_name_msg" style="color:red;"></span>
      </td>
   </tr>
    <tr>
      <td>备注：</td>
      <td colspan="3"><textarea name='notation' tabindex='5' cols="60" rows="8"><?php echo ($sale_res['notation']); ?></textarea>
      </td>
   </tr>
   <tr><th class="tdtit" colspan="4"><h4>回款记录<font size="1" style="color:red;">(如果添加,带*项必填,回款记录建立后不可修改)</font></h4></th></tr>
  
	<tr>
      <td colspan="4">
      	<table id="payment_record">
      	<tr>
      	<td>当前回款<span class='required' style="color:red;">*</span></td>
      	<td>回款日期<span class='required' style="color:red;">*</span></td>
      	<td>未结款</span></td>
      	<td></td>
      	</tr>
      	<?php if(is_array($payment_res)): ?><?php $key = 0;?><?php $__LIST__ = $payment_res?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$pay_vo): ?><?php ++$key;?><?php $mod = (($key % 2 )==0)?><tr>
      	<td><input type='text' style="width:80px;" size='10' name='payment[]' id='payment_<?php echo ($key); ?>' value="<?php echo ($pay_vo['payment']); ?>"  readonly /><span id='payment_<?php echo ($key); ?>_msg' ></span></td>
      	<td><input type='text' style="width:80px;" size='8' name='pay_time[]' value="<?php echo ($pay_vo['pay_time']); ?>" id='pay_time_<?php echo ($key); ?>' readonly /><!--<img src='/oa/Tpl/default/Public/images/ico/calendar.png' alt=''  onclick="return showCalendar('pay_time_<?php echo ($key); ?>', '%Y-%m-%d');"  />--></td>
      	<td><input type='text' style="width:80px;" size='10' name='no_payment[]' id='debt_<?php echo ($key); ?>' value="<?php echo ($pay_vo['no_payment']); ?>" readonly/></td>
      	<td><input type="hidden" name="pay_id[]" style="width:10px;" value="<?php echo ($pay_vo['id']); ?>"><!--<button title='删除回款记录'  type='button' onclick='delete_row(this.parentNode.parentNode.rowIndex);' >删除</button>--></td>
      	</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
      	</table>
      </td>
	</tr> 
		<tr>
      <td colspan="4"><button title="添加回款记录"  type="button" onClick="add_row();" >添加回款记录</button></td>
	</tr> 
</table>

<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>


<link href="/oa/Tpl/default/Public/css/addcentcater.css" rel="stylesheet" type="text/css" />
<!--弹出框开始--> 
<div id="addDrug" style="display:none"></div>
<div id="addbox" style="display:none"> 
		<div id="titlebar">
		   <h2>添加联系人</h2>
		   <span class="closed"><img src="/oa/Tpl/default/Public/newimg/mail_close.gif" width="17" height="17" alt="close" /></span>
		</div>
		<div id="top-sh">
			<ul class="clearfix">
				<li class="all_c"><a href="#">全部</a></li>
				<li><a href="#">A</a></li>
				<li><a href="#">B</a></li>
				<li><a href="#">C</a></li>
				<li><a href="#">D</a></li>
				<li><a href="#">E</a></li>
				<li><a href="#">F</a></li>
				<li><a href="#">G</a></li>
				<li><a href="#">H</a></li>
				<li><a href="#">I</a></li>
				<li><a href="#">J</a></li>
				<li><a href="#">K</a></li>
				<li><a href="#">L</a></li>
				<li><a href="#">M</a></li>
				<li><a href="#">N</a></li>
				<li><a href="#">O</a></li>
				<li><a href="#">P</a></li>
				<li><a href="#">Q</a></li>
				<li><a href="#">R</a></li>
				<li><a href="#">S</a></li>
				<li><a href="#">T</a></li>
				<li><a href="#">U</a></li>
				<li><a href="#">V</a></li>
				<li><a href="#">W</a></li>
				<li><a href="#">X</a></li>
				<li><a href="#">Y</a></li>
				<li><a href="#">Z</a></li>
     		</ul>
     	</div>    
		<div class="selectbox clearfix">
			<div class="selectboxbder1">
			  	<div id="waitselect">
			    	<p class="ss"><input type="text" value="输入搜索内容汉字或拼音" name="" id="sc-name"  onclick="slect()"/><span id="go-sc"><img src="/oa/Tpl/default/Public/newimg/mail_input_sc_bg.gif" width="18" height="16" /></span></p>    
			    	<h3>金凯通达（北京）国际投资有限公司</h3>
				    <div class="selecte-btn">
				      	<h4 class="over">按部门选择</h4><span id="selecte-all-part"><a href="#">全选</a></span>
				    </div>
					<div id="bumen">
						<ul> <?php echo ($list_d); ?> </ul>    
				  	</div> 
				    <div class="selecte-btn">
				    	<h4>按角色选择</h4><span id="selecte-all-role"><a href="#">全选</a></span> 
				    </div> 
				    <div id="juese" style="display:none">      
				        <ul> <?php echo ($list_p); ?> </ul>    
				    </div> 
			  	</div> 
			</div> 
			<div class="selectboxbder3">
			    <div id="rel">
			        <h5>选择部门或角色</h5>
			         	<ul> </ul>
			        <span onclick="selectAll(this)" style="position:absolute; right:10px; top:8px; color:#f60; cursor:pointer">全选</span>
			    </div>
			</div> 
			<div class="selectboxbder2">
			  	<div id="selected">
						<div class="tl">
						<h3>已添加联系人：</h3><span>清空</span>
						</div> 
						<div id="sld-list">
							<ul class="bumen">
							</ul>
							<ul class="juese">
							</ul>
							<ul class="renyuan">
							</ul>
						</div>    
			  	</div>
			</div>
		</div> 
		<p class="subotton"><button type="button" id="qd">确定</button><button type="button" id="qx">取消</button></p>
</div>
<script type="text/javascript">
var person={ 
	<?php echo ($js_list); ?>
   }   
</script>
<script src="/oa/Tpl/default/Public/js/writeAndaddcter.js" type="text/javascript"></script>
<!--弹出框结束--> 
</body></html>
<script>
var payment_row = 0;
function add_row(){
	payment_row++;
	var tableName = document.getElementById('payment_record');
	var prev = tableName.rows.length;
    var count = prev;
    var row = tableName.insertRow(prev);
	row.id = "payment"+count;
	var col1 = row.insertCell(0);
	var col2 = row.insertCell(1);
	var col3 = row.insertCell(2);
	var col4 = row.insertCell(3);
	
	col1.innerHTML="<input type='text' style='width:80px;' size='10' name='payment[]' id='payment_"+prev+"' onblur=\"count_and_debt('sale_price','sale_num','payment_"+prev+"','debt_"+prev+"');\" /><span id='payment_"+prev+"_msg' ></span>";
	col2.innerHTML="<input type='text' style='width:80px;' size='8' name='pay_time[]' id='pay_time_"+prev+"' /><img src='/oa/Tpl/default/Public/images/ico/calendar.png' alt=''  onclick=\"return showCalendar('pay_time_"+prev+"', '%Y-%m-%d');\"  />";
	col3.innerHTML="<input type='text' style='width:80px;' size='10' name='no_payment[]' id='debt_"+prev+"' readonly/>";
	col4.innerHTML="<input type='hidden' style='width:10px;' name='pay_id[]' value=''><button title='删除回款记录'  type='button' onclick='delete_row(this.parentNode.parentNode.rowIndex);' >删除</button>";

}
function delete_row(i)
{
	 payment_row--;
	 document.getElementById('payment_record').deleteRow(i);
}

function count_and_debt(price_id,num_id,pay_id,debt_id){
	var pattern = /^[0-9]+(\.[0-9]+)*$/;
	var pay = document.getElementById(pay_id).value;
	var price = document.getElementById(price_id).value;
	var num = document.getElementById(num_id).value;	

	alertMsg(price_id,price_id+'_msg',pattern,'请填写正确的浮点数!');
	if(!pattern.test(price)) return false;
	
	alertMsg(num_id,num_id+'_msg',pattern,'请填写正确的数字!');
	if(!pattern.test(num)) return false;
	
	alertMsg(pay_id,pay_id+'_msg',pattern,'请填写正确的浮点数!');
	if(!pattern.test(pay)) return false;	
	
	var tableName = document.getElementById('payment_record');
	var prev = tableName.rows.length;
	var debt_val = debt_id.substr(5,1);
	if(prev == 2) var cou = Math.round(price*num*100)/100;
	else if(debt_val == 1) var cou = Math.round(price*num*100)/100;
	else var cou = document.getElementById('debt_'+(debt_val-1)).value;
	
	if((cou-pay) < 0){
		document.getElementById(pay_id+'_msg').innerHTML = '<span style="color:red;">回款不能大于最大欠款!</span>';
		document.getElementById(debt_id).value = '';
		return false;
	}
	document.getElementById(debt_id).value = Math.round((cou-pay)*100)/100;	
}
</script>