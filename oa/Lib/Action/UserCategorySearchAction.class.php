<?php
import("@.Util.userselect");
class UserCategorySearchAction extends PublicAction {
	/**用户选择列表*/
	public function userSelect(){
		$one = isset($_GET['one_id']) ? $_GET['one_id'] : '';
		$id = isset($_GET['ids_id']) ? $_GET['ids_id'] : '';
		$name = isset($_GET['names_id']) ? $_GET['names_id'] : '';
		$this->assign('one',$one);
		$this->assign('ids',$id);
		$this->assign('names',$name);
		$this->display();
	}
	
	public function menuControl(){
		//==================获得部门列表===========================
		$row = PublicAction::_unit();
		if($row['UNIT_NAME']){
			$this->assign('row',$row);
		}
        $dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$PRIV_NO_FLAG=$_REQUEST[PRIV_NO_FLAG]=1;
		if ($PRIV_NO_FLAG) {
			$dao=D("UserPriv");
			$row=$dao->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
			$PRIV_NO=$row[PRIV_NO];
		}
		if($user[POST_PRIV]=="1")
		    $DEPT_PRIV="全体";
		elseif($user[POST_PRIV]=="2")
		    $DEPT_PRIV="指定部门";
		else{
			$dao=D("Department");
			$row=$dao->where("DEPT_ID=$this->LOGIN_DEPT_ID")->find();
		    $DEPT_PRIV=$row["DEPT_NAME"];
		 }
		$PRIV_NO_FLAG=$_REQUEST['PRIV_NO_FLAG']=1;
				
		import("@.Util.userselect");
		$userselect=new userselect();
		
		$deptlist=$userselect->_DeptTree(0,$PRIV_NO_FLAG,$PRIV_NO); 		
		$this->assign("deptlist",$deptlist);			


		//==================获得角色列表===========================
		$role = D('UserPriv');
		$role_list = $role->findAll();
		$this->assign('role_list',$role_list);
		$this->display();	
	}
	
	public function userControl(){
		$user_res = $online_arr = array();
		$user_session = D('UserSession');
		$user_session_res = $user_session->field('userid')->findAll();
		for($i=0;$i<count($user_session_res);$i++){
			foreach ($user_session_res[$i] as $key => $value){
				array_push($online_arr, $value);
			}
		}
		//print_r($user_session_res);exit;
		if($_GET['DEPT_ID']){ // 根据部门选择
			$id = $_GET['DEPT_ID'];
			$dept = D('Department');
			$dept_name = $dept->field('DEPT_NAME')->find($id);
			$user = D('User');
			$user_res = $user->where("DEPT_ID=$id")->findAll();
			$category_name = $dept_name['DEPT_NAME'];
		}
		if($_GET['role_id']){ // 根据角色选择
			$id = $_GET['role_id'];
			$role = D('UserPriv');
			$role_name = $role->field('PRIV_NAME')->find($id);
			$user = D('User');
			$user_res = $user->where("USER_PRIV=$id")->findAll();
			$category_name = $role_name['PRIV_NAME'];
		}
		if($_GET['online']){ // 获取在线用户
			if($user_session_res){
				for($i=0;$i<count($user_session_res);$i++){
					$user = D('User');
					$user_res[$i] = $user->field('uid,USER_NAME,DEPT_ID')->where("USER_ID='".$user_session_res[$i]['userid']."'")->find();
					$dept = D('Department');
					$dept_res = $dept->field('DEPT_NAME')->find($user_res[$i]['DEPT_ID']);
					$user_res[$i]['department'] = $dept_res['DEPT_NAME'];
				}
			}
			$category_name = '在线人员';
		}
		if($_GET['keywords']){ // 查询
			$category_name = '人员查询';
			$keywords = $_GET['keywords'];
			$user = D('User');
			$user_res = $user->where("USER_NAME like '%".$keywords."%' or USER_ID like '%".$keywords."%'")->findAll();
		}
		if($_GET['selected']){ // 已选人员
			$category_name = '已选人员';
		}
		$ids_string = '';
		if($user_res){ 
			for($i=0;$i<count($user_res);$i++){
				$user_res[$i]['login'] = (in_array($user_res[$i]['USER_ID'],$online_arr)) ? 1 : -1;
				$ids_string .= $user_res[$i]['uid'].",";
			}
		}
		//print_r($user_res);exit;
		$this->assign('category_name',$category_name);
		$this->assign('ids_string',$ids_string);
		$this->assign('user_res',$user_res);
		$this->display();
	}
	
	public function closeControl(){
		$this->display();
	}
	public function test(){
		$this->display();
	}		
}
?>