<?php
class ZWorkFlowModel extends Model {
	var $tableName="zwork_flow";
	//调用流程用户列表
	public function userList($username){
		$userList = $this->where("powerUser like '%$username%'")->findAll();
		foreach ($userList as $k=>$v){
			$value = explode($v[powerUser],',');
			if(count($value)>1){
				if(in_array($this->LOGIN_USER_ID,$value)){
					$userList2[$k] = $v;
				}
			}else{
				$userList2[$k] = $v;
			}
		}
		return $userList2;
	}
}
?>