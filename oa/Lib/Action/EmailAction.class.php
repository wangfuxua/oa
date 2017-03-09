<?php
/**
 * EmailAction.class.php
 * 功能：内部邮件管理
 * 
 * */
//import("@.Action.PublicAction");
//import("@.Util.userselect");
class EmailAction extends PublicAction {
	var $curtitle;
	function _initialize(){
		$this->curtitle="内部邮件";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
    /*--------------首页邮件统计-------------------*/
	public  function index(){
		$EMAIL_COUNT=0;//所有邮件
		$NEW_COUNT=0;//新邮件
		$EMAIL_SIZE=0;//邮件容量
		$daobox=D("EmailBox");
		$dao=D("Email");
        $daoatt=D("Attachments");
		//----收件箱----
		$map="BOX_ID=0 and TO_ID='$this->_uid' and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG='0'";//新邮件数量(收)
		$NEW_LETER_COUNT=$dao->count($map);
		$NEW_COUNT=$NEW_LETER_COUNT;
				
		$map="BOX_ID=0 and TO_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG='0'";//所有收件箱
		$list=$dao->where($map)->findall();
		 $INBOX_COUNT=0;//收件箱个数初始
         $EMAIL_SIZE1=0;//收件箱大小
         if ($list) {
			foreach ($list as $ROW){
				$INBOX_COUNT++;
				$EMAIL_SIZE1+=strlen($ROW[CONTENT])+strlen($ROW[SUBJECT]);
				$ATTACHMENT_ID_ARRAY=explode(",",$ROW[ATTACHMENT_ID]);
				foreach ($ATTACHMENT_ID_ARRAY as $attid){
                      if($attid){
                      	$att=$daoatt->where("attid='$attid'")->find();
                      	$EMAIL_SIZE1+=$att['filesize'];
                      }
				}
			}
		 }
		  $EMAIL_SIZE1_M=number_format($EMAIL_SIZE1/1024/1024,2, ".",",");
		  $EMAIL_SIZE+=$EMAIL_SIZE1;
		  $EMAIL_SIZE1=number_format($EMAIL_SIZE1,0, ".",",");
		  
		 $this->assign("INBOX_COUNT",$INBOX_COUNT);
		 $this->assign("EMAIL_SIZE1",$EMAIL_SIZE1);
		 $this->assign("EMAIL_SIZE1_M",$EMAIL_SIZE1_M);
		 $this->assign("NEW_LETER_COUNT",$NEW_LETER_COUNT);
		 
		 //------发件箱（还未发出的）---
		 $map="FROM_ID='$this->_uid' and SEND_FLAG='0'  AND DELETE_FLAG='0'";
		 $list=$dao->where($map)->findall();
		  $OUTBOX_COUNT=0;
          $EMAIL_SIZE2=0;
         if ($list) {
			foreach ($list as $ROW){
				$OUTBOX_COUNT++;
				$EMAIL_SIZE2+=strlen($ROW[CONTENT])+strlen($ROW[SUBJECT]);
				$ATTACHMENT_ID_ARRAY=explode(",",$ROW[ATTACHMENT_ID]);
				foreach ($ATTACHMENT_ID_ARRAY as $attid){
                      if($attid){
                      	$att=$daoatt->where("attid='$attid'")->find();
                      	$EMAIL_SIZE2+=$att['filesize'];
                      }
				}
			}
		 }
		 $EMAIL_SIZE2_M=number_format($EMAIL_SIZE2/1024/1024,2, ".",",");
		 $EMAIL_SIZE+=$EMAIL_SIZE2;
		 $EMAIL_SIZE2=number_format($EMAIL_SIZE2,0, ".",",");		 		
		 
		 $this->assign("OUTBOX_COUNT",$OUTBOX_COUNT);
		 $this->assign("EMAIL_SIZE2",$EMAIL_SIZE2);
		 $this->assign("EMAIL_SIZE2_M",$EMAIL_SIZE2_M);
		 //-------已发送邮件箱
		 $mapall="FROM_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG!='2'";
		 $SENTBOX_COUNT=$dao->count($mapall);//总数
		 
		 $map="FROM_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG='1'";
		 $SENTBOX_COUNT1=$dao->count($map);
		 
		 $list=$dao->where($mapall)->findall();
		 $EMAIL_SIZE3=0;
         if ($list) {
			foreach ($list as $ROW){
				$EMAIL_SIZE3+=strlen($ROW[CONTENT])+strlen($ROW[SUBJECT]);
				$ATTACHMENT_ID_ARRAY=explode(",",$ROW[ATTACHMENT_ID]);
				foreach ($ATTACHMENT_ID_ARRAY as $attid){
                      if($attid){
                      	$att=$daoatt->where("attid='$attid'")->find();
                      	$EMAIL_SIZE3+=$att['filesize'];
                      }
				}
			}
		 }
		 $EMAIL_SIZE3_M=number_format($EMAIL_SIZE3/1024/1024,2, ".",",");
		 $EMAIL_SIZE+=$EMAIL_SIZE3;
		 $EMAIL_SIZE3=number_format($EMAIL_SIZE3,0, ".",",");
		 
		 
		 $this->assign("SENTBOX_COUNT",$SENTBOX_COUNT);
		 $this->assign("EMAIL_SIZE3",$EMAIL_SIZE3);
		 $this->assign("EMAIL_SIZE3_M",$EMAIL_SIZE3_M);
		 $this->assign("SENTBOX_COUNT1",$SENTBOX_COUNT1);
         //----------已删除邮箱
         $mapall="DELETE_USER='$this->_uid' and DELETE_FLAG='1'";
         $DELETEBOX_COUNT=$dao->count($mapall);//总数
         
		 $list=$dao->where($mapall)->findall();
		 $EMAIL_SIZE4=0;
         if ($list) {
			foreach ($list as $ROW){
				$EMAIL_SIZE4+=strlen($ROW[CONTENT])+strlen($ROW[SUBJECT]);
				$ATTACHMENT_ID_ARRAY=explode(",",$ROW[ATTACHMENT_ID]);
				foreach ($ATTACHMENT_ID_ARRAY as $attid){
                      if($attid){
                      	$att=$daoatt->where("attid='$attid'")->find();
                      	$EMAIL_SIZE4+=$att['filesize'];
                      }
				}
			}
		 }
		 $EMAIL_SIZE4_M=number_format($EMAIL_SIZE4/1024/1024,2, ".",",");
		 $EMAIL_SIZE+=$EMAIL_SIZE4;
		 $EMAIL_SIZE4=number_format($EMAIL_SIZE4,0, ".",",");
		 
		 $EMAIL_COUNT=$INBOX_COUNT+$OUTBOX_COUNT+$SENTBOX_COUNT+$DELETEBOX_COUNT;
		 
		 $this->assign("DELETEBOX_COUNT",$DELETEBOX_COUNT);
		 $this->assign("EMAIL_SIZE4",$EMAIL_SIZE4);
		 $this->assign("EMAIL_SIZE4_M",$EMAIL_SIZE4_M);		          
         
		 //----------自定义邮箱
		 $map="uid='$this->_uid'";
		 $list=$daobox->where($map)->order("BOX_NO")->findall();
		 if ($list) {
		 	foreach ($list as $key=>$ROW){
		 		$map="BOX_ID='$ROW[BOX_ID]' and TO_ID='$this->_uid' and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG='0'";
                $ROW[NEW_LETER_COUNT]=$dao->count($map);
                
                $map="BOX_ID='$ROW[BOX_ID]' and TO_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG='0'";
                $listbox=$dao->where($map)->findall();
		 		    $ROW[BOX_COUNT]=0;
                    $ROW[EMAIL_SIZE1]=0;
                    if ($listbox) {
                        	foreach ($listbox as $rows){
                                $ROW[BOX_COUNT]++;
                                $ROW[EMAIL_SIZE1]+=strlen($rows[CONTENT])+strlen($rows[SUBJECT]);
                                $ATTACHMENT_ID_ARRAY=explode(",",$rows[ATTACHMENT_ID]);
                                foreach ($ATTACHMENT_ID_ARRAY as $attid){
				                      if($attid){
				                      	$att=$dao->where("attid='$attid'")->find();
				                      	$ROW[EMAIL_SIZE1]+=$att['filesize'];
				                      }
								}
                                    		
                        	}
                        }
			    $ROW[EMAIL_SIZE1_M]=number_format($ROW[EMAIL_SIZE1]/1024/1024,2, ".",",");
			    $EMAIL_SIZE+=$ROW[EMAIL_SIZE1];
			    $ROW[EMAIL_SIZE1]=number_format($ROW[EMAIL_SIZE1],0, ".",",");                            
			   
			    $EMAIL_COUNT+=$ROW[BOX_COUNT];
			    $NEW_COUNT+=$ROW[NEW_LETER_COUNT];
			    
			    $list[$key][BOX_COUNT]=$ROW[BOX_COUNT];
			    $list[$key][NEW_LETER_COUNT]=$ROW[NEW_LETER_COUNT];
			    $list[$key][EMAIL_SIZE1]=$ROW[EMAIL_SIZE1];
			    $list[$key][EMAIL_SIZE1_M]=$ROW[EMAIL_SIZE1_M];
		 	}
		 }
		 $this->assign("list",$list);
         
		  //----------合计
		  $daopara=D("SysPara");
		  $map="PARA_NAME='EMAIL_CAPACITY'";
		  $ROW=$daopara->where($map)->find();
		  $EMAIL_CAPACITY=$ROW["PARA_VALUE"];
		  
		  $this->assign("EMAIL_CAPACITY",$EMAIL_CAPACITY);
		  
		  $EMAIL_SIZE_M=number_format($EMAIL_SIZE/1024/1024,2, ".",",");
		  $EMAIL_SIZE=number_format($EMAIL_SIZE,0, ".",",");
  		  
		  $this->assign("EMAIL_SIZE",$EMAIL_SIZE);
		  $this->assign("EMAIL_SIZE_M",$EMAIL_SIZE_M);
		  $this->assign("EMAIL_COUNT",$EMAIL_COUNT);
		  $this->assign("NEW_COUNT",$NEW_COUNT);
		$this->display();
	}
	
	protected function strtoarr($str){
		$dao_u=D("User");//按个人选择人员 
		$dao_d=D("Department");//按部门选择人员
		$dao_p=D("UserPriv");//按角色选择人员
		$TO_USER_ID=explode(",",$str);
		$arr="";
        foreach ($TO_USER_ID as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if ($b=="D"){ 
            			$row_d = $dao_d->where("DEPT_ID='$c'")->find();
      				    $arr[$key][id]="D_".$row_d[DEPT_ID];
      				    $arr[$key][name]=$row_d[DEPT_NAME];            			
            		}
            		if ($b=="P"){ 
            			$row_p = $dao_p->where("USER_PRIV='$c'")->find();
      				    $arr[$key][id]="P_".$row_p[USER_PRIV];
      				    $arr[$key][name]=$row_p[PRIV_NAME];            			
 					}
 					if ($b=="U"){ 
            			$row_u = $dao_u->where("uid='$c'")->find();
      				    $arr[$key][id]="U_".$row_u[uid];
      				    $arr[$key][name]=$row_u[USER_NAME]; 
  					}  	 	       
        }
        return  $arr;
	}

	/*-----------------写新邮件**回复邮件**转发邮件**----------------*/
	public function add(){
		####选择用户
		//UserSelectAction::DeptUserSelectAll();
		####选择部门
#########弹出框开始#########  
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        	$this->assign("list_d",$list_d);
        	$this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
#########弹出框结束#########
		
//		UserSelectAction::DeptSelect();		
        ####选择部门
		//UserSelectAction::DeptSelect();
		 
		if($_REQUEST[TO_ID]!="")
		{
		   $ROW[TO_ID]=$_REQUEST[TO_ID].",";
		   $ROW[TO_NAME]=$_REQUEST[TO_NAME].",";
		}
		
		if($_REQUEST[EMAIL_ID]!=""){
			$dao=D("Email");
			$ROW=$dao->where("EMAIL_ID='$_REQUEST[EMAIL_ID]'")
			         ->find();
			 if($_REQUEST[FW]=="1"){//转发
			    $ROW[TO_ID]="";
			    $ROW[COPY_TO_ID]="";
			    $ROW[SUBJECT]="Fw: ".$ROW[SUBJECT];
			    $listatt=array();
			 }elseif($_REQUEST[REPLAY]=="0")//回复
			 {
			    $ROW[COPY_TO_ID]="";
			    $ROW[SECRET_TO_ID]="";
			    $ROW[TO_ID]="U_".$ROW[FROM_ID].",";
			    
			    $daouser=D("User");
			    $touser=$daouser->where("uid='$ROW[FROM_ID]'")->field("uid, USER_NAME")->find();
			    $tolist[0][id]="U_".$touser[uid];
			    $tolist[0][name]=$touser[USER_NAME];
			    $ROW[TO_NAME]=$touser[USER_NAME];
			    
			    
			    $copylist="";
			    $secretlist="";
                $this->assign("tolist",$tolist);
                $this->assign("copylist",$copylist);
                $this->assign("secretlist",$secretlist);
                $listatt=array();
			 }
			 elseif($_REQUEST[REPLAY]=="1")//回复全部
			 {
			    $ROW[SECRET_TO_ID]="";
			    $ROW[TO_ID2]=str_replace("$ROW[FROM_ID],","",$ROW[TO_ID2]);
			    $ROW[TO_ID]=$ROW[FROM_ID].",".$ROW[TO_ID2];
			    $ROW[TO_ID]=str_replace("$this->_uid,","",$ROW[TO_ID]);
			    $ROW[COPY_TO_ID]=str_replace("$this->_uid,","",$ROW[COPY_TO_ID]);
			    $ROW[COPY_TO_ID]=str_replace("$ROW[FROM_ID],","",$ROW[COPY_TO_ID]);
			    $listatt=array();
			 }else{//编辑
			 	$tolist=$this->strtoarr($ROW[TO_ID]);
			 	$copylist=$this->strtoarr($ROW[COPY_TO_ID]);
			 	$secretlist=$this->strtoarr($ROW[SECRET_TO_ID]);
			 	$ROW[TO_NAME]=getList_name($ROW[TO_ID]);
			 	
                $this->assign("tolist",$tolist);
                $this->assign("copylist",$copylist);
                $this->assign("secretlist",$secretlist);			 	
                if ($ROW[ATTACHMENT_ID]){
					$daoatt=D("Attachments");
					$listatt=$daoatt->where("attid in (0".$ROW[ATTACHMENT_ID]."0)")->findall();
				}
				$this->assign("listatt",$listatt);
                 
					
			 }

				$this->assign("curtitle","写邮件");
		}
		
		if($_REQUEST[REPLAY]!="")
		{
		   $ROW[ATTACHMENT_ID]="";
		   $ROW[ATTACHMENT_NAME]="";
		   $ROW[SUBJECT]="Re:".$ROW[SUBJECT];
		   $ROW[CONTENT]="<table border=0 cellspacing=10 cellpadding=0 height=135><tr><td width=2 bgcolor=#000000></td><td>".$ROW[CONTENT]."</td></tr></table>";
		   $HEAD=$ROW[TO_NAME]."您好！<br><br>";
		   if($ROW[CONTENT]!="")
		   {
		      $HEAD.="<br>========您在".$ROW[SEND_TIME]."的来信中写道：========<br>";
		      $TAIL="<br>=========================================";
		   }
		
		   $TAIL.="<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;此致<br>敬礼！";
		   $ROW[CONTENT]=$HEAD.$ROW[CONTENT].$TAIL;
		}

		
		$this->assign("EMAIL_ID",$_REQUEST[EMAIL_ID]);
		$this->assign("BOX_ID",$_REQUEST[BOX_ID]);
		
		$this->assign("FW",$_REQUEST[FW]);
		$this->assign("REPLAY",$_REQUEST[REPLAY]);
		
		if (!$_REQUEST[EMAIL_ID]) {
            $ROW[SMS_REMIND]="on"; 			
		}
		//echo $ROW[CONTENT];
		$this->assign("ROW",$ROW);
		$this->assign("upload_max_filesize",ini_get("upload_max_filesize"));
		$this->display();
	}
	
	public function submit(){
        //print_r($_REQUEST);
        //EXIT; 
		//$_POST[TO_ID]=$_POST[idarr];
		//EXIT;
		####如果附件太大，$_POST则为空;
		//$SMS_REMIND_CHECK=Cookie::set("COOKIE_SMS_REMIND",$_POST[SMS_REMIND]);
		//echo $_POST[TO_ID];
		/*
		if (empty($_POST[TO_ID])) {
            $this->error("提交失败，附件文件不能超过".ini_get("upload_max_filesize"));			
		}
		*/
		 //附件上传
				$a = implode(",",$_POST[ATTACHMENT_ID]);
				$a_old =  implode(",",$_POST[oldattid]);
				if($_POST[oldattid]&&$_POST[ATTACHMENT_ID]){
					$_POST[ATTACHMENT_ID]=$a_old.",".$a.",";
				}elseif($_POST[ATTACHMENT_ID]){
					$_POST[ATTACHMENT_ID]=$a.",";
				}elseif ($_POST[oldattid]){
					$_POST[ATTACHMENT_ID]=$a_old.",";
				}
				$b = implode("*",$_POST[ATTACHMENT_NAME]);
				$b_old =  implode("*",$_POST[oldattname]);
				if($_POST[oldattname]&&$_POST[ATTACHMENT_NAME]){
					$_POST[ATTACHMENT_NAME]=$b_old."*".$b."*";
				}elseif($_POST[ATTACHMENT_NAME]){
					$_POST[ATTACHMENT_NAME]=$b."*";
				}elseif ($_POST[oldattname]){
					$_POST[ATTACHMENT_NAME]=$b_old."*";
				}

		$_POST[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);//自动时间
		//------------------- 保存或发送 -----------------------
		$dao=D("Email");
        $_POST[FROM_ID]=$this->_uid;  
		if ($_POST[SEND_FLAG]==1) {//发送
			    $_POST[TO_ID2]=$_POST[TO_ID];
			    $TO_ID_STR=$_POST[TO_ID].",".$_POST[COPY_TO_ID].",".$_POST[SECRET_TO_ID];
			    //echo $TO_ID_STR."<br>";
			    $idarray=explode(",",$TO_ID_STR);
			    $daouser=D("User");
			    $toidstr="";
			    foreach ($idarray as $key=>$ids){
			    	  if ($ids){
			              $pre=substr($ids,0,2);
			              if ($pre=="U_") {//人员
			                 $toidstr.=substr($ids,2,strlen($ids)).",";
	                      }
			              if ($pre=="P_") {//角色
			                 $topriv=substr($ids,2,strlen($ids));
			                 $toidarr=$daouser->where("USER_PRIV='$topriv' and job_status=1")->field("uid")->findall();
			                  foreach ($toidarr as $row){
			                  	$toidstr.=$row[uid].",";
			                  }  	    		
	                      }
			              if ($pre=="D_") {//部门
			                 $todept=substr($ids,2,strlen($ids));
			                 $toidarr=$daouser->where("DEPT_ID='$todept' and job_status=1")->field("uid")->findall();
			                 //echo $daouser->getlastsql();
			                  foreach ($toidarr as $row){
			                  	$toidstr.=$row[uid].",";
			                  }		                    	    		
	                      }  
			    	  }                                         
			    }
			    
			    if ($_POST[REPLAY]!="") {//回复
			       	$_POST[EMAIL_ID]="";
			    }
			    if ($_POST[FW]!="") {//转发
			       	$_POST[EMAIL_ID]="";
			    }
			    /*			    
			    if($_POST[EMAIL_ID]!=""&&$_POST[REPLAY]==""&&$$_POST[FW]==""){
			   	   //$dao->where("EMAIL_ID='$_POST[EMAIL_ID]'")->save();
			   	   $_POST[EMAIL_ID]="";
			    }
			   */
			    
		        $TO_ID_STR=$toidstr;
			    $TOK=strtok($TO_ID_STR,",");
			    $emailid=$_POST[EMAIL_ID];
			    $_POST[EMAIL_ID]="";
			    
			    if ($emailid) {//如果是从草稿箱发送，则删除草稿箱邮件
			       $dao->setField("sentbox_flag","1","EMAIL_ID='$emailid'");
			       $dao->setField("SEND_FLAG","1","EMAIL_ID='$emailid'");
			       //$dao->where("EMAIL_ID='$emailid'")->delete();
			    }else{//保存本邮件到已发送邮箱
			    	$_POST[sentbox_flag]=1;
			        $dao->create();
			    	$dao->add();
			    	//echo $dao->getlastsql();
			    }
			    
			    $_POST[sentbox_flag]=0;
			    while($TOK!=""){
			    	$_POST[TO_ID]=$TOK;
			    	$dao->create();
			    	$dao->add();
				    if($_POST[SMS_REMIND]=="on")
				    {
				     $SMS_CONTENT="请查收我的邮件！\n主题：".csubstr($_POST[SUBJECT],0,100);
				     $this->send_sms($this->_uid,$TOK,2,$SMS_CONTENT);
				    }
			    	$TOK=strtok(",");
			    }
			    //exit;
          $this->assign("jumpUrl",__APP__."/WebMail/index");
		  $this->success("成功发送");
		}else {//保存
			   $dao->create();
			   if($_POST[EMAIL_ID]==""||$_POST[REPLAY]!=""||$$_POST[FW]!=""){
			   	   $id=$dao->add();
			   }else {
			   	   $dao->where("EMAIL_ID='$_POST[EMAIL_ID]'")->save();
			   	   $id=$_POST[EMAIL_ID];
			   }
			   	   $this->assign("jumpUrl",__APP__."/WebMail/index");
			   	   $this->success("成功保存");
		}
		//echo $dao->getlastsql();exit;
        // $this->redirect("index","Email");
	}

	
	public function outboxsend(){//发送发件箱的邮件
		$SEND_STR=$_REQUEST[SEND_STR];
		$dao=D("Email");
		$EMAIL_ID_ARRAY=explode(",",$SEND_STR);
		//$ARRAY_COUNT=sizeof($EMAIL_ID_ARRAY);
		$k=0;
		foreach ($EMAIL_ID_ARRAY as $EMAIL_ID){
			if ($EMAIL_ID) {
				$k++;
				$ROW=$dao->where("EMAIL_ID='$EMAIL_ID'")->find();
				   $ROW[TO_ID2]=$ROW[TO_ID];
				   $ROW[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
				   $TO_ID_STR=$ROW[TO_ID].$ROW[COPY_TO_ID].$ROW[SECRET_TO_ID];
                   $TOK=strtok($TO_ID_STR,",");
                   while ($TOK!=""){
                   	$ROW[FROM_ID]=$this->_uid;
                   	$ROW[TO_ID]=$TOK;
                   	$ROW[READ_FLAG]=0;
                   	$ROW[DELETE_FLAG]=0;
                   	$ROW[SEND_FLAG]=1;
                   	$dao->create();
                   	$dao->add($ROW);
                   	if ($ROW[SMS_REMIND]==1&&$ROW[SEND_FLAG]==1){
                   		$this->send_sms($this->_uid,$TOK,2,"请查收我的邮件！\n主题：".csubstr($ROW[SUBJECT],0,100));
                   	}
                   	$TOK=strtok(",");
                   }
				$dao->where("EMAIL_ID='$EMAIL_ID'")->delete();
			}
			
		}
		$this->assign("jumpUrl",__URL__."/outbox");
		$this->success("成功发送出".$k."封邮件");
        //$this->redirect("outbox","Email");		
	}
	/**
	 * 收件箱
	 * */
	public function inbox(){//收件箱
		$BOX_ID=intval($_REQUEST[BOX_ID]);
		$load=$_REQUEST[load];
		if (!$BOX_ID) {
			$BOX_ID=0;
		}
		if ($BOX_ID) {
			$map="BOX_ID=$BOX_ID and TO_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG='0' and sentbox_flag=0";
			$map2="a.BOX_ID=$BOX_ID and a.TO_ID='$this->_uid'  and a.SEND_FLAG='1' and a.DELETE_FLAG='0' and a.sentbox_flag=0";
			$mapnew="BOX_ID=$BOX_ID and TO_ID='$this->_uid'  and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG='0' and sentbox_flag=0";			
		}else{
			$map="BOX_ID=$BOX_ID and TO_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG='0' and sentbox_flag=0";
			$map2="a.BOX_ID=$BOX_ID and a.TO_ID='$this->_uid'  and a.SEND_FLAG='1' and a.DELETE_FLAG='0' and a.sentbox_flag=0";
			$mapnew="BOX_ID=$BOX_ID and TO_ID='$this->_uid'  and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG='0' and sentbox_flag=0";
		}
		$dao=D("Email");
	    $count=$dao->count($map);//总数
	   // echo $dao->getlastsql();
	    $new_count=$dao->count($mapnew);//新邮件
	        if($count>0){
	        	import("@.Util.mailpage");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				if ($load) {
				$p          = new mailpage($count,$listRows);	
				}else {
				$p          = new Page($count,$listRows);		
				}
				//分页查询数据
                $list=$dao->table("email as a")
                          ->join("user as b on a.FROM_ID=b.uid")
                          ->join("department as c on b.DEPT_ID=c.DEPT_ID")
                          ->field("a.*,b.USER_NAME as FROM_NAME,b.AVATAR,c.DEPT_NAME")
                          ->order("a.SEND_TIME desc")
                          ->limit("$p->firstRow,$p->listRows")
                          ->where($map2)
                          ->findall();
                          //echo $dao->getlastsql();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
			
	        }
	    $daobox=D("EmailBox");
	    $row=$daobox->where("BOX_ID='$BOX_ID'")->find();
	    if ($BOX_ID==0){
	    	$row[BOX_NAME]="收件箱";
	    }
	    
	    $boxlist=$daobox->where("uid='$this->_uid'")->order("BOX_NO")->findall();
	    $this->assign("boxlist",$boxlist);
	    $this->assign("curtitle",$row[BOX_NAME]);
	    $this->assign("row",$row);
        $this->assign("NEW_COUNT",$new_count); 
        $this->assign("COUNT",$count);
        $this->assign("BOX_ID",$BOX_ID);
        $this->assign("folder",$_REQUEST[folder]);
		$this->display();
		
	}
		
	/**
	 * 发件箱
	 * */
	public function outbox(){//发件箱
		$BOX_ID=$_REQUEST[BOX_ID];
		if (!$BOX_ID) {
			$BOX_ID=0;
		}
		$map="FROM_ID='$this->_uid' AND SEND_FLAG='0' AND DELETE_FLAG='0' and sentbox_flag=0";
		$map2="a.FROM_ID='$this->_uid' and a.SEND_FLAG='0' AND a.DELETE_FLAG='0' and a.sentbox_flag=0";
		$dao=D("Email");
	    $count=$dao->count($map);//总数
	        if($count>0){
	            import("@.Util.mailpage");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p          = new mailpage($count,$listRows);
                $list=$dao->order("SEND_TIME desc")
                          ->where($map)
                          ->limit("$p->firstRow,$p->listRows")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        
	    $daobox=D("EmailBox");    
	    $boxlist=$daobox->where("uid='$this->_uid'")->order("BOX_NO")->findall();
	    $this->assign("boxlist",$boxlist);
	    	        
    	$row[BOX_NAME]="草稿箱";
	    $this->assign("row",$row);
	    $this->assign("curtitle",$row[BOX_NAME]);
        $this->assign("COUNT",$count);
        $this->assign("BOX_ID",$BOX_ID);
        $this->assign("folder",$_REQUEST[folder]);
		$this->display();
	}
	/**
	 * 已发送邮件箱
	 * */	
	public function sentbox(){
		$BOX_ID=$_REQUEST[BOX_ID];
		if (!$BOX_ID) {
			$BOX_ID=0;
		}
		//print_rr($_REQUEST);
		
		$map="FROM_ID='$this->_uid' AND SEND_FLAG='1' and DELETE_FLAG='0' and sentbox_flag=1";
		$map2="a.FROM_ID='$this->_uid' and a.SEND_FLAG='1' and a.DELETE_FLAG='0' and a.sentbox_flag=1";
		$dao=D("Email");
	    $count=$dao->count($map);//总数
	        if($count>0){
	            import("@.Util.mailpage");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p          = new Page($count,$listRows);
				//分页查询数据
                $list=$dao->table("email as a")
                          ->join("left join user as b on a.TO_ID=b.uid left join department as c on b.DEPT_ID=c.DEPT_ID")
                          ->field("a.*,b.USER_NAME as TO_NAME,b.AVATAR,c.DEPT_NAME")
                          ->order("a.SEND_TIME desc")
                          ->where($map2)
                          ->limit("$p->firstRow,$p->listRows")
                          ->findall();
 			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	       }
	    
	    $daobox=D("EmailBox");      
	    $boxlist=$daobox->where("uid='$this->_uid'")->order("BOX_NO")->findall();
	    $this->assign("boxlist",$boxlist);
	    
    	$row[BOX_NAME]="已发送邮件箱";
	    $this->assign("row",$row);
        $this->assign("curtitle",$row[BOX_NAME]);
        $this->assign("COUNT",$count);
        $this->assign("BOX_ID",$BOX_ID);
        $this->assign("folder",$_REQUEST[folder]);
		$this->display();
	}
	/**
	 * 已删除邮件
	 * */
    public function deletebox(){
		$BOX_ID=$_REQUEST[BOX_ID];
		if (!$BOX_ID) {
			$BOX_ID=0;
		}
		$map="DELETE_USER='$this->_uid' and DELETE_FLAG='1'";
		$map2="a.DELETE_USER='$this->_uid' and a.DELETE_FLAG='1'";
		$dao=D("Email");
	    $count=$dao->count($map);//总数
	    //echo $dao->getlastsql();
	        if($count>0){
	            import("@.Util.mailpage");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p          = new mailpage($count,$listRows);
				//分页查询数据
                $list=$dao->table("email as a")
                          ->join("left join user as b on a.FROM_ID=b.uid left join department as c on b.DEPT_ID=c.DEPT_ID")
                          ->field("a.*,b.USER_NAME as FROM_NAME,b.AVATAR,c.DEPT_NAME")
                          ->order("a.EMAIL_ID desc")
                          ->where($map2)
                          ->limit("$p->firstRow,$p->listRows")
                          ->findall();
 			//分页显示
 			//echo $dao->getlastsql();
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	       }
	       
	    $daobox=D("EmailBox");   
	    $boxlist=$daobox->where("uid='$this->_uid'")->order("BOX_NO")->findall();
	    $this->assign("boxlist",$boxlist);
	    
    	$row[BOX_NAME]="已删除邮件";
	    $this->assign("row",$row);
        $this->assign("curtitle",$row[BOX_NAME]);
        $this->assign("COUNT",$count);
        $this->assign("BOX_ID",$BOX_ID);
        $this->assign("folder",$_REQUEST[folder]);
		$this->display(); 	
    	
    }

	/*------------------导出-------------------*/
	public function export(){
		####内容
		$EMAIL_ID=$_REQUEST[EMAIL_ID];
		$dao=D("Email");
		$ROW=$dao->where("EMAIL_ID=$EMAIL_ID")->find();

	    $ROW[SUBJECT]=str_replace("<","&lt",$ROW[SUBJECT]);
	    $ROW[SUBJECT]=str_replace(">","&gt",$ROW[SUBJECT]);
	    $ROW[SUBJECT]=stripslashes($ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace(":","：",$ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace("\\","",$ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace("/","",$ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace("*","",$ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace("?","",$ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace("\"","“",$ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace("<","[",$ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace(">","]",$ROW[SUBJECT]);
		 $ROW[SUBJECT]=str_replace("|","",$ROW[SUBJECT]);
		 	    
	    $dao=D("User");
	    $user=$dao->where("USER_ID='$ROW[FROM_ID]'")->find();
	    $ROW[FROM_NAME]=$user["USER_NAME"];
	    
	    $ROW[TO_NAME]="";
	    $TOK=strtok($TO_ID2,",");
	    while($TOK!="")
	    {
		    $dao=D("User");
		    $user=$dao->where("USER_ID='$TOK'")->find();
	          $ROW[TO_NAME].=$user["USER_NAME"].",";
	       $TOK=strtok(",");
	    }
	    
	    $ROW[COPY_TO_NAME]="";
	    $TOK=strtok($COPY_TO_ID,",");
	    while($TOK!="")
	    {
		    $dao=D("User");
		    $user=$dao->where("USER_ID='$TOK'")->find();
	          $ROW[COPY_TO_NAME].=$user["USER_NAME"].",";
	       $TOK=strtok(",");
	    }
	
	    $ROW[CONTENT]=str_replace("  ","&nbsp;&nbsp;",$ROW[CONTENT]);
	    $ROW[CONTENT]=str_replace("\n","<br>",$ROW[CONTENT]);
    		

		 if(substr($ROW[TO_NAME],-1)==",")
		    $ROW[TO_NAME]=substr($ROW[TO_NAME],0,-1);
		 if(substr($ROW[COPY_TO_NAME],-1)==",")
		    $ROW[COPY_TO_NAME]=substr($ROW[COPY_TO_NAME],0,-1);
		 
		 if(substr($ROW[TO_ID],-1)==",")
		    $ROW[TO_ID]=substr($ROW[TO_ID],0,-1);
		 if(substr($ROW[COPY_TO_ID],-1)==",")
		    $ROW[COPY_TO_ID]=substr($ROW[COPY_TO_ID],0,-1);
		 
		 if($ROW[TO_ID]!=$this->_uid&&$ROW[FROM_ID]!=$this->_uid)
		 {
		 	$this->error("只能导出自己的邮件！");
		 }
		 



		$ATTACHMENT_ID_ARRAY=explode(",",$ROW[ATTACHMENT_ID]);
		$ATTACHMENT_NAME_ARRAY=explode("*",$ROW[ATTACHMENT_NAME]);	    
		$ARRAY_COUNT=sizeof($ATTACHMENT_ID_ARRAY);
		//ECHO $ARRAY_COUNT;
		for($I=0;$I<$ARRAY_COUNT-1;$I++)
		{
            if($ATTACHMENT_ID_ARRAY[$I]=="")
               continue;
            $dao=D("Attachments");
            $att=$dao->where("attid='$ATTACHMENT_ID_ARRAY[$I]'")->find();
            $ATTACH_SIZE=$att[filesize];
            $ATTACH_SIZE=number_format($ATTACH_SIZE,0, ".",",");
            $filename=$this->uploadpath.$att[attachment];
		   $list[$I][name]=$att[filename];
		}

		$this->assign("list",$list);
		$this->assign("ROW",$ROW);
		
		$row[SUBJECT] = preg_replace("/[[:space:]]/","",$row[SUBJECT]);
		$row[SUBJECT] = ereg_replace("[[:space:]]","",$row[SUBJECT]);
			    		 
		###导出
		header("Content-type: text/html; charset=utf-8");   
		Header("Content-type: application/eml");
		Header("Content-Disposition: attachment; filename= ".urlencode($ROW[SUBJECT]).".eml");	

        $this->display();		
		 
		
	}

	/*-------------------读邮件----------------*/
	public function inboxRead(){//读邮件
		$EMAIL_ID=$_REQUEST[EMAIL_ID];
		$BOX_ID=$_REQUEST[BOX_ID];
		$dao=D("Email");
		$dao->setField("READ_FLAG","1","EMAIL_ID='$EMAIL_ID'");
		$row=$dao->where("EMAIL_ID='$EMAIL_ID'")->find();
	    $daouser=D("User");		
		$user=$daouser->where("uid='$row[FROM_ID]'")->find();
		//$row[FROM_NAME].=$user["USER_NAME"];
        $row[FROM_NAME]="<u title=\"部门：".$this->get_dept($user[DEPT_ID])."\" style=\"cursor:hand\">".$user["USER_NAME"]."</u>";
        
			  $TOK=strtok($row[TO_ID],",");
			   while($TOK!=""){
			   	    $user=$daouser->where("USER_ID='$TOK'")->find();
			   	    //$row[TO_NAME].=$user["USER_NAME"].",";
                    $row[TO_NAME]="<u title=\"部门：".$this->get_dept($user[DEPT_ID])."\" style=\"cursor:hand\">".$user["USER_NAME"]."</u>,";			   	    
				    $TOK=strtok(",");
				}
				$TOK=strtok($row[COPY_TO_ID],",");
			   while($TOK!=""){
			   	    $user=$daouser->where("USER_ID='$TOK'")->find();
			   	    //$row[COPY_TO_NAME].=$user["USER_NAME"].",";
                    $row[COPY_TO_NAME]="<u title=\"部门：".$this->get_dept($user[DEPT_ID])."\" style=\"cursor:hand\">".$user["USER_NAME"]."</u>,";			   	    			   	    
				    $TOK=strtok(",");
				}
				
				$TOK=strtok($row[SECRET_TO_ID],",");
			   while($TOK!=""){
			   	    $user=$daouser->where("USER_ID='$TOK'")->find();
			   	    //$row[SECRET_TO_NAME].=$user["USER_NAME"].",";
			   	    $row[SECRET_TO_NAME]="<u title=\"部门：".$this->get_dept($user[DEPT_ID])."\" style=\"cursor:hand\">".$user["USER_NAME"]."</u>,";			   	    			   	    
				    $TOK=strtok(",");
				}
				
		$row[att]=email_att($row[EMAIL_ID],1);
		$this->assign("BOX_ID",$BOX_ID);
		$this->assign("from",$_REQUEST['from']);
		$this->assign("row",$row);
		$this->assign("curtitle","读邮件");
		
		
		##上下封
		   	
		   $str="";	
		   $from=$_REQUEST['from'];   
		   $dao=D("Email");	
		if ($from=="sentbox") {
		   $mappre="FROM_ID='$this->_uid' AND SEND_FLAG='1' and DELETE_FLAG='0' and EMAIL_ID>$EMAIL_ID";
		   $row=$dao->where($mappre)->field("EMAIL_ID")->order("SEND_TIME asc")->find();
		   if ($row[EMAIL_ID]) {
		   $str.="<a href='#' onclick='javascript:$(\"#mail-act\").load(\"/index.php/Email/inboxRead/EMAIL_ID/$row[EMAIL_ID]/BOX_ID/$BOX_ID/from/$from\")'>上一封</a>";
		   }
		   $mapnext="FROM_ID='$this->_uid' AND SEND_FLAG='1' and DELETE_FLAG='0' and EMAIL_ID<$EMAIL_ID";
		   $row=$dao->where($mapnext)->field("EMAIL_ID")->order("SEND_TIME desc")->find();
		   if ($row[EMAIL_ID]) {
		   $str.="<a href='#' onclick='javascript:$(\"#mail-act\").load(\"/index.php/Email/inboxRead/EMAIL_ID/$row[EMAIL_ID]/BOX_ID/$BOX_ID/from/$from\")'>下一封</a>";		   
		   }
		   		
		}elseif ($from=="deletebox"){
		   $mappre="DELETE_USER='$this->_uid' and DELETE_FLAG='1' and EMAIL_ID>$EMAIL_ID";
		   $row=$dao->where($mappre)->field("EMAIL_ID")->order("SEND_TIME asc")->find();
		   if ($row[EMAIL_ID]) {
		   $str.="<a href='#'  onclick='javascript:$(\"#mail-act\").load(\"/index.php/Email/inboxRead/EMAIL_ID/$row[EMAIL_ID]/BOX_ID/$BOX_ID/from/$from\")'>上一封</a>";
		   }
		   
		   $mapnext="DELETE_USER='$this->_uid' and DELETE_FLAG='1' and EMAIL_ID<$EMAIL_ID";
		   $row=$dao->where($mapnext)->field("EMAIL_ID")->order("SEND_TIME desc")->find();
		   if ($row[EMAIL_ID]) {
		   $str.="<a href='#'  onclick='javascript:$(\"#mail-act\").load(\"/index.php/Email/inboxRead/EMAIL_ID/$row[EMAIL_ID]/BOX_ID/$BOX_ID/from/$from\")'>下一封</a>";		   
		   }
		   		   
		}else {
		   $mappre="BOX_ID=$BOX_ID and TO_ID='$this->_uid'  and SEND_FLAG='1' and DELETE_FLAG='0' and EMAIL_ID>$EMAIL_ID";
		   $row=$dao->where($mappre)->field("EMAIL_ID")->order("SEND_TIME asc")->find();
		   if ($row[EMAIL_ID]) {
		   $str.="<a href='#'  onclick='javascript:$(\"#mail-act\").load(\"/index.php/Email/inboxRead/EMAIL_ID/$row[EMAIL_ID]/BOX_ID/$BOX_ID\")'>上一封</a>";
		   }
		   
		   $mapnext="BOX_ID=$BOX_ID and TO_ID='$this->_uid'  and SEND_FLAG='1' and DELETE_FLAG='0' and EMAIL_ID<$EMAIL_ID";		   
		   $row=$dao->where($mapnext)->field("EMAIL_ID")->order("SEND_TIME desc")->find();
		   if ($row[EMAIL_ID]) {
		   $str.="<a href='#'  onclick='javascript:$(\"#mail-act\").load(\"/index.php/Email/inboxRead/EMAIL_ID/$row[EMAIL_ID]/BOX_ID/$BOX_ID\")'>下一封</a>";
		   }
				
		}
	
		$this->assign("nextpre",$str);
		
		
		$this->display();
	}
    
	/**
	 * 全部删除某一邮件夹所有邮件
	 * */	
	public function deleteall(){
		$BOX_ID=$_REQUEST[BOX_ID];
		$from=$_REQUEST['from'];
		$dao=D("Email");
//		$map="BOX_ID=$BOX_ID and TO_ID='$this->_uid'";
		
		if ($from=="inbox") {
			$map="BOX_ID='0' and TO_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG='0' and sentbox_flag=0";
			$data[DELETE_USER]=$this->_uid;
			$data[DELETE_FLAG]=1;
			$dao->where($map)->save($data);
			
			//$dao->setField("DELETE_USER",$this->_uid,$map);
			//$dao->setField("DELETE_FLAG","1",$map);
			
		}elseif ($from=="outbox"){
			$map="FROM_ID='$this->_uid' AND SEND_FLAG='0' AND DELETE_FLAG='0' and sentbox_flag=0";
			$data[DELETE_USER]=$this->_uid;
			$data[DELETE_FLAG]=1;
			$dao->where($map)->save($data);
			
			//$dao->setField("DELETE_USER",$this->_uid,$map);			
			//$dao->setField("DELETE_FLAG","1",$map);
			
		}elseif ($from=="sentbox"){
			$map="FROM_ID='$this->_uid' AND SEND_FLAG='1' and DELETE_FLAG='0' and sentbox_flag=1";
			$data[DELETE_USER]=$this->_uid;
			$data[DELETE_FLAG]=1;
			$dao->where($map)->save($data);
						
			//$dao->setField("DELETE_USER",$this->_uid,$map);
			//$dao->setField("DELETE_FLAG","1",$map);
						
		}elseif ($from=="deletebox"){
            $map="DELETE_USER='$this->_uid' and DELETE_FLAG='1'";
			$dao->where($map)->delete();
		}
		//$dao->where($map)->delete();
	}

	/*------------------删除邮件------------------*/
	public function delete(){//删除
		$DELETE_STR=$_REQUEST[DELETE_STR];
		$BOX_ID=$_REQUEST[BOX_ID];
		$ret=$_REQUEST[ret];
		//echo $DELETE_STR;exit;
		$from=$_REQUEST["from"];
		$TOK=strtok($DELETE_STR,",");
		while($TOK!=""){
		   $daos=new Model();
		   $row=$daos->table("email")->where("EMAIL_ID='$TOK'")->field("EMAIL_ID,DELETE_FLAG")->find();
		   //
		    if ($row[DELETE_FLAG]==0) {//设删除标志
		        $dao=D("Email");
		        $data[DELETE_FLAG]=1;
		        $data[DELETE_USER]=$this->_uid;
		        $dao->where("EMAIL_ID='$TOK'")->save($data);
		        
		     	//$dao->setField("DELETE_FLAG","1","EMAIL_ID='$TOK'");
		    	//$dao->setField("DELETE_USER",$this->_uid,"EMAIL_ID='$TOK'");
		    	//echo $dao->getlastsql();
		    }else{//物理删除
		    	$ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
		        $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
				foreach ($ATTACHMENT_ID_ARRAY as $attid){
					 $this->_deleteattach($attid);
				}
				$dao=D("Email");
		    	$dao->where("EMAIL_ID='$TOK'")->delete();
		    } 
           $TOK=strtok(",");			
		}
		if ($ret) {
			$this->redirect("inbox/BOX_ID/0","Email");
		}
		/*
		if ($from=="outbox") {
			$this->assign("jumpUrl",__URL__."/outbox/BOX_ID/$BOX_ID");
			$this->success("成功删除");
		    //$this->redirect($from."/BOX_ID/0","Email");
		}elseif ($from=="sentbox") {
			$this->assign("jumpUrl",__URL__."/sentbox/BOX_ID/$BOX_ID");
			$this->success("成功删除");
		    //$this->redirect($from."/BOX_ID/0","Email");
		}else{ 
			$this->assign("jumpUrl",__URL__."/inbox/BOX_ID/$BOX_ID");
			$this->success("成功删除");			
		    //$this->redirect("inbox/BOX_ID/0","Email");
		}
		*/
	}
	/*--------------------删除附件-------------------*/
	public function deleteattach(){
         $EMAIL_ID=$_REQUEST[EMAIL_ID];
         $BOX_ID=$_REQUEST[BOX_ID];
         $ATTACHMENT_ID=$_REQUEST[ATTACHMENT_ID];
         //print_r($_REQUEST);exit();
         ##删除附件
         $this->_deleteattach($ATTACHMENT_ID);//删除
         ##更新邮件附件
         if ($EMAIL_ID) {
	         $dao=D("Email");
	         $row=$dao->where("EMAIL_ID='$EMAIL_ID'")->find();
	         $ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
			 $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
	         $ATTACHMENT_ID_NEW=$ATTACHMENT_NAME_NEW="";		                 
	         foreach ($ATTACHMENT_ID_ARRAY as $key=>$attid){
	         	  if ($attid!=$ATTACHMENT_ID&&$attid) {
	         	   	  $ATTACHMENT_ID_NEW.=$attid.",";
	         	   	  $ATTACHMENT_NAME_NEW.=$ATTACHMENT_NAME_ARRAY[$key]."*";
	         	   } 
	         }
	         $dao->setField("ATTACHMENT_ID",$ATTACHMENT_ID_NEW,"EMAIL_ID='$EMAIL_ID'");
	         $dao->setField("ATTACHMENT_NAME",$ATTACHMENT_NAME_NEW,"EMAIL_ID='$EMAIL_ID'");
         }
	}
	/*-----------------转移邮件-----------------*/	
	public function change(){
		//print_rr($_REQUEST);
		//exit;
       $EMAIL_ID_STR=$_REQUEST[EMAIL_ID_STR];
       $BOX_ID=$_REQUEST[BOX_ID];
       $dao=D("Email");       
       if ($BOX_ID=="outbox") {//草稿箱
	       $data[BOX_ID]=0;
	       $data[DELETE_FLAG]=0;
	       $data[delete_user]="NULL";
	       $data[FROM_ID]=$this->_uid;
	       $data[SEND_FLAG]=0;
	       $dao->where("EMAIL_ID in (".$EMAIL_ID_STR."0)")->save($data);
	       $this->redirect("outbox","Email");
       }elseif ($BOX_ID=="sentbox"){//已发送
	       $data[BOX_ID]=0;
	       $data[DELETE_FLAG]=0;
	       $data[delete_user]="NULL";
	       $data[FROM_ID]=$this->_uid;
	       $data[SEND_FLAG]=1;
	       $dao->where("EMAIL_ID in (".$EMAIL_ID_STR."0)")->save($data);
	       $this->redirect("sentbox","Email");
       }elseif ($BOX_ID=="deletebox"){//已删除
	       $data[BOX_ID]=0;
	       $data[DELETE_FLAG]=1;
	       $data[delete_user]=$this->_uid;
	       $dao->where("EMAIL_ID in (".$EMAIL_ID_STR."0)")->save($data);
	       $this->redirect("deletebox","Email");
       }else{//收件箱
	       $data[BOX_ID]=$BOX_ID;
	       $data[DELETE_FLAG]=0;
	       $data[delete_user]="NULL";
	       $data[TO_ID]=$this->_uid;
	       $data[SEND_FLAG]=1;
	       $dao->where("EMAIL_ID in (".$EMAIL_ID_STR."0)")->save($data);
	       $this->redirect("inbox/BOX_ID/$BOX_ID","Email");
       }
	   
       
       

       
       //$dao->setField("BOX_ID",$BOX_ID,"EMAIL_ID in (".$EMAIL_ID_STR."0)");
       
      // $dao->setField("DELETE_FLAG",0,"EMAIL_ID in (".$EMAIL_ID_STR."0)");
       
       
       //exit;
       //$this->assign("jumpUrl",__APP__."/Email/inbox/BOX_ID/$BOX_ID");
       
      // $this->redirect("inbox/BOX_ID/$BOX_ID","Email");
       //$this->success("成功移动");
       /*
       $array=explode(",",$EMAIL_ID_STR);
       foreach ($array as $EMAIL_ID){
       }
		*/
	}

	/*----------------设为已读---------------*/
	public function readok(){//设为已读
		$READ_STR=$_REQUEST[READ_STR];
		$BOX_ID=$_REQUEST[BOX_ID];
		$dao=D("Email");
		
		$TOK=strtok($READ_STR,",");
		while($TOK!=""){ 
		 $dao->setField("READ_FLAG","1","EMAIL_ID='$TOK'");
         
		  $TOK=strtok(",");
		}
		$this->redirect("inbox/BOX_ID/$BOX_ID","Email");
	}
	
	public function printmail(){//打印
		$EMAIL_ID=$_REQUEST[EMAIL_ID];
		$dao=D("Email");
		$daouser=D("User");
		
		$row=$dao->where("EMAIL_ID='$EMAIL_ID'")->find();
		
		$user=$daouser->where("USER_ID='$row[FROM_ID]'")->find();
		$row[FROM_NAME].=$user["USER_NAME"];
		
			  $TOK=strtok($row[TO_ID],",");
			   while($TOK!=""){
			   	    $user=$daouser->where("USER_ID='$TOK'")->find();
			   	    $row[TO_NAME].=$user["USER_NAME"].",";
                    //$row[TO_NAME]="<u title=\"部门：".$this->get_dept($user[DEPT_ID])."\" style=\"cursor:hand\">".$user["USER_NAME"]."</u>,";			   	    
				    $TOK=strtok(",");
				}
				$TOK=strtok($row[COPY_TO_ID],",");
			   while($TOK!=""){
			   	    $user=$daouser->where("USER_ID='$TOK'")->find();
			   	    $row[COPY_TO_NAME].=$user["USER_NAME"].",";
                    //$row[COPY_TO_NAME]="<u title=\"部门：".$this->get_dept($user[DEPT_ID])."\" style=\"cursor:hand\">".$user["USER_NAME"]."</u>,";			   	    			   	    
				    $TOK=strtok(",");
				}
				
				$TOK=strtok($row[SECRET_TO_ID],",");
			   while($TOK!=""){
			   	    $user=$daouser->where("USER_ID='$TOK'")->find();
			   	    $row[SECRET_TO_NAME].=$user["USER_NAME"].",";
			   	    //$row[SECRET_TO_NAME]="<u title=\"部门：".$this->get_dept($user[DEPT_ID])."\" style=\"cursor:hand\">".$user["USER_NAME"]."</u>,";			   	    			   	    
				    $TOK=strtok(",");
				}

		$this->assign("dctime",date("Y年m月d日 H:i:s"));
		$this->assign("curtitle","邮件打印 - $row[SUBJECT]");
		$this->assign("row",$row);
		$this->display();
		
	}
		
    /*--------删除所有收件人已删除邮件 删除所有收件人未读邮件------------*/
    /*
	public function delemail(){//
		$DELETE=$_REQUEST[DELETE];
		$dao=D("Email");
		if($DELETE=='del'){
			$list=$dao->where("FROM_ID='$this->_uid'  and SEND_FLAG='1' and DELETE_FLAG='1'")->findall();
		}else{
			$list=$dao->where("FROM_ID='$this->_uid'  and SEND_FLAG='1' and DELETE_FLAG='0' and READ_FLAG='0'")->findall();
		}
		if ($list) {
			foreach ($list as $row){
				$ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
		        $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
				foreach ($ATTACHMENT_ID_ARRAY as $attid){
					 $this->_deleteattach($attid);
				}
				$dao->where("EMAIL_ID='$row[EMAIL_ID]'")->delete();
				
			}
		}
		
		$this->redirect("sentbox/BOX_ID/0","Email");
		
	}
	*/


	/*----------------查询邮件 ----------------*/
	public function searchform(){//表单
		$BOX_ID=$_REQUEST[BOX_ID];
		$dao=D("EmailBox");
		$list=$dao->where("uid='$this->_uid'")->order("BOX_NO")->findall();
		$this->assign("list",$list);
		$this->assign("curtitle","查询邮件");
		$this->assign("BOX_ID",$BOX_ID);
		$this->display();
	}
	
	protected function utf8RawUrlDecode ($source){
    $decodedStr = "";
    $pos = 0;
    $len = strlen ($source);
    while ($pos < $len) {
        $charAt = substr ($source, $pos, 1);
        if ($charAt == '%') {
            $pos++;
            $charAt = substr ($source, $pos, 1);
            if ($charAt == 'u') {
                // we got a unicode character
                $pos++;
                $unicodeHexVal = substr ($source, $pos, 4);
                $unicode = hexdec ($unicodeHexVal);
                $entity = "&#". $unicode . ';';
                $decodedStr .= utf8_encode ($entity);
                $pos += 4;
            }
            else {
                // we have an escaped ascii character
                $hexVal = substr ($source, $pos, 2);
                $decodedStr .= chr (hexdec ($hexVal));
                $pos += 2;
            }
        } else {
            $decodedStr .= $charAt;
            $pos++;
        }
    }
    return $decodedStr;
}
//识别汉字编码,如果发过来的是gb2312的编码的话,需要可以识别并完成编码转换   
 protected function safeEncoding($string,$outEncoding = 'UTF-8')   
{   
    $encoding = "UTF-8";   
    for($i=0;$i<strlen($string);$i++)   
    {   
        if(ord($string{$i})<128)   
            continue;   
  
        if((ord($string{$i})&224)==224)   
        {   
            //第一个字节判断通过   
            $char = $string{++$i};   
            if((ord($char)&128)==128)   
            {   
                //第二个字节判断通过   
                $char = $string{++$i};   
                if((ord($char)&128)==128)   
                {   
                    $encoding = "UTF-8";   
                    break;   
                }   
            }   
        }   
        if((ord($string{$i})&192)==192)   
        {   
            //第一个字节判断通过   
            $char = $string{++$i};   
            if((ord($char)&128)==128)   
            {   
                //第二个字节判断通过   
                $encoding = "GB2312";   
                break;   
            }   
        }   
    }   
       
    if(strtoupper($encoding) == strtoupper($outEncoding))   
        return $string;   
    else  
        return iconv($encoding,$outEncoding,$string);   
}
		
	public function search(){//搜索结果
		header("Content-Type:text/html; charset=utf-8");
		//print_r($_REQUEST);
		$daouser=D("User");
		$dao=D("Email");
		$daobox=D("EmailBox");
		
		$BOX_ID=$_REQUEST[BOX_ID];
		$BOX=$_REQUEST[BOX];
		
		$READ_FLAG=$_REQUEST[READ_FLAG];
		$BEGIN_DATE=$_REQUEST[BEGIN_DATE];
		$END_DATE=$_REQUEST[END_DATE];
		
		$FROM_ID=$_REQUEST[FROM_ID];
		$TO_ID=$_REQUEST[TO_ID];
		$SUBJECT=$_REQUEST[SUBJECT];
		$keyword=$_REQUEST[keyword];
		//echo $keyword;
		//$SUBJECT=urldecode($_REQUEST[SUBJECT]);
		
		//$SUBJECT=$this->safeEncoding($SUBJECT);
		
		$KEY1=$_REQUEST[KEY1];
		$KEY2=$_REQUEST[KEY2];
		$KEY3=$_REQUEST[KEY3];
		
		$ATTACHMENT_NAME=$_REQUEST[ATTACHMENT_NAME];
        if (!$READ_FLAG) {
        	$READ_FLAG="-1";
        }
        if (!$BOX) {
        	$BOX=1;
        }
		 if($BOX_ID==0&&$BOX==1){
		    $BOX_DESC="收件箱";
		    $BOX_URL="../inbox/read_email";
		    $map="a.TO_ID='$this->_uid'  and a.SEND_FLAG='1' and a.DELETE_FLAG='0' and a.BOX_ID='$BOX_ID'";
		    if($FROM_ID){
		       $map.=" and (u.USER_ID LIKE '%$FROM_ID%' or u.USER_NAME LIKE '%$FROM_ID%')";
		    }
		      $on ="a.FROM_ID = u.uid";
		 }elseif($BOX_ID==0&&$BOX==2){
		    $BOX_DESC="发件箱";
		    $BOX_URL="../new/";
		    $map="a.FROM_ID='$this->_uid' and a.SEND_FLAG='0' and a.DELETE_FLAG='0' and a.BOX_ID=$BOX_ID";
		    if($TO_ID){
		      $map.=" and (u.USER_ID LIKE '%$TO_ID%' or u.USER_NAME LIKE '%$TO_ID%')";
		    }
		    $on ="a.TO_ID = u.uid";
		    
		 }elseif($BOX_ID==0&&$BOX==3){
		    $BOX_DESC="已发送邮件箱";
		    $BOX_URL="../sentbox/read_email";
		    $map="a.FROM_ID='$this->_uid' and a.SEND_FLAG='1' and a.DELETE_FLAG='0'";
		    if ($TO_ID) {
		      $map.=" and (u.USER_ID LIKE '%$TO_ID%' or u.USER_NAME LIKE '%$TO_ID%')";
		    }
		      $on ="a.TO_ID = u.uid";		    
		 }else{
		 	if (!$BOX_ID) {
		 		$BOX_ID=0;
		 	}
		 	$ROW=$daobox->where("BOX_ID='$BOX_ID'")->find();
		    $BOX_DESC=$ROW[BOX_NAME];
		    $BOX_URL="../mailbox/read_email";
		    $map="a.TO_ID='$this->_uid' and a.DELETE_FLAG='0' and a.BOX_ID='$BOX_ID'";
		    if ($FROM_ID) {
		       $map.=" and (u.USER_ID LIKE '%$FROM_ID%' or u.USER_NAME LIKE '%$FROM_ID%')";
		    }
		       $on ="a.FROM_ID = u.uid";		    			    
		 }
		 if ($keyword){
	       $map .=" and (a.SUBJECT like '%$keyword%' or a.CONTENT like '%$keyword%')";	 	
		 }
	
		 if($SUBJECT!="")
		    $map.= " and a.SUBJECT like '%$SUBJECT%'";
		 if($KEY1!="")
		    $map.= " and a.CONTENT like '%$KEY1%'";
		 if($KEY2!="")
		    $map.= " and a.CONTENT like '%$KEY2%'";
		 if($KEY3!="")
		    $map.= " and a.CONTENT like '%$KEY3%'";
		 if($ATTACHMENT_NAME!="")
		    $map.= " and a.ATTACHMENT_NAME like '%$ATTACHMENT_NAME%'";
		 if ($BEGIN_DATE!="") {
		    $map.=" and a.SEND_TIME >='$BEGIN_DATE'";    	
		  }
		  if ($END_DATE){
		  	$map.=" and a.SEND_TIME <='$END_DATE'";    	
		  }    
		  if ($READ_FLAG!='-1') {
            $map.=" and a.READ_FLAG=$READ_FLAG"; 		  	
		  }
		 $count=$dao->table("email as a")
		           ->field("count(*) as count")
		           ->join("left join user as u on ".$on." left join department as d on u.DEPT_ID=d.DEPT_ID")
		           ->where($map)
		           ->find();
		 $count=$count['count'];
		 
	     //import("ORG.Util.Page");
		 if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		 }else {
			$listRows  =  '20';
		 }
		 $p          = new Page($count,$listRows);
		 //echo ;
		 //echo $this->phpdigVerifyUTF8($_REQUEST[SUBJECT]);
		 //echo $map;
		 $list=$dao->table("email as a")
		           ->field("a.*,u.USER_NAME,u.AVATAR,d.DEPT_NAME")
		           ->join("left join user as u on ".$on." left join department as d on u.DEPT_ID=d.DEPT_ID")
		           ->where($map)
		           ->limit("$p->firstRow,$p->listRows")
		           ->order("a.SEND_TIME desc")
		           ->findall();
         //echo $dao->getlastsql(); 
						           
				if (!empty($_GET)) {
				    $array=$_GET;		           	
				}          
				if (!empty($_POST)) {
				    $array=$_POST;	
				     	           	
				}

				
				//print_r($array);				
				foreach($array as $key=>$val) {
		                if ($key=='a'||$key=='m'){
		                   continue;	
		                }
						if(is_array($val)) {
							foreach ($val as $t){
								$p->parameter	.= $key.'[]='.urldecode($t)."&";
							}
						}else{
							//$val=iconv("UTF-8","GB2312",$val);	
							$p->parameter   .=   "$key=".urlencode($val)."&";        
						}
                  
				}
				//ECHO $p->parameter;EXIT;
		$page       = $p->show();
		$this->assign("page",$page);	
		/*$k =urldecode($_REQUEST['SUBJECT']);
		echo $k;
		$a = "测";
		$ab=urlencode($a);
		echo $ab;			              	 
		*/
       // echo $dao->getlastsql();  
        $this->assign("curtitle","邮件搜索"); 
		$this->assign("list",$list);
		$this->assign("BOX_ID",$BOX_ID);
		$this->assign("BOX",$BOX);
		$this->assign("count",$count);
		$this->display();
		
	}
	
	/*-----------------邮件箱管理--------emai_box--  ///还差邮件管理-*/
	public function mailbox(){
         $dao=D("EmailBox");
         $list=$dao->where("uid='$this->_uid'")->order("BOX_NO")->findall();
         $this->assign("list",$list);
         
         $this->assign("curtitle","邮箱管理");
         
		 $this->display();
	}
	
	public function mailboxsubmit(){
		$BOX_ID=$_REQUEST[BOX_ID];
		$dao=D("EmailBox");
		
		if (false===$dao->create()) {
			$this->error("操作失败");
		}
		if ($BOX_ID) {//修改
			$dao->where("BOX_ID='$BOX_ID'")->save();
			//$this->assign("jumpUrl","mailbox");
			
			$this->assign("jumpUrl",__APP__."/WebMail/index/to/mailbox");
			
		    $this->success("成功修改");
		    
		}else {//添加
			$_POST[uid]=$this->_uid;
			//echo $_POST[uid];
			$dao->add($_POST);
			//echo $dao->getlastsql();exit;
			$this->assign("jumpUrl",__APP__."/WebMail/index/to/mailbox");
			$this->success("成功添加");	
		}
	}
	public function mailboxform(){
		$BOX_ID=$_REQUEST[BOX_ID];
		$dao=D("EmailBox");
		$row=$dao->where("BOX_ID='$BOX_ID'")->find();
		
		$this->assign("row",$row);
		$this->display();
	}
	
	public function mailboxdelete(){//删除
		$BOX_ID=$_REQUEST[BOX_ID];
		$dao=D("Email");
		$list=$dao->where("BOX_ID='$BOX_ID'")->findall();
		if ($list) {
			foreach ($list as $rows){
			   $row=$dao->where("EMAIL_ID='$rows[EMAIL_ID]'")->find();
			    if ($row[DELETE_FLAG]==0) {//设删除标志
			     	$dao->setField("DELETE_FLAG","1","EMAIL_ID='$TOK'");
			    }else{//物理删除
			    	$ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
			        $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
					foreach ($ATTACHMENT_ID_ARRAY as $attid){
						 $this->_deleteattach($attid);
					}
			    	$dao->where("EMAIL_ID='$rows[EMAIL_ID]'")->delete();
			    } 
			}
		}
		$daobox=D("EmailBox");
		$daobox->where("BOX_ID='$BOX_ID'")->delete();
		//$this->redirect("index","WebMail");
		$this->assign("jumpUrl",__APP__."/WebMail/index/to/mailbox");
		$this->success("成功删除");
	}
		

	
	
	
	/*------------获得用户部门-----*/
	protected function get_user_dept($userid){
		$daodept=D("Department");
		$daouser=D("User");
		$user=$daouser->where("USER_ID='$userid'")->find();
		$dept=$daodept->where("DEPT_ID='$user[DEPT_ID]'")->find();
		return $dept[DEPT_NAME];
	}
	protected function get_dept($deptid){
		$daodept=D("Department");
		$dept=$daodept->where("DEPT_ID='$deptid'")->find();
		return $dept[DEPT_NAME];
	}
	protected function get_user_name($userid){
		$daouser=D("User");
		$user=$daouser->where("USER_ID='$userid'")->find();
		return $USER[USER_NAME];
		
	}	

}	

?>