<?php
/*
功能：公共文件柜设置
*/
class FileSysAction extends PublicAction {
	function _initialize(){
		$this->assign("curtitle","公共文件柜设置");
		parent::_initialize();
	}
    /*------------------入口-管理文件夹-------*/
	public  function index(){  
		$type=$_REQUEST[type];
		$SORT_ID=$_REQUEST[SORT_ID];  
		###文件夹基本信息
		$dao=D("FileSort");
		$sort=$dao->where("SORT_ID='$SORT_ID'")->find();  
		$this->assign("sort",$sort); 
		$list=$dao->where("SORT_TYPE!='4' and SORT_PARENT=0")
		          ->order("SORT_TYPE,DEPT_ID,SORT_NAME")
		          ->findall();   
		$count=0;         
		if (!empty($list)) {
		   	$count=count($list);//判断有多少个文件夹 
		}            
		$this->assign("count",$count);
		$this->assign("list",$list); 
		$this->assign("SORT_ID",$SORT_ID);
		$this->assign("type",$type); 	
		$this->display();
	}
	/*---------------添加或修改文件夹--表单-----------*/
	public function form(){
		$SORT_ID=$_REQUEST[SORT_ID]; 
		$type=$_REQUEST[type]; 
		$dao=D("FileSort"); 
		if ($SORT_ID) { 
			$row=$dao->where("SORT_ID='$SORT_ID'")->find();
		}else {
			$row=array();
		} 
		$sort=$dao->where("SORT_ID='$SORT_ID'")->find();  
		$list=$dao->where("SORT_TYPE!='4' and SORT_PARENT=0")
		          ->order("SORT_TYPE,DEPT_ID,SORT_NAME")
		          ->findall();
		#########弹出框开始#########
		UserSelectAction::DeptSelect();
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
		
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束#########           
		###########权限设置##########           
		UserSelectAction::DeptSelect();
		//弹出框左侧选项 
		$list1=$dao_d->DeptSelect();
		$dao_p=D("UserPriv");//按角色选择人员
		$list2=$dao_p->findall();
		$dao_u=D("User");//按个人选择人员
		$list3=$dao_u->findall();
		//弹出框右侧选项 
		$a = UserAction::find_allname($row['MANAGE_USER']);
		$b = UserAction::find_allname($row['NEW_USER']);
		$c = UserAction::find_allname($row['DOWN_USER']);
		$_MANAGE_USER=$this->foreachmember("manage_user",$SORT_ID); 
		$_NEW_USER=$this->foreachmember("new_user",$SORT_ID); 
		$_DOWN_USER=$this->foreachmember("down_user",$SORT_ID);
		$manage_name=implode(",",$a);
		$new_name=implode(",",$b);
		$down_name=implode(",",$c); 
		$this->assign("_MANAGE_USER",$_MANAGE_USER);
		$this->assign("_NEW_USER",$_NEW_USER);
		$this->assign("_DOWN_USER",$_DOWN_USER);
		$this->assign("manage_name",$manage_name); 
		$this->assign("new_name",$new_name); 
		$this->assign("down_name",$down_name); 
		$this->assign("list",$list); 
		$this->assign("list1",$list1); 
		$this->assign("list2",$list2); 
		$this->assign("list3",$list3); 
		$this->assign("sort",$sort);   
		$this->assign("SORT_ID",$SORT_ID);
		$this->assign("type",$type);  
		$this->assign("row",$row);
		$this->display();
	}
	
 
	/*---------------遍历--管理权限人员、新建权限人员、下载权限人员-----------*/
	public function foreachmember($columns,$SORT_ID){
		$list_d=array(); $list_p=array(); $list_u=array();
		$dao=D("FileSort");  
		$row=$dao->where("SORT_ID='$SORT_ID'")->find();
		if($columns=='manage_user'){
			$PRIV_SET=explode(",",$row[MANAGE_USER]);
		}elseif ($columns=='new_user'){
			$PRIV_SET=explode(",",$row[NEW_USER]);
		}else{
			$PRIV_SET=explode(",",$row[DOWN_USER]);  
		} 
		$dao_d=D("Department");//按部门选择人员 
		$dao_p=D("UserPriv");//按角色选择人员 
		$dao_u=D("User");//按个人选择人员 
		foreach ($PRIV_SET as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if($b=="D"){ 
            			$list4 = $dao_d->where("DEPT_ID='$c'")->find(); 
            			$listall[$key][id]="D_".$list4[DEPT_ID];
            			$listall[$key][name]=$list4[DEPT_NAME];
            		}
            		if ($b=="P"){ 
            			$list4 = $dao_p->where("USER_PRIV='$c'")->find();
            			$listall[$key][id]="P_".$list4[USER_PRIV];
            			$listall[$key][name]=$list4[PRIV_NAME];
 					}
 					if ($b=="U"){ 
            			$list4 = $dao_u->where("uid='$c'")->find();
            			$listall[$key][id]="U_".$list4[uid];
            			$listall[$key][name]=$list4[USER_NAME];
  					}   	       
        }    
		return  $listall;
	}
	/*---------------添加或修改文件夹--执行程序-----------*/
	public function formsubmit(){
		$SORT_ID=$_REQUEST[SORT_ID];
		if ($_REQUEST[SORT_TYPE]!=2){
			$_POST[DEPT_ID]=0;
		}
		$dao=D("FileSort");

		if ($SORT_ID) {//修改
			$_POST[MANAGE_USER]=$_POST['manage_id'];
			$_POST[NEW_USER]=$_POST['new_id'];
			$_POST[DOWN_USER]=$_POST['down_id'];
			
			$map="SORT_NAME='$_POST[SORT_NAME]' and SORT_TYPE='$_POST[SORT_TYPE]' and DEPT_ID=$_POST[DEPT_ID] and SORT_ID<>$SORT_ID";
			$count=$dao->count($map);
			if ($count) {
				$this->error("指定的文件夹开放范围中名为 ".$_POST[SORT_NAME]." 的文件夹已存在");
			}
						
			$dao->create();
			$dao->where("SORT_ID='$SORT_ID'")->save();
			$this->assign("jumpUrl",__URL__."/index");
			$this->success("成功修改");
		}else {//添加
			$_POST[MANAGE_USER]=$_POST['manage_id'];
			$_POST[NEW_USER]=$_POST['new_id'];
			$_POST[DOWN_USER]=$_POST['down_id'];
			$map="SORT_NAME='$_POST[SORT_NAME]' and SORT_TYPE='$_POST[SORT_TYPE]' and DEPT_ID=$_POST[DEPT_ID] and SORT_PARENT=0";
			$count=$dao->count($map);
			if ($count) {
				$this->error("指定的文件夹开放范围中名为 ".$_POST[SORT_NAME]." 的文件夹已存在");
			}			
			$_POST[SORT_PARENT]=0;
			$dao->create();
			$dao->add();
			$this->assign("jumpUrl",__URL__."/index");
			$this->success("成功提交");
		}
	}
	
