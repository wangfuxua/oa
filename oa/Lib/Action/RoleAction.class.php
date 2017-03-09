<?php
class RoleAction extends BaseAction {
	function roleList(){
		$category_id = $_GET['category_id'];
		$role = D('Role');
		$role_res = $role->where("category_id=$category_id")->findAll();
		$this->assign('res',$role_res);
		$this->display();
	}
	
	/**
	 * 负责人选择页面
	 * */		
	public function assignTo(){
		$id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
		if ($id == null) return false;
		$this->redirect("roleList/category_id/$id",'Role');
	}
	
}
?>