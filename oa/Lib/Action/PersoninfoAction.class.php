<?php
//import("@.Action.PublicAction");
class PersoninfoAction extends PublicAction {
    	var $uploadpath_avatar;
	function _initialize(){
		$this->curtitle="个人设置";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->uploadpath_avatar=APP_PATH."/Tpl/default/Public/images/avatar/";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	/*----------------个人资料--------------*/
	public function index(){
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
    /*----------------个人设置 --------------*/
	public function set(){//界面主题未显示
        $dao=D("User");
        $row=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$this->assign("row",$row);
		$LOGIN_AVATAR=Session::get('LOGIN_AVATAR');
		
		$this->assign("LOGIN_AVATAR",$LOGIN_AVATAR);
		$this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID);
		$this->display();        		
	}
	public function setsubmit(){
	   $ATTACHMENT_NAME=$_POST[ATTACHMENT_NAME];
	   if($ATTACHMENT_NAME!=""){//上传自定义头像
		   $ATTACHMENT_NAME=str_replace("\'","’",$ATTACHMENT_NAME);
		   $filename=$_FILES[ATTACHMENT][name];
		   $pathinfo = pathinfo($filename);
		   $FILENAME=$this->uploadpath_avatar.$this->LOGIN_USER_ID.".".strtolower($pathinfo['extension']);
		   //echo $FILENAME;
		   copy($_FILES[ATTACHMENT][tmp_name],$FILENAME);
		   unlink($_FILES[ATTACHMENT][tmp_name]);
		   //$this->_makeimg($FILENAME,$FILENAME,$this->avatar_width,$this->avatar_height);
		   
		   Session::set("LOGIN_AVATAR",$this->LOGIN_USER_ID);
		   $_POST[AVATAR]=$this->LOGIN_USER_ID;
		   if(!file_exists($FILENAME)){
                 $this->error("附件上传失败");
		   }
		   
		}
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
   public function avatardelete(){//删除自定义头像
		$FILENAME=$this->uploadpath_avatar.$this->LOGIN_USER_ID.".gif";
		unlink($FILENAME);
		$dao=D("User");
		$dao->setField("AVATAR","1","USER_ID='$this->LOGIN_USER_ID'");
		
		Session::set("LOGIN_AVATAR","1");
		$this->redirect("set","Personinfo");
   }
        /*----------------个人网址 --------------*/
   public function url(){//个人网址 （添加和列表）
   	    $dao=D("Url");
   	    $map=array("USER"=>$this->LOGIN_USER_ID);
   	    //$map="USER='$this->LOGIN_USER_ID'";
   	    $count=$dao->count($map);
   	    if ($count>0) {
   	    		import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p          = new Page($count,$listRows);
				$list=$dao->where($map)->order("URL_NO")->limit("$p->firstRow,$p->listRows")->findall();
                //分页跳转的时候保证查询条件		
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
    public function urledit(){//修改网址信息
			$URL_ID=$_REQUEST[URL_ID];
		    $dao=D("Url");
		    $row=$dao->where("URL_ID='$URL_ID'")->find();
		    
		    $this->assign("row",$row);
		    $this->display();
    }
    
	public function urlsubmit(){//提交信息（添加和修改）
		    $URL_ID=$_REQUEST[URL_ID];
		    
		    $dao=D("Url");
		    $_POST[USER]=$this->LOGIN_USER_ID;
		    if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        }
	        if($URL_ID){
	        	if ($re=$dao->where("URL_ID='$URL_ID'")->save()) {
	        		$this->assign("jumpUrl","url");
                   	$this->success("成功修改");	
                 }else{
                 	$this->error("修改失败");			
                 }
	        }else {
		        $id = $dao->add();
		        if ($id) {
		        	$this->assign("jumpUrl","url");
		           $this->success("成功添加");			
		        }else {
		        	$this->error("添加失败");			
		        }	        	
	        	
	        }
	}
	
	public function urldelete(){//删除（删除单个或是全部）
			$URL_ID=$_REQUEST[URL_ID];
		    $dao=D("Url");
		    if ($URL_ID) {
		    	$dao->where("URL_ID='$URL_ID'")->delete();
		    }else{
		    	$dao->where("USER='$this->LOGIN_USER_ID'")->delete();
		    } 
		   $this->redirect("url","Personinfo"); 
		   //$this->success("成功删除");
	}
	
	/*----------------修改密码 --------------*/
	public function pass(){
		$dao=D("User");
		$daopara=D("SysPara");
		
		$row=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		if($row[LAST_PASS_TIME]=="0000-00-00 00:00:00")
            $row[LAST_PASS_TIME]="";
		
        $list=$daopara->where("PARA_NAME='SEC_PASS_FLAG' or PARA_NAME='SEC_PASS_TIME'")->findall();
        foreach ($list as $ROW){
			   $PARA_NAME=$ROW["PARA_NAME"];
			   $PARA_VALUE=$ROW["PARA_VALUE"];
			   if($ROW[PARA_NAME]=="SEC_PASS_FLAG")
			      $SEC_PASS_FLAG=$PARA_VALUE;
			   else if($ROW[PARA_NAME]=="SEC_PASS_TIME")
			      $SEC_PASS_TIME=$PARA_VALUE;
        }
        if($SEC_PASS_FLAG=="1"){
        	$REMARK="您的密码将于 <span class=big4><b>".($SEC_PASS_TIME-floor(($this->CUR_TIME_INT-strtotime($row[LAST_PASS_TIME]))/24/3600))."</span> </b>天后过期。";
        }else{
        	$REMARK="密码永不过期";
        }
        $this->assign($REMARK,$REMARK);
		$this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID);
		$this->assign("row",$row);
		
		$this->display();
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
 		$CUR_TIME=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
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