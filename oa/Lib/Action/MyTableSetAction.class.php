<?php
class MyTableSetAction extends Action {
	private $os;

	public function __construct($NewoaAction){
		$this->os = $NewoaAction;
	}
	
	public function saveAppearance(){
		print $this->save('appearance');
	}
	
	public function saveAutorun(){
		print $this->save('autorun');
	}
	
	public function saveBackground(){
		print $this->save('background');
	}
	
	public function saveQuickstart(){
		print $this->save('quickstart');
	}
	
	public function saveShortcut(){
		//echo "bbc";
		print $this->save('shortcut');
	}
	
	public function viewThemes(){
		print $this->os->preference->get_theme_thumbs();
	}
	
	public function viewWallpapers(){
		print $this->os->preference->get_wallpaper_thumbs();
	}
	
	// end public module actions

	private function save($what){
		$success = "{'success': false}";
	
		switch(true){
			case ($what == "autorun" || $what == "quickstart" || $what == "shortcut"):
				//echo "b";
				// clear old launcher data for member (based on group)
				//print_r($_POST[ids]);
				$this->os->launcher->clear("member", $what)
				//if($this->os->launcher->clear("member", $what)){					
					$ids = $_POST[ids];
					//$ids."c";
					$ids = json_decode(get_magic_quotes_gpc() ? stripslashes($ids) : $ids);
					//print_r($ids);
					// if ids are found
					if(count($ids) > 0){
						$member_id = Session::get('member_id');
						$group_id = Session::get('group_id');
				
						// os will decode the ids
						if($this->os->launcher->set($member_id, $group_id, $ids, $what)){
							$success = "{'success': true}";
						}
					}else{
						$success = "{'success': true}";
					}
				//}
				
				break;
			case ($what == "appearance"  || $what == "background"):
	
				$styles = array(
					'backgroundcolor' => $_POST["backgroundcolor"],
					'fontcolor' => $_POST["fontcolor"],
					'theme_id' => $_POST["theme"],
					'transparency' => $_POST["transparency"],
					'wallpaper_id' => $_POST["wallpaper"],
					'wallpaperposition' => $_POST["wallpaperposition"]
				);
				
				if($styles['backgroundcolor'] != "" && $styles['fontcolor'] != "" && $styles['theme_id'] != "" && $styles['transparency'] != "" && $styles['wallpaper_id'] != "" && $styles['wallpaperposition'] != ""){
					if($this->os->preference->set_styles($styles)){
						$success = "{'success': true}";
					}
				}
				
				break;
		}
		//echo $success;
		return $success;
	}
	
	
	
}


?>