<?php
class SysFunctionModel extends Model {
	 var $tableName="sys_function";
	 
	 var $_validate=array(
	        array("FUNC_NAME","require","名称不能为空","all"),
	        array("funcpath","require","路径不能为空","all"),
	        array("css","require","css不能为空","all"),
	        array("clientjs_moduleid","require","moduleid不能为空","all"),
	        array("clientjs_moduletype","require","moduletype不能为空","all"),
	        array("clientjs","require","配置JS不能为空","all"),
	        array("contentjs","require","内容js不能为空","all"),
	);
	 
}

?>