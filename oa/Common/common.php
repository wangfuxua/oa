<?
//include("email.php");
function msg($TITLE,$CONTENT)
{
if(strlen($CONTENT)<=12)
$wh="120";
elseif(strlen($CONTENT)<=20)
$wh="250";
else
$wh="300";
	
$str='<div align="center" title="提示信息框">';

$str.='<span>';
if($TITLE!="")
  $str.='<font color="#FF0000"><img src="../Public/images/attention.gif" height="20"> <b><?=$TITLE?></b></font><hr>';
  
$str.=$CONTENT;
$str.='</span></div>';
return $str;

}
function bitsize($num){
 if(!preg_match("/^[0-9]+$/", $num)) return 0;
 $type = array( "B", "KB", "MB", "GB", "TB", "PB" );
 
 $j = 0;
 while( $num >= 1024 ) {
  if( $j >= 5 ) return $num.$type[$j];
  $num = $num / 1024;
  $j++;
 }
 $num=round($num,1);
 return $num.$type[$j];
}

function print_rr($arr){
	
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}
function chinese_date($year,$month,$day)
{
  $everymonth=array(
  0=>array(8,0,0,0,0,0,0,0,0,0,0,0,29,30,7,1),
  1=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0,8,2),
  2=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0,9,3),
  3=>array(5,29,30,29,30,29,29,30,29,29,30,30,29,30,10,4),
  4=>array(0,30,30,29,30,29,29,30,29,29,30,30,29,0,1,5),
  5=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0,2,6),
  6=>array(4,29,30,30,29,30,29,30,29,30,29,30,29,30,3,7),
  7=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,4,8),
  8=>array(0,30,29,29,30,30,29,30,29,30,30,29,30,0,5,9),
  9=>array(2,29,30,29,29,30,29,30,29,30,30,30,29,30,6,10),
  10=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0,7,11),
  11=>array(6,30,29,30,29,29,30,29,29,30,30,29,30,30,8,12),
  12=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0,9,1),
  13=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0,10,2),
  14=>array(5,30,30,29,30,29,30,29,30,29,30,29,29,30,1,3),
  15=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,2,4),
  16=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,3,5),
  17=>array(2,30,29,29,30,29,30,30,29,30,30,29,30,29,4,6),
  18=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0,5,7),
  19=>array(7,29,30,29,29,30,29,29,30,30,29,30,30,30,6,8),
  20=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0,7,9),
  21=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0,8,10),
  22=>array(5,30,29,30,30,29,29,30,29,29,30,29,30,30,9,11),
  23=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0,10,12),
  24=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0,1,1),
  25=>array(4,30,29,30,29,30,30,29,30,30,29,30,29,30,2,2),
  26=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0,3,3),
  27=>array(0,30,29,29,30,29,30,29,30,29,30,30,30,0,4,4),
  28=>array(2,29,30,29,29,30,29,29,30,29,30,30,30,30,5,5),
  29=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0,6,6),
  30=>array(6,29,30,30,29,29,30,29,29,30,29,30,30,29,7,7),
  31=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,8,8),
  32=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0,9,9),
  33=>array(5,29,30,30,29,30,30,29,30,29,30,29,29,30,10,10),
  34=>array(0,29,30,29,30,30,29,30,29,30,30,29,30,0,1,11),
  35=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0,2,12),
  36=>array(3,30,29,29,30,29,29,30,30,29,30,30,30,29,3,1),
  37=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0,4,2),
  38=>array(7,30,30,29,29,30,29,29,30,29,30,30,29,30,5,3),
  39=>array(0,30,30,29,29,30,29,29,30,29,30,29,30,0,6,4),
  40=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,7,5),
  41=>array(6,30,30,29,30,30,29,30,29,29,30,29,30,29,8,6),
  42=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,9,7),
  43=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,10,8),
  44=>array(4,30,29,30,29,30,29,30,29,30,30,29,30,30,1,9),
  45=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0,2,10),
  46=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0,3,11),
  47=>array(2,30,30,29,29,30,29,29,30,29,30,29,30,30,4,12),
  48=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0,5,1),
  49=>array(7,30,29,30,30,29,30,29,29,30,29,30,29,30,6,2),
  50=>array(0,29,30,30,29,30,30,29,29,30,29,30,29,0,7,3),
  51=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,8,4),
  52=>array(5,29,30,29,30,29,30,29,30,30,29,30,29,30,9,5),
  53=>array(0,29,30,29,29,30,30,29,30,30,29,30,29,0,10,6),
  54=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0,1,7),
  55=>array(3,29,30,29,30,29,29,30,29,30,29,30,30,30,2,8),
  56=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0,3,9),
  57=>array(8,30,29,30,29,30,29,29,30,29,30,29,30,29,4,10),
  58=>array(0,30,30,30,29,30,29,29,30,29,30,29,30,0,5,11),
  59=>array(0,29,30,30,29,30,29,30,29,30,29,30,29,0,6,12),
  60=>array(6,30,29,30,29,30,30,29,30,29,30,29,30,29,7,1),
  61=>array(0,30,29,30,29,30,29,30,30,29,30,29,30,0,8,2),
  62=>array(0,29,30,29,29,30,29,30,30,29,30,30,29,0,9,3),
  63=>array(4,30,29,30,29,29,30,29,30,29,30,30,30,29,10,4),
  64=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0,1,5),
  65=>array(0,29,30,29,30,29,29,30,29,29,30,30,29,0,2,6),
  66=>array(3,30,30,30,29,30,29,29,30,29,29,30,30,29,3,7),
  67=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0,4,8),
  68=>array(7,29,30,29,30,30,29,30,29,30,29,30,29,30,5,9),
  69=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,6,10),
  70=>array(0,30,29,29,30,29,30,30,29,30,30,29,30,0,7,11),
  71=>array(5,29,30,29,29,30,29,30,29,30,30,30,29,30,8,12),
  72=>array(0,29,30,29,29,30,29,30,29,30,30,29,30,0,9,1),
  73=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0,10,2),
  74=>array(4,30,30,29,30,29,29,30,29,29,30,30,29,30,1,3),
  75=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0,2,4),
  76=>array(8,30,30,29,30,29,30,29,30,29,29,30,29,30,3,5),
  77=>array(0,30,29,30,30,29,30,29,30,29,30,29,29,0,4,6),
  78=>array(0,30,29,30,30,29,30,30,29,30,29,30,29,0,5,7),
  79=>array(6,30,29,29,30,29,30,30,29,30,30,29,30,29,6,8),
  80=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0,7,9),
  81=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0,8,10),
  82=>array(4,30,29,30,29,29,30,29,29,30,29,30,30,30,9,11),
  83=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0,10,12),
  84=>array(10,30,29,30,30,29,29,30,29,29,30,29,30,30,1,1),
  85=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0,2,2),
  86=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0,3,3),
  87=>array(6,30,29,30,29,30,30,29,30,30,29,30,29,29,4,4),
  88=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0,5,5),
  89=>array(0,30,29,29,30,29,29,30,30,29,30,30,30,0,6,6),
  90=>array(5,29,30,29,29,30,29,29,30,29,30,30,30,30,7,7),
  91=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0,8,8),
  92=>array(0,29,30,30,29,29,30,29,29,30,29,30,30,0,9,9),
  93=>array(3,29,30,30,29,30,29,30,29,29,30,29,30,29,10,10),
  94=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0,1,11),
  95=>array(8,29,30,30,29,30,29,30,30,29,29,30,29,30,2,12),
  96=>array(0,29,30,29,30,30,29,30,29,30,30,29,29,0,3,1),
  97=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0,4,2),
  98=>array(5,30,29,29,30,29,29,30,30,29,30,30,29,30,5,3),
  99=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0,6,4),
  100=>array(0,30,30,29,29,30,29,29,30,29,30,30,29,0,7,5),
  101=>array(4,30,30,29,30,29,30,29,29,30,29,30,29,30,8,6),
  102=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,9,7),
  103=>array(0,30,30,29,30,30,29,30,29,29,30,29,30,0,10,8),
  104=>array(2,29,30,29,30,30,29,30,29,30,29,30,29,30,1,9),
  105=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,2,10),
  106=>array(7,30,29,30,29,30,29,30,29,30,30,29,30,30,3,11),
  107=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0,4,12),
  108=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0,5,1),
  109=>array(5,30,30,29,29,30,29,29,30,29,30,29,30,30,6,2),
  110=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0,7,3),
  111=>array(0,30,29,30,30,29,30,29,29,30,29,30,29,0,8,4),
  112=>array(4,30,29,30,30,29,30,29,30,29,30,29,30,29,9,5),
  113=>array(0,30,29,30,29,30,30,29,30,29,30,29,30,0,10,6),
  114=>array(9,29,30,29,30,29,30,29,30,30,29,30,29,30,1,7),
  115=>array(0,29,30,29,29,30,29,30,30,30,29,30,29,0,2,8),
  116=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0,3,9),
  117=>array(6,29,30,29,30,29,29,30,29,30,29,30,30,30,4,10),
  118=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0,5,11),
  119=>array(0,30,29,30,29,30,29,29,30,29,29,30,30,0,6,12),
  120=>array(4,29,30,30,30,29,30,29,29,30,29,30,29,30,7,1)
  );
  ##############################
  #农历天干
  $mten=array("null","甲","乙","丙","丁","戊","己","庚","辛","壬","癸");
  #农历地支
  $mtwelve=array("null","子（鼠）","丑（牛）","寅（虎）","卯（兔）","辰（龙）",
                 "巳（蛇）","午（马）","未（羊）","申（猴）","酉（鸡）","戌（狗）","亥（猪）");
  #农历月份
  $mmonth=array("闰","正","二","三","四","五","六",
                "七","八","九","十","十一","十二","月");
  #农历日
  $mday=array("null","初一","初二","初三","初四","初五","初六","初七","初八","初九","初十",
              "十一","十二","十三","十四","十五","十六","十七","十八","十九","二十",
              "廿一","廿二","廿三","廿四","廿五","廿六","廿七","廿八","廿九","三十");
  ##############################
  #赋给初值
  #天干地支
  $ten=0;
  $twelve=0;
  #星期
  $week=5;
  #农历日
  $md=0;
  #农历月
  $mm=0;
  #阳历总天数 至1900年12月21日
  $total=11;
  #阴历总天数
  $mtotal=0;

  ##############################
  #计算到所求日期阳历的总天数-自1900年12月21日始
  #先算年的和
  for ($y=1901;$y<$year;$y++)
  {
        $total+=365;
        if ($y%4==0) $total ++;
  }
  #再加当年的几个月
  switch ($month)
  {
           case 12:
                $total+=30;
           case 11:
                $total+=31;
           case 10:
                $total+=30;
           case 9:
                $total+=31;
           case 8:
                $total+=31;
           case 7:
                $total+=30;
           case 6:
                $total+=31;
           case 5:
                $total+=30;
           case 4:
                $total+=31;
           case 3:
                $total+=28;
           case 2:
                $total+=31;
  }

  #如果当年是闰年还要加一天
  if ($year%4==0 and $month>2)
       $total++;

  ##############################
  #用农历的天数累加来判断是否超过阳历的天数
  $flag1=0;#判断跳出循环的条件
  $j=0;
  while ($j<=120)
  {
        $i=1;
        while ($i<=13)
        {
              $mtotal+=$everymonth[$j][$i];
              if ($mtotal>=$total)
              {
                   $flag1=1;
                   break;
              }
              $i++;
        }
        if ($flag1==1) break;
        $j++;
  }

  ##############################
  #计算所求月份1号的农历日期
  $md=$everymonth[$j][$i]-($mtotal-$total);

  #是否跨越一年
  switch ($month)
  {
           case 1:
           case 3:
           case 5:
           case 7:
           case 8:
           case 10:
           case 12:
                $dd=31;
                break;
           case 4:
           case 6:
           case 9:
           case 11:
                $dd=30;
                break;
           case 2:
                if ($year%4==0)
                {
                    $dd=29;
                }
                else
                {
                    $dd=28;
                }
                break;
  }

  #根据1号的情况，计算指定日的农历日
  $day_i=1;
  while ($day_i<$day)
  {
     $day_i++;
     $md++;
     if ($md>$everymonth[$j][$i])
     {
          $md=1;
          $i++;
     }
     if (($i>12 and $everymonth[$j][0]==0) or ($i>13 and $everymonth[$j][0]<>0))
     {
           $i=1;
           $j++;
     }
  }

  #计算农历月
  if ($everymonth[$j][0]<>0 and $everymonth[$j][0]<$i)
      $mm=$i-1;
  else
      $mm=$i;

  if ($i==$everymonth[$j][0]+1 and $everymonth[$j][0]<>0)
      $chi=$mmonth[0];#闰

  $chi.=$mmonth[$mm].$mmonth[13].$mday[$md];
  return $chi;
}


