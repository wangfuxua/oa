<?php
class DevelopmentModel extends Model{
	protected $trueTableName = "development";
	//表单验证
	protected  $_validate = array(
		array('content','require','内容不能为空！'),
	);
	
}
?>