<?php
class AttachmentsModel extends BaseModel {
	/*
	protected $_map=array(
	  'filename'=>'name',
	  'filetype'=>'type',
	  'attachment'=>'savename',
	  'filesize'=>'size'
	);
	*/

  protected $_map=array(
	  'name'=>'filename',
	  'type'=>'filetype',
	  'savename'=>'attachment',
	  'size'=>'filesize'
	);

}

?>