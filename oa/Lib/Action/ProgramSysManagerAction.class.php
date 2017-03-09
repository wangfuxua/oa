
<?php
import("@.Action.PublicAction");
import("@.Util.Program");

class ProgramSysManagerAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目管理";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	/*----------首页显示 --- 申报属性--------*/
    public function index(){
    	$dao=D("XmsbSx");
    	$list=$dao->order('SX_NAME')->findall();
    	$this->assign("list",$list);
    	    	
    	$this->display();
    }	
    
    public function menutop(){
    	$COLOR1="#D9E8FF";
        $COLOR2="#DDDDDD";
        $COLOR3="#B3D1FF";
        $this->assign("COLOR1",$COLOR1);
        $this->assign("COLOR2",$COLOR2);
        $this->assign("COLOR3",$COLOR3);
    	$this->display();
    }
 /*----------申报属性--------*/   
    public function sbsx(){
    	$dao=D("XmsbSx");
    	$list=$dao->order('SX_NAME')->findall();
    	$this->assign("list",$list);
    	$this->display();
    }
    
    public function sbsxform(){
    	if($_REQUEST[SX_ID]){
    		$title="编辑申报属性";
    		$dao=D("XmsbSx");
    		$vo=$dao->where("SX_ID='$_REQUEST[SX_ID]'")->find();
    	}else{ 
    	    $vo=array();
    		$title="新建申报属性";
    	}
    	$submit="sbsxsubmit";
    	$this->assign("vo",$vo);
    	$this->assign("title",$title);
    	$this->assign("submit",$submit);
    	$this->display();    
    }
    public function sbsxsubmit(){
    	$dao=D("XmsbSx");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
        if($_REQUEST[SX_ID]){
        	       //保存当前数据对象
		        if($result = $dao->where("SX_ID='$_POST[SX_ID]'")->save()) { //保存成功
		        	$this->assign("jumpUrl",__URL__."/index");
		            $this->success('成功修改');
		        }else { 
		        	$this->assign("jumpUrl",__URL__."/index");
		            $this->error('修改失败');
		        }
        }else {
        	    if($dao->add()) { //保存成功
        	    	$this->assign("jumpUrl",__URL__."/index");
		            $this->success('成功添加');
		        }else { 
		        	$this->assign("jumpUrl",__URL__."/index");
		            $this->error('添加失败');
		        }        	
        }
    }
    public function sbsxdelete(){
    	$SX_ID=$_REQUEST[SX_ID];
    	$dao=D('xmsblb');
    	$map=array("SX_ID"=>$SX_ID);
    	$count=$dao->count($map);
    	if ($count>0) {
    		$this->error("该属性被申请的项目使用，不能删除");
    	}else {
    		$dao=D("XmsbSx");
    		$dao->where($map)->delete();
    		$this->assign("jumpUrl",__URL__."/index");
    		$this->success("成功删除");
    	}
    	
    	
    	
    }
/*-------------申报流程---------------*/
   public function sblc(){
   	$this->curtitle="申报流程管理";
    $this->assign("curtitle",$this->curtitle);
   	$this->display();
   }    
   
   public function sblcmanage(){
    $TYPE=$_REQUEST[TYPE];
    $BM_ID=$_REQUEST[BM_ID];
    
    $pro= new Program();
   	if(empty($TYPEID)) 
   	    $pro->xm_getPrc($TYPE,$TYPENAME,$TYPEID);
   	
    if(empty($TYPEID)){
	  $this->error("错误<br>没有这个流程");
     }
    $limit=""; 
	if($TYPE == "CS"){
		if(isset($BM_ID))
			$CHOOSE_ID = $BM_ID;
		else
			//$CHOOSE_ID = -1;
			$CHOOSE_ID = $this->LOGIN_DEPT_ID;
			
	//ECHO $CHOOSE_ID;		
	$limit = " DEPT_ID = ".$CHOOSE_ID;
	$mydepttree=my_dept_tree(0,$CHOOSE_ID,0);
	$this->assign("mydepttree",$mydepttree);
	if(!$BM_ID)
	    $CHOOSE_ID="-1";
	    
	//ECHO $CHOOSE_ID;
	}else{
		$CHOOSE_ID = 0;
	}
	
	//echo $CHOOSE_ID;
    $list=$pro->xm_getPrcUsers($TYPEID,$CHOOSE_ID);
    
    $this->assign("list",$list);
    
    $userlist=my_user_list("USER_NAME","USER_ID","",$limit);
    $this->assign("userlist",$userlist);

    $this->assign("TYPE",$TYPE);
    $this->assign("TYPEID",$TYPEID);
   	$this->assign("TYPENAME",$TYPENAME);
   	$this->display();
   	
   }
