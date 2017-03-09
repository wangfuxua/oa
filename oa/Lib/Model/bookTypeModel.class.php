<?php
class bookTypeModel extends Model {
	var $tableName='book_type';
	var $_validate=array(
	    array("TYPE_NAME","require","类别名称不能为空","ALL")
	);
}
?>