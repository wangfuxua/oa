<?php
//import("@.Action.PublicAction");
include_cache(APP_PATH."/Common/bgsb.php");
class BgsbAction extends PublicAction {
	var $curtitle;
	
	function _initialize(){
		$this->curtitle="办公设备管理";
		$this->assign("curtitle",$this->curtitle);
		
		parent::_initialize();
	}
    /*-----------办公设备字处理---------*/
	public function manage(){
		$typeid=$_REQUEST[typeid];
		
		$dao=D("Bgsb");
		if ($typeid>0) {
			$daotype=D("BgsbType");
			$rowtype=$daotype->where("typeid='$typeid'")->find();
		    $map=array("typeid"=>$typeid);	
		}else {
		        $map=array();
		        $rowtype[typename]="全部分类";
		}
        $count=$dao->count($map);
        if ($count>0) {
        	import("ORG.Util.Page");
        	$p= new Page($count);	

			$list=$dao->where($map)
			          ->order("BGSB_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			$page       = $p->show();
			$this->assign("page",$page);
			/*
			echo $dao->getlastsql();
			echo "<pre>";
			print_r($list);
			echo "</pre>";
			*/
			$this->assign('list',$list);			             	
        }
		if (!$typeid)  $typeid=-1;
        $this->assign("rowtype",$rowtype);
        $this->assign("typeid",$typeid);
        $this->display();
	}
    /*--------------添加或修改办公设备----------*/ 	
	public function form(){
		$BGSB_ID=$_REQUEST[BGSB_ID];
		//基本信息
		if ($BGSB_ID) {
			$dao=D("Bgsb");
			$row=$dao->where("BGSB_ID='$BGSB_ID'")->find();
		}else {
			$row=array();
		}
		//设备类别
		$dao=D("BgsbType");
		$typelist=$dao->order("listorder desc")->findall();
		$this->assign("typelist",$typelist);
		
		//使用部门
		if(isset($row[SYBM_ID])){
				$CHOOSE_ID = $row[SYBM_ID];
		}else{
				$CHOOSE_ID = $this->LOGIN_DEPT_ID;
		}
		$my_dept_tree=my_dept_tree(0,$CHOOSE_ID,0);
		$this->assign("my_dept_tree",$my_dept_tree);
        //管理人 
		if(isset($row[GLR_ID])){
				$CHOOSE_ID = $row[GLR_ID];
		}else{
				$CHOOSE_ID = $this->LOGIN_USER_ID;
		}
		$my_user_list=my_user_list("USER_NAME","USER_ID",$CHOOSE_ID);
		$this->assign("my_user_list",$my_user_list);
		
        
		$this->assign("LOGIN_USER_NAME",Cookie::get("USER_NAME_COOKIE"));
        $this->assign("a",$_REQUEST[a]);
		$this->assign("row",$row);
		$this->display();
	}
    /*-------------------添加或修改办公设备-----------*/
	public function formsubmit(){
		$BGSB_ID=$_REQUEST[BGSB_ID];
		$_POST[ZCLRY_ID]=$this->LOGIN_USER_ID;
		$_POST[ZCLRY_NAME]=getUsername($this->LOGIN_USER_ID);
		
		$dao=D("Bgsb");
		if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
		if ($BGSB_ID) {//修改
			$dao->where("BGSB_ID='$BGSB_ID'")->save();
						
            $this->assign("jumpUrl",__URL__."/manage");
            $this->success('成功修改');						
		}else{//添加
			
			$id=$dao->add();
			
			
            $this->assign("jumpUrl",__URL__."/manage");
            $this->success('成功添加');			
		}
	}
	/*-------------------删除-------------*/
	public function delete(){
		$BGSB_ID=$_REQUEST[BGSB_ID];
		$typeid=$_REQUEST[typeid];

		$dao=D("Bgsb");
		$dao->where("BGSB_ID='$BGSB_ID'")->delete();

		if (isset($_REQUEST[SH])) {
		$this->assign("jumpUrl",__URL__."/audit/SH/$_REQUEST[SH]");			
		}else if($typeid!=-1){
		$this->assign("jumpUrl",__URL__."/manage/typeid/$typeid");		
		}else {
		$this->assign("jumpUrl",__URL__."/manage");		
		}	
        //$this->assign("jumpUrl",__URL__."/manage");	
		$this->success('成功删除');
		
	}
	/*-------------------详细信息-------------*/
	public function detail(){
		$BGSB_ID=$_REQUEST[BGSB_ID];
		$dao=D("Bgsb");
		$row=$dao->where("BGSB_ID='$BGSB_ID'")->find();
		$this->assign("row",$row);
		$this->display();
		
	}
	
	/*-------------------办公设备查询表单-----------*/
	public function search(){
		$dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();//登录用户信息
        $this->assign("user",$user);

		$dao=D("BgsbType");
		$typelist=$dao->order("listorder desc")->findall();
		$this->assign("typelist",$typelist);

		$my_dept_tree=my_dept_tree(0,$this->LOGIN_DEPT_ID,0);
		$this->assign("my_dept_tree",$my_dept_tree);

		$my_user_list=my_user_list("USER_NAME","USER_ID",'');
		$this->assign("my_user_list",$my_user_list);
		
		$this->display();
	}
	/*-------------------办公设备查询结果-----------*/
	public function searchlist(){
			if ($_GET[p] == ''){
				$map=$this->_search();
			}else{
				$map = urldecode(str_replace('!', '%', $_GET['map']));
			}

	        $dao=D("Bgsb");
			$count=$dao->count($map);

			if ($count==0){
			}
			else {
				import("ORG.Util.Page");
				
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				
				$p= new Page($count,$listRows);

				$p->parameter='map='.str_replace('%', '!', urlencode($map));

				$list=$dao->where($map)
						  ->limit($p->firstRow.','.$p->listRows)
						  ->findall();

				$page       = $p->show();
				$this->assign("page",$page);
				$this->assign('list',$list);
	        }
			$this->display();
	}
	//
	protected function _search() 
    {
    	/*
		$model	=	D("Bgsb");
        foreach($model->getDbFields() as $key=>$val) {
            if(isset($_REQUEST[$val]) && $_REQUEST[$val]!='') {
            	if($val=="SYBM_ID"&&$_REQUEST[$val]=='all')
                     continue;
            	if($val=="GLR_ID"&&$_REQUEST[$val]=='all')
                     continue;
                if($val=="GLR_ID"||$val=="SYBM_ID")
                    $map[$val]	=	$_REQUEST[$val];
                else
                    $map[$val]	=	array('like','%'.$_REQUEST[$val].'%');
            }
        }
        */
		 if($_REQUEST[typeid]!="")
		    $condition[]=" typeid = '$_REQUEST[typeid]'";
		 if($_REQUEST[MC]!="")
		    $condition[]=" MC like '%".$_REQUEST[MC]."%'";
		 if($_REQUEST[BH]!="")
		    $condition[]=" BH like '%".$_REQUEST[BH]."%'";
		 if($_REQUEST[SZD]!="")
		    $condition[]=" SZD like '%".$_REQUEST[SZD]."%'";
		 if($_REQUEST[GLR_ID]!="all")
		    $condition[]=" GLR_ID = '$_REQUEST[GLR_ID]'";
		 if($_REQUEST[SYBM_ID]!="all")
		    $condition[]=" SYBM_ID=$_REQUEST[SYBM_ID]";
		 if($_REQUEST[KSSYRQ]!="")
		    $condition[]=" KSSYRQ like '%".$_REQUEST[KSSYRQ]."%'";
		    
        if (is_array($condition))
                $conditions = implode($_REQUEST[RELATION], $condition);

		 return $conditions;
        
        
        
        //return $map;
    }
    /*--------------------办公设备审核------包括三种情况------------ */	
	public function audit(){
		if (!isset($_REQUEST[SH]))
		    $_REQUEST[SH]=2;
		    
		$SH=intval($_REQUEST[SH]);
		$dao=D("Bgsb");
		
        $map=array("SH"=>$SH);
        
        $count=$dao->count($map);
        if ($count>0) {
        	import("ORG.Util.Page");
        	$p= new Page($count);	
			$list=$dao->where($map)
			          ->order("BGSB_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);			             	
        }
        $this->assign("SH",$SH);
        $this->display();
        
	}
	/*-----------资产审核提交程序 ------------*/
	public function auditto(){
		$AUDIT=intval($_REQUEST[AUDIT]);
		$BGSB_ID=$_REQUEST[BGSB_ID];
		
		
		$dao=D("Bgsb");
		$dao->setField("SH",$AUDIT,"BGSB_ID='$BGSB_ID'");
		

		$this->assign("jumpUrl",__URL__."/audit/SH/$AUDIT");
		$this->success("成功操作");
		
		
	}
	
	/*-------------------导入分组---------------*/
	public function bgsbimport(){
		$typeid=$_REQUEST[typeid];
        $this->menu();
		//设备类别
		$dao=D("BgsbType");
		$typelist=$dao->order("listorder desc")->findall();
		$this->assign("typelist",$typelist);
		        
        $this->assign("typeid",$typeid);
		$this->display();
	}
	
	/*---------------导入处理程序------------*/
	public function bgsbimportsubmit(){
		$typeid=$_REQUEST[typeid];
		$FILE_NAME=$_FILES[CSV_FILE][name];//或者$FILE_NAME=$_POST[FILE_NAME];
		$CSV_FILE=$_FILES[CSV_FILE][tmp_name];
		if(strtolower(substr($FILE_NAME,-3))!="csv"){
			$this->error("只能导入CSV文件");
		}
		//$ID_STR="PSN_NAME,SEX,NICK_NAME,BIRTHDAY,MINISTRATION,MATE,CHILD,DEPT_NAME,ADD_DEPT,POST_NO_DEPT,TEL_NO_DEPT,FAX_NO_DEPT,ADD_HOME,POST_NO_HOME,TEL_NO_HOME,MOBIL_NO,BP_NO,EMAIL,OICQ_NO,ICQ_NO,NOTES";
		$ROW_COUNT = 0;
		$handle = fopen ($CSV_FILE,"r");
		$line = fgets($handle);
		$TITLE=explode(",",$line);
		//print_r($TITLE);
		if(!$handle || !$TITLE){
			$this->error("打开文件错误");
		}
		$dao=D("Bgsb");
		while (!feof($handle)){
			$ADDRESS=array();
			$line = fgets($handle);
			if ($line){
			$DATA=explode(",",$line);
			//print_r($DATA);
		    $ROW_COUNT++;
		    foreach ($DATA as $key=>$value){
		       // $ADDRESS[USER_ID]=$this->LOGIN_USER_ID;
		        $ADDRESS[typeid]=$typeid;
		        if ($this->phpdigVerifyUTF8($TITLE[$key])) {
		        	$TITLE_KEY=$TITLE[$key];
		        }else {
		        	//echo "000";
		           $TITLE_KEY=iconv("GB2312","UTF-8",$TITLE[$key]);
		           $value=iconv("GB2312","UTF-8",$value);
		        }
		        //echo $TITLE_KEY."<br>";
		        if ($TITLE_KEY=="tongji") {
		        	//echo "ddd";
		        }
		    	$ID=match($TITLE_KEY);
		    	//echo $ID."<br>";
		    	
		        $ADDRESS[$ID]=$value;
		           if($ID=="ZCLRY_NAME"){
		           	 // echo "ccc";
		              $ADDRESS[ZCLRY_ID]=getUserid($value);   	  
		           }
		           if($ID=="GLR_NAME"){
		              $ADDRESS[GLR_ID]=getUserid($value);   	  
		           }
		           if($ID=="SYBM_NAME"){
		              $ADDRESS[SYBM_ID]=getDeptid($value);
		           }
		           $ADDRESS[SH]=2;		           
		    }
		   // print_r($ADDRESS);
		    //EXIT;
		    $dao->create();
		    $dao->add($ADDRESS);
		   }
		}
		fclose ($handle);
		if(file_exists($CSV_FILE))
		   unlink($CSV_FILE);
		$this->success("共".$ROW_COUNT."条数据导入!");
	}
	
	protected function _format($STR){
		   $STR=str_replace("\"","'",$STR);
		   $STR=iconv("UTF-8","GB2312",$STR);
		   if(strpos($STR,","))
		      $STR="\"".$STR."\"";
		   return $STR;
	}

	/*-------------是不是utf8---------*/
	protected 	function phpdigVerifyUTF8($str) {
	  // verify if a given string is encoded in valid utf-8
	  if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
	    return true;
	  }
	  else {
	    return false;
	  }
	}
		
	    	
}
?>
	