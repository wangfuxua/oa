<?php
class MeetingAction extends PublicAction {
	var $curtitle;
	var $status;
	function _initialize(){
		$this->curtitle="会议管理";
		$this->assign("curtitle",$this->curtitle);
		$this->status=array(
		    0=>"待批",
		    1=>"已准",
		    2=>"进行中",
		    3=>"未批准",
		    4=>"已结束"
		);
		parent::_initialize();
	}
	/*------------会议申请---------*/	
	public function index(){
		 //$lists=array(0=>0,1=>1,2=>2,3=>3,4=>4);
		 $lists=array(0=>0,1=>1,2=>2,3=>3);
		 $dao=D("Meeting");
		 foreach ($lists as $M_STATUS){
            if($M_STATUS==0)
		       $list[$M_STATUS][M_STATUS_DESC]="待批";
		    elseif($M_STATUS==1)
		       $list[$M_STATUS][M_STATUS_DESC]="已准";
		    elseif($M_STATUS==2)
		       $list[$M_STATUS][M_STATUS_DESC]="进行中";
		    elseif($M_STATUS==3)
		      $list[$M_STATUS][M_STATUS_DESC]="未批准";
		    elseif($M_STATUS==4)
		      $list[$M_STATUS][M_STATUS_DESC]="已结束";		 	
            $map="M_STATUS='$M_STATUS' and M_PROPOSER='$this->LOGIN_USER_ID'";
            $row=$dao->where($map)->order("M_REQUEST_TIME desc")->findall();
            if ($row) {
               $list[$M_STATUS][sub]=$row;	
            }else {
            	$list[$M_STATUS][sub]=array();
            } 
            
            //print_r($list).$M_STATUS;
            //exit;
            $list[$M_STATUS][MEETING_COUNT]=count($list[$M_STATUS][sub]);
		 }
         //print_r($list); 
		 $this->assign("list",$list);
		
		$this->display();
	}
	
	public function meetingform(){//申请表单
		$M_ID=$_REQUEST[M_ID];
		####选择用户
		//UserSelectAction::DeptUserSelectAll();
		UserSelectAction::DeptSelect();
		$dao_d=D("Department");//按部门选择人员
		$list1=$dao_d->DeptSelect();
		$dao_p=D("UserPriv");//按角色选择人员
		$list2=$dao_p->findall();
		$dao_u=D("User");//按个人选择人员
		$list3=$dao_u->findall();

		$this->assign("list1",$list1); 
		$this->assign("list2",$list2);  
		$this->assign("list3",$list3);  		
        if ($M_ID) {
            $dao=D("Meeting");
        	$ROW=$dao->where("M_ID='$M_ID'")->find();
        	$desc="会议修改";
        }else{
        	$ROW=array();
        	$ROW[M_START]=$this->CUR_TIME;
        	$ROW[M_END]=date("Y-m-d H:i:s",$this->CUR_TIME_INT+60*60);
        	$desc="会议申请，在您申请会议前请查看目前会议的情况，以免会议时间发生冲突";
        }
        $daoroom=D("MeetingRoom");
        $list=$daoroom->findall();
		#########弹出框开始#########
		UserSelectAction::DeptSelect();
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
		
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束#########  	
        $this->assign("list",$list);        
		$this->assign("desc",$desc);
		$this->assign("ROW",$ROW);

		$this->display();
	}
	public function meetingsubmit(){//表单提交
		$M_ID=$_REQUEST[M_ID];
		$M_ROOM=$_REQUEST[M_ROOM];
		$dao=D("Meeting");
		if (!$_REQUEST[M_TOPIC]) {
			$this->error("会议主题不能为空");
		}
		if (false===$dao->create()) {
			$this->error("操作失败");
		}		
		###时间段验证
		   $M_START=$_REQUEST[M_START];
		   $M_END=$_REQUEST[M_END];
		   
			if ($M_END<=$M_START) {
			   	$this->error("会议结束时间不能早于开始时间");
			}
				   
		   //$map="(EXTRACT(HOUR_MINUTE from M_END)>=EXTRACT(HOUR_MINUTE from '$M_START')||EXTRACT(HOUR_MINUTE from M_START)>=EXTRACT(HOUR_MINUTE from '$M_END')) AND M_ROOM='$M_ROOM' AND TO_DAYS(M_START)>=TO_DAYS('$M_START')";
		   //$map="(EXTRACT(HOUR_MINUTE from M_START)<EXTRACT(HOUR_MINUTE from '$M_END')||EXTRACT(HOUR_MINUTE from M_END)>EXTRACT(HOUR_MINUTE from '$M_START')) AND M_ROOM='$M_ROOM' AND TO_DAYS(M_START)>=TO_DAYS('$M_START') AND M_STATUS <3 AND M_STATUS>0";
		   $map="(
		         ( 
		         EXTRACT(HOUR_MINUTE from M_START) < EXTRACT(HOUR_MINUTE from '$M_END') and EXTRACT(HOUR_MINUTE from M_START) > EXTRACT(HOUR_MINUTE from '$M_START') 
		         ) 
		         or 
		         ( 
		         EXTRACT(HOUR_MINUTE from M_END) > EXTRACT(HOUR_MINUTE from '$M_START') and EXTRACT(HOUR_MINUTE from M_END) < EXTRACT(HOUR_MINUTE from '$M_END') 
		         )
		          ) AND M_ROOM = '$M_ROOM' AND TO_DAYS(M_START) >= TO_DAYS('$M_START') AND M_STATUS < 3 AND M_STATUS > 0";		   
		   
