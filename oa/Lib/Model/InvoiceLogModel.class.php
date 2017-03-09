<?php
class InvoiceLogModel extends BaseModel{
	
	protected $trueTableName = "crm_invoicelog";
	//表单验证
	protected  $_validate = array(
		array('name','require','名称不能为空！'),
		array('account_name','require','客户名称不能为空！'),
		array('money','require','金额不能为空！'),
		array('invoice_type','require','发票类型不能为空！'),
		array('invoice_date','require','开票日期不能为空！'),
	);
}
?>