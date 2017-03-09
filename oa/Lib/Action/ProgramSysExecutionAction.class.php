<?php
import("@.Action.PublicAction");
import("@.Util.Program");

class ProgramSysExecutionAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目管理";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
    public function index(){
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
    
 /*----------立项类别--------*/   
    public function lxlb(){
    	$dao=D("XmssLb");
    	$list=$dao->order('LB_NAME')->findall();
    	$this->assign("list",$list);
    	$this->display();
    }

    public function lxlbform(){
    	
    	if($_REQUEST[LB_ID]){
    		$title="编辑项目类别";
    		$dao=D("XmssLb");
    		$vo=$dao->where("LB_ID='$_REQUEST[LB_ID]'")->find();
    	}else{ 
    	    $vo=array();
    		$title="新建项目类别";
    	}
    	$submit="sbsxsubmit";
    	$this->assign("vo",$vo);
    	$this->assign("title",$title);
    	$this->assign("submit",$submit);
    	$this->display(); 
    }   
    
  public function lxlbsubmit(){
  	    $dao=D("XmssLb");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
        if($_REQUEST[LB_ID]){
        	       //保存当前数据对象
		        if($result = $dao->where("LB_ID='$_POST[LB_ID]'")->save()) { //保存成功
		            $this->success('成功修改');
		        }else { 
		            $this->error('修改失败');
		        }
        }else {
        	    if($dao->add()) { //保存成功
		            $this->success('成功添加');
		        }else { 
		            $this->error('添加失败');
		        }        	
        }
  }
  
public function lxlbdelete(){
	$dao=D("XmssLx");
	$row=$dao->where("XMLB_ID=$_REQUEST[LB_ID]")->find();
	if (empty($row)){
		$dao=D("XmssLb");
		$dao->where("LB_ID='$_REQUEST[LB_ID]'")->delete();
		header("Location:".__URL__."/lxlb/");
	}else {
		$this->error("该类别被立项的项目使用，不能删除");
	}
	
}
 /*----------项目审核人设置--------*/
 
public function shr(){
	$dao=D('metadata');
	$list=$dao->where("name='XMWH'")->order("value1 ASC")->findall();
	$this->assign("list",$list);
	$this->display();
}

public function shrmanage(){
	$WH_ID=$_REQUEST[WH_ID];
	$WH_NAME=$_REQUEST[WH_NAME];
    $dao=D("metadata");
    $row=$dao->where("id=$WH_ID")->find();
    $WH_NAME=$row[value1];
    
	$pro= new Program();
	$list=$pro->xm_getWhUsers($WH_ID);
	$this->assign("list",$list);
	
	$this->assign("WH_ID",$WH_ID);
	$this->assign("WH_NAME",$WH_NAME);

	$userlist=my_user_list("USER_NAME","USER_ID");
    $this->assign("userlist",$userlist);
	
	$this->display();
	
}

public function shrmanagesubmit(){
  @extract($_POST);
 //print_r($_POST);
 //Array ( [BM_ID] => 3 [USER_ID] => admin [OP] => add [LC_ID] => [TYPE] => CS [TYPENAME] => 初审 [TYPEID] => 3 ) 
 
 if($OP=="add"){
 	$ORDERING = 0;
 	$dao=D("XmssLc");
 	$ROW=$dao->where("WH_ID = $WH_ID")->field("MAX(ORDERING) as `mo`")->find();
   	$ORDERING = intval($ROW["mo"]);
    $_POST[ORDERING]=$ORDERING+1;
 	if(false === $dao->create()) {
       $this->error($dao->getError());
    }
 	$dao->add();
 }
 if($OP=="del"){
 	$dao=D("XmssLc");
 	$dao->where('LC_ID = $_REQUEST[LC_ID]')->delete();
 }
 
 if($OP == "up"){
 	
 	$dao=D("XmssLc");
 	$ROW=$dao->where("LC_ID = $_REQUEST[LC_ID]")->field("ORDERING")->find();
 	$newROW=$dao->where("WH_ID = $WH_ID AND ORDERING < $ROW[ORDERING]")->field("ORDERING")->order("ORDERING DESC")->find();
 	$dao->save($ROW,"WH_ID = $_REQUEST[WH_ID] AND ORDERING = $newROW[ORDERING]");
 	$dao->save($newROW,"LC_ID = $_REQUEST[LC_ID]");
 }
 if($OP == "down"){
 	
 	$dao=D("XmssLc");
 	$ROW=$dao->where("LC_ID = $_REQUEST[LC_ID]")->field("ORDERING")->find();
 	$newROW=$dao->where("WH_ID = $WH_ID AND ORDERING > $ROW[ORDERING]")->field("ORDERING")->order("ORDERING ASC")->find();
 	$dao->save($ROW,"WH_ID = $_REQUEST[WH_ID] AND ORDERING = $newROW[ORDERING]");
 	$dao->save($newROW,"LC_ID = $_REQUEST[LC_ID]");
 }
 
 header("Location:".__URL__."/shrmanage/WH_ID/".$_REQUEST[WH_ID]."/WH_NAME/".$_REQUEST[WH_NAME]);
 
}
 /*----------项目维护--------*/
 
public function wh(){
	$dao=D('metadata');
	$list=$dao->where("name='XMWH'")->order("value1 ASC")->findall();
	$this->assign("list",$list);
	$this->display();
}

public function whdelete(){
	$dao=D('metadata');
	$dao->where("id='$_REQUEST[id]'")->delete();	
	header("Location:".__URL__."/wh");
}

public function whform(){
	$id=$_REQUEST[id];
	if($id){
		$dao=D('metadata');
	    $row=$dao->where("id='$id'")->find();
	    	
	}else{
	  $row=array();	
		
	}
	$this->assign("row",$row);
	$this->assign("id",$id);
	$pro=new program();
	$this->assign("ZLPH",$pro->ZLPH);
	$this->assign("XMCG",$pro->XMCG);
	$this->display();
}

public function whformsubmit(){
	$id=$_REQUEST[id];
	$dao=D('metadata');
        
	if(false === $dao->create()) {
       	$this->error($dao->getError());
    }  
                $data[name]="XMWH";    
                $data[value1]=$_POST[NAME];   
        	    $data[value2]=$_POST[NAME];
        	    $data[value3]=$_POST[FL];        	
	if($id){
        	       //保存当前数据对象
		        if($result = $dao->where("id=$id")->save($data)) { //保存成功
		            $this->success('成功修改');
		        }else { 
		            $this->error('修改失败');
		        }		
	}else{
        	    if($dao->add($data)) { //保存成功
		            $this->success('成功添加');
		        }else { 
		            $this->error('添加失败');
		        }
		
	}
	
	
	
}

}

?>