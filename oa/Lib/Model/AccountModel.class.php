<?php
class AccountModel extends BaseModel{

	protected $trueTableName = "crm_account";
	//表单验证
	protected  $_validate = array(
		array('name','require','姓名不能为空！'),
		array('last_modifier_id','require','修改者不能为空！'),
		array('manager_id','require','负责人不能为空！'),
		array('builder_id','require','创建人不能为空！'),
	);
	
	public function getFields()
	{
		$_account = D('Account');
		$fields = $_account->getDbFields();
		echo $fileds;	
		return $fields;
	}
	
	protected $_fields = array(
						'name'  		   => '客户名称',
						'builder_id'	   => '创建人',
						'manager_id' 	   => '负责人',
						'last_modifier_id' => '最后修改者',
						'source' 		   => '客户来源',
						'type' 			   => '客户类型',
						'industry' 		   => '客户行业',
						'status' 		   => '状态',
						'employees'    	   => '员工数',
						'contact_man' 	   => '客户联系人',
						'website' 		   => '网址',
						'stocks_code' 	   => '股票代码',
						'phone_work' 	   => '办公电话',
						'phone_fax' 	   => '传真',
						'mail' 		       => '电子邮件',
						'description' 	   => '描述',
						'time_create' 	   => '创建时间',
						'time_modify' 	   => '最后修改时间',
					); 
	
	public function getKeys()
	{
		
		//$fields = array();
		return $this->_fields;
	}
	
	/*public function getFields_values()
	{
		$values = '';
		foreach ($this->_fields as $key=>$value) {
			$values .= $value . ',';
		}
		
		return $values;
	}*/
	
	public function getList()
	{
		$account = D('Account');
		$sql = "select *, build.USER_NAME as builder_id, " . 
						 "manager.USER_NAME as manager_id, " . 
						 "modifier.USER_NAME as last_modifier_id " .
						 //"address.*, bank.* " . 
						 "from crm_account as a  " . 
						 "left join user as build on (a.builder_id = build.uid) " . 
						 "left join user as modifier on (a.last_modifier_id = modifier.uid) " .
						 "left join user as manager on (a.manager_id = manager.uid)" . 
						 "left join crm_address as address on (a.id = address.record_id and address.table_name = 'Account') " .
						 "left join crm_bank as bank on (a.id = bank.record_id and bank.table_name = 'Account') ";
		//$sql = "select * from crm_account as a left join crm_address as address on (a.id = address.record_id and address.table_name = 'Account')";
		$accountList = $account->query($sql); 
		//print_r($accountList);
		return $accountList;
	}
	
	public function addCsv($key, $data)
	{
		$account = D('Account');
		//echo $key;
		//echo 'addCsv';
		//print_r($data);
		//$key = '(' . $key . ') ';
		//$elems = '(';
		/*foreach ($data as $row){
			$elems .= '\'' . implode('\',\'', $row) . '\'), (';
			//echo $elems . '<br />';
		}
		//echo $data;
		//echo $elems;
		//$elems = $this->_format($elems);
		$elems = substr($elems, 0, -3); //echo $elemss;
		//echo $elems;
		//echo $data;*/
		//echo $key;
		//echo $data;
		$sql = "insert into crm_account $key values $data";
		//echo $sql;
		//$sql = "insert into crm_account (name,builder_id,manager_id,last_modifier_id,source,type,industry,status,employees,contact_man,website,stocks_code,phone_work,phone_fax,mail,description,time_create,time_modify) values (桂林食品,系统管理员,刘浏,系统管理员,展会,A类，合作意向明确近期可实现签单,建筑类,,300,向冠宇,www.baidu.com,,010-87654321,1012345678,,,2009-3-10 17:25,2009-3-13 17:49 ), (四川实业,系统管理员,销售主管,系统管理员,电话,B类，有合作潜力可长期跟进,,,500,,,,,,,,2009-3-10 19:27,2009-3-13 17:50 ), (云南药品治业公司,系统管理员,科沃,系统管理员,电话,,,,1,和鼓风,,,,,,,2009-3-12 21:19,2009-3-13 17:49 )";
		//$sql = "insert into crm_account (name) values ('aaaaa'), ('bbb')";
		$account->query($sql);
		$id = mysql_insert_id();
		return $id;
		//echo $keys;
		
	}
	
	public function _format($STR){
	   $STR=str_replace("\"","'",$STR);
          $STR=iconv("UTF-8","GB2312",$STR);
          $STR=trim($STR);
          
	   if(strpos($STR,","))
	      $STR="\"".$STR."\"";
	   
	   return $STR;	
	}
}
?>