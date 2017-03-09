<?php

/*----------在列表页显示附件---------*/
function file_att_list($ATTACHMENT_NAME,$ATTACHMENT_ID,$MANAGE_PRIV,$DOWN_PRIV){
         $str="";
	 if($ATTACHMENT_NAME=="")
         $str="无";
	 else {
	 	 $ATTACHMENT_ID_ARRAY=explode(",",$ATTACHMENT_ID);
         $ATTACHMENT_NAME_ARRAY=explode("*",$ATTACHMENT_NAME);
         
         $ARRAY_COUNT=sizeof($ATTACHMENT_ID_ARRAY);
         
         for($I=0;$I<$ARRAY_COUNT;$I++)
         {
            if($ATTACHMENT_ID_ARRAY[$I]=="")
               break;
               
            $dao=D("Attachments");
            $att=$dao->where("attid='$ATTACHMENT_ID_ARRAY[$I]'")->find();
            $ATTACH_SIZE=$att[filesize];
            $ATTACH_SIZE=number_format($ATTACH_SIZE,0, ".",",");
            
            //$str.="<img src=\"../Public/images/email_atta.gif\">";
            
           //如果有下载权限 
           if($DOWN_PRIV==1||(!stristr($ATTACHMENT_NAME_ARRAY[$I],".doc")&&!stristr($ATTACHMENT_NAME_ARRAY[$I],".ppt")&&!stristr($ATTACHMENT_NAME_ARRAY[$I],".xls")))
           {
                $str.="<a href=\"/index.php/Attach/view/ATTACHMENT_ID/".$ATTACHMENT_ID_ARRAY[$I]."/ATTACHMENT_NAME/".urlencode($ATTACHMENT_NAME_ARRAY[$I])."\">".csubstr($ATTACHMENT_NAME_ARRAY[$I],0,40)."</a>";	
           }else
           {
           	    $str.=$ATTACHMENT_NAME_ARRAY[$I];
           }
           
           /*
           if(stristr($ATTACHMENT_NAME_ARRAY[$I],".doc")||stristr($ATTACHMENT_NAME_ARRAY[$I],".ppt")||stristr($ATTACHMENT_NAME_ARRAY[$I],".xls"))
           {
				$str.="<button onClick=\"window.open('/index.php/Attach/read/ATTACHMENT_ID/".($ATTACHMENT_ID_ARRAY[$I])."/ATTACHMENT_NAME/".urlencode($ATTACHMENT_NAME_ARRAY[$I])."/OP/5',null,'menubar=0,toolbar=0,status=1,scrollbars=1,resizable=1');\">阅读</button>";
                if($MANAGE_PRIV==1)//如果有管理权限
				   $str.="<button onClick=\"window.open('/index.php/Attach/read/ATTACHMENT_ID/".($ATTACHMENT_ID_ARRAY[$I])."/ATTACHMENT_NAME/".urlencode($ATTACHMENT_NAME_ARRAY[$I])."/OP/4',null,'menubar=0,toolbar=0,status=1,scrollbars=1,resizable=1');\">编辑</button>";
           }
           */
             
              $str.="（".$ATTACH_SIZE."字节）<br>";
           
         }	 	
	 }
	
     return $str;
     	
}


?>