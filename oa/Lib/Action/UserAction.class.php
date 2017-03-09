<?php
/**
 * UserAction.class.php
 *功能：用户管理模块
 **/
import("@.Util.userselect");
import("@.Util.cpinyin");
class UserAction extends PublicAction {
	var $e,$postDept,$paraUrl1,$paraUrl2,$paraTarget,$paraId,$paraValue,$postPriv,$privNo,$privNoFlag;
	function _initialize(){
		
		parent::_initialize();
	}
/*****************************入口程序*************************************************/
	public function index(){
	//----------------用户列表--------------------------
		
	    $department = new DepartmentModel();
		$departmentList = $department->order('DEPT_ID')->findall();
		$user = new Model();
		
		
    	###外部人员
		$dao=D("UserPriv");
    	$userpriv=$dao->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
    	$PRIV_NO=$userpriv["PRIV_NO"];
		if($this->LOGIN_USER_PRIV!="1"){
		    //$map2="user.USER_PRIV=user_priv.USER_PRIV and user_priv.PRIV_NO>$PRIV_NO and user_priv.USER_PRIV!=1";
		    $map2="user.USER_PRIV=user_priv.USER_PRIV ";
		    $depturl="DEPT_ID/$this->LOGIN_DEPT_ID";
		 }else{
		 	$map2="user.USER_PRIV=user_priv.USER_PRIV ";
		 	$depturl="";
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
			$treeoutuser .="outuser.add('$v2[USER_ID]',$v2[DEPT_ID],'$v2[USER_NAME]','".__URL__."/userEdit/USER_ID/$v2[USER_ID]','','user_main','/oa/Tpl/default/Public/images/ico/user$v2[SEX].png','','','');"	;
		}
		$this->assign("treeoutuser",$treeoutuser);
		
		$this->assign("depturl",$depturl);
		
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
		$deptlist=$userselect->_DeptUserTree(0,$PRIV_NO_FLAG,$PRIV_NO); 
		$this->assign("deptlist",$deptlist);	
				
		//$treeoutuser = "outuser.add(0,-1,'','','','','','','','tree-root');";
		
		$this->display();
	}
/*------------------用户列表---------------*/	
    public function userlist(){
    	$DEPT_ID=intval($_REQUEST[DEPT_ID]);
    	$type=$_REQUEST[type];
    	
    	$dao=D("UserPriv");
    	$userpriv=$dao->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
    	$PRIV_NO=$userpriv["PRIV_NO"];
		 
    	 if($this->LOGIN_USER_PRIV!="1"){
		    //$map="DEPARTMENT.DEPT_ID=user.DEPT_ID and user.USER_PRIV=user_priv.USER_PRIV and user_priv.PRIV_NO>$PRIV_NO and user_priv.USER_PRIV!=1";
		    $map="user.USER_PRIV=user_priv.USER_PRIV and user_priv.PRIV_NO>$PRIV_NO and user_priv.USER_PRIV!=1";
		 }else{
		 	//$map="DEPARTMENT.DEPT_ID=user.DEPT_ID and user.USER_PRIV=user_priv.USER_PRIV ";
		 	$map="user.USER_PRIV=user_priv.USER_PRIV ";
		 }
		  	
	    if ($DEPT_ID) {
	       $map.=" AND user.DEPT_ID='$DEPT_ID'";
	    }
	    
	    if ($type) {
	    	$map.=" AND (user.DEPT_ID='0' OR user.DEPT_ID='')";
	    }else {
	    	$map.=" AND (user.DEPT_ID!='0' OR user.DEPT_ID!='')";
	    }
	    
        //$order="DEPT_NO,PRIV_NO,USER_NAME";
        //$table="user,user_priv,DEPARTMENT";
        $order="PRIV_NO,USER_NAME";
    	$table="user,user_priv";
    	
    	$dao= new Model();
    	
		$count=$dao->table($table)->field("count(*) as num")->where($map)->find();
		$count=$count[num];
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '';
		}
		
		$p= new Page($count,$listRows);
		
    	$list=$dao->table($table)
    	          ->where($map)
    	          ->limit("$p->firstRow,$p->listRows")
    	          ->order($order)
    	          ->findall();
    	//echo $dao->getLastSql();          
		$page       = $p->show();
		$this->assign("page",$page);

