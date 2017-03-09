<?php
class AccountModel extends BaseModel{

	protected $trueTableName = "crm_account";
	//����֤
	protected  $_validate = array(
		array('name','require','��������Ϊ�գ�'),
		array('last_modifier_id','require','�޸��߲���Ϊ�գ�'),
		array('manager_id','require','�����˲���Ϊ�գ�'),
		array('builder_id','require','�����˲���Ϊ�գ�'),
	);
	
	public function getFields()
	{
		$_account = D('Account');
		$fields = $_account->getDbFields();
		echo $fileds;	
		return $fields;
	}
	
	protected $_fields = array(
						'name'  		   => '�ͻ�����',
						'builder_id'	   => '������',
						'manager_id' 	   => '������',
						'last_modifier_id' => '����޸���',
						'source' 		   => '�ͻ���Դ',
						'type' 			   => '�ͻ�����',
						'industry' 		   => '�ͻ���ҵ',
						'status' 		   => '״̬',
						'employees'    	   => 'Ա����',
						'contact_man' 	   => '�ͻ���ϵ��',
						'website' 		   => '��ַ',
						'stocks_code' 	   => '��Ʊ����',
						'phone_work' 	   => '�칫�绰',
						'phone_fax' 	   => '����',
						'mail' 		       => '�����ʼ�',
						'description' 	   => '����',
						'time_create' 	   => '����ʱ��',
						'time_modify' 	   => '����޸�ʱ��',
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
		//$sql = "insert into crm_account (name,builder_id,manager_id,last_modifier_id,source,type,industry,status,employees,contact_man,website,stocks_code,phone_work,phone_fax,mail,description,time_create,time_modify) values (����ʳƷ,ϵͳ����Ա,���,ϵͳ����Ա,չ��,A�࣬����������ȷ���ڿ�ʵ��ǩ��,������,,300,�����,www.baidu.com,,010-87654321,1012345678,,,2009-3-10 17:25,2009-3-13 17:49 ), (�Ĵ�ʵҵ,ϵͳ����Ա,��������,ϵͳ����Ա,�绰,B�࣬�к���Ǳ���ɳ��ڸ���,,,500,,,,,,,,2009-3-10 19:27,2009-3-13 17:50 ), (����ҩƷ��ҵ��˾,ϵͳ����Ա,����,ϵͳ����Ա,�绰,,,,1,�͹ķ�,,,,,,,2009-3-12 21:19,2009-3-13 17:49 )";
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