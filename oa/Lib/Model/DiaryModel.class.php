<?php
class DiaryModel extends Model {
	
	protected $_auto=array(
	   
	);
	protected $_validate=array(
	   array("SUBJECT","require","日志标题不能为空","ALL"),
	   array("CONTENT","require","日志内容不能为空","ALL")
	);
}

?>