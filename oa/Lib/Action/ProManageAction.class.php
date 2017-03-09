<?php
import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");
class ProManageAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目管理";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
    public function index(){
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
   /*--------------------------新建申报form表单--------------------*/ 
   public function applynew(){
   	$XM_ID=$_REQUEST[XM_ID];
   	$BM_ID=$_REQUEST[BM_ID];
   	
    if(!$XM_ID){
    	    $vo=array();
		    $dao=D("XmsbSx");
		    $rows=$dao->field("COUNT(*) AS cont")->find();
		    if(empty($rows[cont])){
		    	$this->error("无可用的项目申报属性！请先到系统菜单设置项目申报属性！");
		    }
		    
		    //"SELECT COUNT(A.LC_ID) as cnt FROM xmsb_lc A LEFT JOIN metadata B ON A.TYPE_ID = B.ID WHERE B.NAME = 'XMLC' AND B.value1 = 'CS'"
		    $rows=$dao->table("xmsb_lc as A")
		              ->join("metadata as B on A.TYPE_ID = B.ID")
		              ->where("B.NAME = 'XMLC' AND B.value1 = 'CS'")
		              ->field("A.LC_ID")
		              ->find();
		    if(empty($rows)){
		    	$this->error("没有定义初审流程！请先到系统菜单设置初审流程！");
		    }              
		    $rows=$dao->table("xmsb_lc as A")
		              ->join("metadata as B on A.TYPE_ID = B.ID")
		              ->where("B.NAME = 'XMLC' AND B.value1 = 'ZS'")
		              ->field("A.LC_ID")
		              ->find();
		    if(empty($rows)){
		    	$this->error("没有定义终审流程！请先到系统菜单设置终审流程！");
		    } 
		    $rows=$dao->table("xmsb_lc as A")
		              ->join("metadata as B on A.TYPE_ID = B.ID")
		              ->where("B.NAME = 'XMLC' AND B.value1 = 'FH'")
		              ->field("A.LC_ID")
		              ->find();
		    if(empty($rows)){
		    	$this->error("没有定义复核人员！请先到系统菜单设置复核人员！");
		    }
		    
		    $rows=$dao->table("xmsb_wt as A")
		              ->join("metadata as B on A.TYPE_ID = B.ID")
		              ->where("B.NAME = 'XMLC' AND B.value1 = 'CS'")
		              ->field("A.WT_ID")
		              ->find();
		    //echo $dao->getlastsql();exit;          
		    if(empty($rows)){
		    	$this->error("没有定义初审问题类型！请先到系统菜单设置初审问题类型！");
		    }
		    $rows=$dao->table("xmsb_wt as A")
		              ->join("metadata as B on A.TYPE_ID = B.ID")
		              ->where("B.NAME = 'XMLC' AND B.value1 = 'ZS'")
		              ->field("A.WT_ID")
		              ->find();
		    if(empty($rows)){
		    	$this->error("没有定义终审问题类型！请先到系统菜单设置终审问题类型！");
		    }
			
			$vo[SJ]=date("Y-m-d",$this->CUR_TIME_INT);

            $att="无附件"; 
 	        $SECRET_TO_NAME="";  
			
			
    }else {
    	$vo=array();
    	$dao=D("xmsblb");
    	$vo=$dao->where("XM_ID=$XM_ID")->find();
    	$att=$this->_attch($vo[FJ_ID],$vo[FJ_NAME]);
 
    	 $SECRET_TO_NAME="";
		 $TOK=strtok($vo[FSBR_ID],",");
		 while($TOK!="")
		 {
		   if($SECRET_TO_NAME!="")
		      $SECRET_TO_NAME.="，";
		   $daouser=D("user");
		   $ROW1=$daouser->where("USER_ID='$TOK'")->find();
           $SECRET_TO_NAME.=$ROW1["USER_NAME"];
		   $TOK=strtok(",");
		 }
     	
    	
    }
            $dao=D("XmsbSx");
		    $rows=$dao->order("SX_NAME")->findall();
		    foreach ($rows as $ROW){
		    	$arData[$ROW["SX_ID"]] = $ROW["SX_NAME"];
		    }
		    $sbsx=my_select("SX_ID",$arData,"BigSelect");//申报属性

		    if(isset($vo[BM_ID])){
				$CHOOSE_ID = $vo[BM_ID];
			}else{
				$CHOOSE_ID = $this->LOGIN_DEPT_ID;
			}
			$bmsx = my_dept_tree(0,$CHOOSE_ID,0);//部门属性
			
			if(isset($vo[ZSBR_ID])){
				$CHOOSE_ID = $vo[ZSBR_ID];
			}
			else{
				$CHOOSE_ID = $this->LOGIN_USER_ID;
			}
			$zsqr = my_user_list("USER_NAME","USER_ID",$CHOOSE_ID);//人员列表
			
			$dao=D("templates");//申报模板
 	        $tpllist=$dao->table('templates as a')
 	          ->join('attachments as b on a.attid=b.attid')
 	          ->where("a.LB='XMSB' and a.attid!='0'")
 	          ->findall();
 	          
    
    $this->assign("vo",$vo);
    $this->assign("SECRET_TO_NAME",$SECRET_TO_NAME);
    $this->assign("att",$att);
    $this->assign("tpllist",$tpllist);
    
    $this->assign("bmsx",$bmsx);
    $this->assign("sbsx",$sbsx);
    $this->assign("zsqr",$zsqr);
   	$this->display();
   }   
   /*-----------------------项目申报--insert动作--------------------*/
    public function applyinsert(){
       $YSZJ=$_POST[YSZJ];
	   if(!is_number($YSZJ)){
		    if(!is_money($YSZJ)){
			    $this->error("项目预算资金错误");
			}
		}elseif(intval($YSZJ) <0){
			   $this->error("项目预算资金错误");
		}
		
		$dao=D("xmsblb");
		if ($_POST[JB]){
			$row=$dao->where("JB = 1 AND MC LIKE '".$_POST[MC]."-%' AND SJ <= '$_POST[SJ]'")
			         ->field("COUNT(XM_ID) as cnt")
			         ->find();
			if (!empty($row)){
				$n=intval($row["cnt"])+1;
				$_POST[MC]=$_POST[MC]."-".$n;
			}else {
				$_POST[MC]=$_POST[MC]."-1";
			}
		}

		$path	=	$this->uploadpath;
		$info	=	$this->_upload($path);
		$data =$info[0];
		if($data){
			$data[addtime]=$this->CUR_TIME_INT;
			$data[filename]=$data[name];
			$data[attachment]=$data[savename];
			$data[filesize]=$data[size];
			$data[filetype]=$data[type];
			
			$daoatt = D("Attachments");
			$daoatt->create();
			$attid=$daoatt->add($data);
			if ($attid) {
		        $_POST[FJ_ID]=$_POST[FJ_ID].$attid.",";
				$_POST[FJ_NAME]=$_POST[ATTACHMENT_NAME_OLD].$data[name]."*";
			}
		}
		
		$_POST[FSBR_ID]=$_POST[SECRET_TO_ID];//副申报人ID（申报人员）;
		if (empty($_POST[XM_ID])){
			if(!$_POST[OPERATION]){//如果不是添加或删除附件则设置项目状态为初审
				$_POST[XMZT]==XMZT_CS;
			}
			if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        }
	        $id = $dao->add();
		}else {
			if(false === $dao->create()) {
        	   $this->error($dao->getError());
            }
            $dao->where("XM_ID='$_POST[XM_ID]'")->save();
            $id=$_POST[XM_ID];
		}
        if($_POST[OPERATION]=="add"){//添加或是删除附件
             $this->redirect('applynew/XM_ID/'.$id,'promanage');//返回到原表单
        }else {//提交并初审
        	$pro=new Program();
        	$pro->xm_getPrc('CS',$TYPENAME,$TYPEID);//得到初审的TYPEID,这个是metadata表里定义好的
        	
        	$daosh=D("XmsbSh");//初审
        	$daosh->create();
        	
        	$data[XM_ID]=$id;
        	$data[TYPE_ID]=$TYPEID;
        	$data[SH_PRC]=0;
        	$daosh->add($data);
        	
        	//---------- 短信提醒 ----------
        	$dao=D("XmsbLc");
        	$row=$dao->where("TYPE_ID = $TYPEID AND BM_ID = $_REQUEST[BM_ID]")
        	         ->field("USER_ID")
        	         ->order("ORDERING ASC")
        	         ->find();
    
		    $datas[FROM_ID]=$this->LOGIN_USER_ID;
		    $datas[TO_ID]=$row[USER_ID];
		    $datas[SMS_TYPE]=10;
		    $datas[SMS_CONTENT]="项目：您有新的初审工作需要办理！";
		    $datas[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
		    $datas[REMIND_FLAG]=1;
		    $daosms=D("Sms");
		    $daosms->create();
		    $daosms->add($datas);
		    
        	$this->success('成功添加');
        }
				
    }   
    public function applylist(){
    	//echo XMZT_CREATE;
    	//$dao=D("xmsblb");
    	//$list=$dao->where("(ZSBR_ID='$LOGIN_USER_ID' OR FSBR_ID='$LOGIN_USER_ID') AND XMZT = ".XMZT_CREATE)
    	//          ->find();
    	    
    	    $dao=D('xmsblb');
	        $map="(ZSBR_ID='$this->LOGIN_USER_ID' OR FSBR_ID='$this->LOGIN_USER_ID') AND XMZT = ".XMZT_CREATE;
	        $maps="B.SH_PRC = 0 AND (A.ZSBR_ID='$this->LOGIN_USER_ID' OR A.FSBR_ID='$this->LOGIN_USER_ID') AND A.XMZT = ".XMZT_CREATE;
	        $count=$dao->count($map);
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
                          ->join("xmsb_sh as B ON A.XM_ID = B.XM_ID")
                          ->where($maps)
                          ->field("A.XM_ID,A.MC,A.SJ,A.JB,A.WCSJ,B.ZJPD,B.WT_ID,B.SJ as PDSJ")
                          ->findall();	
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
			$this->assign("page",$page);
			$this->assign('list',$list);
			
	        }
    	
            $this->display();	
    }
    public function applydelete(){//删除项目
    	$XM_ID=$_REQUEST[XM_ID];
    	if($XM_ID){
	    	$dao=D("XmsbSh");
	    	$dao->where("XM_ID=$XM_ID")->delete();
	    	$dao=D("xmsblb");
	    	$row=$dao->where("XM_ID=$XM_ID")->field("FJ_ID")->find();
	    	$attarr=explode(",",$row[FJ_ID]);
	    	foreach ($attarr as $attid){
	    		if($attid)
	    		   $this->_deleteattach($attid);
	    	}
	    	$dao->where("XM_ID=$XM_ID")->delete();
	    	
	    	$this->success('成功删除');
    	}else{
    	  $this->success('删除失败');	
    	}
    	
    }
    public function applydeleteattach(){//删除附件
    	$attid=$_REQUEST[ATTACHMENT_ID];
    	$XM_ID=$_REQUEST[XM_ID];
	    $this->_deleteattach($attid);
	    $dao=D("xmsblb");
	    $row=$dao->where('XM_ID=$XM_ID')->find();
	    $attarr=explode(",",$row[FJ_ID]);
	    $namearr=explode("*",$row[FJ_NAME]);
	    
    	foreach ($attarr as $i=>$attids){
	    	if($i==$attid||$attids=="")
	    	   continue;
	        $data[FJ_ID] .= $attids.",";
	        $data[FJ_NAME] .= $namearr[$i]."*";		   
	    }
	    
	    $dao->create();
	    $dao->where("XM_ID='$XM_ID'")->save($data);
	    if($_REQUEST[TO]=='edit'){
	         $this->redirect('applyedit/XM_ID/'.$XM_ID,'promanage');	
	    }else{
             $this->redirect('applynew/XM_ID/'.$XM_ID,'promanage');	    	
	    }
	    
	    
    }
    
	public function applyedit(){
		$XM_ID=$_REQUEST[XM_ID];
		
		$dao=D("xmsblb");
		$vo=$dao->table("xmsb_lb as A")
		        ->join("xmsb_sh as B ON A.XM_ID = B.XM_ID")
		        ->field("A.SX_ID,A.XM_ID,A.MC,A.SJ,A.JB,A.ZSBR_ID,A.FSBR_ID,A.FJ_ID,A.FJ_NAME,A.XMLY,A.YSZJ,A.XMJJ,A.WCSJ,B.ZJPD,B.ZJYJ,B.WT_ID,B.SJ as PDSJ")
		        ->where("A.XM_ID = $XM_ID")
		        ->find();
		        
		$daowt=D("XmsbWt");       
		$row=$daowt->field("WT_NAME")->where("WT_ID = '$vo[WT_ID]'")->find();
		$vo[WT_NAME]=$row[WT_NAME];
    	
		 $att=$this->_attch($vo[FJ_ID],$vo[FJ_NAME]);
    	 
		 $SECRET_TO_NAME="";
		 $TOK=strtok($vo[FSBR_ID],",");
		 while($TOK!="")
		 {
		   if($SECRET_TO_NAME!="")
		      $SECRET_TO_NAME.="，";
		   $daouser=D("user");
		   $ROW1=$daouser->where("USER_ID='$TOK'")->find();
           $SECRET_TO_NAME.=$ROW1["USER_NAME"];
		   $TOK=strtok(",");
		 }
		 		
		
            $dao=D("XmsbSx");
		    $rows=$dao->order("SX_NAME")->findall();
		    foreach ($rows as $ROW){
		    	$arData[$ROW["SX_ID"]] = $ROW["SX_NAME"];
		    }
		    //echo $vo[SX_ID];
		    $sbsx=my_select("SX_ID",$arData,"BigSelect",$vo[SX_ID]);//申报属性

		    if(isset($vo[BM_ID])){
				$CHOOSE_ID = $vo[BM_ID];
			}else{
				$CHOOSE_ID = $this->LOGIN_DEPT_ID;
			}
			$bmsx = my_dept_tree(0,$CHOOSE_ID,0);//部门属性
			
			if(isset($vo[ZSBR_ID])){
				$CHOOSE_ID = $vo[ZSBR_ID];
			}
			else{
				$CHOOSE_ID = $this->LOGIN_USER_ID;
			}
			$zsqr = my_user_list("USER_NAME","USER_ID",$CHOOSE_ID);//人员列表
			
			$dao=D("templates");//申报模板
 	        $tpllist=$dao->table('templates as a')
 	          ->join('attachments as b on a.attid=b.attid')
 	          ->where("a.LB='XMSB' and a.attid!='0'")
 	          ->findall();
 	          
	
		
	$this->assign("vo",$vo);	
    $this->assign("SECRET_TO_NAME",$SECRET_TO_NAME);
    $this->assign("att",$att);
    $this->assign("tpllist",$tpllist);
    
    $this->assign("bmsx",$bmsx);
    $this->assign("sbsx",$sbsx);
    $this->assign("zsqr",$zsqr);
    
    $this->display();		
    
	}
	
	public function applyupdate(){
		    $xm_id=$_REQUEST[XM_ID];
		    
			$path	=	$this->uploadpath;
			$info	=	$this->_upload($path);
			$data =$info[0];
			if(!empty($data)){
				$data[addtime]=$this->CUR_TIME_INT;
				$data[filename]=$data[name];
				$data[attachment]=$data[savename];
				$data[filesize]=$data[size];
				$data[filetype]=$data[type];
				$daoatt = D("Attachments");
				$daoatt->create();
				$attid=$daoatt->add($data);
				//ECHO $daoatt->getlastsql();
	        $_POST[FJ_ID]=$_POST[FJ_ID].$attid.",";
			$_POST[FJ_NAME]=$_POST[ATTACHMENT_NAME_OLD].$data[name]."*";
			}
		       $dao=D("xmsblb");
		       if(empty($_POST[OPERATION])){//如果不是添加或删除附件则设置为初审
				   $_POST[XMZT]=XMZT_CS;
		        }
		        
				if (false===$dao->create()) {
	                  $this->error($dao->getError());
				}
				
		      $dao->where("XM_ID='$_POST[XM_ID]'")->save();
//		ECHO $dao->getlastsql();
	//	exit;
		if($_POST[OPERATION]=="add") {
			//header('location:'.__URL__.'/applyedit/XM_ID/'.$_POST[XM_ID]);
			//ECHO "A";exit;
			
			$this->redirect('applyedit/XM_ID/'.$_POST[XM_ID],'promanage');
		}else {

			$pro=new Program();
        	$pro->xm_getPrc('CS',$TYPENAME,$TYPEID);
        	
        	$daosh=D("XmsbSh");//初审
        	$daosh->where("XM_ID='$_POST[XM_ID]'")->delete();//删除旧的审核
        	$daosh->create();
        	
        	$data[XM_ID]=$_POST[XM_ID];
        	$data[TYPE_ID]=$TYPEID;
        	$data[SH_PRC]=0;
        	$daosh->add($data);
        	//---------- 短信提醒 ----------
        	$dao=D("XmsbLc");
        	// AND BM_ID = $_REQUEST[BM_ID]
        	$row=$dao->where("TYPE_ID = $TYPEID")
        	         ->field("USER_ID")
        	         ->order("ORDERING ASC")
        	         ->find();
    
		    $datas[FROM_ID]=$this->LOGIN_USER_ID;
		    $datas[TO_ID]=$row[USER_ID];
		    $datas[SMS_TYPE]=10;
		    $datas[SMS_CONTENT]="项目：您有新的初审工作需要办理！";
		    $datas[SEND_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
		    $datas[REMIND_FLAG]=1;
		    $daosms=D("Sms");
		    $daosms->create();
		    $daosms->add($datas);
			
			
			$this->success('成功修改');					
		}
		  
		

		
		
		
	}
	
}

?>
    