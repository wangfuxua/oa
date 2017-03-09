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

function my_note(DIA_ID)
{
  my_top+=25;
  my_left+=15;

  window.open("/index.php/Diary/note/?DIA_ID="+DIA_ID,"note_win"+DIA_ID,"height=400,width=300,status=0,toolbar=no,menubar=no,location=no,scrollbars=auto,top="+ my_top +",left="+ my_left +",resizable=no");
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
  window.location=str;
}

function cur_month(str)
{
  location=str;
}

</script>
<script type="text/javascript">
$(function(){
        setDomHeight("main", 26);

		createHeader({

        Active: <?php if(($type)  ==  "week"): ?>3<?php endif; ?><?php if(($type)  ==  "month"): ?>4<?php endif; ?>,
        Toolbar: [
            { Title: "帮助", Url: "http://help.7e73.com", Icon: "", IconCls: "ico ico-help", Cls: "", Target: "_blank", TextHide: false },
			{ Title: "刷新", Url: "javascript:",Action:"location.reload()", Icon: "", IconCls: "ico ico-refresh", Cls: "", Target: "_self", TextHide: false }
        ],
        Items: [
				<?php if($USER_ID != ''): ?>{ Title: "日志列表", Url: "/index.php/Diary/infolist/USER_ID/<?php echo ($USER_ID); ?>", Cls: "", Icon: "", IconCls: "ico ico-view" },
            { Title: "日志查询", Url: "/index.php/Diary/infosearch/USER_ID/<?php echo ($USER_ID); ?>", Cls: "", IconCls: "ico ico-query" },
            { Title: "周报", Url: "/index.php/Diary/statistics/USER_ID/<?php echo ($USER_ID); ?>/type/week", Cls: "", IconCls: "ico ico-list" },
            { Title: "月报", Url: "/index.php/Diary/statistics/USER_ID/<?php echo ($USER_ID); ?>/type/month", Cls: "", IconCls: "ico ico-list2" }<?php endif; ?>
        ]
    });		   
});


    $(window).resize(function() { 
        setDomHeight("main", 26); 
    
    });

</script>
<style type="text/css">
	.ym{ margin:10px auto; width:90%; overflow:hidden; height:30px}
	.ym span {float:left}
	.ym b {width:20px; margin:-2px 5px 0 -8px}
	table.tdate{ width:90%}
	.tdate th,.tdate td { text-align:center}
	.tdate tbody td { height:50px}
</style>
<body>
<div class="KDStyle" id="main">

	    <form action="/index.php/Diary/statistics/USER_ID/<?php echo ($USER_ID); ?>/type/<?php echo ($type); ?>"  method="post" name="form1"> 
		<div class="ym">
			<span><b class="ico ico-prev" title="上一年" onclick="set_year(-1);"></b></span>
			<span>
			<select name="YEAR" onchange="My_Submit();">
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
			<span><b class="ico ico-next2" title="下一年" onclick="set_year(1);"></b></span>
			<span>年</span>
			
			<span><b class="ico ico-prev" title="上一月" onclick="set_mon(-1);"></b></span>
			<span>
			<select name="MONTH" onchange="My_Submit();">
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
			<span><b class="ico ico-next2" title="下一月" onclick="set_mon(1);"></b></span>
			<span>月</span>
			<span>
			<button onclick="javascript:cur_month('/index.php/Diary/statistics/USER_ID/<?php echo ($USER_ID); ?>/type/<?php echo ($type); ?>/?YEAR=<?php echo $CUR_YEAR?>&MONTH=<?php echo $CUR_MONTH?>');" />本月</button>
			</span>
			
			<span></span>
			<span></span>
		</div>
		<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<?php
if ($type=="week") {
?>		
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
  if($DAY == $CUR_DAY && $YEAR == $CUR_YEAR && $MONTH == $CUR_MON)
     $DAY_COLOR = " class=\"dm_schver\"";
  else
     $DAY_COLOR = "";

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
     <td><a  href="#" <?php echo $DAY_COLOR?>><?php echo $DAY?></a></td>
<?php
  if ($WEEK==6)
  {

      $WEEK_COUNT++;

      $BEGIN_DAY=$DAY-6;
      if($BEGIN_DAY<0)
         $BEGIN_DAY=1;
?>
     <td><a href="javascript:user_list('/index.php/Diary/statistics/USER_ID/<?php echo ($USER_ID); ?>/type/<?php echo ($type); ?>/?YEAR=<?php echo $YEAR?>&MONTH=<?php echo $MONTH?>&BEGIN_DAY=<?php echo $BEGIN_DAY?>&END_DAY=<?php echo $DAY?>')">第<?php echo $WEEK_COUNT?>周</a></td>
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
     <td><a href="javascript:user_list('/index.php/Diary/statistics/USER_ID/<?php echo ($USER_ID); ?>/type/<?php echo ($type); ?>/?YEAR=<?php echo $YEAR?>&MONTH=<?php echo $MONTH?>&BEGIN_DAY=<?php echo $BEGIN_DAY?>&END_DAY=<?php echo $DAY?>');">第<?php echo $WEEK_COUNT?>周</a></td>
   </tr>
<?php
}
?>
</table>
<?php
}
?>		
    <table style="width:90%">
			<caption class="nostyle"><?php echo ($desc); ?></caption>
			<colgroup>
				<col width="120"></col>
				<col width=""></col>
			</colgroup>
			<thead>
				<tr>
					<th>日期</th>
					<th>日志内容</th>
				</tr>
			</thead>
			<tbody>
			<?php if(is_array($headlist)): ?><?php $k = 0;?><?php $__LIST__ = $headlist?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
					<td><?php echo ($YEAR); ?>-<?php echo ($MONTH); ?>-<?php echo (is_array($vo)?$vo["DAYI"]:$vo->DAYI); ?>(周<?php echo (is_array($vo)?$vo["WEEK_DESC"]:$vo->WEEK_DESC); ?>)</td>
					<td <?php if($vo[week] == '0'): ?>style="background:#f5f5f5"<?php elseif($vo[week] == '6'): ?>style="background:#f5f5f5"<?php endif; ?> >
					<?php if(is_array($vo[sublist])): ?><?php $subk = 0;?><?php $__LIST__ = $vo[sublist]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$subvo): ?><?php ++$subk;?><?php $mod = (($subk % 2 )==0)?><?php echo ($subk); ?>：<?php echo ($subvo[ADD_DATE]); ?> <?php echo (getDiaryTypeName($subvo[DIA_TYPE])); ?> <a href="javascript:my_note('<?php echo (is_array($subvo)?$subvo["DIA_ID"]:$subvo->DIA_ID); ?>')" title="<?php echo (strip_tags(is_array($subvo)?$subvo["CONTENT"]:$subvo->CONTENT)); ?>"><?php echo (csubstr(strip_tags(is_array($subvo)?$subvo["SUBJECT"]:$subvo->SUBJECT),0,50)); ?> </a><br><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
					</td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2"><?php echo ($page); ?></th>
				</tr>
			</tfoot>
    </tr>
   </table>
   
