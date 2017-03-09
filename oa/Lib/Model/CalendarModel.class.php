<?php
class CalendarModel extends Model {
	
		protected $_validate=array(
	        array("CONTENT","require","事务内容不能为空","ALL")
	    );
}


?>