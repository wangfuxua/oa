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

<script type="text/javascript" src="/oa/Tpl/default/Public/js/iframe.js" ></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/js/tree.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	//$.formValidator.initConfig({formid:"form1",onerror:function(){alert("校验没有通过，具体错误请看错误提示")}});
	$.formValidator.initConfig({formid:"form1",onerror:function(msg){alert(msg)}});
	$("#name").formValidator({onshow:"请输入字段描述",onfocus:"字段描述不能为空",oncorrect:"字段描述合法"}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"字段描述两边不能有空符号"},onerror:"字段描述不能为空,请确认"});
	$("#fieldname").formValidator({onshow:"请输入字段（英文名称）",onfocus:"字段（英文名称）不能为空",oncorrect:"字段（英文名称）合法"}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"字段（英文名称）两边不能有空符号"},onerror:"字段（英文名称）不能为空,请确认"}).regexValidator({regexp:"username",datatype:"enum",onerror:"字段（英文名称）格式不正确，只能输入字母或数字"});
	$("#order").formValidator({onshow:"请输入数字",onfocus:"序号不能为空",oncorrect:"合法"}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"序号两边不能有空符号"},onerror:"序号不能为空,请确认"}).regexValidator({regexp:"username",datatype:"enum",onerror:"序号格式不正确，只能输入数字"});
	$("#formtype").formValidator({onshow:"请输入字段类型",onfocus:"字段类型不能为空",oncorrect:"字段类型合法"}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"字段类型两边不能有空符号"},onerror:"字段类型不能为空,请确认"});
});
</script>
    <script type="text/javascript">
        /*  初始化Ajax */
        if(!KD){
            var KD = {
                $: function(id) {
                    return document.getElementById(id);
                },
                InitAjax: function() {
                    var ajax = false;
                    try {
                        ajax = new ActiveXObject("Msxml2.XMLHTTP");
                    } catch (e) {
                        try {
                            ajax = new ActiveXObject("Microsoft.XMLHTTP");
                        } catch (E) {
                            ajax = false;
                        }
                    }
                    if (!ajax && typeof XMLHttpRequest != 'undefined') {
                        ajax = new XMLHttpRequest();
                    }
                    return ajax;
                }
            }
        }

        function selectChoose(value, name, divId, postUrl) {
            var postStr = name+"=" + value;

            var ajax = KD.InitAjax();
            ajax.open("POST", postUrl, true);
            ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            ajax.send(postStr);
            ajax.onreadystatechange = function() {
            if (ajax.readyState == 4 && ajax.status == 200) {
                    KD.$(divId).innerHTML = ajax.responseText;
                }
            }
        }
    </script>
<style type="text/css">
.ie7 {}
.ie7 input{min-width:auto}
</style>
<body>
<h2 class="dm_schedule">创建人事档案字段</h2>
		<div class="dm_conthree">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dm_tablezana">
		
		<form action="/index.php/HrmsModule/add"  method="post" name="form1" id="form1">	
		<tr>
				<td valign="top" class="dm_zanal">
					<font color="red">*</font> <strong>字段描述</strong>
	  			</td>
				<td>
					<input name="name" id="name"  type="text" class="dm_blur3" />
				</td>
				<td width="20%"><div id="nameTip" style="width:250px"></div></td>
		</tr>
		<tr>
				<td valign="top" class="dm_zanal">
					<font color="red">*</font> <strong>字段名称</strong>
	  			</td>
				<td>
					<input name="fieldname" id="fieldname"  type="text" class="dm_blur3" />
				</td>
				<td width="20%"><div id="fieldnameTip" style="width:250px"></div></td>
		</tr>
		<tr>
				<td valign="top" class="dm_zanal">
					<font color="red">*</font> <strong>序号</strong>
				</td>
				<td>
					<input name="order" id="order" type="text" class="dm_blur3" />
				</td>
				<td width="20%"><div id="orderTip" style="width:250px"></div></td>
		</tr>
		<tr>
    	<td valign="top" class="dm_zanal">
					<font color="red">*</font> <strong>字段类型</strong><br />
	  			</td>
	  			<td>
       <select name="formtype" id="formtype" onchange="selectChoose(this.value,this.name,'msg','/index.php/HrmsModule/select')">
       					<option value="">选择类型</option>
						<option value="text">单行文本</option>
						<option value="textarea">多行文本</option>
						<option value="box">选项</option>
						<option value="datetime">日期和时间</option>
						<option value="file">上传文件</option>
	  				</select>
	  				</td>
	  				<td><div id="formtypeTip" style="width:250px"></div></td>
    </tr>
    <tr><td><strong>相关参数</strong>
设置表单相关属性</td><td><div id="msg"></div></td><td></td></tr>
	<tr>
		<td colspan="3" class="dm_btnzan">
		<button name="submit" type="submit" >提交</button>
		</td>
	</tr>
	<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>    
	</table>
	</div>
<h2 class="dm_schedule">使用字段</h2>
		<div class="dm_conthree">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dm_tablezana">
		
		<form action="/index.php/HrmsModule/fileOrder"  method="post" name="form1" >	
		<tr>	<td>排序</td>
				<td valign="top" class="dm_zanal">字段名</td>
				<td>类型</td>
				<td>属性</td>
				<td>管理</td>
			</tr>
		<?php if(is_array($hrmsList)): ?><?php $i = 0;?><?php $__LIST__ = $hrmsList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
				<td width="10%" class="ie7"><input name='listorder[<?php echo ($vo[fieldId]); ?>]' type='text' value='<?php echo ($vo[order]); ?>'></td>
				<td width="30%"><?php echo ($vo['name']); ?></td>
				<td width="20%"><?php echo ($vo['formtype']); ?></td>
				<td width="20%"><?php echo (hrmsField($vo['state'])); ?></td>
				<?php if($vo['state']==2): ?><td width="20%"><a href="/index.php/HrmsModule/edit/fieldId/<?php echo ($vo['fieldId']); ?>">修改</a>|<a href="/index.php/HrmsModule/del/fieldId/<?php echo ($vo['fieldId']); ?>">删除</a></td>
				<?php else: ?><td width="20%"></td><?php endif; ?>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			<tr>
				<td colspan="5" class="dm_btnzan">
					<button name="submit" type="submit">完成</button>
				</td>
			</tr><?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
		</table>
		</div>
</body>
</html>