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

<script>
my_top=50;
my_left=50;
function my_note(CAL_ID)
{
  my_top+=25;
  my_left+=15;

  window.open("/index.php/Calendar/note/CAL_ID/"+CAL_ID,"note_win"+CAL_ID,"height=170,width=180,status=0,toolbar=no,menubar=no,location=no,scrollbars=auto,top="+ my_top +",left="+ my_left +",resizable=no");
}

function My_Submit()
{
  document.form1.submit();
}

function set_year(op)
{
  if(op==-1 && document.form1.YEAR.selectedIndex==0)
     return;
  if(op==1 && document.form1.YEAR.selectedIndex==(document.form1.YEAR.options.length-1))
     return;
  document.form1.YEAR.selectedIndex=document.form1.YEAR.selectedIndex+op;

  My_Submit();
}

function set_mon(op)
{
  if(op==-1 && document.form1.MONTH.selectedIndex==0)
     return;
  if(op==1 && document.form1.MONTH.selectedIndex==(document.form1.MONTH.options.length-1))
     return;
  document.form1.MONTH.selectedIndex=document.form1.MONTH.selectedIndex+op;

  My_Submit();
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
        Active: 1,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
            { Title: "日程安排", Url: "/index.php/Calendar/index", Cls: "", Icon: "", IconCls: "ico ico-calendar" },
            { Title: "日常事务", Url: "/index.php/Calendar/affairIndex", Cls: "", IconCls: "ico ico-clock" }
        ]
    });		   
});
</script>
<body>
	<div class="KDStyle" id="main">
  <table class="calendar">
  
		<thead>
<form action="/index.php/Calendar/index"  method="GET" name="form1"> 
				<tr>
					<th class="" colspan="7">
						<fieldset>
						    <img title="上一年" src="/oa/Tpl/default/Public/images/ico/arrow-left.gif" style="cursor:hand" onClick="set_year(-1);">
							
							<select name="YEAR" onChange="My_Submit();">
<?php
        for($I=2000;$I<=2045;$I++)
        {
?>
          <option value="<?php echo $I?>" <?php if($I==$YEAR) echo "selected";?>><?php echo $I?></option>
<?php
        }
?>
							</select>
							<img title="下一年" src="/oa/Tpl/default/Public/images/ico/arrow-right.gif" style="cursor:hand" onClick="set_year(1);" />
							年

                           <img title="上一月" src="/oa/Tpl/default/Public/images/ico/arrow-left.gif"  style="cursor:hand"onclick="set_mon(-1);" />
							<select name="MONTH" class="BigSelect" onChange="My_Submit();">
<?php
        for($I=1;$I<=12;$I++)
        {
          if($I<10)
             $I="0".$I;
?>
          <option value="<?php echo $I?>" <?php if($I==$MONTH) echo "selected";?>><?php echo $I?></option>
<?php
        }
?>
							</select>
                            <img title="下一月" src="/oa/Tpl/default/Public/images/ico/arrow-right.gif" style="cursor:hand" onClick="set_mon(1);" />							
							月
							<button onClick="location='/index.php/Calendar/index/YEAR/<?php echo $CUR_YEAR?>/MONTH/<?php echo $CUR_MONTH?>'">本月</button>

						</fieldset>
					</th>
				</tr>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
				</thead>

    <tbody>
    <tr class="acc tcenter">
      <th>日</th>
      <th>一</th>
      <th>二</th>
      <th>三</th>
      <th>四</th>
      <th>五</th>
      <th>六</th>
    </tr>
    
<?php
while ($DAY<$DATE)
{
  if($DAY == $CUR_DAY && $YEAR == $CUR_YEAR && $MONTH == $CUR_MON){
     $DAY_COLOR = " class='active'";
     $tdays="（今日）";
  }else{
  	 $tdays="";
     $DAY_COLOR = "";
  }
  $WEEK=date("w",mktime(0,0,0,$MONTH,$DAY,$YEAR));

  if ($WEEK==0 || $DAY==1)
  {
?>
   <tr class="m_h">
<?php
  }

  if($DAY==1)
  {
    for($I=0;$I<$WEEK;$I++)
    {
?>
     <td>&nbsp;</td>
<?php
    }
  }
?>
     <td <?php echo $DAY_COLOR?> <?php if($WEEK==0||$WEEK==6) echo "bgcolor=\"#F5F5F5\""?> width="14%">
      <span><a href="/index.php/Calendar/calform/YEAR/<?php echo $YEAR?>/MONTH/<?php echo $MONTH?>/DAY/<?php echo $DAY?>">添加</a></span> <em><a href="/index.php/Calendar/calmanage/YEAR/<?php echo $YEAR?>/MONTH/<?php echo $MONTH?>/DAY/<?php echo $DAY?>"><?php echo $DAY?>日<?php echo $tdays?></a></em>
      <br>
       <?php echo $CAL_ARRAY[$DAY]?>
     </td>
<?php
  if ($WEEK==6)
  {
?>
   </tr>
<?php
  }

  $DAY++;
}//while

//------------- 补结尾空格 -------------
if($WEEK!=6)
{
  for($I=$WEEK;$I<6;$I++)
  {
?>
     <td class="">&nbsp;</td>
<?php
  }
?>
   </tr>
</tbody>
<tfoot>
<?php
}


//-------------------------- 本月员工生日 -------------------------

if($PERSON_COUNT>0)
{
?>

      <tr class="">
      <td><strong>本月生日：</strong></td>
      <td colspan="6">
      <marquee style="color:#FF6600;" behavior=scroll scrollamount=3 scrolldelay=120 onmouseover='this.stop()' onmouseout='this.start()' border=0>
      <?php echo $PERSON_STR?>
      </marquee>
      </td>
      </tr>

<?php
}
?>
</tfoot>
	  </table>   
       </div>
</body>
</html>