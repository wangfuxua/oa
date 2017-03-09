<?php
/**********************************
 *       在线模块
 * author:廖秋虎  time:08/12/22
 **********************************/

class userOnlineAction extends PublicAction {
/************************在线入口程序********************************/
	var $count;	
	
	public function count(){
		/*
		//$user = new UserModel();
		//$time = time()-65;
		//$count = $user->count("LAST_VISIT_TIME > $time");
		*/
		$dao=D("UserSession");
		//$map="lastactivity>".(time()-$this->onlineTime);
		$map="";
		$count=$dao->count($map);
		return $count;
	}

	public function index(){
			####选择用户
		UserSelectAction::DeptUserSelectAllOnline();	
		$this->display();
	}
	public function refresh(){
		$dao=D("UserSession");
		$list=$dao->findall();
		echo json_encode($list);
	}
	public function IndexOld(){
		//---------------更新最后访问时间--------------
		$user = new UserModel();
		$curTime = $this->CUR_TIME_INT;
		$data = array('LAST_VISIT_TIME'=>$curTime);
		if(!empty($this->LOGIN_USER_ID))
			$user->save($data,"USER_ID='$this->LOGIN_USER_ID'");
		//----------------用户列表--------------------------
		$department = new DepartmentModel();
		$departmentList = $department->order('DEPT_ID')->findall();
		// id, pid, name, url, title, target, icon, iconOpen, open, cls
		$treeOnline = "online.add(0,-1,'','','','','','','','tree-root');";
		foreach ($department as $v){
			$treeOnline .= "online.add($v[DEPT_ID],$v[DEPT_PARENT],'$v[DEPT_NAME]','','$v[DEPT_NAME]','','','','','oTree-bg');";
			$userList = $user->where("DEPT_ID=$v[DEPT_ID]")->order('LAST_VISIT_TIME desc')->findAll();
			if($userList){
				foreach ($userList as $v2){
					if($curTime-$v2['LAST_VISIT_TIME']<65){
					$treeOnline .="online.add('$v2[USER_ID]',$v2[DEPT_ID],'$v2[USER_NAME]','javascript:window.parent.openChat2($v2[uid],\'$v2[USER_ID]\',\'/oa/tpl/default/public/img/noface.gif\')','','', '/oa/tpl/default/public/images/ico/user$v2[SEX].png','','','');";
					}else{
					$treeOnline .="online.add('$v2[USER_ID]',$v2[DEPT_ID],'$v2[USER_NAME]','javascript:window.parent.openChat2($v2[uid],\'$v2[USER_ID]\',\'/oa/tpl/default/public/img/noface.gif\')','','', '/oa/tpl/default/public/images/ico/userleave.png','','','');";	
					}
				}
			}
		}
		$this->assign('count',$count);
		$this->assign('treeOnline',$treeOnline);
		$this->display();
	}
	//--递归求解完整的多级部门名称--
	public function dept_long_name($deptId){
		$department = new DepartmentModel();
		$row = $department->where("DEPT_ID=$deptId")->find();
		if($row){
			$deptName=$row['DEPT_NAME'];
			$deptParent=$row['DEPT_PARENT'];
			if($deptParent==0)
				return $deptName;
			else 
				return dept_long_name($deptParent)."/".$deptName;
		}
	}
	public function find_id($STRING,$ID){
		$MY_ARRAY=explode(",",$STRING);
   		$ARRAY_COUNT=sizeof($MY_ARRAY);
   		if($MY_ARRAY[$ARRAY_COUNT-1]=="")$ARRAY_COUNT--;
   		for($I=0;$I<$ARRAY_COUNT;$I++){ 
   			if(strcmp($MY_ARRAY[$I],$ID)==0||$MY_ARRAY[$I]==$ID)return true;
   		}
   		return false;
	}
}
?>