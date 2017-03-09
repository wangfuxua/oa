<?php
class AddressGroupModel extends Model {
	var $tableName="address_group";

	var $_validate=array(
	        array("GROUP_NAME","require","分组名称不能为空","ALL")
	);
		
}

?>