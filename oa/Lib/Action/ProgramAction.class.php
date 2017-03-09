<?php
import("@.Action.PublicAction");
//import("@.Action.ProgramSysManagerAction");
class ProgramAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目管理";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
}

?>