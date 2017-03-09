<?php
import("ORG.Util.Page");
import("@.Util.mb");
import("@.Util.tree");
class PublicAction extends Action{
	var $CUR_DATE;
	var $CUR_TIME;
	var $CUR_TIME_INT;
	var $LOGIN_USER_ID;
	var $LOGIN_USER_PRIV;
	VAR $LOGIN_DEPT_ID;

	var $uploadpath;

	var $MODULES_DIR;
	var $THEMES_DIR;
	var $WALLPAPERS_DIR;
	var $_uid;

	function _initialize(){
		$this->CUR_DATE=date("Y-m-d",time());
		$this->CUR_TIME=date("Y-m-d H:i:s",time());
		$this->CUR_TIME_INT=time();
		$this->LOGIN_USER_ID=Session::get('LOGIN_USER_ID');//登录用户session值
		$this->LOGIN_USER_PRIV=Session::get('LOGIN_USER_PRIV');//登录用户等级值
		$this->LOGIN_DEPT_ID=Session::get("LOGIN_DEPT_ID");//登录用户部门值

		if (!$this->LOGIN_USER_ID&&$_REQUEST[a]!="login"&&$_REQUEST[a]!="checklogin") {
			header('Location: '.__APP__.'/Public/login');
		}

		$this->uploadpath=APP_PATH."/Uploads/";//上传文件目录
        $this->_uid=Session::get('LOGIN_UID');
    	$this->MODULES_DIR='MOD_PATH'.'/';
		$this->THEMES_DIR='DESKTOP'.'/resources/themes/';
		$this->WALLPAPERS_DIR='DESKTOP'.'/resources/wallpapers/';


		parent::_initialize();
	}
/************************3.3版本登录程序********************************/
	public function login(){
	    if(!$_COOKIE['USER_ID_COOKIE']) {
	    	$focus="USERNAME";
	    }else{
	    	$focus="PASSWORD";
	    }
	    $this->assign("focus",$focus);
	    $this->assign("USER_ID_COOKIE",$_COOKIE['USER_ID_COOKIE']);

	   // $face=$this->_interface();
	   // $this->assign("face",$face);
	    $this->display();
	}

	public function loginnew(){
	    if(!$_COOKIE[USER_ID_COOKIE]) {
	    	$focus="USERNAME";
	    }else{
	    	$focus="PASSWORD";
	    }
	    $this->assign("focus",$focus);
	    $this->assign("USER_ID_COOKIE",$_COOKIE[USER_ID_COOKIE]);
	    $face=$this->_interface();
	    $this->assign("face",$face);
	    $this->display();
	}
/************************3.3版本登录验证程序********************************/
	public function checklogin() {

        //论坛用户
		$disuser=D("disUsers");
		$disinfo=$disuser->where("username='$_POST[USERNAME]'")->find();

		$user = D("User");
		$authInfo = $user->where("USER_ID='$_POST[USERNAME]'")->find();
		//crypt($_POST['PASSWORD'],$authInfo['PASSWORD']);
		if(!$authInfo) {
            $this->error('用户名不存在或已禁用！');
         }else {
			if($authInfo['USER_ID']	!=  $_POST['USERNAME']) {
				$this->assign('jumpUrl',"../Public/login");
				$this->error('帐号错误！');
			}
			elseif($authInfo['PASSWORD'] != md5($_POST['PASSWORD'])){
				$this->assign('jumpUrl',"../Public/login");
				$this->error('密码或账号不匹配！');
			}
			####更新最后活动时间

			$user->setField("LAST_VISIT_TIME",$this->CUR_TIME_INT,"USER_ID='$authInfo[USER_ID]'");
			$user->setField("LAST_LOGIN_TIME",$this->CUR_TIME_INT,"USER_ID='$authInfo[USER_ID]'");
			$user->setField("LOGIN_COUNT","LOGIN_COUNT+1","USER_ID='$authInfo[USER_ID]'");

			$user->save();
			####设置session
            Session::set('LOGIN_USER_ID',$authInfo['USER_ID']);
            Session::set('LOGIN_UID',$authInfo['uid']);
            Session::set('LOGIN_PASSWORD',$authInfo['PASSWORD']);
			Session::set('LOGIN_USER_PRIV',$authInfo['USER_PRIV']);
			Session::set('LOGIN_DEPT_ID',$authInfo['DEPT_ID']);
			Session::set('LOGIN_AVATAR',$authInfo['AVATAR']);
			Session::set('LOGIN_THEME',$authInfo['THEME']);
            //Cookie::set('USER_ID_COOKIE',$authInfo['USER_ID'],60*60*24*365);
			//Cookie::set('USER_NAME_COOKIE',$authInfo['USER_NAME'],60*60*24*365);
			//论坛cookie
		    $cookie_name = 'punbb_cookie';
			$cookie_domain = '';
			$cookie_path = '/';
			$cookie_secure = 0;
			$cookie_seed = 'b7da9fb6c2b80b5e';
			Cookie::set($cookie_name,serialize(array($disinfo['id'], md5($cookie_seed.md5($_POST['PASSWORD'])))), 60*60*24*365, $cookie_path, $cookie_domain);
			//聊天室cookie

				session_register('username');

				session_register('hash');
			//Cookie::set('username',$authInfo['USER_ID'],60*60*24*365);
			//Cookie::set('hash',$authInfo['PASSWORD'],60*60*24*365);
			//var_dump($_COOKIE);exit();
			$this->assign('jumpUrl',"../index/index");
            $this->success('登录成功!');
		}
	}


