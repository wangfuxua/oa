<?php
class voteItemModel extends Model {
        var $tableName="vote_item";	
	
	  	var $_validate=array(
	         array('ITEM_NAME','require','选项名称不能为空！')
	    );
}

?>