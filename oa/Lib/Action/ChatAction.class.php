<?php
/**********************************
 *       在线模块
 * author:廖秋虎  time:08/12/22
 **********************************/
Class ChatAction extends PublicAction{


  function index(){
    $username=Cookie::get('USER_NAME_COOKIE');
    $username=urlencode($username);
    $this->assign("username",$username);
    $this->assign("password",Session::get('LOGIN_PASSWORD'));
    $this->display();
  }

}
?>