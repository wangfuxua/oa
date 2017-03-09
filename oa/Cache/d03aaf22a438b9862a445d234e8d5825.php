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
/*
function CheckForm()
{
   if(document.form1.USER_NAME.value=="")
   { alert("姓名不能为空！");
     return (false);
   }
   return (true);
}
*/
function CheckDate(op)
{
   mon=document.form1.BIR_MON.value;
   year=document.form1.BIR_YEAR.value;

   if(mon=="04" || mon=="06" || mon=="09" || mon=="11")
   {
     if(document.form1.BIR_DAY.options.length>30)
         document.form1.BIR_DAY.remove(30);
     else if(document.form1.BIR_DAY.options.length<30)
     {
       if(document.form1.BIR_DAY.options.length==28)
       {
         var my_option = document.createElement("OPTION");
         my_option.text="29";
         my_option.value="29";
         document.form1.BIR_DAY.add(my_option);
       }

       var my_option = document.createElement("OPTION");
       my_option.text="30";
       my_option.value="30";
       document.form1.BIR_DAY.add(my_option);
     }
   }

   else if(mon=="02")
   {
     document.form1.BIR_DAY.remove(30);
     document.form1.BIR_DAY.remove(29);

     if(document.form1.BIR_DAY.options.length>28)
     if (!(year%400==0 || (year%4==0 && year%100!=0)))
       document.form1.BIR_DAY.remove(28);

     if(document.form1.BIR_DAY.options.length<29)
     if ((year%400==0 || (year%4==0 && year%100!=0)))
     {
       var my_option = document.createElement("OPTION");
       my_option.text="29";
       my_option.value="29";
       document.form1.BIR_DAY.add(my_option);
     }
   }
   else
   {
     if(document.form1.BIR_DAY.options.length<31)
     {
       if(document.form1.BIR_DAY.options.length==28)
       {
         var my_option = document.createElement("OPTION");
         my_option.text="29";
         my_option.value="29";
         document.form1.BIR_DAY.add(my_option);
       }

       if(document.form1.BIR_DAY.options.length==29)
       {
         var my_option = document.createElement("OPTION");
         my_option.text="30";
         my_option.value="30";
         document.form1.BIR_DAY.add(my_option);
       }

       var my_option = document.createElement("OPTION");
       my_option.text="31";
       my_option.value="31";
       document.form1.BIR_DAY.add(my_option);
     }
   }
   
   //if(op==1)
    //  document.form1.USER_NAME.focus();
}

