<?php
class FileContentModel extends BaseModel {
	var $tableName="file_content";
		protected $_validate	 =	 array(
			array('SUBJECT','require','文件名称不能为空！'),
			array("SUBJECT","fun_add","文件名称已经存在","2","callback","add"),
			array("SUBJECT","fun_edit","文件名称已经存在","2","callback","edit"),
		);
		
	public function fun_add(){
		$LOGIN_USER_ID=Session::get("LOGIN_USER_ID");
		$dao=D("FileContent");
           $map="SORT_ID='$_REQUEST[SORT_ID]' and SUBJECT='$_REQUEST[SUBJECT]'";
           $count=$dao->count($map);
           if ($count) {//如果已经存在
           	 return false; //返回失败
           }else {
           	 return true;//返回成功
           }
		
	}	
	
	public function fun_edit(){
		$LOGIN_USER_ID=Session::get("LOGIN_USER_ID");
		$dao=D("FileContent");
		$row=$dao->where("SORT_ID='$_REQUEST[SORT_ID]'")->find();
		$SORT_PARENT=$row[SORT_PARENT];
		
        $map="SORT_ID='$_REQUEST[SORT_ID]' and SUBJECT='$_REQUEST[SUBJECT]' and CONTENT_ID!='$_REQUEST[CONTENT_ID]'";

           $count=$dao->count($map);
           if ($count) {//如果已经存在
           	 return false; //返回失败
           }else {
           	 return true;//返回成功
           }
	}	
			
		
	/*
		protected $_validate	 =	 array(
			array('TO_ID','require','收件人不能为空！'),
			array('SUBJECT','require','邮件主题不能为空！')
		);
		
	protected $_auto	 =	 array(
		array('OP','0','UPDATE'),
		);
		*/
	
}

?>