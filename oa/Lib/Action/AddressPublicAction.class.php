<?php
//import("@.Action.PublicAction");
include_cache(APP_PATH."/Common/address.php");
class AddressPublicAction extends PublicAction {
    var $name="Address";
	public 	function _initialize(){
		$this->curtitle="公共通讯薄";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
    protected function  _grouplist(){
				$dao=D("AddressGroup");
		        $list=$dao->where("USER_ID=''")->order("GROUP_NAME asc")->findall(); 
		        return $list;    	
    }
    
	public function index(){
		$GROUP_ID=intval($_REQUEST[GROUP_ID]);
        $this->menu(); 		
        $this->address($GROUP_ID);
		$this->display();
	}
	
	protected  function menu(){
		//分组
		$dao=D("AddressGroup");
		$menulist=$dao->where("USER_ID=''")->order("GROUP_NAME asc")->findall();
		$this->assign("menulist",$menulist);           
		//索引		
        import("@.Util.mb"); 
        $mb= new Mb();
        $sy=$mb->mbs(); 
       // $py=new pinyin(); 
	    for($i=ord('A'); $i<=ord('Z'); ++$i) {
			$name[chr($i)]=array();
			$nidx[chr($i)]=array();
		}
		$name['other']=array();
		$nidx['other']=array();
	  
		$dao=D("Address");
		$listsy=$dao->where("USER_ID=''") ->order("PSN_NAME") ->findall(); 
		if ($listsy) {
				foreach ($listsy as $row){ 
			    	$s=$row['PSN_NAME'];
					$idx='other';
					if(ord($s[0])>=128){
						$FirstName=substr($s, 0, 3);
						foreach($sy as $key => $s){
							if(strpos($s,$FirstName)){ 
								$idx=strtoupper($key);
								break;
							} 
						}
					}else{
						$FirstName=strtoupper($s[0]);
						if($FirstName>='A' && $FirstName<='Z')
							$idx=$FirstName;
						else $idx='other';
					}
					if(!in_array($FirstName, $nidx[$idx]))
					array_push($nidx[$idx], $FirstName);
					array_push($name[$idx], $row); 
		    	}
            	$INDEX=0;
		    	foreach ($name as $key=>$r){
		    			if(count($name[$key])>0) {
							   if($key=='other'){
							      $names[$INDEX][TABLE_STR]="<b>其它</b>(".count($name[$key]).") - ".implode(', ', $nidx[$key]);
							      $names[$INDEX][TABLE_STR_URL]=implode(', ', $nidx[$key]);
							   }else{
							      $names[$INDEX][TABLE_STR]="<b>".$key."</b>(".count($name[$key]).") - ".implode(', ', $nidx[$key]);
							      $names[$INDEX][TABLE_STR_URL]=implode(', ', $nidx[$key]);
							   }
						       $names[$INDEX][ID_STR]="";
						       foreach($r as $ROW) {
						          $names[$INDEX][ID_STR].=$ROW[ADD_ID].",";
						       }
						       $INDEX++;
						} 	
		    	}   	
		}          
        $this->assign("names",$names); 
	}
	
	protected function address($GROUP_ID){ 
		$_GET[GROUP_ID]=intval($_GET[GROUP_ID]);
		$dao=D("AddressGroup");
		$group=$dao->where("GROUP_ID='$_GET[GROUP_ID]'")->find();
		$GROUP_NAME=$group[GROUP_NAME];         
		if($_GET[GROUP_ID]==0)
		$GROUP_NAME="默认"; 
		$this->assign("GROUP_NAME",$GROUP_NAME);
        $this->assign("GROUP_ID",$_GET[GROUP_ID]);
		$dao=D("Address"); 
		$map="USER_ID='' and GROUP_ID='$_GET[GROUP_ID]'";
		$count=$dao->count($map);
        import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p= new Page($count,$listRows);
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page); 
		 $list=$dao->where($map)
		          ->order("ADD_ID desc")
		          ->limit("$p->firstRow,$p->listRows")
		          ->findall();
		 $this->assign("list",$list);  
	} 
	public function search(){
         $list=$this->_grouplist();
		 $this->assign("list",$list);
         $this->menu(); 		 	
		 $this->display();
	} 
	protected function _search($name=''){
        //生成查询条件
		if(empty($name)) {
			$name	=	$this->name;
		}
		$model	=	D($name);
		$map['USER_ID']="";
        foreach($model->getDbFields() as $key=>$val) {
            if(isset($_REQUEST[$val]) && $_REQUEST[$val]!='') {
            	if($val=="SEX"&&$_REQUEST[$val]=='ALL')
                     continue;
            	if($val=="GROUP_ID"&&$_REQUEST[$val]=='-1')
                     continue;
                if($val=="GROUP_ID"||$val=="SEX")
                    $map[$val]	=	$_REQUEST[$val];
                else
                   $map[$val]	=	array('like','%'.$_REQUEST[$val].'%');
            }
        }
        return $map;
    }
    	
    public function SearchSubmit(){ 
    	if ($_REQUEST[ID_STR]){
    		 $ID_STR=$_REQUEST[ID_STR];
    		 $this->assign("desc","索引结果【".$_REQUEST[TABLE_STR]."】"); 
    		 $ID_ARRAY=explode(",",$ID_STR);
             $dao=D("Address");
    		 $map="ADD_ID in ($ID_STR"."0)";
	         $count=$dao->count($map); 
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
			 $p  = new Page($count,$listRows);	
		     $list=$dao->where($map)->limit("$p->firstRow,$p->listRows")->findall();		    		 
		     $page = $p->show();
		     $this->assign("page",$page); 
			 $this->assign("list",$list); 
    	}else {
	        $map=$this->_search("Address");
	        $dao=D("Address");
	        $count=$dao->count($map);
        	import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p= new Page($count,$listRows);
			//分页显示
				foreach($_REQUEST as $key=>$val) {
		                if ($key=='a'||$key=='m'){
		                   continue;
		                }
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.htmlentities(urlencode($t))."&";
						}
					}else{
						$p->parameter   .=   "$key=".htmlentities(urlencode($val))."&";
					}
				}
			$page = $p->show();
			$this->assign("page",$page); 
	        if(!empty($dao)){ 
	        	$list = $dao ->limit("$p->firstRow,$p->listRows") ->findall($map,"*");
	        	$sql = $dao->getlastsql();
	        } 
	        
			 $this->assign("list",$list);
			 $this->assign("desc","查询结果");
    	}
    	$this->menu(); 		
		$this->display(); 
    }
    
    public function detail(){
		$ADD_ID=$_REQUEST[ADD_ID];
		$dao=D("Address");
		$row=$dao->where("ADD_ID='$ADD_ID'")->find();
		if($row[SEX]==0){
			$row[SEX]="男";
		}else{
			$row[SEX]="女";
		}
		$this->assign("row",$row);
		$this->display();
	} 
}
?>