<?php
include_cache(APP_PATH."/Common/mytable.php");
class MytableAction extends PublicAction {
	var $curtitle;
	
	function _initialize(){
		$this->curtitle="我的办公桌";
        $this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	public function index(){
		
		//第一部分：公告展示
		$notify = new NotifyModel();
		$notifylist = $notify->where("(InStr(TO_ID,'ALL_DEPT,') or InStr(TO_ID,'$this->LOGIN_DEPT_ID,')) AND BEGIN_DATE<=$this->CUR_TIME_INT")->order("BEGIN_DATE DESC")->limit("0,10")->findAll();
		//echo $notify->getLastSql();
		$this->assign('notifylist',$notifylist);
		//第二部分：待办工作
		$flow = new ZworkFlowModel();
		$flowList = $flow->where("powerUser like '%$this->LOGIN_USER_ID%'")->findall();
		$work = new ZworkWorkModel();
		foreach ($flowList as $k=>$v){
			$a=$work->where("zworkId=$v[zworkId] AND state=$v[flowId]")->find();
			if(!empty($a)){
				$workRow[$k]=$a;
				$workRow[$k][tag]=$v['tag'];
			}
		}
		$this->assign('workRow',$workRow);
		//第三部分：速办工作
		$workmodule = new ZworkWorkModuleModel();
		$workList = $workmodule->limit(5)->order("modelId asc")->findall();
		$this->assign('workList',$workList);
		//第四部分：新闻展示
		$news = new NewsModel();
		$newslist = $news->top5('','*','newId desc');
		$this->assign('newslist',$newslist);
/*******注释掉
		$map = "(TO_ID='ALL_DEPT' or InStr(TO_ID,',$_SESSION[LOGIN_DEPT_ID],')>0 or InStr(TO_ID,'$_SESSION[LOGIN_DEPT_ID],')=1) and begin_date<='$this->CUR_DATE' and (end_date>='$this->CUR_DATE' or end_date is null)";
		$dao = D("Notify");
		$notifylist = $dao->findall($map,'*','BEGIN_DATE desc,NOTIFY_ID desc');
		
		$this->assign('notifylist',$notifylist);
		
        $map="";
		$dao = D('news');
		$newslist = $dao->findall($map,'*','NEWS_TIME desc');
		$this->assign('newslist',$newslist);
*************/		
		
		$grkq=$gzsb=$kqgl=$flow="";
		$privs=parent::_user_priv();
		$user_func_id_str=$privs["FUNC_ID_STR"];
		$flow_count=0;
	     if(find_id($user_func_id_str,"7")){
            $flow_count++;
	     	$grkq="<li>个人考勤：<a href=\"/general/attendance/personal\">上下班登记</a></li>";
	     }
	     
	     if(find_id($user_func_id_str,"28")){
	     	$dao=D('Sal_Flow');
	     	$map="to_days(BEGIN_DATE)<=to_days('$this->CUR_DATE') and to_days(END_DATE)>=to_days('$this->CUR_DATE')";
            $gz=$dao->find($map,"*","BEGIN_DATE desc");
            if($gz){
            	$flow_count++;
	     	    $gzsb="<li>工资上报：<a href=\"/general/salary/submit/sal_index.php?FLOW_ID=$FLOW_ID\">$CONTENT</a><li>";
            }
	     }
   	     if(find_id($user_func_id_str,"26")){
	     	$dao=D("AttendOut");
            $kq=$dao->table('attend_out as attend_out')
                    ->where("attend_out.LEADER_ID='$_SESSION[LOGIN_USER_ID]' and to_days(SUBMIT_TIME)=to_days('$this->CUR_DATE') and ALLOW='0'")
                    ->join('user as user on attend_out.USER_ID=user.USER_ID')
                    ->field('attend_out.*,user.*')
                    ->findall();
            if($kq){
            	foreach ($kq as $value){
            	$flow_count++;
            	$USER_NAME=$value[USER_NAME];
	     	    $kqgl .= "<li>考勤管理：<a href=\"/general/attendance/manage\">批示".$USER_NAME."的外出申请</a></li>";
            	}
            }
             $dao=D('attend_leave');
             $kq=$dao->table('attend_leave as attend_leave')
                    ->where("attend_leave.LEADER_ID='$_SESSION[LOGIN_USER_ID]' and attend_leave.status='1' and attend_leave.allow in('0','3')")
                    ->join('user as user on attend_leave.USER_ID=user.USER_ID')
                    ->field('attend_leave.*,user.*')
                    ->findall();           
            if($kq){
            	foreach ($kq as $value){
            	$flow_count++;
            	$USER_NAME=$value[USER_NAME];
                $ALLOW=$ROW["ALLOW"];
			      if($ALLOW=="0")
			         $ALLOW="批示".$USER_NAME."的请假申请";
			      else
			         $ALLOW="批示".$USER_NAME."的销假申请";
	     	    $kqgl .= "<li>考勤管理：<a href=\"/general/attendance/manage\">".$ALLOW."</a></li>";
	     	    
            	}
            }
                 
	     }
	      $dao=D('FlowRunPrcs');
	      $map="USER_ID='$_SESSION[LOGIN_USER_ID]' and prcs_flag<>'4'";
	      $flows=$dao->findall($map,"*");
	      if($flows){
	      	 foreach ($flows as $ROW){
	      	 	    $flow_count++;
				    $RUN_ID = $ROW["RUN_ID"];
				    $PRCS_FLAG = $ROW["PRCS_FLAG"];
				    if($PRCS_FLAG=="1")
				       $STATUS="<img src='/images/email_close.gif' alt='未接收' align='absmiddle'>";
				    else if($PRCS_FLAG=="2")
				       $STATUS="<img src='/images/email_open.gif' alt='已接收' align='absmiddle'>";
				    else
				       $STATUS="<img src='/images/flow_next.gif' alt='已办结' align='absmiddle'>";
				       
	                $dao=D('FlowRun');
	                $ROW=$dao->where("RUN_ID='$RUN_ID'")
	                         ->find();
	                if($ROW){         
				       $FLOW_ID=$ROW["FLOW_ID"];
				       $RUN_NAME=$ROW["RUN_NAME"];
	                }
	                $dao=D('FlowType');
	                $ROW=$dao->where("FLOW_ID='$FLOW_ID'")
	                         ->find();
	                if($ROW)
	                   $FLOW_NAME=$ROW["FLOW_NAME"];          
	                   
	      	 	$flow="<li>工作流：".$STATUS." <a href=\"/general/workflow\">".$FLOW_NAME." - ".$RUN_NAME."</a></li>";
	      	 }
	      	
	      	
	      }
	     
	     $this->assign("FLOW_COUNT",$flow_count);
	     if(!$flow_count){
           $grkq="无待办事宜";	     	
	     }
	     $this->assign("grkq",$grkq);
	     $this->assign("gzsb",$gzsb);
	     $this->assign("kqgl",$kqgl);
	     $this->assign("flow",$flow);
	     
	     $this->assign("calendarlist",$this->calendarlist());
	     $this->assign("affairlist",$this->affairlist());

	     $birthday=$this->WeeklyBirthday();
	     $this->assign("birthday",$birthday);
	     
	     $mails=$this->emailcount();
	     $this->assign("mails",$mails);
	     
	     $perurl=$this->PerUrl();
	     $comurl=$this->ComUrl();
	     $this->assign("perurl",$perurl);
	     $this->assign("comurl",$comurl);
	     $this->assign("cur_date",$this->CUR_DATE);
		$this->display();
		
	}
	
	
	
	
	public function saveSet(){
		$dao=D("UserSetting");
		$row=$dao->where("userid='$this->LOGIN_USER_ID'")->find();
		if ($row) {
		   $dao->setField("setting",$_REQUEST[jsonstr],"userid='$this->LOGIN_USER_ID'");	
		}else {
		   $data[uid]=$this->_uid;
		   $data[userid]=$this->LOGIN_USER_ID;
		   $data[jsonstr]=$_REQUEST[jsonstr];
		   $dao->save($data);	
		}
	}
	public function getSet(){
		$dao=D("UserSetting");
		$row=$dao->where("userid='$this->LOGIN_USER_ID'")->find();
		echo $row[jsonstr];
	}
	
	protected function emailcount(){
		$mails=array();
		$dao=D("Email");
		$mapnew="BOX_ID=0 and TO_ID='$this->LOGIN_USER_ID' and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG!='1'";//新邮件数量(收)
		//$mapnew="TO_ID='$this->LOGIN_USER_ID' and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG!='1'";
		$mails[NEW_LETER_COUNT]=$dao->count($mapnew);
		
		$mapin="BOX_ID=0 and TO_ID='$this->LOGIN_USER_ID' and SEND_FLAG='1' and DELETE_FLAG!='1'";//所有收件箱
		//$mapin="TO_ID='$this->LOGIN_USER_ID' and SEND_FLAG='1' and DELETE_FLAG!='1'";
		$mails[INBOX_COUNT]=$dao->count($mapin);
		
		$mapout="FROM_ID='$this->LOGIN_USER_ID' and SEND_FLAG='0'  AND DELETE_FLAG='0'";
		//$mapout="FROM_ID='$this->LOGIN_USER_ID' and SEND_FLAG='0'";
        $mails[OUTBOX_COUNT]=$dao->count($mapout);
		
       return $mails;
       		
	}
	/*-------------显示日程安排----私有方法--------*/
	protected function calendarlist(){
		$dao=D('Calendar');
		$calendarlist=$dao->where("USER_ID='$this->LOGIN_USER_ID' and to_days(CAL_TIME)=to_days('$this->CUR_DATE')")
		                  ->order('CAL_TIME')
		                  ->findall();
	    return $calendarlist;	                  
	}
	/*-------------显示日程安排----私有方法--------*/
	protected function affairlist(){
		$dao=D('Affair');
		$aff=$dao->where("USER_ID='$this->LOGIN_USER_ID' and BEGIN_TIME<='$this->CUR_TIME'")
		         ->order('REMIND_TIME')
		         ->findall();
		 if($aff){
		 	foreach ($aff as $key=>$ROW){
		 		    $AFF_ID=$ROW["AFF_ID"];
				    $USER_ID=$ROW["USER_ID"];
				    $TYPE=$ROW["TYPE"];
				    $REMIND_DATE=$ROW["REMIND_DATE"];
				    $REMIND_TIME=$ROW["REMIND_TIME"];
				    $CONTENT=$ROW["CONTENT"];
				    
				    $FLAG=0;
				    if($TYPE=="2")
				       $FLAG=1;
				    elseif($TYPE=="3" && date("w",$this->CUR_TIME_INT)==$REMIND_DATE)
				       $FLAG=1;
				    elseif($TYPE=="4" && date("j",$this->CUR_TIME_INT)==$REMIND_DATE)
				       $FLAG=1;
				    elseif($TYPE=="5")
				    {
				       $REMIND_ARR=explode("-",$REMIND_DATE);
				       $REMIND_DATE_MON=$REMIND_ARR[0];
				       $REMIND_DATE_DAY=$REMIND_ARR[1];
				       if(date("n",$this->CUR_TIME_INT)==$REMIND_DATE_MON && date("j",$this->CUR_TIME_INT)==$REMIND_DATE_DAY)
				          $FLAG=1;
				    }
				    
				   if($FLAG==1){
    	                $affairlist[$key]=$ROW;
				   }
		 	}
		 	
		 }
		 		         
	    return $affairlist;	                  		
	}
	
	protected function  WeeklyBirthday(){//本周员工生日
			$dao=D("User");
			$PERSON_COUNT=0;
			$birthday="";
			$WEEK_BEGIN=date("Y-m-d",(strtotime($this->CUR_DATE)-(date("w",strtotime($this->CUR_DATE)))*24*3600));
            $WEEK_END=date("Y-m-d",(strtotime($this->CUR_DATE)+(6-date("w",strtotime($this->CUR_DATE)))*24*3600));
             
			$bir=$dao->field('USER_NAME,BIRTHDAY')
			         ->order('USER_NAME ASC')
			         ->findall();
		    if($bir){
		    	foreach ($bir as $ROW){
		    	    $USER_NAME=$ROW["USER_NAME"];
					    $BIRTHDAY=$ROW["BIRTHDAY"];
					
					    $DATA=substr($this->CUR_DATE,0,4).substr($BIRTHDAY,4,6);
					    if($DATA< $WEEK_BEGIN || $DATA> $WEEK_END || $BIRTHDAY=="1900-01-01 00:00:00" || $BIRTHDAY=="0000-00-00 00:00:00")
					       continue;
					    $PERSON_STR.=$USER_NAME."(".date("m-d",strtotime($DATA)).")&nbsp&nbsp&nbsp&nbsp";
					    $PERSON_COUNT++;	
		    	}
		    	if ($PERSON_COUNT){
		    		
		    		$birthday=" <tr class=\"TableData\">
								  <td colspan=2>
								     <marquee style=\"color:#FF6600;\" behavior=scroll scrollamount=3 scrolldelay=120 onmouseover='this.stop()' onmouseout='this.start()' border=0>
								     本周生日：".$PERSON_STR."
								     </marquee>
								  </td>
								 </tr>";
		    	}
	    	
		    }
		    return $birthday;
	}
	
	protected function PerUrl(){
		$dao=D("Url");
		$per=$dao->where("USER='$this->LOGIN_USER_ID'")
		         ->order('URL_NO')
		         ->limit("0,10")
		         ->findall();
        return $per;   	
	}
	
	protected function ComUrl(){
		$dao=D("Url");
		$com=$dao->where("USER=''")
		         ->order('URL_NO')
		         ->limit("0,10")
		         ->findall();
		return $com;
	}
		
}
?>
