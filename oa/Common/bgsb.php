<?php
function match($TITLE_NAME)
{
	//echo $TITLE_NAME;
   switch($TITLE_NAME)
   {
      case "类别代码":
         return "LB";
         break;
      case "资产编号":
         return "BH";
         break;
      case "登记日期":
         return "DJSJ";
         break;
      case "资产名称":
         return "MC";
         break;
      case "型号规格":
         return "GGXH";
         break;
      case "数量":
         return "SL";
         break;
      case "保管人":
         return "GLR_NAME";
         break;
      case "保管人部门":
         return "SZD";
         break;
      case "所在地":
         return "SZD";
         break;
      case "统计":
         return "ZCLRY_NAME";         
         break;
      case "备注":
         return "info";
         break;
      default:
         return "ZCLRY_NAME"; 
   }
   
}

?>