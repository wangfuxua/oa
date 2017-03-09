<?php
class ProductServiceModel extends BaseModel{
	
	protected $trueTableName = "crm_productservice";
	//表单验证
	protected  $_validate = array(
		array('name','require','产品保修档案名称不能为空！'),
		array('account_name','require','客户名称不能为空！'),
		array('product_name','require','产品名称不能为空！'),
	);
}
?>