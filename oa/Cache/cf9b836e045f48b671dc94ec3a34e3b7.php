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

<script type="text/javascript" src="/oa/Tpl/default/Public/style/default/js/iframe.js" ></script>
<script>
my_top=50;
my_left=50;

function my_note(CAL_ID)
{
  my_top+=25;
  my_left+=15;

  window.open("note.php?CAL_ID="+CAL_ID,"note_win"+CAL_ID,"height=170,width=180,status=0,toolbar=no,menubar=no,location=no,scrollbars=auto,top="+ my_top +",left="+ my_left +",resizable=no");
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

function user_list(str)
{
  ifrMain.location=str+"&DEPT_ID="+document.form1.DEPT_ID.value;
}

function cur_month(str)
{
  location=str+"&DEPT_ID="+document.form1.DEPT_ID.value;
}

</script>
<script type="text/javascript">
$(function(){
        setDomHeight("KDMain", 30);

		createHeader({
        Title: "员工日程安排",
        Icon: "",
        IconCls:"ico ico-head-news",
        Cls: "",
        Active: 3,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("KDMain", 30); 
    
    });

</script>
<style type="text/css">
	.ym{ margin:10px auto; width:50%; overflow:hidden; height:30px}
	.ym span {float:left}
	.ym b {width:20px; margin:-2px 5px 0 -8px}
	table.tdate{ width:50%}
	.tdate th,.tdate td { text-align:center}
	.tdate tbody td { height:30px; line-height:30px}
	.tdate td a { display:block;width:auto; height:100%}
	.tdate td { padding:0; vertical-align:middle}
	.today {padding:0; background-color:#FFF1EE}
</style>
<body>
<div class="KDStyle" id="KDMain">

	<div class="ym">
	    <form action="/index.php/Calendar/info"  method="post" name="form1"> 
		<span><label>部门</label></span>
		<span>
			   <select name="DEPT_ID" onChange="ifrMain.location='/index.php/Calendar/infoblank'">
			   <?php echo (my_dept_tree(0,$DEPT_ID,1)); ?>
			   </select>
		</span>
		<span><b class="ico ico-prev" title="上一年" onClick="set_year(-1);"></b></span>
		<span>
			    <select name="YEAR" onChange="My_Submit();">
<?php
        for($I=2000;$I<=2050;$I++)
        {
?>
          <option value="<?php echo $I?>" <?php if($I==$YEAR) echo "selected";?>><?php echo $I?></option>
<?php
        }
?>
			    </select>
		</span>
		<span><b class="ico ico-next" title="下一年" onClick="set_year(1);"></b></span>
		<span>年</span>
		<span><b class="ico ico-prev" title="上一月" onClick="set_mon(-1);"></b></span>
		<span>
			     <select name="MONTH" onChange="My_Submit();">
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
		</span>
		<span><b class="ico ico-next" title="下一月" onClick="set_mon(1);"></b></span>
		<span>月</span>
		<span><button onClick="javascript:cur_month('/index.php/Calendar/info/?YEAR=<?php echo $CUR_YEAR?>&MONTH=<?php echo $CUR_MONTH?>');" />本月</button></span>
		<span></span>

		</div>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
  <table class="tdate">
    <thead>
     <tr>
      <th>日</th>
      <th>一</th>
      <th>二</th>
      <th>三</th>
      <th>四</th>
      <th>五</th>
      <th>六</th>
      <th>周次</th>
    </tr>
   </thead>
<?php
$WEEK_COUNT=0;
while ($DAY<$DATE)
{
  if($DAY == $CUR_DAY && $YEAR == $CUR_YEAR && $MONTH == $CUR_MON){
     $DAY_COLOR = " class=\"today\"";
     $tday="今日";
  }else{
     $DAY_COLOR = "";
     $tday="";
  }
  $WEEK=date("w",mktime(0,0,0,$MONTH,$DAY,$YEAR));
  if ($WEEK==0 || $DAY==1)
  {
?>
   <tr>
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
     <td><a  href="javascript:user_list('/index.php/Calendar/infolist/?YEAR=<?php echo $YEAR?>&MONTH=<?php echo $MONTH?>&BEGIN_DAY=<?php echo $DAY?>&END_DAY=<?php echo $DAY?>')"<?php echo $DAY_COLOR?>><?php echo $tday.$DAY?></a></td>
<?php
  if ($WEEK==6)
  {

      $WEEK_COUNT++;

      $BEGIN_DAY=$DAY-6;
      if($BEGIN_DAY<0)
         $BEGIN_DAY=1;
?>
     <td><a href="javascript:user_list('/index.php/Calendar/infolist/?YEAR=<?php echo $YEAR?>&MONTH=<?php echo $MONTH?>&BEGIN_DAY=<?php echo $BEGIN_DAY?>&END_DAY=<?php echo $DAY?>')">第<?php echo $WEEK_COUNT?>周</a></td>
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
     <td>&nbsp;</td>
<?php
  }

  $WEEK_COUNT++;

  $DAY--;
  $BEGIN_DAY=$DAY-$WEEK;
  if($BEGIN_DAY<0)
     $BEGIN_DAY=1;
?>
     <td><a href="javascript:user_list('/index.php/Calendar/infolist/?YEAR=<?php echo $YEAR?>&MONTH=<?php echo $MONTH?>&BEGIN_DAY=<?php echo $BEGIN_DAY?>&END_DAY=<?php echo $DAY?>');">第<?php echo $WEEK_COUNT?>周</a></td>
   </tr>
<?php
}
?>
</table>
	<div>
		<script type="text/javascript">
		    // <![CDATA[
		    iframe('/index.php/Calendar/infoblank', '100%', '100%', 'ifrMain');
		    // ]]>
        </script>		
	</div>		
	</div>


	
</body>
</html>