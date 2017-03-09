<?php
//import("@.Action.PublicAction");
include_cache(APP_PATH."/Common/gdzc.php");

class DzyhAction extends PublicAction {
	var $curtitle;
	
	function _initialize(){
		$this->curtitle="低值易耗品管理";
		$this->assign("curtitle",$this->curtitle);
		
		parent::_initialize();
	}
    /*-----------低值易耗品字处理---------*/
	public function manage(){
		$dao=D("Dzyh");
        $map=array();
        $count=$dao->count($map);
        if ($count>0) {
        	import("ORG.Util.Page");
        	$p= new Page($count);	
			$list=$dao->where($map)
			          ->order("DZYH_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			          
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);			             	
        }
        $this->display();
	}
    /*--------------添加或修改低值易耗品----------*/ 	
	public function form(){
		$DZYH_ID=$_REQUEST[DZYH_ID];
		//基本信息
		if ($DZYH_ID) {
			$dao=D("Dzyh");
			$row=$dao->where("DZYH_ID='$DZYH_ID'")->find();
		}else {
			$row=array();
		}
		
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
    /*-------------------添加或修改低值易耗品-----------*/
	public function formsubmit(){
		$DZYH_ID=$_REQUEST[DZYH_ID];
		$_POST[ZCLRY_ID]=$this->LOGIN_USER_ID;
		$dao=D("Dzyh");
		if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
		if ($DZYH_ID) {//修改
			$dao->where("DZYH_ID='$DZYH_ID'")->save();
						
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
		$DZYH_ID=$_REQUEST[DZYH_ID];
		$dao=D("Dzyh");
		$dao->where("DZYH_ID='$DZYH_ID'")->delete();
		if (isset($_REQUEST[SH])) {
		$this->assign("jumpUrl",__URL__."/audit/SH/$_REQUEST[SH]");			
		}else {
		$this->assign("jumpUrl",__URL__."/manage");		
		}
				
		//$this->assign("jumpUrl",__URL__."/manage");
		$this->success('成功删除');
	}
	/*-------------------详细信息-------------*/		
	public function detail(){
		$DZYH_ID=$_REQUEST[DZYH_ID];
		$dao=D("Dzyh");
		$row=$dao->where("DZYH_ID='$DZYH_ID'")->find();
		$this->assign("row",$row);
		$this->display();
	}
	/*-------------------低值易耗品查询表单-----------*/
	public function search(){
		$dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();//登录用户信息
        $this->assign("user",$user);
        
		$my_dept_tree=my_dept_tree(0,$this->LOGIN_DEPT_ID,0);
		$this->assign("my_dept_tree",$my_dept_tree);

		$my_user_list=my_user_list("USER_NAME","USER_ID",'');
		$this->assign("my_user_list",$my_user_list);
		
		$this->display();
	}
	/*-------------------低值易耗品查询结果-----------*/
	public function searchlist(){
	        $map=$this->_search();
	        $dao=D("Dzyh");
	        if(!empty($dao)) {
	        	$list=$dao->findall($map,"*");
	        	$this->assign("list",$list);
	        }
	        $this->display();
	}
	//
	protected function _search($audit=0) {
		/*
		$model	=	D("Dzyh");
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
        return $map;
        */
		if ($audit) {
			$condition[]=" SH = '2'";//只显示审核通过的
		}
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
		 		
    }
    /*--------------------低值易耗品审核------包括三种情况------------ */	
	public function audit(){
		if (!isset($_REQUEST[SH]))
		    $_REQUEST[SH]=2;
		    
		$SH=intval($_REQUEST[SH]);
		$dao=D("Dzyh");
		
        $map=array("SH"=>$SH);
        
        $count=$dao->count($map);
        if ($count>0) {
        	import("ORG.Util.Page");
        	$p= new Page($count);	
			$list=$dao->where($map)
			          ->order("DZYH_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);			             	
        }
        $this->assign("SH",$SH);
        $this->display();
	}
	/*-----------审核提交程序 ------------*/
	public function auditto(){
		$AUDIT=intval($_REQUEST[AUDIT]);
		$DZYH_ID=$_REQUEST[DZYH_ID];

		$dao=D("Dzyh");
		$dao->setField("SH",$AUDIT,"DZYH_ID='$DZYH_ID'");
		

		$this->assign("jumpUrl",__URL__."/audit/SH/$AUDIT");
		$this->success("成功操作");
	}
	
	
	/*---------低值易耗品领用--查询表单-------*/
	public function request(){
		$dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();//登录用户信息
        $this->assign("user",$user);
        
		$my_dept_tree=my_dept_tree(0,$this->LOGIN_DEPT_ID,0);
		$this->assign("my_dept_tree",$my_dept_tree);

		$my_user_list=my_user_list("USER_NAME","USER_ID",'');
		$this->assign("my_user_list",$my_user_list);
		
		$this->display();
	}	
	/*-------------------低值易耗品领用查询结果-----------*/
	public function requestlist(){
		//print_r($_REQUEST);
		//EXIT;
	        $map=$this->_search(2);
	        $dao=D("Dzyh");
	        if(!empty($dao)) {
	        	$list=$dao->findall($map,"*");
	        	//echo $dao->getlastsql();
	        	$this->assign("list",$list);
	        	//echo  $dao->getlastsql();
	        	//exit;
	        }
	        $this->display();
	}
    /*-----------------低值易耗品领用时显示资产详细信息----------------*/
	public function ly(){
		$DZYH_ID=$_REQUEST[DZYH_ID];
		$SYSL=$_REQUEST[SYSL];
		$dao=D("Dzyh");
		$row=$dao->where("DZYH_ID='$DZYH_ID'")->find();
		$this->assign("row",$row);
		$this->assign("SYSL",$SYSL);
		$this->assign("_REQUEST",$_REQUEST);
		
		$this->display();
	}
	/*-----------------低值易耗品领用申请----------------*/
	public function sq(){
		$DZYH_ID=$_REQUEST[DZYH_ID];
		$SYSL=$_REQUEST[SYSL];
		$LYSL=$_REQUEST[LYSL];
        if (empty($DZYH_ID)) {
        	$this->assign("jumpUrl",__URL__."/ly/DZYH_ID/$DZYH_ID/SYSL/$SYSL");
        	$this->error("请选择要领用的低值易耗品");
        }
        
		if(!is_number($LYSL)){
        	$this->assign("jumpUrl",__URL__."/ly/DZYH_ID/$DZYH_ID/SYSL/$SYSL");
			$this->error("领用数量错误");
		}
		if($LYSL > $SYSL){
        	$this->assign("jumpUrl",__URL__."/ly/DZYH_ID/$DZYH_ID/SYSL/$SYSL");
			$this->error("领用数量不能大于剩余数量");			
		}
		
		$dao=D("Dzyh");
		$row=$dao->where("DZYH_ID='$DZYH_ID'")->find();
		if ($row[SH]!=2) {
		   $this->error("低值易耗品未通过审核，不能领用");			
	    }
	    
	    $dao=D("Dzyhply");
	    $data[DZYH_ID]=$DZYH_ID;
		$data[USER_ID]=$this->LOGIN_USER_ID;
		$data[LYSL]=$LYSL;
		$data[LYSJ]=date("Y-m-d",$this->CUR_TIME_INT);
		$dao->create();
		$dao->add($data);
		$this->assign("jumpUrl",__URL__."/check");
		$this->success("成功操作");
	}
	
	/*-----------------低值易耗品领用审核----------------*/
	public function check(){
		if (!isset($_REQUEST[SH]))
		    $_REQUEST[SH]=0;
		    
		$SH=intval($_REQUEST[SH]);
		$dao=D("Dzyhply");
		
        $map=array("SH"=>$SH);
        $maps=array("A.SH"=>$SH);
        $count=$dao->count($map);
        
        if ($count>0) {
        	import("ORG.Util.Page");
        	$p= new Page($count);	
			$list=$dao->table("dzyhply A")
			          ->join("USER B ON A.USER_ID = B.USER_ID LEFT JOIN DZYH C ON A.DZYH_ID = C.DZYH_ID")
			          ->field("A.LY_ID,A.DZYH_ID as DZYH_ID,A.SH,A.LYSL,B.USER_NAME as LYR_NAME,C.MC,C.SL,A.LYSJ")  
			          ->where($maps)
			          ->order("A.DZYH_ID DESC")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			          //echo $dao->getlastsql();
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);			             	
        }
        $this->assign("count",$count);
		$this->assign("SH",$SH);
		$this->display();
	}
	/*---------------------领用审核------------*/
	public function checkaudit(){
		$AUDIT=$_REQUEST[AUDIT];
		$LY_ID=$_REQUEST[LY_ID];
		$dao=D("Dzyhply");
		$dao->setField("SH",$AUDIT,"LY_ID='$LY_ID'");
		$dao->setField("SHR_ID",$this->LOGIN_USER_ID,"LY_ID='$LY_ID'");
		
		$this->assign("jumpUrl",__URL__."/check/SH/$AUDIT");
		$this->success("成功操作");
		
	}
	
	/*--------------------删除领用--------------*/
	public function deletedzyhly(){
		$LY_ID=$_REQUEST[LY_ID];
		$dao=D("Dzyhply");
		$dao->where("LY_ID='$LY_ID'")->delete();
		$this->success("成功删除");
	}
	
	/*---------------------领用查询表单------------*/
	public function lysearch(){
       
		$my_dept_tree=my_dept_tree(0,0,0);
		$this->assign("my_dept_tree",$my_dept_tree);

		$my_user_list=my_user_list("USER_NAME","USER_ID",'');
		$this->assign("my_user_list",$my_user_list);
				
		$this->display();
	}
    /*---------------------领用查询结果------------*/
	public function lysearchlist(){
    		
		 if($_REQUEST[MC]!="")
		    $condition[]=" B.MC like '%".$_REQUEST[MC]."%'";
		 if($_REQUEST[SYBM_ID]!="all")
		    $condition[]=" B.SYBM_ID=$_REQUEST[SYBM_ID]";
		    		    
		 if($_REQUEST[USER_ID]!="all")
		    $condition[]=" A.USER_ID = '$_REQUEST[USER_ID]'";

		 if($_REQUEST[SJ1]!=""&&$_REQUEST[SJ2]!="")
		    $condition[]=" A.LYSJ between '$_REQUEST[SJ1]' AND '$_REQUEST[SJ2]'";
		 elseif($_REQUEST[SJ1]!="")
		    $condition[]=" A.LYSJ >= '$_REQUEST[SJ1]'";
		 elseif($_REQUEST[SJ2]!="")
		    $condition[]=" A.LYSJ <= '$_REQUEST[SJ2]'";		    
		    
        if (is_array($condition))
                $conditions = implode($_REQUEST[RELATION], $condition);

       $dao= new Model();
       $row=$dao->table("dzyhply A")
                ->join("dzyh B ON A.DZYH_ID = B.DZYH_ID")       
                ->field("count(*) AS count")                
                ->where($conditions)
                ->find();
       $count=$row[count];
       //$query="SELECT count(*) from dzyhply A LEFT JOIN dzyh B ON A.DZYH_ID = B.DZYH_ID ".$WHERE_STR;
        if ($count>0) {
        	import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '20';
				}
				        	
        	$p= new Page($count,$listRows);	
			$list=$dao->table("dzyhply A")
			          ->join("dzyh B ON A.DZYH_ID = B.DZYH_ID")
			          ->field("B.MC,B.BH,B.DJ,A.USER_ID,A.LYSL,A.SH,A.LYSJ")  
			          ->where($conditions)
			          ->order("A.LY_ID DESC")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			          //echo $dao->getlastsql();
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);		             	
        }
                         
       
       
       $this->display();
		
	}
    
}
?>
	