<?
class DepartmentModel extends BaseModel {
	
	var $_validate=array(
	        array("DEPT_NAME","require","部门名称不能为空"),
	        array('DEPT_NO','is_number','排序值必须是数字',1,'function')
	);
	
	protected function haveSub($deptid=0){
		$dao=D("Department");
		$map="DEPT_PARENT='$deptid'";
		$count=$dao->count($map);
		if ($count) {
			return true;
		}else {
			return false;
		}
		
	}
	
	public function DeptSelect($parentid=0){
		$dao=D("Department");
		$list=$dao->where("DEPT_PARENT='$parentid'")->findall();
		$str="";
		foreach ($list as $row){
			if ($this->haveSub($row[DEPT_ID])) {
				    $cls='class="subdown"';    
					$cls_title="<img src=\"/oa/Tpl/default/Public/images/bg_9.gif\" width=\"9\" height=\"9\" /><span id=\"D_".$row[DEPT_ID]."\">$row[DEPT_NAME]</span>"; 	
			}else {
					$cls='id="D_'.$row[DEPT_ID].'"';
					$cls_title=$row[DEPT_NAME];    
			} 
        	$str.='<li '.$cls.'>'.$cls_title;
			if ($this->haveSub($row[DEPT_ID])) {
					$str.='<ul style="display:none">';
			}
			$str.=$this->DeptSelect($row[DEPT_ID]);        		
			if ($this->haveSub($row[DEPT_ID])) {
				    $str.='</ul>';
			}
			$str.= '</li>';
		}
		return $str;
	}
####################返回当前部门id的所有父部门名称（js遍历人员相关信息那块用到）##################
	public function re_parent_deptname($dept_id=0){  
    	 
		$dao_d=D("Department");//按部门选择人员 
		$us=$dao_d->where("DEPT_ID=$dept_id")->find();
		if($us){
			$arr[].=$us[DEPT_NAME];
			$arr[].=$this->re_parent_deptname($us[DEPT_PARENT]);   
		}
		$arr_dept_name=implode(",",$arr);
        return $arr_dept_name;	
        
	}
####################返回当前部门id的所有父部门id(主页左上角搜索用)#################
	public function re_parent_deptid($dept_id=0){
		$dao_d=D("Department");
		$rpd=$dao_d->where("DEPT_ID='$dept_id'")->find();
		$arr[].=$dept_id;
		if($dept_id!=0){
		$arr[].=$this->re_parent_deptid($rpd[DEPT_PARENT]);
		}
		$arr_dept_id=implode(",",$arr);
		return $arr_dept_id;
	}
####################根据部门id递归出属于这个部门的所有人员USER_ID###########	
	public function re_user_id($dept_id=0){   
		$dao_u=D("User");//按个人选择人员 
		$dao_d=D("Department");//按部门选择人员 
		$u=$dao_u->where("DEPT_ID=$dept_id")->findall();
		$arr=array();
		if($u){
			foreach ($u as $v){
			$arr[]=$v[USER_ID];
			$d=$dao_d->where("DEPT_PARENT=$dept_id")->findall();
				foreach ($d as $e){
					$arr[]=$this->re_user_id($e[DEPT_ID]);
				}   
			}
		} 
		$arr_user_id=implode(",",$arr); 
        return $arr_user_id;	
        
	}
}
?>