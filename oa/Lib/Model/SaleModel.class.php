<?php
class SaleModel extends BaseModel {
	protected $trueTableName = "crm_sale";
	//表单验证
	protected  $_validate = array(
		array('builder_id','require','创建者不能为空！'),
		array('seller _id','require','销售者不能为空！'),
		array('product_id','require','产品不能为空！'),
		array('last_modifier_id','require','最后修改者不能为空！'),
		array('sale_price','require','销售价格不能为空！'),
		array('sale_num','require','销售数量不能为空！'),
		array('record_num','require','记录号不能为空！'),
		array('payment','require','回款不能为空！'),
		array('no_payment','require','未结款不能为空！'),
	);

}
?>