<?php
/*
 功能：项目复核
 
*/
import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");

class ProExeCreateAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="立项";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
    public function index(){//申报项目列表
    	$this->display();
    }
    public function menutop(){
    	$COLOR1="#D9E8FF";
        $COLOR2="#DDDDDD";
        $COLOR3="#B3D1FF";
        
        $this->assign("COLOR1",$COLOR1);
        $this->assign("COLOR2",$COLOR2);
        $this->assign("COLOR3",$COLOR3);
   	    $this->display();
    }
    /*-------------待立项的项目申报列表-------------*/
    public function createindex(){//科技性立项，也就是从项目申报提交过来的
    	$dao=D("xmsblb");
    	$map="XMZT = ".XMZT_SUCCESS;
    	$count=$dao->count($map);
    	//echo $dao->getlastsql();
    		if($count>0){
	            import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p          = new Page($count,$listRows);
				//分页查询数据
				
				$list=$dao->table("xmsb_lb as A")
				          ->join("left join department as D on A.BM_ID=D.DEPT_ID left join user as U on A.ZSBR_ID=U.USER_ID")
                          ->where("A.XMZT = '".XMZT_SUCCESS."'")
				          ->field('A.XM_ID,A.MC,A.BM_ID,A.ZSBR_ID,A.SJ,A.WCSJ,A.JB,D.DEPT_NAME as BM_NAME,U.USER_NAME as ZSBR_NAME')
				          ->order('A.BM_ID ASC,A.SJ DESC')
				          ->findAll();
				//echo $dao->getlastsql();          				
               // $list=$dao->findall($map);	
                //分页跳转的时候保证查询条件		
				foreach($map as $key=>$val) {
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						$p->parameter   .=   "$key=".urlencode($val)."&";        
					}
				}
			//分页显示
			$page       = $p->show();
	      }
    	
			$this->assign("page",$page);
			$this->assign('list',$list);
			//$this->assign("XM_ID",$XM_ID);
			//echo "a";
			$this->display();    	
    	
    }
    /*------------------------------待立项的项目详细信息---------------*/
    public function detail(){
    	$XM_ID=$_REQUEST[XM_ID];
    	$dao=new Model();
    	$daouser=D("user");
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
            $this->display();
    }
    /*--------------------------项目立项表表单--------------------*/
    public function createnew(){//立项 -- 科技型项目
    	    $XM_ID=$_REQUEST[XM_ID];
    	    //$att="无附件"; 
 	        //$SECRET_TO_NAME="";  
 	        $dao=D("templates");//项目模板
 	        $tpllist=$dao->table('templates as a')
 	          ->join('attachments as b on a.attid=b.attid')
 	          ->where("a.LB='XMLX' and a.attid!='0'")
 	          ->findall();
 	        
 	       		/*-------------登录用户信息--------*/
 	       		$daouser=D("user");
		        $daodept=D("department");
				$depts=$daodept->where("DEPT_ID = $this->LOGIN_DEPT_ID")->find();
				$dept_name=$depts[DEPT_NAME];
				$users=$daouser->where("USER_ID = '$this->LOGIN_USER_ID'")->find();
				$user_name=$users[USER_NAME];
	 	         $this->assign("dept_name",$dept_name);
	             $this->assign("user_name",$user_name);  
            
	       
            
             $dao=D("XmssLx");
             $row=$dao->where("XM_ID='$XM_ID'")->find();
             //立项时间
             if (!$row[LXSJ])
	            $row[LXSJ]=date("Y-m-d",$this->CUR_TIME_INT); 
            //项目负责人
            $listuser = my_user_list("USER_NAME","USER_ID",$row[XMFZR_ID]);//人员列表 
            $this->assign("listuser",$listuser);
            //项目类别
            $arData = array();
            $dao=D("XmssLb");
            $list=$dao->order('LB_NAME')->field("LB_ID,LB_NAME")->findall();
            foreach ($list as $ROW){
            	$arData[$ROW["LB_ID"]] = $ROW["LB_NAME"];
            }
            $xmlb= my_select("XMLB_ID",$arData,"BigSelect",$$row[XMLB_ID]);
            $this->assign("xmlb",$xmlb);
             //附件
            $att=$this->_attch($row[FJ_ID],$row[FJ_NAME]);
             //项目组成员           
		 	 $SECRET_TO_NAME="";
			 $TOK=strtok($row[XMZ_IDS],",");
			 //echo $row[XMZ_IDS];
			 while($TOK!="")
			 {
			   if($SECRET_TO_NAME!="")
			      $SECRET_TO_NAME.="，";
			      
			      $ROW1=$daouser->where("USER_ID='$TOK'")->find();
			      $SECRET_TO_NAME.=$ROW1["USER_NAME"];
			      
			   $TOK=strtok(",");
			 }
 		    $this->assign("SECRET_TO_NAME",$SECRET_TO_NAME);
		    
		    $this->assign("att",$att);
		    $this->assign("tpllist",$tpllist);
		    $this->assign("XMZT_LX",XMZT_LX);
		    $this->assign("XM_ID",$XM_ID);
		    
    		$this->assign("row",$row);
    	    $this->display();
    }
