<?php
/**********************************
 *       公告管理模块
 * author:廖秋虎  time:08/12/15
 **********************************/ 
include_cache(APP_PATH."/Common/notify.php");
class manageAction extends UserAction {
/****************入口程序*******************************************************************/
	public function index(){
		$notify = new NotifyModel();
		//获取等级查询数据
		if($this->LOGIN_USER_PRIV !="1"){
			$map="FROM_ID='$this->LOGIN_USER_ID'";
		}else{
			$map="";
		} 
		$count=$notify->count($map);
	    import("ORG.Util.Page");	
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '20';
		}
		$p = new Page($count,$listRows); 
		$notify = $notify->where($map)->limit("$p->firstRow,$p->listRows")->order("NOTIFY_ID DESC")->findAll();
		foreach ($notify as $key=>$v){
				$allname = $this->find_allname($v['TO_ID']);
				if(empty($allname)){
					$notify[$key][allname] = "全体";	
				}else{
				$notify[$key][allname] = implode(',',$allname);				
				}   
		}
		$page = $p->show();
		$this->assign("page",$page);
		$this->assign("list_all",$list_all);
		$this->assign('notify',$notify);
		$this->display();
	}
 
/****************创建新程序*******************************************************************/
	public function news(){
		####选择部门
		UserSelectAction::DeptSelect();
		
		$NOTIFY_ID=intval($_REQUEST[NOTIFY_ID]);
		if ($NOTIFY_ID) {
			$dao=D("Notify");
			$ROW=$dao->where("NOTIFY_ID='$NOTIFY_ID'")->find();
		}else {
			$ROW=array();
		}
		//附件列表
		if ($ROW[ATTACHMENT_ID]){
			$daoatt=D("Attachments");
			$listatt=$daoatt->where("attid in (0".$ROW[ATTACHMENT_ID]."0)")->findall();
		}
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
		$this->assign("listatt",$listatt);		
		$this->assign("upload_max_filesize",ini_get("upload_max_filesize"));
		$this->assign("NOTIFY_ID",$NOTIFY_ID);
		$this->assign("ROW",$ROW);
		$this->display();
	}
/*****************添加部门****************************************************************/
	public function deptSelect(){ 
		$s = $this->dept_tree_list(0,$privOp);
		$this->assign('s',$s);
		$this->display();			
	}
	
