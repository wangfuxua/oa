<?php
/*
 功能：项目终审
 
*/
import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");

class ProZsAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目终审管理";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
/*------------------申报项目列表-----------------*/	
    public function index(){//申报项目列表
    	$pro=new Program();
        $pro->xm_getPrc('ZS',$TYPE_NAME,$TYPE_ID);
        IF($this->LOGIN_DEPT_ID==0){//超级用户
        	$BM_ID=-1;
            $map=""; 
        }else {
        	$BM_ID=$this->LOGIN_DEPT_ID;
        	$map=" AND B.BM_ID = $this->LOGIN_DEPT_ID ";
        }
        
		//$arProUsers = $pro->xm_getPrcManagers($TYPE_ID,$BM_ID);//终审核权限人员列表
		$arProUsers = $pro->xm_getPrcManagers($TYPE_ID);//终审核权限人员列表
		$arPRCs = $pro->xm_getSHPrcs($arProUsers);//

		//if(in_array($this->LOGIN_USER_ID,$arProUsers)){
	            import("ORG.Util.Page");
	            $dao= new Model();
                $rowc=$dao->table('xmsb_sh as A')
                         ->join('xmsb_lb as B ON A.XM_ID = B.XM_ID')
                         ->where("B.XMZT = '".XMZT_ZS."' AND A.TYPE_ID = '$TYPE_ID'"." AND A.ZS_CURRENT_USER = '$this->LOGIN_USER_ID'")
                         ->field("COUNT(A.XM_ID) as cnt")
                         ->find();
               // echo $dao->getLastSql();         
		        $count=$rowc[cnt];
		       // echo $count;
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p          = new Page($count,$listRows);
				$list=$dao->table("xmsb_sh as A")
				          ->join("left join xmsb_lb as B ON A.XM_ID = B.XM_ID left join department as D on B.BM_ID=D.DEPT_ID left join user as U on B.ZSBR_ID=U.USER_ID")
				          ->where("B.XMZT = '".XMZT_ZS."' AND A.TYPE_ID = '$TYPE_ID'"." AND A.ZS_CURRENT_USER = '$this->LOGIN_USER_ID'")
				          ->field('A.XM_ID,A.TYPE_ID,B.MC,B.BM_ID,B.ZSBR_ID,B.SJ,B.JB,D.DEPT_NAME as BM_NAME,U.USER_NAME as ZSBR_NAME')
				          ->order('B.BM_ID ASC,B.SJ DESC')
				          ->findAll();
			  // echo $dao->getLastSql();	          			
			   $page       = $p->show();
		//}
		$this->assign("page",$page);
        $this->assign("list",$list);
         	
    	$this->display();
    }
    
    public function manage(){
    	$XM_ID=$_REQUEST[XM_ID];
    	$TYPE_ID=$_REQUEST[TYPE_ID];
    	$pro=new Program();
    	
		$arProUsers = $pro->xm_getPrcManagers($TYPE_ID);//终审人员
		//$arPRCs = $pro->xm_getSHPrcs($arProUsers);//
		
		$dao= new Model();
		$daosh=D("XmsbSh");
		$daouser=D("user");
		$daodept=D("department");
		$shrow=$daosh->where("XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID")->field("ZS_USERS")->find();
		$ZS_USERS = explode(",",$shrow["ZS_USERS"]);//已经终审过的人员
		
		$n = 0;
		foreach($ZS_USERS as $user){
			if(!empty($user)){
				$n ++;
			}
		}
		
		/*-------------登录用户信息--------*/
		$depts=$daodept->where("DEPT_ID = $this->LOGIN_DEPT_ID")->find();
		$dept_name=$depts[DEPT_NAME];
		$users=$daouser->where("USER_ID = '$this->LOGIN_USER_ID'")->find();
		$user_name=$users[USER_NAME];
		
		/*-------------初审核信息-----------*/
		
		$NEXT_USER_ID = $arProUsers[$SH_PRC];
		$ROW=$daouser->where("USER_ID = '$NEXT_USER_ID'")->find();
		$NEXT_USER_NAME = $ROW["USER_NAME"];
		    

		//print_r($arSBPDs);
			/*-------------项目信息-----------*/
			$q .= " LEFT JOIN DEPARTMENT C ON A.BM_ID = C.DEPT_ID ";
			$q .= " LEFT JOIN USER D ON A.ZSBR_ID = D.USER_ID ";
			$q .= " LEFT JOIN USER E ON A.FSBR_ID = E.USER_ID ";			
			$row=$dao->table("xmsb_lb as A")
				          ->join("xmsb_sx B ON A.SX_ID = B.SX_ID".$q)
				          ->where("A.XM_ID = $XM_ID")
				          ->field('A.MC,A.WCSJ,B.SX_NAME,C.DEPT_NAME as BM_NAME,D.USER_NAME as ZSBR_NAME,A.FSBR_ID,A.JB,A.SJ,A.XMLY,A.YSZJ,A.XMJJ,A.FJ_ID,A.FJ_NAME,A.XMZT')
				          ->find();		
				          //echo $row[FSBR_ID];
			 $TOK=strtok($row[FSBR_ID],",");
			 while($TOK!="")
			 {
			   if($FSBR_NAME!="")
			      $FSBR_NAME.="，";
			      
			      $user=$daouser->where("USER_ID='$TOK'")->find();
			      $FSBR_NAME.=$user["USER_NAME"];
			      
			   $TOK=strtok(",");
			 }
            $row[FSBR_NAME]=$FSBR_NAME;
            $att=$this->_attch($row[FJ_ID],$row[FJ_NAME],$candele=0);
            $row[att]=$att;
            
            $this->assign("row",$row);

                        
            /*-----------已经审核过的人的初审核信息----------------*/
		        IF($this->LOGIN_DEPT_ID==0){//超级用户
		        	$BM_ID=-1;
		            $map=""; 
		        }else {
		        	$BM_ID=$this->LOGIN_DEPT_ID;
		        	$map=" AND B.BM_ID = $LOGIN_DEPT_ID";
		        }
		                    
				$pro->xm_getPrc("CS",$TYPE_NAME_CS,$TYPE_ID_CS);//初审
				$arProUsers_cs = $pro->xm_getPrcManagers($TYPE_ID_CS,$BM_ID);
				            
				$rowsh=$daosh->where("XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID_CS")->field("SBPD,SBYJ,SH_PRC")->find();
				$SH_PRC=intval($rowsh[SH_PRC])+1;
			    $SBPD = $rowsh["SBPD"];
			    $SBYJ = $rowsh["SBYJ"];
				$arSBPDs = explode("@",$SBPD);
				$arSBYJs = explode("@",$SBYJ);
	        		
			  for($i = 0 ; $i < count($arSBPDs) ; $i++){
			  	$list[$i][SBPD]=$arSBPDs[$i];
			  	$list[$i][SBYJ]=$arSBYJs[$i];
			  	if(empty($list[$i][SBPD]) || empty($list[$i][SBYJ]))continue;
			  	$USER_ID = $arProUsers_cs[$i];
			  	$user=$daouser->where(" USER_ID = '$USER_ID'")->field("USER_NAME")->find();
			  	$list[$i][USER_NAME] = $user["USER_NAME"];
			  }
			  //print_r($list);
	         $this->assign("list",$list);
	         /*------------------终审过的信息-------*/
				$rowsh=$daosh->where("XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID")->field("SBPD,SBYJ")->find();
				$SH_PRC=intval($rowsh[SH_PRC])+1;
			    $SBPD = $rowsh["SBPD"];
			    $SBYJ = $rowsh["SBYJ"];
				$arSBPDs = explode("@",$SBPD);
				$arSBYJs = explode("@",$SBYJ);
	        		
			  for($i = 0 ; $i < count($arSBPDs) ; $i++){
			  	$listzs[$i][SBPD]=$arSBPDs[$i];
			  	$listzs[$i][SBYJ]=$arSBYJs[$i];
			  	if(empty($listzs[$i][SBPD]) || empty($listzs[$i][SBYJ]))continue;
			  	$USER_ID = $arProUsers[$i];
			  	$user=$daouser->where(" USER_ID = '$USER_ID'")->field("USER_NAME")->find();
			  	$listzs[$i][USER_NAME] = $user["USER_NAME"];
			  }
			  //print_r($list);
	         $this->assign("listzs",$listzs);
	         	         
	         
	         
             $this->assign("dept_name",$dept_name);
             $this->assign("user_name",$user_name);
             $this->assign("TYPE_ID",$TYPE_ID);
             $this->assign("XM_ID",$XM_ID);
             			  		
		if($n+1 == count($arProUsers)){//所有终审完毕，终审的最后一步
             //-------------问题类型-------
			$arData = array();
			$daowt=D("XmsbWt");
			$listwt=$daowt->where("TYPE_ID = $TYPE_ID")->order("WT_NAME")->field("WT_ID,WT_NAME")->findall();
			
			$arData[0] = "无";
			foreach ($listwt as $ROW){
				$arData[$ROW["WT_ID"]] = $ROW["WT_NAME"];
			}        
	        
	        $wt=my_select("WT_ID",$arData,"BigSelect",0);
	        $this->assign("wt",$wt);

            
            //$XMZT_ZS $XMZT_CREATE $XMZT_CSREJECT
            $this->assign("XMZT_FH",XMZT_FH);
            $this->assign("XMZT_CREATE",XMZT_CREATE);
            $this->assign("XMZT_ZSREJECT",XMZT_ZSREJECT);
            $this->assign("SJ",date("Y-m-d",$this->CUR_TIME_INT));
            
			$this->display('manage_saudit');
		}else{//终审过程
            //-----------其他终审人员列表---------
            $daolc=D("XmsbLc");
            $pro = new Program();
    		$pro->xm_getPrc("ZS",$TYPE_NAME,$TYPE_ID);
    		$listlc=$daolc->table("xmsb_lc a")
    		                ->join("user b on a.USER_ID = b.USER_ID")
    		                ->field("a.USER_ID,b.USER_NAME")
    		                ->where("a.TYPE_ID = $TYPE_ID")
    		                ->findall();
    		$arData=array();               
			foreach ($listlc as $ROW){
				if(!stristr($ZS_USERS,$ROW[USER_ID]) && $ROW[USER_ID] != $this->LOGIN_USER_ID)//去掉本身和已经终审过的人员
				     $arData[$ROW["USER_ID"]] = $ROW["USER_NAME"];
			}  
    		$listuser=my_select("ZS_CURRENT_USER",$arData,"BigSelect","");
            $this->assign("listuser",$listuser);
            
			$this->display('manage_paudit');
		}
			
    	
    }
    

    public function pauditSubmit(){//初审审核中间步骤提交
         $XM_ID=$_POST[XM_ID];
         $TYPE_ID=$_POST[TYPE_ID];
    	 $daosh=D("XmsbSh");
    	  
    	   $ROW=$daosh->where("XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID")->field("SBPD,SBYJ")->find();
    	 
			$arSBPDs = explode("@",$ROW["SBPD"]);
			 $SBPD_new = "";
			 $bAdd = true;
			 foreach($arSBPDs as $tmpSBPD){
			 	$arSBPD = explode("#",$tmpSBPD);
			 	if($arSBPD[0] == $this->LOGIN_USER_ID){
			 		$arSBPD[1] = $_POST[SBPD];
			 		$bAdd = false;
			 	}
			 	if(!empty($arSBPD[0])){
			 		$SBPD_new .= $arSBPD[0]."#".$arSBPD[1]."@";
			 	}
			 }
			 if($bAdd){
			 	$SBPD_new .= $this->LOGIN_USER_ID."#".$_POST[SBPD]."@";
			 }
			 $arSBYJs = explode("@",$ROW["SBYJ"]);
			 $SBYJ_new = "";
			 $bAdd = true;
			 foreach($arSBYJs as $tmpSBYJ){
			 	$arSBYJ = explode("#",$tmpSBYJ);
			 	if($arSBYJ[0] == $this->LOGIN_USER_ID){
			 		$arSBYJ[1] = $_POST[SBYJ];
			 		$bAdd = false;
			 	}
			 	if(!empty($arSBYJ[0])){
			 		$SBYJ_new .= $arSBYJ[0]."#".$arSBYJ[1]."@";
			 	}
			 }
			 if($bAdd){
			 	$SBYJ_new .= $this->LOGIN_USER_ID."#".$_POST[SBYJ]."@";
			 }
			     	 
		 //$SBPD_new = $ROW["SBPD"].$_POST[SBPD]."@";
		 //$SBYJ_new = $ROW["SBYJ"].$_POST[SBYJ]."@";
		 if($_POST['FINAL'] == 'T'){
		 $T = ",ZS_USERS = CONCAT(ZS_USERS,'$this->LOGIN_USER_ID',',')";
		 }
		  $sql = "UPDATE xmsb_sh SET SBPD = '$SBPD_new',SBYJ = '$SBYJ_new',ZS_CURRENT_USER = '$_POST[ZS_CURRENT_USER]' $T WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";
		 //ECHO $sql;exit;
		 $daosh->execute($sql);
		 //echo $daosh->getlastsql();
		 //exit;
		 /*---------- 短信提醒 ----------*/
		 $daosms=D("Sms");		 
		 $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
		 $data[CONTENT]="项目：您有新的终审工作需要办理！";
         $data[FROM_ID]=$this->LOGIN_USER_ID;
         $data[TO_ID]=$_POST[ZS_CURRENT_USER];
         $data[SMS_TYPE]="11";
         $data[REMIND_FLAG]=1; 		 
		 $daosms->create();
		 $daosms->add($data);
		 
		 $this->redirect("index","prozs");
		 //header("Location:index.php");
    }
    
     public function pauditSubmitLast(){//初审审核最后一步提交
         $XM_ID=$_POST[XM_ID];
         $TYPE_ID=$_POST[TYPE_ID];
         $option=$_POST[option];
     	      $daosh=D("XmsbSh");
     	      $daolb=D("xmsblb");
     	      $daosms=D("Sms");
			if($option == XMZT_CREATE){//未通过
				$daosh->where("XM_ID = $XM_ID AND TYPE_ID <> $TYPE_ID")->delete();//不理解为什么用不等于type_id
                $q = "UPDATE xmsb_sh SET ZJPD = '$this->LOGIN_USER_ID#$_POST[ZJPD]',ZJYJ = '$this->LOGIN_USER_ID#$_POST[ZJYJ]',SJ = '$_POST[SJ]',ZS_CURRENT_USER = '___NO___USER___',WT_ID=$_POST[WT_ID] WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";
				$daosh->execute($q);
				
				$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
				$daolb->execute($q);
				
				/*---------- 短信提醒项目主申请人 ----------*/
				 $ROW=$daolb->where("XM_ID = $XM_ID")->find();
				 
				 $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
				 $data[CONTENT]="项目：您的项目没有通过终审！";
		         $data[FROM_ID]=$this->LOGIN_USER_ID;
		         $data[TO_ID]=$ROW[ZSBR_ID];
		         $data[SMS_TYPE]="17";
		         $data[REMIND_FLAG]=1; 		 
				 $daosms->create();
				 $daosms->add($data);
				 				
			 }
			 else if($option == XMZT_ZSREJECT){//项目申请终止
			 	$q = "UPDATE xmsb_sh SET ZJPD = '$this->LOGIN_USER_ID#$_POST[ZJPD]',ZJYJ = '$this->LOGIN_USER_ID#$_POST[ZJYJ]',SH_PRC = 0,ZS_CURRENT_USER = '___NO___USER___',SJ='$_POST[SJ]',WT_ID=$_POST[WT_ID],BZ='$_POST[BZ]' WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";
			 	$daosh->execute($q);
			 	
			 	$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
			 	$daolb->execute($q);

			 }
			 else if($option == XMZT_FH){//提交终审 到复核
    	           $daosh=D("XmsbSh");
    	           
		    	   $ROW=$daosh->where("XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID")->field("SBPD,SBYJ")->find();
					$arSBPDs = explode("@",$ROW["SBPD"]);
					 $SBPD_new = "";
					 $bAdd = true;
					 foreach($arSBPDs as $tmpSBPD){
					 	$arSBPD = explode("#",$tmpSBPD);
					 	if($arSBPD[0] == $this->LOGIN_USER_ID){
					 		$arSBPD[1] = $_POST[SBPD];
					 		$bAdd = false;
					 	}
					 	if(!empty($arSBPD[0])){
					 		$SBPD_new .= $arSBPD[0]."#".$arSBPD[1]."@";
					 	}
					 }
					 if($bAdd){
					 	$SBPD_new .= $this->LOGIN_USER_ID."#".$_POST[SBPD]."@";
					 }
					 $arSBYJs = explode("@",$ROW["SBYJ"]);
					 $SBYJ_new = "";
					 $bAdd = true;
					 foreach($arSBYJs as $tmpSBYJ){
					 	$arSBYJ = explode("#",$tmpSBYJ);
					 	if($arSBYJ[0] == $this->LOGIN_USER_ID){
					 		$arSBYJ[1] = $_POST[SBYJ];
					 		$bAdd = false;
					 	}
					 	if(!empty($arSBYJ[0])){
					 		$SBYJ_new .= $arSBYJ[0]."#".$arSBYJ[1]."@";
					 	}
					 }
					 if($bAdd){
					 	$SBYJ_new .= $this->LOGIN_USER_ID."#".$_POST[SBYJ]."@";
					 }
				 			 	
			 	$q = "UPDATE xmsb_sh SET SBPD = '$SBPD_new',SBYJ = '$SBYJ_new',ZS_CURRENT_USER = '___NO___USER___', ZJPD = '$this->LOGIN_USER_ID#$_POST[ZJPD]',ZJYJ = '$this->LOGIN_USER_ID#$_POST[ZJYJ]',SJ='$_POST[SJ]',WT_ID=$_POST[WT_ID],BZ='$_POST[BZ]' WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";			 	
			 	$daosh->execute($q);
			 	
			 	$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
			 	$daolb->execute($q);
			 	
			 	$pro=new Program();
			 	$pro->xm_getPrc('FH',$TYPE_NAME_FH,$TYPE_ID_FH);
				$query="INSERT INTO xmsb_sh (XM_ID,TYPE_ID,SH_PRC) VALUES ($XM_ID,$TYPE_ID_FH,'0')";
				$daosh->execute($query);
				
                 //------------	短信提醒项目项目复核人员
                 $daolc=D("XmsbLc");
                 $ROW=$daolc->where("TYPE_ID = $TYPE_ID_FH")->field("USER_ID")->order("ORDERING ASC")->find();
	             $TOK = $ROW["USER_ID"];
			    $SMS_CONTENT="项目：您有新的复核工作需要办理！";
			    $SEND_TIME=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
			    $query1="insert into SMS(FROM_ID,TO_ID,SMS_TYPE,CONTENT,SEND_TIME,REMIND_FLAG) values ('$this->LOGIN_USER_ID','$TOK','12','$SMS_CONTENT','$SEND_TIME','1')";
			    $daosms->execute($query1);
		 }
			 
			 $this->redirect("index","prozs");
     	
     }
    
    
}
?>