<?php
class BgsbTypeModel extends BaseModel {
	var $tableName="bgsb_type";
	
	var $_validate=array(
	        array("typename","require","类别名称不能为空","ALL")
	);
	
}

?>