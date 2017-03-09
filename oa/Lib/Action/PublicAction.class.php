<?php
/**
 * PublicAction.class.php
 * 功能：公共方法模块。
 * */
import("ORG.Util.Page");
import("@.Util.mb");
import("@.Util.tree");
import("@.Util.xml");
import("ORG.Net.UploadFile");
class PublicAction extends Action{
	var $CUR_DATE;
	var $CUR_TIME;
	var $CUR_TIME_INT;
	var $LOGIN_USER_ID;
	var $LOGIN_USER_PRIV;
	var $LOGIN_DEPT_ID;
	var $LOGIN_DEPT_NAME;//中文部门名称
	var $LOGIN_POST_NAME;//岗位名称
	
	
	var $uploadpath;
    var $imgpath;
	var $_uid;
	
	var $onlineTime;
	
	var $avatar_width;
	var $avatar_height;
	function _initialize(){
		//print_r(ini_get_all()) ;
		//print_r(ini_get("date.timezone"));
		//ini_set("date.timezone","Etc/GMT-8");
        //print_r(ini_get_all());
		//$this->CUR_TIME_INT=time()-60*60*8;
		
		$this->CUR_TIME_INT=CUR_TIME_INT;
		$this->CUR_DATE=date("Y-m-d",$this->CUR_TIME_INT);
		$this->CUR_TIME=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
		
		
		$this->onlineTime=120;//2分钟掉线 大于 短信息的刷新时间就可以
		
		$this->avatar_width=30;//用户上传生成头象的宽度
		$this->avatar_height=40;
		
		$this->LOGIN_USER_ID=Session::get('LOGIN_USER_ID');//登录用户session值
		$this->LOGIN_USER_PRIV=Session::get('LOGIN_USER_PRIV');//登录用户等级值
		$this->LOGIN_DEPT_ID=Session::get("LOGIN_DEPT_ID");//登录用户部门值

		if (!$this->LOGIN_USER_ID&&$_REQUEST[a]!="login"&&$_REQUEST[a]!="checklogin") {
			header('Location: '.__APP__.'/Public/login');
		}

		$this->uploadpath=APP_PATH."/Uploads/";//上传文件目录
	    $this->imgpath="/oa/Uploads/";			
        $this->_uid=Session::get('LOGIN_UID');
        if ($this->LOGIN_USER_ID) {
			$this->LOGIN_USER_NAME =$this->USER_NAME();	 		
			$this->LOGIN_DEPT_NAME = $this->DEPT_NAME();
			$this->LOGIN_POST_NAME = $this->POST_NAME();
        }
		parent::_initialize();
	}
/*************************中文用户信息**************************/
	public function USER_NAME(){
		$user = new UserModel();
		$userRow = $user->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		return $userRow['USER_NAME'];
	}
/**
 +------------------------------------------------------------------------------
 *部门信息
 +------------------------------------------------------------------------------
 */
	public function DEPT_NAME(){
		$hrms = new HrmsModel();
		$hrmsRow = $hrms->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		return $hrmsRow['DEPARTMENT'];
	}
/**
 +------------------------------------------------------------------------------
 *岗位信息
 +------------------------------------------------------------------------------
 */
	public function POST_NAME(){
		$hrms = new HrmsModel();
		$hrmsRow = $hrms->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		return $hrmsRow['POST'];
	}
/************************3.3版本登录程序********************************/		
	public function login(){
		
	    if(!$_COOKIE['USER_ID_COOKIE']) {
	    	$focus="USERNAME";
	    }else{
	    	$focus="PASSWORD";
	    }
	    
	    $unit=$this->_unit();
	    //print_r($unit);
   	    $this->assign("indexTitle",$unit[UNIT_NAME]);  
   	    
	    $this->assign("focus",$focus);
	    $this->assign("USER_ID_COOKIE",$_COOKIE['USER_ID_COOKIE']);
	    $this->display();
	}
	
/************************登录验证程序********************************/			
	public function checklogin() {
		$USERNAME=trim($_POST[USERNAME]);
		if (!trim($_POST[USERNAME])) {
             $this->error("请输入用户名");			
		}
		$user = D("User");
		$authInfo = $user->where("USER_ID='$USERNAME'")->find();
		Cookie::set('USER_ID_COOKIE',$USERNAME,60*60*24*365);
		if(!$authInfo) {
            $this->error('用户名不存在');
         }else {
			if($authInfo['USER_ID']	!=  $USERNAME) {
				$this->assign('jumpUrl',"../Public/login"); 
				$this->error('帐号错误！');
			}
			elseif($authInfo['PASSWORD'] != md5($_POST['PASSWORD'])){
				$this->assign('jumpUrl',"../Public/login"); 
				$this->error('密码不正确！');
			}
			
		    if (($authInfo[DEPT_ID]==""||$authInfo[DEPT_ID]=="0")&&$authInfo[USER_ID]!="admin") {//离职人员不能登录
				$this->assign('jumpUrl',"../Public/login"); 
				$this->error('用户名不存在或已禁用！');		    		
		    }
			
			$user->setField("LAST_LOGIN_TIME",$this->CUR_TIME_INT,"USER_ID='$authInfo[USER_ID]'");//最后登录时间
			$user->setField('LOGIN_COUNT','(LOGIN_COUNT+1)',"USER_ID='$authInfo[USER_ID]'",false);//登录次数加1

			####设置session
            Session::set('LOGIN_USER_ID',$authInfo['USER_ID']);
            Session::set('LOGIN_UID',$authInfo['uid']);
            Session::set('LOGIN_PASSWORD',$authInfo['PASSWORD']);
			Session::set('LOGIN_USER_PRIV',$authInfo['USER_PRIV']);
			Session::set('LOGIN_DEPT_ID',$authInfo['DEPT_ID']);
			Session::set('LOGIN_AVATAR',$authInfo['AVATAR']);
			Session::set('LOGIN_SEX',$authInfo['SEX']);            
            Cookie::set('USER_ID_COOKIE',$authInfo['USER_ID'],60*60*24*365);			
			Cookie::set('USER_NAME_COOKIE',$authInfo['USER_NAME'],60*60*24*365);
			
			
			###session 表
			$sess = new userSessionAction();
			$sess->sess_gc();
			$sess->sess_write();
			
			###日常事务提醒
			affair_sms();
			
			$this->assign('jumpUrl',__APP__."/index/index"); 
            $this->success('登录成功!');
		}
	}
	
    
    /*--------------登出程序---COOKIE未作删除--------*/
    public function logout(){
    	        ###清除库  
				$sess = new userSessionAction();
				$sess->sess_dele();
			    ###		    	
				Session::set('LOGIN_UID','');
				Session::set('LOGIN_PASSWORD','');
	            Session::set('LOGIN_USER_ID','');
				Session::set('LOGIN_USER_PRIV','');
				Session::set('LOGIN_DEPT_ID','');
				Session::set('LOGIN_AVATAR','');
				Session::set('LOGIN_SEX','');            
	            //Cookie::set('USER_ID_COOKIE','',-60*60*24*365);			
				Cookie::set('USER_NAME_COOKIE','',-60*60*24*365);
				
				unset($_SESSION);
				session_destroy();
				session_unset();
				//清除聊天记录
				WebimChatAction::removeMyRecord();
                header('Location: '.'login');
    }
	public function _interface(){
		$dao = D('Interface'); 
		$face = $dao->find();
        return $face;        		
	}
	public function _unit(){
		$dao = D('Unit'); 
		$unit = $dao->find();
        return $unit;        		
	}
			