function format_date($STRING1) //-- 返回形如 1999年2月1日
{
  $STRING1=str_replace("-0","-",$STRING1);
  $STR=strtok($STRING1,"-");
  $STRING2=$STR."年";
  $STR=strtok("-");
  $STRING2.=$STR."月";
  $STR=strtok(" ");
  $STRING2.=$STR."日";

  return $STRING2;
}

function get_week($STRING1) //-- 返回形如 六
{
  $STR=strtok($STRING1,"-");
  $YEAR=$STR;

  $STR=strtok("-");
  $MONTH=$STR;

  $STR=strtok(" ");
  $DAY=$STR;

  $TIME1=mktime(0,0,0,$MONTH,$DAY,$YEAR);

  switch(date("w", $TIME1))
  {
    case 0:
  	return "日";
    case 1:
  	return "一";
    case 2:
  	return "二";
    case 3:
  	return "三";
    case 4:
  	return "四";
    case 5:
  	return "五";
    case 6:
  	return "六";
  }
}

function format_money($STR)
{

 if($STR=="")
   return "";

 if($STR==".00")
   return "0.00";

 $TOK=strtok($STR,".");

 if(strcmp($STR,$TOK)=="0")
   $STR.=".00";

 else
 {
  $TOK=strtok(".");

  for($I=1;$I<=2-strlen($TOK);$I++)
    $STR.="0";
 }

 if(substr($STR,0,1)==".")
    $STR="0".$STR;

 return $STR;
}

function compare_date($DATE1,$DATE2) //-- DATE1=DATE2 返回0,DATE1>DATE2 返回1,DATE1<DATE2 返回-1
{
  $STR=strtok($DATE1,"-");
  $YEAR1=$STR;
  $STR=strtok("-");
  $MON1=$STR;
  $STR=strtok("-");
  $DAY1=$STR;

  $STR=strtok($DATE2,"-");
  $YEAR2=$STR;
  $STR=strtok("-");
  $MON2=$STR;
  $STR=strtok("-");
  $DAY2=$STR;

  if($YEAR1>$YEAR2)
     return 1;
  else if($YEAR1<$YEAR2)
     return -1;
  else
  {
    if($MON1>$MON2)
       return 1;
    else if($MON1<$MON2)
       return -1;
    else
    {
      if($DAY1>$DAY2)
         return 1;
      else if($DAY1<$DAY2)
         return -1;
      else
         return 0;
    }
  }
}

function compare_time($TIME1,$TIME2) //-- time1=time2 返回0,time1>time2 返回1,time1<time2 返回-1
{
  $STR=strtok($TIME1,":");
  $HOUR1=$STR;
  $STR=strtok(":");
  $MIN1=$STR;
  $STR=strtok(":");
  $SEC1=$STR;

  $STR=strtok($TIME2,":");
  $HOUR2=$STR;
  $STR=strtok(":");
  $MIN2=$STR;
  $STR=strtok(":");
  $SEC2=$STR;

  if($HOUR1>$HOUR2)
     return 1;
  else if($HOUR1<$HOUR2)
     return -1;
  else
  {
    if($MIN1>$MIN2)
       return 1;
    else if($MIN1<$MIN2)
       return -1;
    else
    {
      if($SEC1>$SEC2)
         return 1;
      else if($SEC1<$SEC2)
         return -1;
      else
         return 0;
    }
  }
}

