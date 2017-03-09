<?php
import("@.Action.PublicAction");
class UrlAction extends PublicAction {
    	var $uploadpath_avatar;
	function _initialize(){
		$this->curtitle="公共网址设置";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->uploadpath_avatar=APP_PATH."/Tpl/default/Public/images/avatar/";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
    /*----------------公共网址 --------------*/
   public function index(){//公共网址 （添加和列表）
   	    $dao=D("Url");
   	    $map="USER=''";
   	    $count=$dao->count($map);
   	    if ($count>0) {
   	    		import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p          = new Page($count,$listRows);
				$list=$dao->where($map)->order("URL_NO")->limit("$p->firstRow,$p->listRows")->findall();
				//echo $dao->getlastsql();
                //分页跳转的时候保证查询条件		
				foreach($_REQUEST as $key=>$val) {
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						$p->parameter   .=   "$key=".urlencode($val)."&";        
					}
				}
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
   	    }
   	    $this->display();
   }
    public function urledit(){//修改网址信息
			$URL_ID=$_REQUEST[URL_ID];
		    $dao=D("Url");
		    $row=$dao->where("URL_ID='$URL_ID'")->find();
		    
		    $this->assign("row",$row);
		    $this->display();
    }
    
	public function urlsubmit(){//提交信息（添加和修改）
		    $URL_ID=$_REQUEST[URL_ID];
		    
		    $dao=D("Url");
		    //$_POST[USER]=$this->LOGIN_USER_ID;
		    if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        }
	        if($URL_ID){
	        	if ($re=$dao->where("URL_ID='$URL_ID'")->save()) {
	        		$this->assign("jumpUrl","index");
                   	$this->success("成功修改");	
                 }else{
                 	
                 	$this->error("修改失败");			
                 }
	        }else {
		        $id = $dao->add();
		        if ($id) {
		        	$this->assign("jumpUrl","index");
		           $this->success("成功添加");			
		        }else {
		        	$this->error("添加失败");			
		        }	        	
	        	
	        }
	}
	
	public function urldelete(){//删除（删除单个或是全部）
			$URL_ID=$_REQUEST[URL_ID];
		    $dao=D("Url");
		    if ($URL_ID) {
		    	$dao->where("URL_ID='$URL_ID'")->delete();
		    }else{
		    	$dao->where("USER=''")->delete();
		    } 
		   $this->redirect("index"); 
		   //$this->success("成功删除");
	}
	
	/*--------------左边菜单显示的URL------------------*/
	public function lefturl(){
		$dao=D("Url");
   	    $map="USER=''";
   	    $comlist=$dao->where($map)->order("URL_NO")->findall();
   	    
   	    $map="USER='$this->LOGIN_USER_ID'";
   	    $perlist=$dao->where($map)->order("URL_NO")->findall();		
		
		$this->assign("comlist",$comlist);
		$this->assign("perlist",$perlist);
		$this->display();
	}
	
}
?>