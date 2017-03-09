<?php
/**********************************
 *       新闻管理模块
 * author:廖秋虎  time:08/11/18
 **********************************/
class NewsAction extends PublicAction {
/*****************************入口程序*************************************************/
	public function index(){
		$news = new NewsModel();
		$map="";
		$count=$news->count($map);
        import("ORG.Util.Page");	
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '20';
		}
		$p= new Page($count,$listRows);
		//$list = $news->top5('','*','newId desc');
        $list = $news->where($map)->limit("$p->firstRow,$p->listRows")->order("newId desc")->findAll();  		
		$page       = $p->show();
		$this->assign("page",$page);
		$this->assign('list',$list);
		$this->assign('curtitle',"新闻管理");
		$this->display();	
	}
/*****************************发布程序*************************************************/
	public function create(){
		$this->display();
	}
/*****************************处理新表单*************************************************/
	public function insert(){
		
	    if (empty($_POST[content])) {
			$this->error("请输入内容");
		}
		$_POST['PROVIDER'] = $this->LOGIN_USER_ID;
		$_POST['createTime'] = $this->CUR_TIME_INT;
		$news = new NewsModel();
		
		if (false===$news->create()) {
			$this->error($news->getError());
		}else {
			$news->add();
			$this->assign('jumpUrl',__URL__."/index");
			$this->success("成功添加");
		}

	}
/*****************************删除程序*************************************************/
	public function del(){
		$news = new NewsModel();
		$news->find($_GET['id']);
		$news->delete();
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('删除成功!');
	}
	public function delAll(){
		$news = new NewsModel();
		$news->delete();
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('全部删除成功!');
	}
/*****************************更新程序*************************************************/
	public function upload(){
		$news = new NewsModel();
		if($_POST){
			$news->find($_POST[newId]);
			$news->title = $_POST[title];
			$news->content = $_POST[content];
			$news->save();
			$this->redirect();
		}elseif(isset($_GET[id])){
			$article = $news->find($_GET['id']);
			$this->assign('article',$article);
			$this->display();
		}
	}
/*****************************内容展示程序*************************************************/
	public function article(){
		$news = new NewsModel();
		//$news->setField('CLICK_COUNT','(CLICK_COUNT+1)','newId='.$_GET[id],false);//也可以
		//
		$news->setInc('CLICK_COUNT','newId='.$_GET[id]);
		
		$article = $news->where("newId='$_GET[id]'")->find();
		//echo $news->getLastSql();
		$this->assign('article',$article);
		$this->display();
	}
	
}
?>
