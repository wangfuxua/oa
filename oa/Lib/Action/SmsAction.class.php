<?php
/**
 * SmsAction.class.php
 * 功能：站内短信息模块
 * */
include_cache(APP_PATH."/Common/sms.php");
class SmsAction extends PublicAction {
	var $curtitle;
	function _initialize(){
		$this->curtitle="内部短信息";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
/*---------------已接收-------------*/		
	public function index(){
		//print_r(ini_get_all()) ;
		//print_r(ini_get("date.timezone"));
		ini_set("date.timezone","Etc/GMT-8");
       //print_r(ini_get_all());
       //exit;		
		import("@.Util.sms");
		$smstype=new sms();
		$smstype_array=$smstype->getSmsType();
		$dao=D("Sms");
		$map["TO_ID"]=$this->LOGIN_USER_ID;
		$map["SEND_TIME"]=array('elt',$this->CUR_TIME);
		
		$map2=array(
		"s.TO_ID"=>$this->LOGIN_USER_ID,
		"s.SEND_TIME"=>array("elt", $this->CUR_TIME)
		);
		$count=$dao->count($map);
	        if($count>0){
	            import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p= new Page($count,$listRows);
				//分页查询数据
                $list=$dao->table("sms as s")
                          ->join("user as b on s.FROM_ID=b.USER_ID")
                          ->field("s.*,b.USER_NAME,b.AVATAR")
                          ->order("s.SEND_TIME desc")
                          ->where($map2)
                          ->limit("$p->firstRow,$p->listRows")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        $this->assign("COUNT",$count);
	        $this->assign("CUR_TIME",$this->CUR_TIME);
	        $this->assign("smstype_array",$smstype_array);
		$this->display();
		
	} 
	public function smsform(){
		####选择用户
		UserSelectAction::DeptUserSelectAll(); 
		$TO_ID=$_REQUEST['TO_ID'];
		$TO_NAME=$_REQUEST['TO_NAME'];
		#########弹出框开始#########  
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束#########  		
		$this->assign("TO_NAME",$TO_NAME);
		$this->assign("TO_ID",$TO_ID); 
		$this->display();
	}
 
	
	
	
	public function submit(){//发送

		if (empty($_POST[CONTENT])) {
			$this->error("请输入短信息内容");
		}
		$a = UserAction::getUser_id($_POST[TO_ID]);
		$to_id=implode(",",$a);  
		if($this->send_sms($this->LOGIN_USER_ID,$to_id,0,$_POST[CONTENT])){
		   $this->assign("jumpUrl",__URL__."/index");
		   $this->success("成功发送");
		}else{ 
		   $this->assign("jumpUrl",__URL__."/index");	
		   $this->error("发送失败");   
		}
	}
 
	public function resubmit(){//重发
		$SMS_ID=$_REQUEST[SMS_ID];
		$dao=D("Sms");
		$row=$dao->where("SMS_ID='$SMS_ID'")
		         ->find();
		if($this->send_sms($this->LOGIN_USER_ID,$row[TO_ID],0,$row[CONTENT])){
		   $this->assign("jumpUrl",__URL__."/index");
		   $this->success("成功发送");
		}else{ 
			$this->assign("jumpUrl",__URL__."/index");
		   $this->error("发送失败");   
		}
	}
		
	
	public function delete(){//删除单个记录
		$SMS_ID=$_REQUEST[SMS_ID];
		$to=$_REQUEST[to];
    	  $BEGIN_DATE=$_REQUEST[BEGIN_DATE];
    	  $END_DATE=$_REQUEST[END_DATE];
    	  $TO_ID=$_REQUEST[TO_ID];
    	  $TYPE=$_REQUEST[TYPE];
    	  $ORDER_BY=$_REQUEST[ORDER_BY];
    	  $SEQ=$_REQUEST[SEQ];
		  $CONTENT=$_REQUEST[CONTENT];
		  
		$dao=D("Sms");
		$dao->where("SMS_ID='$SMS_ID'")->delete();
		
		if ($to=="search") {
           $this->assign("jumpUrl",__URL__."/search/?BEGIN_DATE=$BEGIN_DATE&END_DATE=$END_DATE&TO_ID=$TO_ID&TYPE=$TYPE&ORDER_BY=$ORDER_BY&SEQ=$SEQ&CONTENT=$CONTENT");			
		}elseif ($to=="index"){
			$this->assign("jumpUrl",__URL__."/index");			
		}elseif ($to=="send"){
			$this->assign("jumpUrl",__URL__."/send");			
		}
		$this->success("成功删除");
		//$this->redirect($to,"Sms");
	}
	
	/*---选择删除-----*/
	public function deleteAllSelect(){
		$DELETE_STR=$_REQUEST[DELETE_STR];
		$to=$_REQUEST[to];
    	  $BEGIN_DATE=$_REQUEST[BEGIN_DATE];
    	  $END_DATE=$_REQUEST[END_DATE];
    	  $TO_ID=$_REQUEST[TO_ID];
    	  $TYPE=$_REQUEST[TYPE];
    	  $ORDER_BY=$_REQUEST[ORDER_BY];
    	  $SEQ=$_REQUEST[SEQ];
		  $CONTENT=$_REQUEST[CONTENT];
		  		
		$dao=D("Sms");
		$dao->where("SMS_ID in (".$DELETE_STR."0)")->delete();
		
		if ($to=="search") {
           $this->assign("jumpUrl",__URL__."/search/?BEGIN_DATE=$BEGIN_DATE&END_DATE=$END_DATE&TO_ID=$TO_ID&TYPE=$TYPE&ORDER_BY=$ORDER_BY&SEQ=$SEQ&CONTENT=$CONTENT");			
		}elseif ($to=="index"){
			$this->assign("jumpUrl",__URL__."/index");			
		}elseif ($to=="send"){
			$this->assign("jumpUrl",__URL__."/send");			
		}
		
		$this->success("成功删除");
				
		
	}
	
	public function deleteall(){//删除所有(接收到的或发送出去的)
		$to=$_REQUEST[to];
		$DEL_TYPE=$_REQUEST[DEL_TYPE];
		$dao=D("Sms");
		if ($DEL_TYPE==1) {
		    $dao->where("TO_ID='$this->LOGIN_USER_ID'  and SEND_TIME<='$this->CUR_TIME'")->delete();
		}else {
			$dao->where("FROM_ID='$this->LOGIN_USER_ID'  and SEND_TIME<='$this->CUR_TIME'")->delete();
		}
		/*
		if ($to=="index"){
			$this->assign("jumpUrl",__URL__."/index");			
		}elseif ($to=="send"){
			$this->assign("jumpUrl",__URL__."/send");			
		}
		*/
		$this->redirect($to,"Sms");
	}
	public function readall(){
		$dao=D("Sms");
		$dao->setField("REMIND_FLAG","0","TO_ID='$this->LOGIN_USER_ID' and SEND_TIME<='$this->CUR_TIME'");
		
		
		$this->assign("jumpUrl",__URL__."/index");
		$this->success("成功取消");
		//echo $dao->getlastsql();exit;
		
		//$this->redirect("index","Sms");
		
	}
	
	/*---------------已发送-------------*/
	public function send(){
		import("@.Util.sms");
		$smstype=new sms();
		$smstype_array=$smstype->getSmsType();
		$dao=D("Sms");
		$map["FROM_ID"]=$this->LOGIN_USER_ID;
		$map["SEND_TIME"]=array('elt',$this->CUR_TIME);
		
		$map2=array(
		"s.FROM_ID"=>$this->LOGIN_USER_ID,
		"s.SEND_TIME"=>array("elt", $this->CUR_TIME)
		);
		$count=$dao->count($map);
	        if($count>0){
	            import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '20';
				}
				if (empty($_REQUEST[p]))
				    $_REQUEST[p]=1;
				$firstRows=($_REQUEST[p]-1)*$listRows;
				//echo $firstRows;
				$p          = new Page($count,$listRows);
				//分页查询数据
                $list=$dao->table("sms as s")
                          ->join("user as b on s.TO_ID=b.USER_ID")
                          ->field("s.*,b.USER_NAME,b.AVATAR")
                          ->order("s.SEND_TIME desc")
                          ->where($map2)
                          ->limit("$firstRows,$listRows")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        $this->assign("COUNT",$count);
	        $this->assign("CUR_TIME",$this->CUR_TIME);
	        $this->assign("smstype_array",$smstype_array);
		$this->display();
		
	}
	
	/*------------------查询----------*/
	public function query(){//表单 
		#########弹出框开始######### 
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
		
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束#########  		
		$this->assign("CUR_TIME",$this->CUR_TIME);
		$this->display();
	}
    public function search(){//搜索结果
    	  $BEGIN_DATE=$_REQUEST[BEGIN_DATE];
    	  $END_DATE=$_REQUEST[END_DATE];
    	  $TO_ID=$_REQUEST[TO_ID];
    	  $TYPE=$_REQUEST[TYPE];
    	  $ORDER_BY=$_REQUEST[ORDER_BY];
    	  $SEQ=$_REQUEST[SEQ];
    	  $CONTENT=$_REQUEST[CONTENT];
    	  
    	  if($BEGIN_DATE!=""){
		    $TIME_OK=is_date($BEGIN_DATE);
		   	if(!$TIME_OK){
		    	$this->error("起始日期格式不对，应形如 2007-1-2 14:55:20");
		    }
		  }
		
		  if($END_DATE!=""){
		    $TIME_OK=is_date($END_DATE);
		    if(!$TIME_OK){ 
		    	$this->error("截止日期格式不对，应形如 2007-1-2 14:55:20");
		    }
		  }
		  if($END_DATE!=""&&$BEGIN_DATE!=""&&$BEGIN_DATE> $END_DATE){
		    	$this->error("截止时间不能晚于起始时间!");			
		  }
		$TO_ID = implode(",",UserAction::getUser_id($TO_ID)); 
		if($TO_ID!="")
		   $TO_ID="'".str_replace(",","','",$TO_ID)."'";
		
		if($TO_ID!="")
		   $query1.=" and s.".$TYPE." in ($TO_ID)";
		   
		if($BEGIN_DATE!="")
		   $query1.=" and s.SEND_TIME>='$BEGIN_DATE'";
		if($END_DATE!="")
		   $query1.=" and s.SEND_TIME<='$END_DATE'";
		
		if ($CONTENT) {
		   	$query1.=" and s.CONTENT like '%$CONTENT%'";
		   }
		      
		if($TYPE=="TO_ID") {
		   $TYPE_OTHER="s.FROM_ID";
		   $TITLE="收信人";
		   $JOIN="s.TO_ID";
		} else {//=="FROM_ID"
		   $TYPE_OTHER="s.TO_ID";
		   $TITLE="发送人";
		   $JOIN="s.FROM_ID";
		}
		
		if($ORDER_BY=="FROM_TO")
		 $ORDER_BY=$TYPE; 
		 $dao=D("Sms");
		 $count=$dao->table("sms as s")
                          ->join("user as b on ".$JOIN."=b.USER_ID")
                          ->field("count(*) as count")
                          ->where("$TYPE_OTHER='$this->LOGIN_USER_ID'".$query1)
                          ->find(); 
		 $count=$count['count'];
	     import("ORG.Util.Page");
		 if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		 }else {
			$listRows  =  '15';
		 }
		 $p = new Page($count,$listRows);
		 		 
		        $list=$dao->table("sms as s")
                          ->join("user as b on ".$JOIN."=b.USER_ID")
                          ->field("s.*,b.USER_NAME,b.AVATAR")
                          ->order("$ORDER_BY $SEQ")
                          ->where("$TYPE_OTHER='$this->LOGIN_USER_ID'".$query1)
                          ->limit("$p->firstRow,$p->listRows")
                          ->findall();
                // echo $dao->getlastsql();         
				foreach($_REQUEST as $key=>$val) {
		                if ($key=='a'||$key=='m'){
		                   continue;	
		                }
						if(is_array($val)) {
							foreach ($val as $t){
								$p->parameter	.= $key.'[]='.urlencode($t)."&";
							}
						}else{
							
							$p->parameter   .=   "$key=".urlencode($val)."&";        
						}
                  
				}
		$page       = $p->show();
		$this->assign("page",$page);
		$this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID);
        $this->assign("COUNT",count($list));                                                    
        $this->assign("TITLE",$TITLE); 

    	  $this->assign("BEGIN_DATE",$BEGIN_DATE);
    	  $this->assign("END_DATE",$END_DATE);
    	  $this->assign("TO_ID",$TO_ID);
    	  $this->assign("TYPE",$TYPE);
    	  $this->assign("ORDER_BY",$ORDER_BY);
    	  $this->assign("SEQ",$SEQ);
          $this->assign("CONTENT",$CONTENT);
        
    	$this->assign("list",$list);
        $this->display();
    }	
  