    /*--------------登出程序---COOKIE未作删除--------*/
    public function logout(){

				Session::set('LOGIN_UID','');
				Session::set('LOGIN_PASSWORD','');
	            Session::set('LOGIN_USER_ID','');
				Session::set('LOGIN_USER_PRIV','');
				Session::set('LOGIN_DEPT_ID','');
				Session::set('LOGIN_AVATAR','');
				Session::set('LOGIN_THEME','');
	            //Cookie::set('USER_ID_COOKIE','',-60*60*24*365);
				//Cookie::set('USER_NAME_COOKIE','',-60*60*24*365);

				//注销聊天室

				session_unregister('username');
				//Cookie::set('inchat','',-60*60*24*365);

				//Cookie::set('username','',-60*60*24*365);
				//Cookie::set('hash','',-60*60*24*365);
				//var_dump($_COOKIE);exit();
				//注销论坛
			    $cookie_name = 'punbb_cookie';
				$cookie_domain = '';
				$cookie_path = '/';
				$cookie_secure = 0;
				$cookie_seed = 'b7da9fb6c2b80b5e';
				Cookie::set($cookie_name,'',-60*60*24*365, $cookie_path, $cookie_domain, $cookie_secure);
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
   	  $dao=D("userPriv");
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
            $dao=D('Attachments');
            $att=$dao->where("attid='$ATTACHMENT_ID_ARRAY[$I]'")->find();
            $ATTACH_SIZE=$att[filesize];
            $ATTACH_SIZE=number_format($ATTACH_SIZE,0, ".",",");

             $str.="<a href=\"/index.php/attach/view/ATTACHMENT_ID/".$ATTACHMENT_ID_ARRAY[$I]."/ATTACHMENT_NAME/".urlencode($ATTACHMENT_NAME_ARRAY[$I])."\">".$ATTACHMENT_NAME_ARRAY[$I]."</a> (".$ATTACH_SIZE."字节)";

           if(stristr($ATTACHMENT_NAME_ARRAY[$I],".doc")||stristr($ATTACHMENT_NAME_ARRAY[$I],".ppt")||stristr($ATTACHMENT_NAME_ARRAY[$I],".xls"))
           {
				$str.="<input type='button' value='阅读' class='SmallButton' onClick=\"window.open('/index.php/attach/read/ATTACHMENT_ID/".($ATTACHMENT_ID_ARRAY[$I])."/ATTACHMENT_NAME/".urlencode($ATTACHMENT_NAME_ARRAY[$I])."/OP=5',null,'menubar=0,toolbar=0,status=1,scrollbars=1,resizable=1');\">&nbsp;";

				$str.="<input type=button value=编辑 class=SmallButton onClick=\"window.open('/index.php/attach/read/ATTACHMENT_ID/".($ATTACHMENT_ID_ARRAY[$I])."/ATTACHMENT_NAME/".urlencode($ATTACHMENT_NAME_ARRAY[$I])."/OP=4',null,'menubar=0,toolbar=0,status=1,scrollbars=1,resizable=1');\">&nbsp;";
           }

               $str.="<input type=button value=删除 class=SmallButton onClick=\"delete_attach('".$ATTACHMENT_ID_ARRAY[$I]."','".$ATTACHMENT_NAME_ARRAY[$I]."');\">(".$ATTACH_SIZE."字节)<br>";

         }//for
      }//else

   	   return $str;

   }

   public function _deleteattach($attid){
   	  $dao=D('Attachments');
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

    /*-------------上传附件方法--返回id和name组成的数组------------*/
    public function updatefiles(){//
    	$arr=array();
		$info	=	$this->_upload($this->uploadpath);
		if ($info) {
			$data =$info[0];
				$data[addtime]=time();
				$data[filename]=$data[name];
				$data[attachment]=$data[savename];
				$data[filesize]=$data[size];
				$data[filetype]=$data[type];
				$dao = D('Attachments');
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
		  $SEND_TIME=date("Y-m-d H:i:s",time());
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
}

?>