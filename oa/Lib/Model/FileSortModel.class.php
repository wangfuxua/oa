<?php
class FileSortModel extends BaseModel {
	var $tableName="file_sort";
	
	protected $_validate	 =	 array(
			array('SORT_NAME','require','文件柜名称不能为空！'),
			array("SORT_NAME","fun_add","文件柜名称已经存在","2","callback","add"),
			array("SORT_NAME","fun_edit","文件柜名称已经存在","2","callback","edit"),
		);

	public function fun_add(){
		$LOGIN_USER_ID=Session::get("LOGIN_USER_ID");
		$dao=D("FileSort");
           $map="SORT_NAME='$_REQUEST[SORT_NAME]' and SORT_PARENT='$_REQUEST[SORT_ID]'";
           if ($_REQUEST[FILE_SORT]==2) {//个人文件柜
           	  $map.=" and USER_ID='$LOGIN_USER_ID'";
           }else {
           	  $map.=" and USER_ID=''";
           }
           $count=$dao->count($map);
           if ($count) {//如果已经存在
           	 return false; //返回失败
           }else {
           	 return true;//返回成功
           }
		
	}	
	
	public function fun_edit(){
		$LOGIN_USER_ID=Session::get("LOGIN_USER_ID");
		$dao=D("FileSort");
		$row=$dao->where("SORT_ID='$_REQUEST[SORT_ID]'")->find();
		$SORT_PARENT=$row[SORT_PARENT];
		
        $map="SORT_NAME='$_REQUEST[SORT_NAME]' and SORT_PARENT='$SORT_PARENT' and SORT_ID!='$_REQUEST[SORT_ID]'";
           
           if ($_REQUEST[FILE_SORT]==2) {//个人文件柜
           	  $map.=" and USER_ID='$LOGIN_USER_ID'";
           }else {
           	  $map.=" and USER_ID=''";
           }
           $count=$dao->count($map);
           
           if ($count) {//如果已经存在
           	 return false; //返回失败
           }else {
           	 return true;//返回成功
           }
	}	
	
	/*	
	protected $_auto	 =	 array(
		array('OP','0','UPDATE'),
		);
		*/
}

?>