    /*--------------------以下是关于短信息提醒功能---------------*/
    
    /*--------------显示新短信条数------------*/
    public function msg(){
    	header( "Cache-Control: no-cache, must-revalidate" );
		header( "Pragma: no-cache" );
		
		###日常事务
		affair_sms();
	    ###刷新当前用户最后活动时间
	    $sess=new userSessionAction();
	    $sess->sess_write();
		###短信息
	    $dao=D("Sms");
	    $map="TO_ID='$this->LOGIN_USER_ID' and REMIND_FLAG='1' and SEND_TIME<='$this->CUR_TIME'";
	    $count=$dao->count($map);
	    echo $count;	
    }
    /*------------显示最后一条新短信息----------*/
    public function msghtml(){
	    $dao=D("Sms");
	    $map="TO_ID='$this->LOGIN_USER_ID' and REMIND_FLAG='1' and SEND_TIME<='$this->CUR_TIME'";
	    $count=$dao->count($map);
	    
	    $row=$dao->where($map)->order("SEND_TIME desc")->find();
	    
    	$this->assign("count",$count);
    	$this->assign("row",$row);
    	$this->display();
    }
   /*-----------------短信息提醒处理-我知道了和删除------------*/ 
   public function msgsubmit(){
   	   $SMS_ID=$_REQUEST[SMS_ID];
   	   $DEL=$_REQUEST[DEL];
   	   $dao=D("Sms");
   	   if ($DEL==1) {//删除短信息
   	   	   $dao->where("SMS_ID='$SMS_ID'")->delete();
   	   	   
   	   }else {//置为不提醒
   	   	   $dao->setField("REMIND_FLAG","0","SMS_ID='$SMS_ID'");
   	   	   
   	   	   //$dao->save();
   	   }
   	   ###处理完成
   	   
	    $map="TO_ID='$this->LOGIN_USER_ID' and REMIND_FLAG='1' and SEND_TIME<='$this->CUR_TIME'";
	    $count=$dao->count($map);
	    
	    if ($count>0) {//查看一下条
	    $row=$dao->where($map)->order("SEND_TIME desc")->find();
    	$this->assign("count",$count);
    	$this->assign("row",$row);
    	$this->display("msghtml");	       	   	
	       	   	echo "
			   	   <script type=\"text/javascript\">
			   	        parent.window.Refresh();
				   </script>
			   	   ";	       	   	
	   }else {//关闭
				echo "
				   <script type=\"text/javascript\">
					    parent.window.oAmsg.close();
					    parent.window.Refresh();
				   </script>
				  ";	   	
	   	
	   }   	   
   	   
   }
   /*-----------------短信息提醒处理-回复------------*/
   public function msgback(){
   	    $SMS_ID=$_REQUEST[SMS_ID];
   	    
	    $dao=D("Sms");
	    $map="SMS_ID='$SMS_ID'";
	    $row=$dao->where($map)->find();
	    $TO_ID=$row[FROM_ID];
	    
	    $dao=D("User");
	    $map="USER_ID='$TO_ID'";
	    $row=$dao->where($map)->find();
	    $TO_NAME=$row[USER_NAME];
	    
	    $this->assign("TO_ID",$TO_ID);
	    $this->assign("TO_NAME",$TO_NAME);
	    $this->assign("SMS_ID",$SMS_ID);
   	    $this->display();
   }
   /*-----------------短信息提醒处理-回复------------*/
   public function msgbacks(){
   	    $SMS_ID=$_REQUEST[SMS_ID];
   	    
	    $dao=D("Sms");
	    $map="SMS_ID='$SMS_ID'";
	    $row=$dao->where($map)->find();
	    $TO_ID=$row[FROM_ID];
	    
	    $dao=D("User");
	    $map="USER_ID='$TO_ID'";
	    $row=$dao->where($map)->find();
	    $TO_NAME=$row[USER_NAME];
	    
	    $this->assign("TO_ID",$TO_ID);
	    $this->assign("TO_NAME",$TO_NAME);
	    $this->assign("SMS_ID",$SMS_ID);
   	    $this->display();
   }
      
