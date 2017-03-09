<?php

class LeftAction extends PublicAction {
	function _initialize(){
		$this->curtitle="";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
    /*---------------快捷组列表---------------*/
	public function quick(){
		###菜单快捷组
		$dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$dao=D("SysFunction");
		$array=explode(",",$user[QUICK]);
		$list1=$list2=array();
		foreach ($array as $key=>$code){
			if ($code) {
			   $map="FUNC_ID = '$code' AND flag=1";	
			   $row=$dao->where($map)->find();
			   $list1[$key]=$row;				
			}
	    }
	    //print_r($list1);
		$this->assign("list1",$list1);
		
		###Windows快捷组
		$dao=D("Winexe");
		$list2=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->order("WIN_NO")->findall();
		$this->assign("list2",$list2);
		
		
		$this->display();
	}
	
	/*---------------设置菜单快捷组---------------*/
	public function setquick(){
		###已经定义的快捷组
		$dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$QUICK=$user[QUICK];
        //echo $QUICK;
        $dao=D("UserPriv");
        $priv=$dao->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();		
	    $USER_FUNC_ID_STR=$priv["FUNC_ID_STR"];	
	    
        ###已选菜单 
        $QUICK_ARRAY=explode(",",$QUICK);
        foreach ($QUICK_ARRAY as $key=>$FUNC_ID){
           	if ($FUNC_ID&&find_id($USER_FUNC_ID_STR,$FUNC_ID)) {
           	   $dao=D("SysFunction");	
			   $map="FUNC_ID = '$FUNC_ID' AND flag=1";	
			   $row=$dao->where($map)->find();
			   $list1[$key]=$row;
           	}
        }
        $this->assign("list1",$list1);
        /*
        ###备选菜单//只能是最后一级菜单
        $dao=D("SysFunction");
        $list=$dao->where("flag=1")->order("MENU_ID")->findall();
        foreach ($list as $key=>$row){
          $MENU_ID=$row["MENU_ID"];
          $LEN=strlen($MENU_ID);        	 
          
          $map=" flag=1 and MENU_ID like '$MENU_ID%' and length(MENU_ID)>$LEN";
          $rows=$dao->where($map)->find();
          if($rows)
            continue;
            
          $list2[$key]=$row;	
        }
       $this->assign("list2",$list2);
       */
       /*-------*/
  	    ###备选菜单

             $USER_FUNC_ID_STR=$priv["FUNC_ID_STR"];
		 if($this->LOGIN_USER_ID=="admin")
		     $USER_FUNC_ID_STR.="32,33,56,";

   	    $daomenu=D("SysMenu");
        $daofunc=D("SysFunction");
		$list=$daomenu->where("flag=1")->order("MENU_ID")->findall();
		$i=0;
		foreach ($list as $row){
			$listfunc=$daofunc->where("MENU_ID like '$row[MENU_ID]%' and length(MENU_ID)=4 and flag=1 and FUNC_ID in (".$USER_FUNC_ID_STR."0)")->order("MENU_ID")->findall();
			     $k=0; 
			foreach ($listfunc as $keys=>$rows){
				$listfunc[$keys][pid]=intval($row[MENU_ID]+9999);
				$listfunc[$keys][id]=intval($rows[FUNC_ID]);
				
				if(substr($rows[FUNC_CODE],0,1)=="@"){//是不是子菜单
					$listfunc2=$daofunc->where("MENU_ID like '$rows[MENU_ID]%' and length(MENU_ID)=6 and flag=1 and FUNC_ID in (".$USER_FUNC_ID_STR."0)")->order("MENU_ID")->findall();
					  if ($listfunc2) {
					  	 foreach ($listfunc2 as $keyss=>$rowss){
					  	 	$listfunc2[$keyss][pid]=intval($rows[FUNC_ID]);
					  	 	$listfunc2[$keyss][id]=intval($rowss[FUNC_ID]);
					  	 }
					  }
					$listfunc[$k][sub2]=$listfunc2;
				}
				$k++;
			}
			$list[$i][pid]=0;//
			$list[$i][id]=intval($row[MENU_ID]+9999);//
			if (!empty($listfunc)) {
                 $list[$i][subcount]=1;					
			}		
			$list[$i][sub]=$listfunc;
			$i++;
		}
		$this->assign("list",$list);
		       
		
       
       /*------*/
       
	   $this->assign("QUICK",$QUICK);
	   $this->assign("USER_FUNC_ID_STR",$USER_FUNC_ID_STR);
	   $this->display();
		
	}
	
	/*---------------菜单快捷组提交程序---------------*/
	public function setquicksubmit(){
		$FLD_STR=$_REQUEST[FLD_STR];
		$dao=D("User");
		$dao->setField("QUICK",$FLD_STR,"USER_ID='$this->LOGIN_USER_ID'");
		
		//ECHO $dao->getlastsql();
		$this->assign("jumpUrl",__URL__."/setquick");
		$this->success("成功提交");
		
	}
	
	/*----------------window 菜单列表 ------------*/
    public function winexeIndex(){
		$dao=D("Winexe");
		$map=array("USER_ID"=>"$this->LOGIN_USER_ID");
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
                $list=$dao->where($map)
                          ->order("WIN_NO")
                          ->limit("$p->firstRow,$p->listRows")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        $this->assign("COUNT",$count);
	        
		    $this->display();    	
    	
    	
    }
    /*----------------window 菜单FORM ------------*/
	public function winexeForm(){
		$WIN_ID=intval($_REQUEST['WIN_ID']);
        if ($WIN_ID) {//修改
        	$dao=D("Winexe");
           	$row=$dao->where("WIN_ID='$WIN_ID'")->find();
        }else {
        	$row=array();
        }
        $this->assign("row",$row);
		$this->display();
	}
    /*----------------window 菜单FORM提交 ------------*/	    
	public function winexeSubmit(){
		$WIN_ID=intval($_REQUEST['WIN_ID']);
		
		$dao=D("Winexe");	
        if ($WIN_ID) {//修改
        	
	        if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        }
	        if($dao->where("WIN_ID='$WIN_ID'")->save()){
			   $this->assign("jumpUrl",__URL__."/winexeIndex");
			   $this->success("成功修改");
			}else{ 
			   $this->assign("jumpUrl",__URL__."/winexeIndex");	
			   $this->error("修改失败");   
			}
        }else {//添加
	        $_POST[USER_ID]=$this->LOGIN_USER_ID;
	        if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        }
            $id = $dao->add();
            
	        if($id){
			   $this->assign("jumpUrl",__URL__."/winexeIndex");
			   $this->success("成功添加");
			}else{ 
			   $this->assign("jumpUrl",__URL__."/winexeIndex");	
			   $this->error("添加失败");   
			}
		                	
        }  				
		
        
		
	}
	/*----------------window 菜单 删除单个记录 ------------*/
	public function winexeDelete(){//
		$WIN_ID=intval($_REQUEST['WIN_ID']);
		$dao=D("Winexe");
		$dao->where("WIN_ID='$WIN_ID'")->delete();
		$this->assign("jumpUrl",__URL__."/winexeIndex");	
		$this->success("成功删除");
	}
	/*----------------window 菜单 排序 ------------*/    
    public function winexeListorder(){
    	$listorder=$_POST[listorder];
    	$dao=D("Winexe");
    	if (is_array($listorder)) {
    		foreach ($listorder as $WIN_ID=>$WIN_NO){
    			$dao->setField("WIN_NO",$WIN_NO,"WIN_ID='$WIN_ID'");
    			
    		}
    	}
    	
		$this->assign("jumpUrl",__URL__."/winexeIndex");	
		$this->success("排序完成");
    }	
}


?>