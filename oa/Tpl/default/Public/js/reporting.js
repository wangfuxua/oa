/**
* author : 贺辽平;
* date ： 2009-01-20
*
*/

//主要模块
var primary_module = Array();
primary_module[0] = Array('Contact','我的联系人');
primary_module[1] = Array('Account','我的客户');
primary_module[2] = Array('Report','客户反馈');
primary_module[3] = Array('Callback','客户回访');

//相关模块
var related_module = Array();
related_module['Contact'] = Array();
related_module['Contact'][0] = Array('Contact','我的联系人');
related_module['Contact'][1] = Array('Account','我的客户');
related_module['Account'] = Array();
related_module['Account'][0] = Array('Account','我的客户');
related_module['Report'] = Array();
related_module['Report'][0] = Array('Account','我的客户');
related_module['Callback'] = Array();
related_module['Callback'][0] = Array('Contact','我的联系人');
related_module['Callback'][1] = Array('Account','我的客户');
related_module['Callback'][2] = Array('Report','客户反馈');

//表字段对应名称
var table_fields_name = Array();

table_fields_name['Contact'] = Array();
table_fields_name['Contact']['time_create'] = '创建时间';
table_fields_name['Contact']['time_modify'] = '修改时间';
table_fields_name['Contact']['modified_user_id'] = '最后修改者';
table_fields_name['Contact']['assigned_user_id'] = '负责人';
table_fields_name['Contact']['first_name'] = '姓名';
table_fields_name['Contact']['en_name'] = '英文名';
table_fields_name['Contact']['nick_name'] = '昵称';
table_fields_name['Contact']['spouse_name'] = '配偶姓名';
table_fields_name['Contact']['contact_source'] = '联系人来源';
table_fields_name['Contact']['title'] = '职务';
table_fields_name['Contact']['department'] = '部门';
table_fields_name['Contact']['reports_to_name'] = '经理';
table_fields_name['Contact']['birthdate'] = '生日';
table_fields_name['Contact']['phone_home'] = '家庭电话';
table_fields_name['Contact']['phone_mobile'] = '手机';
table_fields_name['Contact']['phone_work'] = '办公电话';
table_fields_name['Contact']['phone_other'] = '其他电话';
table_fields_name['Contact']['phone_fax'] = '传真';
table_fields_name['Contact']['email1'] = '主要电子邮件';
table_fields_name['Contact']['email2'] = '其他电子邮件';
table_fields_name['Contact']['assistant'] = '助理';
table_fields_name['Contact']['assistant_phone'] = '助理电话';
table_fields_name['Contact']['primary_address_street'] = '地址';
table_fields_name['Contact']['primary_address_city'] = '城市';
table_fields_name['Contact']['primary_address_state'] = '省份';
table_fields_name['Contact']['primary_address_postalcode'] = '邮编';
table_fields_name['Contact']['description'] = '说明';
table_fields_name['Contact']['account_name'] = '客户名称';

table_fields_name['Account'] = Array();
table_fields_name['Account']['time_create'] = '创建时间';
table_fields_name['Account']['time_modify'] = '修改时间';
table_fields_name['Account']['modified_user_id'] = '修改者';
table_fields_name['Account']['assigned_user_id'] = '负责人';
table_fields_name['Account']['name'] = '客户名称';
table_fields_name['Account']['parent_name'] = '上级单位';
table_fields_name['Account']['account_type'] = '类型';
table_fields_name['Account']['industry'] = '行业';
table_fields_name['Account']['annual_revenue'] = '年收益';
table_fields_name['Account']['phone_fax'] = '传真';
table_fields_name['Account']['primary_address_street'] = '地址';
table_fields_name['Account']['primary_address_city'] = '城市';
table_fields_name['Account']['primary_address_state'] = '省份';
table_fields_name['Account']['primary_address_postalcode'] = '邮编';
table_fields_name['Account']['description'] = '说明';
table_fields_name['Account']['rating'] = '评价';
table_fields_name['Account']['phone_work'] = '办公电话';
table_fields_name['Account']['phone_other'] = '其他电话';
table_fields_name['Account']['email1'] = '主要邮件';
table_fields_name['Account']['email2'] = '其他邮件';
table_fields_name['Account']['website'] = '网站';
table_fields_name['Account']['owner'] = '所有者';
table_fields_name['Account']['employees'] = '员工数';
table_fields_name['Account']['sic_code'] = 'sic编码';
table_fields_name['Account']['stock_code'] = '股票代码';
table_fields_name['Account']['bank1'] = '开户行1';
table_fields_name['Account']['bank1_account'] = '开户名称1';
table_fields_name['Account']['bank1_account_id'] = '银行账号1';
table_fields_name['Account']['tax_id1'] = '纳税号1';
table_fields_name['Account']['bank2'] = '开户行2';
table_fields_name['Account']['bank2_account'] = '开户名称2';
table_fields_name['Account']['bank2_account_id'] = '银行账号2';
table_fields_name['Account']['tax_id2'] = '纳税号2';
table_fields_name['Account']['payment_type'] = '支付方式';
table_fields_name['Account']['payment_credit'] = '信用额度';

