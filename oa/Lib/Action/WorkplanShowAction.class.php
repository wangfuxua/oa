<?php
import("@.Action.PublicAction");
class WorkplanShowAction extends PublicAction {
    	var $uploadpath_avatar;
	function _initialize(){
		$this->curtitle="工作计划";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		//$this->uploadpath_avatar=APP_PATH."/Tpl/default/Public/images/avatar/";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	public function menutop(){
		$COLOR1="#D9E8FF";
		$COLOR2="#DDDDDD";
		$COLOR3="#B3D1FF";
		$this->assign("COLOR1",$COLOR1);
		$this->assign("COLOR2",$COLOR2);
		$this->assign("COLOR3",$COLOR3);
		$this->display();;	
	}
		
	public function index(){
		
		$this->display();
	}
	
	/*----------------工作计划列表--------------*/
	public function show(){
		$DATE=$_REQUEST['DATE'];
		$this->assign("DATE",$DATE);
		$this->display();
	}
	
	public function query(){
		$DATE=$_REQUEST['DATE'];
		$RANGE=$_REQUEST['RANGE'];
		
		 if($RANGE==0)
		    $RANGE_STR="TO_ID='ALL_DEPT'";
		 else
		    $RANGE_STR="(InStr(TO_ID,',$LOGIN_DEPT_ID,')>0 or InStr(TO_ID,'$LOGIN_DEPT_ID,')=1)";
		    
		 $CUR_DATE=date("Y-m-d",time());
		 
		 $WEEK_BEGIN=date("Y-m-d",(strtotime($CUR_DATE)-(date("w",strtotime($CUR_DATE)))*24*3600));
		 $WEEK_END=date("Y-m-d",(strtotime($CUR_DATE)+(6-date("w",strtotime($CUR_DATE)))*24*3600));
		
		 $MONTH_BEGIN=date("Y-m-d",(strtotime($CUR_DATE)-(date("j",strtotime($CUR_DATE))-1)*24*3600));
		 $MONTH_END=date("Y-m-d",(strtotime($CUR_DATE)+(date("t",strtotime($CUR_DATE))-date("j",strtotime($CUR_DATE)))*24*3600));
		
		 if($DATE==1)//本日
		    $DATE_STR="BEGIN_DATE<='$CUR_DATE' and (END_DATE>='$CUR_DATE' or END_DATE='0000-00-00')";
		 elseif($DATE==2)//本周
		    $DATE_STR="((END_DATE='0000-00-00' and BEGIN_DATE<='$WEEK_END' or (BEGIN_DATE>='$WEEK_BEGIN' and BEGIN_DATE<='$WEEK_END' or END_DATE>='$WEEK_BEGIN' and END_DATE<='$WEEK_END') and END_DATE!='0000-00-00') and (END_DATE='0000-00-00' or END_DATE>='$CUR_DATE'))";
		 elseif($DATE==3)//本月
		    $DATE_STR="((END_DATE='0000-00-00' and BEGIN_DATE<='$MONTH_END' or (BEGIN_DATE>='$MONTH_BEGIN' and BEGIN_DATE<='$MONTH_END' or END_DATE>='$MONTH_BEGIN' and END_DATE<='$WEEK_END') and END_DATE!='0000-00-00') and (END_DATE='0000-00-00' or END_DATE>='$CUR_DATE'))";
		    
		$dao=D("workplan");
		$list=$dao->where("$RANGE_STR and $DATE_STR")->order("CREATE_DATE desc")->findall();
		$daotype=D("plantype");
		$daodept=D("Department");
		$daouser=D("User");
		if($list){
		    foreach ($list as $ROW){
		    	
		    	
		    }
		}
		$this->assign("list",$list);
		
		$this->display();
	}
	
    /*----------------个人资料--------------*/
	public  function info(){//个人资料
        $dao=D("User");
        $row=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
	    $BIRTHDAY=$row["BIRTHDAY"];
	    $STR=strtok($BIRTHDAY,"-");
	    $row[BIR_YEAR]=$STR;
	    $STR=strtok("-");
	    $row[BIR_MON]=$STR;
	    $STR=strtok(" ");
	    $row[BIR_DAY]=$STR;        
		$this->assign("row",$row);
		$this->display();
	}
	public function submit(){
       $_POST[BIRTHDAY]=$_POST[BIR_YEAR]."-".$_POST[BIR_MON]."-".$_POST[BIR_DAY];
       $dao=D("User");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
       //保存当前数据对象
        if($result = $dao->where("USER_ID='$this->LOGIN_USER_ID'")->save()) { //保存成功
            $this->success('成功修改');
        }else { //失败提示
            $this->error('修改失败');
        }
	}
    
	
	public function passsubmit(){
		//-------------输入合法性检验-------------------------------------------------
		 $PASS0=$_POST[PASS0];
		 $PASS1=$_POST[PASS1];
		 $PASS2=$_POST[PASS2];
		 if(strlen($PASS1)<6||strlen($PASS2)<6||strlen($PASS1)>20||strlen($PASS2)>20){
		 	$this->error("密码长度应6-20位!");
		 }
		$dao=D("User");
		
		$row=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		
		if(md5($PASS0)!= $row[PASSWORD]){
          	$this->error("输入的原密码错误!");
        }
        
		/*
        if(crypt($PASS0,$row[PASSWORD])!= $row[PASSWORD]){
          	$this->error("输入的原密码错误!");
        }
        */
		if($PASS1!=$PASS2){
			$this->error("输入的新密码不一致！");
		}
		if(strstr($PASS1,"\'")!=false){
			$this->error("新密码中含有非法字符");			
		}
		if($PASS1==$PASS0){
			$this->error("新密码不能与原密码相同！");			
		}
		//---------------------修改-------------------
		//$PASS1=crypt($PASS1);
		$PASS1=md5($PASS1);
 		$CUR_TIME=date("Y-m-d H:i:s",time());
 		$data=array(
 		      "PASSWORD"=>$PASS1,
 		      "LAST_PASS_TIME"=>$CUR_TIME
 		);
 		$map="USER_ID='$this->LOGIN_USER_ID'";
 		if($resault=$dao->save($data,$map))
 		    $this->success("成功修改");
        else 
            $this->error("修改失败");
	}
	
	
}
?>