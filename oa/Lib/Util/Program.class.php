<?php
class Program {
	var $ZLPH="质量平衡文件";
	var $XMCG="项目成果文件";	
		
	public  function xm_getXMBH($XMSX){
					//$connection = OpenConnection();
					$today = getdate();
					$y = $today["year"];
					if($XMSX == 'S'){
						$preString = "WJYX-KJ-0".$y."-";
					}
					else{
						$preString = "WJYX-ZL-0".$y."-";
					}
					$dao=D("xmsslx");
					$ROW=$dao->where("XMBH LIKE '$preString%'")->field("XMBH")->order("XMBH DESC")->find();
					
					//$q = "SELECT XMBH FROM xmss_lx WHERE XMBH LIKE '$preString%' ORDER BY XMBH DESC LIMIT 1";
					//$cursor = exequery($connection,$q);
					if($ROW){
						$XMBH = $ROW["XMBH"];
						$n = intval(substr($XMBH,strlen($preString)))+1;
						if($n < 10)$n = "00".$n;
						else if($n < 100)$n = "0".$n;
						$XMBH = $preString.$n;	
					}
					else{
						$XMBH = $preString."001";
					}
			return $XMBH;
		 }
		
		 public  function xm_getPrcManagers($TYPE_ID,$BM_ID = 0){
					$arPrcUsers = array();
					$dao=D("xmsblc");
					$map="TYPE_ID = $TYPE_ID AND BM_ID = $BM_ID";
					if($BM_ID==-1)
					$map="TYPE_ID = $TYPE_ID";
					
					$list=$dao->where($map)
					          ->order("ORDERING")
					          ->field("USER_ID")
					          ->findall();
					//echo $dao->getlastsql();          
					if ($list){
						foreach ($list AS $ROW){
						  $arPrcUsers[] = $ROW["USER_ID"];	
						}
					}          
			return $arPrcUsers;
		 }
		 
		 public  function xm_getSHPrcs($arProUsers){
			 	//global $LOGIN_USER_ID;
			 	//ECHO $_SESSION[LOGIN_USER_ID];
			 	//ECHO $this->LOGIN_USER_ID;
			 	$arPRCs = array();
			 	$i = 0;
			 	foreach($arProUsers as $prouser){
			 		if($_SESSION[LOGIN_USER_ID] == $prouser){
			 			$arPRCs[] = $i;
			 		}
			 		$i++;
			 	}
			 	if(count($arPRCs) == 0)$arPRCs[] = "-1";
			 	return $arPRCs;
		 }
		
		 public function xm_getPrc($TYPECODE,&$TYPENAME,&$TYPEID){
				 	$dao = D('metadata');
				 	$ROW=$dao->where("name = 'XMLC' AND value1 = '$TYPECODE'")->field('id,value2')->find();
				 	$TYPENAME = "";
			 		$TYPEID = $ROW["id"];
			 		$TYPENAME = $ROW["value2"];
		 }
		 public  function xm_getPrcUsers($TYPEID,$BM_ID = 0){
		 	$dao=D("xmsblc");
		 	$map="A.TYPE_ID = $TYPEID AND A.BM_ID = $BM_ID";
		 	if($BM_ID==-1)
		 	$map="A.TYPE_ID = $TYPEID";
		 	 
		 	$ROW=$dao->table('xmsb_lc as A')
		 	         ->join('user as B ON A.USER_ID = B.USER_ID')
		 	         ->field('A.LC_ID,A.USER_ID,A.ORDERING,B.USER_NAME')
		 	         ->where($map)
		 	         ->order("A.ORDERING ASC")
		 	         ->findall();
		 	//echo $dao->getlastsql();         
		 	return $ROW;
		 }
		 
		 public  function xm_getPrcQuestions($TYPEID){
		 	$dao=D("xmsbwt");
		 	$ROW=$dao->where("TYPE_ID = $TYPEID")->findall();
		 	return $ROW;
		 }
		 protected function xm_getMBPath($TYPE){
		 	global $ATTACH_PATH;
		 	$connection = OpenConnection();
		 	$q = "SELECT PATH FROM templates WHERE LB = '$TYPE'";
		 	$cursor = exequery($connection,$q);
		 	if($ROW=mysql_fetch_array($cursor)){
		 		$PATH = $ROW["PATH"];
		 	}
		 	else{
		 		$PATH = "";
		 	}
		 	$PATH = $ATTACH_PATH.$PATH;
		 	return $PATH;
		 }
		 
