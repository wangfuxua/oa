<?php 
class bookInfoModel extends Model{
	protected $tableName = 'book_info';
	var $_validate=array(
	    array("BOOK_NAME","require","图书名称不能为空")
	);
}
?>