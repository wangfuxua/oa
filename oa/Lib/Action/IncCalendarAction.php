<?php
import("@.Action.PublicAction");
class IncCalendarAction extends PublicAction {
	
	 function _initialize(){
		$this->curtitle="日历";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	public function index(){

		$this->display();
	}
	
}

?>