/********************提交数据***********************************************************/
	public function submit(){
	    $NOTIFY_ID=$_REQUEST[NOTIFY_ID]; 
	    if (empty($_POST[CONTENT])) {
			$this->error("请输入内容");
		} 
		$dao=D("Notify");
		$_POST[FROM_ID]=$this->LOGIN_USER_ID;
		$_POST[SEND_TIME] = $this->CUR_TIME_INT;  
//		$array=explode(",",$_POST[TO_ID]);
//		$to_id="";
//		foreach ($array as $dept_id){
//					$to_id.=getSubDepts($dept_id);
//		}
//		$_POST[TO_ID]=$_POST[TO_ID].$to_id;  
		$_POST[BEGIN_DATE] = empty($_POST['BEGIN_DATE'])?$this->CUR_TIME_INT:mktimeFormat($_POST['BEGIN_DATE']);
		$_POST[END_DATE] = empty($_POST['END_DATE'])?($this->CUR_TIME_INT+7862400):mktimeFormat($_POST['END_DATE']); 
		
		 //附件上传
				$a = implode(",",$_POST[ATTACHMENT_ID]);
				$a_old =  implode(",",$_POST[oldattid]);
				if($_POST[oldattid]&&$_POST[ATTACHMENT_ID]){
					$_POST[ATTACHMENT_ID]=$a_old.",".$a.",";
				}elseif($_POST[ATTACHMENT_ID]){
					$_POST[ATTACHMENT_ID]=$a.",";
				}else{
					$_POST[ATTACHMENT_ID]=$a_old.",";
				}
		
				$b = implode("*",$_POST[ATTACHMENT_NAME]);
				$b_old =  implode("*",$_POST[oldattname]);
				if($_POST[oldattname]&&$_POST[ATTACHMENT_NAME]){
					$_POST[ATTACHMENT_NAME]=$b_old."*".$b."*";
				}elseif($_POST[ATTACHMENT_NAME]){
					$_POST[ATTACHMENT_NAME]=$b."*";
				}else{
					$_POST[ATTACHMENT_NAME]=$b_old."*";
				}
				
			if (false===$dao->create()) {
	             $this->error($dao->getError());			
			}			
           	if ($NOTIFY_ID) {
              $dao->where("NOTIFY_ID='$NOTIFY_ID'")->save();
              $id=$NOTIFY_ID;
           	}else{
           	  $id=$dao->add(); 
           	} 
 
           	if ($_REQUEST[SMS_REMIND]=="on") {
           	  $dao=D("User");
           	  $data[CONTENT]="请查看公告通知！\n标题：".csubstr($_POST[SUBJECT],0,100);
           	  $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
           	  if(compare_date(date("Y-m-d",$_POST[BEGIN_DATE]),$this->CUR_DATE)!=0)
                  $data[SEND_TIME]=date("Y-m-d H:i:s",$_POST[BEGIN_DATE]);
              $a=UserAction::getUser_id($_POST[TO_ID]); 
              $b=$dao->findall();  
              $dao=D("Sms");
		      if ($_POST[TO_ID]=='') {
           	    		$data[FROM_ID]=$this->LOGIN_USER_ID;
		           	   	foreach ($b as $c) {
			           	   	$data[TO_ID]=$c[USER_ID];
			           	   	$data[SMS_TYPE]=1;
			           	   	$data[REMIND_FLAG]=1;
			           	   	$dao->add($data);  
		           	   	}       		
           	  }else {   
           	    		$data[FROM_ID]=$this->LOGIN_USER_ID;
		           	   	foreach ($a as $d){
			           	   	$data[TO_ID]=$d;
			           	   	$data[SMS_TYPE]=1;
			           	   	$data[REMIND_FLAG]=1;
			           	   	$dao->add($data);  
		           	   	}   
           	  }   
           	} 
		  $this->assign('jumpUrl',__URL__."/index"); 
		  $this->success('操作已成功!');	 	
	 
}

/*******************查看数据***********************************************************/
	public function view(){
		$notify = new NotifyModel();
		$row = $notify->find($_GET['NOTIFY_ID']);
		$All_Name=$row['TO_ID']; 
		$allname = $this->find_allname($All_Name);
		$row[allname] = implode(',',$allname);	
		if ($row[ATTACHMENT_ID]){
			$daoatt=D("Attachments");
			$listatt=$daoatt->where("attid in (0".$row[ATTACHMENT_ID]."0)")->findall();
		}
		$this->assign("listatt",$listatt);
		
    if(!find_id($row[READERS],$this->LOGIN_USER_ID))
    {
       $row[READERS].=$this->LOGIN_USER_ID.",";
       $notify->setField("READERS",$row[READERS],"NOTIFY_ID='$_GET[NOTIFY_ID]'");
    }
		$this->assign('row',$row);
		$this->display();
	}

