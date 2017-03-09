<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>系统发生错误</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="Generator" content="EditPlus"/>
<style>
body{
	font-family: Verdana;
	font-size:14px;
}
a{text-decoration:none;color:#174B73;}
a:hover{ text-decoration:none;color:#FF6600;}
h2{
	border-bottom:1px solid #DDD;
	padding:8px 0;
    font-size:25px;
}
.title{
	margin:4px 0;
	color:#F60;
	font-weight:bold;
}
.message,#trace{
	padding:1em;
	border:solid 1px #000;
	margin:10px 0;
	background:#FFD;
	line-height:150%;
}
.message{
	background:#FFD;
	color:#2E2E2E;
		border:1px solid #E0E0E0; 
}
#trace{
	background:#E7F7FF;
	border:1px solid #E0E0E0; 
	color:#535353;
}
.notice{
    padding:10px;	
	margin:5px; 
	color:#666; 
	background:#FCFCFC; 
	border:1px solid #E0E0E0; 
}
.red{
	color:red;
	font-weight:bold;
}
</style>
</head>
<body>
<div class="notice">
<h2>系统发生错误 </h2>
<div >您可以选择 [ <A HREF="<?php echo($_SERVER['PHP_SELF'])?>">重试</A> ] [ <A HREF="javascript:history.back()">返回</A> ] 或者 [ <A HREF="<?php echo(__APP__);?>">回到首页</A> ]</div>
<?php if(isset($e['file'])) {?>
<p><strong>错误位置:</strong>　FILE: <span class="red"><?php echo $e['file'] ;?></span>　LINE: <span class="red"><?php echo $e['line'];?></span></p>
<?php }?>
<p class="title">[ 错误信息 ]</p>
<p class="message"><?php echo $e['message'];?></p>
<?php if(isset($e['trace'])) {?>
<p class="title">[ TRACE ]</p>
<p id="trace">
<?php echo nl2br($e['trace']);?>
</p>	
<?php }?>
</div>

<div class="notice">
<h3>请您把错误信息反馈给我们，我们会及时处理 </h3>
  <!-- Editor Start  /oa/Tpl/default/Public--> 
    <script type="text/javascript" src="/<?php echo APP_NAME; ?>/Tpl/default/Public/neweditor/tiny_mce.js"></script> 
    <script type="text/javascript">
        tinyMCE.init({
            mode: "exact",
            elements: "content",          // 要显示编辑器的textarea容器ID
            language: "zh",
            theme: "advanced",
            plugins: "table,emotions",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_buttons1: "formatselect,fontselect,fontsizeselect,bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,outdent,indent,separator,link,image,forecolor,backcolor,table,emotions",
            theme_advanced_buttons2: ""
        });
    </script>
    <!-- Editor End -->
<div>
<form action="<?php echo(__APP__);?>/Feedback/submit"  method="post" name="form1">
  <table>
				<colgroup>
					<col width="80"></col>
					<col></col>
				</colgroup>
<tbody>				  
    <tr>
      <td>
		<textarea name="content" id="content" cols="20" rows="10">
		<?php if(isset($e['file'])) {?>
		错误位置:　FILE: <?php echo $e['file'] ;?>　LINE: <?php echo $e['line'];?>
		<?php }?>
		<?php echo $e['message'];?>
		</textarea>
      </td>
    </tr>
    <tbody>
    <tfoot>
    <tr>
      <td colspan="2" nowrap>
        <button type="submit" value="保存">保存</button>
        <button type="button" value="返回" onClick="javascript:window.history.back();">返回</button>
      </td>
    </tr>
    <tfoot>
  </table>
</form>
</div>
</div>
</body>
</html>
