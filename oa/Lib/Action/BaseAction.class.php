<?php
import("ORG.Util.Page");

class BaseAction extends Action{
	var $mid;	//当前登陆的用户ID = mid
	var $uid;	//当前浏览的用户ID = uid
	protected  function _initialize(){
		//判断用户登录
		$this->mid	=	1; //Session::get("mid");
		$this->uid	=	$_REQUEST['uid'];

		if(empty($this->mid)){
			//登陆验证
			$this->redirect("checklogin","Public");
			exit;
		}
		if(empty($this->uid)){
			$this->uid	=	$this->mid;
		}
		$this->assign('mid',$this->mid);
		$this->assign('uid',$this->uid);

		parent::_initialize();
    }	
}
?>