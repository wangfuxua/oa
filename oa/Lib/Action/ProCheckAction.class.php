<?php
/*
 功能：项目复核
 
*/
import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");

class ProCheckAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目复核管理";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
    public function index(){//申报项目列表
    	$pro=new Program();
        $pro->xm_getPrc('FH',$TYPE_NAME,$TYPE_ID);
        //echo $TYPE_ID;
        //echo $this->LOGIN_DEPT_ID;
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

		
		//print_r($arProUsers);
		//echo $this->LOGIN_USER_ID;
		
		//if(in_array($this->LOGIN_USER_ID,$arProUsers)){
	            import("ORG.Util.Page");
	            $dao= new Model();
                $rowc=$dao->table('xmsb_sh as A')
                         ->join('xmsb_lb as B ON A.XM_ID = B.XM_ID')
                         ->where("B.XMZT = '".XMZT_FH."' AND A.TYPE_ID = '$TYPE_ID'"." AND A.SH_PRC IN (".implode(",",$arPRCs).")".$map)
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
                         ->where("B.XMZT = '".XMZT_FH."' AND A.TYPE_ID = '$TYPE_ID'"." AND A.SH_PRC IN (".implode(",",$arPRCs).")".$map)
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
	            $pro->xm_getPrc("ZS",$TYPE_NAME_CS,$TYPE_ID_ZS);
				$rowsh=$daosh->where("XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID_ZS")->field("SBPD,SBYJ,ZJPD,ZJYJ")->find();
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
            $this->assign("XMZT_SUCCESS",XMZT_SUCCESS);
            $this->assign("XMZT_CREATE",XMZT_CREATE);
            $this->assign("XMZT_FHREJECT",XMZT_FHREJECT);
            $this->assign("SJ",date("Y-m-d",$this->CUR_TIME_INT));
            
			$this->display();
		
			
    	
    }

        public function submit(){//初审审核最后一步提交
         $XM_ID=$_POST[XM_ID];
         $TYPE_ID=$_POST[TYPE_ID];
         $option=$_POST[option];
     	      $daosh=D("XmsbSh");
     	      $daolb=D("xmsblb");
     	      $daosms=D("Sms");
     	      
			if($option == XMZT_CREATE){//未通过
				$daosh->where("XM_ID = $XM_ID AND TYPE_ID <> $TYPE_ID")->delete();//不理解为什么用不等于type_id
                $q = "UPDATE xmsb_sh SET ZJPD = '$_POST[ZJPD]',ZJYJ = '$_POST[ZJYJ]',SJ = '$_POST[SJ]',SH_PRC = 0  WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";
				$daosh->execute($q);
				
				$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
				$daolb->execute($q);
				
				/*---------- 短信提醒项目主申请人 ----------*/
				 $ROW=$daolb->where("XM_ID = $XM_ID")->find();
				 
				 $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
				 $data[CONTENT]="项目：您有新的立项工作需要办理！";
		         $data[FROM_ID]=$this->LOGIN_USER_ID;
		         $data[TO_ID]=$ROW[ZSBR_ID];
		         $data[SMS_TYPE]="13";
		         $data[REMIND_FLAG]=1; 		 
				 $daosms->create();
				 $daosms->add($data);
				 				
			 }
			 else if($option == XMZT_FHREJECT){//项目申请终止
 			 	$q = "UPDATE xmsb_sh SET ZJPD = '$_POST[ZJPD]',ZJYJ = '$_POST[ZJYJ]',SH_PRC = 0,SJ='$_POST[SJ]',BZ='$_POST[BZ]' WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";
			 	$daosh->execute($q);
			 	
			 	$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
			 	$daolb->execute($q);

			 }
			 else if($option == XMZT_SUCCESS){//项目申请成功
    	        $daosh=D("XmsbSh");
	 	        $q = "UPDATE xmsb_sh SET ZJPD = '$_POST[ZJPD]',ZJYJ = '$_POST[ZJYJ]',SH_PRC = SH_PRC+1,SJ='$_POST[SJ]',BZ='$_POST[BZ]' WHERE XM_ID = $XM_ID AND TYPE_ID = $TYPE_ID";			 			 	
			 	$daosh->execute($q);
			 	$q = "UPDATE xmsb_lb SET XMZT = $option WHERE XM_ID = $XM_ID";
			 	$daolb->execute($q);
		 }
			 
			 $this->redirect("index","prozs");
     	
     }   
    
    
}
?>	