<?php
import("@.Action.PublicAction");
class MyBlogAction extends PublicAction {
	
		function _initialize(){
		$this->curtitle="工作日志";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	public  function index(){
		$dao=D("Diary");
		$map="USER_ID='$this->LOGIN_USER_ID'";
		$limit=$_REQUEST[limit]?$_REQUEST[limit]:15;
		$start=$_REQUEST[start]?$_REQUEST[start]:0;
		$limits="$start,$limit";
	    $count=$dao->count($map);
	    foreach($map as $key=>$val) {
			if(is_array($val)) {
				foreach ($val as $t){
					$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						    $p->parameter   .=   "$key=".urlencode($val)."&";        
					}
		 }
		$list=$dao->where($map)
		          ->order("DIA_ID desc")
		          ->limit($limits)
		          ->findall();
         echo $_GET['callback'].'({"totalProperty":"'.$count.'","results":'.json_encode($list).'})'; 
	}

	public function insert(){
		$_POST[USER_ID]=$this->LOGIN_USER_ID;
		$DIA_ID=$_POST[DIA_ID];
		
		$dao=D("Diary");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
        if ($DIA_ID){
        	$dao->where("DIA_ID='$DIA_ID'")->save();
        	$id=$DIA_ID;
        }else{
            $id = $dao->add();
        }
        if($id) { //保存成功
           $success = "{'success': true}"; 
        }else { //失败提示
           $success = "{'success': false}"; 
        }
        print $success;	
		
		
	}
	
	public function delete(){
		$DIA_ID=$_REQUEST[id];
		if(!$DIA_ID){
			$success = "{'success': false}"; 
		}
		$dao=D("Diary");
		
		if ($dao->where("DIA_ID='$DIA_ID' AND USER_ID='$this->LOGIN_USER_ID'")
		     ->delete()){
		          $success = "{'success': true}"; 
		}else {
			$success = "{'success': false}"; 
			
		}
		print $success;	
	}
	
	public function edit(){
		$DIA_ID=$_REQUEST[id]=5;
		// AND USER_ID='$this->LOGIN_USER_ID'
		$dao=D("Diary");
		$row=$dao->where("DIA_ID='$DIA_ID'")
		         ->find();
		         //echo $dao->getlastsql();
		print json_encode($row);
		     
	}
	
}
?>