<?
class WebMailSetModel extends Model {
	var $tableName="webmail_set";
	
	var $_validate=array(
	           array("email","email","邮箱地址不正确","ALL"),
	           array("popserver","require","邮件服务器不能为空","ALL"),
	           array("popport","require","服务器端口不能为空","ALL"),
	           array("popuser","require","登录用户名不能为空","ALL"),
	           array("poppass","require","登录密码不能为空","ALL")
	);
	
	
}


?>