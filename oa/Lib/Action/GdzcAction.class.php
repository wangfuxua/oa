<?php
//import("@.Action.PublicAction");
include_cache(APP_PATH."/Common/gdzc.php");

class GdzcAction extends PublicAction {
	var $curtitle;
	
	function _initialize(){
		$this->curtitle="固定资产管理";
		$this->assign("curtitle",$this->curtitle);
		
		parent::_initialize();
	}
    /*-----------固定资产字处理---------*/
	public function manage(){
		$dao=D("Gdzc");
        $map=array();
        $count=$dao->count($map);
        if ($count>0) {
        	import("ORG.Util.Page");
        	$p= new Page($count);	
			$list=$dao->where($map)
			          ->order("GDZC_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			          
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);			             	
        }
        $this->display();
	}
    /*--------------添加或修改固定资产----------*/ 	
	public function form(){
		$GDZC_ID=$_REQUEST[GDZC_ID];
		//基本信息
		if ($GDZC_ID) {
			$dao=D("Gdzc");
			$row=$dao->where("GDZC_ID='$GDZC_ID'")->find();
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
    /*-------------------添加或修改固定资产-----------*/
	public function formsubmit(){
		$GDZC_ID=$_REQUEST[GDZC_ID];
		$_POST[ZCLRY_ID]=$this->LOGIN_USER_ID;
		$dao=D("Gdzc");
		if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
		if ($GDZC_ID) {//修改
			$dao->where("GDZC_ID='$GDZC_ID'")->save();
						
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
		$GDZC_ID=$_REQUEST[GDZC_ID];
		$SH=$_REQUEST[SH];
		$dao=D("Gdzc");
		$dao->where("GDZC_ID='$GDZC_ID'")->delete();
		
		if (isset($_REQUEST[SH])) {
		$this->assign("jumpUrl",__URL__."/audit/SH/$_REQUEST[SH]");			
		}else {
		$this->assign("jumpUrl",__URL__."/manage");		
		}
		$this->success('成功删除');
		
	}
	/*-------------------详细信息-------------*/	
	public function detail(){
		$GDZC_ID=$_REQUEST[GDZC_ID];
		$dao=D("Gdzc");
		$row=$dao->where("GDZC_ID='$GDZC_ID'")->find();
		$this->assign("row",$row);
		$this->display();
		
	}
	
	/*-------------------固定资产查询表单-----------*/
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
	/*-------------------固定资产查询结果-----------*/
	public function searchlist(){
			if ($_GET[p] == ''){
				$map=$this->_search();
			}else{
				$map = urldecode(str_replace('!', '%', $_GET['map']));
			}

	        $dao=D("Gdzc");
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
	protected function _search(){
		/*
		$model	=	D("Gdzc");
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
    /*--------------------固定资产审核------包括三种情况------------ */	
	public function audit(){
		if (!isset($_REQUEST[SH]))
		    $_REQUEST[SH]=2;
		    
		$SH=intval($_REQUEST[SH]);
		$dao=D("Gdzc");
		
        $map=array("SH"=>$SH);
        
        $count=$dao->count($map);
        if ($count>0) {
        	import("ORG.Util.Page");
        	$p= new Page($count);	
			$list=$dao->where($map)
			          ->order("GDZC_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);			             	
        }
        //echo $dao->getlastsql();
        $this->assign("SH",$SH);
        $this->display();
                
	}
	/*-----------资产审核提交程序 ------------*/
	public function auditto(){
		$AUDIT=intval($_REQUEST[AUDIT]);
		$GDZC_ID=$_REQUEST[GDZC_ID];
		
		$dao1=new Model();
		$row=$dao1->table("gdzc")->where("GDZC_ID='$GDZC_ID'")->find();
		
		$dao=D("Gdzc");
		$dao->setField("SH",$AUDIT,"GDZC_ID='$GDZC_ID'");
		

		$this->assign("jumpUrl",__URL__."/audit/SH/$AUDIT");
		$this->success("成功操作");
		
		
	}
	
	    	
}
?>
	