    	$this->assign("list",$list);
    	$this->assign("DEPT_ID",$DEPT_ID);
    	$dao=D("Department");
    	$dept=$dao->where("DEPT_ID='$DEPT_ID'")->find();    	
    	if ($dept[DEPT_NAME]) {
    	   $desc="用户列表（".$dept[DEPT_NAME]."），共".$count."人";    		
    	}else {
    	   $desc="用户列表，共".$count."人";    			
    	}
    	$this->assign("type",$type);
    	$this->assign("DEPT_ID",$DEPT_ID);
    	$this->assign("desc",$desc);
    	$this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID);
    	$this->display();
    }
    

    	#########js部分人员信息列表###########
	public  function js_list(){  
		$dao_d=D("Department");//按部门选择人员 
		$dao_p=D("UserPriv");//按角色选择人员 
		$dao_u=D("User");//按个人选择人员 
		$list=$dao_u->findall();   
		foreach ($list as $key1=>$v) {
			$dept_name[]=$dao_d->where("DEPT_ID=$v[DEPT_ID]")->find();
			$priv_name[]=$dao_p->where("USER_PRIV=$v[USER_PRIV]")->find(); 
		}
		foreach ($dept_name as $key2=>$j){
			$list[$key2][DEPT_NAME]=$j[DEPT_NAME];
		}
		foreach ($priv_name as $key3=>$k){
			$list[$key3][PRIV_NAME]=$k[PRIV_NAME];
		}   
	 	foreach ($list as $key4=>$vv){ 
	 		if($vv[USER_ID]!=''){							  
	 		$arr[]=$vv[USER_ID].":{name:'".$vv[USER_NAME]."',part:'".$dao_d->re_parent_deptname($vv[DEPT_ID])."',role:'".$vv[PRIV_NAME]."',id:'U_".$vv[uid]."'}";  
	 		} 
		}   
		$arr_depttree=implode(",",$arr);
		return $arr_depttree;
	} 
	
	###########角色树########
	public  function left_privtree(){  
		$dao_p=D("UserPriv");//按角色选择人员  <li rel='lijian'>人事经理</li>
		$list=$dao_p->findall();   
		foreach ($list as $key=>$v) { 
			$arr_priv[]="<li id=\"P_".$v[USER_PRIV]."\">".$v[PRIV_NAME]."</li>";
		}
		$arr_priv_li=implode(" ",$arr_priv);
		return $arr_priv_li; 
	}
	
	
    	/**************************根据id 提取部门、角色、人员名称************************/	
		public  function find_allname($all_id){
		#######部门名称##### 
		$dao_dd=D("Department");//按部门选择人员 
		$dao_pp=D("UserPriv");//按角色选择人员 
		$dao_uu=D("User");//按个人选择人员 
		$list_all = array();  
			$ALLID = explode(',',$all_id); 
            foreach ($ALLID as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if($b=="D"){ 
            			$list = $dao_dd->where("DEPT_ID='$c'")->find();  
            			$list_all[]=$list[DEPT_NAME];
            		}
            		if ($b=="P"){ 
            			$list = $dao_pp->where("USER_PRIV='$c'")->find(); 
            			$list_all[]=$list[PRIV_NAME];
 					}
 					if ($b=="U"){ 
            			$list = $dao_uu->where("uid='$c'")->find(); 
            			$list_all[]=$list[USER_NAME];
  					}   	       
        	}
		return  $list_all;
}
		/*************根据部门角色用户id  取得用户USER_ID**********/
public function getUser_id($id){  
		$dao_u=D("User");//按个人选择人员 
		$dao_p=D("Department");//按部门选择人员 
		$TO_USER_ID=explode(",",$id);
		$listall=array();
        foreach ($TO_USER_ID as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if ($b=="D"){   
						 $lista=explode(",",$dao_p->re_user_id($c));
						 foreach ($lista as $v2){
						 	$listall[]=$v2;
						 }
            		} elseif ($b=="P"){ 
            			$list_p = $dao_u->where("USER_PRIV='$c'")->findall();
            			foreach($list_p as $key3=>$v3){ 
            					$listall[]=$v3[USER_ID]; 
            			}  
 					}elseif ($b=="U"){ 
            			$list_u = $dao_u->where("uid='$c'")->findall();
            			foreach($list_u as $key4=>$v4){ 
            					$listall[]=$v4[USER_ID]; 
            			} 
  					}else{
  						$listall[]=$id;
  					}  	 	       
        }
         
        return  $listall;
	}
