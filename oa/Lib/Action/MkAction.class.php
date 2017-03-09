<?php

class MkAction extends PublicAction {
	
	function _initialize(){
		$this->curtitle="模块管理";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	public function index(){
				$dao=D("SysFunction");
		$list=$dao->where("funcpath!=''")
		          ->order("FUNC_ID desc")
		          ->findall();
		$this->assign("list",$list);          
		$this->display();
	}
	
	public function form(){
	   $FUNC_ID=$_REQUEST[FUNC_ID];
	   $daopriv=D("UserPriv");
	   $array=array(
	       "shortcut"=>"桌面快捷方式",
	       "contextmenu"=>"右键菜单",
	       "quickstart"=>"快速启动",
	       "autorun"=>"自动运行"
	   );
	   
	   if ($FUNC_ID) {
	   	$dao=D("SysFunction");
	   	$row=$dao->where("FUNC_ID='$FUNC_ID'")->find();
	   	
	   	$this->assign("row",$row);  
	   	
	   }else{
	   	
	   	 $row=array();
	   	
	   }
	   $privs=$daopriv->findall();
       
	   foreach ($privs as $priv){//因为菜单位置不用限制，所以不用控制的这么麻烦了
               $checked="";
	   	   if($row[FUNC_ID]&&find_id($priv[funcidstr],$row[FUNC_ID])){
	   	   	   
	           $checked="checked";
	   	   }
	           $module_wz="";
	   	      foreach ($array as $key=>$value){
	   	   	         $chk="";
	   	         if($row[FUNC_ID]&&find_id($priv[$key],$row[FUNC_ID])){
	   	            $chk="checked";
	   	         }   	       	   	       	   	       
	   	   	   $module_wz .= "<input type=checkbox name='".$key."[]"."' value='$priv[USER_PRIV]' $chk>".$value;
	   	      }
	   	   $user_priv .= "<input type=checkbox name='funcidstr[]' value='$priv[USER_PRIV]' $checked >".$priv[PRIV_NAME]."&nbsp;&nbsp;".$module_wz."<br>";
	   }
	   
        $this->assign("user_priv",$user_priv);
		$this->display(); 	
		
	}
	
	public function formsubmit(){
		$FUNC_ID=$_POST[FUNC_ID];
		
		$funcidstr=$_POST[funcidstr];
		
		$shortcut=$_POST[shortcut];
		$contextmenu=$_POST[contextmenu];
		$quickstart=$_POST[quickstart];
		$autorun=$_POST[autorun];
		
		$daopriv=D("UserPriv");
		$dao=D("SysFunction");

		
		if(false === $dao->create()) {
	        	$this->error($dao->getError());
	    }
	    $privs=$daopriv->findall();
	    
		if (!$FUNC_ID){
			$id = $dao->add();
			$msg="成功添加";
		}else {
			$dao->where("FUNC_ID='$FUNC_ID'")->save();
            $id=$FUNC_ID;
            $msg="成功修改";
		}
		
		   $array=array(
		       "funcidstr",  
		       "shortcut",
		       "contextmenu",
		       "quickstart",
		       "autorun"
		   );
	      foreach ($array as $value){
	      	$arrstr=$$value;
	      	  foreach ($privs as $priv){
	      	  	   if(in_array($priv[USER_PRIV],$arrstr)){
	      	  	       if(!find_id($priv[$value],$FUNC_ID))	{
	      	  	       	  $str=$priv[$value].$FUNC_ID.",";//增加
			      	  	   $sql="update user_priv set $value='$str' where USER_PRIV='$priv[USER_PRIV]'";
			      	  	   $daopriv->execute($sql);    	  	       	  
	      	  	       }
	      	  	   }else {
	      	  	       if(find_id($priv[$value],$FUNC_ID)){
	      	  	       	  $str=str_replace($FUNC_ID.',','',$priv[$value]);//删除
			      	  	   $sql="update user_priv set $value='$str' where USER_PRIV='$priv[USER_PRIV]'";
			      	  	   $daopriv->execute($sql);	
	      	  	       }
	      	  	   }
	      	  	   
	      	  }
	      }  
	      
          $this->success($msg);
	      
	}
	
	public function delete(){
		
		
	}
		
}


?>