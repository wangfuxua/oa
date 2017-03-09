<?php
class CrmAddressModel extends Model{
	
	protected $trueTableName = "crm_address";
	//表单验证
	protected  $_validate = array(
		array('table_name','require','类型不能为空！'),
		array('record_id','require','记录不能为空！'),
	);
	
	protected $_fields = array(
				'country'		=> '国别',
				'province'		=> '省份',
				'city'			=> '城市',
				'street'		=> '街道',
				'postcode'		=> '邮编',							
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
		$address = D('CrmAddress');
		$sql = "insert into crm_address $key values $data";
		$address->query($sql);	
	}
	
}
?>