public function sblcmanagesubmit(){
  @extract($_POST);
 //print_r($_POST);
 //Array ( [BM_ID] => 3 [USER_ID] => admin [OP] => add [LC_ID] => [TYPE] => CS [TYPENAME] => 初审 [TYPEID] => 3 ) 
 if(empty($BM_ID))$BM_ID = 0;
 if($OP=="add"){
 	$ORDERING = 0;
 	$dao=D('metadata');
 	$daolc=D("XmsbLc");
 	$ROW=$dao->where("id='$TYPEID'")->field('value1')->find();
 		if($ROW["value1"] == "FH"){
 			$daolc->where("TYPE_ID = $TYPEID")->delete();
 		}else{
 			$ROW=$daolc->where("TYPE_ID = $TYPEID AND BM_ID = $BM_ID")->field("MAX(ORDERING) as `mo`")->find();
   	        $ORDERING = intval($ROW["mo"]);
 		}
 		$_POST[ORDERING]=$ORDERING+1;
 		if(false === $daolc->create()) {
        	$this->error($dao->getError());
        }
 		$daolc->add();
 		//echo $daolc->getlastsql();
 	//exit;
 }
 if($OP=="del"){
 	$daolc=D("XmsbLc");
 	$daolc->where("LC_ID = $LC_ID")->delete();
 	
 }
 //
 $this->redirect("/sblcmanage/TYPE/".$_REQUEST[TYPE]."/TYPEID/".$_REQUEST[TYPEID]."/TYPENAME/".$_REQUEST[TYPENAME]."/BM_ID/".$_REQUEST[BM_ID].$_POST[XM_ID],'programsysmanager');
 
 //header("Location:".__URL__."/sblcmanage/TYPE/".$_REQUEST[TYPE]."/TYPEID/".$_REQUEST[TYPEID]."/TYPENAME/".$_REQUEST[TYPENAME]."/BM_ID/".$_REQUEST[BM_ID]);
 
}


