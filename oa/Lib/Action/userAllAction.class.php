<?php
/**********************************
 *       全部人员模块
 * author:廖秋虎  time:08/12/22
 **********************************/
class userAllAction extends UserAction {
	public function index(){
		$this->display();
		}
	public function userInfo(){
		$user = new UserModel();
		$user->where("USER_ID='$_GET[USER_ID]'")->find();
		$user->state=($this->CUR_TIME_INT-$user->LAST_VISIT_TIME)<65?'在线':'不在线';
		$user->SEX = ($user->SEX ==0)?'男':'女';
		$this->assign('user',$user);
		$this->display();
	}		
}

?>
