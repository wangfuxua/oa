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
<script language="JavaScript">
function CheckForm()
{
   if(document.form1.URL_DESC.value=="")
   { alert("说明不能为空！");
     return (false);
   }
   if(document.form1.URL.value=="")
   { alert("网址不能为空！");
     return (false);
   }
}

function delete_url(URL_ID)
{
 msg='确认要删除该项网址么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Personinfo/urldelete/URL_ID/" + URL_ID;
  window.location=URL;
 }
}
function delete_all()
{
 msg='确认要删除所有网址么？';
 if(window.confirm(msg))
 {
  URL="/index.php/Personinfo/urldelete";
  window.location=URL;
 }
}
</script>
<script type="text/javascript">
$(function(){
        setDomHeight("leftpannel", 56);
        setDomHeight("mainPannel", 56);
        setDomHeight("news_main",56);
        setDomWidth("mainPannel", 200);

		createHeader({
        Title: "公告通知管理",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 3,
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
<body onLoad="document.form1.URL_NO.focus();">

<div class="KDStyle" id="main">
<table>
  <form action="/index.php/Personinfo/urlsubmit"  method="post" name="form1" onsubmit="return CheckForm();">  
<caption class="nostyle">添加个人网址</caption>  
   <tr>
    <td>序号：</td>
    <td>
        <input type="text" name="URL_NO" class="BigInput" size="10" maxlength="25">
    </td>
   <tr>
    <td>说明：</td>
    <td>
        <input type="text" name="URL_DESC" class="BigInput" size="25" maxlength="200">
    </td>
   </tr>
   <tr>
    <td>网址：</td>
    <td>
        <input type="text" name="URL" class="BigInput" size="25" maxlength="200" value="http://">
    </td>
   </tr>
   <tfoot>
   <tr>
    <th colspan="2">
        <input type="submit" value="添加" class="btnFnt" title="添加网址" name="button">
    </th>
    </tr>
    </tfoot>
  <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</table>

<div align="center">

    <table>
    <caption class="nostyle">管理个人网址</caption>
    <thead>
      <tr>
      <th>序号</th>
      <th>说明</th>
      <th>网址</th>
      <th>操作</th>
      </tr>
    </thead>
    <?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
      <td><?php echo (is_array($vo)?$vo["URL_NO"]:$vo->URL_NO); ?></td>
      <td><?php echo (is_array($vo)?$vo["URL_DESC"]:$vo->URL_DESC); ?></td>
      <td><a href="<?php echo (is_array($vo)?$vo["URL"]:$vo->URL); ?>" target="_blank"><?php echo (is_array($vo)?$vo["URL"]:$vo->URL); ?></a></td>
      <td width="80">
      <a href="/index.php/Personinfo/urledit/URL_ID/<?php echo (is_array($vo)?$vo["URL_ID"]:$vo->URL_ID); ?>"> 编辑</a>
      <a href="javascript:delete_url('<?php echo (is_array($vo)?$vo["URL_ID"]:$vo->URL_ID); ?>');"> 删除</a>
      </td>
    </tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
    <tfoot>
     <tr>
      <td colspan="5">
      <input type="button" class="btnFnt" onClick="javascript:delete_all();" value="全部删除">
      </td>
      </tr>
    </tfoot>
    </table>
    <?php echo ($page); ?>
</div>
</div>

</body>
</html>