<?php
//import("@.Action.PublicAction");
import("@.Util.userselect");
include_cache(APP_PATH."/Common/diary.php");

class DiaryAction extends PublicAction {
	
		function _initialize(){
		$this->curtitle="工作日志";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
   /*--------------入口程序-显示日志列表*/
	public  function index(){
		$dao=D("Diary");
		$map=array("USER_ID"=>$this->LOGIN_USER_ID);
		$count=$dao->count($map);
           if($count>0){
	            import("ORG.Util.Page");	
			$p= new Page($count);
				//分页查询数据
			$list=$dao->where($map)
			          ->order("DIA_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	          
		$this->display();
	}
	/*------------添加日志表单----------------*/
	public function add(){
		$dao=D("User");
		$user=$dao->table("user as u")->join("department d on u.DEPT_ID = d.DEPT_ID")->where("u.USER_ID='$this->LOGIN_USER_ID'")->find();
		if ($user[DEPT_NAME]) {
            $user[DEPT_NAME]="(".$user[DEPT_NAME].")";			
		}
		
		$this->assign("subject",$user[USER_NAME].$user[DEPT_NAME].$this->CUR_DATE." 日志");
		
		$this->assign("CUR_DATE",$this->CUR_DATE);
		
		$this->assign("CONTENT","");
		$this->assign("user",$user);
		$this->assign("curDate",$this->CUR_DATE);
		$this->display();
	}
	/*------------插入程序------------------*/
	public function insert(){
		$_POST[USER_ID]=$this->LOGIN_USER_ID;
		$_POST[ADD_DATE]=$this->CUR_TIME;
		$dao=D("Diary");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
        $id = $dao->add();
        if($id) { //保存成功
            //成功提示
            $this->assign("jumpUrl",__URL__."/index");
            $this->success('成功添加');
        }else { 
            //失败提示
            $this->assign("jumpUrl",__URL__."/index");
            $this->error('添加失败');
        }
	}
	/*----------------删除日志------------------*/
	public function delete(){
		if(!$_GET[DIA_ID]){
			$this->error('日志不存在');
		}
		$dao=D("Diary");
		if ($dao->where("DIA_ID='$_GET[DIA_ID]' AND USER_ID='$this->LOGIN_USER_ID'")
		     ->delete()){
		     	  $this->assign("jumpUrl",__URL__."/index");
		          $this->success('删除成功');
		//          $this->redirect('diary','index');
		}else {
			      $this->assign("jumpUrl",__URL__."/index");
			      $this->error('删除失败');
			//      $this->redirect('diary','index');
			
		}
		
	}
	/*------------修改------------*/
	public function edit(){
		$dao=D("Diary");
		$DIA_ID=$_REQUEST[DIA_ID];
		
		$map=array("USER_ID"=>$this->LOGIN_USER_ID,
		           "DIA_ID"=>$DIA_ID 
		           );
		$ROW=$dao->where($map)->find();
		$this->assign("ROW",$ROW); 
		$this->display();		
	}
	/*-------------------更新日志------------*/
	public function update(){
		$dao=D("Diary");
		$_POST[ADD_DATE]=$this->CUR_TIME;
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
        if($dao->where("DIA_ID='$_REQUEST[DIA_ID]'")->save()) { //保存成功
            $this->assign("jumpUrl",__URL__."/index");
            $this->success('修改添加');
        }else { 
            $this->assign("jumpUrl",__URL__."/index");
            $this->error('修改失败');
        }
	
	}
	
	/*------------显示个人查询表单------------*/
	public function search(){
		
		$this->display();
	}
	/*------------个人查询结果------------------*/
	public  function query(){
		$dao=D("Diary");
		
		           $maps="USER_ID='$this->LOGIN_USER_ID'";
            	if($_REQUEST[DIA_TYPE]){
                   $map[DIA_TYPE]=  $_REQUEST[DIA_TYPE];
                   $maps.=" AND DIA_TYPE='$_REQUEST[DIA_TYPE]'";
            	}
            	
                if ($_REQUEST[DIA_DATE_START]){
                	$map[DIA_DATE_START]=$_REQUEST[DIA_DATE_START];
                	$maps.=" AND DIA_DATE>='".trim($_REQUEST[DIA_DATE_START])."'";
                }
                if ($_REQUEST[DIA_DATE_END]){
                	$map[DIA_DATE_END]=$_REQUEST[DIA_DATE_END];
                	$maps.=" AND DIA_DATE<='".trim($_REQUEST[DIA_DATE_END])."'";
                }
                
                if($_REQUEST[key1]){
                   $map[key1]	=	$_REQUEST[key1];
                   $maps.=" AND (CONTENT like '%$_REQUEST[key1]%' or SUBJECT like '%$_REQUEST[key1]%')";
                }
                if($_REQUEST[key2]){
                   $map[key2]	=	$_REQUEST[key2];
                   $maps.=" AND (CONTENT like '%$_REQUEST[key2]%' or SUBJECT like '%$_REQUEST[key2]%')";
                }
                if($_REQUEST[key3]){
                   $map[key3]	=	$_REQUEST[key3];
                   $maps.=" AND (CONTENT like '%$_REQUEST[key3]%' or SUBJECT like '%$_REQUEST[key3]%')";
                }
		$count=$dao->count($maps);
		//echo $dao->getlastsql();
		//exit;
           if($count>0){
	            import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
					            	
			$p= new Page($count,$listRows);
				//分页查询数据
			$list=$dao->where($maps)
			          ->order("DIA_DATE desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
				
			   foreach($map as $key=>$val) {
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						$p->parameter   .=   "$key=".urlencode($val)."&";        
					}
				}
							          
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	          
		$this->display();
	}
	
	
	
	/*-----------------管理员查询入口---左右柜架式----------*/
	public function info(){
		$this->display();
	}
    /*------------------显示树形----------------*/
	public function infotree(){
		$dao=D("Unit");
		$row=$dao->find();
		$this->assign("_UNIT",$row[UNIT_NAME]);
		
		###获取用户基本信息（管理范围）
		$dao=D("User");
		$user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		
		$PRIV_NO_FLAG=$_REQUEST[PRIV_NO_FLAG]=1;
		if ($PRIV_NO_FLAG) {
			$dao=D("UserPriv");
			$row=$dao->where("USER_PRIV='$this->LOGIN_USER_PRIV'")->find();
			$PRIV_NO=$row[PRIV_NO];
		}
		
		if($user[POST_PRIV]=="1")
		    $DEPT_PRIV="全体";
		elseif($user[POST_PRIV]=="2")
		    $DEPT_PRIV="指定部门";
		else{
			$dao=D("Department");
			$row=$dao->where("DEPT_ID=$this->LOGIN_DEPT_ID")->find();
		    $DEPT_PRIV=$row["DEPT_NAME"];
		 }
		$PRIV_NO_FLAG=$_REQUEST['PRIV_NO_FLAG']=1;
		
		$userselect=new userselect();
		$deptlist=$userselect->_DeptUserTree(0,$PRIV_NO_FLAG,$PRIV_NO); 
		
		$this->assign("deptlist",$deptlist);		 		
		
		$this->display();
		
	}
	/*---------------查询初始页面提示选择员工------------*/
	public function infoblank(){
		
		$this->assign("msg","请选择相关的员工进行查看");
		$this->display();
		
	}
	/*------------- 得到本周的开始结束时间------------*/
	protected function getWeekBeginEnd($week=0){
		switch($week)
		{
		  case 0:
		   $week_start = date("U",mktime(0,0,0,date("m"),date("d"),date("Y")));
		   $week_end = date("U",mktime(23,59,59,date("m"),date("d")+6,date("Y")));
		   break;
		  case 1:
		   $week_start = date("U",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
		   $week_end = date("U",mktime(23,59,59,date("m"),date("d")+5,date("Y")));
		   break;
		  case 2:
		   $week_start = date("U",mktime(0,0,0,date("m"),date("d")-2,date("Y")));
		   $week_end = date("U",mktime(23,59,59,date("m"),date("d")+4,date("Y")));
		   break;
		  case 3:
		   $week_start = date("U",mktime(0,0,0,date("m"),date("d")-3,date("Y")));
		   $week_end = date("U",mktime(23,59,59,date("m"),date("d")+3,date("Y")));
		   break;
		  case 4:
		   $week_start = date("U",mktime(0,0,0,date("m"),date("d")-4,date("Y")));
		   $week_end = date("U",mktime(23,59,59,date("m"),date("d")+2,date("Y")));
		   break;
		  case 5:
		   $week_start = date("U",mktime(0,0,0,date("m"),date("d")-5,date("Y")));
		   $week_end = date("U",mktime(23,59,59,date("m"),date("d")+1,date("Y")));
		   break;
		  case 6:
		   $week_start = date("U",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
		   $week_end = date("U",mktime(23,59,59,date("m"),date("d"),date("Y")));
		   break;
		}
		$array[0]=$week_start;
		$array[1]=$week_end;		
		return $array;
	}
/*--------------查询结果----------------*/
	public function statistics(){
		$USER_ID=$_REQUEST[USER_ID];
		$type=$_REQUEST[type];
        
		$BEGIN_DAY=$_REQUEST[BEGIN_DAY];
        $END_DAY=$_REQUEST[END_DAY];
        		
        $YEAR=$_REQUEST[YEAR];
        $MONTH=$_REQUEST[MONTH];		
		$CUR_YEAR = date('Y');
		$CUR_MON = date('m');
		$CUR_DAY = date('d');
		$DATE=1;
		$DAY=1;
		if(!$YEAR)
		   $YEAR = $CUR_YEAR;
		if(!$MONTH)
		   $MONTH = $CUR_MON;
		while (checkdate($MONTH,$DATE,$YEAR))
		  $DATE++;		   
		   		
		if (!$USER_ID) {
			$this->assign("jumpUrl",__URL__."/infoblank");
			$this->error("请选择员工");
		}
		

        $dao=D("User");
		$user=$dao->where("USER_ID='$USER_ID'")->find();
        
        if ($type=="week") {
        	$WEEKS=date("w");
        	if (!$BEGIN_DAY&&!$END_DAY) {
        		$array=$this->getWeekBeginEnd($WEEKS);
        		$BEGIN_DAY=date("d",$array[0]);
        		$END_DAY=date("d",$array[1]);
        	}
        $desc=$user[USER_NAME]."一周工作日志";	
        $desc_week=$user[USER_NAME]."本周周报";
        
        }elseif ($type=="month"){
        	    $BEGIN_DAY=$DAY;
        		$END_DAY=$DATE-1;
        		
        $desc=$user[USER_NAME]."一月工作日志";        		
        $desc_month=$user[USER_NAME]."本月月报";
        }
        $this->assign("desc",$desc);
        $this->assign("desc_week",$desc_week);
        $this->assign("desc_month",$desc_month);
        $dao=D("Diary");
        for($DAYI=$BEGIN_DAY;$DAYI<=$END_DAY;$DAYI++)
		{
		  $WEEK=date("w",mktime(0,0,0,$MONTH,$DAYI,$YEAR));
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
		  $headlist[$DAYI][WEEK_DESC]=$WEEK_DESC;
		  $headlist[$DAYI][DAYI]=$DAYI;
		  $headlist[$DAYI][week]=$WEEK;

		  $headlistweek[$DAYI][WEEK_DESC]=$WEEK_DESC;
		  $headlistweek[$DAYI][DAYI]=$DAYI;
		  $headlistweek[$DAYI][week]=$WEEK;
		  		  
		  $headlistmonth[$DAYI][WEEK_DESC]=$WEEK_DESC;
		  $headlistmonth[$DAYI][DAYI]=$DAYI;
		  $headlistmonth[$DAYI][week]=$WEEK;
		  		  
	      $maps="USER_ID='$USER_ID' and (DIA_TYPE='1' or DIA_TYPE='3' or DIA_TYPE='4' or DIA_TYPE='5') and to_days(DIA_DATE)=to_days('$YEAR-$MONTH-$DAYI')";
		  $list=$dao->where($maps)
			        ->order("DIA_DATE desc")
			        ->findall();
	      if (is_array($list)) {
			  foreach ($list as $key=>$arr){
                    if ($arr[DIA_TYPE]==3) {//周报
                       $headlistweek[$DAYI][sublist][]=$arr;  			      		
                    } 
                    if ($arr[DIA_TYPE]==4) {//月报
                       $headlistmonth[$DAYI][sublist][]=$arr;  			      		
                    }  
			  } 
	      }		        
		  $headlist[$DAYI][sublist]=$list;

		  
		     
		}
		
		$this->assign('headlist',$headlist);
	    
		$this->assign("USER_ID",$USER_ID);
		$this->assign("type",$type);
		$this->assign("YEAR",$YEAR);  
		$this->assign("MONTH",$MONTH);  
		$this->assign("DAY",$DAY);
		$this->assign("DATE",$DATE);
		$this->assign("CUR_YEAR",$CUR_YEAR);
		$this->assign("CUR_MON",$CUR_MON);
		$this->assign("CUR_DAY",$CUR_DAY);		
		$this->display();
		
	}
			
	/*--------------查询结果----------------*/
	public function infolist(){
		$USER_ID=$_REQUEST[USER_ID];
		if (!$USER_ID) {
			$this->assign("jumpUrl",__URL__."/infoblank");
			$this->error("请选择员工");
		}
		$dao=D("User");
		$user=$dao->where("USER_ID='$USER_ID'")->find();
        $this->assign("desc",$user[USER_NAME]."的工作日志"); 		
        
		$dao=D("Diary");
		
		        $maps="USER_ID='$USER_ID' and DIA_TYPE='1'";
		        
                if ($_REQUEST[DIA_DATE_START]){
                	$map[DIA_DATE_START]=$_REQUEST[DIA_DATE_START];
                	$maps.=" AND DIA_DATE>='$_REQUEST[DIA_DATE_START]'";
                }
                if ($_REQUEST[DIA_DATE_END]){
                	$map[DIA_DATE_END]=$_REQUEST[DIA_DATE_END];
                	$maps.=" AND DIA_DATE<='$_REQUEST[DIA_DATE_END]'";
                }
                
                if($_REQUEST[key1]){
                   $map[key1]	=	$_REQUEST[key1];
                   $maps.=" AND CONTENT like '%$_REQUEST[key1]%'";
                }
                if($_REQUEST[key2]){
                   $map[key2]	=	$_REQUEST[key2];
                   $maps.=" AND CONTENT like '%$_REQUEST[key2]%'";
                }
                if($_REQUEST[key3]){
                   $map[key3]	=	$_REQUEST[key3];
                   $maps.=" AND CONTENT like '%$_REQUEST[key3]%'";
                }
                		
		
		
		$count=$dao->count($maps);
        if($count>0){
	            import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
					            	
			$p= new Page($count,$listRows);
				//分页查询数据
			$list=$dao->where($maps)
			          ->order("DIA_DATE desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
				
			   foreach($map as $key=>$val) {
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						$p->parameter   .=   "$key=".urlencode($val)."&";        
					}
				}
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        		
		$this->assign("USER_ID",$USER_ID);
		$this->display();
		
	}
	/*---------------查询表单------------*/
	public function infosearch(){
		$USER_ID=$_REQUEST[USER_ID];
		if (!$USER_ID) {
			$this->assign("jumpUrl",__URL__."/infoblank");
			$this->error("请选择员工");
		}
		$dao=D("User");
		$user=$dao->where("USER_ID='$USER_ID'")->find();
        $this->assign("desc",$user[USER_NAME]."的工作日志"); 	
        
		$this->assign("USER_ID",$USER_ID);	
		$this->display();
		
	}
   /*-----------------查看日志内容--------------*/
   public function infodetail(){
   		$dao=D("Diary");
		$DIA_ID=$_REQUEST[DIA_ID];
		
		$map=array("DIA_ID"=>$DIA_ID);
		$row=$dao->where($map)->find();
		$dao=D("User");
		$user=$dao->where("USER_ID='$row[USER_ID]'")->find();
		
		$this->assign("USER_ID",$user[USER_ID]);
		$this->assign("USER_NAME",$user[USER_NAME]);
		
		$this->assign("row",$row); 
		$this->display();
			
   }
   
   /*-----------------查看日志内容--便签形式--------------*/
   public function note(){
   		$dao=D("Diary");
		$DIA_ID=$_REQUEST[DIA_ID];
		
		$map=array("DIA_ID"=>$DIA_ID);
		$row=$dao->where($map)->find();
		$dao=D("User");
		$user=$dao->where("USER_ID='$row[USER_ID]'")->find();
		
		$this->assign("USER_ID",$user[USER_ID]);
		$this->assign("USER_NAME",$user[USER_NAME]);
		
		$this->assign("row",$row); 
		$this->display();
			
   }
   	
}
?>