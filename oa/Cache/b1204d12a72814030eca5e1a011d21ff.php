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
 
<title>新建客户</title>
<link href="/oa/Tpl/default/Public/css/addcentcater.css" rel="stylesheet" type="text/css" />  
<link href="/oa/Tpl/default/Public/css/default.css" rel="stylesheet" type="text/css" />
<link href="/oa/Tpl/default/Public/style/default/css/KDailog.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/oa/Tpl/default/Public/js/crm_data.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/crm_common.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/dialog/dialog.js"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/KDailog.js"></script>
<script Language="JavaScript">
$(function(){
	$(":submit").click(function(){
		managers = $('#manager_name').attr('value');
		if (managers.indexOf(',') != -1)
		{
			$("#m_check").replaceWith("<font color='#FF0000'>您只能输入一个负责人</font>"); 
			return  false;   
		}
	});
});
function Clear(id_1,id_2){
	document.getElementById(id_1).value='';
	document.getElementById(id_2).value='';
}
</script>
</head>
<body>
<!--	<ul class="dm_submenuul">
		<li><a href="/index.php/Account/lists" title=""><span>客户列表</span></a></li>
		<li class="dm_on"><a href="/index.php/Account/create" title=""><span><?php if($update): ?>修改<?php else: ?>新建<?php endif; ?>客户</span></a></li>
	</ul>-->

<form method="POST" name="form1" action='<?php if($update): ?>/index.php/Account/update<?php else: ?>/index.php/Account/save<?php endif; ?>' onSubmit="return idNull('name','name_msg','姓名不能为空！');">		
<input type="hidden" name="current_id" value="<?php echo ($account_res['id']); ?>" />
<table>
<colgroup>
	<col width="15%" />
	<col width="" />
	<col width="15%" />
	<col width="" />
</colgroup>
<thead>
	<tr>
		<td colspan="4" class="tdleft">
			<input type="hidden" name="account_id" value="<?php echo ($account_res['id']); ?>" />
			<input type="hidden" name="address_id" value="<?php echo ($address_res['id']); ?>" />
			<input type="hidden" name="bankinfo_id" value="<?php echo ($bankinfo_res['id']); ?>" />
			<button type="submit" >保存</button>
			<button  type="reset" >重置</button>
			<label>*代表必填项</label>

		</td>
	</tr>