function compare_date_time($DATE_TIME1,$DATE_TIME2)
{
    if($DATE_TIME1==null||strlen($DATE_TIME1)==0 || $DATE_TIME2==null||strlen($DATE_TIME2)==0)
       return -1;
    $DATE_TIME1_ARRY=explode(" ",$DATE_TIME1);
    $DATE_TIME2_ARRY=explode(" ",$DATE_TIME2);
    if(compare_date($DATE_TIME1_ARRY[0],$DATE_TIME2_ARRY[0])==1)
       return 1;
    elseif(compare_date($DATE_TIME1_ARRY[0],$DATE_TIME2_ARRY[0])==0)
    {
        if(compare_time($DATE_TIME1_ARRY[1],$DATE_TIME2_ARRY[1])==1)
           return 1;
        elseif(compare_time($DATE_TIME1_ARRY[1],$DATE_TIME2_ARRY[1])==0)
           return 0;
        else
           return -1;
    }
    else
       return -1;
}

/*
function Button_Back()
{
	echo '<br><center><input type="button" class="BigButton" value="返回" onclick="history.back();"></center>';
}

function upload($ATTACHMENT,$ATTACHMENT_NAME)
{
  if(!file_exists($ATTACHMENT))
  {
    echo msg("附件上传失败","原因：附件文件为空或文件名太长，或附件大于100兆字节，或文件路径不存在！");
    echo Button_Back();
    
    exit;
  }

  //-- 生成随机数 --
  mt_srand((double)microCUR_TIME_INT*1000000);
  $ATTACHMENT_ID = mt_rand();

  //-- 将上载文件转存 --
  global $ATTACH_PATH;
  $ATTACHMENT_NAME=str_replace("\'","’",$ATTACHMENT_NAME);
  $PATH=$ATTACH_PATH.$ATTACHMENT_ID;

  if(!file_exists($PATH))
      mkdir($PATH, 0700);

  $FILENAME=$PATH."/".$ATTACHMENT_NAME;

  copy($ATTACHMENT,$FILENAME);
  unlink($ATTACHMENT);

  if(!file_exists($FILENAME))
  {
    echo msg("附件上传失败","原因：附件文件为空或文件名太长，或附件大于30兆字节，或文件路径不存在！");
    echo Button_Back();
    exit;
  }
  return $ATTACHMENT_ID;
}
*/
function delete_attach($ATTACHMENT_ID,$ATTACHMENT_NAME)
{
   global $ATTACH_PATH;
   $PATH=$ATTACH_PATH.$ATTACHMENT_ID;

   $FILENAME=$PATH."/".$ATTACHMENT_NAME;
   unlink($FILENAME);
   rmdir($PATH);
}

function attach_size($ATTACHMENT_ID,$ATTACHMENT_NAME)
{
   $ATTACH_SIZE=0;

   global $ATTACH_PATH;
   $PATH=$ATTACH_PATH.$ATTACHMENT_ID;
   $FILENAME="./Uploads/".$ATTACHMENT_NAME;
   if(file_exists($FILENAME))
      $ATTACH_SIZE=filesize($FILENAME);

   return $ATTACH_SIZE;
}


//检查并创建多级目录
function checkDir($path){
	$pathArray = explode('/',$path);
	$nowPath = '';
	array_pop($pathArray);
	foreach ($pathArray as $key=>$value){
		if ( ''==$value ){
			unset($pathArray[$key]);
		}else{
			if ( $key == 0 )
				$nowPath .= $value;
			else
				$nowPath .= '/'.$value;
			if ( !is_dir($nowPath) ){
				if ( !mkdir($nowPath, 0777) ) return false;
			}
		}
	}
	return true;
}

//以下是针对中文的定位折行.(本论坛使用的是这个函数.)
//#######################################################################################
//# 判断某个位置是中文字符的左还是右半部分，或不是中文
///# 返回值 -1 左 0 不是中文字符 1 右
//# 用法
/*
$a = 'this is 中文';
print is_chinese($a, 1); // 0
print is_chinese($a,8); // -1
print is_chinese($a,9); // 1
*/
function is_chinese(&$str, $location) {
$ch = true;
$i = $location;
while(ord($str[$i])>0xa0 && $i >= 0) {
$ch = !$ch;
$i --;
}

if($i != $location) {
$f_str = $ch ? 1: -1;
}
else {
$f_str = false;
}

return $f_str;
}

/* 中文字符串截取函数
一些中文字符串截取函数经常有一些问题，例如在一些自动换行程序中
$a=“1中2”；
经两次截取后，
csubstr($str,$a,0,2);
csubstr($str, $a, 2,2)
由于载取位置指向“中”的右字节，可能会是这样的结果
1, 2
用本函数会产生正确的结果
1中, 2
*/
# start 开始位置，从0开始
# long = 0 则从start 一直取到字符串尾
# ltor = true 时从左到右取字符，false 时到右到左取字符
# $cn_len 中文字符按字节取还是字数取，如果按字数取，则一个中文当一个字节计算
function csubstr__old(&$str, $start=0, $long=0, $ltor=true, $cn_len=2) {
if($long == 0) $long = strlen($str);
if($ltor == false) $str = cstrrev($str);

if($cn_len == 1) {

for($i=0, $fs=0; $i<$start; $fs++)
$i += (ord($str[$fs]) <= 0xa0) ? 1 : 0.5;
for($i=0, $fe=$fs; $i<$long; $fe++)
$i += (ord($str[$fe]) <= 0xa0) ? 1 : 0.5;
$long = $fe - $fs;

}
else {

$fs = (is_chinese($str, $start) == 1) ? $start - 1 : $start;
$fe = $long + $start - 1;
$end = ( is_chinese($str, $fe) == -1 ) ? $fe -1 : $fe;
$long = $end - $fs + 1;
}

$f_str = substr($str, $fs, $long);
if($ltor == false) $f_str = cstrrev($f_str);

return $f_str;
}



/*-----------字符串截取----------*/

