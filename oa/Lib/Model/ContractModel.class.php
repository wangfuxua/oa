<?php
class ContractModel extends BaseModel {
	protected $trueTableName = "crm_contract";
	//表单验证
	protected  $_validate = array(
		array('code','require','编号不能为空！'),
		array('name','require','名称不能为空！'),
		array('account_id','require','客户不能为空！'),
		array('account_name','require','客户不能为空！'),
		array('type','require','类型不能为空！'),
		array('star_time','require','生效日期不能为空！'),
		array('end_time','require','终止日期不能为空！'),
		array('buyer','require','买方签约人不能为空！'),
		array('seller_id','require','卖方签约人不能为空！'),
		array('seller_name','require','卖方签约人不能为空！'),
		array('time_create','require','创建时间不能为空！'),
		array('time_modify','require','最后修改时间不能为空！'),
	);		
}
?>