/*******************编辑数据***********************************************************/
	public function edit(){
		####选择部门
		UserSelectAction::DeptSelect();
		
		$NOTIFY_ID=intval($_REQUEST[NOTIFY_ID]);
		
		$dao = D("Notify");
		$ROW = $dao->where("NOTIFY_ID='$NOTIFY_ID'")->find();
		$All_Name=$ROW['TO_ID']; 
		$allname = $this->find_allname($All_Name);
		$ROW[allname] = implode(',',$allname);	 
		//附件列表
		if ($ROW[ATTACHMENT_ID]){
			$daoatt=D("Attachments");
			$listatt=$daoatt->where("attid in (0".$ROW[ATTACHMENT_ID]."0)")->findall();
		}
		$this->assign("listatt",$listatt);
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
		#########弹出框右侧######### 
		$dao_d=D("Department");//按部门选择人员
		$list1=$dao_d->DeptSelect();
		$dao_p=D("UserPriv");//按角色选择人员
		$list2=$dao_p->findall();
		$dao_u=D("User");//按个人选择人员
		$list3=$dao_u->findall();
		$list_d = $list_p = $list_u = array(); 
		$TO_USER_ID=explode(",",$ROW[TO_ID]);
        foreach ($TO_USER_ID as $key=>$a){
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
  		
        $this->assign("list1",$list1); 
		$this->assign("list2",$list2);  
		$this->assign("list3",$list3); 		
        $this->assign("listall",$listall); 
		$this->assign("upload_max_filesize",ini_get("upload_max_filesize"));
		$this->assign("NOTIFY_ID",$NOTIFY_ID);
		$this->assign("ROW",$ROW);
		$this->display();
	}
/*--------------------编辑提交----------------*/	
    public function update(){
	  	$NOTIFY_ID=$_REQUEST[NOTIFY_ID]; 
		$dao=D("Notify");
		$_POST[FROM_ID]=$this->LOGIN_USER_ID;
		$_POST[SEND_TIME] = $this->CUR_TIME_INT;
			
		$_POST[BEGIN_DATE] = empty($_POST['BEGIN_DATE'])?$this->CUR_TIME_INT:mktimeFormat($_POST['BEGIN_DATE']);
		$_POST[END_DATE] = empty($_POST['END_DATE'])?($this->CUR_TIME_INT+7862400):mktimeFormat($_POST['END_DATE']); 
			  	 
		//附件上传
			$a = implode(",",$_POST[ATTACHMENT_ID]);
			$a_old =  implode(",",$_POST[oldattid]);
			if($_POST[oldattid]&&$_POST[ATTACHMENT_ID]){
				$_POST[ATTACHMENT_ID]=$a_old.",".$a.",";
			}elseif($_POST[ATTACHMENT_ID]){
				$_POST[ATTACHMENT_ID]=$a.",";
			}else{
				$_POST[ATTACHMENT_ID]=$a_old.",";
			}
	
			$b = implode("*",$_POST[ATTACHMENT_NAME]);
			$b_old =  implode("*",$_POST[oldattname]);
			if($_POST[oldattname]&&$_POST[ATTACHMENT_NAME]){
				$_POST[ATTACHMENT_NAME]=$b_old."*".$b."*";
			}elseif($_POST[ATTACHMENT_NAME]){
				$_POST[ATTACHMENT_NAME]=$b."*";
			}else{
				$_POST[ATTACHMENT_NAME]=$b_old."*";
			}
			
		if (false===$dao->create()) {
	             $this->error($dao->getError());			
		}
        $dao->where("NOTIFY_ID='$NOTIFY_ID'")->save(); 
         
		if (false===$dao->create()) {
             $this->error($dao->getError());			
		}	 	
        $dao->where("NOTIFY_ID='$NOTIFY_ID'")->save();
        if ($_REQUEST[SMS_REMIND]=="on") { 
           	  $dao=D("User");
           	  $data[CONTENT]="请查看公告通知！\n标题：".csubstr($_POST[SUBJECT],0,100);
           	  $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
           	  if(compare_date(date("Y-m-d",$_POST[BEGIN_DATE]),$this->CUR_DATE)!=0)
                  $data[SEND_TIME]=date("Y-m-d H:i:s",$_POST[BEGIN_DATE]);
              if (find_id($_POST[TO_ID],'ALL_DEPT')) {
                 $list=$dao->findall();           		
           	  }else {
                 $list=$dao->where("InStr('$_POST[TO_ID]',CONCAT(',',DEPT_ID,','))>0 or InStr('$_POST[TO_ID]',concat(DEPT_ID,','))=1")->findall();  
           	  }
           	    $dao=D("Sms");
           	    $data[FROM_ID]=$this->LOGIN_USER_ID;
           	  foreach ($list as $row){
           	   	$data[TO_ID]=$row[USER_ID];
           	   	$data[SMS_TYPE]=1;
           	   	$data[REMIND_FLAG]=1;
           	   	$dao->add($data); 
           	  }
         } 
		  $this->assign('jumpUrl',__URL__."/index"); 
		  $this->success('操作已成功!');	 	 
    }
/*******************删除数据***********************************************************/
	public function delete(){
		 $NOTIFY_ID=$_REQUEST[NOTIFY_ID];
		 ###删除附件
		 $dao=D("Notify");
		 $row=$dao->where("NOTIFY_ID='$NOTIFY_ID'")->find();		 
         $ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
         foreach ($ATTACHMENT_ID_ARRAY as $key=>$attid){
         	  if ($attid) {
                  $this->_deleteattach($attid);//删除
         	   } 
         }
         ###删除记录
        $dao->where("NOTIFY_ID='$NOTIFY_ID'")->delete(); 		
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('删除成功!');
	}
/*******************全部删除数据***********************************************************/
	public function deleteAll(){
		$dao=D("Notify");
		$list=$dao->findall();
		foreach ($list as $row){
			 ###删除附件
	         $ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
	         foreach ($ATTACHMENT_ID_ARRAY as $key=>$attid){
	         	  if ($attid) {
	                  $this->_deleteattach($attid);//删除
	         	   } 
	         }
	         ###删除记录
			 $dao->where("NOTIFY_ID='$row[NOTIFY_ID]'")->delete(); 
		}
		
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('清空成功!');
	}

/*----------------删除附件---------*/
	public function deleteAttach(){
         $NOTIFY_ID=$_REQUEST[NOTIFY_ID];
         $ATTACHMENT_ID=$_REQUEST[ATTACHMENT_ID];
         $from=$_REQUEST['from'];
         //print_r($_REQUEST);
         //EXIT;
         ##删除附件
         $this->_deleteattach($ATTACHMENT_ID);//删除
         
         ##更新公告附件
         $dao=D("Notify");
         $row=$dao->where("NOTIFY_ID='$NOTIFY_ID'")->find();
		 
         $ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
		 $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
         $ATTACHMENT_ID_NEW=$ATTACHMENT_NAME_NEW="";		                 
         //print_r($ATTACHMENT_ID_ARRAY);
         foreach ($ATTACHMENT_ID_ARRAY as $key=>$attid){
         	  if ($attid!=$ATTACHMENT_ID&&$attid) {
         	   	  $ATTACHMENT_ID_NEW.=$attid.",";
         	   	  $ATTACHMENT_NAME_NEW.=$ATTACHMENT_NAME_ARRAY[$key]."*";
         	   } 
         }
         //ECHO $ATTACHMENT_ID_NEW;EXIT;
         $dao->setField("ATTACHMENT_ID",$ATTACHMENT_ID_NEW,"NOTIFY_ID='$NOTIFY_ID'");
         $dao->setField("ATTACHMENT_NAME",$ATTACHMENT_NAME_NEW,"NOTIFY_ID='$NOTIFY_ID'");
         
         header("location:".__URL__."/$from/NOTIFY_ID/$NOTIFY_ID");
         
         //$this->redirect("add/EMAIL_ID/$EMAIL_ID/BOX_ID/$BOX_ID","email");
         	   	
		
	}	
	
/*---------------以下为查看部分-----------------*/	

/*******************用户查看数据***********************************************************/
	public function viewUser(){
		$from=$_GET['from'];
		
		$notify = new NotifyModel();
		$row = $notify->where("NOTIFY_ID='$_GET[NOTIFY_ID]'")->find();
		
		if ($row[ATTACHMENT_ID]){
			$daoatt=D("Attachments");
			$listatt=$daoatt->where("attid in (0".$row[ATTACHMENT_ID]."0)")->findall();
		}
		$this->assign("listatt",$listatt);

    if(!find_id($row[READERS],$this->LOGIN_USER_ID))
    {
       $row[READERS].=$this->LOGIN_USER_ID.",";
       $notify->setField("READERS",$row[READERS],"NOTIFY_ID='$_GET[NOTIFY_ID]'");
    }
    	
        $this->assign("from",$from);		
		$this->assign('row',$row);
		$this->display();
	}
	
/************************公告通知*******************************************************/
	public function notifyAll(){
		$notify = new NotifyModel();
 		$map="TO_ID=''"; 
		$count=$notify->count($map); 
		import("ORG.Util.Page");	
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '20';
		}
		$p = new Page($count,$listRows); 
		$list = $notify->where($map)->limit("$p->firstRow,$p->listRows")->order("NOTIFY_ID DESC")->findAll(); 
		$page=$p->show();
		$this->assign("page",$page); 
		$this->assign('list',$list);
		$this->display();
	}
