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
   //document.form1.CONTENT.value=EDIT_HTML.GetHtml();
   if(document.form1.CONTENT.value=="")
   { alert("日志内容不能为空！");
     return (false);
   }
   return (true);
}
</script>
<script type="text/javascript" src="/oa/Tpl/default/Public/DatePicker/WdatePicker.js"></script>
    <!-- Editor Start --> 
    <script type="text/javascript" src="/oa/Tpl/default/Public/neweditor/tiny_mce.js"></script> 
    <script type="text/javascript">
        tinyMCE.init({
            mode: "exact",
            elements: "CONTENT",          // 要显示编辑器的textarea容器ID
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
<script type="text/javascript">
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "工作日志",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 3,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "日志列表", Url: "/index.php/Diary/index", Cls: "", Icon: "", IconCls: "ico ico-list" },
            { Title: "添加日志", Url: "/index.php/Diary/add", Cls: "", IconCls: "ico ico-add" },
            { Title: "修改日志", Url: "#", Cls: "", IconCls: "ico ico-edit" },
            { Title: "日志查询", Url: "/index.php/Diary/search", Cls: "", IconCls: "ico ico-query" }
        ]
    });		   
});
</script>


<body>

<div class="KDStyle" id="main">
<form action="/index.php/Diary/update"  method="post" name="form1" onSubmit="return CheckForm();">
  <table>
				<colgroup>
					<col width="120"></col>
					<col></col>
				</colgroup>
<tbody>				  
    <tr>
      <td>日志类型：</td>
      <td>
        <select name="DIA_TYPE" class="BigSelect">
          
          <option value="1" <?php if($ROW[DIA_TYPE] == 1): ?>selected<?php endif; ?>>工作日志</option>
          <option value="3" <?php if($ROW[DIA_TYPE] == 3): ?>selected<?php endif; ?>>工作周报</option>
          <option value="4" <?php if($ROW[DIA_TYPE] == 4): ?>selected<?php endif; ?>>工作月报</option>
          <option value="5" <?php if($ROW[DIA_TYPE] == 5): ?>selected<?php endif; ?>>年度总结</option>
          <option value="2" <?php if($ROW[DIA_TYPE] == 2): ?>selected<?php endif; ?>>个人日志</option>
          
        </select>
      </td>
    </tr>
    <tr>
      <td>日&nbsp;&nbsp;&nbsp;&nbsp;期：</td>
      <td>
       <fieldset class="date">
        <input type="text" name="DIA_DATE" size="30" maxlength="30"  value="<?php echo (is_array($ROW)?$ROW["DIA_DATE"]:$ROW->DIA_DATE); ?>" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})">
        <img src="/oa/Tpl/default/Public/images/ico/calendar.png" alt=""  onclick="WdatePicker({el:$dp.$('DIA_DATE'),dateFmt:'yyyy-MM-dd'})"  />       
        </fieldset>  
      </td>
    </tr>
    <tr>
      <td>标题：</td>
      <td>
        <input type="text" name="SUBJECT" size="50" maxlength="80" value="<?php echo ($ROW[SUBJECT]); ?>">
      </td>
    </tr>     
    <tr>
    <td>内容：</td>
      <td class="clearTable">
						    <fieldset class="HtmlContet">
							    <textarea name="CONTENT" id="CONTENT" cols="" rows="" class="content"><?php echo (is_array($ROW)?$ROW["CONTENT"]:$ROW->CONTENT); ?></textarea>
							</fieldset>
        
      </td>
    </tr>
    <tbody>
    <tfoot>
    <tr>
      <th colspan="2" nowrap>
        <input type="hidden" name="DIA_ID" value="<?php echo (is_array($ROW)?$ROW["DIA_ID"]:$ROW->DIA_ID); ?>">
        <button type="submit" value="保存" class="btnFnt">保存</button>
        <button type="button" value="返回" class="btnFnt" onClick="location='/index.php/Diary/index'">返回</button>
      </th>
    </tr>
    <tfoot>
  </table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>


</body>
</html>