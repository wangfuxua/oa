<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta http-equiv='Refresh' content='<?php echo ($waitSecond); ?>;URL=<?php echo ($jumpUrl); ?>'>
<title>Loading...</title>
<style type="text/css">
<!--
*{margin:0; padding:0; border:0;}
html,body{background:#fff; font-size:12px; color:#000; font-family:"宋体";}
div{margin:0 auto;}
ul,li{padding:0; margin:0; list-style-type:none;}
form{padding:0; margin:0;}
em{font-style:normal;}
.str{font-weight:bold;}
h1,h2,h3,h4,h5,h6{font-size:14px; text-align:left;}
a{color:#000; text-decoration:none;}a:hover{color:#d12700;}
td{vertical-align:top; text-align:left;}
.clear {height:0;font-size:0px;line-height:0px;clear:both;float:none;overflow:hidden}
/* Loading... */
.loading {width:400px;height:60px;margin:0 auto;line-height:30px;padding:10px;border:1px silver solid;position:absolute; top:50%; left:50%;margin:-30px 0 0 -200px}
.loading h1 {height:30px;width:80px;float:left;padding-left:40px;color:gray;background:url(/images/loading.gif) no-repeat 10px 5px}
.loading p {padding-left:40px}
.loading span {font-weight:bold;margin:0 5px}
.loading a {color:blue}.loading a:hover {color:#CC0000}
.loading p.erro {color:red;font-size:14px;font-weight:bold}
.loading p.ok	{color:#009900;font-size:14px;font-weight:bold}
.loading p.tips {color:gray}
-->
</style>
</head>

<body><?php if(isset($message)): ?><div class="loading">
		<h1>操作提示：</h1> 
		<p class="erro"><?php echo ($msgTitle); ?><?php echo ($message); ?></p><?php endif; ?>
	<?php if(isset($error)): ?><div class="loading">
		<h1>操作提示：</h1> 
		<p class="erro"><?php echo ($error); ?></p><?php endif; ?>	
	<?php if(isset($closeWin)): ?><p class="tips">系统将在<span><?php echo ($waitSecond); ?></span>秒后自动跳转,如果不想等待,点击 <a href="<?php echo ($jumpUrl); ?>">这里</a>  关闭 </p>
	</div><?php endif; ?>
	<?php if(!isset($closeWin)): ?><p class="tips">系统将在<span><?php echo ($waitSecond); ?></span>秒后自动跳转,如果不想等待,点击 <a href="<?php echo ($jumpUrl); ?>">这里</a>  跳转 </p>
	</div><?php endif; ?>
</body>

</html>