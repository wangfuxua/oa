<?php
class UserPrivModel extends BaseModel {
	var $tableName="user_priv";
	
	
	public function PrivSelect(){
		$dao=D("UserPriv");
		$list=$dao->order("PRIV_NO")->findall();
		
		return $list;
		
	}
	
	
}

?>
