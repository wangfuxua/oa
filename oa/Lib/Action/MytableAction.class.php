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
		
$this->display();
}
	public function email(){
	     
	     $mails=$this->emailcount();
	     $this->assign("mails",$mails);
	     
		$this->display();
		
	}

	public function url(){
	     
	     $perurl=$this->PerUrl();
	     $comurl=$this->ComUrl();

		 $this->assign('perurl', $perurl);
		 $this->assign('comurl', $comurl);
	     
		$this->display();
		
	}
	
	public function notify()
	{
		//第一部分：公告展示
		$notify = new NotifyModel();
		$dept_id = 'D_' . $this->LOGIN_DEPT_ID;
		$notifylist = $notify->where("(InStr(TO_ID,'ALL_DEPT,') or InStr(TO_ID,'$dept_id') or TO_ID = '') AND BEGIN_DATE<=$this->CUR_TIME_INT")->order("BEGIN_DATE DESC")->limit("0,10")->findAll();
		$this->assign('notifylist',$notifylist);

		$this->display();
	}

	public function flow(){
		
		$flow = new ZworkFlowModel();
		$flowList = $flow->where("powerUser like '%$this->LOGIN_USER_ID%'")->findall();
		//print_r($flowList);
		$work = new ZworkWorkModel();
		foreach ($flowList as $k=>$v){
			$a=$work->where("zworkId=$v[zworkId] AND state=$v[flowId]")->limit("0,10")->find();
			//$a=$work->where("zworkId=$v[zworkId]")->limit("0,10")->find();
			if(!empty($a)){
				$workRow[$k]=$a;
				$workRow[$k][tag]=$v['tag'];
			}
		}
		//$workRows = $this->sortData($workRow,"grade",0);

		//$workList = array_slice($workRows,$p->firstRow,$p->listRows);
		$this->assign('workRow',$workRow);	
		$this->display();
	}

	public function news(){
		
		$news = new NewsModel();
		$newslist = $news->top5('','*','newId desc');
		$this->assign('newslist',$newslist);
		$this->display();
	}

	public function Calendar()
	{
	     //$this->assign("calendarlist",$this->calendarlist());
	     $this->assign("affairlist",$this->affairlist());
		 $this->display();
	}

	
	protected function emailcount(){
		$mails=array();
		$dao=D("Email");
		$mapnew="BOX_ID=0 and TO_ID='$this->_uid' and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG='0' and sentbox_flag=0";
		//$mapnew="BOX_ID=0 and TO_ID='$this->_uid' and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG='0'";//新邮件数量(收)
		//$mapnew="TO_ID='$this->LOGIN_USER_ID' and SEND_FLAG='1' and READ_FLAG='0' and DELETE_FLAG!='1'";
		$mails[NEW_LETER_COUNT]=$dao->count($mapnew);
		
		$mapin="BOX_ID=0 and TO_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG='0' and sentbox_flag=0";
		//$mapin="BOX_ID=0 and TO_ID='$this->_uid' and SEND_FLAG='1' and DELETE_FLAG='0'";//所有收件箱
		//$mapin="TO_ID='$this->LOGIN_USER_ID' and SEND_FLAG='1' and DELETE_FLAG!='1'";
		$mails[INBOX_COUNT]=$dao->count($mapin);

		$mapout="FROM_ID='$this->_uid' AND SEND_FLAG='0' AND DELETE_FLAG='0' and sentbox_flag=0";
		//$mapout="FROM_ID='$this->_uid' and SEND_FLAG='0'  AND DELETE_FLAG='0'";
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
		
	public function saveSet(){
		$dao=D("UserSetting");
		$row=$dao->where("userid='$this->LOGIN_USER_ID'")->find();
		if ($row) {
		   $dao->setField("setting",$_REQUEST[jsonstr],"userid='$this->LOGIN_USER_ID'");	
		}else {
		   //$data[uid]=$_SESSION[LOGIN_UID];
		   $data[userid]=$_SESSION[LOGIN_USER_ID];
		   //$data[jsonstr]=$_REQUEST[jsonstr];
		   $dao->add($data);	
		   $dao->setField("setting",$_REQUEST[jsonstr],"userid='$_SESSION[LOGIN_USER_ID]'");
		}
	}
	public function getSet(){
		$dao=D("UserSetting");
		$row=$dao->where("userid='$_SESSION[LOGIN_USER_ID]'")->find();
		echo $row[setting];
	}
}
?>
