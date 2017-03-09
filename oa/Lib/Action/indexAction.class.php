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
   	    $this->assign("LOGIN_USER_ID",$user[USER_ID]);
   	    
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
   
   
   public function indexSearch(){ 
   	$keyword=$_REQUEST['key'];
   	
   	/*用户*//*用户名及拼音名、部门、职务、手机、Email*/
   	$dao_address=D("Address");
   	$map_address="(USER_ID like '%".$keyword."%')||(PSN_NAME like '%".$keyword."%')||(MINISTRATION like '%".$keyword."%')||(DEPT_NAME like '%".$keyword."%')||(MOBIL_NO like '%".$keyword."%')||(EMAIL like '%".$keyword."%')";
   	$row_address=$dao_address->where($map_address)->findall(); 
   	$count_address=count($row_address);
   	$this->assign("row_address",$row_address);
   	$this->assign("count_address",$count_address);
   	
  	
   	/*邮件*//*标题、内容*/
   	$dao_email=D("Email");
   	$map_email="(TO_ID='$this->_uid')&&((SUBJECT like '%".$keyword."%')||(CONTENT like '%".$keyword."%'))";  
   	$row_email=$dao_email->where($map_email)->findall(); 	
   	foreach ($row_email as $key=>$v){
   		$row_email[$key][CONTENT]=csubstr__old(&$v['CONTENT'], $start=0, $long=100, $ltor=true, $cn_len=2);
   	}
   	$this->assign("row_email",$row_email); 
   	
   	/*公告*//*标题、内容*/
   	$dao_notify=D("Notify");
   	$dao_d=D("Department");
	$a=$dao_d->re_parent_deptid($this->LOGIN_DEPT_ID);
	$b=explode(",",$a); 
	foreach ($b as $c){
		$d[] = "InStr(TO_ID,'D_$c')>0";
	}
	$list_deptid=implode(" or ",$d);   
	$list_privid="P_".$this->LOGIN_USER_PRIV;
	$map_notify="(InStr(TO_ID,'$list_userid')>0 or InStr(TO_ID,'$list_privid')>0 or $list_deptid)&&((SUBJECT like '%".$keyword."%')||(CONTENT like '%".$keyword."%'))";   
   	$row_notyfy=$dao_notify->where($map_notify)->order('SEND_TIME DESC')->findall(); 
	foreach ($row_notyfy as $key=>$v){
   		$row_notyfy[$key][SEND_TIME]=date('Y-m-d',$v['SEND_TIME']);
   		$row_notyfy[$key][CONTENT]=$this->cut_str($v['CONTENT'], 10, 0, 'UTF-8');
   	} 
   	$this->assign("row_notify",$row_notyfy);
   	
   	/*文件柜*//*标题、内容、附件名称、附件描述*/
   	$dao_file_sort=D("FileSort");
   	$dao_file_content=D("FileContent");
   	$map_file="USER_ID='$this->LOGIN_USER_ID'&&((SUBJECT like '%".$keyword."%')||(CONTENT like '%".$keyword."%')||(ATTACHMENT_NAME like '%".$keyword."%')||(ATTACHMENT_DESC like '%".$keyword."%'))";
   	$row_file=$dao_file_sort->table('file_sort a')->join('file_content b on a.SORT_ID=b.SORT_ID')->field('a.*,b.*')->where($map_file)->order('SEND_TIME DESC')->findall(); 
   	$this->assign("row_file",$row_file);
   	
   	/*工作流*//*标题*/
	//我创建的工作
	$zworkwork = new ZworkWorkModel();
	$zworkList = $zworkwork->where("UserId='$this->LOGIN_USER_ID' and zworkName like '%".$keyword."%'")->order('zworkTime desc')->findAll();		
	$this->assign('zworkList',$zworkList); 
	//我被指派做的工作
	$workflow = new ZworkFlowModel();
	$workflowList = $workflow->where("powerUser like '%$this->LOGIN_USER_ID%' and flowName like '%".$keyword."%'")->order('startTime desc')->findAll();
	foreach ($workflowList as $k=>$v){
		$workRow=$zworkwork->find("zworkId=$v[zworkId]");
		$workflowList[$k][zworkName]=$workRow[zworkName];
	}
	$this->assign('workflowList',$workflowList);
	/*客户信息*//*客户名称、省、市、街道*/
	$ids_array = array();
	$ids_string = null;
	$account	= D("Account"); 
	$share_res = $account->getShareInfo('Account', $this->_uid);
	$share_count = count($share_res);
	if ($share_res) {
		for ($i = 0; $i < $share_count; $i++){
			array_push($ids_array, $share_res[$i]['record_id']); 
		}
		$ids_string = join(',', $ids_array);
	} 
	if (null != $ids_string) {
		$account_res = "manager_id=$this->_uid or builder_id=$this->_uid or a.id in ($ids_string)";
	}else{
		$account_res = "manager_id=$this->_uid or builder_id=$this->_uid";
	}
   	if($_GET['field']){
		$sort = $_GET['field']." ".$_GET['sort'];
		$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
	}else{
		$sort = 'time_modify desc';
		$def_sort = 'desc';
	}
	$map_account="`name` like '%".$keyword."%' or `province` like '%".$keyword."%' or `city` like '%".$keyword."%' or `street` like '%".$keyword."%'";
	
   	$list = $account->query("select a.*, province,city from crm_account a, crm_address b where (".$map_account.") and (".$account_res.") and a.id = record_id and table_name='Account' order by ".$sort."");
   	/**判断记录查看权限,插入到相关记录中*/ 
   	if($list) 	$list = $account->addManager('Account', $list, $share_res);
   	$this->assign('account_record',$list);
		
   	
   	$this->assign("keyword",$keyword);
   	$this->display();
   }
/* 
Utf-8、gb2312都支持的汉字截取函数 
cut_str(字符串, 截取长度, 开始长度, 编码); 
编码默认为 utf-8 
开始长度默认为 0 
*/ 
 
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8') 
{ 
    if($code == 'UTF-8') 
    { 
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/"; 
        preg_match_all($pa, $string, $t_string); 
 
        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."..."; 
        return join('', array_slice($t_string[0], $start, $sublen)); 
    } 
    else 
    { 
        $start = $start*2; 
        $sublen = $sublen*2; 
        $strlen = strlen($string); 
        $tmpstr = ''; 
 
        for($i=0; $i< $strlen; $i++) 
        { 
            if($i>=$start && $i< ($start+$sublen)) 
            { 
                if(ord(substr($string, $i, 1))>129) 
                { 
                    $tmpstr.= substr($string, $i, 2); 
                } 
                else 
                { 
                    $tmpstr.= substr($string, $i, 1); 
                } 
            } 
            if(ord(substr($string, $i, 1))>129) $i++; 
        } 
        if(strlen($tmpstr)< $strlen ) $tmpstr.= "..."; 
        return $tmpstr; 
    } 
} 
        
} 
?>