		 protected function xm_getPath($FJ_ID,$XMZT){
		 	global $ATTACH_PATH;
		 	$connection = OpenConnection();
		 	if(!$XMZT)$XMZT = "0";
		 	$q = "SELECT value2 FROM metadata WHERE name = 'XMZT' AND value1 = '$XMZT'";
		 	$cursor = exequery($connection,$q);
		 	if($ROW=mysql_fetch_array($cursor)){
		 		$PATH = $ROW["value2"];
		 	}
		 	else{
		 		$PATH = "";
		 	}
		 	$PATH = $ATTACH_PATH.$PATH;
		 	//echo $ATTACH_PATH.$PATH;exit;
		 	return $PATH.$FJ_ID."/";
		 }
		 protected function xm_upload($ATTACHMENT,$ATTACHMENT_NAME,$PATH)
		{
		  if(!file_exists($ATTACHMENT))
		  {
		    Message("附件上传失败","原因：附件文件为空或文件名太长，或附件大于100兆字节，或文件路径不存在！");
		    Button_Back();
		    exit;
		  }
		
		  //-- 将上载文件转存 --
		  $ATTACHMENT_NAME=str_replace("\'","’",$ATTACHMENT_NAME);
		
		  if(!file_exists($PATH))mkdir($PATH, 0700);
		
		  $FILENAME=$PATH.$ATTACHMENT_NAME;
		
		  copy($ATTACHMENT,$FILENAME);
		  unlink($ATTACHMENT);
		
		  if(!file_exists($FILENAME))
		  {
		    Message("附件上传失败","原因：附件文件为空或文件名太长，或附件大于30兆字节，或文件路径不存在！");
		    Button_Back();
		    exit;
		  }
		}
		
		protected function xm_delete_attach($FILENAME,$PATH){
			unlink($PATH.$FILENAME);
			if(file_exists($PATH)){
				if ($handle = opendir($PATH)) {
					$bDel = true;
				   while (false !== ($file = readdir($handle))) {
				   		if ($file != "." && $file != "..") {
				           $bDel = false;
				       }
				   }
				   closedir($handle);
				   if($bDel){
				   	rmdir($PATH);
				   }
				}
			}
		}
		
		protected function xm_delete_attach_all($FJ_ID,$XMZT)
		{
			$PATH = xm_getPath($FJ_ID,$XMZT);
			if(file_exists($PATH)){
				if ($handle = opendir($PATH)) {
				   while (false !== ($file = readdir($handle))) {
				       if ($file != "." && $file != "..") {
				           unlink($PATH.$file);	
				       }
				   }
				   closedir($handle);
				}
				rmdir($PATH);
			}
		}
		
		protected function xm_copy($FJ_ID,$XMZT_FROM,$XMZT_TO){
			
			$PATH_FROM = xm_getPath($FJ_ID,$XMZT_FROM);
			$PATH_TO = xm_getPath($FJ_ID,$XMZT_TO);
			
			if($PATH_TO == $PATH_FROM)return;
			if(file_exists($PATH_FROM)){
				if(!file_exists($PATH_TO))mkdir($PATH_TO, 0700);
				if ($handle = opendir($PATH_FROM)) {
				   while (false !== ($file = readdir($handle))) {
				       if ($file != "." && $file != "..") {
				           copy($PATH_FROM.$file,$PATH_TO.$file);	
				       }
				   }
				   closedir($handle);
				}
			}
			xm_delete_attach_all($FJ_ID,$XMZT_FROM);
		}
		
		protected function xm_copy1($PATH_FROM,$PATH_TO){
			if($PATH_TO == $PATH_FROM)return;
			if(file_exists($PATH_FROM)){
				if(!file_exists($PATH_TO))mkdir($PATH_TO, 0700);
				if ($handle = opendir($PATH_FROM)) {
				   while (false !== ($file = readdir($handle))) {
				       if ($file != "." && $file != "..") {
				           copy($PATH_FROM.$file,$PATH_TO.$file);	
				       }
				   }
				   closedir($handle);
				}
				if ($handle = opendir($PATH_FROM)) {
				   while (false !== ($file = readdir($handle))) {
				       if ($file != "." && $file != "..") {
				           unlink($PATH_FROM.$file);	
				       }
				   }
				   closedir($handle);
				}
				rmdir($PATH_FROM);
			}
		}
		
		public function xm_getWhUsers($WH_ID){
			$dao=D("xmsslc");
			$list=$dao->table('xmss_lc as A')
			          ->join('user as B on A.USER_ID=B.USER_ID')
			          ->field('A.LC_ID,A.USER_ID,A.ORDERING,B.USER_NAME')
			          ->where("A.WH_ID = $WH_ID")
			          ->order('A.ORDERING ASC')
			          ->findall();
		 	return $list;
		}
	
	
	
}



?>