/*************************部门通知*********************************************************/
   
	public function notifyDept(){
		$notify = new NotifyModel();
		$dao_uid=D("User");
	    $list_uid=$dao_uid->where("USER_ID='$this->LOGIN_USER_ID'")->find();
	    $list_userid="U_".$list_uid['uid'];
	    
	    $dao_d=D("Department");
   		$a=$dao_d->re_parent_deptid($this->LOGIN_DEPT_ID);
   		$b=explode(",",$a); 
   		foreach ($b as $c){
   			$d[] = "InStr(TO_ID,'D_$c')>0";
   		}
   		$list_deptid=implode(" or ",$d);  
	    //$list_deptid="D_".$this->LOGIN_DEPT_ID;
	    $list_privid="P_".$this->LOGIN_USER_PRIV;
		$map="InStr(TO_ID,'$list_userid')>0 or InStr(TO_ID,'$list_privid')>0 or $list_deptid";   
		$count=$notify->count($map); 
		import("ORG.Util.Page");	
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '20';
		}
		$p = new Page($count,$listRows); 
		$list = $notify->where($map)->limit("$p->firstRow,$p->listRows")->order("NOTIFY_ID desc")->findAll(); 
		$page=$p->show();
		$this->assign("page",$page); 
		$this->assign('list',$list);
		$this->display();
	}
	
	
	/*-------------以下无用-可以删除------------*/
