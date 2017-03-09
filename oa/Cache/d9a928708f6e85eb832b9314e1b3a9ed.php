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


<script language="JavaScript">
function CheckForm()
{
   if(document.form1.CONTENT.value=="")
   { alert("事务内容不能为空！");
     return (false);
   }
   form1.submit();
}

</script>
<script type="text/javascript">
$(function(){
		setDomHeight("main", 56);
		createHeader({
        Title: "日程安排",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 3,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "日程安排", Url: "/index.php/Calendar/index", Cls: "", Icon: "", IconCls: "ico ico-calendar" },
            { Title: "日常事务", Url: "/index.php/Calendar/affairIndex", Cls: "", IconCls: "ico ico-clock" },
            { Title: "新建日常事务", Url: "/index.php/Calendar/affairform", Cls: "", IconCls: "ico ico-add" }
        ]
    });		   
});
</script>
<body onLoad="document.form1.CONTENT.focus();">

<div class="KDStyle" id="main">
<form action="/index.php/Calendar/calformsubmit"  method="post" name="form1">
 <table>
 				<caption class="nostyle"><?php echo ($desc); ?>（<?php echo ($YEAR); ?>年<?php echo ($MONTH); ?>月<?php echo ($DAY); ?>日）</caption>
				<colgroup>
					<col width="80"></col>
					<col width=""></col>
				</colgroup>
				  
  <tbody>
    <tr>
      <td>开始时间：</td>
      <td>
<!-------------- 时 ------------>
        <select name="CAL_HOUR">
        <?php echo ($cal_hour); ?>
        </select>：

<!-------------- 分 ------------>
        <select name="CAL_MIN">
        <?php echo ($cal_min); ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>结束时间：</td>
      <td>

<!-------------- 时 ------------>
        <select name="END_HOUR">
        <?php echo ($end_hour); ?>
        </select>：

<!-------------- 分 ------------>
        <select name="END_MIN">
        <?php echo ($end_min); ?>
        </select>
      </td>
    </tr>
    <tr>
      <td> 事务类型：</td>
      <td>
        <select name="CAL_TYPE">
          <option value="1" <?php if($row[CAL_TYPE] == 1): ?>selected<?php endif; ?>>工作事务</option>
          <option value="2" <?php if($row[CAL_TYPE] == 2): ?>selected<?php endif; ?>>个人事务</option>
        </select>
      </td>
    </tr>
    <tr>
      <td> 事务内容：</td>
      <td>
        <textarea name="CONTENT" cols="45" rows="5" class="BigInput"><?php echo (is_array($row)?$row["CONTENT"]:$row->CONTENT); ?></textarea>
        
      </td>
    </tr>
    <tr>
      <td> 提醒：</td>
      <td> 
        <input type="checkbox" name="SMS_REMIND" checked>使用短信息提醒自己
      </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
      <td colspan="2" nowrap>
        <input type="hidden" name="CAL_ID" value="<?php echo (is_array($row)?$row["CAL_ID"]:$row->CAL_ID); ?>">
        <input type="hidden" name="CAL_YEAR" value="<?php echo ($YEAR); ?>">
        <input type="hidden" name="CAL_MON" value="<?php echo ($MONTH); ?>">
        <input type="hidden" name="CAL_DAY" value="<?php echo ($DAY); ?>">
        <button class="btnFnt" type="button" value="确定" onClick="CheckForm();">确定</button>
        <button class="btnFnt" type="button" value="返回" onClick="location='/index.php/Calendar/calmanage/YEAR/<?php echo ($YEAR); ?>/MONTH/<?php echo ($MONTH); ?>/DAY/<?php echo ($DAY); ?>'">
		返回</button>
      </td>
    </tr>
    </tfoot>
  </table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>
</body>
</html>