<?php
import("ORG.Util.HashMap");
class WebimChatAction extends PublicAction {
	var $_uid;
	public  function _initialize(){
		parent::_initialize();

		$this->_uid=Session::get("LOGIN_UID");
		//$this->LOGIN_USER_ID
		//Cookie::get('USER_NAME_COOKIE');
		$this->REAL_NAME = Cookie::get('USER_NAME_COOKIE');
	}
	public function sendMsg(){
		//插入数据
        if ($_REQUEST[send]) {
        	$msg = $_POST['msg'];
        	/*
			$msg = isset($_POST['msg']) ? rtrim($_POST['msg']):'';
			$msg = h($_POST['msg']);
			$msg = preg_replace("/\[em:(.+?):]/is", "<img src=\"".APP_PUBLIC_URL."/img/face/\\1.gif\" class=\"face\">", $msg);//替换表情
			$msg = preg_replace("/\<br.*?\>/is", ' ', $msg);
			$msg = str_replace("\'","&apos;",$msg);
			*/
			$to_user_name = $_POST['to_user_name'] ? h($_POST['to_user_name']):'';
			
			$dao = D("WebimChat");
			$dao->uid = $this->_uid;
			$dao->user_name = $this->LOGIN_USER_ID;
			$dao->to_uid = $to_uid;
			$dao->to_user_name = $to_user_name?$to_user_name:'';
			$dao->msg = $msg;
			$dao->dis_time = date("H:i:s");
			$dao->flag_new = 1;
			$dao->cre_time = $this->CUR_TIME_INT;
			
			$id=$dao->add();
			if (!$id) {
			   $re=array("flag"=>$dao->getlastsql());
			}else{
			   $re=array("flag"=>0);
			}
			
			echo json_encode($re);
    	}
    	//显示数据
		$dao	=	D("WebimChat");
		
		$listmsg=$dao->where("(user_name='$this->LOGIN_USER_ID' and to_user_name='$_REQUEST[to_user_name]') or (user_name='$_REQUEST[to_user_name]' and to_user_name='$this->LOGIN_USER_ID')")->order("cre_time")->findall();
		
		$dao->setField("flag_new",0,"to_user_name='$this->LOGIN_USER_ID' and user_name='$_REQUEST[to_user_name]'");
		
		$this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID);
		
		$this->assign("listmsg",$listmsg);
		