table_fields_name['Report'] = Array();
table_fields_name['Report']['time_create'] = '创建时间';
table_fields_name['Report']['time_modify'] = '修改时间';
table_fields_name['Report']['modified_user_id'] = '修改者';
table_fields_name['Report']['assigned_user_id'] = '负责人';
table_fields_name['Report']['subject'] = '主题';
table_fields_name['Report']['account_name'] = '客户名称';
table_fields_name['Report']['status'] = '状态';
table_fields_name['Report']['type'] = '类型';
table_fields_name['Report']['reason'] = '反馈原因';
table_fields_name['Report']['source'] = '反馈来源';
table_fields_name['Report']['priority'] = '优先级';
table_fields_name['Report']['description'] = '描述';
table_fields_name['Report']['resolution'] = '分析';

table_fields_name['Callback'] = Array();
table_fields_name['Callback']['time_create'] = '创建时间';
table_fields_name['Callback']['time_modify'] = '修改时间';
table_fields_name['Callback']['modified_user_id'] = '修改者';
table_fields_name['Callback']['assigned_user_id'] = '负责人';
table_fields_name['Callback']['account_name'] = '客户名称';
table_fields_name['Callback']['contact_name'] = '联系人姓名';
table_fields_name['Callback']['report_name'] = '客户反馈';
table_fields_name['Callback']['type'] = '类型';
table_fields_name['Callback']['service_call'] = '服务电话接听';
table_fields_name['Callback']['service_technical'] = '工程服务技术';
table_fields_name['Callback']['service_attitudinal'] = '服务态度';
table_fields_name['Callback']['service_date'] = '客服日期';
table_fields_name['Callback']['description'] = '描述';

var object_refs = new Object();

//给id值为child_id的生成select，并且生成对应的字段
function add_module_fields(value,child_id,url){
	var related_arr = related_module[value];
	var select_options = '<select  onchange="add_more_fields(\''+url+'\',this.value);" name="related_module" id="related_module"><OPTION value="">--请选择--</OPTION>';
	for (var i=0; i<related_arr.length; i++){
		select_options = select_options + "<OPTION value='"+related_arr[i][0]+"'>"+related_arr[i][1]+"</OPTION>";
	}
	select_options = select_options + "</select>";
	document.getElementById(child_id).innerHTML = select_options;
	fields_ajax(url+"/ReportingData/fields/tab/"+value,0);
}

//添加options
function add_more_fields(url,value){
	fields_ajax(url+"/ReportingData/fields/tab/"+value,1);
}

//创建XMLHTTPRequest对象
function createXMLHttpRequest(){
	var xmlHttp = null;
	try{
		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}catch(e){
			try{
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP"); 
			}catch(e){
				try{
					xmlHttp=new XMLHttpRequest();
				}catch(e){
					alert("error!");
					exit;
				}
			}
		}
	return xmlHttp;
}

//启动发送请求
function fields_ajax(url,flag){
	var flag = flag;
	xmlHttp=createXMLHttpRequest();
	var url=url + "/date/" + new Date();
	xmlHttp.open("GET",url,true);
	if(flag) xmlHttp.onreadystatechange=chindCallback;
	else xmlHttp.onreadystatechange=startCallback;
	xmlHttp.send(null);
}