/*****************************新建程序*************************************************/
	public function news(){
		UserSelectAction::DeptSelect();
	//部门下拉菜单
		//$select = $this->my_dept_tree(0,$deptId,1);
		$DEPT_ID=$_REQUEST[DEPT_ID];
		if (!$DEPT_ID)
		     $DEPT_ID=$this->LOGIN_DEPT_ID;
		$this->assign("DEPT_ID",$DEPT_ID);     
		$select = my_dept_tree(0,$DEPT_ID,1);
		$this->assign('select',$select);
	//角色
		$userPriv = new UserPrivModel();
		$userPrivs = $userPriv->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
		$privNo = $userPrivs['PRIV_NO'];
		if($this->LOGIN_USER_PRIV != "1")
			$userPriv1 = $userPriv->where('PRIV_NO>'.$privNo.' and USER_PRIV != 1')->order('PRIV_NO')->findAll();
		else 
			$userPriv1 = $userPriv->order('PRIV_NO')->findAll();
		//echo $userPriv->getLastSql();	
		$this->assign('userPriv1',$userPriv1);
		//管理范围
		$dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$POST_PRIV=$user[POST_PRIV];
		$this->assign("POST_PRIV",$POST_PRIV);
		####选择部门
		/*
		if ($POST_PRIV!=0) {
		   UserSelectAction::DeptSelect();	
		}
		*/
		
		
		$this->display();
	}
/*****************************添加数据程序*************************************************/
	public function add(){
		/*
		$dao=D("disUsers");
		$count=$dao->count("username='$_POST[USER_ID]'");
        if ($count) {//验证论坛用户表
            $this->error("用户名已存在"); 					
        }
        */
		$user=D("User");
		$_POST[PASSWORD]=md5($_POST[PWD]);
		$_POST[POST_DEPT]=$_POST[TO_ID];
		if (false===$user->create()) {
            $this->error($user->getError());			
		}
		$id=$user->add();
		/*
		###论坛相关
		    $data[username]=$_POST[USER_ID];
		    $data[password]=$_POST[PASSWORD];
		    $data[group_id]=4;
		    $data[realname]=$_POST[USER_NAME];
		    $data[registered]=$this->CUR_TIME_INT;
		    $disid=$dao->add($data);
			*/
			if ($id) {
			    $this->success('添加成功');	
			}else {
		        $this->error("添加失败");		
			}
	}
/*****************************用户组程序*************************************************/
	public function dept2(){
		$user = new UserModel();
		$userRow = $user->where("USER_ID='$_GET[USER_ID]'")->find();
		return $userRow['DEPT_ID'];
	}
/*****************************更新用户信息模板程序*************************************************/
	public function userEdit(){
		UserSelectAction::DeptSelect();	
	//登陆用户信息
		$user = new UserModel();
		$user = $user->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$postPriv = $user['POST_PRIV'];
		$POST_PRIV=$user[POST_PRIV];
		$this->assign("POST_PRIV",$POST_PRIV);
		
		$this->assign('postPriv',$postPriv);
	//被访问用户信息
	
		$users = new UserModel();
		$users = $users->where("USER_ID='$_REQUEST[USER_ID]'")->find();
		$deptId=$users['DEPT_ID'];
		$this->assign('users',$users);
		
	    $TO_ID=$users[POST_DEPT];
	    $TOK=strtok($TO_ID,",");
	    while($TOK!="")
	    {
	    	$dao=D("Department");
	    	$ROW=$dao->where("DEPT_ID='$TOK'")->find();
	        $POST_DEPT_NAME.=$ROW["DEPT_NAME"].",";
	       $TOK=strtok(",");
	    }
    	$this->assign("POST_DEPT_NAME",$POST_DEPT_NAME);	
    	
	//部门下拉菜单
		
		//$select = my_dept_tree(0,$users[DEPT_ID],1);
		
		$select = my_dept_tree(0,$users[DEPT_ID],0);
		$this->assign('select',$select);
		    	
	//权限
		$postPriv1 = $users['POST_PRIV'];
		$this->assign('postPriv1',$postPriv1);
	//下拉菜单
		$dept = new DepartmentModel();
		$deptList = $dept->findAll();
		$this->assign('deptList',$deptList);
		$myDeptId = $this->dept2();
		$this->assign('myDeptId',$myDeptId);
		//$select = $this->my_dept_tree(0,$deptId,1);
		//$this->assign('select',$select);
		
	//角色类型

		$userPriv = new UserPrivModel();
		$userPrivs = $userPriv->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
		$privNo = $userPrivs['PRIV_NO'];
		if($this->LOGIN_USER_PRIV != "1")
			$userPriv1 = $userPriv->where('PRIV_NO>'.$privNo.' and USER_PRIV != 1')->order('privNo')->findAll();
		else 
			$userPriv1 = $userPriv->order('PRIV_NO')->findAll();
		$select3 ="";
		foreach ($userPriv1 as $k=>$v){
			$select3 .= '<option value='.$v['USER_PRIV'].'>'.$v['PRIV_NAME'].'</option>';
		}
		//$this->assign('select3',$select3);
		//管理范围
		$this->assign('userPriv1',$userPriv1);
		$this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID);
		
	//用户组
	   // $dao=D("UserGroup");
	   // $usergroups=$dao->order("listorder desc")->findall();	
	   // $this->assign("usergroups",$usergroups);
	    
	    $this->assign("type",$_REQUEST[type]);
	    
		$this->display();
	}