<?php if($type == 'week'): ?><table style="width:90%">
			<caption class="nostyle"><?php echo ($desc_week); ?></caption>
			<colgroup>
				<col width="120"></col>
				<col width=""></col>
			</colgroup>
			<thead>
				<tr>
					<th>日期</th>
					<th>内容</th>
				</tr>
			</thead>
			<tbody>
			<?php if(is_array($headlistweek)): ?><?php $k = 0;?><?php $__LIST__ = $headlistweek?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
					<td><?php echo ($YEAR); ?>-<?php echo ($MONTH); ?>-<?php echo (is_array($vo)?$vo["DAYI"]:$vo->DAYI); ?>(周<?php echo (is_array($vo)?$vo["WEEK_DESC"]:$vo->WEEK_DESC); ?>)</td>
					<td <?php if($vo[week] == '0'): ?>style="background:#f5f5f5"<?php elseif($vo[week] == '6'): ?>style="background:#f5f5f5"<?php endif; ?> >
					<?php if(is_array($vo[sublist])): ?><?php $subk = 0;?><?php $__LIST__ = $vo[sublist]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$subvo): ?><?php ++$subk;?><?php $mod = (($subk % 2 )==0)?><?php echo ($subk); ?>：<?php echo ($subvo[ADD_DATE]); ?> <?php echo (getDiaryTypeName($subvo[DIA_TYPE])); ?> <a href="javascript:my_note('<?php echo (is_array($subvo)?$subvo["DIA_ID"]:$subvo->DIA_ID); ?>')" title="<?php echo (strip_tags(is_array($subvo)?$subvo["CONTENT"]:$subvo->CONTENT)); ?>"><?php echo (csubstr(strip_tags(is_array($subvo)?$subvo["CONTENT"]:$subvo->CONTENT),0,50)); ?> </a><br><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
					</td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2"><?php echo ($page); ?></th>
				</tr>
			</tfoot>
    </tr>
   </table><?php endif; ?>

<?php if($type == 'month'): ?><table style="width:90%">
			<caption class="nostyle"><?php echo ($desc_month); ?></caption>
			<colgroup>
				<col width="120"></col>
				<col width=""></col>
			</colgroup>
			<thead>
				<tr>
					<th>日期</th>
					<th>内容</th>
				</tr>
			</thead>
			<tbody>
			<?php if(is_array($headlistweek)): ?><?php $k = 0;?><?php $__LIST__ = $headlistweek?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
					<td><?php echo ($YEAR); ?>-<?php echo ($MONTH); ?>-<?php echo (is_array($vo)?$vo["DAYI"]:$vo->DAYI); ?>(周<?php echo (is_array($vo)?$vo["WEEK_DESC"]:$vo->WEEK_DESC); ?>)</td>
					<td <?php if($vo[week] == '0'): ?>style="background:#f5f5f5"<?php elseif($vo[week] == '6'): ?>style="background:#f5f5f5"<?php endif; ?> >
					<?php if(is_array($vo[sublist])): ?><?php $subk = 0;?><?php $__LIST__ = $vo[sublist]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$subvo): ?><?php ++$subk;?><?php $mod = (($subk % 2 )==0)?><?php echo ($subk); ?>：<?php echo ($subvo[ADD_DATE]); ?> <?php echo (getDiaryTypeName($subvo[DIA_TYPE])); ?> <a href="javascript:my_note('<?php echo (is_array($subvo)?$subvo["DIA_ID"]:$subvo->DIA_ID); ?>')" title="<?php echo (strip_tags(is_array($subvo)?$subvo["CONTENT"]:$subvo->CONTENT)); ?>"><?php echo (csubstr(strip_tags(is_array($subvo)?$subvo["CONTENT"]:$subvo->CONTENT),0,50)); ?> </a><br><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
					</td>
				</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2"><?php echo ($page); ?></th>
				</tr>
			</tfoot>
    </tr>
   </table><?php endif; ?>
      
</div>

</body>
</html>