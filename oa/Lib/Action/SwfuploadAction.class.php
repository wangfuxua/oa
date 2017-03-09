<?php
class SwfuploadAction extends PublicAction {

	function _initialize(){
		parent::_initialize();
	}
	public function index(){

	$model= D("Attachments");


	$ar=$model->fileinfo_save();

	echo $ar[id];

	}

}

?>