function csubstr($string, $start=0 ,$length=0, $dot = ' ...') {
	//global $charset;
    $charset='utf-8';
	if(strlen($string) <= $length) {
		return $string;
	}

	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

	$strcut = '';
	if(strtolower($charset) == 'utf-8') {

		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	return $strcut.$dot;
}

/*
//------------- 检查IP是否合法 -------------------------------
function is_ip($IP)
{
    $IP_ARRAY=explode(".",$IP);
    $IP_ARRAY_NUM=sizeof($IP_ARRAY);
    if($IP_ARRAY_NUM!=4)
       return false;
    for($I=0;$I<$IP_ARRAY_NUM;$I++)
    {
       if(!is_numeric($IP_ARRAY[$I]) || $IP_ARRAY[$I]<0||$IP_ARRAY[$I]>255)
          return false;
       if($I==3 && $IP_ARRAY[$I]==255)
          return false;
     }
     return true;
}

function check_ip($USER_IP,$TYPE)
{
$connection=OpenConnection();
$query="select * from IP_RULE where TYPE='$TYPE'";
$cursor= exequery($connection,$query);
$RULE_COUNT=0;
$FLAG=0;
while($ROW=mysql_fetch_array($cursor))
 {
    $RULE_COUNT++;
    $BEGIN_IP=$ROW["BEGIN_IP"];
    $END_IP=$ROW["END_IP"];

    if(ip2long($USER_IP)>=ip2long($BEGIN_IP) && ip2long($USER_IP)<=ip2long($END_IP))
    {
       $FLAG=1;
       break;
    }
 }
if($RULE_COUNT==0 || $FLAG==1)
    return true;
else
    return false;
}
*/
function maskstr($STR,$FIRST,$LAST)
{
   if(!is_numeric($FIRST) || !is_numeric($LAST))
      return;
   if(strlen($STR)<=$FIRST+$LAST)
      return $STR;

   $RETURN_STR=substr($STR,0,$FIRST);
   for($I=0;$I< strlen(substr($STR,$FIRST,-$LAST));$I++)
      $RETURN_STR.="*";
   $RETURN_STR.=substr($STR,-$LAST);

   return $RETURN_STR;
}
/*
function add_log($TYPE,$REMARK,$OPERATOR)
{
   $connection=OpenConnection();
   $CUR_TIME=date("Y-m-d H:i:s",CUR_TIME_INT);
   $USER_IP=$_SERVER["REMOTE_ADDR"];

   if($TYPE==3 || $TYPE==4 || $TYPE==5)
   {
   	  $query = "SELECT * from DEPARTMENT where DEPT_ID=$REMARK";
      $cursor = exequery($connection,$query);
      if($ROW=mysql_fetch_array($cursor))
      {
         $DEPT_ID=$ROW["DEPT_ID"];
         $DEPT_NAME=$ROW["DEPT_NAME"];
      }
      $REMARK="$DEPT_NAME,DEPT_ID=$DEPT_ID";
   }
   elseif($TYPE==6 || $TYPE==7 || $TYPE==8 || $TYPE==11)
   {
      $query = "SELECT * from USER where USER_ID='$REMARK'";
      $cursor = exequery($connection,$query);
      if($ROW=mysql_fetch_array($cursor))
      {
          $USER_ID=$ROW["USER_ID"];
          $USER_NAME=$ROW["USER_NAME"];
          $DEPT_ID=$ROW["DEPT_ID"];
      }

      $query = "SELECT * from DEPARTMENT where DEPT_ID=$DEPT_ID";
      $cursor = exequery($connection,$query);
      if($ROW=mysql_fetch_array($cursor))
          $DEPT_NAME=$ROW["DEPT_NAME"];

      $REMARK="[$DEPT_NAME]$USER_NAME,USER_ID=$USER_ID";
   }

   $query="insert into SYS_LOG (USER_ID,TIME,IP,TYPE,REMARK) values ('$OPERATOR','$CUR_TIME','$USER_IP','$TYPE','$REMARK')";
   exequery($connection,$query);
   return $query;
}
*/
/*
function check_cfg()
{
   $connection=OpenConnection();
   $query="select * from CP_ASSETCFG";
   $cursor = exequery($connection,$query);
   if($ROW=mysql_fetch_array($cursor))
   {
   	   $DPCT_SORT=$ROW["DPCT_SORT"];
   	   $BAL_SORT=$ROW["BAL_SORT"];
   }
   if($DPCT_SORT!="M"&&$DPCT_SORT!="S"&&$DPCT_SORT!="Y" || $BAL_SORT!="01"&&$BAL_SORT!="02")
      return false;
   else
      return true;
}
*/
/**
 * 递归求解上级部门ID
 * */

function getParentDepts($DEPT_ID,$pre=""){
		  $dao =D("Department");
		  $map="DEPT_ID=$DEPT_ID";		
		  $row=$dao->where($map)->find();
		  if ($row[DEPT_ID]) {
		     $DEPT_ID.=",".getParentDepts($row[DEPT_PARENT],$pre);
		     return $pre.$DEPT_ID;
		  }
	}
	
/*-------------递归求解得到下级部门---------*/

function getSubDepts($DEPT_ID){
  $dao =D("Department");
  $map="DEPT_PARENT=$DEPT_ID";
  $list=$dao->where($map)->field("DEPT_ID")->findall();
  	  foreach ($list as $row){
	  	$subdept.=$row[DEPT_ID].",";
 	    $subdept.=getSubDepts($row[DEPT_ID]);	
	  }	
	return $subdept;
}

//-- 递归求解PARENT_ID是否是DEPT_ID的父节点 --
function is_dept_parent($DEPT_ID,$PARENT_ID)
{
  $dao =D("Department");
  $map="DEPT_ID='$DEPT_ID'";
  $ROW=$dao->where($map)->find();
  if($ROW)
  {
     $DEPT_PARENT=$ROW["DEPT_PARENT"];
     if($DEPT_PARENT==0)
        return 0;
     else if($DEPT_PARENT==$PARENT_ID)
        return 1;
     else
        return is_dept_parent($DEPT_PARENT,$PARENT_ID);
  }
  
}

//-- 查看$DEPT_ID是否属于本人管理范围 --
function is_dept_priv($DEPT_ID)
{
  //global $LOGIN_USER_ID,$LOGIN_DEPT_ID,$_SESSION;
  $LOGIN_USER_ID = Session::get('LOGIN_USER_ID');
  $LOGIN_DEPT_ID = Session::get("LOGIN_DEPT_ID");
  $dao =D("User");
  $map="USER_ID='$LOGIN_USER_ID'";
  $ROW=$dao->where($map)->find();
  $POST_PRIV=$ROW["POST_PRIV"];
  $POST_DEPT=$ROW["POST_DEPT"];

  if($POST_PRIV==0 && $DEPT_ID!=$LOGIN_DEPT_ID && !is_dept_parent($DEPT_ID,$LOGIN_DEPT_ID)){
     $DEPT_PRIV=0;
  }elseif($POST_PRIV==2){
     $DEPT_PRIV=0;
     $MY_ARRAY=explode(",",$POST_DEPT);
     $ARRAY_COUNT=sizeof($MY_ARRAY);
     foreach ($MY_ARRAY as $key=>$value){
     	 if ($value) {
     	 	if(is_dept_parent($DEPT_ID,$value) || $value==$DEPT_ID){
	           $DEPT_PRIV=1;
	           break;
	        }
     	 }
     }
  }else
     $DEPT_PRIV=1;

  return $DEPT_PRIV;
}
/*
function is_dept_priv2($DEPT_ID)
{
  //global $LOGIN_USER_ID,$LOGIN_DEPT_ID,$_SESSION;
  $LOGIN_USER_ID = Session::get('LOGIN_USER_ID');
  $LOGIN_DEPT_ID = Session::get("LOGIN_DEPT_ID");
  $dao =D("User");
  $map="USER_ID='$LOGIN_USER_ID'";
  $ROW=$dao->where($map)->find();
  $POST_PRIV=$ROW["POST_PRIV"];
  $POST_DEPT=$ROW["POST_DEPT"];

  if($POST_PRIV==0){//本部门
  	   $DEPT_PRIV=0;
  	 if (is_dept_parent($LOGIN_DEPT_ID,$DEPT_ID)) {
  	   $DEPT_PRIV=1;
  	  } 
     if ($DEPT_ID==$LOGIN_DEPT_ID) {
     	$DEPT_PRIV=1;
     }
  }elseif($POST_PRIV==2){//指定部门
     $DEPT_PRIV=0;
     $MY_ARRAY=explode(",",$POST_DEPT);
     $ARRAY_COUNT=sizeof($MY_ARRAY);
     foreach ($MY_ARRAY as $key=>$value){
     	 if ($value) {
     	 	if(is_dept_parent($DEPT_ID,$value) || $value==$DEPT_ID){
	           $DEPT_PRIV=1;
	           break;
	        }
     	 }
     }
  }else{//全体
     $DEPT_PRIV=1;
  }
  return $DEPT_PRIV;
}
*/
function my_user_list($name,$value,$CHOOSE_ID='',$limit = ''){
	$OPTION_TEXT="";
	$dao=D("User");
	$list=$dao->where($limit)->field("$name as `opname`,$value as `opvalue`")->findall();
    if ($list) {
    	foreach ($list as $ROW){
		$selected = "";
		if($CHOOSE_ID == $ROW["opvalue"])$selected = "selected";
		$OPTION_TEXT .= "<option value=\"".$ROW["opvalue"]."\" $selected>".$ROW["opname"]."</option>\n";    		
    		
    	}
    }
    //echo $dao->getlastsql();
	return $OPTION_TEXT;
}

//------ 多级部门下拉菜单，支持按管理范围列出 --------
function my_dept_tree($DEPT_ID,$DEPT_CHOOSE,$POST_OP)
{
  global $DEEP_COUNT;
  if($DEEP_COUNT=="")
     $DEEP_COUNT="|";
     
  $dao=D("Department");
  $list=$dao->where("DEPT_PARENT=$DEPT_ID")
            ->order('DEPT_NO')
            ->findall();
  $OPTION_TEXT="";
  $DEEP_COUNT1=$DEEP_COUNT;
  $DEEP_COUNT.="　|";
            
  if ($list) {
     foreach ($list as $ROW){
      $COUNT++;
      $DEPT_ID=$ROW["DEPT_ID"];
      $DEPT_NAME=$ROW["DEPT_NAME"];
      $DEPT_PARENT=$ROW["DEPT_PARENT"];

      $DEPT_NAME=str_replace("<","&lt",$DEPT_NAME);
      $DEPT_NAME=str_replace(">","&gt",$DEPT_NAME);
      $DEPT_NAME=stripslashes($DEPT_NAME);

      if($POST_OP==1)
         $DEPT_PRIV=is_dept_priv($DEPT_ID);
      else
         $DEPT_PRIV=1;

      $OPTION_TEXT_CHILD=my_dept_tree($DEPT_ID,$DEPT_CHOOSE,$POST_OP);

      if($DEPT_PRIV==1)
      {
        $OPTION_TEXT.="<option ";
        if($DEPT_ID==$DEPT_CHOOSE)
           $OPTION_TEXT.="selected ";
        $OPTION_TEXT.="value=$DEPT_ID>".$DEEP_COUNT1."─".$DEPT_NAME."</option>\n";
       }

       if($OPTION_TEXT_CHILD!="")
          $OPTION_TEXT.=$OPTION_TEXT_CHILD;
     }           	
  }           
  $DEEP_COUNT=$DEEP_COUNT1;
  
  return $OPTION_TEXT;
}

//-- 递归求解完整的多级部门名称 --
/*
function dept_long_name($DEPT_ID)
{
  $connection=OpenConnection();
  $query = "SELECT * from DEPARTMENT where DEPT_ID=$DEPT_ID";
  $cursor= exequery($connection,$query);
  if($ROW=mysql_fetch_array($cursor))
  {
     $DEPT_NAME=$ROW["DEPT_NAME"];
     $DEPT_PARENT=$ROW["DEPT_PARENT"];

     if($DEPT_PARENT==0)
        return $DEPT_NAME;
     else
        return dept_long_name($DEPT_PARENT)."/".$DEPT_NAME;
  }
}
*/
function image_mimetype($fichier)
{
	if(eregi("\.mid$",$fichier)){$image="mid.gif";}
	else if(eregi("\.txt$",$fichier)){$image="txt.gif";}
	else if(eregi("\.sql$",$fichier)){$image="txt.gif";}
	else if(eregi("\.js$",$fichier)){$image="js.gif";}
	else if(eregi("\.gif$",$fichier)){$image="gif.gif";}
	else if(eregi("\.jpg$",$fichier)){$image="jpg.gif";}
	else if(eregi("\.html$",$fichier)){$image="html.gif";}
	else if(eregi("\.htm$",$fichier)){$image="html.gif";}
	else if(eregi("\.rar$",$fichier)){$image="rar.gif";}
	else if(eregi("\.gz$",$fichier)){$image="zip.gif";}
	else if(eregi("\.tgz$",$fichier)){$image="zip.gif";}
	else if(eregi("\.z$",$fichier)){$image="zip.gif";}
	else if(eregi("\.ra$",$fichier)){$image="ram.gif";}
	else if(eregi("\.ram$",$fichier)){$image="ram.gif";}
	else if(eregi("\.rm$",$fichier)){$image="ram.gif";}
	else if(eregi("\.pl$",$fichier)){$image="pl.gif";}
	else if(eregi("\.zip$",$fichier)){$image="zip.gif";}
	else if(eregi("\.wav$",$fichier)){$image="wav.gif";}
	else if(eregi("\.php$",$fichier)){$image="php.gif";}
	else if(eregi("\.phtml$",$fichier)){$image="php.gif";}
	else if(eregi("\.exe$",$fichier)){$image="exe.gif";}
	else if(eregi("\.bmp$",$fichier)){$image="bmp.gif";}
	else if(eregi("\.png$",$fichier)){$image="gif.gif";}
	else if(eregi("\.css$",$fichier)){$image="css.gif";}
	else if(eregi("\.mp3$",$fichier)){$image="mp3.gif";}
	else if(eregi("\.xls$",$fichier)){$image="xls.gif";}
	else if(eregi("\.doc$",$fichier)){$image="doc.gif";}
	else if(eregi("\.pdf$",$fichier)){$image="pdf.gif";}
	else if(eregi("\.mov$",$fichier)){$image="mov.gif";}
	else if(eregi("\.avi$",$fichier)){$image="avi.gif";}
	else if(eregi("\.mpg$",$fichier)){$image="mpg.gif";}
	else if(eregi("\.mpeg$",$fichier)){$image="mpeg.gif";}
	else if(eregi("\.swf$",$fichier)){$image="flash.gif";}
	else {$image="defaut.gif";}
	return $image;
}

//------ 多级部门下拉菜单，支持按管理范围列出 --------
function my_sort_tree($SORT_ID_CHOOSE,$FILE_SORT,$PARENT_ID,$LOGIN_DEPT_ID,$LOGIN_USER_ID,$SORT)
{
  global $DEEP_COUNT;
  
  if($DEEP_COUNT=="")
     $DEEP_COUNT="|";

  //$connection=OpenConnection();
  if($PARENT_ID==0)
  {
    if($FILE_SORT==1)
        $query = "SELECT * from file_sort where (SORT_TYPE='1' or (SORT_TYPE='2' and DEPT_ID=$LOGIN_DEPT_ID) or (SORT_TYPE='3' and (InStr(USER_ID,',$LOGIN_USER_ID,')>0 or InStr(USER_ID,'$LOGIN_USER_ID,')=1))) and SORT_PARENT=$PARENT_ID order by SORT_NAME";
    else
        $query = "SELECT * from file_sort where SORT_TYPE='4' and USER_ID='$LOGIN_USER_ID' and SORT_PARENT=$PARENT_ID order by SORT_NAME";
  }
  else
      $query = "SELECT * from file_sort where SORT_PARENT=$PARENT_ID order by SORT_NAME";
  $dao= new Model();
  $list=$dao->query($query);
  //print_r($list);
  //echo $dao->getLastSql();
  //$cursor= exequery($connection,$query);
  $OPTION_TEXT="";
  if($FILE_SORT==2&&$PARENT_ID==0)
     $OPTION_TEXT="<option value=0>─ 个人文件柜</option>\n";
  $DEEP_COUNT1=$DEEP_COUNT;
  $DEEP_COUNT.="　|";
  $XML_TEXT="";
  if ($list) {
  	 foreach ($list as $ROW){
	  	  $SORT_ID=$ROW["SORT_ID"];
	      $SORT_NAME=$ROW["SORT_NAME"];
	
	      $SORT_NAME=str_replace("<","&lt",$SORT_NAME);
	      $SORT_NAME=str_replace(">","&gt",$SORT_NAME);
	      $SORT_NAME=stripslashes($SORT_NAME);
	
	      $OPTION_TEXT_CHILD=my_sort_tree($SORT_ID_CHOOSE,$FILE_SORT,$SORT_ID,$LOGIN_DEPT_ID,$LOGIN_USER_ID,$SORT);
	
	      
	      $OPTION_TEXT.="<option value=$SORT_ID";
	      if($SORT_ID==$SORT_ID_CHOOSE)
	         $OPTION_TEXT.=" selected";
	      $OPTION_TEXT.=">".$DEEP_COUNT1."─".$SORT_NAME."</option>\n";
	      
	      if($SORT==1&&$SORT_ID==$SORT_ID_CHOOSE)
	         continue;
	      
	      if($OPTION_TEXT_CHILD!="")
	         $OPTION_TEXT.=$OPTION_TEXT_CHILD;
  	 }
  }
  $DEEP_COUNT=$DEEP_COUNT1;
  return $OPTION_TEXT;
}

function doc2txt($path)
{
  exec ($_SERVER["DOCUMENT_ROOT"]."inc/doc2txt.exe -q -s $path",$OUT_ARRAY);
  $count = count($OUT_ARRAY);
  for ($i = 0; $i < $count; $i++)
    $OUT.=$OUT_ARRAY[$i]."\n";
  return $OUT;
}

function affair_sms()
{
 //global $LOGIN_USER_ID;
 $LOGIN_USER_ID = Session::get('LOGIN_USER_ID');
 
 $CUR_DATE=date("Y-m-d",CUR_TIME_INT);
 $CUR_TIME=date("Y-m-d H:i:s",CUR_TIME_INT);
 
 $dao=D("Affair");
 $map="USER_ID='$LOGIN_USER_ID' and BEGIN_TIME<='$CUR_TIME' and (LAST_REMIND<'$CUR_DATE' or LAST_REMIND='0000-00-00')";
 $list=$dao->where($map)->findall();

 foreach ($list as $ROW)
 {
    $AFF_ID=$ROW["AFF_ID"];
    $USER_ID=$ROW["USER_ID"];
    $TYPE=$ROW["TYPE"];
    $REMIND_DATE=$ROW["REMIND_DATE"];
    $REMIND_TIME=$ROW["REMIND_TIME"];
    $CONTENT=$ROW["CONTENT"];
    
    $SEND_TIME=date("Y-m-d",CUR_TIME_INT)." ".$REMIND_TIME;
    $SMS_CONTENT="日常事务提醒：".csubstr($CONTENT,0,100);
    
    $FLAG=0;
    if($TYPE=="2")
       $FLAG=1;
    elseif($TYPE=="3" && date("w",CUR_TIME_INT)==$REMIND_DATE)
       $FLAG=1;
    elseif($TYPE=="4" && date("j",CUR_TIME_INT)==$REMIND_DATE)
       $FLAG=1;
    elseif($TYPE=="5")
    {
       $REMIND_ARR=explode("-",$REMIND_DATE);
       $REMIND_DATE_MON=$REMIND_ARR[0];
       $REMIND_DATE_DAY=$REMIND_ARR[1];
       if(date("n",CUR_TIME_INT)==$REMIND_DATE_MON && date("j",CUR_TIME_INT)==$REMIND_DATE_DAY)
          $FLAG=1;
    }
    
    if($FLAG==1)
    {  $data=array();
       $dao=D("Sms");	
       $data[FROM_ID]=$LOGIN_USER_ID;
       $data[TO_ID]=$LOGIN_USER_ID;
       $data[SMS_TYPE]=5;
       $data[CONTENT]=$SMS_CONTENT;
       $data[SEND_TIME]=$SEND_TIME;
       $data[REMIND_FLAG]=1;
       $dao->add($data);
       $dao=D("Affair");
       $dao->setField("LAST_REMIND",$CUR_DATE,"AFF_ID='$AFF_ID'");
       
    }
 }
}

function my_select($selectname,$arData,$class='',$selValue = '',$dop='',$extra = ''){
	$html = "<select name=\"$selectname\" class=\"$class\" $extra>\n";
	//echo $selValue;
	if(!empty($dop))$html .= $dop;
	foreach($arData as $val => $text){
		
		$selected = "";
		if($selValue == $val)
		$selected="selected";
		
		$html .= "<option value=\"$val\" $selected>$text</option>\n"; 
	}
	$html .= "</select>";
	
	return $html;
}

####
  //菜单中用到
####
function find_id($STRING,$ID)
{
   $MY_ARRAY=explode(",",$STRING);
   $ARRAY_COUNT=sizeof($MY_ARRAY);
   if($MY_ARRAY[$ARRAY_COUNT-1]=="")$ARRAY_COUNT--;
   for($I=0;$I<$ARRAY_COUNT;$I++)
   { if(strcmp($MY_ARRAY[$I],$ID)==0||$MY_ARRAY[$I]==$ID)return true;}
   return false;
}

function formatdate($sTime,$type = 'normal',$alt = 'false'){
		//sTime=源时间，cTime=当前时间，dTime=时间差
	$cTime		=	CUR_TIME_INT;
	$dTime		=	$cTime - $sTime;
	$dDay		=	intval(date("Ymd",$cTime)) - intval(date("Ymd",$sTime));
	$dYear		=	intval(date("Y",$cTime)) - intval(date("Y",$sTime));
	//normal：n秒前，n分钟前，n小时前，日期
	if($type=='normal'){
		if( $dTime < 60 ){
			echo $dTime."秒前";
		}elseif( $dTime < 3600 ){
			echo intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			echo intval($dTime/3600)."小时前";
		}elseif($dYear==0){
			echo date("m-d ,H:i",$sTime);
		}else{
			echo date("Y-m-d ,H:i",$sTime);
		}
	//full: Y-m-d , H:i:s
	}elseif($type=='full'){
		echo date("Y-m-d",$sTime);
	}
}


/* check */

function is_number($str)
{
if(substr($str,0,1)=="-")
  $str=substr($str,1);
$length=strlen($str);
for($i=0;$i<$length;$i++)
{
 $ascii_value=ord(substr($str,$i,1));
 if(($ascii_value<48 || $ascii_value>57))
   return false;
}

if($str!="0")
{
 	$str=intval($str);
	if($str==0)
    	return false;
}

 return true;
}

function is_money($str)
{
 $dot_pos=strpos($str,".");
 if(!$dot_pos)
    return false;

 $str1=substr($str,0,$dot_pos);
 if(strlen($str1)>14)
    return false;
 if(!is_number($str1))
   return false;

 $str2=substr($str,$dot_pos+1,strlen($str)-$dot_pos);
 if(strlen($str2)!=2)
    return false;
 if(!is_number($str2))
   return false;
 return true;
}


function is_money_len($str,$int_len,$dot_len)
{
 $dot_pos=strpos($str,".");
 if(!$dot_pos)
    return false;

 $str1=substr($str,0,$dot_pos);

 if(strlen($str1)>$int_len)
    return false;

 if(!is_number($str1))
   return false;


 $str2=substr($str,$dot_pos+1,strlen($str)-$dot_pos);
 if(strlen($str2)!=$dot_len)
    return false;
 if(!is_number($str2))
   return false;

 return true;
}

function is_date($str)
{
 $YEAR="";
 $MONTH="";
 $DAY="";

 $len=strlen($str);

 $offset=0;
 $i=strpos($str,"-",$offset);
 $YEAR=substr($str,$offset,$i-$offset);

 $offset=$i+1;
 if($offset>$len)
   return false;

 if($i)
 { $i=strpos($str,"-",$offset);
   $MONTH=substr($str,$offset,$i-$offset);
   $offset=$i+1;

   if($offset>$len)
     return false;

   if($i)
    $DAY=substr($str,$offset,$len-$offset);
 }

 if($YEAR=="" || $MONTH=="" || $DAY=="")
   return false;

 if(!checkdate(intval($MONTH),intval($DAY),intval($YEAR)))
   return false;

 return true;
}

function is_time($str)
{
 $TEMP="";
 $HOUR="";
 $MIN="";
 $SEC="";

 $TEMP=strtok($str,":");
 $HOUR=$TEMP;
 if($HOUR=="" || $HOUR>24 || $HOUR<0 || !is_number($HOUR))
    return false;

 $TEMP=strtok(":");
 $MIN=$TEMP;
 if($MIN=="" || $MIN>60 || $MIN<0 || !is_number($MIN))
    return false;

 $TEMP=strtok(":");
 $SEC=$TEMP;
 if($SEC=="" || $SEC>60 || $SEC<0 || !is_number($SEC))
    return false;

 return true;
}


/*--------ikernel/utility.php--------*/

function is_leep_year($YEAR)
{
//    if(!is_int($YEAR))
//      return false;
    if(($YEAR%4==0&&$YEAR%100!=0)||$YEAR%400==0)
       return true;
    else
       return false;
}

function is_date_affair($DATE_STR)
{
    $DATE_STR=explode(" ",$DATE_STR);
    $DATE_STR=$DATE_STR[0];
    if($DATE_STR==null||strlen($DATE_STR)==0)
       return false;
    $DATE_ARRY=explode("-",$DATE_STR);
    if(count($DATE_ARRY)!=3)
       return false;
    $YEAR=$DATE_ARRY[0];
    $MONTH=$DATE_ARRY[1];
    $DAY=$DATE_ARRY[2];
    if(!checkdate($MONTH,$DAY,$YEAR))
       return false;
    return true;
}

function is_time_affair($TIME_STR)
{
    if($TIME_STR==null||strlen($TIME_STR)==0)
       return false;
    $TIME_ARRY=explode(":",$TIME_STR);
    if(count($TIME_ARRY)!=3)
       return false;
    $HOUR=$TIME_ARRY[0];
    $MINUTE=$TIME_ARRY[1];
    $SECOND=$TIME_ARRY[2];
    if($HOUR<0||$HOUR>23||$MINUTE<0||$MINUTE>59||$SECOND<0||$SECOND>59)
       return false;
    return true;
}

function is_date_time($DATE_TIME_STR)
{
    if($DATE_TIME_STR==null||strlen($DATE_TIME_STR)==0)
       return false;
    $DATE_TIME_ARRY=explode(" ",$DATE_TIME_STR);
    if(is_date($DATE_TIME_ARRY[0])&&is_time($DATE_TIME_ARRY[1]))
       return true;
    return false;
}

function type_check($FIELD_VALUE,$FIELD_TYPE)
{
   if($FIELD_VALUE=="")
      return true;
   switch($FIELD_TYPE)
   {
      case "C":
           return is_string($FIELD_VALUE)||ctype_alnum($FIELD_TYPE);
      case "T":
           return is_string($FIELD_VALUE)||ctype_alnum($FIELD_TYPE)||is_numeric($FIELD_VALUE);
      case "N":
           return is_numeric($FIELD_VALUE);
      case "D":
           return is_date($FIELD_VALUE);
      case "T":
           return is_time($FIELD_VALUE);
      case "DT":
           return is_date_time($FIELD_VALUE);
      default:
           return false;
   }
}


/*----------------EMAIL-----*/

function email_att($emailid,$return=0){
	$dao=D("Email");
	$row=$dao->where("EMAIL_ID='$emailid'")->find();
	$ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
    $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
    $ARRAY_COUNT=count($ATTACHMENT_ID_ARRAY);
    //ECHO $ARRAY_COUNT;
    $EMAIL_ATTACH="";
    //foreach ($ATTACHMENT_ID_ARRAY AS $)
    for($I=0;$I<$ARRAY_COUNT-1;$I++)
    {
       if ($ATTACHMENT_ID_ARRAY[$I]){	
	       if($EMAIL_ATTACH!="")
	          $EMAIL_ATTACH.="";
	       //ECHO "A";      
	       $EMAIL_ATTACH.="<img src=\"/".APP_PATH."/Tpl/default/Public/images/email_atta.gif\"><a href=\"/index.php/Attach/view/ATTACHMENT_ID/".$ATTACHMENT_ID_ARRAY[$I]."/ATTACHMENT_NAME/$ATTACHMENT_NAME_ARRAY[$I]\">$ATTACHMENT_NAME_ARRAY[$I]</a>";
	       /*
	       if(stristr($ATTACHMENT_NAME_ARRAY[$I],".doc")||stristr($ATTACHMENT_NAME_ARRAY[$I],".ppt")||stristr($ATTACHMENT_NAME_ARRAY[$I],".xls"))
	       {
		     $EMAIL_ATTACH.=" <input type='button' value='阅读' class='SmallButton' onClick=\"window.open('/index.php/Attach/view/ATTACHMENT_ID/".$ATTACHMENT_ID_ARRAY[$I]."/ATTACHMENT_NAME/".urlencode($ATTACHMENT_NAME_ARRAY[$I])."/OP/5',null,'menubar=0,toolbar=0,status=1,scrollbars=1,resizable=1')\";>";
	       }
	       */
       }
    }
    if($return)
       return $EMAIL_ATTACH;
    else 
	   echo $EMAIL_ATTACH;
}

function email_size($emailid){
	$dao=D("Email");
	$daoatt=D("Attachments");
	$row=$dao->where("EMAIL_ID='$emailid'")->find();
	$EMAIL_SIZE=strlen($row[SUBJECT])+strlen($row[CONTENT]);
	$ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
    $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
    foreach ($ATTACHMENT_ID_ARRAY as $attid){
    	if ($attid) {
            $atts=$daoatt->where("attid='$attid'")->find();
            $EMAIL_SIZE += $atts['filesize'];
    	}
    }
    echo $EMAIL_SIZE;
}

function getUserid($username){//
	$dao=D("User");
	$row=$dao->where("USER_NAME='$username'")->find();
    return $row["USER_ID"];
}

function getUsername($userid,$return="1"){
	$dao=D("User");
	$row=$dao->where("USER_ID='$userid'")->find();
	if ($return)
	   return $row["USER_NAME"];
	else 
	   echo $row["USER_NAME"];
	      
}
function getAvatar($userid){
	$dao=D("User");
	$row=$dao->where("USER_ID='$userid'")->find();
	return $row["AVATAR"];
}

function getAvatarImg($userid){
	$dao=D("User");
	$row=$dao->where("USER_ID='$userid'")->find();
	return MOD_PATH."/Public/images/avatar/".$row[AVATAR].".gif";
}

function getOnlineStatusImg($userid){
	/*
	$dao=D("User");
	$row=$dao->where("USER_ID='$userid'")->find();
    $cur_time=CUR_TIME_INT;
    if ($cur_time-$row[LAST_VISIT_TIME]<65) {
    	return MOD_PATH."/Public/images/ico/user$row[SEX].png";
    }else {
        return 	MOD_PATH."/Public/images/ico/userleave.png";
    }
    */
	
    $dao=D("UserSession");    
    $row=$dao->where("userid='$userid'")->find();
    if ($row[userid]) {
    	if ($row[sex]==1) {
            $sex="female";    		
    	}else {
    		$sex="male";    		
    	}
    	if ($row[user_status]=="usOnline"||!$row[user_status]) {
    		$status="";
    	}elseif ($row[user_status]=="usBusy") {
    		$status="_busy";
    	}elseif ($row[user_status]=="usLeave") {
    		$status="_leave";
    	}
    	
    	return MOD_PATH."/Public/images/user/user_".$sex.$status.".gif";
    }else {
    	$dao=D("User");
    	$row=$dao->where("USER_ID='$userid'")->field("SEX")->find();
    	if ($row[SEX]==1) {
            $sex="female";    		
    	}else {
    		$sex="male";    		
    	}
    	    	
        return 	MOD_PATH."/Public/images/user/user_".$sex."_offline.gif";
    }
	///oa/tpl/default/public/images/ico/user$v2[SEX].png
	
	
}
/*----获得多个部门名称---*/
function getDeptnames($deptids){
    	$a = substr($TO_ID,0,strlen($TO_ID)-1);
    	$array=explode(",",$deptids);
    	$dao=D("Department");
    	$name="";
    	foreach ($array as $deptid){
    		if ($deptid) {
    		   if ($deptid=="ALL_DEPT"){
    		       $name.="所有部门,";
    		   }else{    	
    	           $row=$dao->where("DEPT_ID='$deptid'")->find();		
    	           $name.=$row[DEPT_NAME].",";
    		   }
    		}
    	}
		return $name;	
}
/*----获得单个部门名称---*/
function getDeptname($deptid,$return="1"){
	$dao=D("Department");
	$row=$dao->where("DEPT_ID='$deptid'")->find();
	if ($return)
	   return $row["DEPT_NAME"];
	else 
	   echo $row["DEPT_NAME"];
}
function getDeptid($deptname){
	$dao=D("Department");
	$row=$dao->where("DEPT_NAME='$deptname'")->find();
    return $row["DEPT_ID"];
}
function getPrivname($userpriv,$return="1"){
	$dao=D("UserPriv");
	$row=$dao->where("USER_PRIV='$userpriv'")->find();
	if ($return)
	   return $row["PRIV_NAME"];
	else 
	   echo $row["PRIV_NAME"];
}

/*----获得用户状态 ----------*/
function getUserstatus($userstatus){
	
	switch ($userstatus){
		case "usOline":
			return "在线";
		break;	
		case "usBusy":
			return "忙碌";
		break;	
		case "usLeave":
			return "离开";		
		break;	
		default:
			return "在线";
		break;	
	}
	
}

function mktimeFormat($time){//$time = Y-m-d H:i:s
	$array=explode(" ",$time);
	$ymdarray=explode("-",$array[0]);
	$hisarray=explode(":",$array[1]);
	
	if (empty($array[1])) {
		$hisarray[0]=$hisarray[1]=$hisarray[2]=0;
	}
	return mktime($hisarray[0],$hisarray[1],$hisarray[2],$ymdarray[1],$ymdarray[2],$ymdarray[0]);
}

/*----获取状态--时间比较----*/
	 function timeStatus($beginDate,$endDate){
		$curDate = CUR_TIME_INT;
		if($beginDate>$curDate){
			$notifyStatus=1;
		}else{
			$notifyStatus=2;
		}
		if(!empty($endDate)&&$endDate<$curDate){		
			$notifyStatus=3;
		}
		return $notifyStatus;		
	}
	
/*---获取状态--时间比较---*/
	function timeStatusStr($beginDate,$endDate){
		$curDate = CUR_TIME_INT;
		if($beginDate>$curDate){
			$notifyStatus="待生效";
		}else{
			$notifyStatus="生效";
		}
		if(!empty($endDate)&&$endDate<$curDate){		
			$notifyStatus="终止";
		}
		return $notifyStatus;	
	}
	
/*----获取状态--时间比较----*/
	 function FormatTimeStatus($beginDate,$endDate){
		$curDate = CUR_TIME_INT;
		if(mktimeFormat($beginDate)>$curDate){
			$notifyStatus=1;
		}else{
			$notifyStatus=2;
		}
		if(($endDate!='0000-00-00'&&$endDate!='0000-00-00 00:00:00')&&mktimeFormat($endDate)<$curDate){		
			$notifyStatus=3;
		}
		return $notifyStatus;		
	}
		
/*---获取状态--时间比较---*/
	function FormatTimeStatusStr($beginDate,$endDate){
		$curDate = CUR_TIME_INT;
		if(mktimeFormat($beginDate)>$curDate){
			$notifyStatus="待生效";
		}else{
			$notifyStatus="生效";
		}
		if(($endDate!='0000-00-00'&&$endDate!='0000-00-00 00:00:00')&&mktimeFormat($endDate)<$curDate){		
			$notifyStatus="终止";
		}
		return $notifyStatus;	
	}
		
/*-------------------字符串截取-----------------*/
function str_cut($string, $length, $dot = '...')
{
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8')
	{
		$n = $tn = $noc = 0;
		while($n < $strlen)
		{
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t < 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) break;
		}
		if($noc > $length) $n -= $tn;
		$strcut = substr($string, 0, $n);
	}
	else
	{
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		for($i = 0; $i < $maxi; $i++)
		{
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}
	$strcut = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), $strcut);
	return $strcut.$dot;
}