/*****************************数据更新程序*************************************************/
	public function update(){
		$user = new UserModel();
		$_POST[POST_DEPT]=$_POST[TO_ID];
		$pinyin= new cpinyin();
		$_POST[USER_SPELL]=$pinyin->get($_POST[USER_NAME]);
		$groupids=$_POST[groupid];
		if (is_array($groupids)) {
			foreach ($groupids as $groupid){
				$gids.=$groupid.",";
			}
		}
        $_POST[groupids]=$gids;
		if (false===$user->create()) {
            $this->error($user->getError());			
		}
		$user->where("USER_ID='$_POST[USER_ID]'")->save();
		//echo $user->getLastSql();exit;
				
		###论坛相关
		/*
		$dao=D("disUsers");
		$count=$dao->count("username='$_POST[USER_ID]'");
		if (!$count) {//如果用户在论坛表里不存在
		    $data[username]=$_POST[USER_ID];
		    $users=$user->where("USER_ID='$_POST[USER_ID]'")->find();
		    $data[password]=$users[PASSWORD];
		    $data[group_id]=4;
		    $data[email]=$users[EMAIL];
		    $data[realname]=$users[USER_NAME];
		    $data[registered]=$this->CUR_TIME_INT;
		    $dao->add($data);
		    
		}else {
			$data[realname]=$_POST[USER_NAME];
			$dao->where("username='$_POST[USER_ID]'")->save($data);
		}
		*/	
		
			$this->assign("jumpUrl",__URL__."/userlist/?DEPT_ID=$_POST[DEPT_ID]");
			$this->success('更新成功!');
	}
	
	/*-----------------------修改用户密码-------------*/
    public function editpass(){
		$users = new UserModel();
		$users = $users->where("USER_ID='$_REQUEST[USER_ID]'")->find();
		$deptId=$users['DEPT_ID'];
		$this->assign('users',$users);

		$this->display();	
    }
    
    /*------------------修改用户密码提交程序-------------*/
    public function updatepass(){
    	$_POST[PASSWORD]=md5($_POST[PASSWORD]);
    	
    	$user = new UserModel();
		if (false===$user->create()) {
            $this->error($user->getError());			
		}
			$user->where("USER_ID='$_POST[USER_ID]'")->save();
			$this->assign("jumpUrl",__URL__."/userlist/?DEPT_ID=$_POST[DEPT_ID]");
			$this->success('成功修改!');
    }
     	
	
	
	/*-------------------删除用户-------------*/
	public function delete(){
		$USER_ID=$_REQUEST[USER_ID];
		$uid=$_REQUEST[uid];
		$DEPT_ID=$_REQUEST[DEPT_ID];
		$user = new UserModel();
		
		//$row=$user->where("USER_ID='$USER_ID'")->find();
		if ($uid==1||$USER_ID=="admin") {
			$this->error("超级用户不能删除");
		}
		
		if ($uid) {
		   $user->where("uid='$uid'")->delete();	
		}else{
		   $user->where("USER_ID='$USER_ID'")->delete();
		}
		$this->assign("jumpUrl",__URL__."/userlist/DEPT_ID/$DEPT_ID");
		$this->success("成功删除");
	}
	/*---------------清空密码-----------*/
	public function nopass(){
		$USER_ID=$_REQUEST[USER_ID];
		$DEPT_ID=$_REQUEST[DEPT_ID];
		$user = new UserModel();
		//$row=$user->where("USER_ID='$USER_ID'")->find();
		if ($this->LOGIN_USER_ID=="admin") {
	//		ECHO $PASSWORD;
			$user->setField("PASSWORD",md5($PASSWORD),"USER_ID='$USER_ID'");
//ECHO $user->getLastSql();EXIT;			
			$user->setField("LAST_PASS_TIME",'',"USER_ID='$USER_ID'");
			//$user->save();

		$this->assign("jumpUrl",__URL__."/userlist/DEPT_ID/$DEPT_ID");
		$this->success("成功清空密码");			
		}else {
		$this->assign("jumpUrl",__URL__."/userlist/DEPT_ID/$DEPT_ID");
		$this->error("您没有这个权限");			
		}
		
	}	
	
	/*****************************获取$deptPriv值***********方法类********************************/
	protected function Dept($postPriv){
		if($postPriv=="1")
			$deptPriv="全体";
		elseif ($deptPriv=="2")
			$deptPriv="指定部门";
		else{
			$departement = new DepartmentModel();
			$departement = $departement->where('DEPT_ID='.$this->LOGIN_DEPT_ID)->find();
			$deptPriv = $departement['DEPT_NAME'];
			echo $deptPriv;
		}
		return $deptPriv;
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
}
?>