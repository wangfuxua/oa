<?
class userselect{
	
	
	protected function getuser($dept_id,$PRIV_NO_FLAG){
		$LOGIN_USER_PRIV=Session::get('LOGIN_USER_PRIV');//登录用户等级值
		
			         if($LOGIN_USER_PRIV!="1" && $PRIV_NO_FLAG){
			            $map = "DEPT_ID=$dept_id and user.USER_PRIV=user_priv.USER_PRIV and user_priv.USER_PRIV!=1";
		             //$map = "DEPT_ID=$row[DEPT_ID] and user.USER_PRIV=user_priv.USER_PRIV and user_priv.PRIV_NO>$PRIV_NO and user_priv.USER_PRIV!=1";			            
			         }else{
			            $map = "DEPT_ID=$dept_id and user.USER_PRIV=user_priv.USER_PRIV";
			         }
			            $daouser=new Model();
			            $users=$daouser->table("user,user_priv")
			                           ->where($map)
			                           ->order("PRIV_NO,USER_NAME")
			                           ->findAll(); 
			                           
			     foreach ($users as $userskey=>$usersval){
			       	$users[$userskey][UserState]=array(
			      	           "UserState"=>array("Labor"=>$usersval[USER_LABOR],"Spell"=>$usersval["USER_SPELL"],"SpellSimple"=>$usersval["USER_SPELL_SIMPLE"],"State"=>"","Sex"=>$usersval[SEX]),
			     	           "test"=>""
			                    );
			     }
			     
			     return $users;
			     		
	}
	/**
	 * 		          //if (!$DEPT_PRIV) {//这儿还有问题
		            //	continue;
		            //}
	 * */
   /*------------------部门用户选择树形------------------------------*/	
   public function _DeptUserTree($PARENT_ID,$PRIV_NO_FLAG=1,$PRIV_NO){
		$dao=D("Department");
		$LOGIN_USER_PRIV=Session::get('LOGIN_USER_PRIV');//登录用户等级值
		//$LOGIN_DEPT_ID=Session::get("LOGIN_DEPT_ID");
		$list=$dao->where("DEPT_PARENT=$PARENT_ID")->order("DEPT_NO")->findall();
		if ($list) {
			foreach ($list as $key=>$row){
				if($PRIV_NO_FLAG){
		          $DEPT_PRIV=is_dept_priv($row[DEPT_ID]);
				}else{
		          $DEPT_PRIV=1;
				}
		         $users=array();
		         if($DEPT_PRIV==1){//显示用户
                    $users=$this->getuser($row[DEPT_ID],$PRIV_NO_FLAG);
		         }
		          $row[user]=$users;
		          
		          $listchild=$this->_DeptUserTree($row[DEPT_ID],$PRIV_NO_FLAG=1,$PRIV_NO);		          	
		          
	      	   	  if (!$listchild) {
	      	   	  	if (is_dept_priv($row[DEPT_ID])) {
	      	   	        $rlist[]=$row;
	      	   	  	}
	      	   	  }else{
	      	   	  	 $rlist[]=$row;	
	      	   	  	 foreach ($listchild as $keys=>$rows){
	      	   	  	    $rlist[]=$rows;
	      	   	  	 }
	      	   	  }
			}
		}
		return $rlist;
	}	
/*------------------部门用户选择树形全部无权限------------------------------*/	
   public function _DeptUserTreeAll($PARENT_ID){
		$dao=D("Department");
		$LOGIN_USER_PRIV=Session::get('LOGIN_USER_PRIV');//登录用户等级值
		//$LOGIN_DEPT_ID=Session::get("LOGIN_DEPT_ID");
		$list=$dao->where("DEPT_PARENT=$PARENT_ID")->order("DEPT_NO")->findall();
		if ($list) {
			foreach ($list as $key=>$row){
		         $users=array();
			         //echo $map;
			         $map = "DEPT_ID=$row[DEPT_ID] and user.USER_PRIV=user_priv.USER_PRIV and user_priv.USER_PRIV!=1";
			            $daouser=new Model();
			            $users=$daouser->table("user,user_priv")
			                           ->where($map)
			                           ->order("PRIV_NO,USER_NAME")
			                           ->findAll();  
		         	         
		          $row[user]=$users;
	      	   	  $listchild=$this->_DeptUserTreeAll($row['DEPT_ID']);
	      	   	  
	      	   	  if (!$listchild) {
	      	   	     $rlist[]=$row;
	      	   	  }else{
	      	   	  	 $rlist[]=$row;	
	      	   	  	 foreach ($listchild as $keys=>$rows){
	      	   	  	    $rlist[]=$rows;
	      	   	  	 }
	      	   	  	 
	      	   	  }
      	   	  		         
			}
		}
		return $rlist;
	}	

/*------------------部门用户选择树形全部无权限------------------------------*/	
   public function _DeptUserTreeAllOnline($PARENT_ID){
		$dao=D("Department");
		$LOGIN_USER_PRIV=Session::get('LOGIN_USER_PRIV');//登录用户等级值
		//$LOGIN_DEPT_ID=Session::get("LOGIN_DEPT_ID");
		$list=$dao->where("DEPT_PARENT=$PARENT_ID")->order("DEPT_NO")->findall();
		if ($list) {
			foreach ($list as $key=>$row){
		         $users=array();
			         //echo $map;
			         $map = "DEPT_ID=$row[DEPT_ID] and user.USER_PRIV=user_priv.USER_PRIV and user_priv.USER_PRIV!=1";
			            $daouser=new Model();
			            $users=$daouser->table("user,user_priv")
			                           ->where($map)
			                           ->order("PRIV_NO,USER_NAME")
			                           ->findAll();  
			                           
			            foreach ($users as $userskey=>$usersval){
			            	$dsession=D("UserSession");
			            	$sessrow=$dsession->where("userid='$usersval[USER_ID]'")->find();
			            	if ($sessrow) {
			            		$status="On".$sessrow[user_status];
			            	}else {
			            		$status="Off";
			            	}
			            	
			            	$users[$userskey][UserState]=array(
			            	                        "UserState"=>array("Labor"=>$usersval[USER_LABOR],"Spell"=>$usersval["USER_SPELL"],"State"=>$status,"Sex"=>$usersval[SEX],"SpellSimple"=>$usersval[USER_SPELL_SIMPLE]),
			            	                     "test"=>""

			            	                     );
			            	
			            }               
		         	         //$users[UserState]=array("Labor"=>$users[USER_LABOR],"Spell"=>$users["USER_SPELL"],"State"=>"");
		         	         
		          $row[user]=$users;
	      	   	  $listchild=$this->_DeptUserTreeAllOnline($row['DEPT_ID']);
	      	   	  
	      	   	  if (!$listchild) {
	      	   	     $rlist[]=$row;
	      	   	  }else{
	      	   	  	 $rlist[]=$row;	
	      	   	  	 foreach ($listchild as $keys=>$rows){
	      	   	  	    $rlist[]=$rows;
	      	   	  	 }
	      	   	  	 
	      	   	  }
      	   	  		         
			}
		}
		return $rlist;
	}	
		
   /*------------------部门选择------------------------------*/	
   public function _DeptTree($PARENT_ID,$PRIV_NO_FLAG,$PRIV_NO){
		$dao=D("Department");
		$LOGIN_USER_PRIV=Session::get('LOGIN_USER_PRIV');//登录用户等级值
		$list=$dao->where("DEPT_PARENT=$PARENT_ID")->order("DEPT_NO")->findall();
		
		
		if ($list) {
			foreach ($list as $key=>$row){
				if($PRIV_NO_FLAG)
		         $DEPT_PRIV=is_dept_priv($row[DEPT_ID]);
		        else
		         $DEPT_PRIV=1;

	      	   	  $listchild=$this->_DeptTree($row['DEPT_ID'],$PRIV_NO_FLAG,$PRIV_NO);
	      	   	  if (!$listchild) {
	      	   	     $rlist[]=$row;
	      	   	  }else{
	      	   	  	 $rlist[]=$row;	
	      	   	  	 foreach ($listchild as $keys=>$rows){
	      	   	  	    $rlist[]=$rows;
	      	   	  	 }
	      	   	  	 
	      	   	  }
      	   	  		         
			}
		}
		return $rlist;
	}
		
	
}


?>