	/*------------------共享文件夹--调用fileAction的方法--------------*/
	public function sharesubmit(){
		$SORT_ID=$_REQUEST[SORT_ID];
		$type=$_REQUEST[type];
		$FLD_STR=$_REQUEST[FLD_STR]; 
		$dao=D("FileSort");
		if($type=='manage'){
			$priv_filed="MANAGE_USER";
		}elseif ($type=='new'){
			$priv_filed="NEW_USER";
		}else {
			$priv_filed="DOWN_USER";
		}		 
		$dao->setField($priv_filed,$FLD_STR,"SORT_ID=$SORT_ID"); 
		$this->success("成功提交");
	}
	/*------------------删除文件夹--调用fileAction的方法--------------*/
	public function delete(){
		$file= new FileAction();
        $file->_tree_delete($_REQUEST[SORT_ID]);
        $this->assign("jumpUrl",__URL__."/index");
        $this->success('成功删除');
	}	 
	/*--------------管理权限--新建权限--下载权限---提交程序------------------*/
	public function setsubmit(){
		$SORT_ID=$_REQUEST[SORT_ID];
		$type=$_REQUEST[type];
		if ($type=="manage") {
			$MANAGE="MANAGE_USER";
		}elseif ($type=="new"){
			$MANAGE="NEW_USER";
		}elseif ($type=="down") {
			$MANAGE="DOWN_USER";
		}elseif ($type=="touser") {
			$MANAGE="TO_USER_ID";
		}
		$dao=D("FileSort"); 
		$FLD_STR=$_REQUEST[FLD_STR]; 
		$dao->setField($MANAGE,$FLD_STR,"SORT_ID=$SORT_ID");
		//ECHO $dao->getlastsql();exit;
		$this->assign("jumpUrl",__URL__."/index");
		$this->success("成功提交");
	}
}	
?>