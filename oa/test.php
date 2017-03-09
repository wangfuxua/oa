<?
//定义ThinkPHP框架路径
define('THINK_PATH','../ThinkPHP');
//定义项目名称和路径
define('APP_NAME','thinkoa');
define('APP_PATH','.');
//加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");
//实例化一个网站应用实例
class IndexAction extends Action {
	public function Index(){
		$user = new userModel();
		$list = $user->findAll();
		dump($list);
	}
}

?>