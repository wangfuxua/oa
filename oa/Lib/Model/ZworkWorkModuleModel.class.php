<?php
class ZWorkWorkModuleModel extends Model {
	var $tableName="zwork_workmodule";
	var $_validate=array(
	        array("modelName","require","中文名称不能为空"),
	        array("modelTableName","require","英文名称不能为空")
	        
	);
}
?>