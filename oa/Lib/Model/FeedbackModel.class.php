<?php
class FeedbackModel extends Model{
	protected $trueTableName = "feedback";
	//表单验证
	protected  $_validate = array(
		array('content','require','内容不能为空！'),
	);
	
}
?>