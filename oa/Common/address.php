<?php
function match($TITLE_NAME)
{
   switch($TITLE_NAME)
   {
      case "姓名":
         return "PSN_NAME";
      case "性别":
         return "SEX";
      case "昵称":
         return "NICK_NAME";
      case "电子邮件地址":
         return "EMAIL";
      case "电子邮件":
         return "EMAIL";
      case "住宅所在街道":
         return "ADD_HOME";
      case "家庭所在街道":
         return "ADD_HOME";
      case "手机":
         return "MOBIL_NO";
      case "移动电话":
         return "MOBIL_NO";
      case "寻呼机":
         return "BP_NO";
      case "传呼机":
         return "BP_NO";
      case "QQ":
         return "OICQ_NO";
      case "ICQ":
         return "ICQ_NO";
      case "生日":
         return "BIRTHDAY";
      case "家庭所在地邮政编码":
         return "POST_NO_HOME";
      case "住宅所在地的邮政编码":
         return "POST_NO_HOME";
      case "家庭所在街道":
         return "ADD_HOME";
      case "住宅所在街道":
         return "ADD_HOME";
      case "家庭电话1":
      case "住宅电话":
         return "TEL_NO_HOME";
      case "公司所在地邮政编码":
         return "POST_NO_DEPT";
      case "公司所在地的邮政编码":
         return "POST_NO_DEPT";
      case "公司所在街道":
         return "ADD_DEPT";
      case "职位":
         return "MINISTRATION";
      case "职务":
         return "MINISTRATION";
      case "办公电话1":
         return "TEL_NO_DEPT";
      case "业务电话":
         return "TEL_NO_DEPT";
      case "公司传真":
         return "FAX_NO_DEPT";
      case "业务传真":
         return "FAX_NO_DEPT";
      case "配偶":
         return "MATE";
      case "子女":
         return "CHILD";
      case "公司":
         return "DEPT_NAME";
      case "附注":
         return "NOTES";
      default:
         return;
   }
   
}

?>