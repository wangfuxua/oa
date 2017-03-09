<?php
class EmailModel extends Model {
	
		var $_validate	 =	 array(
			array('TO_ID','require','收件人不能为空！'),
			array('SUBJECT','require','邮件主题不能为空！')
		);
		/*
	protected $_auto	 =	 array(
		array('OP','0','UPDATE'),
		);
				
	*/
}

?>