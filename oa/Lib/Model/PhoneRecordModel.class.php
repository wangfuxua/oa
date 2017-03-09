<?php
class PhoneRecordModel extends BaseModel{
	
	protected $trueTableName = "crm_phonerecord";
	//表单验证
	protected  $_validate = array(
		array('subject','require','主题不能为空！'),
		array('phone','require','电话不能为空！'),
	);
}
?>