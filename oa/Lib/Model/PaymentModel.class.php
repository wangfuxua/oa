<?php
class PaymentModel extends BaseModel {
	protected $trueTableName = "crm_payment";
	//表单验证
	protected  $_validate = array(
		array('contract_id','require','合同不能为空！'),
		array('product_id','require','产品不能为空！'),
		array('product_name','require','产品不能为空！'),
		array('product_sale_price','require','售价不能为空！'),
		array('product_sale_num','require','销售数量不能为空！'),
	);	
}
?>