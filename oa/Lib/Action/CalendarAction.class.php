<?php
/**
 * CalendarAction.class.php
 * 功能：日程安排
 * */
include_cache(APP_PATH."/Common/mytable.php");
class CalendarAction extends PublicAction {
	public 	function _initialize(){
		$this->curtitle="日程安排";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	/*----------------------入口程序显示日历表------------*/
	public function index(){//日历表
		$YEAR=$_REQUEST[YEAR];
		$MONTH=$_REQUEST[MONTH];
		$CUR_YEAR = date('Y',$this->CUR_TIME_INT);
		$CUR_MON = date('m',$this->CUR_TIME_INT);
		$CUR_DAY = date('d',$this->CUR_TIME_INT);
		$DATE=1;
		$DAY=1;
		if(!$_REQUEST[YEAR])
		   $YEAR = $CUR_YEAR;
		else
		   $YEAR=$_REQUEST[YEAR];
		if(!$_REQUEST[MONTH])
		   $MONTH = $CUR_MON;
		else 
		   $MONTH=$_REQUEST[MONTH];
		while (checkdate($MONTH,$DATE,$YEAR))
		  $DATE++;
		  //echo $DAY;
		$this->assign("YEAR",$YEAR);  
		$this->assign("MONTH",$MONTH);  
		$this->assign("DAY",$DAY);
		$this->assign("DATE",$DATE);
		$this->assign("CUR_YEAR",$CUR_YEAR);
		$this->assign("CUR_MON",$CUR_MON);
		$this->assign("CUR_DAY",$CUR_DAY);
         //=========显示日程安排 ==
		$dao=D("Calendar");
		$list=$dao->where("USER_ID='$this->LOGIN_USER_ID' and year(CAL_TIME)=$YEAR and month(CAL_TIME)=$MONTH")
		          ->order("CAL_TIME")
		          ->findall();
		if ($list) {
			foreach ($list as $ROW){
				        $CAL_ID=$ROW["CAL_ID"];
					    $CAL_TIME=$ROW["CAL_TIME"];
					    $END_TIME=$ROW["END_TIME"];
					    $CAL_TYPE=$ROW["CAL_TYPE"];
					    $CONTENT=$ROW["CONTENT"];
					    $CONTENT=str_replace("<","&lt",$CONTENT);
					    $CONTENT=str_replace(">","&gt",$CONTENT);
					    $CONTENT=stripslashes($CONTENT);
					    $CAL_DAY=strtok($CAL_TIME,"-");
					    $CAL_DAY=strtok("-");
					    $CAL_DAY=strtok(" ");
					    if(substr($CAL_DAY,0,1)=="0")
					       $CAL_DAY=substr($CAL_DAY,-1);
					       
					    $CAL_TIME=strtok($CAL_TIME," ");
					    $CAL_TIME=strtok(" ");
					    $CAL_TIME=substr($CAL_TIME,0,5);
					    $END_TIME=strtok($END_TIME," ");
					    $END_TIME=strtok(" ");
					    $END_TIME=substr($END_TIME,0,5);
					    switch($CAL_TYPE)
					    {
					     case "1":
					         $CAL_TYPE="工作";
					         break;
					     case "2":
					         $CAL_TYPE="个人";
					         break;
					    }
		
					    $CAL_ARRAY[$CAL_DAY].="$CAL_TIME-$END_TIME<br>".$CAL_TYPE.":<a href='#' onclick='javascript:my_note($CAL_ID);'>".csubstr($CONTENT,0,15)."</a><br>";
			}
		}
		//print_r($CAL_ARRAY);
		
		$this->assign("CAL_ARRAY",$CAL_ARRAY);

		//--------------本月员工生日
					 $CUR_MONTH=$MONTH;
					 $dao=D("Hrms");
					 $list=$dao->order("USER_ID ASC")->field("USER_ID,CARD_NO")->findall();

					 if ($list) {
						foreach ($list as $ROW) {
							if (strlen($ROW["CARD_NO"]) == 15){
								$MON = substr($ROW["CARD_NO"], 8, 2);
								if ($MON != $CUR_MONTH) continue;
								$DAY = substr($ROW["CARD_NO"], 10, 2);
								$user_id .= "'" . $ROW["USER_ID"] . "',";
								$list_bth[$ROW["USER_ID"]] = $DAY;
							}
							else if(strlen($ROW["CARD_NO"]) == 18) {
								$MON = substr($ROW["CARD_NO"], 10, 2);		
								if ($MON != $CUR_MONTH) continue;
								$DAY = substr($ROW["CARD_NO"], 12, 2);	
								$user_id .= "'" . $ROW["USER_ID"]. "',";
								$list_bth[$ROW["USER_ID"]] = $DAY;	
							}							
						}
					 }

					 $user_id = substr($user_id, 0, strlen($user_id)-1); 
					 $dao1=D("User");
					 $sql = "select USER_NAME, USER_ID from __TABLE__ where USER_ID in (" . $user_id . ")";

					 $list_user = $dao1->query($sql);  
					 foreach ($list_user as $user) {
						$PERSON_COUNT++;
						$user_bth[$user['USER_NAME']]=$list_bth[$user['USER_ID']];
					 }

					 asort($user_bth);
					 foreach ($user_bth as $key=>$value) {
						$PERSON_STR.=$key."(" . $CUR_MONTH . "-" .$value .")&nbsp&nbsp&nbsp&nbsp";
					 }

             $this->assign('PERSON_COUNT',$PERSON_COUNT);	
             $this->assign('PERSON_STR',$PERSON_STR);
		
			 $this->display();
	}
	/*------------------------管理日程安排----------------------*/
	public function calmanage(){//
		$YEAR=$_REQUEST[YEAR];
		$MONTH=$_REQUEST[MONTH];
		$DAY=$_REQUEST[DAY];
		$dao=D("Calendar");
		
		$map="USER_ID='$this->LOGIN_USER_ID' and TO_DAYS(CAL_TIME)=TO_DAYS('$YEAR-$MONTH-$DAY')";
	    $count=$dao->count($map);
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '';
		}
		$p= new Page($count,$listRows);
        $list=$dao->where($map)
                  ->order("CAL_TIME")
                  ->limit("$p->firstRow,$p->listRows")
                  ->findall();
		$page       = $p->show();
		$this->assign("page",$page);
        $this->assign("list",$list);
        $this->assign("YEAR",$YEAR);  
		$this->assign("MONTH",$MONTH);  
		$this->assign("DAY",$DAY);
		$this->assign("CUR_TIME",date("Y-m-d H:i:s",$this->CUR_TIME_INT));
		$this->display();
	}
	