/*-----------------------------------立项操作--之不立项------------*/    
    public function uncreate(){
    	$dao=D("xmsblb");
    	$today = getdate();
        $date = $today["year"]."-".$today["mon"]."-".$today["mday"];
        $q = "UPDATE xmsb_lb SET XMZT = ".XMZT_UNCREATE.", SJSJ = '$date' WHERE XM_ID =".$_REQUEST[XM_ID];
    	$dao->execute($q);
    	$this->redirect("createindex","proexecreate");
    	
    }
 /*--------------------------立项目操作--立项表单提交---------------*/   
    public function submit(){
    	 $pro=new Program();
    	 $XM_ID=$_REQUEST[XM_ID];
    	 $OPERATION=$_REQUEST[OPERATION];
    	 $daolx=D("XmssLx");
    	 $pd=$daolx->where("XM_ID='$XM_ID'")->find();
    	 //print_r($_POST);
    	 //EXIT;
    	 $_POST[LXBM_ID]=$this->LOGIN_DEPT_ID;
    	 $_POST[LXRY_ID]=$this->LOGIN_USER_ID;
    	 $_POST[XMZ_IDS]=$_POST[SECRET_TO_ID];
    	 $_POST[XMBH] = $pro->xm_getXMBH($_POST[XMSX]);
    	 
    	 if($OPERATION=="add"){//添加附件
				$path	=	$this->uploadpath;
				//echo $path;exit;
				$info	=	$this->_upload($path);
				$data =$info[0];
				if($data){
					$data[addtime]=$this->CUR_TIME_INT;
					$data[filename]=$data[name];
					$data[attachment]=$data[savename];
					$data[filesize]=$data[size];
					$data[filetype]=$data[type];
					
					$dao = D("Attachments");
					$dao->create();
					$attid=$dao->add($data);
					
					if ($attid) {
			             $_POST[FJ_ID]=$_POST[FJ_ID].$attid.",";
					     $_POST[FJ_NAME]=$_POST[ATTACHMENT_NAME_OLD].$data[name]."*";						
					}	

				}
		    	 	
    	 $daolx->create();
    	 if($pd){
    	 	$daolx->where("XM_ID='$XM_ID'")->save();
    	 }else {
    	 	$daolx->add();
    	 }
    	 	$this->redirect("createnew/XM_ID/$XM_ID","proexecreate");
    	 	
    	 }elseif ($OPERATION=="del"){//删除附件
    	 	
    	 	
    	 }else {//提交表单
    	 	$XMSX=$_POST[XMSX];
			$XMSX = "S";
			if($XMSX=="S") $XMZT = XMZT_CREATE_SCIENCE;
			else $XMZT = XMZT_CREATE_ORDER;
			$q = "UPDATE xmsb_lb SET XMZT = $XMZT WHERE XM_ID =".$XM_ID;
    	    $daolb=D("xmsblb");
    	    $daolb->execute($q);
    	    
    	 	$daolx->create();
	    	 if($pd){
	    	 	$daolx->where("XM_ID='$XM_ID'")->save();
	    	 }else {
	    	 	$daolx->add();
	    	 }
		 /*---------- 短信提醒 ----------*/
		 $daosms=D("Sms");		 
		 $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
		 $data[CONTENT]="项目：您的项目已立项！";
         $data[FROM_ID]=$this->LOGIN_USER_ID;
         $data[TO_ID]=$_POST[XMFZR_ID];
         $data[SMS_TYPE]="14";
         $data[REMIND_FLAG]=1; 		 
		 $daosms->create();
		 $daosms->add($data);
		     	     	 	
    	 	$this->redirect("createindex","proexecreate");
    	 }
    	
    	
    }

    /*-------------------------------------------------------指令性项目------------------------------------*/
    /*----------------------新建项目立项-----------*/
    public function create1(){//
    	    $XM_ID=$_REQUEST[XM_ID];
    	    //echo "b";
    	    //$att="无附件"; 
 	        //$SECRET_TO_NAME="";  
 	        $dao=D("templates");//项目模板
 	        $tpllist=$dao->table('templates as a')
 	          ->join('attachments as b on a.attid=b.attid')
 	          ->where("a.LB='XMLX' and a.attid!='0'")
 	          ->findall();
 	        
 	       		/*-------------登录用户信息--------*/
 	       		$daouser=D("user");
		        $daodept=D("department");
				$depts=$daodept->where("DEPT_ID = $this->LOGIN_DEPT_ID")->find();
				
				$dept_name=$depts[DEPT_NAME];
				$users=$daouser->where("USER_ID = '$this->LOGIN_USER_ID'")->find();
				$user_name=$users[USER_NAME];
	 	         $this->assign("dept_name",$dept_name);
	             $this->assign("user_name",$user_name);  
            
             $dao=D("XmssLx");
             if($XM_ID)
                $row=$dao->where("XM_ID='$XM_ID'")->find();
             
             //立项时间
             if (!$row[LXSJ])
	            $row[LXSJ]=date("Y-m-d",$this->CUR_TIME_INT); 
	            
            //项目负责人
            $listuser = my_user_list("USER_NAME","USER_ID",$row[XMFZR_ID]);//人员列表 
            $this->assign("listuser",$listuser);
            
            //项目类别
            $arData = array();
            $dao=D("XmssLb");
            $list=$dao->order('LB_NAME')->field("LB_ID,LB_NAME")->findall();
            foreach ($list as $ROW){
            	$arData[$ROW["LB_ID"]] = $ROW["LB_NAME"];
            }
            $xmlb= my_select("XMLB_ID",$arData,"BigSelect",$$row[XMLB_ID]);
            $this->assign("xmlb",$xmlb);
             //附件
            $att=$this->_attch($row[FJ_ID],$row[FJ_NAME]);
             //项目组成员           
		 	 $SECRET_TO_NAME="";
			 $TOK=strtok($row[XMZ_IDS],",");
			 //echo $row[XMZ_IDS];
			 while($TOK!="")
			 {
			   if($SECRET_TO_NAME!="")
			      $SECRET_TO_NAME.="，";
			      
			      $ROW1=$daouser->where("USER_ID='$TOK'")->find();
			      $SECRET_TO_NAME.=$ROW1["USER_NAME"];
			      
			   $TOK=strtok(",");
			 }
 		    $this->assign("SECRET_TO_NAME",$SECRET_TO_NAME);
		    
		    $this->assign("att",$att);
		    $this->assign("tpllist",$tpllist);
		    $this->assign("XMZT_LX",XMZT_LX);
		    $this->assign("XM_ID",$XM_ID);
		    
    		$this->assign("row",$row);
    	    $this->display();
    }

    /*----------------项目立项提交--------------*/
    public function submit1(){
    	 $pro=new Program();
    	 $XM_ID=$_REQUEST[XM_ID];
    	 $OPERATION=$_REQUEST[OPERATION];
    	 $daolx=D("XmssLx");
    	 $pd=$daolx->where("XM_ID='$XM_ID'")->find();
    	 //print_r($_POST);
    	 //EXIT;
    	 $_POST[LXBM_ID]=$this->LOGIN_DEPT_ID;
    	 $_POST[LXRY_ID]=$this->LOGIN_USER_ID;
    	 $_POST[XMZ_IDS]=$_POST[SECRET_TO_ID];
    	 $_POST[XMBH] = $pro->xm_getXMBH($_POST[XMSX]);
    	 
    	 if($OPERATION=="add"){//添加附件
				$path	=	$this->uploadpath;
				//echo $path;exit;
				$info	=	$this->_upload($path);
				$data =$info[0];
				if($data){
					$data[addtime]=$this->CUR_TIME_INT;
					$data[filename]=$data[name];
					$data[attachment]=$data[savename];
					$data[filesize]=$data[size];
					$data[filetype]=$data[type];
					
					$dao = D("Attachments");
					$dao->create();
					$attid=$dao->add($data);
					if ($attid) {
				        $_POST[FJ_ID]=$_POST[FJ_ID].$attid.",";
						$_POST[FJ_NAME]=$_POST[ATTACHMENT_NAME_OLD].$data[name]."*";						
					}
		      }
    	 $daolx->create();
    	 if($pd){
    	 	$daolx->where("XM_ID='$XM_ID'")->save();
    	 }else {
    	 	$daolx->add();
    	 }
    	 	$this->redirect("create1/XM_ID/$XM_ID","proexecreate");
    	 	
    	 }elseif ($OPERATION=="del"){//删除附件
    	 	
    	 	
    	 }else {//提交表单
    	 	$XMSX=$_POST[XMSX];
			$XMSX = "S";
			if($XMSX=="S") $XMZT = XMZT_CREATE_SCIENCE;
			else $XMZT = XMZT_CREATE_ORDER;
			$q = "UPDATE xmsb_lb SET XMZT = $XMZT WHERE XM_ID =".$XM_ID;
    	    $daolb=D("xmsblb");
    	    $daolb->execute($q);
    	    
    	 	$daolx->create();
	    	 if($pd){
	    	 	$daolx->where("XM_ID='$XM_ID'")->save();
	    	 }else {
	    	 	$daolx->add();
	    	 }
		 /*---------- 短信提醒 ----------*/
		 $daosms=D("Sms");		 
		 $data[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
		 $data[CONTENT]="项目：您的项目已立项！";
         $data[FROM_ID]=$this->LOGIN_USER_ID;
         $data[TO_ID]=$_POST[XMFZR_ID];
         $data[SMS_TYPE]="14";
         $data[REMIND_FLAG]=1; 		 
		 $daosms->create();
		 $daosms->add($data);
		  //ECHO "A";EXIT;     	     	 	
    	$this->redirect("create1","proexecreate");
    	 }
    	
    	
    }
    
    
}
?>	