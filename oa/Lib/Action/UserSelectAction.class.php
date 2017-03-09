<?php
//import("@.Action.PublicAction");
//import("@.Util.xmltree");
import("@.Util.userselect");
class UserSelectAction extends PublicAction {
        var $PARA_URL="/index.php/userselect/user";
		var $PARA_TARGET="user";
		var $PARA_ID="ID";
		var $PARA_VALUE="";
        var $UNIT_NAME=""; 
		var $PRIV_NO_FLAG=0;
		
		var $e="";
		var $ID;
		var $DEPT_ID;
		var $USER_PRIV;
		VAR $POST_PRIV;
		VAR $POST_DEPT;
	function _initialize(){
		$this->curtitle="选择人员";
        $this->assign("curtitle",$this->curtitle);
        
	    $units=$this->_unit();
		$this->UNIT_NAME=$units["UNIT_NAME"];
		
        $this->PARA_VALUE=$_GET[ID];
        
        if($_GET[e]=="")
           $_GET[e]=0;
           
        $this->e=$_GET[e];
        $this->ID=$_GET[ID];
		$this->DEPT_ID=$_GET[DEPT_ID];
		$this->USER_PRIV=$_GET[USER_PRIV];
		$this->POST_PRIV=$_GET[POST_PRIV];
		$this->POST_DEPT=$_GET[POST_DEPT];
		
		parent::_initialize();
	}
	
	public function index(){
		/*
		$unit=$this->_unit();
	  	$unit_name=$unit[UNIT_NAME];
   	  $dao=D("User");
   	  $map="USER_ID='$_SESSION[LOGIN_USER_ID]'";
   	  $priv=$dao->find($map,'*');
   	  */	  	
		$this->display();
	}
	public function dept(){
		//$ID=$_GET[ID];
		//$DEPT_ID=$_GET[DEPT_ID];
		//$USER_PRIV=$_GET[USER_PRIV];
				
		$XML_TEXT="<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		$XML_TEXT.="<TREE name=\"$this->UNIT_NAME\">";
		$XML_TEXT.=my_xml_tree(0);
		$XML_TEXT.="</TREE>";
		//echo $XML_TEXT;         
		$tree = new tree();
		$tree->ROOT_IMAGE="/oa/Tpl/default/Public/images/menu/system.gif";
		$tree->PARA_ID=$this->PARA_ID;
		$tree->PARA_VALUE=$this->PARA_VALUE;
		$html = $tree->makeTreeText($XML_TEXT,$this->e);
		$this->assign("xmldept",$html);
		

		 $dao=D('user_priv');
		 $list=$dao->order('PRIV_NO')->findall();

		 $ID_STR=$USER_PRIV_STR=$POST_PRIV_STR=$POST_DEPT_STR="";
		 if ($this->ID){
		 	$ID_STR="/ID/".$this->ID;
		 }
		 if ($this->USER_PRIV){
		 	$USER_PRIV_STR="/USER_PRIV/".$this->USER_PRIV;
		 }
		 if ($this->POST_PRIV){
		 	$POST_PRIV_STR="/POST_PRIV/".$this->POST_PRIV;
		 }
		 if ($this->POST_DEPT){
		 	$POST_DEPT_STR="/POST_DEPT/".$this->POST_DEPT;
		 }
		 
		 $this->assign("ID",$this->ID); 
		 
		 $this->assign("ID_STR",$ID_STR); 
		// $this->assign("USER_PRIV_STR",$USER_PRIV_STR);
		 $this->assign("POST_PRIV_STR",$POST_PRIV_STR);
		 $this->assign("POST_DEPT_STR",$POST_DEPT_STR);
		 $this->assign("PARA_URL",$this->PARA_URL);
		 $this->assign("list",$list); 
		
		$this->display();
	}
	
