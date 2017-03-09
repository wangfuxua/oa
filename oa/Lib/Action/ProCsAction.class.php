<?php
/*
 功能：项目初审
 
*/
import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");

class ProCsAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目初审管理";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
    public function index(){//申报项目列表
    	$pro=new Program();
        $pro->xm_getPrc('CS',$TYPE_NAME,$TYPE_ID);
        //echo $TYPE_ID;
        //echo $this->LOGIN_DEPT_ID;
        IF($this->LOGIN_DEPT_ID==0){//超级用户
        	$BM_ID=-1;
            $map=""; 
        }else {
        	$BM_ID=$this->LOGIN_DEPT_ID;
        	$map=" AND B.BM_ID = $this->LOGIN_DEPT_ID ";
        }
        
		$arProUsers = $pro->xm_getPrcManagers($TYPE_ID,$BM_ID);//审核权限人员列表
		$arPRCs = $pro->xm_getSHPrcs($arProUsers);//
		
		//print_r($arProUsers);
		//echo $this->LOGIN_USER_ID;
		
		if(in_array($this->LOGIN_USER_ID,$arProUsers)){
	            import("ORG.Util.Page");
	            $dao= new Model();
                $rowc=$dao->table('xmsb_sh as A')
                         ->join('xmsb_lb as B ON A.XM_ID = B.XM_ID')
                         ->where("B.XMZT = '".XMZT_CS."' AND A.TYPE_ID = '$TYPE_ID'"." AND A.SH_PRC IN (".implode(",",$arPRCs).")".$map)
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
				          ->where("B.XMZT = '".XMZT_CS."' AND A.TYPE_ID = '$TYPE_ID'"." AND SH_PRC IN (".implode(",",$arPRCs).")".$map)
				          ->field('A.XM_ID,A.TYPE_ID,B.MC,B.BM_ID,B.ZSBR_ID,B.SJ,B.JB,D.DEPT_NAME as BM_NAME,U.USER_NAME as ZSBR_NAME')
				          ->order('B.BM_ID ASC,B.SJ DESC')
				          ->findAll();
			  // echo $dao->getLastSql();	          			
			   $page       = $p->show();
		}
		$this->assign("page",$page);
        $this->assign("list",$list);
         	
    	$this->display();
    }
    
    public function manage(){
    	$XM_ID=$_REQUEST[XM_ID];
    	$TYPE_ID=$_REQUEST[TYPE_ID];
    	$pro=new Program();
        IF($this->LOGIN_DEPT_ID==0){//超级用户
        	$BM_ID=-1;
            $map=""; 
        }else {
        	$BM_ID=$this->LOGIN_DEPT_ID;
        	$map=" AND B.BM_ID = $LOGIN_DEPT_ID";
        }
            	
		$arProUsers = $pro->xm_getPrcManagers($TYPE_ID,$BM_ID);
		$arPRCs = $pro->xm_getSHPrcs($arProUsers);//
		
		$dao= new Model();
		$daosh=D("XmsbSh");
		$daouser=D("user");
		$daodept=D("department");
		
		/*-------------登录用户信息--------*/
		$depts=$daodept->where("DEPT_ID = $this->LOGIN_DEPT_ID")->find();
		$dept_name=$depts[DEPT_NAME];
		$users=$daouser->where("USER_ID = '$this->LOGIN_USER_ID'")->find();
		$user_name=$users[USER_NAME];
		
		/*-------------审核信息-----------*/
		$rowsh=$daosh->where("XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID")->field("SBPD,SBYJ,SH_PRC")->find();
		$SH_PRC=intval($rowsh[SH_PRC])+1;
	    $SBPD = $rowsh["SBPD"];
	    $SBYJ = $rowsh["SBYJ"];

		$NEXT_USER_ID = $arProUsers[$SH_PRC];
		//print_r($arProUsers);
		
		$ROW=$daouser->where("USER_ID = '$NEXT_USER_ID'")->find();
		$NEXT_USER_NAME = $ROW["USER_NAME"];
		    
		$arSBPDs = explode("@",$SBPD);
		$arSBYJs = explode("@",$SBYJ);
		//print_r($arSBPDs);
			/*-------------项目信息-----------*/
			$q .= " LEFT JOIN DEPARTMENT C ON A.BM_ID = C.DEPT_ID ";
			$q .= " LEFT JOIN USER D ON A.ZSBR_ID = D.USER_ID ";
			$q .= " LEFT JOIN USER E ON A.FSBR_ID = E.USER_ID ";			
			
			$row=$dao->table("xmsb_lb as A")
				          ->join("left join xmsb_sx B ON A.SX_ID = B.SX_ID".$q)
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
            /*-----------已经审核过的人的审核信息----------------*/
            
			  for($i = 0 ; $i < count($arSBPDs) ; $i++){
			  	$list[$i][SBPD]=$arSBPDs[$i];
			  	$list[$i][SBYJ]=$arSBYJs[$i];
			  	if(empty($list[$i][SBPD]) || empty($list[$i][SBYJ]))continue;
			  	$USER_ID = $arProUsers[$i];
			  	$user=$daouser->where(" USER_ID = '$USER_ID'")->field("USER_NAME")->find();
			  	$list[$i][USER_NAME] = $user["USER_NAME"];
			  }
			  //print_r($list);
	         $this->assign("list",$list);
             $this->assign("dept_name",$dept_name);
             $this->assign("user_name",$user_name);
             $this->assign("TYPE_ID",$TYPE_ID);
             $this->assign("XM_ID",$XM_ID);
             			  		
		if($SH_PRC == count($arProUsers)){//所有初审完毕，初审的最后一步
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
            //-----------终审人员列表---------
            $daolc=D("XmsbLc");
            $pro = new Program();
            
    		$pro->xm_getPrc("ZS",$ZS_TYPE_NAME,$ZS_TYPE_ID);
    		$listlc=$daolc->table("xmsb_lc a")
    		                ->join("user b on a.USER_ID = b.USER_ID")
    		                ->field("a.USER_ID,b.USER_NAME")
    		                ->where("a.TYPE_ID = $ZS_TYPE_ID")
    		                ->findall();
    		//echo $daolc->getlastsql(); 
    		$arData=array();               
			foreach ($listlc as $ROW){
				$arData[$ROW["USER_ID"]] = $ROW["USER_NAME"];
			}  
    		$listuser=my_select("ZS_CURRENT_USER",$arData,"BigSelect","");
            $this->assign("listuser",$listuser);
            
            //$XMZT_ZS $XMZT_CREATE $XMZT_CSREJECT
            $this->assign("XMZT_ZS",XMZT_ZS);
            $this->assign("XMZT_CREATE",XMZT_CREATE);
            $this->assign("XMZT_CSREJECT",XMZT_CSREJECT);
            $this->assign("SJ",date("Y-m-d",$this->CUR_TIME_INT));
            
			$this->display('manage_saudit');
		}elseif($SH_PRC < count($arProUsers)){//初审过程
			
             $this->assign("NEXT_USER_ID",$NEXT_USER_ID);
             $this->assign("NEXT_USER_NAME",$NEXT_USER_NAME);
			$this->display('manage_paudit');
		}
			
    	
    }
    

    public function pauditSubmit(){//初审审核中间步骤提交
         $XM_ID=$_POST[XM_ID];
         $TYPE_ID=$_POST[TYPE_ID];
    	 $daosh=D("XmsbSh");
    	 $ROW=$daosh->where("XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID")->find();
		 $SBPD_new = $ROW["SBPD"].$_POST[SBPD]."@";
		 $SBYJ_new = $ROW["SBYJ"].$_POST[SBYJ]."@";
		 $sql = "UPDATE xmsb_sh SET SBPD = '$SBPD_new',SBYJ = '$SBYJ_new',SH_PRC = SH_PRC+1 WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";
		 $daosh->execute($sql);
		 //echo $daosh->getlastsql();
		 //exit;
		 /*---------- 短信提醒 ----------*/
		 $daosms=D("Sms");		 
		 $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
		 $data[CONTENT]="项目：您的新的申报初审工作要办理！";
         $data[FROM_ID]=$this->LOGIN_USER_ID;
         $data[TO_ID]=$_POST[NEXT_USER_ID];
         $data[SMS_TYPE]="10";
         $data[REMIND_FLAG]=1; 		 
		 $daosms->create();
		 $daosms->add($data);
		 
		 $this->redirect("index","procs");
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
				
                $q = "UPDATE xmsb_sh SET ZJPD = '$_POST[ZJPD]',ZJYJ = '$_POST[ZJYJ]',SJ = '$_POST[SJ]',SH_PRC = 0,WT_ID=$_POST[WT_ID] WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";
				$daosh->execute($q);
				
				$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
				$daolb->execute($q);
				
				/*---------- 短信提醒项目主申请人 ----------*/
				 $ROW=$daolb->where("XM_ID = $XM_ID")->find();
				 
				 $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
				 $data[CONTENT]="项目：您的项目没有通过初审！";
		         $data[FROM_ID]=$this->LOGIN_USER_ID;
		         $data[TO_ID]=$ROW[ZSBR_ID];
		         $data[SMS_TYPE]="17";
		         $data[REMIND_FLAG]=1; 		 
				 $daosms->create();
				 $daosms->add($data);
				 				
			 }
			 else if($option == XMZT_CSREJECT){
			 	$q = "UPDATE xmsb_sh SET ZJPD = '$_POST[ZJPD]',ZJYJ = '$_POST[ZJYJ]',SH_PRC = 0,SJ='$_POST[SJ]',WT_ID=$_POST[WT_ID],BZ='$_POST[BZ]' WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";
			 	$daosh->execute($q);
			 	
			 	$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
			 	$daolb->execute($q);

			 }
			 else if($option == XMZT_ZS){
			 	$q = "UPDATE xmsb_sh SET ZJPD = '$_POST[ZJPD]',ZJYJ = '$_POST[ZJYJ]',SH_PRC = SH_PRC+1,SJ='$_POST[SJ]',WT_ID=$_POST[WT_ID],BZ='$_POST[BZ]' WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";			 	
			 	$daosh->execute($q);
			 	
			 	$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
			 	$daolb->execute($q);
			 	
			 	$pro=new Program();
			 	$pro->xm_getPrc('ZS',$TYPE_NAME_ZS,$TYPE_ID_ZS);
				$query="INSERT INTO xmsb_sh (XM_ID,TYPE_ID,ZS_CURRENT_USER) VALUES ($XM_ID,$TYPE_ID_ZS,'$_POST[ZS_CURRENT_USER]')";
				$daosh->execute($query);
				
                 //------------	短信提醒项目项目终审人员			
				$TOK = $ZS_CURRENT_USER;
			    $SMS_CONTENT="项目：您有新的终审工作需要办理！";
			    $SEND_TIME=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
			    $query1="insert into SMS(FROM_ID,TO_ID,SMS_TYPE,CONTENT,SEND_TIME,REMIND_FLAG) values ('$this->LOGIN_USER_ID','$TOK','11','$SMS_CONTENT','$SEND_TIME','1')";
			    $daosms->execute($query1);

			 }
			 
			 $this->redirect("index","procs");
     	
     }
    
    
}
?>