//startCallback方法
function startCallback(){
	if(xmlHttp.readyState==4){
		if(xmlHttp.status==200){
			if(xmlHttp.responseText){
				var display_fields = '<select id="hide_fields_list" name="hide_fields_list[]" size="10" multiple="multiple" >';
				var count_fields = '<select id="no_total_fields_list" name="no_total_fields_list[]" size="10" multiple="multiple" >';
				var display_fields_options = null;
				var count_fields_options = null;
				var fields = Array();
				fields = xmlHttp.responseText.split(",");
				var tab = fields[fields.length-1];
				for(var i=0; i<primary_module.length; i++){
					if(primary_module[i][0] == tab)
						var tab_name = primary_module[i][1]
				}
				for(var i=0; i<fields.length-2; i++){
					if(table_fields_name[tab][fields[i]]){
						display_fields_options = display_fields_options + '<option value="'+tab+'.'+fields[i]+'">'+tab_name+':'+table_fields_name[tab][fields[i]]+'</option>';
						count_fields_options = count_fields_options +  '<option value="'+tab+'.'+fields[i]+'">'+tab_name+':'+table_fields_name[tab][fields[i]]+'</option>';
					}
				}
				display_fields = display_fields + display_fields_options + "</select>";
				count_fields = count_fields + count_fields_options + "</select>";
				document.getElementById('hide_fields_list_td').innerHTML = display_fields;
				document.getElementById('no_total_fields_list_td').innerHTML = count_fields;
				
				object_refs['hide_fields_list'] = document.getElementById('hide_fields_list');
				object_refs['display_fields_list'] = document.getElementById('display_fields_list');
				object_refs['no_total_fields_list'] = document.getElementById('no_total_fields_list');
				object_refs['total_fields_list'] = document.getElementById('total_fields_list');
				//alert(count_fields);
			}else{
				alert("获取失败!");
				return false;
			}
		}
	}
}

//chindCallback方法
function chindCallback(){
	if(xmlHttp.readyState==4){
		if(xmlHttp.status==200){
			if(xmlHttp.responseText){
				var display_fields = '<select id="hide_fields_list" name="hide_fields_list[]" size="10" multiple="multiple" >';
				var count_fields = '<select id="no_total_fields_list" name="no_total_fields_list[]" size="10" multiple="multiple" >';
				var display_fields_options = document.getElementById('hide_fields_list').innerHTML;
				var count_fields_options = document.getElementById('no_total_fields_list').innerHTML;
				var fields = Array();
				fields = xmlHttp.responseText.split(",");
				var tab = fields[fields.length-1];
				for(var i=0; i<primary_module.length; i++){
					if(primary_module[i][0] == tab)
						var tab_name = primary_module[i][1]
				}
				for(var i=0; i<fields.length-2; i++){
					if(table_fields_name[tab][fields[i]]){
						display_fields_options = display_fields_options + '<option value="'+tab+'.'+fields[i]+'">'+tab_name+':'+table_fields_name[tab][fields[i]]+'</option>';
						count_fields_options = count_fields_options +  '<option value="'+tab+'.'+fields[i]+'">'+tab_name+':'+table_fields_name[tab][fields[i]]+'</option>';
					}
				}
				display_fields = display_fields + display_fields_options + "</select>";
				count_fields = count_fields + count_fields_options + "</select>";
				document.getElementById('hide_fields_list_td').innerHTML = display_fields;
				document.getElementById('no_total_fields_list_td').innerHTML = count_fields;
				
				object_refs['hide_fields_list'] = document.getElementById('hide_fields_list');
				object_refs['display_fields_list'] = document.getElementById('display_fields_list');
				object_refs['no_total_fields_list'] = document.getElementById('no_total_fields_list');
				object_refs['total_fields_list'] = document.getElementById('total_fields_list');
				//alert(count_fields);
			}else{
				alert("获取失败!");
				return false;
			}
		}
	}
}

function buildSelectHTML(info,flag)
{
        var text;
        var f;
        f = flag
        text = "<select";

        if ( typeof (info['select']['size']) != 'undefined')
        {
                text +=" size=\""+ info['select']['size'] +"\"";
        }

        if ( typeof (info['select']['name']) != 'undefined')
        {
                text +=" name=\""+ info['select']['name'] +"\"";
        }

        if ( typeof (info['select']['style']) != 'undefined')
        {
                text +=" style=\""+ info['select']['style'] +"\"";
        }

        if ( typeof (info['select']['onchange']) != 'undefined')
        {
                text +=" onChange=\""+ info['select']['onchange'] +"\"";
        }

        if ( typeof (info['select']['multiple']) != 'undefined')
        {
                text +=" multiple";
        }
        text +=">";

        for(i=0; i<info['options'].length;i++)
        {
                option = info['options'][i];
                text += "<option value=\""+option['value']+"\" ";
                if (f)
                {
                        text += "selected";
                }
                text += ">"+option['text']+"</option>";
        }
        text += "</select>";
        return text;
}


