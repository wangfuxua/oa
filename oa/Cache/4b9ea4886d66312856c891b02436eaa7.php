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

<title>客户列表</title>

<link href="/oa/Tpl/default/Public/css/default.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.calendar{width:220px;height:100px}
.calendar td,.calendar th{padding:0px;height:16px;line-height:18px;text-align:center;cursor:pointer}

</style>
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
</head>

<body>
<!--	<ul class="dm_submenuul">
		<li><a href="/index.php/Account/lists" title=""><span>导入客户</span></a></li>
		<li><a href="/index.php/Account/create" title=""><span>导出客户</span></a></li>
	</ul>-->
	<ul class="dm_submenuul">
		<li><a href="/index.php/Account/import" title=""><span>导入客户</span></a></li>
		<li><a href="/index.php/Account/export" title=""><span>导出客户</span></a></li>
	</ul>


<form action="/index.php/Account/search" method="post">
<table>
	<caption class="nostyle">查找客户</caption>
	<colgroup>
		<col width="20%" />
		<col width="30%" />
		<col width="20% " />
		<col width="30%" />
	</colgroup>
<tbody>
	<tr>
	<td>客户名称：</td>
	<td><input name='name' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>客户来源：</td>
	<td>
			<select name="source">
			<option value="">请选择</option>
			<?php if(is_array($xml_sou)): ?><?php $i = 0;?><?php $__LIST__ = $xml_sou?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo_sou): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo_sou['name']); ?>"><?php echo ($vo_sou['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</select>
	</td>
	</tr>
	<tr>
		<td>负责人：</td>
		<td  >
			<input id='one_or_more' type="hidden" value="y" />
			<input id='manager_id' name='manager_id' type="hidden" value="<?php echo ($account_res['uid']); ?>" />
			<input id="manager_name" type="text" value="<?php echo ($account_res['USER_NAME']); ?>" readonly/>
			<button title="添加收信人" type="button"  onclick="setInput('manager_id','manager_name','right_show1','true')"/>添加</button>	<span id='m_check'></span>
		</td>
		<td >创建时间：</td>
		<td >
			<fieldset class="date">
					<input type="text" name="create_date_from" id="sel1" /><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="return showCalendar('sel1', '%Y-%m-%d');"  /> <span>到</span> 
					<input type="text" name="create_date_to" id="sel3" size="30" /><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="" onclick="return showCalendar('sel3', '%Y-%m-%d');"  />
			 </fieldset>
		</td>
	</tr>

</tbody>
<tbody id="searchAll" style="display:none;">
	<tr>
	<td>办公电话：</td>
	<td><input name='phone_work' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td>网站：</td>
	<td><input name='website' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	</tr>
	<tr>
	<td>客户行业：</td>
	<td>
<!--<script>
change_selected('industry', industry, "");
</script>-->	
			<select name="industry">
			<option value="">请选择</option>
			<?php if(is_array($xml_ind)): ?><?php $i = 0;?><?php $__LIST__ = $xml_ind?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo_ind): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo_ind['name']); ?>"><?php echo ($vo_ind['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</select>
	</td>
	<td>员工数：</td>
	<td><input name='employees' type="text" tabindex='2' size='20' maxlength='100' value='' /></td>
	</tr>
	<tr>
	<td>客户类型：</td>
	<td>
<!--	<script>
change_selected('account_type', account_type, "");
</script>-->
			<select name="type">
			<option value="">请选择</option>
			<?php if(is_array($xml_type)): ?><?php $i = 0;?><?php $__LIST__ = $xml_type?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo_type): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo_type['name']); ?>"><?php echo ($vo_type['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</select>
</td>
	<td>股票代码：</td>
	<td><input name='stocks_code' type="text" tabindex='2' size='20' maxlength='100' value='' /></td>
	</tr>
<tr><!--
	<td>负责人：</td>
	<td><select size="3" name='manager_id' tabindex='1' multiple="multiple">
			<option value="<?php echo ($user['id']); ?>"><?php echo ($user['name']); ?></option>
			</select></td>-->
	<td>邮件：</td>
	<td><input name='mail' type="text" tabindex='2' size='20' maxlength='25' value='' /></td>
	<td >最后更新：</td>
	<td >
		<fieldset class="date">
		      	<input type="text" name="date_from" id="sel1" /><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="return showCalendar('sel1', '%Y-%m-%d');"  /> <span>到</span> 
		      	<input type="text" name="date_to" id="sel3" size="30" /><img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt="" onclick="return showCalendar('sel3', '%Y-%m-%d');"  />
		 </fieldset>
	</td>
	</tr><tr>
	<td>省份：</td>
	<td>
	<script>showprovince('province', 'city', "");</script>
	</td>
	<td>城市：</td>
	<td >
