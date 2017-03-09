<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

class module {
	/*
    private $os;
	public function __construct($NewoaAction){
		$this->os = $NewoaAction;
	}
	*/
	private $os;
	
	public function __construct($NewoaAction){
		$this->os = $NewoaAction;
	}
	
	/** init() Initial page load or refresh has occured
	  * 
	  **/
	public function init(){
	    if(isset($_SESSION['modules'])){
	        unset($_SESSION['modules']);
	    }
	    
		if(isset($_SESSION['dependencies'])){
			unset($_SESSION['dependencies']);
		}
	}
	
	/** is_loadable() Returns true if all of the module's files (and dependencies) are found.
	  * The module can be loaded.
	  * 
	  * @param {string} $moduleId The client moduleId property 
	  **/
	private function is_loadable($moduleId){
	    $response = false;
	    //print_r($_SESSION['modules']);
	    //echo $_SESSION['modules']['can_load'][$moduleId];
	    //if(!isset($_SESSION['modules'])){
	    if(isset($_SESSION['modules'])){	
	        $this->find_files();
	    }
	    //echo $_SESSION['modules']['can_load'][$moduleId];
	    if(isset($_SESSION['modules']['can_load'][$moduleId])){
			if($_SESSION['modules']['can_load'][$moduleId]){
				
				$response = true;
			}
		}
		
		return $response;
	} // end is_loadable()
	
	
	
	/** find_files() Queries the Db and checks the module files.
	  * Results are stored in session.
	  **/
	private function find_files(){
	    //$member_id = $this->os->sessions->get_member_id();
		//$group_id = $this->os->sessions->get_group_id();
		
		$member_id=Session::get('member_id');
		$group_id=Session::get('group_id');
		if($member_id != "" && $group_id != ""){
	    	unset($_SESSION['modules']);
	    	
	    	$modules_dir = $this->os->get_module_dir();
			//echo $modules_dir;
			$sql = "SELECT
				M.id,
				M.module_id AS moduleId,
				concat(F.directory, F.file) AS path
				FROM
				qo_modules_files AS F
					INNER JOIN qo_modules AS M ON M.id = F.qo_modules_id
				WHERE
				M.active = 1";
			$dao=new Model();
			$list=$dao->table('qo_modules_files AS F')
			          ->join(array('INNER','qo_modules AS M ON M.id = F.qo_modules_id'))
			          ->field('M.id,M.module_id AS moduleId,concat(F.directory, F.file) AS path')
			          ->where('M.active = 1') 
			          ->findall();
			if($list){
				$prev_module = '';
				$is_allowed = false;
				foreach ($list as $row){
					$cur_module = $row['id'];
					if($prev_module != $cur_module){
						$prev_module = $cur_module;
						$is_loadable = 1;
						if($this->os->privilege->is_allowed("loadModule", $row["moduleId"], $member_id, $group_id)){					    
						    if(!$this->check_dependencies($modules_dir, $cur_module)){
						    	
								$is_loadable = 0;
							}
							$is_allowed = true;
						}else{
						    $is_allowed = false;
						}
					}
					
					if($is_allowed){
						$path = $row['path'];
						//echo $modules_dir.$path."--".is_file($modules_dir.$path)."<br>";
						//echo substr($modules_dir.$path,1,strlen($modules_dir.$path))."<br>";
						if($path != ''){
							if(!is_file(substr($modules_dir.$path,1,strlen($modules_dir.$path)))){
								//echo "c-";
								$_SESSION['modules']['has_error'][] = 'Script: module.php, Method: find_files, Missing file: '.$modules_dir.$path;
								$is_loadable = 0;
							}
						}
					}

					$_SESSION['modules']['can_load'][ $row['moduleId'] ] = $is_loadable;					
					
				}
			}
				if(count($_SESSION['modules']['has_error']) > 0){
				    //$this->os->error->log($_SESSION['modules']['has_error']);
				}
			}
	    }
	 // end find_files()
	
	
	
	/** check_dependencies()
	  * 
	  * $param {integer} $id The server (database) module id
	  **/
	private function check_dependencies($modules_dir, $id){
		$response = true;
	    
	    $sql = "SELECT
	    	M.module_id AS moduleId,
			D.directory,
			D.file
			FROM
			qo_modules_has_dependencies AS MD
				INNER JOIN qo_dependencies AS D ON D.id = MD.qo_dependencies_id
				INNER JOIN qo_modules AS M ON M.id = MD.qo_modules_id
			WHERE
			M.id = ".$id;
		
		if($result = mysql_query($sql)){
		    while($row = mysql_fetch_assoc($result)){
				$path = $row['directory'].$row['file'];
				//echo $modules_dir.$path;
				if($path != ''){
					if(!is_file(substr($modules_dir.$path,1,strlen($modules_dir.$path)))){
						$_SESSION['modules']['has_error'][] = 'Script: module.php, Method: check_dependencies, Missing file: '.$modules_dir.$path;
						$response = false;
					}
				}
		    }
		}
	    
	    return $response;
	} // end check_dependencies()
	
	
	
