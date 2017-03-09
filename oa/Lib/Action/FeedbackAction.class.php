<?php

class FeedbackAction extends PublicAction {
	var $curtitle;
	function _initialize(){
		$this->curtitle="意见反馈";
		$this->assign("curtitle",$this->curtitle);
		
		parent::_initialize();
	}
	
	public function index(){
		
		$dao=D("Feedback");
		$map="userid='$this->LOGIN_USER_ID'";
		
		$count=$dao->count($map);
           if($count>0){
	        import("ORG.Util.Page");	
			$p= new Page($count,10);
			$list=$dao->where($map)
			          ->order("feedid desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
        $this->assign("LOGIN_USER_PRIV",$this->LOGIN_USER_PRIV);
        $this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID);	        
		$this->display();
	}
	
	public function submit(){
		$_POST[userid]=$this->LOGIN_USER_ID;
		$feedid=$_REQUEST[feedid];
		
		$dao=D("Feedback");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }		
		if ($feedid) {
		
		    $_POST[edittime]=$this->CUR_TIME_INT;			
		    $dao->where("feedid='$feedid'")->save();
		    	
            $this->assign("jumpUrl",__URL__."/index");
            $this->success('成功修改');		   
		}else {
			
			$_POST[addtime]=$this->CUR_TIME_INT;
			$_POST[edittime]=$this->CUR_TIME_INT;			
            $dao->add($_POST);
            $this->assign("jumpUrl",__URL__."/index");
            $this->success('成功添加');           
		}
        
		
	}
	
	public function delete(){
		if(!$_GET[feedid]){
			$this->error('意见不存在');
		}
		
		$from=$_REQUEST['from'];
		if (!$from) {
			$from="index";
		}
		$dao=D("Feedback");
		if ($dao->where("feedid='$_GET[feedid]'")
		     ->delete()){
		     	  $this->assign("jumpUrl",__URL__."/$from");
		          $this->success('删除成功');
		}else {
			      $this->assign("jumpUrl",__URL__."/$from");
			      $this->error('删除失败');
		}
		
	}
	
	public function listfeed(){//所有意见
		$dao=D("Feedback");
		$map="";
		$count=$dao->count($map);
           if($count>0){
	        import("ORG.Util.Page");	
			$p= new Page($count,10);
			$list=$dao->where($map)
			          ->order("feedid desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        
	    $this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID);   
        $this->assign("LOGIN_USER_PRIV",$this->LOGIN_USER_PRIV);	        
		$this->display();
	}

	public function edit(){
		$dao=D("Feedback");
		$feedid=$_REQUEST[feedid];
		
		$map=array("feedid"=>$feedid);
		$row=$dao->where($map)->find();
		$this->assign("row",$row); 
		$this->display();			
	}
	

}
?>