</thead>
	<tr><th class="tdtit" colspan="4"><h4>客户信息</h4></th></tr>
	<tr>
		<td>客户名称： <span class="required" style="color:red;">*</span></td>
		<td><input name='name' id="name" tabindex='1' size='35' maxlength='150' type="text" value="<?php echo ($account_res['name']); ?>" onBlur="idNull('name','name_msg','名称不能为空！');" /><span id="name_msg"></span></td>
		<td>联系人(客户方)：</td>
		<td>
		<input name="contact_man" type="text" maxlength="30" value="<?php echo ($account_res['contact_man']); ?>" /></td>
	</tr>
	<tr>
		<td>联系人职务(客户方)：</td>
		<td>
		<input name="contact_post" type="text" maxlength="30" value="<?php echo ($account_res['contact']); ?>" /></td>
		<td>产品类型：</td>
		<td>
		<input id="product_type_id" type="hidden" />
		<input name='product_type' id="product_type" type="text" tabindex='2' size='20' maxlength='25' value="<?php echo ($account_res['productType']); ?>" readonly />
		<button type="button" onClick="KDwin('/index.php/Product/lists/to_id/product_type_id/to_name/product_type',500,350,'选择产品');">选择</button>	</td>
	</tr>
	<tr>
		<td>客户来源：</td>
		<td>
		<input id="source_id" type="hidden" />
		<input name='source' id="source" type="text" tabindex='2' size='20' maxlength='25' value="<?php echo ($account_res['source']); ?>" readonly />
		<button title="选择"  type="button" onClick="KDwin('/index.php/XmlFile/lists/dis/account_source/to_id/source_id/to_name/source',500,350,'选择');">选择</button>	</td>
		<td>客户网站：</td>
		<td><input name='website' type="text" tabindex='1' size='28' maxlength='255' value="<?php echo ($account_res['website']); ?>" /></td>
	</tr>
	<tr>
		<td>客户类型：</td>
		<td  valign="top" >
		<input id="type_id" type="hidden" />
		<input name='type' id="type" type="text" tabindex='2' size='20' maxlength='25' value="<?php echo ($account_res['type']); ?>" readonly />
		<button title="选择"  type="button" onClick="KDwin('/index.php/XmlFile/lists/dis/account_type/to_id/type_id/to_name/type',500,350,'选择');">选择</button></td>
		<td>股票代码：</td>
		<td><input name='stocks_code' type="text" tabindex='1' size='10' maxlength='10' value="<?php echo ($account_res['stocks_code']); ?>" /></td>
	</tr>
	<tr>
		<td>客户行业：</td>
		<td  valign="top"  >
		<input id="industry_id" type="hidden" />
		<input name='industry' id="industry" type="text" tabindex='2' size='20' maxlength='25' value="<?php echo ($account_res['industry']); ?>" readonly />
		<button title="选择"  type="button" onClick="KDwin('/index.php/XmlFile/lists/dis/account_industry/to_id/industry_id/to_name/industry',500,350,'选择');">选择</button>
		</td>
		<td>办公电话：</td>
		<td><input name='phone_work' type="text" tabindex='2' size='20' maxlength='25'  value="<?php echo ($account_res['phone_work']); ?>" /></td>
	</tr>
	<tr>
		<td>状态：</td>
		<td>
		<input id="status_id" type="hidden" />
		<input name='status' id="status" type="text" tabindex='2' size='20' maxlength='25' value="<?php echo ($latent_res['status']); ?>" readonly />
		<button title="选择"  type="button" onClick="KDwin('/index.php/XmlFile/lists/dis/account_status/to_id/status_id/to_name/status',500,350);">选择</button>	
		</td>
		<td>移动电话：</td>
		<td><input name='phone_mobile' type="text" tabindex='2' size='20' maxlength='25'  value="<?php echo ($account_res['phone_mobile']); ?>" onBlur="checkMobile('phone_mobile','mobile_msg','请输入正确的手机号码');" /><span id='mobile_msg'></span></td>
	</tr>
	<tr>
		<td>员工数(规模)：</td>
		<td><input name='employees' id="employees" type="text" tabindex='1' size='10' maxlength='10' value="<?php echo ($account_res['employees']); ?>" onBlur="checkNumber('employees','employees_msg','请填写数字!');" />人<span id='employees_msg'></span></td>
		<td>传真：</td>
		<td><input name='phone_fax' type="text" tabindex='2' size='20' maxlength='25' value="<?php echo ($account_res['phone_fax']); ?>" /></td>
	</tr>
	<tr>
		<td>负责人：</td>
		<td  >
		<input id='one_or_more' type="hidden" value="y" />
		<input id='manager_id' name='manager_id' type="hidden" value="<?php echo ($account_res['uid']); ?>" />
		<input id="manager_name" type="text" value="<?php echo ($account_res['USER_NAME']); ?>" readonly/>
		<button title="添加收信人" type="button"  onclick="setInput('manager_id','manager_name','right_show1','true')"/>添加</button>	<span id='m_check'></span>
		</td>
		<td>电子邮件：</td>
		<td><input name='mail' type="text" tabindex='2' size='35' maxlength='100' value="<?php echo ($account_res['mail']); ?>" onBlur="checkEmail('mail','mail_msg','请输入正确的邮箱！');" />
		<span id='mail_msg'></span></td>
	</tr>
	<tr><th class="tdtit" colspan="4"><h4>地址信息</h4></th></tr>
	<tr>
		<td >省份：</td>
		<td >
