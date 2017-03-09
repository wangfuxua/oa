<?php

class UserModel extends BaseModel {
	var $_validate=array(
	    array('USER_ID','require','用户名不能为空！'),
	    array('USER_ID','','用户名已经存在',0,'unique','ADD'),
		array('PWD','require','密码不能为空！'),
		array('REPWD','PWD','密码不一致！',0,'confirm'),	    
        array('USER_NAME','require','真实姓名不能为空！')
	);
	
		public function UserSelect(){
		$dao=D("User");
		$list=$dao->where("job_status=1")->findall();
		return $list;
	   }
}

?>