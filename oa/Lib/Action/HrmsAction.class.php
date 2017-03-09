<?php
/**********************************
 *       人事档案管理模块
 * author:廖秋虎  time:08/12/15
 **********************************/
import("@.Util.userselect");
class hrmsAction extends WorkFormAction {
/********************入口程序************************/
	public function index(){
		$department = new DepartmentModel();
		$departmentList = $department->order('DEPT_ID')->findall();
		$user = new Model();
    	
		$dao=D("UserPriv");
    	$userpriv=$dao->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
    	$PRIV_NO=$userpriv["PRIV_NO"];
		if($this->LOGIN_USER_PRIV!="1"){
		    $map2="user.USER_PRIV=user_priv.USER_PRIV and user_priv.PRIV_NO>$PRIV_NO and user_priv.USER_PRIV!=1";
		 }else{
		 	$map2="user.USER_PRIV=user_priv.USER_PRIV ";
		 } 
        $order2="PRIV_NO,USER_NAME";
        $table2="user,user_priv";
		$treeoutuser="";
		$userList=$user->table($table2)
   	                   ->where($map2." AND (user.DEPT_ID='0'||user.DEPT_ID='')")
		    	       ->order($order2)
		    	       ->findall();
		foreach ($userList as $v2){
			$treeoutuser .="online.add('$v2[USER_ID]',1,'$v2[USER_NAME]','/index.php/Hrms/form/USER_ID/$v2[USER_ID]','','hrms','/oa/Tpl/default/Public/images/ico/user.png','','','');"	;
		}
		$this->assign("treeoutuser",$treeoutuser);
		
				###获取用户基本信息（管理范围）
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
		 //echo $DEPT_PRIV;
		$PRIV_NO_FLAG=$_REQUEST['PRIV_NO_FLAG']=1;
		
		
		$userselect=new userselect();
		$deptlist=$userselect->_DeptUserTreeAll(0); 
		$this->assign("deptlist",$deptlist);
		
		
		$this->display();
		}
/********************提示选择员工************************/
	public function blank(){
		$this->display();
	}
/********************内容程序************************/
	public function form(){
		$hrms = D('Hrms');
		$user = D('User');
		if(empty($_GET['USER_ID'])){
			$hrmsRow = $hrms->where("USER_ID='$this->LOGIN_USER_ID'")->find();
			$userRow = $user->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		}else{
			$hrmsRow = $hrms->where("USER_ID='$_GET[USER_ID]'")->find();
			$userRow = $user->where("USER_ID='$_GET[USER_ID]'")->find();
		}
		if ($hrmsRow[USER_ID]) {
           	$OP=2;
            $STATUS="已建档";
		}else {
			$OP=1;
			$STATUS="未建档";					
		}
		if ($hrmsRow[PHOTO]) {
           	$hrmsRow[PHOTO]="/".$this->uploadpath.$hrmsRow[PHOTO];				
		}
		//$hrmsField = D('HrmsField');
		$hrmsField = new HrmsFieldModel();
		$hrmsList = $hrmsField->where("state=2")->order('`order`')->findAll();
		foreach ($hrmsList as $k=>$v){
			$hrmsList[$k][form]=$this->typeForm($v['formtype'],$v['fieldname'],$v['setting'],$hrmsRow[$v['fieldname']]);
		}
		$this->assign('hrmsList',$hrmsList);
		$this->assign("OP",$OP);
		$this->assign("STATUS",$STATUS);	
		$this->assign('hrmsRow',$hrmsRow);
		$this->assign('userRow',$userRow);
		$this->display();
	}
				
/******************内容更新程序*********************/
	public function update(){
		$hrms = new HrmsModel();
		if($_FILES[ATTACHMENT][name]){
			$arr=$hrms->fileinfo_save(array('maxSize' => 1024*1024*2, 
						'allowExts' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'), 
						'allowTypes' => array('image/pjpeg', 'image/gif', 'image/x-png', 'image/bmp')));
			$_POST["PHOTO"]=$arr[attachment];
		}
		if($hrms->create()){
			if ($_POST[OP]==2) {
			   $hrms->where("USER_ID='$_POST[USER_ID]'")->save();	
			}elseif ($_POST[OP]==1){
				$hrms->add();	
			}
			$this->assign("jumpUrl",__URL__."/form/USER_ID/$_POST[USER_ID]"); 
			$this->success('更新成功!');
		}
	}
		
		
	/*------------------员工档案查询-------------*/
		public function query(){
		$department = new DepartmentModel();
		$departmentList = $department->order('DEPT_ID')->findall();
		$user = new Model();
    	
		$dao=D("UserPriv");
    	$userpriv=$dao->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
    	$PRIV_NO=$userpriv["PRIV_NO"];
		if($this->LOGIN_USER_PRIV!="1"){
		    $map2="user.USER_PRIV=user_priv.USER_PRIV and user_priv.PRIV_NO>$PRIV_NO and user_priv.USER_PRIV!=1";
		 }else{
		 	$map2="user.USER_PRIV=user_priv.USER_PRIV ";
		 } 
        $order2="PRIV_NO,USER_NAME";
        $table2="user,user_priv";
		$treeoutuser="";
		$userList=$user->table($table2)
   	                   ->where($map2." AND (user.DEPT_ID='0'||user.DEPT_ID='')")
		    	       ->order($order2)
		    	       ->findall();
		//echo $user->getLastSql();    	       
		foreach ($userList as $v2){
			$treeoutuser .="online.add('$v2[USER_ID]',1,'$v2[USER_NAME]','/index.php/Hrms/view/USER_ID/$v2[USER_ID]','','hrms','/oa/Tpl/default/Public/images/ico/user.png','','','');"	;
		}
		$this->assign("treeoutuser",$treeoutuser);
		

		
				###获取用户基本信息（管理范围）
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
		 //echo $DEPT_PRIV;
		$PRIV_NO_FLAG=$_REQUEST['PRIV_NO_FLAG']=1;
		
		
		$userselect=new userselect();
		$deptlist=$userselect->_DeptUserTreeAll(0); 
		$this->assign("deptlist",$deptlist);
				
		$this->display();
		}
	/********************员工档案显示************************/
		public function view(){
			$hrms = new HrmsModel();
			$user = new UserModel();
			if(empty($_GET['USER_ID'])){
				$hrmsRow = $hrms->where("USER_ID='$this->LOGIN_USER_ID'")->find();
				$userRow = $user->where("USER_ID='$this->LOGIN_USER_ID'")->find();
			}else{
				$hrmsRow = $hrms->where("USER_ID='$_GET[USER_ID]'")->find();
				$userRow = $user->where("USER_ID='$_GET[USER_ID]'")->find();
			}
				if ($hrmsRow[USER_ID]) {
                   $OP=2;
                   $STATUS="已建档";
				}else {
                   $OP=1;
                   $STATUS="未建档";					
				}
				if ($hrmsRow[PHOTO]) {
                   $hrmsRow[PHOTO]="/".$this->uploadpath.$hrmsRow[PHOTO];					
				}
				$hrmsField = new HrmsFieldModel();
				$hrmsList = $hrmsField->where("state=2")->order('`order`')->findAll();
				foreach ($hrmsList as $k=>$v){
					if($v['formtype']=='box'){
						$setting = unserialize($v['setting']);
						$options = $this->option($setting['options']);
						$hrmsList[$k][value] = $options[$hrmsRow[$v['fieldname']]];
					}else{
					$hrmsList[$k][value]=$hrmsRow[$v['fieldname']];}
				}
				$this->assign('hrmsList',$hrmsList);
				$this->assign("OP",$OP);
				$this->assign("STATUS",$STATUS);
				$this->assign('hrmsRow',$hrmsRow);
				$this->assign('userRow',$userRow); 
				$this->display();
			
		}
				
		
	/*****************转化为时间戳********************/
		protected function maketime($time){
			$time = strtotime($time);
			return $time;
		}
	/****************转化为时间*******************/
		protected function retime($time){
			$times = date("Y-m-d",$time);
			return $times;
		}
	}
?>
