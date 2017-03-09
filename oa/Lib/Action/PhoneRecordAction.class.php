<?php
/**
 * Function: 来电记录相关操作Action
 * Author: heliaoping
 * Date: 2009-01-09
 * */
class PhoneRecordAction extends PublicAction {
	
	/**
	 * 来电记录创建输入页面
	 * */
	public function create(){
		/**获取当前用户相关信息*/
		$user = D('User');
		$user_res = $user->where("uid=$this->_uid")->find();

		$this->_popSelectUser();	 

		$this->assign('phoneRecord_res', $user_res);
		$this->display();
	}
	
	/**
	 * 来电记录创建操作页面
	 * */
	public function save(){
		
		/**新建初始化创建日期，修改日期，创建者id以及最后修改者id*/
		$_POST['time_create'] = date('y-m-d H:i:s');
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['builder_id'] = $this->_uid;
		$_POST['last_modifier_id'] = $this->_uid;
		
		/**根据$_POST创建新建条件以及跳转*/
		$phoneRecord = D('PhoneRecord');
		if($phoneRecord->create()){
			$id = $phoneRecord->add();
			//echo $latent->getLastSql();exit;
			$this->redirect("view/id/$id");
		}else{
			$this->redirect('create');
		}
	}
	
	/**
	 * 来电记录信息预览页面
	 * */	
	public function view(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		if (!$id)
			$this->redirect('lists');
		$phoneRecord = D('PhoneRecord');
		$phoneRecord_res = $phoneRecord->find($id);
		
		/**获取当前记录的相关创建人，最后修改人以及负责人*/
		$user =  D('User');
		$builder_res = $user->where("uid='".$phoneRecord_res['builder_id']."'")->find();
		$modify_res = $user->where("uid='".$phoneRecord_res['last_modifier_id']."'")->find();
		$manager_res = $user->where("uid='".$phoneRecord_res['manager_id']."'")->find();
		
		$this->assign('phoneRecord_res',$phoneRecord_res);
		$this->assign('builder_res',$builder_res);
		$this->assign('modify_res',$modify_res);
		$this->assign('manager_res',$manager_res);
		$this->display();		
	}
	
	/**
	 * 来电记录编辑页面
	 * */	
	public function edit(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		$update = isset($_GET['update']) ? $_GET['update'] : null; 
		if (!$id)
			$this->redirect('lists');
		/**获取指定id的来电信息*/
		$phoneRecord = D('PhoneRecord');
		$phoneRecord_res = $phoneRecord->find($id);
		$user = D('User');
		$user_res = $user->where("uid='".$phoneRecord_res['manager_id']."'")->find();
		$phoneRecord_res['uid'] = $user_res['uid'];
		$phoneRecord_res['USER_NAME'] = $user_res['USER_NAME'];

		$this->_popSelectUser();

		$this->assign('update', $update);
		$this->assign('phoneRecord_res',$phoneRecord_res);
		$this->display('create');	
	}
	
	/**
	 * 来电记录更新操作
	 * */	
	public function update(){
		$id = isset($_POST['current_id']) ? $_POST['current_id'] : null;
		if (null == $id)
			$this->redirect('edit');
		
		/**初始化修改日期和修改者id*/
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['last_modifier_id'] = $this->_uid;
		
		/**根据$_POST对指定id数据进行更新*/
		$phoneRecord = D('PhoneRecord');	
		if ($phoneRecord->create()){
			$phoneRecord->where("id='$id'")->save();
		}
		$this->redirect("view/id/$id");
	}
	
	/**
	 * 来电记录列表页面
	 * */	
	public function lists(){
		$condition = "manager_id=$this->_uid or builder_id=$this->_uid";
		$phoneRecord = D('PhoneRecord');	
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		$count	= $phoneRecord->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list	=	$phoneRecord->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		$user =  D('User');
		if($list){
			for($i=0;$i<count($list);$i++){
				$res = $user->where("uid=".$list[$i]['manager_id'])->find();
				$list[$i]['user_name'] = $res['USER_NAME'];
			}
		} 
		$xml_type = new xml("phonerecord_type.xml", 'item');
		$xml_sta = new xml("phonerecord_status.xml", 'item');
		
		$this->assign('xml_type',$xml_type->array); // 类型记录集
		$this->assign('xml_sta',$xml_sta->array); // 处理状态记录集
		$this->assign('phoneRecord_res',$list);
		$this->assign('page',$page);
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&sort=".$def_sort); // 排序操作路径
		$this->display();
	}
	
	/**
	 * 来电记录查询操作
	 * */
	function search(){
		$condition = "(manager_id=$this->_uid or builder_id=$this->_uid) ";
		$phoneRecord = D('PhoneRecord');	
		$cond = $param = '';
		if($_POST){
			/**组合post过来的查询条件$condition和分页url参数$param*/
			foreach($_POST as $key => $value){
				if($value){
					$cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		else if($_GET){
			/**组合get过来的查询条件$condition和分页url参数$param*/
			foreach($_GET as $key => $value){
				if($value && $key != 'a' && $key != 'm' && $key != 'p' && $key != 'field' && $key != 'sort'){
					$cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		$cond = substr($cond,0,-4);
		$param = substr($param,0,-1);
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		if($cond)$condition .= "and ".$cond;
		$count	= $phoneRecord->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows,$param);
		$list	=	$phoneRecord->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		$user =  D('User');
		if($list){
			for($i=0;$i<count($list);$i++){
				$res = $user->where("uid=".$list[$i]['manager_id'])->find();
				$list[$i]['user_name'] = $res['USER_NAME'];
			}
		} 
		$xml_type = new xml("phonerecord_type.xml", 'item');
		$xml_sta = new xml("phonerecord_status.xml", 'item');
		
		$this->assign('xml_type',$xml_type->array); // 类型记录集
		$this->assign('xml_sta',$xml_sta->array); // 处理状态记录集
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
		$this->assign('phoneRecord_res',$list);
		$this->assign('page',$page);
		$this->display('lists');	
	}
	/**
	 * 来电记录删除操作
	 * */	
	public function del(){
		
		//获取传递过来要删除的id数组
		$ids_array = isset($_POST["mass"]) ? $_POST["mass"] : null;
		$id = isset($_POST["id"]) ? $_POST["id"] : null;
		if (null == $ids_array && null == $id){
			$this->redirect('lists');
		}
		//将id数组组合成字符串
		if ($id)
			$ids_string = $id;
		else
			$ids_string = join(',', $ids_array);
		$latent = D('PhoneRecord');
		$latent->delete("id in($ids_string)");
		$this->redirect('lists');
	}
}
?>