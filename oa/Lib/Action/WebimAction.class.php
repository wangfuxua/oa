<?php
import("ORG.Util.HashMap");
class WebimAction extends PublicAction {
	
	public  function _initialize(){
		parent::_initialize();
	}
	
	public function Index(){
		$this->assign("my_name",$this->LOGIN_USER_ID); // 我显示得用户名

		$this->assign('use_webim',1);
		$this->assign('chat_conf_close',0);// 是否关闭时时检测未开启的对话框消息
		$this->assign('online_num',0);//在线好友数量
		$this->display("webim:index");
	}
}
?>