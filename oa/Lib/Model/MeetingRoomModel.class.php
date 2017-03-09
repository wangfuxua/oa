<?php
class MeetingRoomModel extends Model {
	var $tableName="meeting_room";
	
	var $_validate=array(
	       
	    array("MR_NAME","require","会议室名称不能为空") 
	
	);
	
}


?>