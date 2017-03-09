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

<link type="text/css" rel="stylesheet" href="/oa/Tpl/default/Public/css/validator.css"></link>
<script src="/oa/Tpl/default/Public/js/jquery_last.js" type="text/javascript"></script>
<script src="/oa/Tpl/default/Public/js/formValidator.js" type="text/javascript" charset="UTF-8"></script>
<script src="/oa/Tpl/default/Public/js/formValidatorRegex.js" type="text/javascript" charset="UTF-8"></script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	//$.formValidator.initConfig({onerror:function(){alert("校验没有通过，具体错误请看错误提示")}});
	$.formValidator.initConfig({formid:"form1",onerror:function(msg){}});

	$("#name").formValidator({onshow:"*",oncorrect:"恭喜你,你输对了"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"非空字符格式不正确"});
	
	$("#number").formValidator({onshow:"*",oncorrect:"恭喜你,你输对了"}).regexValidator({regexp:"intege1",datatype:"enum",onerror:"正整数格式不正确"});
	
	$("#price").formValidator({onshow:"*",oncorrect:"恭喜你,你输对了"}).regexValidator({regexp:"intege1",datatype:"enum",onerror:"正数格式不正确"});
});

</script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 56);

		createHeader({
        Title: "固定资产管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: <?php if($row[GDZC_ID] != ''): ?>3<?php else: ?>2<?php endif; ?>,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "固定资产列表", Url: "/index.php/Gdzc/manage", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "添加固定资产", Url: "/index.php/Gdzc/form", Cls: "", IconCls: "ico ico-view" }
			<?php if($row[GDZC_ID] != ''): ?>,
            { Title: "修改固定资产", Url: "#", Cls: "", IconCls: "ico ico-view" }<?php endif; ?>
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("KDMain", 56); 
    
    });

</script>
<body>

<div class="KDStyle" id="KDMain">	
<table>
<col width="120px" />
  <form action="/index.php/Gdzc/formsubmit" method="post" name="form1" id="form1">
   <tr>
    <th>固定资产名称：</th>
    <td>
    	<input type="text" name="MC" id="name" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["MC"]:$row->MC); ?>">&nbsp;<span id="nameTip"></span>
    </td>
   </tr>
   <tr>
    <th>编号：</th>
    <td>
        <input type="text" name="BH" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["BH"]:$row->BH); ?>">&nbsp;
    </td>
   </tr>
   <tr>
    <th>资产类别：</th>
    <td>
        <input type="text" name="LB" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["LB"]:$row->LB); ?>">&nbsp;
    </td>
   </tr>
   <tr>
    <th>数量：</th>
    <td>
        <input type="text" name="SL" id="number" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["SL"]:$row->SL); ?>">&nbsp;<span id="numberTip"></span>
    </td>
   </tr>
   <tr>
    <th>规格型号：</th>
    <td>
        <input type="text" name="GGXH" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["GGXH"]:$row->GGXH); ?>">&nbsp;
    </td>
   </tr>
   <tr>
    <th>单价：</th>
    <td>
        <input type="text" name="DJ" id="price" size="33" maxlength="10" value="<?php echo (is_array($row)?$row["DJ"]:$row->DJ); ?>">&nbsp;<span id="priceTip"></span>
    </td>
   </tr>
   <tr>
    <th>供货单位：</th>
    <td>
        <input type="text" name="JZDW" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["JZDW"]:$row->JZDW); ?>">&nbsp;
    </td>
   </tr>
   <tr>
    <th>开始使用日期：</th>
    <td>
        <input type="text" name="KSSYRQ" size="30" maxlength="30" value="<?php echo (is_array($row)?$row["KSSYRQ"]:$row->KSSYRQ); ?>" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'});">&nbsp;
    </td>
   </tr>
   <tr>
    <th>预计使用年限：</th>
    <td>
        <input type="text" name="YJSYNX" size="33" maxlength="10" value="<?php echo (is_array($row)?$row["YJSYNX"]:$row->YJSYNX); ?>">&nbsp;
    </td>
   </tr>
   <tr>
    <th>使用部门：</th>
    <td>
		<select name="SYBM_ID">
		<?php echo ($my_dept_tree); ?>
        </select>
    </td>
   </tr>
   <tr>
    <th>所在地：</th>
    <td>
		<input type="text" name="SZD" size="33" maxlength="100" value="<?php echo (is_array($row)?$row["SZD"]:$row->SZD); ?>">&nbsp;
    </td>
   </tr>
   <tr>
    <th>管理人：</th>
    <td>
       <select name="GLR_ID">
       <?php echo ($my_user_list); ?>
        </select>
    </td>
   </tr>
   <tr>
    <th>资产录入员：</th>
    <td>
        <?php echo ($LOGIN_USER_NAME); ?>&nbsp;
    </td>
   </tr>
   <tfoot>
   <tr>
    <th  colspan="2">
        <input type="hidden" name="GDZC_ID" value="<?php echo ($row[GDZC_ID]); ?>"> 
        <?php if($row[GDZC_ID]): ?><button type="submit" value="修改" class="btnFnt" title="修改固定资产" name="button">修改</button>
        <?php else: ?>
        <button type="submit" value="添加" class="btnFnt" title="增加固定资产" name="button">添加</button><?php endif; ?>
        <button type="button" value="返回" class="btnFnt" onClick="location='/index.php/Gdzc/manage'">返回</button>
    </th>
   </tr>
   </tfoot>
  <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</table>
</div>

</body>
</html>