   /*-----------------短信息提醒处理-回复处理程序------------*/
   public function msgsend(){
   	   $SMS_ID=$_REQUEST[SMS_ID];
       $dao=D("Sms");  
   	   $dao->setField("REMIND_FLAG","0","SMS_ID='$SMS_ID'");  
   	   	   
 	   $this->send_sms($this->LOGIN_USER_ID,$_POST[TO_ID],0,$_POST[CONTENT]);
 	   
   	   ###处理完成关闭
   	   echo "
   	   <script type=\"text/javascript\">
		    parent.window.oAmsg.close();
		    parent.window.RefreshMsg();
	   </script>
   	   ";
   }
   
   
   /*-----------------左边菜单短信息-----------*/
   
   public function leftmsg(){
   	 $dao=D("Sms");
   	 $count=5;//只显示最新五条
   	 
   	 $map="TO_ID='$this->LOGIN_USER_ID' and SEND_TIME<='$this->CUR_TIME'";
   	 $list=$dao->where($map)
   	           ->order("SEND_TIME desc")
   	           ->limit("0,$count")
   	           ->findall();
   	foreach ($list as $key=>$row){
   		$SMS_ID_STR.=$row[SMS_ID].",";
   	}           
   	
   	$this->assign("SMS_ID_STR",$SMS_ID_STR);
   	
   	$this->assign("count",$count); 
   	$this->assign("list",$list);
   	$this->display();
   	
   }
    /*-----------------删除全部已显示左边菜单短信息-----------*/
	public function leftmsgdeleteall(){
		$to=$_REQUEST[to];
		$SMS_ID_STR=$_REQUEST[SMS_ID_STR];
		$dao=D("Sms");
	    $dao->where("TO_ID='$this->LOGIN_USER_ID'  and SMS_ID  in(".$SMS_ID_STR."0)")->delete();

		$this->redirect("leftmsg","Sms");
	}

	
   /*-------------短信阅读-------------*/	
   public function view(){
   	 $SMS_ID=$_REQUEST[SMS_ID];
   	 $dao=D("Sms");
   	 $row=$dao->where("SMS_ID='$SMS_ID'")->find();
   	 if ($this->LOGIN_USER_ID==$row[FROM_ID]) {
         $row[USER_DESC]="收信人：";
         $row[USER_NAME]=getUsername($row[TO_ID]);
   	 }else {
         $row[USER_DESC]="发信人：";
         $row[USER_NAME]=getUsername($row[FROM_ID]);
   	 }
   	 $this->assign("row",$row);
   	 $this->display();
   	 
   }
}

?>