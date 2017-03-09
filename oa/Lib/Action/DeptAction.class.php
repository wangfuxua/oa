<?php
/**********************************
 *       部门管理模块
 * author:廖秋虎  time:08/11/20
 **********************************/
import("@.Util.tree");
class DeptAction extends PublicAction {
/*****************************入口程序*************************************************/
	public function index(){
		$row = PublicAction::_unit();
		if($row['UNIT_NAME']){
			$this->assign('row',$row);
		}
		/*
		$dept = new DepartmentModel();
		if($this->LOGIN_USER_PRIV!="1"){
		    $map="DEPT_ID='$this->LOGIN_DEPT_ID' or DEPT_PARENT = '$this->LOGIN_DEPT_ID'";//只有两级
		 }else{
		 	$map="";
		 }
		$list = $dept->where($map)->order("DEPT_NO")->findAll();

		//echo $dept->getLastSql();
		//==================获得部门列表===========================

		$treeOnline = "";
		foreach ($list as $v){
			$treeOnline .="online.add($v[DEPT_ID],$v[DEPT_PARENT],'$v[DEPT_NAME]','deptEdit/DEPT_ID/$v[DEPT_ID]','dept','blank','/oa/tpl/default/public/images/ico/group.png','/oa/tpl/default/public/images/ico/group.png','','');";
		}
		$this->assign('treeOnline',$treeOnline);
		*/
		
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
				
		import("@.Util.userselect");
		$userselect=new userselect();
		
	//	if($this->LOGIN_USER_PRIV!="1" && $PRIV_NO_FLAG)
		//$deptlist=$userselect->_DeptTree($this->LOGIN_DEPT_ID,$PRIV_NO_FLAG,$PRIV_NO); 
		//else 
		$deptlist=$userselect->_DeptTree(0,$PRIV_NO_FLAG,$PRIV_NO); 		
		
		//print_r($deptlist);
		
		$this->assign("deptlist",$deptlist);
		
		$this->display();
	}
/*****************************添加新部门程序**********************************************/
	public function add(){
		$dept = new DepartmentModel();
		if(false === $dept->create()) {
        	$this->error($dept->getError());
        }
			$dept->add();
			$this->success('上传成功');
	}
/*****************************添加新部门程序**********************************************/
	public function blank(){
		$dept = new DepartmentModel();
		/*
		$deptList = $dept->findall();
		$this->assign('deptList',$deptList);
		*/
		
		$my_dept_tree=my_dept_tree(0,$dept_id,0);
		$this->assign("my_dept_tree",$my_dept_tree);
		$this->display();
	}
/*****************************修改部门信息程序**********************************************/	
	public function deptEdit(){
		$dept = new DepartmentModel();
		$deptRow = $dept->where("DEPT_ID='$_GET[DEPT_ID]'")->find();
		$this->assign('deptRow',$deptRow);
		//$deptList = $dept->where("DEPT_ID!='$_GET[DEPT_ID]'")->findall();
		//$this->assign('deptList',$deptList);
		
		$DEPT_STR=my_dept_tree(0,$deptRow[DEPT_PARENT],0);
		/*
		$POS1=strpos($DEPT_STR,"<option value=$_GET[DEPT_ID]>");
		$POS2=strpos($DEPT_STR,"</option>",$POS1);
		$DEPT_STR1=substr($DEPT_STR,$POS1,$POS2-$POS1+9);
		$my_dept_tree=str_replace($DEPT_STR1,"",$DEPT_STR);
		*/
		$this->assign("my_dept_tree",$DEPT_STR);
		$this->display();
	}
/*****************************数据更新程序*************************************************/
	public function update(){
		$dept = new DepartmentModel();
		$DEPT_PARENT=$_REQUEST[DEPT_PARENT];
		$DEPT_ID=$_REQUEST[DEPT_ID];
        if ($DEPT_ID==$DEPT_PARENT) {
			$this->error("不能选择本部门做为上级部门");
		}
		
		$subs=getSubDepts($DEPT_ID);
				
		if (find_id($subs,$DEPT_PARENT)) {
			$this->error("不能选择子部门做为本部门的上级部门");
		}
		if (false===$dept->create()) {
			$this->error($dept->getError());
		}
		
			$dept->where("DEPT_ID='$_POST[DEPT_ID]'")->save();
			//echo $dept->getLastSql();exit;
			$this->success('更新成功!');
	}
/*---------------删除部门-----------*/	
	public function delete(){
		$DEPT_ID=$_REQUEST[DEPT_ID];
		
	    $dept=D("Department");
	    $map="DEPT_PARENT='$DEPT_ID'";
	    $count=$dept->count($map);
	    if ($count>0) {
	    	$this->error("请先删除下级部门");
	    }
	    
	    $dao=D("User");
	    $map="DEPT_ID='$DEPT_ID'";
	    $count=$dao->count($map);
	    if ($count>0) {
	    	$this->error("请先删除部门里的成员");
	    }
	    
	    $dept->where("DEPT_ID='$DEPT_ID'")->delete();
	    
	    $this->assign("jumpUrl",__URL__."/blank");
	    $this->success("成功删除");
	    	
	}
/*****************************多级部门下拉菜单**********方法********************************/
	public function deptTree($dept_id,$dept_choose,$post_op){
		$dept = new DepartmentModel();
		$result = $dept->where('DEPT_PARENT='.$dept_id)->findAll();
		   foreach ($result as $k=>$v){
		   	echo $v['DEPT_ID'].$v['DEPT_NAME'].$v['DEPT_PARENT'];
		}
	}
/********************************菜单列表**************公用*****************************/
	public function deptSelect(){
		$this->display();		
	}		
}
?>
