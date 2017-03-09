//alert(1);
// 验证制定id值是否为空，空则提示
function idNull(id,tid,msg){
	var pattern = /(\S)+/;
	//return alertMsg(id,tid,pattern,msg);
	if (is_array(id)){
		if (is_array(tid)){
			if (is_array(msg)){
				if (id.length != tid.length || id.length != msg.length || tid.length != msg.length){
					alert('参数错误00!');
					return false;
				}
				for (var i=0; i<id.length; i++){
					var res = idNull(id[i],tid[i],msg[i]);
					if (!res) {
						return false;
						break;
					}
				}
			}
			else if (id.length !== tid.length){
				alert('参数错误11!');
				return false;
			}
			for (var i=0; i<id.length; i++){
				var res = idNull(id[i],tid[i],msg);
				if (!res) {
					return false;
					break;
				}
			}			
		}
		else if (is_array(msg)){
			alert('参数错误22!');
			return false;
		}
		for (var i=0; i<id.length; i++){
			var res = idNull(id[i],tid,msg);
			if (!res) {
				return false;
				break;
			}
		}		
	}
	return alertMsg(id,tid,pattern,msg);
}
//验证手机号码
function checkMobile(id,tid,msg){
	var pattern = /^1[3,5]\d{9}$/;
	var val = getObjById(id).value;
	if (val == '' || val == null || val ==' '){
		setValueById(tid,'')
	}else{
		return alertMsg(id,tid,pattern,msg);
	}
}

//验证电子邮件
function checkEmail(id,tid,msg){
	var pattern = /^[a-z0-9]+([\._-]?[a-z0-9]+)*@[a-z0-9]+([-\.]?[a-z0-9]+)*[\.][a-z]{2,3}(([\.][a-z]{2})?)$/i;
	var val = getObjById(id).value;
	if (val == '' || val == null || val ==' '){
		setValueById(tid,'')
	}else{
		return alertMsg(id,tid,pattern,msg);
	}
}

//验证数字
function checkNumber(id,tid,msg){
	var pattern = /^[0-9]+$/;
	var val = getObjById(id).value;
	if (val == '' || val == null || val ==' '){
		setValueById(tid,'')
	}else{
		return alertMsg(id,tid,pattern,msg);
	}
}

//消息提示
function alertMsg(id,tid,pattern,msg){
	var obj = getObjById(id);
	var pattern_1 = /(^\s*)|(\s*$)/g;
	var val = obj.value.replace(pattern_1,'');
	if(!pattern.test(val)){
		getObjById(id).value = '';
		if(!tid){
			alert(msg);
		}else{
			getObjById(tid).innerHTML = "<font color='red'>&nbsp;"+msg+"&nbsp;</font>";
		}
		return false;
	}else if(tid)
		getObjById(tid).innerHTML = "<font color='green'>&nbsp;输入正确...&nbsp;</font>";
	return true;
}

//通过id获取对象
function getObjById(id){
	return document.getElementById(id);
}

//通过id赋值
function setValueById(id,value){
	document.getElementById(id).innerHTML = value;
}

//全部选中
function checkAll(form, field, value) {
	for (i = 0; i < form.elements.length; i++) {
		if(form.elements[i].name == field)
			form.elements[i].checked = value;
	}
}

//判断是否删除
function del_records(form,name) {
	var del = false;
	for (i = 0; i < form.elements.length; i++){
		if (form.elements[i].name == name){
			if (form.elements[i].checked == true){
				del = true;
				break;
			}
		}		
	}
	if (del){
		if(confirm('确定要删除所选择的记录吗?'))
			return true;
		return false;		
	}else{
		alert("请选择相关操作记录!");
		return false;
	}
}

//设定标签值
function setTagValue(obj){
	var value = prompt('请输入标签名称:','');
	if(value==null)return false; 
	if(value == '' || value.replace(/^\s+/, '').replace(/\s+$/) =='')return false;
	obj.value=value.replace(/^\s+/, '').replace(/\s+$/);
}

//设定模块是否显示
function displayOrNo(id,value){
	var obj = getObjById(id);
	obj.style.display = value;
}

//给父级id赋值
function setParentValue(id, value){
	if (id == null || id =='' || id == ' ') return false;
	if (is_array(id)){
		if (is_array(value)){
			for (var i=0; i<id.length; i++){
				parent.document.getElementById(id[i]).value = value[i];
				//alert(id[i]+"="+document.getElementById(id[i]).value);
			}
		}else{
			for (var i=0; i<id.length; i++){
				parent.document.getElementById(id[i]).value = value;
			}
		}
	}else{
		parent.document.getElementById(id).value = value;
	}
}

//判断变量是否为数组
function is_array(value)  {
	if(typeof value == 'object' && typeof value.length == 'number'){
		return true;  
	}
	else{
		return false;  
	}
}

//清除数据
function clear_data(id){
	if(is_array(id)){
		for(var i=0; i<id.length; i++){
			document.getElementById(id[i]).value = '';
		}
	}else{
		document.getElementById(id).value = '';
	}
}

//验证浮点数
function is_float(id,tid,msg){
	var pattern = /^[0-9]+(\.[0-9]+)*$/;
	var value = document.getElementById(id).value;
	if (value == '' || value == null || value ==' '){
		setValueById(tid,'')
	}else{
		return alertMsg(id,tid,pattern,msg);
	}
}

