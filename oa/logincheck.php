<?
include_once("inc/reg_check.php");
if($OA_REG_ON!=2)
   exit;

include_once("inc/conn.php");
include_once("inc/utility.php");
include_once("inc/utility_all.php");
session_start();
ob_start();
$CUR_TIME=date("Y-m-d H:i:s",time());
?>

<html>
<head>
<title>ϵͳ��¼</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" type="text/css" href="/theme/1/style.css">
</head>

<body class="bodycolor" topmargin="5">

<?
//--------  ȡϵͳ���ò��� --------
$query = "SELECT * from SYS_PARA where PARA_NAME='SEC_PASS_FLAG' or PARA_NAME='SEC_PASS_TIME' or PARA_NAME='SEC_PASS_IMG'";
$cursor= exequery($connection,$query);
while($ROW=mysql_fetch_array($cursor))
{
   $PARA_NAME=$ROW["PARA_NAME"];
   $PARA_VALUE=$ROW["PARA_VALUE"];

   if($PARA_NAME=="SEC_PASS_FLAG")
      $SEC_PASS_FLAG=$PARA_VALUE;
   else if($PARA_NAME=="SEC_PASS_TIME")
      $SEC_PASS_TIME=$PARA_VALUE;
   else if($PARA_NAME=="SEC_PASS_IMG")
      $SEC_PASS_IMG=$PARA_VALUE;
}

//-------- ��֤�û�����ͼ����֤�� ----------
if($SEC_PASS_IMG=="1")
{
   if(isset($PASSWORD2)&&session_is_registered("PASS_IMG_NUM"))
   {
       if(crypt($PASSWORD2,$PASS_IMG_NUM)!=$PASS_IMG_NUM)
       {
          $LOGIN_ERROR="ͼ����֤�����";
          Message("����",$LOGIN_ERROR);
?>
<br>
<div align="center">
  <input type="button" value="���µ�¼" class="BigButton" onclick="location='/'">
</div>
<?
          exit;
       }
   }
   else
   {
      $LOGIN_ERROR="ͼ����֤�����";
      Message("����",$LOGIN_ERROR);
?>
<br>
<div align="center">
  <input type="button" value="���µ�¼" class="BigButton" onclick="location='/'">
</div>
<?
      exit;
   }
}

//-------- IP���� --------
$USER_IP=$_SERVER["REMOTE_ADDR"];
if(!check_ip($USER_IP,"0"))
{
   add_log(9,"USER_ID=$USERNAME",$USERNAME);
   Message("����","����Ȩ�޴Ӹ�IP(".$USER_IP.")��¼!");
?>
<br>
<div align="center">
  <input type="button" value="���µ�¼" class="BigButton" onclick="location='/'">
</div>
<?
  exit;
}

//-------- ���� --------
$OA_UPDATE_FILE="update/1.php";
if(file_exists($OA_UPDATE_FILE))
{
   include_once($OA_UPDATE_FILE);
   unlink($OA_UPDATE_FILE);
}

//--------- ������֤ --------
 $LOGIN_ERROR="�û������������ע���Сд���������µ�¼!";
 $connection=OpenConnection();
 $query = "SELECT * from USER where USER_ID='$USERNAME'";
 $cursor= exequery($connection,$query);

 if(!$ROW=mysql_fetch_array($cursor))
 {
  add_log(10,"USER_ID=$USERNAME",$USERNAME);
  Message("����",$LOGIN_ERROR);
?>
<br>
<div align="center">
  <input type="button" value="���µ�¼" class="BigButton" onclick="location='/'">
</div>
<?
  exit;
 }

 $USER_ID=$ROW["USER_ID"];
 if($USERNAME!=$USER_ID)
 {
  add_log(10,"USER_ID=$USERNAME",$USERNAME);
  Message("����",$LOGIN_ERROR);
?>
<br>
<div align="center">
  <input type="button" value="���µ�¼" class="BigButton" onclick="location='/'">
</div>
<?
  exit;
 }

 $PWD=$ROW["PASSWORD"];
 $LOGIN_USER_PRIV=$ROW["USER_PRIV"];
 $LOGIN_AVATAR=$ROW["AVATAR"];
 $LOGIN_DEPT_ID=$ROW["DEPT_ID"];
 $MENU_TYPE=$ROW["MENU_TYPE"];
 $LAST_PASS_TIME=$ROW["LAST_PASS_TIME"];
 $LOGIN_THEME=$ROW["THEME"];

 $MENU_TYPE=2-$MENU_TYPE;

 if(0)
 {
  $ERROR_PWD=maskstr($PASSWORD,2,1);
  add_log(2,$ERROR_PWD,$USERNAME);
  Message("����",$LOGIN_ERROR);
?>
<br>
<div align="center">
  <input type="button" value="���µ�¼" class="BigButton" onclick="location='/'">
</div>
<?
  exit;
 }

$LOGIN_USER_ID=$USER_ID;

session_register("LOGIN_USER_ID");
session_register("LOGIN_USER_PRIV");
session_register("LOGIN_DEPT_ID");
session_register("LOGIN_AVATAR");
session_register("LOGIN_THEME");

setcookie ("USER_NAME_COOKIE", $LOGIN_USER_ID,time() + 60*60*24*1000);

//-------- ��������Ƿ���� --------
if($SEC_PASS_FLAG=="1"&&(time()-strtotime($LAST_PASS_TIME) >=$SEC_PASS_TIME*24*3600))
   header("location: general/pass.php");

//-------- ��־ --------
add_log(1,"",$LOGIN_USER_ID);

//-------- �����ճ̰������ɶ���Ϣ --------
affair_sms();
?>

<script>
 var open_flag=window.open("general/index.php",'<?=md5($USERNAME)?>',"menubar=0,toolbar=<?=$MENU_TYPE?>,status=1,resizable=1");
 if(open_flag== null)
    location="general/index.php";
 else
 {
    focus();
    window.opener =window.self;
    window.close();
 }
</script>

</body>
</html>