public function refresh(){
	 //header('location:'.__URL__.'/filenew/file_sort/'.$_REQUEST[file_sort].'/sort_id/'.$_REQUEST[sort_id].'/CONTENT_ID/'.$_REQUEST[CONTENT_ID]);
	 $this->redirect("/sblcmanage/TYPE/".$_REQUEST[TYPE]."/BM_ID/".$_REQUEST[BM_ID]."/TYPEID/".$_REQUEST[TYPEID]."/TYPENAME/".$_REQUEST[TYPENAME],'programsysmanager');
	 //header("Location:".__URL__."/sblcmanage/TYPE/".$_REQUEST[TYPE]."/BM_ID/".$_REQUEST[BM_ID]."/TYPEID/".$_REQUEST[TYPEID]."/TYPENAME/".$_REQUEST[TYPENAME]);
}
     
 /*-------------申报问题---------------*/

 public function sbwt(){
 	
   	$this->curtitle="申报问题管理";
    $this->assign("curtitle",$this->curtitle);
   	$this->display();
 	
 }
 
 public function sbwtmanage(){
    $TYPE=$_REQUEST[TYPE];
    $TYPEID=$_REQUEST[TYPEID];
    
    $pro= new Program();
   	if(empty($TYPEID)) 
   	    $pro->xm_getPrc($TYPE,$TYPENAME,$TYPEID);
   	
    if(empty($TYPEID)){
	  $this->error("错误<br>没有这个流程");
     }
      	
    $list=$pro->xm_getPrcQuestions($TYPEID);
    $this->assign("list",$list);
    
    $this->assign("TYPE",$TYPE);
    $this->assign("TYPEID",$TYPEID);
   	$this->assign("TYPENAME",$TYPENAME);
   	
    $this->display();
 }
 
 public function sbwtmanagesubmit(){
 	     @extract($_POST);
 	     $dao=D("XmsbWt");
		 if($OP=="add"){
		 		if(false === $dao->create()) {
		        	$this->error($dao->getError());
		        }
		 		$dao->add();
		 }
		 if($OP == "del"){
		 	$dao->where("WT_ID = '$WT_ID'")->delete();
		 }
		 header("Location:".__URL__."/sbwtmanage/TYPEID/".$_REQUEST[TYPEID]."/TYPENAME/".$_REQUEST[TYPENAME]); 
 }
 
  /*-------------项目模板---------------*/
  public function mbgl(){
 	
   	$this->curtitle="模板管理";
    $this->assign("curtitle",$this->curtitle);
   	$this->display();
 	
 }
 
 public function mbglmanage(){
 	$TYPE=$_REQUEST[TYPE];
 	$dao=D('templates');
 	$list=$dao->table('templates as a')
 	          ->join('attachments as b on a.attid=b.attid')
 	          ->where("a.LB='$TYPE' and a.attid!='0'")
 	          ->findall();
 	          
 	//$list=$dao->where("LB='$TYPE' and attid!='0'")->findall();
 	$this->assign("list",$list);
 	$this->assign("TYPE",$TYPE);
 	$this->display();
 }
public function mbglmanagesubmit(){
	
	//print_r($_POST);
	//print_r($_FILES);
	//EXIT;
//Array ( [TYPE] => XMSB [OPERATION] => add [ATTACHMENT_NAME] => http_imgload.jpg [ATTACHMENT_NAME_OLD] => ) Array ( [ATTACHMENT] => Array ( [name] => http_imgload.jpg [type] => image/pjpeg [tmp_name] => D:\xampp\tmp\php1B84.tmp [error] => 0 [size] => 50563 ) ) 		
    if($_REQUEST[OPERATION]=='add'&&$_REQUEST[ATTACHMENT_NAME]){
    	//echo "a";exit;
	    $path	=	$this->uploadpath;
		$info	=	$this->_upload($path);
		//print_r($info);
		//exit;
		$data =$info[0];
		if($data){
			$data[addtime]=$this->CUR_TIME_INT;
			$data[filename]=$data[name];
			$data[attachment]=$data[savename];
			$data[filesize]=$data[size];
			$data[filetype]=$data[type];
			
			$dao = D("Attachments");
			$dao->create();
			$attid=$dao->add($data);
			//echo $dao->getlastsql();
			//echo $attid;exit;
			
			//exit;
		}
		
		$_POST[attid]=$attid;
		$_POST[LB]=$_POST[TYPE];
		
		$dao=D("templates");
			if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        }
	        $dao->add($_POST);
	        //echo $dao->getlastsql();
    }elseif ($_REQUEST[OPERATION]=='del'){
    	$dao=D("templates");
    	$row=$dao->where("ID='$_REQUEST[id]'")->find();
    	$this->_deleteattach($row[attid]);
    	$dao->where("ID='$_REQUEST[id]'")->delete();
            	
    } 
    
      header("Location:".__URL__."/mbglmanage/TYPE/".$_REQUEST[TYPE]); 	       
}  
  
	
}

?>