function left_to_right(left_name,right_name) 
{
	var display_columns_ref = object_refs[left_name];
//alert(display_columns_ref);
	var hidden_columns_ref = object_refs[right_name];

	var left_td = document.getElementById(left_name+'_td');
	var right_td = document.getElementById(right_name+'_td');

	var selected_left = new Array();
	var notselected_left = new Array();
	var notselected_right = new Array();

	var left_array = new Array();

        // determine which options are selected in left 
	for (i=0; i < display_columns_ref.options.length; i++)
	{
		if ( display_columns_ref.options[i].selected == true) 
		{
			selected_left[selected_left.length] = {text: display_columns_ref.options[i].text, value: display_columns_ref.options[i].value};
		}
		else
		{
			notselected_left[notselected_left.length] = {text: display_columns_ref.options[i].text, value: display_columns_ref.options[i].value};
		}
		
	}

	for (i=0; i < hidden_columns_ref.options.length; i++)
	{
		notselected_right[notselected_right.length] = {text:hidden_columns_ref.options[i].text, value:hidden_columns_ref.options[i].value};
		
	}

	var left_select_html_info = new Object();
	var left_options = new Array();
	var left_select = new Object();

	left_select['name'] = left_name+'[]';
	left_select['id'] = left_name;
	left_select['size'] = '10';
	left_select['multiple'] = 'true';

	var right_select_html_info = new Object();
	var right_options = new Array();
	var right_select = new Object();

	right_select['name'] = right_name+'[]';
	right_select['id'] = right_name;
	right_select['size'] = '10';
	right_select['multiple'] = 'true';

	for (i=0;i < notselected_right.length;i++)
	{
		right_options[right_options.length] = notselected_right[i];	
	}

	for (i=0;i < selected_left.length;i++)
	{
		right_options[right_options.length] = selected_left[i];	
	}
	for (i=0;i < notselected_left.length;i++)
	{
		left_options[left_options.length] = notselected_left[i];	
	}
	left_select_html_info['options'] = left_options;
	left_select_html_info['select'] = left_select;
	right_select_html_info['options'] = right_options;
	right_select_html_info['select'] = right_select;
	right_select_html_info['style'] = 'background: lightgrey';

	var left_html = buildSelectHTML(left_select_html_info,1);
	var right_html = buildSelectHTML(right_select_html_info,0);

	left_td.innerHTML = left_html;
	right_td.innerHTML = right_html;
	object_refs[left_name] = left_td.getElementsByTagName('select')[0];
	object_refs[right_name] = right_td.getElementsByTagName('select')[0];
}


function right_to_left(left_name,right_name) 
{
	var display_columns_ref = object_refs[left_name];
	var hidden_columns_ref = object_refs[right_name];

	var left_td = document.getElementById(left_name+'_td');
	var right_td = document.getElementById(right_name+'_td');

	var selected_right = new Array();
	var notselected_right = new Array();
	var notselected_left = new Array();

	for (i=0; i < hidden_columns_ref.options.length; i++)
	{
		if (hidden_columns_ref.options[i].selected == true) 
		{
			selected_right[selected_right.length] = {text:hidden_columns_ref.options[i].text, value:hidden_columns_ref.options[i].value};
		}
		else
		{
			notselected_right[notselected_right.length] = {text:hidden_columns_ref.options[i].text, value:hidden_columns_ref.options[i].value};
		}
		
	}

	for (i=0; i < display_columns_ref.options.length; i++)
	{
		notselected_left[notselected_left.length] = {text:display_columns_ref.options[i].text, value:display_columns_ref.options[i].value};
		
	}

	var left_select_html_info = new Object();
	var left_options = new Array();
	var left_select = new Object();

	left_select['name'] = left_name+'[]';
	left_select['id'] = left_name;
	left_select['multiple'] = 'true';
	left_select['size'] = '10';

	var right_select_html_info = new Object();
	var right_options = new Array();
	var right_select = new Object();

	right_select['name'] = right_name+ '[]';
	right_select['id'] = right_name;
	right_select['multiple'] = 'true';
	right_select['size'] = '10';

	for (i=0;i < notselected_left.length;i++)
	{
		left_options[left_options.length] = notselected_left[i];	
	}

	for (i=0;i < selected_right.length;i++)
	{
		left_options[left_options.length] = selected_right[i];
	}
	for (i=0;i < notselected_right.length;i++)
	{
		right_options[right_options.length] = notselected_right[i];	
	}
	
	left_select_html_info['options'] = left_options;
	left_select_html_info['select'] = left_select;
	right_select_html_info['options'] = right_options;
	right_select_html_info['select'] = right_select;
	right_select_html_info['style'] = 'background: lightgrey';

	var left_html = buildSelectHTML(left_select_html_info,1);
	var right_html = buildSelectHTML(right_select_html_info,0);

	left_td.innerHTML = left_html;
	right_td.innerHTML = right_html;
	object_refs[left_name] = left_td.getElementsByTagName('select')[0];
	object_refs[right_name] = right_td.getElementsByTagName('select')[0];
}


