<?php
class AddressModel extends Model {
	
	var $_validate=array(
	        array("PSN_NAME","require","联系人姓名不能为空","ALL"),
	        array("POST_NO_DEPT","fun_zip","单位邮编不正确","2","callback"),
	        array("TEL_NO_DEPT","fun_phone","单位电话不正确","2","callback"),
	        array("POST_NO_HOME","fun_zip","家庭邮编不正确","2","callback"),
	        array("TEL_NO_HOME","fun_phone","家庭电话不正确","2","callback"),
	        array("EMAIL","email","邮件格式不正确","2")
	);
	
	//验证邮编    
	public function fun_zip($str)    
	{    
	  return (preg_match("/^[1-9]\d{5}$/",$str))?true:false;    
	} 
	//验证电话号码    
	public function fun_phone($str)    
	{    
	  return (preg_match("/^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}$/",$str))?true:false;    
	} 
}

?>