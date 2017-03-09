<?php
class XmlFileAction extends PublicAction {
	
	public function lists(){ // 获取相关xml文件显示
		$display = $_GET['dis']; // 要获取的xml文件名
		if(!$display) $this->error("无显示页面!");
		$xml = new xml($display.".xml", 'item');
		
		$sort_arr = array();
		foreach ($xml->array as $key => $value){
			$sort_arr[$key] = $value['sort'];
		}
		array_multisort($sort_arr, SORT_ASC, $xml->array); // 根据sort升序排序
		
		$user = D('User');
		$currentUser = $user->where("uid = " . $_SESSION[LOGIN_UID])->find();

		if (($currentUser['USER_PRIV'] == 32) || ($currentUser['USER_PRIV'] == 1) || ($currentUser['DEPT_ID'] == 68)) {
			$userPriv = 'havepriv';
		}
		$this->assign('userPriv', $userPriv);
		
		$this->assign('to_id',$_GET['to_id']); // 传值目标id
		$this->assign('to_name',$_GET['to_name']); // 传值目标id
		$this->assign('dis',$display);
		$this->assign('result',$xml->array);
		$this->display($display);
	}
	
	public function save(){ // 写入xml文件
		$display = $_GET['dis']; // 写入的xml文件名
		if(!$display) $this->error("无显示页面!");
		if(!$_POST['name']) $this->error("请填写必选项!");
		
		$xml = new xml($display.".xml", 'item');
		$item_count = count($xml->array); // 获取xml文件的数据数
		
		$new_arr = array();
		$new_arr['id'] = $item_count+1;
		$new_arr['name'] = trim($_POST['name']);
		foreach ($xml->array as $key => $value){ // 判断是否有相同记录
			if($new_arr['name'] == $value['name']) $this->error("当前名称已存在,请重新填写!");
		}
		$new_arr['sort'] = !empty($_POST['sort']) ? trim($_POST['sort']) : 100;
		
		$xml->xml_query('insert','','',$new_arr); // 写入xml文件
		$this->redirect('lists/dis/'.$display.'/to_id/'.$_GET['to_id'].'/to_name/'.$_GET['to_name']);
	}
	
	public function update(){ // 更新xml文件
		$display = $_GET['dis']; // 更新的xml文件名
		if(!$display) $this->error("无显示页面!");
		if(!$_POST['current_id']) $this->error("操作错误!");
		if(!$_POST['name']) $this->error("请填写必选项!");
		$id = $_POST['current_id'];
		
		$xml = new xml($display.".xml", 'item');
		$item_count = count($xml->array); // 获取xml文件的数据数
		
		$new_arr = array();
		$new_arr['id'] = $item_count+1;
		$new_arr['name'] = trim($_POST['name']);
		foreach ($xml->array as $key => $value){ // 判断是否有相同记录
			if($new_arr['name'] == $value['name'] && $value['id'] != $id) $this->error("当前名称已存在,请重新填写!");
		}
		$new_arr['sort'] = !empty($_POST['sort']) ? trim($_POST['sort']) : 100;
		
		$xml->xml_query('update','id,=,'.$id,'and',$new_arr); // 写入xml文件
		$this->redirect('lists/dis/'.$display.'/to_id/'.$_GET['to_id'].'/to_name/'.$_GET['to_name']);
	}
	
	public function del(){
		$display = $_GET['dis']; // 更新的xml文件名
		if(!$display) $this->error("无显示页面!");
		if(!$_GET['id']) $this->error("操作错误!");
		$id = $_GET['id'];
		
		$xml = new xml($display.".xml", 'item');
		$new_arr = array();
		$xml->xml_query('update','id,=,'.$id,'and',$new_arr); // 写入xml文件
		$this->redirect('lists/dis/'.$display.'/to_id/'.$_GET['to_id'].'/to_name/'.$_GET['to_name']);
	}
}
?>