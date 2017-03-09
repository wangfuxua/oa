<?php
class CallbackModel extends BaseModel{
	
	protected $trueTableName = "crm_callback";
	//表单验证
	protected  $_validate = array(
		array('account_name','require','客户名称不能为空！'),
		array('service_date','require','客服日期不能为空！'),
	);
}
?>