	public function user(){
		$ID=$_GET[ID];//无用
		
		$DEPT_ID=$_GET[DEPT_ID];
		$USER_PRIV=$_GET[USER_PRIV];
		
		$POST_PRIV=$_GET[POST_PRIV];//现在未用到2008-11-21
		$POST_DEPT=$_GET[POST_DEPT];//现在未用到2008-11-21
		//echo $ID;
		if($DEPT_ID=="")
           $DEPT_ID=Session::get('LOGIN_DEPT_ID');
          //echo $DEPT_ID;
		 if($USER_PRIV=="")
		 {
		 	
		    //$query = "SELECT * from USER,USER_PRIV where DEPT_ID=$DEPT_ID and USER.USER_PRIV=USER_PRIV.USER_PRIV order by PRIV_NO,USER_NAME";
		    $dao = new Model();
		    $list = $dao->table("user as user")
		                ->join("user_priv as user_priv on user.USER_PRIV=user_priv.USER_PRIV")            
		                ->field("user.*,user_priv.*")
		                ->where("user.DEPT_ID='$DEPT_ID'")
		                ->order("user_priv.PRIV_NO,user.USER_NAME")
		                ->findAll();
		    //echo  $dao->getLastSql(); 
		    //print_r($list);            
		    $dao=D("Department");
		    $ROW=$dao->where("DEPT_ID='$DEPT_ID'")
		              ->find();
		    $TITLE=$ROW["DEPT_NAME"];
		 }
		 else
		 {
		    //$query = "SELECT * from USER where USER_PRIV='$USER_PRIV' and DEPT_ID!=0 order by USER_NAME";
		    $dao=D("User");
		    $list=$dao->where("USER_PRIV='$USER_PRIV' and DEPT_ID!=0 ")
		              ->order("USER_NAME") 
		              ->findall();
		              
		    $dao=D('user_priv');
		    $ROW=$dao->where("USER_PRIV='$USER_PRIV'")
		              ->find();
		    $TITLE=$ROW["PRIV_NAME"];
		 }
		 //echo $dao->getlastsql();
if($ID==1)
{
   $TO_ID="SECRET_TO_ID";
   $TO_NAME="SECRET_TO_NAME";
}
elseif($ID==2)
{
   $TO_ID="COPY_TO_ID";
   $TO_NAME="COPY_TO_NAME";
}
else
{
   $TO_ID="TO_ID";
   $TO_NAME="TO_NAME";
}
         $this->assign("TO_ID",$TO_ID); 
         $this->assign("TO_NAME",$TO_NAME);
        $this->assign("TITLE",$TITLE);
        $this->assign("list",$list);
         		
		$this->display();
	}

	public function UserOnline(){
			$dao=D("User");
		    $list=$dao->where("")
		              ->order("USER_NAME") 
		              ->findall();
		  $this->assign("list",$list);           
		 $this->display('user');
	}
	
	
	public function DeptUserSelect(){
		$PRIV_NO_FLAG=$PRIV_NO=0;
		if ($PRIV_NO_FLAG) {
			$dao=D("User");
			$user=$dao->where("USER_PRIV=$this->LOGIN_USER_PRIV");
			$PRIV_NO=$user["PRIV_NO"];
		}
		
		$userselect=new userselect();
		$deptuserlist=$userselect->_DeptUserTree(0,$PRIV_NO_FLAG,$PRIV_NO); 
		$this->assign("deptuserlist",$deptuserlist);
	}
	
	public function DeptUserSelectAll(){
		$PRIV_NO_FLAG=$PRIV_NO=0;
		if ($PRIV_NO_FLAG) {
			$dao=D("User");
			$user=$dao->where("USER_PRIV=$this->LOGIN_USER_PRIV");
			$PRIV_NO=$user["PRIV_NO"];
		}
		
		$userselect=new userselect();
		$deptuserlist=$userselect->_DeptUserTreeAll(0); 
		$this->assign("deptuserlist",$deptuserlist);
	}
	
	public function DeptUserSelectAllOnline(){
		$PRIV_NO_FLAG=$PRIV_NO=0;
		if ($PRIV_NO_FLAG) {
			$dao=D("User");
			$user=$dao->where("USER_PRIV=$this->LOGIN_USER_PRIV");
			$PRIV_NO=$user["PRIV_NO"];
		}
		
		$userselect=new userselect();
		$deptuserlist=$userselect->_DeptUserTreeAllOnline(0); 
		$this->assign("deptuserlist",$deptuserlist);
	}
			
	public function DeptSelect($list=array()){
		/*
		$PRIV_NO_FLAG=$PRIV_NO=0;
		if ($PRIV_NO_FLAG) {
			$dao=D("User");
			$user=$dao->where("USER_PRIV=$this->LOGIN_USER_PRIV");
			$PRIV_NO=$user["PRIV_NO"];
		}
		
		$userselect=new userselect();
		$deptlist=$userselect->_DeptTree(0,$PRIV_NO_FLAG,$PRIV_NO); 
		$this->assign("deptlist",$deptlist);
		*/
		
		$dao=D("Unit");
		$row=$dao->find();
		$this->assign("_UNIT",$row[UNIT_NAME]);
		
		$dao=D("Department");
        $str=$dao->DeptSelect(0);
        
        $this->assign("pub_deptstr",$str);
        $dao=D("UserPriv");
        $str=$dao->PrivSelect();
        $this->assign("pub_privlist",$str);        
        $dao=D("User");
        $str=$dao->UserSelect();
        $this->assign("pub_userlist",$str);
		
        $this->assign("pub_selected",$list);
	}
	
	protected function haveSubDept(){
        		
		
	}
	protected function getSubDepts(){
		
		
	}
	public function DeptSelectNew(){//包含部门角色人员三类
		$dao=D("Department");
		
		
		
	}
		
}


?>