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
	</head>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "个人设置",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 4,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "个人资料", Url: "/index.php/Personinfo/index", Cls: "", Icon: "", IconCls: "ico ico-userInfo" },
            { Title: "个性设置", Url: "/index.php/Personinfo/set", Cls: "", IconCls: "ico ico-userCustom" },
            { Title: "个人网址", Url: "/index.php/Personinfo/url", Cls: "", IconCls: "ico ico-userUrl" },
            { Title: "修改密码", Url: "/index.php/Personinfo/pass", Cls: "", IconCls: "ico ico-userPassword" }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 56);
    
    });

</script>
<body onLoad="document.form1.PASS0.focus();">


<div class="KDStyle" id="main">
<form method="post" action="/index.php/Personinfo/passsubmit" name="form1" >
<table>

<tr>
	<td width="150">用户名：</td>
	<td ><?php echo ($LOGIN_USER_ID); ?></td>
</tr>
<tr>
	<td >原密码：</td>
	<td >
	  <input type="password" name="PASS0"  class="BigInput" size="15" maxlength="20" >
	</td>
</tr>
<tr>
	<td >新密码：</td>
	<td >
	  <input type="password" name="PASS1"  class="BigInput" size="15" maxlength="20" > 6-20位
	</td>
</tr>

<tr>
	<td >确认新密码：</td>
	<td >
	  <input type="password" name="PASS2"  class="BigInput" size="15" maxlength="20" > 6-20位
	</td>
</tr>

<tr>
	<td >上次修改时间：</td>
	<td >
	  <?php echo (is_array($row)?$row["LAST_PASS_TIME"]:$row->LAST_PASS_TIME); ?>
	</td>
</tr>

<tr>
	<td >密码过期：</td>
	<td >
	  <?php echo ($REMARK); ?>
	</td>
</tr>

<tfoot>
    <th colspan="2" >
      <button type="submit" value="保存修改">保存修改</button>
    </th>
</tfoot>

</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>


</body>
</html>