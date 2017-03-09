<?php
class CrmBankModel extends BaseModel{
	
	protected $trueTableName = "crm_bank";
	//表单验证
	protected  $_validate = array(
		array('record_id','require','相关记录id不能为空！'),
	);
	
	protected $_fields = array(
				'bank1_name' 			=> '第一开户银行',
				'bank1_account_name'	=> '第一开户银行开户人',	
				'bank1_account_name'	=> '第一开户银行帐号',
				'bank1_tariff'			=> '第一纳税号',
				'bank2_name' 			=> '第二开户银行',
				'bank2_account_name'	=> '第二开户银行开户人',	
				'bank2_account_name'	=> '第二开户银行帐号',
				'bank2_tariff'			=> '第二纳税号',
				'pay_type'				=> '支付方式',
				'credit'				=> '信用额度',				
	);
	
	public function getKeys()
	{
		
		//$fields = array();
		return $this->_fields;
	}
	
	public function addCsv($key, $data)
	{
		//echo $key;
		//echo $data;
		$bank = D('CrmBank');
		$sql = "insert into crm_bank $key values $data";
		$bank->query($sql);	
	}
}
?>