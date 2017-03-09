<?php

class DevelopmentAction extends PublicAction {
	var $curtitle;
	function _initialize(){
		$this->curtitle="开发日志";
		$this->assign("curtitle",$this->curtitle);
		
		parent::_initialize();
	}
	
	public function index(){
		$dao=D("Development");
		$map="";
		$count=$dao->count($map);
           if($count>0){
	        import("ORG.Util.Page");	
			$p= new Page($count,10);
			$list=$dao->where($map)
			          ->order("did desc")
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
		$did=$_REQUEST[did];
		
		$dao=D("Development");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }		
		if ($did) {
		
		    $_POST[edittime]=$this->CUR_TIME_INT;			
		    $dao->where("did='$did'")->save();
		    	
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
		if(!$_GET[did]){
			$this->error('开发日志不存在');
		}
		
		$from=$_REQUEST['from'];
		if (!$from) {
			$from="index";
		}
		$dao=D("Development");
		if ($dao->where("did='$_GET[did]'")
		     ->delete()){
		     	  $this->assign("jumpUrl",__URL__."/$from");
		          $this->success('删除成功');
		}else {
			      $this->assign("jumpUrl",__URL__."/$from");
			      $this->error('删除失败');
		}
		
	}
	
	public function edit(){
		$dao=D("Development");
		$did=$_REQUEST[did];
		
		$map=array("did"=>$did);
		$row=$dao->where($map)->find();
		$this->assign("row",$row); 
		$this->display();			
	}
	

}
?>