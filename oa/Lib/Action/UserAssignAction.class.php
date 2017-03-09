<?php
class UserAssignAction extends PublicAction {
	
	public function create(){
		if($_GET['id']){ // 更新操作
			if(!$_GET['id']) $this->error('链接错误!');
			$id = $_GET['id'];
			$assign = D('UserAssign');
			$result = $assign->find($id);
			$this->assign('assign_res', $result);
		}

		$this->_popSelectUser();

		$this->display();
	}
	
	public function save(){
		if(!$_POST['manager_id'] || !$_POST['assign_user_id']) $this->error("创建失败,请填写完整信息!");
		$manager_id = trim(ereg_replace('\s*,$', '', $_POST['manager_id'])); // $_POST['manager_id'] 为1,2,形式
		$manager_name = trim(ereg_replace('\s*,$', '', $_POST['manager_name'])); // $_POST['manager_name'] 为1,2,形式
		
		$assign_user_id = trim($_POST['assign_user_id']); 
		$assign_user_name = trim($_POST['assign_user_name']); 
		
		$manager_id_arr = explode(",", $manager_id); 
		$manager_name_arr = explode(",", $manager_name); 
		$assign = D('UserAssign');
		$data = array();
		foreach($manager_id_arr as $key => $value){
			$data[$key]['manager_id'] = $value;
			$data[$key]['manager_name'] = $manager_name_arr[$key];
			$data[$key]['assign_user_id'] = $assign_user_id;
			$data[$key]['assign_user_name'] = $assign_user_name;
			if(!$assign->where("manager_id=$value")->find()){ // 如果没有重复记录则添加
				$assign->create($data[$key]);
				$assign->add();
			}
		}
		$this->redirect('lists');
	}
	
	public function update(){
		if(!$_POST['current_id']) $this->error('更新失败!');
		$id = trim($_POST['current_id']);
		$assign = D('UserAssign');
		$assign->create();
		$assign->where("id=$id")->save();
		$this->redirect('lists');
	}
	
	public function lists(){
		$assign = D('UserAssign');
		$count = $assign->count();		
		$rows	=	20;
		$p		=	new Page($count,$rows);
		$list = $assign->query("select d.id as id,USER_ID,USER_NAME,DEPT_NAME,PRIV_NAME from user a, department b,user_priv c,user_assign d where d.manager_id=a.uid and a.DEPT_ID=b.DEPT_ID and a.USER_PRIV=c.USER_PRIV order by b.DEPT_NAME desc,c.PRIV_NAME desc limit ".$p->firstRow.",".$p->listRows);
		$page	=	$p->show();
		
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	
	public function assignuser(){
		if(!$_GET['id']) $this->error('链接错误!');
		$id = $_GET['id'];
		$assign = D('UserAssign');
		$result = $assign->find($id);
		if(!$result) $this->error('没有相关记录!');
		$user = trim(ereg_replace('\s*,$', '', $result['assign_user_id'])); // $result['assign_user'] 为1,2,形式
		
		$count = count($assign->query("select a.uid,a.USER_ID, a.USER_NAME,b.DEPT_NAME,c.PRIV_NAME from user a, department b,user_priv c where a.uid in ($user) and a.DEPT_ID=b.DEPT_ID and a.USER_PRIV=c.USER_PRIV"));		
		$rows	=	20;
		$p		=	new Page($count,$rows);
		$list = $assign->query("select a.uid,a.USER_ID, a.USER_NAME,b.DEPT_NAME,c.PRIV_NAME from user a, department b,user_priv c where a.uid in ($user) and a.DEPT_ID=b.DEPT_ID and a.USER_PRIV=c.USER_PRIV order by b.DEPT_NAME desc,c.PRIV_NAME desc limit ".$p->firstRow.",".$p->listRows);
		$page	=	$p->show();
		
		$this->assign('manager',$result);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	
	public function del(){
		if(!$_GET['id']) $this->error('链接错误!');
		$id = $_GET['id'];
		$assign = D('UserAssign');
		$assign->delete($id);
		$this->redirect('lists');
	}
}
?>