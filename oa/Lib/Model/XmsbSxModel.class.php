<?php
class XmsbSxModel extends Model {
	var $tableName="xmsb_sx";
	
	protected $_validate=array(
	   array('SX_NAME','require','名称不能为空'),
	
	);
}


?>