		$this->display("listmsg");
		
	}
	/*-------------清除登录用户的聊天记录------------*/ 
	public function removeMyRecord(){
		$dao=D("WebimChat");
		$dao->where("user_name='$this->LOGIN_USER_ID' or to_user_name='$this->LOGIN_USER_ID'")->delete();
	}
	
	/*
	public function refresh(){
		$user_str=$_POST['uArray'];//当前聊天非激活状态用户
		//$user_act=$_POST['action'];//当前聊天激活状态用户
		//$map="to_user_name='$this->LOGIN_USER_ID' and user_name='$value' and cre_time>(time()-5)";
		$array=explode(",",$user_str);
		$dao	=	D('WebimChat');
		$re=array();
		foreach($array as $key=>$value){
		   if($value){
		   	  $map="to_user_name='$this->LOGIN_USER_ID' and user_name='$value' and flag_new=1";
		   	  $count=$dao->count($map);
		   	  if ($count){
		           $re[]=array("uid"=>$value);
		   	  }
		   }
		} 
		$back=array(
		"list"=>$re
		);
		echo json_encode($back);		
		
		
	}
	*/
	public function refresh(){
        $dao=D("User");
        $list=$dao->findall();
		$dao	=	D('WebimChat');
		$re=array();
        foreach ($list as $row){
        	 if ($row[USER_ID]){
		   	  $map="to_user_name='$this->LOGIN_USER_ID' and user_name='$row[USER_ID]' and flag_new=1";
		   	  $count=$dao->count($map);
		   	  if ($count){
		           $re[]=array("uid"=>$row[USER_ID],"uname"=>$row[USER_NAME]);
		   	  }
        	 }
        }
        
		$back=array(
		"list"=>$re
		);
		echo json_encode($back);		
	}

	
	/**
	 * 获取聊天内容
	 * 获取对方发送过来得聊天内容
	 *
	 */
	public function receiveMsg(){
		$dao	=	D('WebimChat');

		$msgs = array();
		$from_uids = isset($_POST["id"]) && is_array($_POST["id"])?$_POST["id"]:array();
		if(empty($from_uids)){
			return false;
		}

		foreach ($from_uids as $from_uid){
			if(is_number($from_uid) && $from_uid>0){
				$list = $dao->where("uid='$from_uid' and to_uid='$this->_uid' and flag_new=1")->findAll();

				if($list && is_array($list) && !empty($list)){
					$sql = "UPDATE `webimchat` SET flag_new = 0 WHERE `uid`='$from_uid' and to_uid='$this->_uid' and flag_new=1";
					$dao->execute($sql);
					foreach ($list as $onemsg){
						$tmp_msgs = array();
						$tmp_msgs['id'] = $from_uid;
						$tmp_msgs['msg'] = $onemsg['msg'];
						array_push($msgs,$tmp_msgs);
					}
				}
			}
		}

		if(!empty($msgs)){
			echo json_encode($msgs);
		}
	}

	public function receiveRecord(){
		$to_uid 	=	isset($_POST['to_uid']) ? intval($_POST['to_uid']):0;
		$list = array();
		if($to_uid<1){

			return false;
		}
		$dao = D('WebimChat');
		$condition = "((uid = $this->_uid and to_uid='$to_uid') or (uid='$to_uid' and to_uid=$this->_uid))";
		$list = $dao->findAll($condition,'id,uid,msg,dis_time','cre_time desc','10');

		$sql = "UPDATE `webimchat` SET flag_new = 0 WHERE uid='$to_uid' and to_uid='$this->_uid' and flag_new=1";
		$dao->execute($sql);

		$del_id = $list[9]->id;
		$sql = "DELETE FROM `webimchat` WHERE (uid = '$this->_uid' and to_uid='$to_uid') or (uid='$to_uid' and to_uid='$this->_uid') and flag_new=0 and id<$del_id";
		$dao->execute($sql);

		if(!empty($list)){
			echo json_encode($list);
		}
	}

	//清除聊天记录
	public function delRecord(){
		$uid  = 	isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($uid<1){
			return false;
		}
		$dao	=	D('WebimChat');
		$sql = "delete from `webimchat` WHERE (uid = '$uid' and to_uid='$this->_uid') or (uid = '$this->_uid' and to_uid='$uid')";
		echo $dao->execute($sql);
	}

	public function getOnlineFriends() {
		$dao=D("user");
		$list=$dao->where("uid!='$this->_uid'")->findAll();
		$Users = array();
		if($list){
			for($i=0;$i<count($list);$i++){
				$Users[$i]['uid'] = $list[$i]['uid'];
				$Users[$i]['user_face'] = 'http://'.$_SERVER['HTTP_HOST'].APP_PUBLIC_URL.'/img/noface2.gif';
				$Users[$i]['disply_name'] = $list[$i]['USER_ID'];
			}
		}
		if(!empty($Users)){
			echo json_encode($Users);
		}
	}

	// 检测是否有新消息
	function checkNoOpenChat(){
		$openId = "'";
		for($i=0;$i<count($_POST['id']);$i++){
			$openId .= $_POST['id'][$i];
		}
		$openId .= "'";
		$dao	=	D("WebimChat");
		$map	=	new HashMap();
		$map->put("to_uid",$this->_uid);
		$map->put("uid",array("NOT IN",$openId));
		$map->put("flag_new",array("IN",'1,2'));
		$list	=	$dao->findAll($map);
		if($list){
			for($i=0;$i<count($list);$i++){
				$Users[$i]['uid'] = $list[$i]['uid'];
				$Users[$i]['user_face'] = 'http://'.$_SERVER['HTTP_HOST'].APP_PUBLIC_URL.'/img/noface2.gif';
				$Users[$i]['disply_name'] = $list[$i]['user_name'];
			}
		}
		echo json_encode($Users);
	}

	function checkMsg() {
		$msg = array();
		$msg['flag'] = 0;

		$lastOnCount = $_POST['lastOnCount'];

		/*$dao	=	D("Online");
		$friends	=	getUserFriends($this->mid);
		$cond['userId']	=	array('in',$friends);
		$cond['status']	=	0;
		$onlineCount = $dao->count($cond);
		//如果好友数比显示在页面上的多，则有“好友上线消息提示”
		if($onlineCount > $lastOnCount){
		$result = $dao->getN(0,$cond); //查找最后一个上线的好友
		$msg["flag"] = 3;
		$msg["info"] = getUserName($result->userId);
		}*/
		if($msg['flag'] != 0){
			echo json_encode($msg);
		}
	}

	public function Index(){

	}
}
?>