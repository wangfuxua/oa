<?php
/**********************************
 *       工资上报模块
 * author:廖秋虎  time:08/12/17
 **********************************/
import("@.Util.userselect");
class salaryNoteAction extends salaryAction {
	
/************************工资上报入口程序********************************/
	public function index(){
		$curDate = $this->CUR_TIME_INT;
		$salFlow = new SalFlowModel();
		$salList = $salFlow->where("BEGIN_DATE<=$curDate and END_DATE>=$curDate")->order('BEGIN_DATE')->findall();
		$this->assign('salList',$salList);	
		$this->display();
	}
/************************工资上报框架程序********************************/
	public function salIndex(){
		###获取用户基本信息（管理范围）
		$dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		
		$PRIV_NO_FLAG=$_REQUEST[PRIV_NO_FLAG]=0;
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
		 
		$PRIV_NO_FLAG=$_REQUEST['PRIV_NO_FLAG']=0;
		
		
		$userselect=new userselect();
		$deptlist=$userselect->_DeptUserTreeAll(0); 
		
		$this->assign("deptlist",$deptlist);
		
		$this->assign('FLOW_ID',$_GET['FLOW_ID']);
		$this->display();
	}
/************************工资上报右边框架程序********************************/
	public function blank(){
		echo $_GET['USER_ID'];
		$this->display();
	}

/************************工资上报数据程序********************************/
	public function salData(){
		//工资单
		$salData = new salDataModel();
		$salDataRow= $salData->where("FLOW_ID=$_GET[FLOW_ID] and USER_ID='$_GET[USER_ID]'")->find();

		//用户信息	
		$user = new UserModel();		
		$userRow = $user->where("USER_ID='$_GET[USER_ID]'")->find();
		$this->assign('userRow',$userRow);
		//流程信息
		$this->assign('flowId',$_GET['FLOW_ID']);
		//工资类型信息
		$salItem = new salItemModel();
		$salItemList = $salItem->order("ITEM_ORDER asc")->findAll();
		//工资单
	
		foreach($salItemList as $k=>$v){
			$salItemList[$k]['s']=$salDataRow['s'.$v['ITEM_ID']];
		}
		$this->assign('salItemList',$salItemList);
		
		$this->display();
	}
/***********************工资上报数据*********************/
	public function updateData(){
		$salItem = new salItemModel();
		$salItemList = $salItem->order("ITEM_ORDER asc")->findAll();
		//print_r($salItemList);
		foreach($salItemList as $k=>$v){
			//echo 's'.$v['ITEM_ID'];
			//echo $_REQUEST['s'.$v['ITEM_ID']];exit;
			
			if (isset($_REQUEST['s'.$v['ITEM_ID']])) {
			    if (!is_numeric($_REQUEST['s'.$v['ITEM_ID']])) {
			    	 $this->error($v['ITEM_NAME']."必须为数字");	
			    }
			}
			//$salItemList[$k]['s']=$salDataRow['s'.$v['ITEM_ID']];
		}
						
		$salData = new salDataModel();
		$map="FLOW_ID='$_POST[FLOW_ID]' and USER_ID='$_POST[USER_ID]'";
		$count=$salData->count($map);
		//$a = $salData->where()->find();
		
		if(!$count){
			$salData->create();
			$salData->add();
			//echo $salData->getLastSql();
			//exit();
			$this->success("成功上报");
			
		}else{
			$salData->create();
			$salData->where($map)->save();
			//echo $salData->getLastSql();
			//exit();
			$this->success("成功修改上报数据");
			//echo $salData->getLastSql();
		}
		//print_r($_POST);
	}
	/************************查看$deptId是否属于本人管理范围**********方法类**************************/
	public function is_dept_priv($deptId){
		$user=new UserModel();
		$user=$user->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$postPriv=$user['POST_PRIV'];
		$postDept=$user['POST_DEPT'];
		if($postPriv==0 && $deptId!=$this->LOGIN_USER_ID && !$this->is_dept_parent($deptId,$this->LOGIN_DEPT_ID))
			$deptPriv=0;
		else if($postPriv==2){
			$deptPriv=0;
			
			$myArray=explode(",",$postDept);
			$arrayCount=sizeof($myArray);
			if($myArray[$arrayCount-1]=="")
				$arrayCount--;
			for($i=0;$i<$arrayCount;$i++){
				if($this->is_dept_parent($deptId,$myArray[$i])||$myArray[$i]==$deptId){
					$deptPriv=1;
					break;
				}
			}
		}
		else 
			$deptPriv =1;
		return $deptPriv;
	}
	/************************递归求解PARENT_ID是否是deptId的父节点*********方法类****************************/
	public function is_dept_parent($deptId,$parentId){
		$department = new DepartmentModel();
		$department = $department->where("DEPT_ID='$deptId'")->find();
		$deptParent = $department['DEPT_PARENT'];
		if($deptParent==0)
			return 0;
		else if ($deptParent==$parentId)
			return 1;
		else 
			return $this->is_dept_parent($deptParent,$parentId);
		
	}
	/************************多级部门下拉菜单，支持按管理范围列出*********方法类****************************/
	public function my_dept_tree($deptId,$deptChoose,$postOP){
		//$deepCount = $_GET['deepCount'];
		$deepCount = (empty($deepCount))? '|':$deepCount;
		$deptParents = new DepartmentModel();
		$deptParents = $deptParents->where("DEPT_PARENT='$deptId'")->order('DEPT_NO')->findAll();
		$optionText = "";
		$deepCount1 = $deepCount;
		$deepCount.=" |";
		if($deptParents)
			foreach ($deptParents as $k=>$v){
				$deptId = $v['DEPT_ID'];
				$deptName = $v['DEPT_NAME'];
				$deptParent = $v['DEPT_PARENT'];
				if($postOP==1)
					$deptPriv=$this->is_dept_priv($deptId);
				else 
					$deptPriv=1;
				
				$optionTextChild=$this->my_dept_tree($deptId,$deptChoose,$postOP);
			
				if($deptPriv==1){
					$optionText.="<option ";
					if($deptId==$deptChoose)
						$optionText.="selected";
					$optionText.="value=$deptId>".$deepCount1."--".$deptName."</option>\n";				
				}
				if($optionTextChild!="")
					$optionText.=$optionTextChild;
			}
		$deepCount=$deepCount1;
		return $optionText;
	}
	public function format_money($str){
		if($str=="")
			return "";
		if($str==".00")
			return "0,00";
		$tok=strtok($str,".");
		if(strcmp($str,$tok)=="0")
			$str.=".00";
		else{
			$tok=strtok(".");
			for($i=1;$i<=2-strlen($tok);$i++)
				$str.="0";
		}
		if(substr($str,0,1)==".")
			$str="0".$str;
		return $str;
	}
}
?>