</script>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 56);

		createHeader({
        Title: "个人设置",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 1,
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
<body onLoad="CheckDate(1);">

<div class="KDStyle" id="main">
<form action="/index.php/Personinfo/submit"  method="post" name="form1" onSubmit="return CheckForm();">
 <table>
  
  			<colgroup>
				<col width="80"></col>
				<col></col>
			</colgroup>
			<tr><th class="acc" colspan="2">个人信息</th></tr>

    <tr>
      <td> 姓名：</td>
      <td><?php echo (is_array($row)?$row["USER_NAME"]:$row->USER_NAME); ?>
        <input type="hidden" name="USER_NAME" size="10" maxlength="10" class="BigStatic" value="<?php echo (is_array($row)?$row["USER_NAME"]:$row->USER_NAME); ?>" readonly>
      </td>
    </tr>
    <tr>
      <td> 性别：</td>
      <td>
        <select name="SEX" class="BigSelect">
          <option value="0" <?php if($row[SEX] == 0): ?>selected<?php endif; ?>>男</option>
          <option value="1" <?php if($row[SEX] == 1): ?>selected<?php endif; ?>>女</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>生日：</td>
      <td>

<!-------------- 年 ------------>
        <select name="BIR_YEAR" class="BigSelect" onChange="CheckDate();">
<?php
        $j = date('Y');
        for($I=$j;$I>=$j-100;$I--)
        {
?>
          <option value="<?php echo $I ?>" <?php if($I==$row[BIR_YEAR]) echo "selected";?>><?php echo $I?></option>
<?php
        }
?>
        </select>年

<!-------------- 月 ------------>
        <select name="BIR_MON" class="BigSelect" onChange="CheckDate();">
<?php
        for($I=1;$I<=12;$I++)
        {
          if($I<10)
             $I="0".$I;
?>
          <option value="<?php echo $I?>" <?php if($I==$row[BIR_MON]) echo "selected";?>><?php echo $I?></option>
<?php
        }
?>
        </select>月

<!-------------- 日 ------------>
        <select name="BIR_DAY" class="BigSelect">
<?php
        for($I=1;$I<=31;$I++)
        {
          if($I<10)
             $I="0".$I;
?>
          <option value="<?php echo $I?>" <?php if($I==$row[BIR_DAY]) echo "selected";?>><?php echo $I?></option>
<?php
        }
?>
        </select>日
      </td>
    </tr>
    <tr>
      <th class="acc" colspan="2">联系方式（单位）</th>
    </tr>
    <tr>
      <td> 单位电话：</td>
      <td>
        <input type="text" name="TEL_NO_DEPT" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["TEL_NO_DEPT"]:$row->TEL_NO_DEPT); ?>">
      </td>
    </tr>

    <tr>
      <td> 单位传真：</td>
      <td>
        <input type="text" name="FAX_NO_DEPT" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["FAX_NO_DEPT"]:$row->FAX_NO_DEPT); ?>">
      </td>
    </tr>

    <tr>
      <th class="acc" colspan="2">联系方式（家庭）</th>
    </tr>

    <tr>
      <td> 家庭住址：</td>
      <td>
        <input type="text" name="ADD_HOME" size="40" maxlength="100" class="BigInput" value="<?php echo (is_array($row)?$row["ADD_HOME"]:$row->ADD_HOME); ?>">
      </td>
    </tr>

    <tr>
      <td> 家庭邮编：</td>
      <td>
        <input type="text" name="POST_NO_HOME" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["POST_NO_HOME"]:$row->POST_NO_HOME); ?>">
      </td>
    </tr>

    <tr>
      <td> 家庭电话：</td>
      <td>
        <input type="text" name="TEL_NO_HOME" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["TEL_NO_HOME"]:$row->TEL_NO_HOME); ?>">
      </td>
    </tr>

    <tr>
      <td> 手机：</td>
      <td>
        <input type="text" name="MOBIL_NO" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["MOBIL_NO"]:$row->MOBIL_NO); ?>"><br>
        填写后可接收OA系统发送的手机短信
      </td>
    </tr>

    <tr>
      <td> 寻呼机：</td>
      <td>
        <input type="text" name="BP_NO" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["BP_NO"]:$row->BP_NO); ?>">
      </td>
    </tr>

    <tr>
      <td> 电子邮件：</td>
      <td>
        <input type="text" name="EMAIL" size="25" maxlength="50" class="BigInput" value="<?php echo (is_array($row)?$row["EMAIL"]:$row->EMAIL); ?>">
      </td>
    </tr>

    <tr>
      <td> OICQ号码：</td>
      <td>
        <input type="text" name="OICQ_NO" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["OICQ_NO"]:$row->OICQ_NO); ?>">
      </td>
    </tr>

    <tr>
      <td> ICQ号码：</td>
      <td>
        <input type="text" name="ICQ_NO" size="25" maxlength="25" class="BigInput" value="<?php echo (is_array($row)?$row["ICQ_NO"]:$row->ICQ_NO); ?>">
      </td>
    </tr>
    <tr>
      <td> MSN：</td>
      <td>
        <input type="text" name="MSN" size="25" maxlength="50" class="BigInput" value="<?php echo (is_array($row)?$row["MSN"]:$row->MSN); ?>">
      </td>
    </tr>
    <tfoot>
      <th colspan="2" nowrap>
        <button type="submit" value="保存修改" class="btnFnt" >保存修改</button>
      </th>
    </tfoot>
    
  </table>
  <?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>


</body>
</html>