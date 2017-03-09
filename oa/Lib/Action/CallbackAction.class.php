<?php
/**
 * Function: 客户回访相关操作Action
 * Author: heliaoping
 * Date: 2009-01-09
 * */
class CallbackAction extends PublicAction {
	
	/**
	 * 客户回访创建操作页面
	 * */
	public function create(){
		/**获取当前用户相关信息*/
		$this->_hasId();
		$user = D('User');
		$user_res = $user->where("uid=$this->_uid")->find();

		$this->_popSelectUser();	
		$this->assign('callback_res', $user_res);
		$this->display();
	}
	
	/**
	 * 客户回访创建操作
	 * */
	public function save(){
		/**新建初始化创建日期，修改日期，创建者id以及最后修改者id*/
		$_POST['time_create'] = date('y-m-d H:i:s');
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['builder_id'] = $this->_uid;
		$_POST['last_modifier_id'] = $this->_uid;
		
		/**根据$_POST创建新建条件以及跳转*/
		$callback = D('Callback');
		if($callback->create()){
			$id = $callback->add();
			$this->redirect("view/id/$id");
		}else{
			$this->redirect('create');
		}
	}
	
	/**
	 * 客户回访预览
	 * */	
	public function view(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		if (!$id)
			$this->redirect('lists');
		
		/**获取指定id的潜在客户信息及地址信息*/
		$callback = D('Callback');
		$callback_res = $callback->find($id);
		
		/**获取当前记录的相关创建人，最后修改人以及负责人*/
		$user =  D('User');
		$modify_res = $user->where("uid='".$callback_res['last_modifier_id']."'")->find();
		$manager = $user->where("uid='".$callback_res['manager_id']."'")->find();  
		$manager = $manager['USER_NAME'];
		
		$this->assign('manager', $manager);
		$this->assign('callbackId', $id);
		$this->assign('callback_res',$callback_res);
		$this->assign('modify_res',$modify_res);
		
		if (null != $_GET['lists']) {
			$this->display('lview');
		} else {
			$this->display();	
		}
	}
	
	/**
	 * 客户回访编辑
	 * */	
	public function edit(){
		$lock =  isset($_GET['lock']) ? $_GET['lock'] : '';
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		$update = isset($_GET['update']) ? $_GET['update'] : null; 
		if (!$id)
			$this->redirect('lists');
		/**获取指定id的信息及地址信息*/
		$callback = D('Callback');
		$callback_res = $callback->find($id);
		/**获取当前记录的相关创建人，最后修改人以及负责人*/
		$user =  D('User');
		$manager_res = $user->where("uid='".$callback_res['manager_id']."'")->find();
		$callback_res['uid'] = $manager_res['uid'];
		$callback_res['USER_NAME'] = $manager_res['USER_NAME'];
		
		$this->_popSelectUser();

		$this->assign('lock', $lock);
		$this->assign('update', $update);
		$this->assign('callback_res',$callback_res);
		$this->display('create');		
	}
	
	/**
	 * 客户回访更新操作
	 * */	
	public function update(){
		$id = isset($_POST['callback_id']) ? $_POST['callback_id'] : null;
		if (null == $id)
			$this->redirect('edit');
			
		/**初始化修改日期和修改者id*/
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['last_modifier_id'] = $this->_uid;
		
		$callback = D('Callback');	
		if ($callback->create()){
			$callback->where("id='$id'")->save();
		}
		$this->redirect("view/id/$id");
	}
	
	/**
	 * 客户回访列表操作
	 * */	
	public function lists(){
		$this->_hasId();
		if (null != $_GET['id']) {
			$condition = "account_id = " . $_GET['id'] . " and ";
		}
		$condition .= " (manager_id=$this->_uid or builder_id=$this->_uid)";
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		$callback	= D("Callback");
		$count	= $callback->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list	=	$callback->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		//echo $callback->getLastSql();
		$page	=	$p->show();
		/**获取类型,电话服务,技术服务,服务态度*/
		$xml_type = new xml("callback_type.xml", 'item');
		$xml_call = new xml("service_call.xml", 'item');
		$xml_tech = new xml("service_technical.xml", 'item');
		$xml_att = new xml("service_attitudinal.xml", 'item');
		
		$this->assign('xml_type',$xml_type->array); // 类型记录集
		$this->assign('xml_call',$xml_call->array); // 电话服务记录集
		$this->assign('xml_tech',$xml_tech->array); // 技术服务记录集
		$this->assign('xml_att',$xml_att->array); // 服务态度记录集
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&sort=".$def_sort); // 排序操作路径
		
		$this->assign('callback_res',$list);
		$this->assign('page',$page);
		$this->display();
	}
	
	//查询操作
	function search(){
		$condition = "(manager_id=$this->_uid or builder_id=$this->_uid) ";
		$callback	= D("Callback");
		$cond = $param = '';
		if($_POST){
			/**组合post过来的查询条件$condition和分页url参数$param*/
			foreach($_POST as $key => $value){
				if($value){
					if($key == "date_from") $cond .= 'service_date >="'.$value.' 00:00:00" and ';
					else if($key == "date_to") $cond .= 'service_date <="'.$value.' 23:59:59" and ';
					else $cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		else if($_GET){
			/**组合get过来的查询条件$condition和分页url参数$param*/
			foreach($_GET as $key => $value){
				if($value && $key != 'a' && $key != 'm' && $key != 'p' && $key != 'field' && $key != 'sort'){
					if($key == "date_from") $cond .= 'service_date >="'.$value.' 00:00:00" and ';
					else if($key == "date_to") $cond .= 'service_date <="'.$value.' 23:59:59" and ';
					else $cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		$cond = substr($cond,0,-4);
		$param = substr($param,0,-1);
		if($cond)$condition .= "and ".$cond;
		
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		
		$count	= $callback->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows,$param);
		$list	=	$callback->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		
		/**获取类型,电话服务,技术服务,服务态度*/
		$xml_type = new xml("callback_type.xml", 'item');
		$xml_call = new xml("service_call.xml", 'item');
		$xml_tech = new xml("service_technical.xml", 'item');
		$xml_att = new xml("service_attitudinal.xml", 'item');
		
		$this->assign('xml_type',$xml_type->array); // 类型记录集
		$this->assign('xml_call',$xml_call->array); // 电话服务记录集
		$this->assign('xml_tech',$xml_tech->array); // 技术服务记录集
		$this->assign('xml_att',$xml_att->array); // 服务态度记录集
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
		
		$this->assign('callback_res',$list);
		$this->assign('page',$page);
		$this->display('lists');		
	}

	/**
	 * 客户回访删除操作
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
		$callback = D('Callback');
		$callback->delete("id in($ids_string)");
		$this->redirect('lists');
	}
	
	/**
	 * Select If the users of pop windows
	 */
	protected function _popSelectUser()
	{
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
	}
	
	/**
	 * 
	 */
	protected function _hasId()
	{
		if (null == $_GET['id']) {
			return ;
		} else {
			$accountId = $_GET['id'];
			$account = D('Account');
			$accountBlur = $account->where("id = " . $accountId)->find();
			$user = D('User');
			$userManager = $user->where("uid = " . $accountBlur['manager_id'])->find();
			$accountBlur['manager'] = $userManager['USER_NAME'];
			//echo $accountId;
			$this->assign('accountId', $accountId);
			$this->assign('accountBlur', $accountBlur);
			//print_r($accountBlur); 
		}	
	}
}
?>