function up(name) {
	var td = document.getElementById(name+'_td');
	var obj = td.getElementsByTagName('select')[0];
	obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var sel = new Array();

	for (i=0; i<obj.length; i++) {
		if (obj[i].selected == true) {
			sel[sel.length] = i;
		}
	}
	for (i in sel) {
		if (sel[i] != 0 && !obj[sel[i]-1].selected) {
			var tmp = new Array(obj[sel[i]-1].text, obj[sel[i]-1].value);
			obj[sel[i]-1].text = obj[sel[i]].text;
			obj[sel[i]-1].value = obj[sel[i]].value;
			obj[sel[i]].text = tmp[0];
			obj[sel[i]].value = tmp[1];
			obj[sel[i]-1].selected = true;
			obj[sel[i]].selected = false;
		}
	}
}


function down(name) {
	var td = document.getElementById(name+'_td');
	var obj = td.getElementsByTagName('select')[0];
	if (obj.tagName.toLowerCase() != "select" && obj.length < 2)
		return false;
	var sel = new Array();
	for (i=obj.length-1; i>-1; i--) {
		if (obj[i].selected == true) {
			sel[sel.length] = i;
		}
	}
	for (i in sel) {
		if (sel[i] != obj.length-1 && !obj[sel[i]+1].selected) {
			var tmp = new Array(obj[sel[i]+1].text, obj[sel[i]+1].value);
			obj[sel[i]+1].text = obj[sel[i]].text;
			obj[sel[i]+1].value = obj[sel[i]].value;
			obj[sel[i]].text = tmp[0];
			obj[sel[i]].value = tmp[1];
			obj[sel[i]+1].selected = true;
			obj[sel[i]].selected = false;
		}
	}
}

var rowCnt = 0;
function fnAddRow(){
	rowCnt++;
	var tableName = document.getElementById('conditionTab');
	var prev = tableName.rows.length;
    var count = prev;
    var row = tableName.insertRow(prev);
	row.id = "row"+count;
	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);
	var colthree = row.insertCell(2);
	var colfour = row.insertCell(3);
	var colfive = row.insertCell(4);
	row.className = "dataLabel";

	var fields = object_refs['no_total_fields_list'];
	var opt = "<select name='conditionField_"+count+"'>";
	for(var i = 0; i < fields.options.length; i++){
		opt = opt + "<OPTION value='"+fields.options[i].value+"'>"+fields.options[i].innerHTML+"</OPTION>";
	}
	opt = opt + "</select>";
	colone.innerHTML=opt;
	coltwo.innerHTML="<select name='fopField_"+count+"'><OPTION value='eq'>等于</OPTION><OPTION value='ueq'>不等于</OPTION><OPTION value='lessthan'>小于</OPTION><OPTION value='lessthanandequal'>小于等于</OPTION><OPTION value='morethan'>大于</OPTION><OPTION value='morethanandequal'>大于等于</OPTION><OPTION value='like'>匹配</OPTION></select>";
	colthree.innerHTML="<input type='text' size=20 name='conditionValue_"+count+"'>";
	colfour.innerHTML="<span>and</span>";
	colfive.innerHTML="<input type='button' value='刪除' class='dataLabel' onclick=\"deleteRow(this.parentNode.parentNode.rowIndex)\">";

}

var orderbyCnt = 0;
function orderbyAddRow(){
	orderbyCnt++;
	var tableName = document.getElementById('orderbyTab');
	var prev = tableName.rows.length;
    var count = prev;
    var row = tableName.insertRow(prev);
	row.id = "row"+count;
	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);
	var colthree = row.insertCell(2);
	row.className = "dataLabel";

	var fields = object_refs['no_total_fields_list'];
	var opt = "<select name='orderbyField_"+count+"'>";
	for(var i = 0; i < fields.options.length; i++){
		opt = opt + "<OPTION value='"+fields.options[i].value+"'>"+fields.options[i].innerHTML+"</OPTION>";
	}
	opt = opt + "</select>";
	colone.innerHTML=opt;
	coltwo.innerHTML="<select name='orderbyDirection_"+count+"'><OPTION value='asc'>升序</OPTION><OPTION value='desc'>降序</OPTION></select>";
	colthree.innerHTML="<input type='button' class='dataLabel' value='删除' onclick=\"deleteOrderbyRow(this.parentNode.parentNode.rowIndex)\">";

}
function deleteRow(i)
{
	 rowCnt--;
	 document.getElementById('conditionTab').deleteRow(i);
}
function deleteOrderbyRow(i)
{
	 orderbyCnt--;
	 document.getElementById('orderbyTab').deleteRow(i);
}