<script>showprovince('province', 'city', "<?php echo ($address_res['province']); ?>");</script>
		</td>
		<td>城市：</td>
		<td >
<script>showcity('city', "<?php echo ($address_res['city']); ?>", 'province');</script>
		</td>
    </tr>
    <tr>
		<td >地址：</td>
		<td ><textarea name='street' rows="2" tabindex='3' cols="30"><?php echo ($address_res['street']); ?></textarea></td>
		<td >邮编：</td>
		<td><input name='postcode' id="postcode" tabindex='3' size='15' maxlength='20' value="<?php echo ($address_res['postcode']); ?>" onBlur="checkNumber('postcode','postcode_msg','请填写数字!');" /><span id='postcode_msg'></span></td>
	</tr>
	<tr><th class="tdtit" colspan="5"><h4>银行财务信息</h4></th></tr>
	
	<tr>
		<td >开户银行1：</td>
		<td ><input name='bank1_name' type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['bank1_name']); ?>" />
		</td>
		<td>开户银行2：</td>
		<td ><input name='bank2_name' type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['bank2_name']); ?>" />
		</td>
    </tr>
	<tr>
		<td >开户名称1：</td>
		<td ><input name='bank1_account_name' type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['bank1_account_name']); ?>" />
		</td>
		<td>开户名称2：</td>
		<td ><input name='bank2_account_name' type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['bank2_account_name']); ?>" />
		</td>
    </tr>
	<tr>
		<td >银行帐号1：</td>
		<td ><input name='bank1_account' id="bank1_account" type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['bank1_account']); ?>" onBlur="checkNumber('bank1_account','bank1_account_msg','请填写数字!');" /><span id='bank1_account_msg'></span></td>
		<td>银行帐号2：</td>
		<td ><input name='bank2_account' id="bank2_account" type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['bank2_account']); ?>" onBlur="checkNumber('bank2_account','bank2_account_msg','请填写数字!');" /><span id='bank2_account_msg'></span></td>
    </tr>
	<tr>
		<td >纳税号1：</td>
		<td ><input name='bank1_tariff' type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['bank1_tariff']); ?>" />
		</td>
		<td>纳税号2：</td>
		<td ><input name='bank2_tariff' type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['bank2_tariff']); ?>" />
		</td>
    </tr>
	<tr>
		<td >支付方式：</td>
		<td >
<!--		<script>
		change_selected('pay_type', payment_type, "<?php echo ($bankinfo_res['pay_type']); ?>");
		</script>-->
		<input id="pay_type_id" type="hidden" />
		<input name='pay_type' id="pay_type" type="text" tabindex='2' size='20' maxlength='25' value="<?php echo ($bankinfo_res['pay_type']); ?>" readonly />
		<button title="选择"  type="button" onClick="KDwin('/index.php/XmlFile/lists/dis/pay_type/to_id/pay_type_id/to_name/pay_type',500,350,'选择');">选择</button>
		</td>
		<td>信用额度：</td>
		<td ><input name='credit' id="credit" type="text" tabindex='1' size='25' maxlength='100' value="<?php echo ($bankinfo_res['credit']); ?>" onBlur="checkNumber('credit','credit_msg','请填写数字!');" /><span id='credit_msg'></span>
		</td>
    </tr>
    <tr><th class="tdtit" colspan="4"><h4>说明信息</h4></th></tr>
	<tr>
		<td>说明：</td>
		<td colspan="3"><textarea name='description' tabindex='5' cols="60" rows="8"><?php echo ($account_res['description']); ?></textarea></td>
	</tr>
	<tfoot>
		<tr>
			<th colspan="4" class="tdleft">
				<button type="submit" >保存</button>
				<button  type="reset">重置</button>
				<label>*代表必填项</label>
	
			</th>
		</tr>
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