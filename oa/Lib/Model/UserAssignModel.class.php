<?php
class UserAssignModel extends BaseModel {
	protected $trueTableName = "user_assign";
	protected  $_validate = array(
		array('manager_id','require','负责人不能为空！'),
	);
}
?>