	/** get_id() Returns the id of a module's database record.
	  * 
	  * @param {string} $moduleId The client module's moduleId property.
	  **/
	public function get_id($moduleId){
		$id = '';
		
		//if($this->os->sessions->exists() && $moduleId != ""){
		$dao=new Model();
        if($moduleId != ""){ 			
			$sql = "SELECT
				id
				FROM
				qo_modules
				WHERE
				module_id = '".$moduleId."'";
			$list=$dao->query($sql);
			if($list){
				foreach ($list as $row){
				  $id = $row["id"];	
					
				}
				
			}
			/*
			echo $sql;
			echo mysql_num_rows($result = mysql_query($sql));
			if(mysql_num_rows($result = mysql_query($sql)) > 0){
				echo "a";
				$row = mysql_fetch_assoc($result);
				$id = $row["id"];
			}
			*/
		}
		//echo $id;
		return $id;
	} // end get_id()
	
	
	
	/** get_all() Returns new instances of the loaded modules.
	  * Used by getModules() of QoDesk.php
	  **/
	public function get_all(){
		$response = '';
		$m = $_SESSION['modules']['loaded'];
		
		for($i = 0, $len = count($m); $i < $len; $i++){
			$response .= "new ".$m[$i]['class']."(),";
		}
		
		$response = rtrim($response, ","); // trim the trailing comma
		
		return $response;
	} // end get_all()
	
	
	
	/** get_css() Returns a string of all css files to include
	  * Used by index.php
	  **/
	public function get_css(){
		$response = '';
		//$member_id = $this->os->sessions->get_member_id();
		//$group_id = $this->os->sessions->get_group_id();
		$member_id=Session::get('member_id');
		$group_id=Session::get('group_id'); 
		$modules_dira = $this->os->get_module_dir();
		//echo $modules_dir;
		if($member_id != "" && $group_id != ""){
			$sql = "SELECT
				M.module_id AS moduleId,
				F.directory,
				F.file
				FROM
				qo_modules_files AS F
					INNER JOIN qo_modules AS M ON M.id = F.qo_modules_id
				WHERE
				F.is_stylesheet = 1
				AND
				M.active = 1";
			
			if($result = mysql_query($sql)){				
				while($row = mysql_fetch_assoc($result)){
					// if the member is not allowed to load this module, skip it
					//echo !$this->os->privilege->is_allowed("loadModule", $row["moduleId"], $member_id, $group_id);
				    if(!$this->os->privilege->is_allowed("loadModule", $row["moduleId"], $member_id, $group_id)){
				    	//echo "a";
				    	continue;
				    }
				    
				    if(!$this->is_loadable($row["moduleId"])){
				    	//echo "a";
				        continue;
				    }
				    
				    $response .= '<link rel="stylesheet" type="text/css" href="'.$modules_dira.$row["directory"].$row["file"].'" />';
				    
				}
			}
			
			//$response;
		}
		
		return $response;
	} // end get_css()
	
	
	
	/** load() Prints the contents of the module's javascript files.
	  * Dependencies will also be loaded if needed.
	  * Used for Module on Demand functionality.
	  * 
	  * @param {string} $moduleId The client moduleId property
	  **/
	public function load($moduleId){
		
		$module_id = $this->get_id($moduleId); // the server (database) module id
		$member_id=Session::get('member_id');
		$group_id=Session::get('group_id'); 
		//echo $moduleId;
		if($module_id != "" && $member_id != "" && $group_id != ""){
			
			// if the member is not allowed to load this module, skip it
	    	if(!$this->os->privilege->is_allowed("loadModule", $moduleId, $member_id, $group_id)){
	    		die("{success: false, msg: 'You do not have the required privileges!'}");
	    	}
	    
			$document_root = $this->os->get_document_root();
			$modules_dir = $this->os->get_module_dir();
			
			// get module dependencies based on the member group
			$sql = "SELECT
				D.id,
		    	D.directory,
				D.file
				FROM
				qo_modules_has_dependencies AS MD
					INNER JOIN qo_dependencies AS D ON D.id = MD.qo_dependencies_id
					INNER JOIN qo_modules AS M ON M.id = MD.qo_modules_id
				WHERE
				M.active = 1
				AND
				M.id = ".$module_id;
			//echo $sql;
			if($result = mysql_query($sql)){
				while($row = mysql_fetch_assoc($result)){
					if($_SESSION['dependencies']['loaded'][ $row['id'] ]){ continue; }
					//echo $document_root.$modules_dir.$row["directory"].$row["file"];
				    print file_get_contents($document_root.$modules_dir.$row["directory"].$row["file"]);
				    
				    $_SESSION['dependencies']['loaded'][ $row['id'] ] = 1;
				}
			}
			
			// get module files
			$sql = "SELECT
				F.directory,
				F.file
				FROM
				qo_modules_files AS F
					INNER JOIN qo_modules AS M ON M.id = F.qo_modules_id
				WHERE
				F.is_stylesheet = 0
				AND
				F.is_server_module = 0
				AND
				F.is_client_module = 0
				AND
				M.active = 1
				AND
				M.id = '".$module_id."'";
			
			if($result = mysql_query($sql)){
				while($row = mysql_fetch_assoc($result)){				    
				    print file_get_contents($document_root.$modules_dir.$row["directory"].$row["file"]);
				}
			}
		}
	} // end load()
	
	
	
