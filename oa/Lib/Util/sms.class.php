<?
class sms{
	public function getSmsType(){
		$array=array(
			"0"=>"个人短信",
			"1"=>"公告通知提醒",
			"2"=>"邮件提醒",
			"3"=>"网络会议提醒",
			"4"=>"工资上报提醒",
			"5"=>"日程安排提醒",
			"6"=>"考勤批示提醒",
			"7"=>"工作流提醒",
			"8"=>"会议申请提醒",
			"9"=>"车辆申请提醒",
			"10"=>"申报初审工作提醒",
			"11"=>"申报终审工作提醒",
			"12"=>"申报复核工作提醒",
			"13"=>"立项工作提醒",
			"14"=>"项目工作提醒",
			"15"=>"项目文件审核工作提醒",
			"16"=>"项目审批工作提醒",
			"17"=>"项目申报工作提醒"
		);
	return $array;  	
	}
	
}


?>