/*******************获得部门名称***********************************************************/
	private function deptName($TO_ID){
		$a = substr($TO_ID,0,strlen($TO_ID)-1);
		$department= new DepartmentModel();
		$list = $department->findAll("DEPT_ID IN ($a)");
		$name="";
		foreach($list as $v){
			$name.=$v['DEPT_NAME'].',';
		}
		return $name;
	}
/***********************获取状态******************************************************/
	private function timeStatus($beginDate,$endDate){
		$curDate = $this->CUR_TIME_INT;
		if($beginDate>$curDate){
			$notifyStatus=1;
		}else{
			$notifyStatus=2;
		}
		if(!empty($endDate)&&$endDate<$curDate){		
			$notifyStatus=3;
		}
		return $notifyStatus;		
	}
/***********************获取状态******************************************************/
	private function timeStatusStr($beginDate,$endDate){
		$curDate = $this->CUR_TIME_INT;
		if($beginDate>$curDate){
			$notifyStatus="待生效";
		}else{
			$notifyStatus="生效";
		}
		if(!empty($endDate)&&$endDate<$curDate){		
			$notifyStatus="终止";
		}
		return $notifyStatus;	
	}
/********************部门方法类***********************************************************/
	private function dept_tree_list($deptId,$privOp){
		$department = new DepartmentModel();
		$department = $department->where("DEPT_PARENT=$deptId")->order(DEPT_NO)->findAll();
		$optionText="";
		$deepCount1=$deepCount;
		$deepCount.=" ";
		foreach ($department as $k=>$v){
			$count++;
			$deptId=$v['DEPT_ID'];
			$deptName=$v['DEPT_NAME'];
			$deptParent=$v['DEPT_PARENT'];
			if($privOp==1)
				$deptPriv=$this->is_dept_priv($deptId);
			else
				$deptPriv=1;
			
			$optionTextChild=$this->dept_tree_list($deptId,$privOp);
			if($deptPriv==1){
				$optionText.="
				<tr class=TableControl>
				<td class='menulines' id='".$deptId."' name='".$deptName."' onclick=javascript:click_dept('".$deptId."') style=cursor:hand>".$deepCount1."├".$deptName."</a></td>
				</tr>";
			}
			if($optionTextChild!="")
				$optionText.=$optionTextChild;
		}
		$deepCount=$deepCount1;
		return $optionText;
	}
}
?>