<script>showcity('city', "", 'province');</script>
</td>
	</tr><tr>
	<td>地址：</td>
	<td><textarea name='street' rows="2" tabindex='3' cols="30"></textarea></td>
	<td>邮编：</td>
	<td colspan="3"><input  type="text" name='postcode' tabindex='2' size='10' maxlength='20' value='' /></td>
	</tr>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="4">
				<button title="查找 [Alt+Q]" type="submit">查找</button>
				<button title="重置 [Alt+C]" type="reset" >重置</button>
				<button title="高级查找 [Alt+A]" type="button"  onclick="displayOrNo('searchAll','block');">高级查找</button>
			</th>
		</tr>
	</tfoot>
</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<form action="/index.php/Account/del" method='post' name='MassUpdate'  id="MassUpdate">
<table>
	<caption class="nostyle">客户列表</caption>
	<colgroup>
		<!--<col width="20" />-->
		<col width="" />
		<col width="10%" />
		<col width="" />
		<col width="15%" />
		<col width="15%" />
		<col width="15%" />
	
	</colgroup>
	<thead>
		<tr><td colspan="8"><?php echo ($page); ?></td></tr>
		<tr>
<!--			<th><fieldset class="check"><input type='checkbox' class='checkbox' name='massall' value='' onclick='checkAll(document.MassUpdate, "mass[]", this.checked)' /></fieldset></th>-->
			<th><a href="<?php echo ($sort_url); ?>&field=name">客户名称&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=type">客户类型&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=province">地区&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=industry">行业&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=phone_work">办公电话&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=manager_id">负责人&nbsp;</a></th>
			<th><a href="<?php echo ($sort_url); ?>&field=manager_id">操作&nbsp;</a></th>
			<th>拜访次数</th>
		</tr>
	</thead>
	<?php if(is_array($account_record)): ?><?php $i = 0;?><?php $__LIST__ = $account_record?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<!--			<td><fieldset class="check"><?php if(($vo['flag'])  >  "3"): ?><input type='checkbox' class='checkbox' name='mass[]' value="<?php echo ($vo['id']); ?>" /><?php endif; ?></fieldset></td>-->
			<td>
				<?php if(($vo['flag'])  >  "1"): ?><a href="/index.php/Account/view/id/<?php echo ($vo['id']); ?>" class="listViewTdLinkS1"><?php endif; ?>
				&nbsp;<?php echo ($vo['name']); ?>
				<?php if(($vo['flag'])  >  "1"): ?></a><?php endif; ?>
			</td>
			<td ><?php echo ($vo['type']); ?></td>
			<td ><?php echo ($vo['province']); ?>&nbsp;&nbsp;<?php echo ($vo['city']); ?></td>
			<td ><?php echo ($vo['industry']); ?></td>
			<td><?php echo ($vo['phone_work']); ?></td>
		    <td><?php echo ($vo['manager_name']); ?></td>
			<td><a href="/index.php/Callback/create/id/<?php echo ($vo['id']); ?>" title="追加客户交流记录">追加客户交流记录</a>  &nbsp&nbsp&nbsp  <a href="/index.php/Callback/lists/id/<?php echo ($vo['id']); ?>" title="查看交流记录">查看交流记录</a></td>
			<td><?php echo ($vo['count']); ?></td>
		</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>	
	<tfoot>
		<tr>
			<td colspan="8"><?php echo ($page); ?></td>
		</tr>
<!--		<tr>
			<th colspan="7">
				<button onclick='checkAll(document.MassUpdate, "mass[]", 1);' type="button">全选</button>
				<button onclick='checkAll(document.MassUpdate, "mass[]", 0);' type="button">取消选中</button>
				<button type='submit' onclick="return del_records(this.form,'mass[]');">删除</button>
			</th>
		</tr>-->
	</tfoot>

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