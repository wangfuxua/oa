<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<meta name="Designer mail" content="appledesire(at)gmail.com" />
<meta name="author" content="JKTD-D-丁亚娟" />
<meta name="Keywords" content="" />
<meta name="Description" content="" />
<title><?php echo ($indexTitle); ?></title>
<style type="text/css">
body,ul,ol,li,p,h1,h2,h3,h4,h5,h6,form,fieldset,table,td,img,div,dl,dt,dd,em,strong{margin:0;padding:0;border:0;}
body{color:#000;font-size:12px;font-family:Arial, Helvetica, sans-serif, "宋体";line-height:22px;background:url(/oa/Tpl/default/Public/images/dm_bodybg.jpg) repeat-x #ffffff;}
a{text-decoration:none;color:#999;}
a:hover{color:#999;}
input{vertical-align:middle;}
.clear{clear:both;height:0;overflow:hidden;}
.bold{font-weight:bold;}
.dm_main{background:url(/oa/Tpl/default/Public/images/dm_mainbg.jpg) no-repeat;width:970px;height:600px;margin:0 auto;}
.dm_inside{width:402px;margin:0 auto;overflow:hidden;}
.dm_headimg{background:url(/oa/Tpl/default/Public/images/dm_headpic.jpg) no-repeat;width:330px;text-indent:-99em;overflow:hidden;height:80px;margin-top:142px;margin-left:4px;_margin-left:3px;}
.dm_headimg a{display:block;width:330px;height:80px;}
.dm_logink{width:400px;height:145px;border:1px solid #9b9bbf;background:#f9f9f9;}
.dm_loginl{width:250px;float:left;padding-left:30px;padding-top:24px;}
.dm_loginl p{height:42px;}
.dm_loginl p label{font-weight:bold;color:#d12700;line-height:30px;}
input.dm_putcss{background:url(/oa/Tpl/default/Public/images/inputbg.gif) no-repeat;width:183px;height:23px;border:none;font-size:12px;padding:7px 0 0 5px;}
.dm_loginr{width:118px;float:left;padding-top:32px;}
input.dm_btncss{background:url(/oa/Tpl/default/Public/images/dm_loginbtn.jpg) no-repeat;cursor:pointer;width:88px;height:65px;text-indent:-99em;overflow:hidden;border:none;display:block;}
.dm_faqdm{width:280px;padding:0 0 0 80px;color:#999;}
.dm_faqdm input{vertical-align:baseline;}
.dm_faqdm a{margin-left:20px;}
.dm_foot{background:url(/oa/Tpl/default/Public/images/dm_footbg.jpg) no-repeat;height:50px;text-align:center;color:#999;padding-top:30px;}
</style>
</head>

<body onload="javascript:form1.<?php echo ($focus); ?>.focus();">
	<div class="dm_main">
		<div class="dm_inside">
			<div class="dm_headimg"><a href="" title="">凯达OA第四代网络协同办公软件</a></div>
			<div class="dm_logink">
				<form name="form1" method="post" action="/index.php/Public/checklogin">
			    <div class="dm_loginl">
					<p><label>用户名：</label><input name="USERNAME" type="text" value="<?php echo ($USER_ID_COOKIE); ?>" class="dm_putcss"/></p>
					<p><label>密&nbsp;&nbsp;&nbsp;&nbsp;码：</label><input name="PASSWORD" type="password" value="" class="dm_putcss" /></p>
				</div>
				<div class="dm_loginr">
					<input name="" type="submit" value="登陆" class="dm_btncss" />
				</div>
				<div class="clear"></div>
				<div class="dm_faqdm">
<!--					<input name="" type="checkbox" value="" /><span>记住我的密码</span>
					<a href="" title="">系统帮助？</a>
-->					
				</div>
				<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
				
			</div>
			<div class="dm_foot">
				Copyright&copy;&nbsp;2009&nbsp;&nbsp;北京爱搜信息技术有限公司版权所有
			</div>
		</div>
	</div>
</body>
</html>