   public function _user_priv(){
   	  $dao=D("UserPriv");
   	  $map="USER_PRIV='$_SESSION[LOGIN_USER_PRIV]'";
   	  $priv=$dao->find($map,'*');
   	 return $priv;
   }

   public function _attch($ATTACHMENT_ID,$ATTACHMENT_NAME){ //公共显示附件 
   	
   	  if($ATTACHMENT_ID=="")
         $str="无附件";
      else
      {
         $ATTACHMENT_ID_ARRAY=explode(",",$ATTACHMENT_ID);
         $ATTACHMENT_NAME_ARRAY=explode("*",$ATTACHMENT_NAME);
         
         $ARRAY_COUNT=sizeof($ATTACHMENT_ID_ARRAY);
         //ECHO $ARRAY_COUNT;
         for($I=0;$I<$ARRAY_COUNT;$I++)
         {
            if($ATTACHMENT_ID_ARRAY[$I]=="")
               break;
            $dao=D("Attachments");
            $att=$dao->where("attid='$ATTACHMENT_ID_ARRAY[$I]'")->find();
            $ATTACH_SIZE=$att[filesize];
            $ATTACH_SIZE=number_format($ATTACH_SIZE,0, ".",",");

             $str.="<a href=\"/index.php/Attach/view/ATTACHMENT_ID/".$ATTACHMENT_ID_ARRAY[$I]."/ATTACHMENT_NAME/".urlencode($ATTACHMENT_NAME_ARRAY[$I])."\">".$ATTACHMENT_NAME_ARRAY[$I]."</a> (".$ATTACH_SIZE."字节)";
               $str.="<button onClick=\"delete_attach('".$ATTACHMENT_ID_ARRAY[$I]."','".$ATTACHMENT_NAME_ARRAY[$I]."');\">删除</button><br>";

         }//for
      }//else
      
   	   return $str;
   	   
   }
   
