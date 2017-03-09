<?php
class userSessionAction extends PublicAction {
	function _initialize(){
		parent::_initialize();
	}
		
	public function sess_read(){
        $dao=D("UserSession"); 		
		$row=$dao->where("userid='$this->LOGIN_USER_ID'")->find();
		return $row;
	}	
	
	public function sess_count(){
        $dao=D("UserSession"); 		
        $count=$dao->count();
		//$row=$dao->where("userid='$this->LOGIN_USER_ID'")->find();
		return $count;
	}	
		
	public function sess_write(){
	  if ($this->LOGIN_USER_ID) {
        $dao=D("UserSession");
        $map="userid='$this->LOGIN_USER_ID'";
        $count=$dao->count($map);
	        if ($count>0) {
	            $dao->setField("lastactivity",$this->CUR_TIME_INT,"userid='$this->LOGIN_USER_ID'");   	  		
	        }else {
	        	$data[userid]=$this->LOGIN_USER_ID;
	        	$data[sex]=Session::get("LOGIN_SEX");
	        	$data[lastactivity]=$this->CUR_TIME_INT;
	        	$dao->add($data);
	        }
	  }
	}
	
	public function sess_dele($userid=""){//删除单个用户
		$dao=D("UserSession"); 
		if ($userid) {
		$dao->where("userid='$userid'")->delete();
		}else {
		$dao->where("userid='$this->LOGIN_USER_ID'")->delete();		
		}
	}
			
	public function sess_gc(){//删除未活动的用户
		$dao=D("UserSession");
		$dao->where("lastactivity< ".($this->CUR_TIME_INT-$this->onlineTime))->delete();
	}
	
}


?>