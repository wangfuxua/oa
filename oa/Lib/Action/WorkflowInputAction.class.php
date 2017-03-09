<?php
class WorkflowInputAction extends WorkflowAction{
	protected function findId($STRING,$ID) {
		$MY_ARRAY=explode(",",$STRING);
   		$ARRAY_COUNT=sizeof($MY_ARRAY);
   		if($MY_ARRAY[$ARRAY_COUNT-1]=="")$ARRAY_COUNT--;
   		for($I=0;$I<$ARRAY_COUNT;$I++){ 
   			if(strcmp($MY_ARRAY[$I],$ID)==0||$MY_ARRAY[$I]==$ID)return true;
   		}
   		return false;
	}
	
	protected function deptLongName($DEPT_ID) {
		$department = new DepartmentModel();
		$department = $department->where('DEPT_ID='.$DEPT_ID)->findall();
		foreach ($department as $k=>$v) {
			$DEPT_NAME=$v["DEPT_NAME"];
     		$DEPT_PARENT=$v["DEPT_PARENT"];
			if($DEPT_PARENT == 0)
				return $DEPT_NAME;
			else 
				return dept_long_name($DEPT_PARENT)."/".$DEPT_NAME;
		}
	} 
	public function index(){
		$list = $_GET;
		$this->assign('list',$list);
		//时间变量
		$CUR_TIME1 = date("H:i:s",time());
		$CUR_DATE = date("Y-m-d");
		$CUR_TIME = $CUR_DATE." ".$CUR_TIME1;
		$browser = $_GET;
		$this->assign('browser',$browser);
//写处理标记为已接收，写处理时间
		$flowRunPrcs = new FlowRunPrcsModel();
		$a = array('USER_ID'=>$this->LOGIN_USER_ID,'RUN_ID'=>$_GET['RUN_ID'],'PRCS_ID'=>$_GET['PRCS_ID']);
		$list = $flowRunPrcs->where($a)->find();
		if($list['PRCS_FLAG'] = 1){
			$data=array('PRCS_FLAG'=>2,'PRCS_TIME'=>time());
			$flowRunPrcs->save($data,$a);
		}
//???????????????
		$data2 =array('PRCS_FLAG'=>4);
		$a2 = array('RUN_ID'=>$_GET['RUN_ID'],'PRCS_ID'=>$_GET['PRCS_ID']-1);
		$flowRunPrcs->save($data2,$a2);

		$flowType = new FlowTypeModel();
		$flowType = $flowType ->where('FLOW_ID='.$_GET['FLOW_ID'])->find();
			
		if($flowType['FLOW_TYPE']==1){
			$flowProcess = new FlowProcessModel();
			$flowProcess = $flowProcess->where(array('FLOW_ID'=>$_GET['FLOW_ID'],'PRCS_ID'=>$_GET['FLOW_PRCS']))->findall();
			foreach ($flowProcess as $k=>$v){
				$PRCS_ITEM=$v['PRCS_ITEM'];
			}
		}
///////////////////////////////////////////////////////////////
//取模板
		$flowFormType = new FlowFormTypeModel();
		$flowFormType = $flowFormType->where('FORM_ID='.$flowType['FORM_ID'])->find();
//取文号，附件信息
		$flowRun = new FlowRunModel();
		$flowRun = $flowRun->where('RUN_ID='.$_GET['RUN_ID'])->find();

 //取表单数据
 		$flowRunData = new FlowRunDataModel();
 		$flowRunData = $flowRunData->where(array('RUN_ID'=>$browser['RUN_ID']))->findall();
 		foreach($flowRunData as $k=>$v){
 			
 		}
		$this->assign('ITEM_ID',$v['ITEM_ID']);
//取步骤1
		$flowRunPrcs1 = $flowRunPrcs->where(array('RUN_ID'=>$_GET['RUN_ID'],'PRCS_ID'=>1))->find();
		$flowRunPrcs1['PRCS_TIME'] = date('Y-m-d',$flowRunPrcs1['PRCS_TIME']);
//取表单模板
		$PRINT_MODEL=str_replace("#[表单]","<b>".$flowFormType['FORM_NAME']."</b>",$flowFormType['PRINT_MODEL']);	
		$PRINT_MODEL=str_replace("#[文号]",$flowRun['RUN_NAME'],$PRINT_MODEL);
		$PRINT_MODEL=str_replace("#[时间]","日期：".$flowRunPrcs1['PRCS_TIME'],$PRINT_MODEL);
//Html 智能分析
		$ELEMENT_ARRAY= $this->htmlElement($PRINT_MODEL);

		$ITEM_ID_MAX=sizeof($ELEMENT_ARRAY);
		$ITEM_ID=0;
		for($I=0;$I<$ITEM_ID_MAX;$I++){
  			$ELEMENT=$ELEMENT_ARRAY[$I];
  			$ENAME=$this->get_attr($ELEMENT,"NAME");
  			$EVALUE=$this->get_attr($ELEMENT,"VALUE");
  			$EITLE=$this->get_attr($ELEMENT,"TITLE");
  			$ECLASS=$this->get_attr($ELEMENT,"CLASS");

//echo $ENAME."-".$EITLE."-".$EVALUE."<br>";

//--- 加入控件名称 ---

			if($ECLASS!="DATE"){
				$ITEM_ID++;
				$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME name='DATA_$ITEM_ID'",$ELEMENT);
				
			}

//--- 赋值 ----
			$STR="DATA_".$ITEM_ID;
			$ITEM_VALUE=$$STR;
			
			if($ITEM_VALUE=="{宏控件}")
				$ITEM_VALUE="";

			if($ENAME=="INPUT"){
				if(!strstr($ELEMENT_OUT,"type=checkbox")) { //textfield
					$ELEMENT_OUT=str_replace("value=$EVALUE","",$ELEMENT_OUT);
					$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME value='$ITEM_VALUE'",$ELEMENT_OUT);
				} else {
					$ELEMENT_OUT=str_replace(" CHECKED","",$ELEMENT_OUT);		
					if($ITEM_VALUE=="on")
					$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME CHECKED",$ELEMENT_OUT);
				}
			} else if ($ENAME=="TEXTAREA") {
				$ELEMENT_OUT=str_replace(">$EVALUE<",">$ITEM_VALUE<",$ELEMENT_OUT);
			} else if ($ENAME=="SELECT" && $ITEM_VALUE!="" && $ECLASS!="AUTO") {
				$ELEMENT_OUT=str_replace(" selected","",$ELEMENT_OUT);
				$ELEMENT_OUT=str_replace("<OPTION value=$ITEM_VALUE>","<OPTION selected value=$ITEM_VALUE>",$ELEMENT_OUT);
			}

//--- 特殊控件 ---
			if($ECLASS=="DATE") {//日期控件
     			$ELEMENT_OUT="<IMG class=DATE align=absmiddle title=日期控件：$EVALUE style=\"CURSOR: hand\" src=\"/images/menu/calendar.gif\" border=0 onclick=\"td_calendar('$EVALUE')\">";
			} elseif($ECLASS=="CALC") { //计算控件
     			$K=0;
     			for($J=0;$J<$ITEM_ID_MAX;$J++) {
					$ELEMENT1=$ELEMENT_ARRAY[$J];
       				$ETITLE1=$this->get_attr($ELEMENT1,"TITLE");
					$ECLASS1=$this->get_attr($ELEMENT1,"CLASS");

       				if($ECLASS1!="DATE")
          				$K++;
       				else
          				continue;

       				$ETITLE_ARRAY[$K]=$ETITLE1;
     			}

				$EVALUE1=$EVALUE."#";

				$POS=0;
				$ECALC1="";
				$ECALC2="";
				while($POS<strlen($EVALUE1)) {
					$STR=substr($EVALUE1,$POS,1);
					$POS++;

					if($STR=="+"||$STR=="-"||$STR=="*"||$STR=="/"||$STR=="("||$STR==")"||$STR=="^"||$STR=="#") {
						if($ECALC2!="") {

							$ARRAY_COUNT=sizeof($ETITLE_ARRAY);
								if($ETITLE_ARRAY[$ARRAY_COUNT-1]=="")$ARRAY_COUNT--;
									for($K=0;$K<$ARRAY_COUNT;$K++) {
										if($ETITLE_ARRAY[$K]==$ECALC2) {
											$ECALC1.="parseFloat(document.form1.DATA_".$K.".value)";
											$ECALC2="";
											break;
										}
									}
						}

						if($STR!="#")
							$ECALC1.=$STR;
					} else
						$ECALC2.=$STR;
				}

				$ELEMENT_OUT.="<IMG title=计算 align=absmiddle style=\"CURSOR: hand\" src=\"/images/form/calc.gif\" border=0 onclick=\"calc_$ITEM_ID()\">";
				$ELEMENT_OUT.="<script>
					function calc_$ITEM_ID() {
						myvalue=$ECALC1;
						if(!isNaN(myvalue))
							document.form1.DATA_$ITEM_ID.value=Math.round(myvalue * 100)/100;
						else
							document.form1.DATA_$ITEM_ID.value=\"\";
						setTimeout(\"calc_$ITEM_ID()\",1000);
					}
					calc_$ITEM_ID();
					</script>";
			} elseif($ECLASS=="AUTO") {//宏控件
				$EDATAFLD=$this->get_attr($ELEMENT,"DATAFLD");
				if($ENAME=="INPUT" && $ITEM_VALUE=="") {// 单行输入框，无值时时将自动取值
					switch($EDATAFLD) {
						case "SYS_DATE":
                         	$AUTO_VALUE=$CUR_DATE;
                         break;
						case "SYS_TIME":
                         	$AUTO_VALUE=$CUR_TIME1;
                         break;
           				case "SYS_DATETIME":
                         	$AUTO_VALUE=$CUR_TIME;
                         break;
           				case "SYS_USERID":
                         	$AUTO_VALUE=$this->LOGIN_USER_ID;
                         break;
           				case "SYS_USERNAME":
           					$user = new UserModel();
           					$user = $user->where('USER_ID='.$this->LOGIN_USER_ID)->find();
           					$AUTO_VALUE = $user['USER_NAME'];
                         break;
						case "SYS_DEPTNAME":
							$AUTO_VALUE=$this->deptLongName($this->LOGIN_DEPT_ID);
                         break;
           				case "SYS_FORMNAME":
                         	$AUTO_VALUE=$flowFormType['FORM_NAME'];
                         break;
           				case "SYS_RUNNAME":
                         	$AUTO_VALUE=$flowRun['RUN_NAME'];
                         break;
           				case "SYS_RUNDATE":
                         	$AUTO_VALUE=$flowRunPrcs1['PRCS_TIME'];
                         break;
           				case "SYS_RUNDATETIME":
                         	$AUTO_VALUE=$flowRunPrcs1['PRCS_TIME'];
                         break;
						case "SYS_SQL":
							$EDATASRC=$this->get_attr($ELEMENT,"DATASRC");
							$EDATASRC=str_replace("`","'",$EDATASRC);
							$cursor_SYS_SQL = exequery($connection,$EDATASRC);
							if($ROW=mysql_fetch_array($cursor_SYS_SQL))
								$AUTO_VALUE=$ROW[0];
						break;
					}

					$ELEMENT_OUT=str_replace("value=$EVALUE","",$ELEMENT_OUT);
					$ELEMENT_OUT=str_replace("value=''","",$ELEMENT_OUT);
					$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME value='$AUTO_VALUE'",$ELEMENT_OUT);
				} elseif($ENAME=="SELECT") {
					$AUTO_VALUE="<option value=\"\"";
					if($ITEM_VALUE=="")
						$AUTO_VALUE.=" selected";
						$AUTO_VALUE.="></option>\n";

        			$ITEM_VALUE_TEXT="";
        			switch($EDATAFLD)
        			{
			           case "SYS_LIST_DEPT":
			                         $AUTO_VALUE.=my_dept_tree(0,$ITEM_VALUE,0);
			                         if($ITEM_VALUE!="")
			                         {
			                            $query_auto="SELECT * from DEPARTMENT where DEPT_ID=$ITEM_VALUE";
			                            $cursor_auto = exequery($connection,$query_auto);
			                            if($ROW=mysql_fetch_array($cursor_auto))
			                               $ITEM_VALUE_TEXT=$ROW["DEPT_NAME"];
			                         }
			                         break;
			           case "SYS_LIST_USER":
			                         $query_auto="SELECT * from USER,USER_PRIV where USER.USER_PRIV=USER_PRIV.USER_PRIV order by PRIV_NO,USER_NAME";
			                         $cursor_auto = exequery($connection,$query_auto);
			                         while($ROW=mysql_fetch_array($cursor_auto))
			                         {
			                            $USER_ID=$ROW["USER_ID"];
			                            $USER_NAME=$ROW["USER_NAME"];
			                            $AUTO_VALUE.="<option value=\"$USER_ID\"";
			                            if($ITEM_VALUE==$USER_ID)
			                            {
			                               $AUTO_VALUE.=" selected";
			                               $ITEM_VALUE_TEXT=$USER_NAME;
			                            }
			                            $AUTO_VALUE.=">$USER_NAME</option>\n";
			                         }
			                         break;
			           case "SYS_LIST_PRIV":
			                         $query_auto="SELECT * from USER_PRIV order by PRIV_NO";
			                         $cursor_auto = exequery($connection,$query_auto);
			                         while($ROW=mysql_fetch_array($cursor_auto))
			                         {
			                            $USER_PRIV=$ROW["USER_PRIV"];
			                            $PRIV_NAME=$ROW["PRIV_NAME"];
			                            $AUTO_VALUE.="<option value=\"$USER_PRIV\"";
			                            if($ITEM_VALUE==$USER_PRIV)
			                            {
			                               $AUTO_VALUE.=" selected";
			                               $ITEM_VALUE_TEXT=$PRIV_NAME;
			                            }
			                            $AUTO_VALUE.=">$PRIV_NAME</option>\n";
			                         }
			                         break;
			           case "SYS_LIST_PRCSUSER1":
			                         $query_auto = "select * from FLOW_PROCESS where FLOW_ID=$FLOW_ID order by PRCS_ID";
			                         $cursor_auto=exequery($connection,$query_auto);
			                         $PRCS_USER="";
			                         while($ROW=mysql_fetch_array($cursor_auto))
			                            $PRCS_USER.=$ROW["PRCS_USER"];
			
			                         $query_auto = "SELECT * from USER,USER_PRIV where USER.USER_PRIV=USER_PRIV.USER_PRIV order by PRIV_NO,USER_NAME";
			                         $cursor_auto=exequery($connection,$query_auto);
			                         while($ROW=mysql_fetch_array($cursor_auto))
			                         {
			                           $USER_ID=$ROW["USER_ID"];
			                           $USER_NAME=$ROW["USER_NAME"];
			                           if(find_id($PRCS_USER,$USER_ID))
			                           {
			                               $AUTO_VALUE.="<option value=\"$USER_ID\"";
			                               if($ITEM_VALUE==$USER_ID)
			                               {
			                                  $AUTO_VALUE.=" selected";
			                                  $ITEM_VALUE_TEXT=$USER_NAME;
			                               }
			                               $AUTO_VALUE.=">$USER_NAME</option>\n";
			                           }
			                         }
			                         break;
									case "SYS_LIST_PRCSUSER2":
										$query_auto = "select * from FLOW_PROCESS where FLOW_ID=$FLOW_ID and PRCS_ID=$FLOW_PRCS";
										$cursor_auto=exequery($connection,$query_auto);
										if($ROW=mysql_fetch_array($cursor_auto))
											$PRCS_USER=$ROW["PRCS_USER"];
			
										$query_auto = "SELECT * from USER,USER_PRIV where USER.USER_PRIV=USER_PRIV.USER_PRIV order by PRIV_NO,USER_NAME";
										$cursor_auto=exequery($connection,$query_auto);
										while($ROW=mysql_fetch_array($cursor_auto)) {
											$USER_ID=$ROW["USER_ID"];
											$USER_NAME=$ROW["USER_NAME"];
											if(find_id($PRCS_USER,$USER_ID)||($ITEM_VALUE==$USER_ID)) {
												$AUTO_VALUE.="<option value=\"$USER_ID\"";
												if($ITEM_VALUE==$USER_ID) {
													$AUTO_VALUE.=" selected";
													$ITEM_VALUE_TEXT=$USER_NAME;
												}
												$AUTO_VALUE.=">$USER_NAME</option>\n";
											}
										}	
										break;
									case "SYS_LIST_SQL": 
										$EDATASRC=str_replace("`","'",$EDATASRC);
										$cursor_SYS_SQL = exequery($connection,$EDATASRC);
										while($ROW=mysql_fetch_array($cursor_SYS_SQL))
										{
											$AUTO_VALUE_SQL=$ROW[0];
											$AUTO_VALUE.="<option value=\"$AUTO_VALUE_SQL\"";
											if($ITEM_VALUE==$AUTO_VALUE_SQL)
											{
												$AUTO_VALUE.=" selected";
												$ITEM_VALUE_TEXT=$AUTO_VALUE_SQL;
											}
											$AUTO_VALUE.=">$AUTO_VALUE_SQL</option>\n";
										}
										break;
        			}
					$ELEMENT_OUT=str_replace($EVALUE,$AUTO_VALUE,$ELEMENT_OUT);
				}
			}
//---- 设置可写字段属性 ----
			if($flowType['FLOW_TYPE']=="1")
			{
				if(!$this->findId($PRCS_ITEM,$EITLE))
				{
					if(strstr($ELEMENT_OUT,"type=checkbox"))
					{
						if(strstr($ELEMENT_OUT," CHECKED"))
							$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME readonly onclick='this.checked=1;' class=BigStatic1",$ELEMENT_OUT);
						else
							$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME readonly onclick='this.checked=0;' class=BigStatic1",$ELEMENT_OUT);
					}
					else
						$ELEMENT_OUT="<$ENAME readonly class=BigStatic1 ".str_replace("<$ENAME","",$ELEMENT_OUT);
	
					if($ENAME=="SELECT")
					{
						$EVALUE=$this->get_attr($ELEMENT_OUT,"VALUE");
						if($ECLASS!="AUTO")
							$ELEMENT_OUT=str_replace($EVALUE,"<OPTION value=$ITEM_VALUE>$ITEM_VALUE</OPTION>",$ELEMENT_OUT);
						else if($ITEM_VALUE_TEXT!="")
							$ELEMENT_OUT=str_replace($EVALUE,"<OPTION value=$ITEM_VALUE>$ITEM_VALUE_TEXT</OPTION>",$ELEMENT_OUT);
					}
				}
			}

//-- 找到代换位置进行控件代换 --
			
			$POS=strpos($PRINT_MODEL,$ELEMENT,$POS);
			
			$PRINT_MODEL=substr($PRINT_MODEL,0,$POS).$ELEMENT_OUT.substr($PRINT_MODEL,$POS+strlen($ELEMENT));
		}


	$this->assign('PRINT_MODEL',$PRINT_MODEL);
	$this->display();
	}
	
	public function inputForm(){
//时间变量
		$CUR_TIME1 = date("H:i:s",time());
		$CUR_DATE = date("Y-m-d");
		$CUR_TIME = $CUR_DATE." ".$CUR_TIME1;
		$browser = $_GET;
		$this->assign('browser',$browser);
//写处理标记为已接收，写处理时间
		$flowRunPrcs = new FlowRunPrcsModel();
		$a = array('USER_ID'=>$this->LOGIN_USER_ID,'RUN_ID'=>$_GET['RUN_ID'],'PRCS_ID'=>$_GET['PRCS_ID']);
		$list = $flowRunPrcs->where($a)->find();
		if($list['PRCS_FLAG'] = 1){
			$data=array('PRCS_FLAG'=>2,'PRCS_TIME'=>time());
			$flowRunPrcs->save($data,$a);
		}
//???????????????
		$data2 =array('PRCS_FLAG'=>4);
		$a2 = array('RUN_ID'=>$_GET['RUN_ID'],'PRCS_ID'=>$_GET['PRCS_ID']-1);
		$flowRunPrcs->save($data2,$a2);

		$flowType = new FlowTypeModel();
		$flowType = $flowType ->where('FLOW_ID='.$_GET['FLOW_ID'])->find();
			
		if($flowType['FLOW_TYPE']==1){
			$flowProcess = new FlowProcessModel();
			$flowProcess = $flowProcess->where(array('FLOW_ID'=>$_GET['FLOW_ID'],'PRCS_ID'=>$_GET['FLOW_PRCS']))->findall();
			foreach ($flowProcess as $k=>$v){
				$PRCS_ITEM=$v['PRCS_ITEM'];
			}
		}
///////////////////////////////////////////////////////////////
//取模板
		$flowFormType = new FlowFormTypeModel();
		$flowFormType = $flowFormType->where('FORM_ID='.$flowType['FORM_ID'])->find();
//取文号，附件信息
		$flowRun = new FlowRunModel();
		$flowRun = $flowRun->where('RUN_ID='.$_GET['RUN_ID'])->find();

 //取表单数据
 		$flowRunData = new FlowRunDataModel();
 		$flowRunData = $flowRunData->where(array('RUN_ID'=>$browser['RUN_ID']))->findall();
 		foreach($flowRunData as $k=>$v){
 			
 		}
		$this->assign('ITEM_ID',$v['ITEM_ID']);
//取步骤1
		$flowRunPrcs1 = $flowRunPrcs->where(array('RUN_ID'=>$_GET['RUN_ID'],'PRCS_ID'=>1))->find();
		$flowRunPrcs1['PRCS_TIME'] = date('Y-m-d',$flowRunPrcs1['PRCS_TIME']);
//取表单模板
		$PRINT_MODEL=str_replace("#[表单]","<b>".$flowFormType['FORM_NAME']."</b>",$flowFormType['PRINT_MODEL']);	
		$PRINT_MODEL=str_replace("#[文号]",$flowRun['RUN_NAME'],$PRINT_MODEL);
		$PRINT_MODEL=str_replace("#[时间]","日期：".$flowRunPrcs1['PRCS_TIME'],$PRINT_MODEL);
//Html 智能分析
		$ELEMENT_ARRAY= $this->htmlElement($PRINT_MODEL);

		$ITEM_ID_MAX=sizeof($ELEMENT_ARRAY);
		$ITEM_ID=0;
		for($I=0;$I<$ITEM_ID_MAX;$I++){
  			$ELEMENT=$ELEMENT_ARRAY[$I];
  			$ENAME=$this->get_attr($ELEMENT,"NAME");
  			$EVALUE=$this->get_attr($ELEMENT,"VALUE");
  			$EITLE=$this->get_attr($ELEMENT,"TITLE");
  			$ECLASS=$this->get_attr($ELEMENT,"CLASS");

//echo $ENAME."-".$EITLE."-".$EVALUE."<br>";

//--- 加入控件名称 ---

			if($ECLASS!="DATE"){
				$ITEM_ID++;
				$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME name='DATA_$ITEM_ID'",$ELEMENT);
				
			}

//--- 赋值 ----
			$STR="DATA_".$ITEM_ID;
			$ITEM_VALUE=$$STR;
			
			if($ITEM_VALUE=="{宏控件}")
				$ITEM_VALUE="";

			if($ENAME=="INPUT"){
				if(!strstr($ELEMENT_OUT,"type=checkbox")) { //textfield
					$ELEMENT_OUT=str_replace("value=$EVALUE","",$ELEMENT_OUT);
					$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME value='$ITEM_VALUE'",$ELEMENT_OUT);
				} else {
					$ELEMENT_OUT=str_replace(" CHECKED","",$ELEMENT_OUT);		
					if($ITEM_VALUE=="on")
					$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME CHECKED",$ELEMENT_OUT);
				}
			} else if ($ENAME=="TEXTAREA") {
				$ELEMENT_OUT=str_replace(">$EVALUE<",">$ITEM_VALUE<",$ELEMENT_OUT);
			} else if ($ENAME=="SELECT" && $ITEM_VALUE!="" && $ECLASS!="AUTO") {
				$ELEMENT_OUT=str_replace(" selected","",$ELEMENT_OUT);
				$ELEMENT_OUT=str_replace("<OPTION value=$ITEM_VALUE>","<OPTION selected value=$ITEM_VALUE>",$ELEMENT_OUT);
			}

//--- 特殊控件 ---
			if($ECLASS=="DATE") {//日期控件
     			$ELEMENT_OUT="<IMG class=DATE align=absmiddle title=日期控件：$EVALUE style=\"CURSOR: hand\" src=\"/images/menu/calendar.gif\" border=0 onclick=\"td_calendar('$EVALUE')\">";
			} elseif($ECLASS=="CALC") { //计算控件
     			$K=0;
     			for($J=0;$J<$ITEM_ID_MAX;$J++) {
					$ELEMENT1=$ELEMENT_ARRAY[$J];
       				$ETITLE1=$this->get_attr($ELEMENT1,"TITLE");
					$ECLASS1=$this->get_attr($ELEMENT1,"CLASS");

       				if($ECLASS1!="DATE")
          				$K++;
       				else
          				continue;

       				$ETITLE_ARRAY[$K]=$ETITLE1;
     			}

				$EVALUE1=$EVALUE."#";

				$POS=0;
				$ECALC1="";
				$ECALC2="";
				while($POS<strlen($EVALUE1)) {
					$STR=substr($EVALUE1,$POS,1);
					$POS++;

					if($STR=="+"||$STR=="-"||$STR=="*"||$STR=="/"||$STR=="("||$STR==")"||$STR=="^"||$STR=="#") {
						if($ECALC2!="") {

							$ARRAY_COUNT=sizeof($ETITLE_ARRAY);
								if($ETITLE_ARRAY[$ARRAY_COUNT-1]=="")$ARRAY_COUNT--;
									for($K=0;$K<$ARRAY_COUNT;$K++) {
										if($ETITLE_ARRAY[$K]==$ECALC2) {
											$ECALC1.="parseFloat(document.form1.DATA_".$K.".value)";
											$ECALC2="";
											break;
										}
									}
						}

						if($STR!="#")
							$ECALC1.=$STR;
					} else
						$ECALC2.=$STR;
				}

				$ELEMENT_OUT.="<IMG title=计算 align=absmiddle style=\"CURSOR: hand\" src=\"/images/form/calc.gif\" border=0 onclick=\"calc_$ITEM_ID()\">";
				$ELEMENT_OUT.="<script>
					function calc_$ITEM_ID() {
						myvalue=$ECALC1;
						if(!isNaN(myvalue))
							document.form1.DATA_$ITEM_ID.value=Math.round(myvalue * 100)/100;
						else
							document.form1.DATA_$ITEM_ID.value=\"\";
						setTimeout(\"calc_$ITEM_ID()\",1000);
					}
					calc_$ITEM_ID();
					</script>";
			} elseif($ECLASS=="AUTO") {//宏控件
				$EDATAFLD=$this->get_attr($ELEMENT,"DATAFLD");
				if($ENAME=="INPUT" && $ITEM_VALUE=="") {// 单行输入框，无值时时将自动取值
					switch($EDATAFLD) {
						case "SYS_DATE":
                         	$AUTO_VALUE=$CUR_DATE;
                         break;
						case "SYS_TIME":
                         	$AUTO_VALUE=$CUR_TIME1;
                         break;
           				case "SYS_DATETIME":
                         	$AUTO_VALUE=$CUR_TIME;
                         break;
           				case "SYS_USERID":
                         	$AUTO_VALUE=$this->LOGIN_USER_ID;
                         break;
           				case "SYS_USERNAME":
           					$user = new UserModel();
           					$user = $user->where('USER_ID='.$this->LOGIN_USER_ID)->find();
           					$AUTO_VALUE = $user['USER_NAME'];
                         break;
						case "SYS_DEPTNAME":
							$AUTO_VALUE=$this->deptLongName($this->LOGIN_DEPT_ID);
                         break;
           				case "SYS_FORMNAME":
                         	$AUTO_VALUE=$flowFormType['FORM_NAME'];
                         break;
           				case "SYS_RUNNAME":
                         	$AUTO_VALUE=$flowRun['RUN_NAME'];
                         break;
           				case "SYS_RUNDATE":
                         	$AUTO_VALUE=$flowRunPrcs1['PRCS_TIME'];
                         break;
           				case "SYS_RUNDATETIME":
                         	$AUTO_VALUE=$flowRunPrcs1['PRCS_TIME'];
                         break;
						case "SYS_SQL":
							$EDATASRC=$this->get_attr($ELEMENT,"DATASRC");
							$EDATASRC=str_replace("`","'",$EDATASRC);
							$cursor_SYS_SQL = exequery($connection,$EDATASRC);
							if($ROW=mysql_fetch_array($cursor_SYS_SQL))
								$AUTO_VALUE=$ROW[0];
						break;
					}

					$ELEMENT_OUT=str_replace("value=$EVALUE","",$ELEMENT_OUT);
					$ELEMENT_OUT=str_replace("value=''","",$ELEMENT_OUT);
					$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME value='$AUTO_VALUE'",$ELEMENT_OUT);
				} elseif($ENAME=="SELECT") {
					$AUTO_VALUE="<option value=\"\"";
					if($ITEM_VALUE=="")
						$AUTO_VALUE.=" selected";
						$AUTO_VALUE.="></option>\n";

        			$ITEM_VALUE_TEXT="";
        			switch($EDATAFLD)
        			{
			           case "SYS_LIST_DEPT":
			                         $AUTO_VALUE.=my_dept_tree(0,$ITEM_VALUE,0);
			                         if($ITEM_VALUE!="")
			                         {
			                            $query_auto="SELECT * from DEPARTMENT where DEPT_ID=$ITEM_VALUE";
			                            $cursor_auto = exequery($connection,$query_auto);
			                            if($ROW=mysql_fetch_array($cursor_auto))
			                               $ITEM_VALUE_TEXT=$ROW["DEPT_NAME"];
			                         }
			                         break;
			           case "SYS_LIST_USER":
			                         $query_auto="SELECT * from USER,USER_PRIV where USER.USER_PRIV=USER_PRIV.USER_PRIV order by PRIV_NO,USER_NAME";
			                         $cursor_auto = exequery($connection,$query_auto);
			                         while($ROW=mysql_fetch_array($cursor_auto))
			                         {
			                            $USER_ID=$ROW["USER_ID"];
			                            $USER_NAME=$ROW["USER_NAME"];
			                            $AUTO_VALUE.="<option value=\"$USER_ID\"";
			                            if($ITEM_VALUE==$USER_ID)
			                            {
			                               $AUTO_VALUE.=" selected";
			                               $ITEM_VALUE_TEXT=$USER_NAME;
			                            }
			                            $AUTO_VALUE.=">$USER_NAME</option>\n";
			                         }
			                         break;
			           case "SYS_LIST_PRIV":
			                         $query_auto="SELECT * from USER_PRIV order by PRIV_NO";
			                         $cursor_auto = exequery($connection,$query_auto);
			                         while($ROW=mysql_fetch_array($cursor_auto))
			                         {
			                            $USER_PRIV=$ROW["USER_PRIV"];
			                            $PRIV_NAME=$ROW["PRIV_NAME"];
			                            $AUTO_VALUE.="<option value=\"$USER_PRIV\"";
			                            if($ITEM_VALUE==$USER_PRIV)
			                            {
			                               $AUTO_VALUE.=" selected";
			                               $ITEM_VALUE_TEXT=$PRIV_NAME;
			                            }
			                            $AUTO_VALUE.=">$PRIV_NAME</option>\n";
			                         }
			                         break;
			           case "SYS_LIST_PRCSUSER1":
			                         $query_auto = "select * from FLOW_PROCESS where FLOW_ID=$FLOW_ID order by PRCS_ID";
			                         $cursor_auto=exequery($connection,$query_auto);
			                         $PRCS_USER="";
			                         while($ROW=mysql_fetch_array($cursor_auto))
			                            $PRCS_USER.=$ROW["PRCS_USER"];
			
			                         $query_auto = "SELECT * from USER,USER_PRIV where USER.USER_PRIV=USER_PRIV.USER_PRIV order by PRIV_NO,USER_NAME";
			                         $cursor_auto=exequery($connection,$query_auto);
			                         while($ROW=mysql_fetch_array($cursor_auto))
			                         {
			                           $USER_ID=$ROW["USER_ID"];
			                           $USER_NAME=$ROW["USER_NAME"];
			                           if(find_id($PRCS_USER,$USER_ID))
			                           {
			                               $AUTO_VALUE.="<option value=\"$USER_ID\"";
			                               if($ITEM_VALUE==$USER_ID)
			                               {
			                                  $AUTO_VALUE.=" selected";
			                                  $ITEM_VALUE_TEXT=$USER_NAME;
			                               }
			                               $AUTO_VALUE.=">$USER_NAME</option>\n";
			                           }
			                         }
			                         break;
									case "SYS_LIST_PRCSUSER2":
										$query_auto = "select * from FLOW_PROCESS where FLOW_ID=$FLOW_ID and PRCS_ID=$FLOW_PRCS";
										$cursor_auto=exequery($connection,$query_auto);
										if($ROW=mysql_fetch_array($cursor_auto))
											$PRCS_USER=$ROW["PRCS_USER"];
			
										$query_auto = "SELECT * from USER,USER_PRIV where USER.USER_PRIV=USER_PRIV.USER_PRIV order by PRIV_NO,USER_NAME";
										$cursor_auto=exequery($connection,$query_auto);
										while($ROW=mysql_fetch_array($cursor_auto)) {
											$USER_ID=$ROW["USER_ID"];
											$USER_NAME=$ROW["USER_NAME"];
											if(find_id($PRCS_USER,$USER_ID)||($ITEM_VALUE==$USER_ID)) {
												$AUTO_VALUE.="<option value=\"$USER_ID\"";
												if($ITEM_VALUE==$USER_ID) {
													$AUTO_VALUE.=" selected";
													$ITEM_VALUE_TEXT=$USER_NAME;
												}
												$AUTO_VALUE.=">$USER_NAME</option>\n";
											}
										}	
										break;
									case "SYS_LIST_SQL": 
										$EDATASRC=str_replace("`","'",$EDATASRC);
										$cursor_SYS_SQL = exequery($connection,$EDATASRC);
										while($ROW=mysql_fetch_array($cursor_SYS_SQL))
										{
											$AUTO_VALUE_SQL=$ROW[0];
											$AUTO_VALUE.="<option value=\"$AUTO_VALUE_SQL\"";
											if($ITEM_VALUE==$AUTO_VALUE_SQL)
											{
												$AUTO_VALUE.=" selected";
												$ITEM_VALUE_TEXT=$AUTO_VALUE_SQL;
											}
											$AUTO_VALUE.=">$AUTO_VALUE_SQL</option>\n";
										}
										break;
        			}
					$ELEMENT_OUT=str_replace($EVALUE,$AUTO_VALUE,$ELEMENT_OUT);
				}
			}
//---- 设置可写字段属性 ----
			if($flowType['FLOW_TYPE']=="1")
			{
				if(!$this->findId($PRCS_ITEM,$EITLE))
				{
					if(strstr($ELEMENT_OUT,"type=checkbox"))
					{
						if(strstr($ELEMENT_OUT," CHECKED"))
							$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME readonly onclick='this.checked=1;' class=BigStatic1",$ELEMENT_OUT);
						else
							$ELEMENT_OUT=str_replace("<$ENAME","<$ENAME readonly onclick='this.checked=0;' class=BigStatic1",$ELEMENT_OUT);
					}
					else
						$ELEMENT_OUT="<$ENAME readonly class=BigStatic1 ".str_replace("<$ENAME","",$ELEMENT_OUT);
	
					if($ENAME=="SELECT")
					{
						$EVALUE=$this->get_attr($ELEMENT_OUT,"VALUE");
						if($ECLASS!="AUTO")
							$ELEMENT_OUT=str_replace($EVALUE,"<OPTION value=$ITEM_VALUE>$ITEM_VALUE</OPTION>",$ELEMENT_OUT);
						else if($ITEM_VALUE_TEXT!="")
							$ELEMENT_OUT=str_replace($EVALUE,"<OPTION value=$ITEM_VALUE>$ITEM_VALUE_TEXT</OPTION>",$ELEMENT_OUT);
					}
				}
			}

//-- 找到代换位置进行控件代换 --
			
			$POS=strpos($PRINT_MODEL,$ELEMENT,$POS);
			
			$PRINT_MODEL=substr($PRINT_MODEL,0,$POS).$ELEMENT_OUT.substr($PRINT_MODEL,$POS+strlen($ELEMENT));
		}


	$this->assign('PRINT_MODEL',$PRINT_MODEL);
	$this->display();
	}
	public function control(){
		$this->display();
	}
	public function inputArea(){
		echo 'inputArea';
	}
}
?>