	/** load_all() Prints the content of all the client module files
	  **/
	public function load_all(){
		$member_id=Session::get('member_id');
		$group_id=Session::get('group_id'); 
		
		if($member_id != "" && $group_id != ""){
			$document_root = $this->os->get_document_root();
			$modules_dir = $this->os->get_module_dir();
			//echo $document_root;
			//echo $modules_dir;
			// get active modules
			$sql = "SELECT
				M.module_id AS moduleId,
				F.directory,
				F.file,
				F.class_name AS class
				FROM
				qo_modules_files AS F
					INNER JOIN qo_modules AS M ON M.id = F.qo_modules_id
				WHERE
				F.is_client_module = 1
				AND
				M.active = 1";
			
			if($result = mysql_query($sql)){
				$count = 0;
				
				while($row = mysql_fetch_assoc($result)){
					if($row['moduleId'] != '' && $row['directory'] != '' && $row['file'] != '' && $row['class'] != ''){
						
						// if the member is not allowed to load this module, skip it
						/*
					    if(!$this->os->privilege->is_allowed("loadModule", $row["moduleId"], $member_id, $group_id)){
					    	continue;
					    }
					    
					    // if the module is not valid
					    if(!$this->is_loadable($row["moduleId"])){
					        continue;
					    }
					    */
					    //echo $document_root.$modules_dir.$row["directory"].$row["file"];
					    //system/modules/grid-win/grid-win-override2.js
					     $files .= file_get_contents($document_root.$modules_dir.$row["directory"].$row["file"]);
					    
					    // track loaded modules
					    $_SESSION['modules']['loaded'][$count]['moduleId'] = $row["moduleId"];
					    $_SESSION['modules']['loaded'][$count]['class'] = $row["class"];
					    
					    $count++;
					}
				}
			}
		}
		return $files;
	} // end load_all()
	
	
	
	/** run_action() Will check the users privileges and execute the action if allowed
	  * 
	  * @param {string} $moduleId The client moduleId property
	  * @param {string} $action, the name of the action/method to call (e.g. $module->action())
	  **/
	public function run_action($moduleId, $action){
		$member_id=Session::get('member_id');
		$group_id=Session::get('group_id'); 

		if($member_id == '' || $group_id == ''){
			die("{success: false, msg: 'You are not currently logged in'}");
		}else{
			//if member is allowed this action on this module
			// 允许
			if(!$this->os->privilege->is_allowed($action, $moduleId, $member_id, $group_id)){
				die("{success: false, msg: 'You do not have the required privileges!'}");
			}
			
			$sql = "SELECT
				F.directory,
				F.file,
				F.class_name AS class
				FROM
				qo_modules_files AS F
					INNER JOIN qo_modules AS M ON M.id = F.qo_modules_id
				WHERE
				F.is_server_module = 1
				AND
				M.active = 1
				AND
				M.module_id = '".$moduleId."'";
			
				//echo $sql;
				//echo mysql_num_rows($result = mysql_query($sql));
				$result = mysql_query($sql);
			//if(mysql_num_rows($result = mysql_query($sql)) > 0){
				$row = mysql_fetch_assoc($result);
				$module_dir = $this->os->get_module_dir();
				
				$file = $module_dir.$row["directory"].$row["file"];
				$file= substr($file,1,strlen($file));
				$class = $row["class"];
				$class = 'MyTableSetAction';
                    import('@.Action.MyTableSetAction');
					if(class_exists($class)){				
						$module = new $class($this->os);
						if(method_exists($module, $action)){
							$module->$action();
						}
					}
		}
	} // end run_action()
}
?>