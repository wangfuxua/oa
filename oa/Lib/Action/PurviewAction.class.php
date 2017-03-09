<?php
//import("@.Action.PublicAction");
class PurviewAction extends PublicAction {
		function _initialize(){
		//$this->curtitle="权限设置";
		//$this->assign("curtitle",$this->curtitle);
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

	public function submit(){
       //$_POST[USER_ID]=$this->LOGIN_USER_ID;
	   /*	
		$dao=D("SysFunction");
		if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
        $id = $dao->add();
        if($id) { //保存成功
        $success = "{'success': true}"; 
        }else { //失败提示
          $success = "{'success': false}"; 
        }
        */
	    $success = "{'success': true}";
        print $success;	
	}
	
	public function delete(){
		
		
		
		if(!$_GET[DIA_ID]){
			
			$this->error('日志不存在');
		}
		$dao=D("Diary");
		
		if ($dao->where("DIA_ID='$_GET[DIA_ID]' AND USER_ID='$this->LOGIN_USER_ID'")
		     ->delete()){
		          $this->success('删除成功');
		//          $this->redirect('diary','index');
		}else {
			      $this->error('删除失败');
			//      $this->redirect('diary','index');
			
		}
		
	}
	
	public function save(){
		
				$success = "{'success': true}"; 
		
        print $success;
		
	}
}
?>