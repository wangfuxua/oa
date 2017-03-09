<?php 

class GeneralAction extends Action{
	public function index(){
		header("Content-Type:text/html; charset=utf-8");
		//$Face = new InterfaceModel();
		//$face = $Face->find();
   	    //$this->assign("face",$face);
	   // $USER_NAME_COOKIE=$_COOKIE["USER_NAME_COOKIE"];
	    //$this->assign("USER_NAME_COOKIE",$USER_NAME_COOKIE);
	    $this->display();
	}
	
} 
?>