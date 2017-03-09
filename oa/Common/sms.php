<?
function getSmsType($SMS_TYPE="0"){
	
	switch($SMS_TYPE)
    {
     case "0":
         $SMS_TYPE_NAME="个人短信";
         break;
     case "1":
         $SMS_TYPE_NAME="公告通知提醒";
         break;
     case "2":
         $SMS_TYPE_NAME="邮件提醒";
         break;
     case "3":
         $SMS_TYPE_NAME="网络会议提醒";
         break;
     case "4":
         $SMS_TYPE_NAME="工资上报提醒";
         break;
     case "5":
         $SMS_TYPE_NAME="日程安排提醒";
         break;
     case "6":
         $SMS_TYPE_NAME="考勤批示提醒";
         break;
     case "7":
         $SMS_TYPE_NAME="工作流提醒";
         break;
     case "8":
         $SMS_TYPE_NAME="会议申请提醒";
         break;
     case "9":
         $SMS_TYPE_NAME="车辆申请提醒";
         break;
     case "10":
     	$SMS_TYPE_NAME="申报初审工作提醒";
      	break;
     case "11":
     	$SMS_TYPE_NAME="申报终审工作提醒";
     	break;
     case "12":
     	$SMS_TYPE_NAME="申报复核工作提醒";
     	break;
     case "13":
     	$SMS_TYPE_NAME="立项工作提醒";
     	break;
     case "14":
     	$SMS_TYPE_NAME="项目工作提醒";
     	break;
     case "15":
     	$SMS_TYPE_NAME="项目文件审核工作提醒";
       	break;
     case "16":
     	$SMS_TYPE_NAME="项目审批工作提醒";
       	break;
     case "17":
     	$SMS_TYPE_NAME="项目申报工作提醒";
       	break;
     case "18":
     	$SMS_TYPE_NAME="合同到期提醒";
       	break;
    }
    
    return $SMS_TYPE_NAME;
	
}

function getLeftSmsUrl($SMS_TYPE,$CONTENT=""){
	
	switch($SMS_TYPE)
    {
     case "0":
         $SMS_TYPE_NAME="个人短信";
         $URL=__APP__.'/Sms/index';
         break;
     case "1":
         $SMS_TYPE_NAME="公告通知提醒";
         $URL=__APP__.'/manage/notifyAll';
         break;
     case "2":
         $SMS_TYPE_NAME="邮件提醒";
         $URL=__APP__.'/Email/inbox/?BOX_ID=0';
         break;
     case "3":
         $SMS_TYPE_NAME="网络会议提醒";
         $URL=__APP__.'/NetMeeting/';
         break;
     case "4":
         $SMS_TYPE_NAME="工资上报提醒";
         $URL=__APP__.'/salary/index';
         break;
     case "5":
         $SMS_TYPE_NAME="日程安排提醒";
         $URL=__APP__.'/Calendar/';
         break;
     case "6":
         $SMS_TYPE_NAME="考勤批示提醒";
         if(strstr($CONTENT,"提交")&&strstr($CONTENT,"申请")&&strstr($CONTENT,"请批示"))
            $URL=__APP__.'/attendance/manage/';
         else
            $URL=__APP__.'/attendance/personal/';
         break;
     case "7":
         $SMS_TYPE_NAME="工作流提醒";
         $URL=__APP__.'/ZworkFlow/';
         break;
     case "8":
         $SMS_TYPE_NAME="会议申请批复提醒";
         $URL=__APP__.'/Meeting/';
         break;
     case "9":
         $SMS_TYPE_NAME="车辆申请批复提醒";
         $URL=__APP__.'/vehicle/';
         break;
     case "10":
     	$SMS_TYPE_NAME="初审工作提醒";
     	$URL=__APP__.'/ProCs';
     	break;
     case "11":
     	$SMS_TYPE_NAME="终审工作提醒";
     	$URL=__APP__.'/ProZs';
     	break;
     case "12":
     	$SMS_TYPE_NAME="复核工作提醒";
     	$URL=__APP__.'/ProCheck';
     	break;
     case "13":
     	$SMS_TYPE_NAME="立项工作提醒";
     	$URL=__APP__.'/ProExeCreate';
     	break;
     case "14":
     	$SMS_TYPE_NAME="项目工作提醒";
     	$URL=__APP__.'/general/pro_execution/maintenance/';
     	break;
     case "15":
     	$SMS_TYPE_NAME="项目文件审核工作提醒";
     	$URL=__APP__.'/general/pro_execution/audit/';
     	break;
     case "16":
     	$SMS_TYPE_NAME="项目审批工作提醒";
     	$URL=__APP__.'/general/pro_execution/permit/';
     	break;
     case "17":
     	$SMS_TYPE_NAME="项目申报工作提醒";
     	$URL=__APP__.'/general/pro_manage/pro_apply/';
     	break;
    }
	
	return $URL;
	
}

?>