/*---------------添加\'---在添加部门用户选择列表里用到--------*/
function addDyh($str){
	return "\'".$str."\'";
}

/***************************************************webim使用到下列函数*******************************************/
//过滤脚本代码
function cleanJs($text){
	$text	=	trim($text);
	$text	=	stripslashes($text);
	//完全过滤注释
	$text	=	preg_replace('/<!--?.*-->/','',$text);
	//完全过滤动态代码
	$text	=	preg_replace('/<\?|\?>/','',$text);
	//完全过滤js
	$text	=	preg_replace('/<script?.*\/script>/','',$text);
	//过滤多余html
	$text	=	preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i','',$text);
	//过滤on事件lang js
	while(preg_match('/(<[^><]+)(lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	//过滤多余空格
	$text	=	str_replace('  ',' ',$text);
	return $text;
}
//纯文本输出
function t($text){
	$text	=	cleanJs($text);
	$text	=	strip_tags($text);
	$text	=	htmlspecialchars($text);
	return $text;
}
//输出安全的html
function h($text){
	$text	=	cleanJs($text);
	return $text;
}
/****************************************工作流模块***廖秋虎*****************************************************************/
/******工作级别grade******/
function workgrade($i){
	$arr=array(0=>'一般',1=>'重要',2=>'紧急',3=>'紧急且重要');
	echo $arr[$i];
}
/******工作过程*********/
function workState($id){
	if(empty($id)){
		echo '未开始(先定义流程)';
	}elseif ($id=='1001'){
		echo '工作结束';
	}elseif ($id=='1002'){
		echo '工作中断';
	}else {
		echo "工作进入第".$id."步骤";
	}
}
/*******流程state********/
function flowState($id){
	if($id){
	$arr=array(1=>'未开始',2=>'正在进行',3=>'已结束',4=>'中止');
	echo $arr[$id];
	}else{
		echo '未开始';
	}
}
/*****人事管理字段*******/
function hrmsField($id){
	if($id){
		$arr=array(2=>'可定义',1=>'系统默认');
		echo $arr[$id];
	}
}


/**
 * 按人员选择列表
 * */
function getUser_id($id){  
		$dao_u=D("User");//按个人选择人员 
		$TO_USER_ID=explode(",",$id);
		//$listall=array();
		$str="";
        foreach ($TO_USER_ID as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if ($b=="D"){ 
            			$list_d = $dao_u->where("DEPT_ID='$c'")->findall();
            			foreach($list_d as $key2=>$v2){ 
            					$str.=$v2[USER_ID].","; 
            			} 
            		}
            		if ($b=="P"){ 
            			$list_p = $dao_u->where("USER_PRIV='$c'")->findall();
            			foreach($list_p as $key3=>$v3){ 
            					$str.=$v3[USER_ID].","; 
            			}  
 					}
 					if ($b=="U"){ 
            			$list_u = $dao_u->where("uid='$c'")->findall();
            			foreach($list_u as $key4=>$v4){ 
            					$str.=$v4[USER_ID].","; 
            			} 
  					}  	 	       
        }
        return  $str;
	}
/**
 * 
 * */
function getList_name($id){
		$dao_u=D("User");//按个人选择人员 
		$dao_d=D("Department");//按部门选择人员
		$dao_p=D("UserPriv");//按角色选择人员
		
		$TO_USER_ID=explode(",",$id);
		//$listall=array();
		$str="";
        foreach ($TO_USER_ID as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if ($b=="D"){ 
            			$list_d = $dao_d->where("DEPT_ID='$c'")->findall();
            			foreach($list_d as $key2=>$v2){ 
            					$str.=$v2[DEPT_NAME].","; 
            			} 
            		}
            		if ($b=="P"){ 
            			$list_p = $dao_p->where("USER_PRIV='$c'")->findall();
            			foreach($list_p as $key3=>$v3){ 
            					$str.=$v3[PRIV_NAME].","; 
            			}  
 					}
 					if ($b=="U"){ 
            			$list_u = $dao_u->where("uid='$c'")->findall();
            			foreach($list_u as $key4=>$v4){ 
            					$str.=$v4[USER_NAME].","; 
            			} 
  					}  	 	       
        }
        return  $str;
}

?>