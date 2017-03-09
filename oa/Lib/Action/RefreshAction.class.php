<?php
class RefreshAction extends PublicAction {
	function _initialize(){
		parent::_initialize();
	}

	public function  index(){
		
		###日常事务
		affair_sms();
		
	    ###刷新当前用户最后活动时间
	    $sess=new userSessionAction();
	    $sess->sess_gc();
	    $sess->sess_write();
		$ucount=$sess->sess_count();
		
	    $re=array();
		###收到的最后一条信息
		$map="TO_ID='$this->LOGIN_USER_ID' and REMIND_FLAG='1' and SEND_TIME<='$this->CUR_TIME'";
		$dao=D("Sms");
		
		$row=$dao->where($map)->order("SEND_TIME desc")->find();
		$count=$dao->count($map);
		
		$re[msg][mId]=$row[SMS_ID];
		$re[msg][mCon]=strip_tags($row[CONTENT]);
		$re[msg][mNum]=$count;
		
		###收到最后一个对话
		/*
		$map="user_name=user.USER_ID and to_user_name='$this->LOGIN_USER_ID' and flag_new=1 and cre_time<='$this->CUR_TIME_INT'";
		$dao=D('WebimChat');
		$row=$dao->table("webimchat,user")->where($map)->field("user_name as userid,webimchat,msg,user.USER_NAME as username")->order("cre_time desc")->find();
		*/
		$map="to_user_name='$this->LOGIN_USER_ID' and flag_new=1 and cre_time<='$this->CUR_TIME_INT'";
		$dao=D('WebimChat');
		
		$row=$dao->where($map)->order("cre_time desc")->find();
        $dao->setField("flag_new","0",$map);
        
		$dao=D("User");
		$user=$dao->where("USER_ID='$row[user_name]'")->find();
		$re[msg][tId]=$user[USER_ID];
		$re[msg][tName]=$user[USER_NAME];
		$re[msg][tCon]=strip_tags($row[msg]);
		$re[msg][ucount]=$ucount;
		echo json_encode($re);
		
	}
	
}


?>