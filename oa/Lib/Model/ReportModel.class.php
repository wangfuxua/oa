<?php
class ReportModel extends BaseModel{
	
	protected $trueTableName = "crm_report";
	//表单验证
	protected  $_validate = array(
		array('subject','require','主题不能为空！'),
		array('account_name','require','客户名称不能为空！'),
	);
}
?>