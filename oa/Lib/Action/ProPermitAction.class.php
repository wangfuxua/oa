<?php
/*
功能：项目审批
作者：陈洪鲁
时间：2008-12-10
*/

import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");
include_cache(APP_PATH."/Common/xm.php");

class ProPermitAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目审批";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		//this->LOGIN_USER_NAME=Session::get("LOGIN_USER_NAME");
		
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	/*---------------首页---显示搜索表单--------*/
	public function index(){
		//人员列表
		$listuser=my_user_list("USER_NAME","USER_ID");
		$this->assign("listuser",$listuser);
		
		$this->display();
	}
	/*--------------项目搜索结果---------------*/
	public function search(){
		//搜索条件 map
			$RELATION=$_REQUEST[RELATION];
			$MC=$_REQUEST[MC];
			$XMLY=$_REQUEST[XMLY];
			$XMJJ=$_REQUEST[XMJJ];
			$ZCY_ID=$_REQUEST[ZCY_ID];
			$J1=$_REQUEST[J1];
			$J2=$_REQUEST[J2];
			
		 if($MC!="")$WHERE_STR.=" $RELATION B.MC like '%".$MC."%'";
		 if($XMLY!="")$WHERE_STR.=" $RELATION B.XMLY like '%".$XMLY."%'";
		 if($XMJJ!="")$WHERE_STR.=" $RELATION B.XMJJ like '%".$XMJJ."%'";
		 if($ZCY_ID!="")$WHERE_STR.=" $RELATION (A.XMFZR_ID like '%$ZCY_ID%' OR A.XMZ_IDS LIKE '%$ZCY_ID%')";
		 if($SJ1!="" && $SJ2!="")$WHERE_STR .= " $RELATION A.LXSJ between '$SJ1' AND '$SJ2'";
		 else if($SJ1 != "")$WHERE_STR .= " $RELATION A.LXSJ >= '$SJ1'";
		 else if($SJ2 != "")$WHERE_STR .= " $RELATION A.LXSJ <= '$SJ2'";
		 	
		 if($WHERE_STR!=""){
		    $WHERE_STR=substr($WHERE_STR,strlen($RELATION)+2);
		    $map = "  AND (".$WHERE_STR.")";
		 }
		      
		 $dao= new Model();
		 $row=$dao->table("xmss_lx A")
		          ->join("xmsb_lb B ON A.XM_ID = B.XM_ID")
		          ->field("count(A.XM_ID) as num")
		          ->where("A.SP = 3".$map)
		          ->find();
         $count=$row[num];
         
         if ($count>0) {
         		import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '20';
				}
				if (empty($_REQUEST[p]))
				    $_REQUEST[p]=1;
				$firstRows=($_REQUEST[p]-1)*$listRows;
				//echo $firstRows;
				$p          = new Page($count,$listRows);
				
			    $list=$dao->table("xmss_lx A")
				          ->join("xmsb_lb B ON A.XM_ID = B.XM_ID")
				          ->field("A.XM_ID,A.LXSJ,A.XMFZR_ID,B.XMLY,B.MC")
				          ->where("A.SP = 3".$map)
				          ->limit("$p->firstRow,$p->listRows")
				          ->findall();
                //分页跳转的时候保证查询条件		
                /*
				foreach($map as $key=>$val) {
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						$p->parameter   .=   "$key=".urlencode($val)."&";        
					}
				}*/
								          
			   $page       = $p->show();
			   $this->assign("page",$page);
			   $this->assign('list',$list);
				
         }
		
         $this->display();
		
	}

	/*-------------项目详细信息-------------*/
	public function detail(){
		$XM_ID=$_REQUEST[XM_ID];
		$dao=new Model();
		//项目申报信息
		$row=$dao->table("xmsb_lb A")
		        ->join("left join xmsb_sx B ON A.SX_ID = B.SX_ID LEFT JOIN department C ON A.BM_ID = C.DEPT_ID LEFT JOIN user D ON A.ZSBR_ID = D.USER_ID")
		        ->field("A.MC,A.WCSJ,A.SJSJ,B.SX_NAME,C.DEPT_NAME as BM_NAME,D.USER_NAME as ZSBR_NAME,A.FSBR_ID,A.JB,A.SJ,A.XMLY,A.YSZJ,A.XMJJ,A.FJ_ID,A.FJ_NAME,A.XMZT")
		        ->where("A.XM_ID = '$XM_ID'")
		        ->find();
	   $this->assign("row",$row);
	   
		if(empty($row[SJSJ]) || $row[SJSJ] == '0000-00-00'){
	    	$SJSJ = "/";
	    }
	    if(empty($row[SJ]) || $row[SJ] == '0000-00-00'){
	    	$SJ = "/";
	    }
       $this->assign("SJSJ",$SJSJ);
       $this->assign("SJ",$SJ);
	   $this->assign("XMZT_UNCREATE",XMZT_UNCREATE);
	   
	   //项目申报附件
      $ATTACHMENT_NAME_ARRAY=explode("*",$row[FJ_NAME]);
      $ATTACHMENT_ID_ARRAY=explode(",",$row[FJ_ID]);
      $ARRAY_COUNT=sizeof($ATTACHMENT_NAME_ARRAY);
      for($I=0;$I<$ARRAY_COUNT;$I++)
      {
         if($ATTACHMENT_NAME_ARRAY[$I]=="")
           break;
         $list[$I][ATTACH_IMAGE]=image_mimetype($ATTACHMENT_NAME_ARRAY[$I]); 
         $list[$I][ATTACH_NAME]=$ATTACHMENT_NAME_ARRAY[$I]; 
         $list[$I][ATTACH_ID]=$ATTACHMENT_ID_ARRAY[$I];
      }	   
	  $this->assign("list",$list);
	  // 立项信息
       $row1=$dao->table("xmss_lx A")
	            ->join("left join DEPARTMENT B ON A.LXBM_ID = B.DEPT_ID LEFT JOIN USER C ON A.LXRY_ID = C.USER_ID LEFT JOIN USER D ON A.XMFZR_ID = D.USER_ID")
	            ->field("A.XMZ_IDS,A.LXSJ,A.XMSX,A.XMBH,A.XMZJ,A.LXYJ,A.FJ_ID,A.FJ_NAME,B.DEPT_NAME as LXBM_NAME,C.USER_NAME as LXRY_NAME,D.USER_NAME as XMFZR_NAME")
	            ->where("XM_ID = '$XM_ID'")
		        ->find();
	  	if($row1[XMSX] == "S")$row1[XMSX_DESC] = "科技性项目";
	    else $row1[XMSX_DESC] = "指令性项目";
	    
	    if($SJSJ == "/"){
	    	$FJ_ID = $row1["FJ_ID"];
	    	$XMZT = XMZT_LX;
	    	$extrapath = "";
	    }else{
	    	$extrapath = "lx";
	    }
	    $this->assign("XMZT",$XMZT);  	    
	   $this->assign("row1",$row1);
	   
	   //立项附件列表
      $ATTACHMENT_NAME_ARRAY=explode("*",$row1[FJ_NAME]);
      $ATTACHMENT_ID_ARRAY=explode(",",$row1[FJ_ID]);
      $ARRAY_COUNT=sizeof($ATTACHMENT_NAME_ARRAY);
      for($I=0;$I<$ARRAY_COUNT;$I++)
      {
         if($ATTACHMENT_NAME_ARRAY[$I]=="")
           break;
         $list[$I][ATTACH_IMAGE]=image_mimetype($ATTACHMENT_NAME_ARRAY[$I]); 
         $list[$I][ATTACH_NAME]=$ATTACHMENT_NAME_ARRAY[$I]; 
         $list[$I][ATTACH_ID]=$ATTACHMENT_ID_ARRAY[$I];
      }	   
	  $this->assign("list1",$list);
	  	   
	     
	   $this->assign("curtitle","项目详细信息");
	   
	   $this->display();	
	}	
	/*----------------项目审批-------------*/
	public function permit(){
          $dao=D("XmssLx");
          $dao->setField("SP","1","XM_ID = $_REQUEST[XM_ID]");
          
          $dao=D("xmsblb");
          $dao->setField("SJSJ","$_REQUEST[SJSJ]","XM_ID = $_REQUEST[XM_ID]");
          
          $this->success("通过审批");
         
/*
$q = "SELECT FJ_ID FROM xmss_lx WHERE XM_ID = $XM_ID";
$cursor=exequery($connection,$q);
$ROW=mysql_fetch_array($cursor);
$FJ_ID=$ROW["FJ_ID"];
$PATH_FROM = xm_getPath($FJ_ID,XMZT_LX);
$q = "SELECT FJ_ID,XMZT FROM xmsb_lb WHERE XM_ID = $XM_ID";
$cursor=exequery($connection,$q);
$ROW=mysql_fetch_array($cursor);
$FJ_ID=$ROW["FJ_ID"];
$XMZT=$ROW["XMZT"];
$PATH_TO = xm_getPath($FJ_ID,$XMZT);
xm_copy1($PATH_FROM,$PATH_TO."lx/");
*/		
		
	}
}
?>	