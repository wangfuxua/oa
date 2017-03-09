<?php
class SupplierModel extends BaseModel {
	protected $trueTableName = "crm_supplier";
	//表单验证
	protected  $_validate = array(
		array('name','require','名称不能为空！'),
		array('builder_id','require','创建人不能为空！'),
		array('last_modifier_id','require','修改者不能为空！'),
	);
}
?>