   public function _deleteattach($attid){
   	  $dao=D("Attachments");
   	  $att=$dao->where("attid='$attid'")->find();
   	  if($att){
   	  	@unlink($this->uploadpath.$att[attachment]);
   	  }
   	  $dao->where("attid='$attid'")->delete();
      return true; 
   }
   
   /* ----M转换成B--针对php.ini里的设置-------*/
   protected function MtoB($size){
   	
   	if (strpos($size,"M")) {
   		$size=str_replace("M","",$size);
   		return $size*1000*1000;
   	}
   	elseif (strpos($size,"K")) {
   		$size=str_replace("K","",$size);
   		return $size*1000;
   	}   	
   	else 
   	   return $size;
   	   
   }
	//执行单图上传操作
	public function _upload($path) {

		if(!checkDir($path)){
			return '目录创建失败: '.$path;
		}
		import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        //$upload->maxSize	=	'2000000' ;
        $upload->maxSize	=	$this->MtoB(ini_get("upload_max_filesize"));
        //设置上传文件类型
        //$upload->allowExts	=	explode(',',strtolower('jpg,gif,png,jpeg'));
		//存储规则
		$upload->saveRule	=	'uniqid';
		//设置上传路径
		$upload->savePath	=	$path;
        //执行上传操作
        if(!$upload->upload()) {
            //捕获上传异常
            //return $upload->getErrorMsg();
            $this->error("附件上传失败，附件最大允许：".ini_get("upload_max_filesize"));
            
            return false;//如果上传失败或是没有上传文件返回false;
        }else{
			//上传成功
			return $upload->getUploadFileInfo();
    	}
    }
    
	public function _makeimg($imganame,$imgdname,$twidth="100",$theight="100"){
		if($twidth>500)
	       $twidth=500;
   
		$id=imagecreatefromjpeg($imganame);
		$srcW=ImageSX($id);
		$srcH=ImageSY($id);
		
	    if ($twidth&&$theight) {
		   $dstW=$twidth;
		   $dstH=$theight;
	    }elseif ($twidth&&!$theight){
	       $HW=$srcH/$srcW;
			if($srcW>$twidth){
				$dstW=$twidth;
				$dstH=$twidth*$HW;
			}else{
			    $dstW=$srcW;
				$dstH=$srcH;
			}	       
	    }elseif(!$twidth&&$theight){
		   $WH=$srcW/$srcH;
			if($srcH>$theight){
				$dstH=$theight;
				$dstW=$theight*$WH;
			}else{
			    $dstW=$srcW;
				$dstH=$srcH;
			}
	    }	
	  $ni=imagecreatetruecolor($dstW,$dstH);
	  imageCopyResampled($ni,$id,0,0,0,0,$dstW,$dstH,$srcW,$srcH);
	  
      Imagejpeg($ni,$imgdname);
	  ImageDestroy($id);
	}
    
    /*-------------上传附件方法--返回id和name组成的数组------------*/
    public function updatefiles(){//
    	$arr=array();
		$info	=	$this->_upload($this->uploadpath);
		if ($info) {
			$data =$info[0];
				$data[addtime]=$this->CUR_TIME_INT;
				$data[filename]=$data[name];
				$data[attachment]=$data[savename];
				$data[filesize]=$data[size];
				$data[filetype]=$data[type];
				$dao = D("Attachments");
				$dao->create();
				$attid=$dao->add($data);
				if($attid){	
			        $arr[id]=$attid;
					$arr[name]=$data[name];
					$arr[attachment]=$data[savename];
			    }
		}
		
    	return $arr;
    }
    
	
		/*---------email ----实时短信--------*/
	public function send_sms($FROM_ID,$TO_ID,$SMS_TYPE,$CONTENT){ //只适合实时短信，日程提醒除外
		  $SEND_TIME=date("Y-m-d H:i:s",$this->CUR_TIME_INT);
		  $MY_ARRAY=explode(",",$TO_ID);
		  $ARRAY_COUNT=sizeof($MY_ARRAY);
		  $dao=D("Sms");
		  foreach ($MY_ARRAY as $to_id){
		  	    $data=array();
		  	  if($to_id){
		  	  	 $data[FROM_ID]=$FROM_ID;
		  	  	 $data[TO_ID]=$to_id;
		  	  	 $data[SMS_TYPE]=$SMS_TYPE;
		  	  	 $data[CONTENT]=$CONTENT;
		  	  	 $data[SEND_TIME]=$SEND_TIME;
		  	  	 $data[REMIND_FLAG]=1;
		         $dao->create(); 
		  	  	 $dao->add($data);
		  	  }
		  }
		  return true;
	}
	    
    
    public function overwrite_assoc_array($a, $b){
	    $c = $a;  
	    while(list($k,$v)=each($b)){
	        if(!is_array($v) || ( is_array($v) && count($v) > 0 )){
	        	$c[$k] = $v;
	        }
	    }
	    return $c;
	} // end overwrite_assoc_array()
	