	/*--------------------添加或修改日程安排--表单---------------*/
	public function calform(){
		$CAL_ID=$_REQUEST[CAL_ID];
		$YEAR=$_REQUEST[YEAR];
		$MONTH=$_REQUEST[MONTH];
		$DAY=$_REQUEST[DAY];
		if ($CAL_ID) {//修改
			$dao=D("Calendar");
			$row=$dao->where("CAL_ID=$CAL_ID")->find();
			$desc="修改事务";
		}else {//添加
			if (empty($YEAR)||empty($MONTH)||empty($DAY)){
				$this->error("请选择时间");
			}
			$row[CAL_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
			$row[END_TIME]=date("Y-m-d H:i:s",$this->CUR_TIME_INT+60*60);
			$desc="添加事务";
		}
		  $CAL_TIME=explode(" ",$row[CAL_TIME]);
		  $CAL_TIME_ARRAY=explode(":",$CAL_TIME[1]);
		  $CAL_TIME_HOUR=$CAL_TIME_ARRAY[0];
		  $CAL_TIME_MIN=$CAL_TIME_ARRAY[1];
		  $END_TIME=explode(" ",$row[END_TIME]);
		  $END_TIME_ARRAY=explode(":",$END_TIME[1]);
		  $END_TIME_HOUR=$END_TIME_ARRAY[0];
		  $END_TIME_MIN=$END_TIME_ARRAY[1];
		        for($I=0;$I<=23;$I++){
			          if($I<10)
			             $I="0".$I;
			           if ($CAL_TIME_HOUR==$I)  
			              $cal_hour.="<option value=$I selected>$I</option>";
			           else   
			              $cal_hour.="<option value=$I>$I</option>";
                 }
		        for($I=0;$I<=59;$I++){
			          if($I<10)
			             $I="0".$I;
			           if ($CAL_TIME_MIN==$I)  
			              $cal_min.="<option value=$I selected>$I</option>";
			           else   
			              $cal_min.="<option value=$I>$I</option>";
                 }

		        for($I=0;$I<=23;$I++){
			          if($I<10)
			             $I="0".$I;
			           if ($END_TIME_HOUR==$I)  
			              $end_hour.="<option value=$I selected>$I</option>";
			           else   
			              $end_hour.="<option value=$I>$I</option>";
                 }
		        for($I=0;$I<=59;$I++){
			          if($I<10)
			             $I="0".$I;
			           if ($END_TIME_MIN==$I)  
			              $end_min.="<option value=$I selected>$I</option>";
			           else   
			              $end_min.="<option value=$I>$I</option>";
                 }
        $this->assign("CAL_ID",$CAL_ID);  
        $this->assign("YEAR",$YEAR);  
		$this->assign("MONTH",$MONTH);  
		$this->assign("DAY",$DAY);
		$this->assign("cal_hour",$cal_hour);
		$this->assign("cal_min",$cal_min);
		$this->assign("end_hour",$end_hour);
		$this->assign("end_min",$end_min);
		$this->assign("desc",$desc);		
        $this->assign("row",$row);
		$this->display();
	}
	/*-----------------------添加或修改日程安排--------------------*/
	public function calformsubmit(){
		   $CAL_ID=$_REQUEST[CAL_ID];
		   if($_POST[END_HOUR]<$_POST[CAL_HOUR] || ($_POST[END_HOUR]==$_POST[CAL_HOUR] && $_POST[END_MIN]<$_POST[CAL_MIN])){
				$this->error("开始时间晚于结束时间！");
			}
			$_POST[CAL_TIME]=$_POST[CAL_YEAR]."-".$_POST[CAL_MON]."-".$_POST[CAL_DAY]." ".$_POST[CAL_HOUR].":".$_POST[CAL_MIN].":00";
			$_POST[END_TIME]=$_POST[CAL_YEAR]."-".$_POST[CAL_MON]."-".$_POST[CAL_DAY]." ".$_POST[END_HOUR].":".$_POST[END_MIN].":00";
			$_POST[USER_ID]=$this->LOGIN_USER_ID;
			$dao=D("Calendar");
			$dao->create();
			if ($CAL_ID) {
				     $daos=new Model(); 
				     $row=$daos->where("CAL_ID='$CAL_ID'")->find();
				     $SMS_CONTENT1="请查看日程安排！\n内容：".csubstr($row[CONTENT],0,100);//跟添加时的规则要一致才可以
				     /*----------修改---------------*/
				     $dao->where("CAL_ID='$CAL_ID'")->save();
					/*---------- 短信提醒 ----------*/
					 $daosms=D("Sms");
					 $daosms->where("FROM_ID='$this->LOGIN_USER_ID' and SEND_TIME='$row[CAL_TIME]' and CONTENT='$SMS_CONTENT1' and SMS_TYPE='5'")
					        ->delete();//删除旧的提醒
					 if ($_POST[SMS_REMIND]=="on"){       
						 $data[SEND_TIME]=$_POST[CAL_TIME];
						 $data[CONTENT]="请查看日程安排！\n内容：".csubstr($_POST[CONTENT],0,100);
				         $data[FROM_ID]=$this->LOGIN_USER_ID;
				         $data[TO_ID]=$this->LOGIN_USER_ID;
				         $data[SMS_TYPE]="5";
				         $data[REMIND_FLAG]=1; 		 
						 $daosms->create();
						 $daosms->add($data);
					 }
				
				$this->assign("jumpUrl",__URL__."/calmanage/YEAR/".$_POST[CAL_YEAR]."/MONTH/".$_POST[CAL_MON]."/DAY/".$_POST[CAL_DAY]);
				$this->success("成功修改");
			}else {
				$dao->add();
					/*---------- 短信提醒 ----------*/
					if ($_POST[SMS_REMIND]=="on"){
					 $daosms=D("Sms");		 
					 $data[SEND_TIME]=$_POST[CAL_TIME];
					 $data[CONTENT]="请查看日程安排！\n内容：".csubstr($_POST[CONTENT],0,100);
			         $data[FROM_ID]=$this->LOGIN_USER_ID;
			         $data[TO_ID]=$this->LOGIN_USER_ID;
			         $data[SMS_TYPE]="5";
			         $data[REMIND_FLAG]=1; 		 
					 $daosms->create();
					 $daosms->add($data);
					}
                $this->assign("jumpUrl",__URL__."/calmanage/YEAR/".$_POST[CAL_YEAR]."/MONTH/".$_POST[CAL_MON]."/DAY/".$_POST[CAL_DAY]);								$this->success("成功添加");
			}
	}
	/*----------------------删除日程安排-------------------*/
	public function caldelete(){
		$CAL_ID=$_REQUEST[CAL_ID];
		$YEAR=$_REQUEST[YEAR];
		$MONTH=$_REQUEST[MONTH];
		$DAY=$_REQUEST[DAY];
		$dao=D("Calendar");
		$row=$dao->where("CAL_ID='$CAL_ID'")->find();
		$SMS_CONTENT1="请查看日程安排！\n内容：".csubstr($row[CONTENT],0,100);//跟添加时的规则要一致才可以
		$dao->where("CAL_ID='$CAL_ID'")->delete();
		/*---------- 删除短信提醒 ----------*/
		$daosms=D("Sms");
		$daosms->where("FROM_ID='$this->LOGIN_USER_ID' and SEND_TIME='$row[CAL_TIME]' and CONTENT='$SMS_CONTENT1' and SMS_TYPE='5'")
		       ->delete();//删除旧的提醒
		$this->redirect("/calmanage/YEAR/$YEAR/MONTH/$MONTH/DAY/$DAY","Calendar"); 
		//$this->success("成功删除");
	}
	
	public function note(){
		$CAL_ID=$_REQUEST[CAL_ID];
		$dao=D("Calendar");
		$row=$dao->where("CAL_ID='$CAL_ID'")->find();
		$this->assign("row",$row);
		$this->display();
	}
/*---------------------------------------日常事务管理 --------------------------*/	
    public function affairIndex(){
    	$dao=D("Affair");
	    $map="USER_ID='$this->LOGIN_USER_ID'";  
	    $count=$dao->count($map);
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '';
		}
		$p= new Page($count,$listRows);
        $list=$dao->where($map)
                  ->order("BEGIN_TIME desc")
                  ->limit("$p->firstRow,$p->listRows")
                  ->findall();
		$page       = $p->show();
		$this->assign("page",$page);
        $this->assign("list",$list);
    	$this->display();
    }
    public function affairform(){//
    	     $AFF_ID=$_REQUEST[AFF_ID];
			 $CUR_DATE_TIME=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
			 $CUR_DATE=date("Y-m-d",$this->CUR_TIME_INT);
			 $CUR_TIME=date("H:i:s",$this->CUR_TIME_INT);
			     	    
    		if ($AFF_ID) {//修改
				$dao=D("Affair");
				$row=$dao->where("AFF_ID=$AFF_ID AND USER_ID='$this->LOGIN_USER_ID'")->find();
				    if($row[TYPE]=="5")
				    {
				       $REMIND_ARR=explode("-",$row[REMIND_DATE]);
				       $row[REMIND_DATE_MON]=$REMIND_ARR[0];
				       $row[REMIND_DATE_DAY]=$REMIND_ARR[1];
				    }
				 $desc="修改事务";   
    		}else{//新建
                 $desc="添加事务";     		    
    			 $row[BEGIN_TIME]=$CUR_DATE_TIME;
    			 $row[TYPE]=2;//默认按日提醒;
    			 $row[REMIND_TIME]=$CUR_TIME;
    		}
    		
    		$this->assign("CUR_DATE_TIME",$CUR_DATE_TIME);
    		$this->assign("CUR_DATE",$CUR_DATE);
    		$this->assign("CUR_TIME",$CUR_TIME);
    		$this->assign("desc",$desc);
    		$this->assign("AFF_ID",$AFF_ID);
    		
    		$this->assign("row",$row);
    	    $this->display();
    }
    public function affairsubmit(){
    	        $AFF_ID=$_REQUEST[AFF_ID];
				$dao=D("Affair");
    	        $CUR_DATE_TIME=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
				$CUR_DATE=date("Y-m-d",$this->CUR_TIME_INT);
				$CUR_TIME=date("H:i:s",$this->CUR_TIME_INT);
				if($_POST[BEGIN_TIME]!=""){
					  if (!is_date_time($_POST[BEGIN_TIME])) {
					    $this->error("起始时间应为日期时间型，如：1999-01-01 10:20:10");	
					  }
				}
				else
				      $_POST[BEGIN_TIME]=$CUR_DATE_TIME;
				$DATE_VAR="REMIND_DATE".$_POST[TYPE];
				$TIME_VAR="REMIND_TIME".$_POST[TYPE];
				$_POST[REMIND_DATE]=$_POST[$DATE_VAR];
				$_POST[REMIND_TIME]=$_POST[$TIME_VAR];				   
                if($_POST[TYPE]=="5")
				   $_POST[REMIND_DATE]=$REMIND_DATE5_MON."-".$REMIND_DATE5_DAY;
				if($_POST[REMIND_TIME]!=""){
					  if (!is_time_affair($_POST[REMIND_TIME])) {
					  $this->error("提醒时间应为时间型，如：10:20:10");	
					  }
				}else{
				      $_POST[REMIND_TIME]=$CUR_TIME;
				}
    	if (empty($AFF_ID)) {//添加
				$_POST[USER_ID]=$this->LOGIN_USER_ID;
				$dao->create();    		
    		    $dao->add();
    		    $this->redirect("affairIndex","Calendar");
    	}else{//修改
				$dao->create();    		
    		    $dao->where("AFF_ID='$AFF_ID'")->save();
    		    $this->redirect("affairIndex","Calendar");
    	}
    }
	public function affairdelete(){
		$AFF_ID=$_REQUEST[AFF_ID];
		$dao=D("Affair");
		$dao->where("AFF_ID='$AFF_ID'")->delete();
		$this->redirect("affairIndex","Calendar");
	}
	public function affairnote(){
		$AFF_ID=$_REQUEST[AFF_ID];
		$dao=D("Affair");
		$row=$dao->where("AFF_ID='$AFF_ID'")->find();
    if($row[TYPE]=="2")
       $row[AFF_TIME]="每日 ".$row[REMIND_TIME];
    elseif($row[TYPE]=="3")
    {
       if($row[REMIND_DATE]=="1")
          $REMIND_DATE="一";
       elseif($row[REMIND_DATE]=="2")
          $REMIND_DATE="二";
       elseif($row[REMIND_DATE]=="3")
          $REMIND_DATE="三";
       elseif($row[REMIND_DATE]=="4")
          $REMIND_DATE="四";
       elseif($row[REMIND_DATE]=="5")
          $REMIND_DATE="五";
       elseif($row[REMIND_DATE]=="6")
          $REMIND_DATE="六";
       elseif($row[REMIND_DATE]=="0")
          $REMIND_DATE="日";
       $row[AFF_TIME]="每周".$REMIND_DATE." ".$row[REMIND_TIME];
    }
    elseif($row[TYPE]=="4")
       $row[AFF_TIME]="每月".$REMIND_DATE."日 ".$row[REMIND_TIME];
    elseif($row[TYPE]=="5")
       $row[AFF_TIME]="每年".str_replace("-","月",$REMIND_DATE)."日 ".$row[REMIND_TIME];
	    $row[CONTENT]=str_replace("<","&lt",$row[CONTENT]);
	    $row[CONTENT]=str_replace(">","&gt",$row[CONTENT]);
	    $row[CONTENT]=stripslashes($row[CONTENT]);
		$this->assign("row",$row);
		$this->display();
	}
    /*---------------------员工日程查询-----------------*/
	public function info(){
        $YEAR=$_REQUEST[YEAR];
        $MONTH=$_REQUEST[MONTH];		
		$CUR_YEAR = date('Y',$this->CUR_TIME_INT);
		$CUR_MON = date('m',$this->CUR_TIME_INT);
		$CUR_DAY = date('d',$this->CUR_TIME_INT);
		$DATE=1;
		$DAY=1;
		if(!$YEAR)
		   $YEAR = $CUR_YEAR;
		if(!$MONTH)
		   $MONTH = $CUR_MON;
		while (checkdate($MONTH,$DATE,$YEAR))
		  $DATE++;
		$dao=D("User");
		$row=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$POST_PRIV=$ROW["POST_PRIV"];
        $POST_DEPT=$ROW["POST_DEPT"];  
        $this->assign("POST_DEPT",$POST_DEPT);
        $this->assign("POST_PRIV",$POST_PRIV); 
        $this->assign("DEPT_ID",$this->LOGIN_DEPT_ID);
		$this->assign("YEAR",$YEAR);  
		$this->assign("MONTH",$MONTH);  
		$this->assign("DAY",$DAY);
		$this->assign("DATE",$DATE);
		$this->assign("CUR_YEAR",$CUR_YEAR);
		$this->assign("CUR_MON",$CUR_MON);
		$this->assign("CUR_DAY",$CUR_DAY);
		$this->display();
	}
	public function infoblank(){
		$this->display();
	}
	public function infolist(){
		$DEPT_ID=$_REQUEST[DEPT_ID];
        $YEAR=$_REQUEST[YEAR];
        $MONTH=$_REQUEST[MONTH];	
        $BEGIN_DAY=$_REQUEST[BEGIN_DAY];
        $END_DAY=$_REQUEST[END_DAY];
        ####头
        for($DAY=$BEGIN_DAY;$DAY<=$END_DAY;$DAY++)
		{
		  $WEEK=date("w",mktime(0,0,0,$MONTH,$DAY,$YEAR));
		  switch($WEEK)
		  {
		    case 0:$WEEK_DESC="日";
		           break;
		    case 1:$WEEK_DESC="一";
		           break;
		    case 2:$WEEK_DESC="二";
		           break;
		    case 3:$WEEK_DESC="三";
		           break;
		    case 4:$WEEK_DESC="四";
		           break;
		    case 5:$WEEK_DESC="五";
		           break;
		    case 6:$WEEK_DESC="六";
		           break;
		  }
		  $headlist[$DAY][WEEK_DESC]=$WEEK_DESC;
		  $headlist[$DAY][DAY]=$DAY;
		}
		$this->assign("headlist",$headlist);
		###逐人逐日显示日程安排
		$CUR_YEAR = date('Y',$this->CUR_TIME_INT);
		$CUR_MON = date('m',$this->CUR_TIME_INT);
		$CUR_DAY = date('d',$this->CUR_TIME_INT);
		$dao=new Model();
		$DEPT_IDS=getSubDepts($DEPT_ID);
		$lists=$dao->table("user,user_priv")
		           ->where("user.USER_PRIV=user_priv.USER_PRIV and DEPT_ID in ($DEPT_IDS$DEPT_ID)")
		           ->order("PRIV_NO")
		           ->findAll(); 
		$dao=D("Calendar");
		if ($lists) {
			foreach ($lists as $key=>$user){
				  $USER_ID=$user["USER_ID"];
                  $list[$key][USER_NAME]=$user["USER_NAME"];
                  for($DAY=$BEGIN_DAY;$DAY<=$END_DAY;$DAY++)
					{
                        $map="USER_ID='$USER_ID' and to_days(CAL_TIME)=to_days('$YEAR-$MONTH-$DAY')";
					    $clist=$dao->where($map)->order("CAL_TIME")->findall();
					    $array[$DAY][sub]=$clist; 
					    $list[$key][day]=$array;
					}
			}
		}
		$this->assign("YEAR",$YEAR);  
		$this->assign("MONTH",$MONTH);  
		$this->assign("DAY",$DAY);
		$this->assign("DATE",$DATE);
		$this->assign("CUR_YEAR",$CUR_YEAR);
		$this->assign("CUR_MON",$CUR_MON);
		$this->assign("CUR_DAY",$CUR_DAY);
		$this->assign("list",$list);
		$this->display();
	}
}
?>