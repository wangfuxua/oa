<?php
class ProductModel extends BaseModel {
	protected $trueTableName = 'crm_product';
	protected  $_validate = array(
		array('name','require','名称不能为空！'),
		array('type','require','类型不能为空！'),
	);
}
?>