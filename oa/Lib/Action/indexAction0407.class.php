<?php 

class IndexAction extends PublicAction {

	public function _initialize()
    {
       if(!isset($_SESSION[LOGIN_USER_ID])) {
			$this->redirect('login','Public');
        }
        parent::_initialize();
    }
    
   public function menu(){
    $loginuser=$_SESSION[LOGIN_USER_ID]; 
    //echo $_SESSION[C('USER_AUTH_KEY')];
    //echo $loginuser;
   	$user=new UserModel();
    $users = $user->where("USER_ID='$loginuser'")->find();	
   	$this->assign("users",$users);  
   	
    $userpriv=new user_privModel();
    $userprivs = $userpriv->where("USER_PRIV='$users[USER_PRIV]'")->find();
    //$dao = D("sys_menu");
    //print_r($userprivs);
    $this->display();
   }

   
   
   public function callleftmenu(){
   	$loginuser=$_SESSION[C('USER_AUTH_KEY')]; 
    //echo $_SESSION[C('USER_AUTH_KEY')];
    //echo $loginuser;
   	$user=new UserModel();
    $users = $user->where("USER_ID='$loginuser'")->find();	
   	$this->assign("users",$users);  
   	
     $this->display("general:callleftmenu");
   }   
   
   public function table_index(){
     $this->display("general:table_index");
   }  
   
    public function login_info(){
   	$loginuser=$_SESSION[LOGIN_USER_ID]; 
   	$user=new UserModel();
    $users = $user->where("USER_ID='$loginuser'")->find();	
   	$this->assign("users",$users);  
    $this->display("general:login_info");
   } 
   public function status(){
   	 	$Face = new InterfaceModel();
		$face = $Face->find();
		$STATUS_TEXT=$face[STATUS_TEXT];
		   $STATUS_TEXT=str_replace("<","&lt",$STATUS_TEXT);
		   $STATUS_TEXT=str_replace(">","&gt",$STATUS_TEXT);
		   $STATUS_TEXT=stripslashes($STATUS_TEXT);
		   $STATUS_TEXT=str_replace(" ","&nbsp;",$STATUS_TEXT);
		   $STATUS_TEXT=str_replace("\n","<br>",$STATUS_TEXT);
   		$this->assign("STATUS_TEXT",$STATUS_TEXT);
   	    $this->assign("face",$face);
   	    
     $this->display("status:index");
   }

   public function setStatus(){
   	$status=$_REQUEST[status];
  // 	$dao=D("User");
   	$daosess=D("UserSession");
   	if (empty($status)) {
   	    //$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->field("USER_STATUS")->find();
   	    $user=$daosess->where("userid='$this->LOGIN_USER_ID'")->field("user_status")->find();   
   	    echo $user[user_status];
   	}else {
//   		$dao->setField("USER_STATUS",$status,"USER_ID='$this->LOGIN_USER_ID'");
   		
   		$daosess->setField("user_status",$status,"userid='$this->LOGIN_USER_ID'");
   		
   		//echo $status;
   	}
   	
  }
   /*---------首页------------*/
   public function index(){//新首页
   	   	$unit=$this->_unit();
   	    $this->assign("indexTitle",$unit[UNIT_NAME]);  
   	    
   		$count = userOnlineAction::count();
   		$this->assign('count',$count);
   		
   		//$TIME_STR=date("Y,m,d,H,i,s");
   	    //$this->assign("TIME_STR",$TIME_STR);
   	    $dao=D("Department");
   	    $depts=$dao->where("DEPT_ID='$this->LOGIN_DEPT_ID'")->find();
   	    $this->assign("LOGIN_DEPT_NAME",$depts[DEPT_NAME]);
   	    
   	    $dao=D("User");
   	    $user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
   	    $this->assign("LOGIN_USER_NAME",$user[USER_NAME]);
   	    $daopriv=new  UserPrivModel();
   	    //$daopriv=D("UserPriv");
   	    $priv=$daopriv->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
   	    $this->assign("USER_PRIV",$priv[PRIV_NAME]);
   	    //菜单

             $USER_FUNC_ID_STR=$priv["FUNC_ID_STR"];
		 if($this->LOGIN_USER_ID=="admin")
		     $USER_FUNC_ID_STR.="32,33,56,";

   	    $daomenu=D("SysMenu");
        $daofunc=D("SysFunction");
		$list=$daomenu->where("flag=1")->order("MENU_ID")->findall();
		$i=0;
		foreach ($list as $row){
			$listfunc=$daofunc->where("MENU_ID like '$row[MENU_ID]%' and length(MENU_ID)=4 and flag=1 and FUNC_ID in (".$USER_FUNC_ID_STR."0)")->order("MENU_ID")->findall();
			     $k=0; 
			foreach ($listfunc as $keys=>$rows){
				$listfunc[$keys][pid]=intval($row[MENU_ID]+9999);
				$listfunc[$keys][id]=intval($rows[FUNC_ID]);
				
				if(substr($rows[FUNC_CODE],0,1)=="@"){//是不是子菜单
					$listfunc2=$daofunc->where("MENU_ID like '$rows[MENU_ID]%' and length(MENU_ID)=6 and flag=1 and FUNC_ID in (".$USER_FUNC_ID_STR."0)")->order("MENU_ID")->findall();
					  if ($listfunc2) {
					  	 foreach ($listfunc2 as $keyss=>$rowss){
					  	 	$listfunc2[$keyss][pid]=intval($rows[FUNC_ID]);
					  	 	$listfunc2[$keyss][id]=intval($rowss[FUNC_ID]);
					  	 }
					  }
					$listfunc[$k][sub2]=$listfunc2;
				}
				$k++;
			}
			$list[$i][pid]=0;//
			$list[$i][id]=intval($row[MENU_ID]+9999);//
			if (!empty($listfunc)) {
                 $list[$i][subcount]=1;					
			}		
			$list[$i][sub]=$listfunc;
			$i++;
		}
		$this->assign("list",$list);
		//print_r($list);



   	$this->display();
   	
   }
   
   public function smsmsg(){
    $dao=D("Sms");
    $map="TO_ID='$this->LOGIN_USER_ID' and REMIND_FLAG='1' and SEND_TIME<='$this->CUR_TIME'";
    $count=$dao->count($map);
    
    $this->assign("count",$count);
   	$this->display();
   	
   }
   
   public function index_iframe(){
   	
   	$this->display();
   	
   }
   public function test(){
   	echo userOnlineAction::count();
   }
        
} 
?>