	//------ 多级部门下拉菜单，支持按管理范围列出 --------修改时间：12月19日-------------
	public function my_dept_tree($DEPT_ID,$DEPT_CHOOSE,$POST_OP){
		global $DEEP_COUNT;
		if($DEEP_COUNT=="")
			$DEEP_COUNT="|";
		$department = new DepartmentModel();
		$query = $department->where("DEPT_PARENT=$DEPT_ID")->order('DEPT_NO')->findAll();
		$OPTION_TEXT="";
		$DEEP_COUNT1=$DEEP_COUNT;
		$DEEP_COUNT.="　|";
		if($query){
			foreach($query as $k=>$v){
				$COUNT++;
				$DEPT_ID=$v["DEPT_ID"];
				$DEPT_NAME=$v["DEPT_NAME"];
				$DEPT_PARENT=$v["DEPT_PARENT"];
		
				$DEPT_NAME=str_replace("<","&lt",$DEPT_NAME);
				$DEPT_NAME=str_replace(">","&gt",$DEPT_NAME);
				$DEPT_NAME=stripslashes($DEPT_NAME);
		
				if($POST_OP==1)
					$DEPT_PRIV=$this->is_dept_priv($DEPT_ID);
				else
					$DEPT_PRIV=1;
		
				$OPTION_TEXT_CHILD=$this->my_dept_tree($DEPT_ID,$DEPT_CHOOSE,$POST_OP);
		
				if($DEPT_PRIV==1){
					$OPTION_TEXT.="<option ";
					if($DEPT_ID==$DEPT_CHOOSE)
						$OPTION_TEXT.="selected ";
					$OPTION_TEXT.="value=$DEPT_ID>".$DEEP_COUNT1."─".$DEPT_NAME."</option>\n";
				}
				if($OPTION_TEXT_CHILD!="")
					$OPTION_TEXT.=$OPTION_TEXT_CHILD;
			}
		}//while
		$DEEP_COUNT=$DEEP_COUNT1;
		return $OPTION_TEXT;
	}
//-- 查看$DEPT_ID是否属于本人管理范围 --
	public function is_dept_priv($DEPT_ID){
		$user = new UserModel();
		$ROW = $user->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		if($ROW){
		$POST_PRIV=$ROW["POST_PRIV"];
		$POST_DEPT=$ROW["POST_DEPT"];
		}
		if($POST_PRIV==0 && $DEPT_ID!=$this->LOGIN_DEPT_ID && !is_dept_parent($DEPT_ID,$this->LOGIN_DEPT_ID))
			$DEPT_PRIV=0;
		else if($POST_PRIV==2){
			$DEPT_PRIV=0;
			$MY_ARRAY=explode(",",$POST_DEPT);
 			$ARRAY_COUNT=sizeof($MY_ARRAY);
			if($MY_ARRAY[$ARRAY_COUNT-1]=="")
				$ARRAY_COUNT--;
			for($I=0;$I<$ARRAY_COUNT;$I++){
				if(is_dept_parent($DEPT_ID,$MY_ARRAY[$I]) || $MY_ARRAY[$I]==$DEPT_ID){
 					$DEPT_PRIV=1;
 					break;
				}
			}
		}else
			$DEPT_PRIV=1;

		return $DEPT_PRIV;
	}	
//-- 递归求解PARENT_ID是否是DEPT_ID的父节点 --
	public function is_dept_parent($DEPT_ID,$PARENT_ID){
		$department = new DepartmentModel();
		$ROW = $department->where("DEPT_ID=$DEPT_ID")->find();
  		if($ROW){
  			$DEPT_PARENT=$ROW["DEPT_PARENT"];
			if($DEPT_PARENT==0)
				return 0;
			else if($DEPT_PARENT==$PARENT_ID)
				return 1;
			else
				return is_dept_parent($DEPT_PARENT,$PARENT_ID);
  		}
	}
//--判断值是否存在--
	/*
	0=>数字，1=>字符串
	*/
	public function is_value($id,$c){
		//判断是否为空
		if(empty($id)){
			return exit;
		}
		switch ($c){
			case 0;
				$a =(is_int($id))?'dds':'exit;';
			
			case 1;
			if(is_string($id)){
				return;
			}	
		}
		return $a;
	}		       

	/**
	 * Select the users of pop windows
	 */
	protected function _popSelectUser()
	{
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
	}
}

?>