		   $count=$dao->count($map);
		   if ($count>0) {
		   	  $this->error("本会议室时间段有冲突");
		   }

		if ($M_ID) {//修改
			$dao->where("M_ID='$M_ID'")->save();
		    $this->success("成功修改");	
		}else {//添加 
			$a = UserAction::getUser_id($_POST[M_RENYUAN_ID]);
			$_POST[M_RENYUAN_ID]=implode(",",$a).",";  
			$_POST[M_REQUEST_TIME]=$this->CUR_TIME;
			$_POST[M_PROPOSER]=$this->LOGIN_USER_ID;
			$dao->add($_POST);
			$this->success("成功添加");	
		}
	}
	
   public function meetingdelete(){//删除
   	    $M_ID=$_REQUEST[M_ID];
   	    $from=$_REQUEST['from'];
		$dao=D("Meeting");
		$dao->where("M_ID='$M_ID'")->delete();
		//$this->success("成功删除");
		$this->redirect($from."/M_STATUS"."/".$_REQUEST[M_STATUS]);
   }
   public function meetingdetail(){//详细
   	    $M_ID=$_REQUEST[M_ID];
		$dao=D("Meeting");
		$daouser=D("User");
		$daoroom=D("MeetingRoom");
		
		$row=$dao->where("M_ID='$M_ID'")->find();
		$row[M_MEET_TIME]=$row[M_START]."至".$row[M_END];
		$user=$daouser->where("USER_ID='$row[M_PROPOSER]'")->find();
		$row[USER_NAME]=$user[USER_NAME];
		
		$room=$daoroom->where("MR_ID='$row[M_ROOM]'")->find();
		
		$row[M_ROOM]=$room[MR_NAME]."（".$room[MR_PLACE]."）";
		$this->assign("row",$row);
		$this->assign("status",$this->status);
		$this->display();
   }
     	
   /*----------------------------会议室设置---------------------*/
   
   public function meetingroom(){
   	$dao=D("MeetingRoom");
   	$list=$dao->findall();
   	$count=count($list);
   	$this->assign("COUNT",$count);
   	$this->assign("list",$list);
   	$this->display();
   }
	public function meetingroomform(){//会议室设置表单
		$MR_ID=$_REQUEST[MR_ID];
        if ($MR_ID) {
            $dao=D("MeetingRoom");
        	$ROW=$dao->where("MR_ID='$MR_ID'")->find();
        	$desc="会议室修改";
        }else{
        	$ROW=array();
        	$ROW[M_START]=$this->CUR_TIME;
        	$ROW[M_END]=date("Y-m-d H:i:s",$this->CUR_TIME_INT+60*60);
        	$desc="新建会议室";
        }
		$this->assign("desc",$desc);
		$this->assign("ROW",$ROW);

		$this->display();
	}
	  	
	public function meetingroomsubmit(){//表单提交
		$MR_ID=$_REQUEST[MR_ID];
		$dao=D("MeetingRoom");
		if (false===$dao->create()) {
			$this->error("操作失败");
		}
		if ($MR_ID) {//修改
			$dao->where("MR_ID='$MR_ID'")->save();
			$this->assign("jumpUrl",__URL__."/meetingroom");
		    $this->success("成功修改");	
		}else {//添加
			$dao->add();
			$this->assign("jumpUrl",__URL__."/meetingroom");
			$this->success("成功添加");	
		}
	}
	public function mrquery(){
		$MR_ID=$_REQUEST[MR_ID];
		$dao=D("Meeting");
        $daomr=D("MeetingRoom");				
        
        $row=$daomr->where("MR_ID='$MR_ID'")->find();
        $this->assign("row",$row);
        
           ####更新会议状态 后才能保证统计的正确性
		   $map="(EXTRACT(HOUR_MINUTE from M_START)<=".date("Hi",$this->CUR_TIME_INT)."&&EXTRACT(HOUR_MINUTE from M_END)>=".date("Hi",$this->CUR_TIME_INT).") AND TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."') AND M_STATUS <3 AND M_STATUS>0";////进行中的会议
		   $dao->setField("M_STATUS","2",$map);
		   $map="(EXTRACT(HOUR_MINUTE from M_END)<".date("Hi",$this->CUR_TIME_INT)." and TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')) OR TO_DAYS(M_END)<TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')";//结束的会议
		   $dao->setField("M_STATUS","4",$map);
		
           $map="(EXTRACT(HOUR_MINUTE from M_START)<=".date("Hi",$this->CUR_TIME_INT)."&&EXTRACT(HOUR_MINUTE from M_END)>=".date("Hi",$this->CUR_TIME_INT).") AND TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')";//
        $list=$dao->where("M_ROOM='$MR_ID' and (M_STATUS <3 or (".$map."))")->findall();
        if ($list) {
           $MEETING_COUNT=count($list);	
        }else {
           $MEETING_COUNT=0;
        }
        $this->assign("MEETING_COUNT",$MEETING_COUNT);
        $this->assign("list",$list);
        $this->assign("status",$this->status);
        
        $this->display();
        
	}

	public function mrquerylist(){
		$MR_ID=$_REQUEST[MR_ID];
		$dao=D("Meeting");
        $daomr=D("MeetingRoom");				
        
        $row=$daomr->where("MR_ID='$MR_ID'")->find();
        $this->assign("row",$row);
        
           ####更新会议状态 后才能保证统计的正确性
		   $map="(EXTRACT(HOUR_MINUTE from M_START)<=".date("Hi",$this->CUR_TIME_INT)."&&EXTRACT(HOUR_MINUTE from M_END)>=".date("Hi",$this->CUR_TIME_INT).") AND TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."') AND M_STATUS <3 AND M_STATUS>0";////进行中的会议
		   $dao->setField("M_STATUS","2",$map);
		   $map="(EXTRACT(HOUR_MINUTE from M_END)<".date("Hi",$this->CUR_TIME_INT)." and TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')) OR TO_DAYS(M_END)<TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')";//结束的会议
		   $dao->setField("M_STATUS","4",$map);
		
           $map="(EXTRACT(HOUR_MINUTE from M_START)<=".date("Hi",$this->CUR_TIME_INT)."&&EXTRACT(HOUR_MINUTE from M_END)>=".date("Hi",$this->CUR_TIME_INT).") AND TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')";//
        $list=$dao->where("M_ROOM='$MR_ID' and (M_STATUS <3 or (".$map."))")->findall();
        if ($list) {
           $MEETING_COUNT=count($list);	
        }else {
           $MEETING_COUNT=0;
        }
        $this->assign("MEETING_COUNT",$MEETING_COUNT);
        $this->assign("list",$list);
        $this->assign("status",$this->status);
        
        $this->display();
        
	}
		
	public function mrlist(){
	   	$dao=D("MeetingRoom");
	   	$list=$dao->findall();
	   	//echo $dao->getlastsql();
	   	$count=count($list);
	   	$this->assign("COUNT",$count);
	   	$this->assign("list",$list);
	   	$this->display();
        
	}
		
	public function mrdetail(){//会议室详细
		$MR_ID=$_REQUEST[MR_ID];
        if ($MR_ID) {
            $dao=D("MeetingRoom");
        	$ROW=$dao->where("MR_ID='$MR_ID'")->find();
        	$desc="会议室修改";
        }
		$this->assign("desc",$desc);
		$this->assign("ROW",$ROW);
		$this->display();
	}
		
	public function mrdelete(){//单个删除
		$MR_ID=$_REQUEST[MR_ID];
		$dao=D("MeetingRoom");
		$dao->where("MR_ID='$MR_ID'")->delete();		
		$this->redirect("meetingroom");
	}
	public function mrdeleteall(){//删除所有
		$dao=D("MeetingRoom");
		$dao->delete();		
		$this->redirect("meetingroom");
	}
	
    /*--------------------会议查询-------------------------*/
	/*    
	public function query(){
       //$this->display();		
		$M_STATUS=$_REQUEST[M_STATUS];
		//$daomr=D("MeetingRoom");
		
	    $dao=D("Meeting");
	    $list=$dao->where("M_STATUS='$M_STATUS'")->findall();
	    if ($list) {
	        $COUNT=count($list);    	
	    }else {
	    	$COUNT="0";
	    }
	    
	    //print_r($list);
	    $this->assign("COUNT",$COUNT);
	    $this->assign("status",$this->status);
		$this->assign("list",$list);
		$this->display();
		       
	}
	*/
	public function search(){
		$M_STATUS=$_REQUEST[M_STATUS];
		if (!$M_STATUS) {
			$M_STATUS=0;
		}
		$dao=D("Meeting");
		
		if($M_STATUS==2) {//进行中的会议
		   $map="(EXTRACT(HOUR_MINUTE from M_START)<=".date("Hi",$this->CUR_TIME_INT)."&&EXTRACT(HOUR_MINUTE from M_END)>=".date("Hi",$this->CUR_TIME_INT).") AND TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."') AND M_STATUS <3 AND M_STATUS>0";
		   $dao->setField("M_STATUS","2",$map);
		}elseif ($M_STATUS==4){//已结束会议
		   $map="(EXTRACT(HOUR_MINUTE from M_END)<".date("Hi",$this->CUR_TIME_INT)." and TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')) OR TO_DAYS(M_END)<TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')";
		   $dao->setField("M_STATUS","4",$map);
		}
		else{ 
		   $map=array("M_STATUS"=>$M_STATUS);
		}
		
		
		//$map=array("M_STATUS"=>$M_STATUS);
		 
		$COUNT=$dao->count($map);
        if ($COUNT>0) {
        	
           $p = new Page($COUNT);
           	
           $list=$dao->where($map)->order("M_REQUEST_TIME desc")->limit("$p->firstRow,$p->listRows")->findall();
           
           $page       = $p->show();
		   $this->assign("page",$page);
        }
	    //print_r($list);
	    $this->assign("COUNT",$COUNT);
	    $this->assign("status",$this->status);
	    $this->assign("M_STATUS",$M_STATUS);
		$this->assign("list",$list);
		$this->display();
		
	}

	public function searchform(){
		$daoroom=D("MeetingRoom");
        $list=$daoroom->findall();

        $this->assign("list",$list);  
		$this->display();
	}
	
	public function searchformsubmit(){
		$dao=D("Meeting");
		$map=$this->_search("Meeting");

		$list=$dao->where($map)->findall();
		//echo $dao->getlastsql();
	    if ($list) {
	        $COUNT=count($list);    	
	    }else {
	    	$COUNT="0";
	    }
	    
	    $this->assign("COUNT",$COUNT);
	    $this->assign("status",$this->status);
		$this->assign("list",$list);
		$this->display();
	}
	
	protected function _search($name='') 
    {
        //生成查询条件
        /*
        $rearray=$this->_relation();
       // print_r($rearray);
		if(empty($name)) {
			$name	=	$this->name;
		}
		$model	=	D($name);
        foreach($model->getDbFields() as $key=>$val) {
            if(isset($_REQUEST[$val]) && $_REQUEST[$val]!='') {
            	if($val=="M_ROOM"&&$_REQUEST[$val]=='0')
                     continue;
                if ($val=="M_START"||$val=="M_END"||$val=="M_RENYUAN") {
                	continue;
                }
           
                $re=$_REQUEST[$val."_RELATION"];
                
                if($re>=1&&$re<=6){
                    $map[$val]	=	array($rearray[$re],$_REQUEST[$val]);
                }elseif ($re==7){
                   $map[$val]	=	array($rearray[$re],$_REQUEST[$val].'%');
                }elseif($re==8){
                   $map[$val]	=	array($rearray[$re],'%'.$_REQUEST[$val].'%');
                }elseif ($re==9){
                   $map[$val]	=	array($rearray[$re],'%'.$_REQUEST[$val]);   
                }
            }
        }
         if ($_REQUEST[M_START]&&$_REQUEST[M_START1]) {
         	  $map[M_START] = array($_REQUEST[M_START],$_REQUEST[M_START1]);
         }elseif ($_REQUEST[M_START]&&!$_REQUEST[M_START1]){
         	  $map[M_START] = array("egt",$_REQUEST[M_START]);
         }elseif (!$_REQUEST[M_START]&&$_REQUEST[M_START1]) {
         	  $map[M_START] = array("elt",$_REQUEST[M_START1]);
         }
         if ($_REQUEST[M_END]&&$_REQUEST[M_END1]) {
         	  $map[M_END] = array($_REQUEST[M_END],$_REQUEST[M_END1]);
         }elseif ($_REQUEST[M_END]&&!$_REQUEST[M_END1]){
         	  $map[M_END] = array("egt",$_REQUEST[M_END]);
         }elseif (!$_REQUEST[M_END]&&$_REQUEST[M_END1]) {
         	  $map[M_END] = array("elt",$_REQUEST[M_END1]);
         }
         */
        $conditions="";
         if ($_REQUEST[M_ROOM]) {
            $condition[]= " M_ROOM = $_REQUEST[M_ROOM]";           	
          } 
		 if($_REQUEST[keywords]!="")
		    $condition[]=" (M_NAME like '%".$_REQUEST[keywords]."%' OR M_TOPIC like '%".$_REQUEST[keywords]."%' OR M_DESC like '%".$_REQUEST[keywords]."%')";       
		 if($_REQUEST[M_RENYUAN]!="")
		    $condition[]=" (M_RENYUAN like '%".$_REQUEST[M_RENYUAN]."%' OR M_RENYUAN_NAME like '%".$_REQUEST[M_RENYUAN]."%')"; 

         if ($_REQUEST[M_START]&&$_REQUEST[M_START1]) {
         	  $condition[] = " M_START >= '$_REQUEST[M_START]' AND M_START <= '$_REQUEST[M_START1]'";
         }elseif ($_REQUEST[M_START]&&!$_REQUEST[M_START1]){
         	  $condition[] = " M_START >= '$_REQUEST[M_START]'";
         }elseif (!$_REQUEST[M_START]&&$_REQUEST[M_START1]) {
         	  $condition[] = " M_START <= '$_REQUEST[M_START1]'";
         }
         if ($_REQUEST[M_END]&&$_REQUEST[M_END1]) {
         	  $condition[] = " M_END >= '$_REQUEST[M_END]' AND M_END <= '$_REQUEST[M_END1]'";
         }elseif ($_REQUEST[M_END]&&!$_REQUEST[M_END1]){
         	  $condition[] = " M_END >= '$_REQUEST[M_END]'";
         }elseif (!$_REQUEST[M_END]&&$_REQUEST[M_END1]) {
         	  $condition[] = " M_END <= '$_REQUEST[M_END1]'";
         }
        if (is_array($condition))
                $conditions = implode($_REQUEST[RELATION], $condition);
                		    		    
        return $conditions;
    }
    protected function _relation(){
    	$rearray=array(
    	"1"=>"eq",
		"2"=>"gt",
		"3"=>"lt",
		"4"=>"egt",
		"5"=>"elt",
		"6"=>"neq",
		"7"=>"like",
		"8"=>"like",
		"9"=>"like"
    	);
    	return $rearray;
    }	
    
    
    
	/*----会议管理------*/

	public function manage(){
		 $lists=array(0=>0,1=>1,2=>2,3=>3,4=>4);
		$M_STATUS=$_REQUEST[M_STATUS];
		if (!$M_STATUS) {
			$M_STATUS=0;
		}
				 
		 $dao=D("Meeting");
		 
		 /*
		 foreach ($lists as $M_STATUS){
		 	
            if($M_STATUS==0)
		       $list[$M_STATUS][M_STATUS_DESC]="待批";
		    elseif($M_STATUS==1)
		       $list[$M_STATUS][M_STATUS_DESC]="已准";
		    elseif($M_STATUS==2)
		       $list[$M_STATUS][M_STATUS_DESC]="进行中";
		    elseif($M_STATUS==3)
		      $list[$M_STATUS][M_STATUS_DESC]="未批准";
		    elseif($M_STATUS==4)
		      $list[$M_STATUS][M_STATUS_DESC]="已结束";		 	
		      
		     // and M_PROPOSER='$this->LOGIN_USER_ID' 
            $map="M_STATUS='$M_STATUS'";
            $row=$dao->where($map)->findall();
            if ($row) {
               $list[$M_STATUS][sub]=$row;	
            }else {
            	$list[$M_STATUS][sub]=array();
            }
            
            //print_r($list).$M_STATUS;
            //exit;
            $list[$M_STATUS][MEETING_COUNT]=count($list[$M_STATUS][sub]);
		 }
		 */
         //print_r($list); 
		 

		if($M_STATUS==2) {//进行中的会议
		   $map="(EXTRACT(HOUR_MINUTE from M_START)<=".date("Hi",$this->CUR_TIME_INT)."&&EXTRACT(HOUR_MINUTE from M_END)>=".date("Hi",$this->CUR_TIME_INT).") AND TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."') AND M_STATUS <3 AND M_STATUS>0";
		   $dao->setField("M_STATUS","2",$map);
		}elseif ($M_STATUS==4){//已结束会议
		   $map="(EXTRACT(HOUR_MINUTE from M_END)<".date("Hi",$this->CUR_TIME_INT)." and TO_DAYS(M_END)=TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')) OR TO_DAYS(M_END)<TO_DAYS('".date("Ymd",$this->CUR_TIME_INT)."')";
		   $dao->setField("M_STATUS","4",$map);
		   
		}
		else{ 
		   $map=array("M_STATUS"=>$M_STATUS);
		}
		
		//$map=array("M_STATUS"=>$M_STATUS);
		$COUNT=$dao->count($map);
		//echo $dao->getlastsql();
        if ($COUNT>0) {
        	
           $p = new Page($COUNT);
           	
           $list=$dao->where($map)->order("M_REQUEST_TIME desc")->limit("$p->firstRow,$p->listRows")->findall();
           
           $page       = $p->show();
		   $this->assign("page",$page);
        }
	    $this->assign("COUNT",$COUNT);
	    $this->assign("status",$this->status);
	    $this->assign("M_STATUS",$M_STATUS);        		 
		$this->assign("list",$list);
		$this->display();
	}
	
	public function checkup(){
		$M_STATUS=$_REQUEST[M_STATUS];
		$M_ID=$_REQUEST[M_ID];
		
		$dao=D("Meeting");
		$row=$dao->where("M_ID='$M_ID'")->find();
		
		if ($M_STATUS==1) {
			###时间段验证
			   $M_START=$row[M_START];
			   $M_END=$row[M_END];
			   $M_ROOM=$row[M_ROOM];
				//$map="(EXTRACT(HOUR_MINUTE from M_START)<EXTRACT(HOUR_MINUTE from '$M_END')||EXTRACT(HOUR_MINUTE from M_END)>EXTRACT(HOUR_MINUTE from '$M_START')) AND M_ROOM='$M_ROOM' AND TO_DAYS(M_START)>=TO_DAYS('$M_START')  AND M_STATUS <3 AND M_STATUS>0"; //查看是否与已经批准的会议冲突//AND M_STATUS='1'
		   $map="(
		         ( 
		         EXTRACT(HOUR_MINUTE from M_START) < EXTRACT(HOUR_MINUTE from '$M_END') and EXTRACT(HOUR_MINUTE from M_START) > EXTRACT(HOUR_MINUTE from '$M_START') 
		         ) 
		         or 
		         ( 
		         EXTRACT(HOUR_MINUTE from M_END) > EXTRACT(HOUR_MINUTE from '$M_START') and EXTRACT(HOUR_MINUTE from M_END) < EXTRACT(HOUR_MINUTE from '$M_END') 
		         )
		          ) AND M_ROOM = '$M_ROOM' AND TO_DAYS(M_START) >= TO_DAYS('$M_START') AND M_STATUS < 3 AND M_STATUS > 0";
		   				
			   $count=$dao->count($map);
			   if ($count>0) {
			   	  $this->error("本会议室时间段有冲突");
			   }
		}
		$dao->setField("M_STATUS",$M_STATUS,"M_ID='$M_ID'");
		//echo $dao->getlastsql();
		//exit();
		
		//短信提醒
		if($M_STATUS=="0")
		   $CONTENT="您的会议申请已被撤销！";
		else if($M_STATUS=="1")
		   $CONTENT="您的会议申请已被批准！";
		else if($M_STATUS=="3")
		   $CONTENT="您的会议申请未被批准！";
		$this->send_sms($this->LOGIN_USER_ID,$row[M_PROPOSER],8,$CONTENT);
		
		if ($M_STATUS==1) {//会议申请已被批准！
			$dao=D("MeetingRoom");
			$rowroom=$dao->where("MR_ID='$row[M_ROOM]'")->find();
						
			###短信提醒要出席会议的人员
			$renarray=explode(",",$row[M_RENYUAN_ID]);
			foreach ($renarray as $userid){
				if ($userid) {
				$content=$row[M_START]."至".$row[M_END]."在".$rowroom[MR_NAME]."有会议要参加，会议发起人：".getUsername($row[M_PROPOSER]);
				$this->send_sms($row[M_PROPOSER],$userid,0,$content);
				}
			}
			//发送公告通知
			$data[TO_ID]="ALL_DEPT";
			$data[SUBJECT]="会议通知";
			$data[CONTENT]="<p align=\"center\"><strong><font color=\"black\" size=\"5\">会议通知</font></strong></p>";
			$data[CONTENT].="<center>  <TABLE width=\"100%\" border=0>";
			$data[CONTENT].="<TR style=\"border-bottom:1px #000 dotted\">";
			$data[CONTENT].="<td>名称：</td><td>".$row[M_NAME]."</td></tr>";
			$data[CONTENT].="<TR style=\"border-bottom:1px #000 dotted\">";
			$data[CONTENT].="<td>主题：</td><td>".$row[M_TOPIC]."</td></tr>";
			$data[CONTENT].="<TR style=\"border-bottom:1px #000 dotted\">";
			$data[CONTENT].="<td>描述：</td><td>".$row[M_DESC]."</td></tr>";
			$data[CONTENT].="<TR style=\"border-bottom:1px #000 dotted\">";
			$data[CONTENT].="<td>开始时间：</td><td>".$row[M_START]."</td></tr>";
			$data[CONTENT].="<TR style=\"border-bottom:1px #000 dotted\">";
			$data[CONTENT].="<td>结束时间：</td><td>".$row[M_END]."</td></tr>";
			$data[CONTENT].="<TR style=\"border-bottom:1px #000 dotted\">";
			$data[CONTENT].="<td>会议室：</td><td>".$rowroom[MR_NAME]."</td></tr>";
			$data[CONTENT].="</table>";
			
			$data[SEND_TIME]=$this->CUR_TIME;
			$data[BEGIN_DATE]=$this->CUR_TIME;
			$data[END_DATE]=$row[M_END];
  			$dao=D("Notify");
  			$dao->create();
  			$dao->add($data);
		}
		
		$this->redirect("manage");//返回
	}
	

	public function managesearchform(){
		$daoroom=D("MeetingRoom");
        $list=$daoroom->findall();

        $this->assign("list",$list);  
		$this->display();
	}
	
	public function managesearchformsubmit(){
		$dao=D("Meeting");
		$map=$this->_search("Meeting");
		echo $map;
		$list=$dao->where($map)->findall();
	    if ($list) {
	        $COUNT=count($list);    	
	    }else {
	    	$COUNT="0";
	    }
	    $this->assign("COUNT",$COUNT);
	    $this->assign("status",$this->status);
		$this->assign("list",$list);
		$this->display();
	}
		
}

?>