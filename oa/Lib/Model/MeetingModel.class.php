<?php
class MeetingModel extends BaseModel {
		var $_validate=array(
	    array("M_TOPIC ","require","会议主题不能为空","ALL") 
	);
}

?>