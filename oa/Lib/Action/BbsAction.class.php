<?php
/**********************************
 *       在线模块
 * author:廖秋虎  time:08/12/22
 **********************************/
Class BbsAction extends PublicAction{


  function index(){
    $this->assign("username",$this->LOGIN_USER_ID);
    $this->assign("password",Session::get('LOGIN_PASSWORD'));

    $this->display();
  }

}
?>