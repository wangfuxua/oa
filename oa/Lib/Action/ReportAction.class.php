<?php
/**
 * Function: 客户反馈相关操作Action
 * Author: heliaoping
 * Date: 2009-01-09
 * */
class ReportAction extends PublicAction {
	
	/**
	 * 客户反馈创建操作页面
	 * */
	public function create(){
		/**获取当前用户相关信息*/
		$user = D('User');
		$user_res = $user->where("uid=$this->_uid")->find();

		$this->_popSelectUser();	

		$this->assign('report_res', $user_res);
		$this->display();
	}
	
	/**
	 * 客户反馈创建操作
	 * */
	public function save(){
		/**新建初始化创建日期，修改日期，创建者id以及最后修改者id*/
		$_POST['time_create'] = date('y-m-d H:i:s');
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['builder_id'] = $this->_uid;
		$_POST['last_modifier_id'] = $this->_uid;
		/**根据$_POST创建新建条件插入以及跳转*/
		$report = D('Report');
		if($report->create()){
			$id = $report->add();
			/**潜在客户创建成功后插入该记录的相关地址信息记录*/
			$this->redirect("view/id/$id");
		}else{
			$this->redirect('create');
		}
	}
	
	/**
	 * 客户反馈预览
	 * */	
	public function view(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		if (!$id)
			$this->redirect('lists');
		
		/**获取指定id的潜在客户信息及地址信息*/
		$report = D('Report');
		$report_res = $report->find($id);
		
		/**获取当前记录的相关创建人，最后修改人以及负责人*/
		$user =  D('User');
		$builder_res = $user->where("uid='".$report_res['builder_id']."'")->find();
		$modify_res = $user->where("uid='".$report_res['last_modifier_id']."'")->find();
		$manager_res = $user->where("uid='".$report_res['manager_id']."'")->find();
		
		$this->assign('report_res',$report_res);
		$this->assign('builder_res',$builder_res);
		$this->assign('modify_res',$modify_res);
		$this->assign('manager_res',$manager_res);
		$this->display();	
	}
	
	/**
	 * 客户反馈编辑页面
	 * */	
	public function edit(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		$update = isset($_GET['update']) ? $_GET['update'] : null; 
		if (!$id)
			$this->redirect('lists');
		/**获取指定id的潜在客户信息及地址信息*/
		$report = D('Report');
		$report_res = $report->find($id);
		/**获取当前记录的相关创建人，最后修改人以及负责人*/
		$user =  D('User');
		$manager_res = $user->where("uid='".$report_res['manager_id']."'")->find();
		$report_res['uid'] = $manager_res['uid'];
		$report_res['USER_NAME'] = $manager_res['USER_NAME'];
		
		$this->_popSelectUser();

		$this->assign('update', $update);
		$this->assign('report_res',$report_res);
		$this->display('create');	
	}
	
	/**
	 * 客户反馈更新操作
	 * */	
	public function update(){
		
		$id = isset($_POST['report_id']) ? $_POST['report_id'] : null;
		if (null == $id)
			$this->redirect('edit');
			
		/**初始化修改日期和修改者id*/
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['last_modifier_id'] = $this->_uid;
		
		$report = D('Report');	
		if ($report->create()){
			$report->where("id='$id'")->save();
		}
		$this->redirect("view/id/$id");
	}
	
	/**
	 * 客户反馈列表操作
	 * */	
	public function lists(){
		$condition = "manager_id=$this->_uid or builder_id=$this->_uid";
		$report	= D("Report");
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		$count	= $report->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list	=	$report->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		$user =  D('User');
		if($list){
			for($i=0;$i<count($list);$i++){
				$res = $user->where("uid=".$list[$i]['manager_id'])->find();
				$list[$i]['user_name'] = $res['USER_NAME'];
			}
		} 
		$xml_sta = new xml("report_status.xml", 'item');
		$xml_pri = new xml("priority.xml", 'item');
		
		$this->assign('xml_sta',$xml_sta->array); // 类型记录集
		$this->assign('xml_pri',$xml_pri->array); // 电话服务记录集
		$this->assign('report_res',$list);
		$this->assign('page',$page);
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('reportList');	
		}else{
			$this->display();
		}
	}
	
	//查询操作
	function search(){
		$report	= D("Report");
		$condition = "(manager_id=$this->_uid or builder_id=$this->_uid) ";
		$cond = $param = '';
		if($_POST){
			/**组合post过来的查询条件$condition和分页url参数$param*/
			foreach($_POST as $key => $value){
				if($value){
					if($key == "date_from") $cond .= 'time_modify >="'.$value.' 00:00:00" and ';
					else if($key == "date_to") $cond .= 'time_modify <="'.$value.' 23:59:59" and ';
					else $cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		else if($_GET){
			/**组合get过来的查询条件$condition和分页url参数$param*/
			foreach($_GET as $key => $value){
				if($value && $key != 'a' && $key != 'm' && $key != 'p' && $key != 'field' && $key != 'sort'){
					if($key == "date_from") $cond .= 'time_modify >="'.$value.' 00:00:00" and ';
					else if($key == "date_to") $cond .= 'time_modify <="'.$value.' 23:59:59" and ';
					else $cond .= $key.' like "%'.$value.'%" and ';
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
		$count	= $report->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows,$param);
		$list	=	$report->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		$user =  D('User');
		if($list){
			for($i=0;$i<count($list);$i++){
				$res = $user->where("uid=".$list[$i]['manager_id'])->find();
				$list[$i]['user_name'] = $res['USER_NAME'];
			}
		} 
		$xml_sta = new xml("report_status.xml", 'item');
		$xml_pri = new xml("priority.xml", 'item');
		
		$this->assign('xml_sta',$xml_sta->array); // 类型记录集
		$this->assign('xml_pri',$xml_pri->array); // 电话服务记录集
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
		$this->assign('report_res',$list);
		$this->assign('page',$page);
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('reportList');	
		}else{
			$this->display('lists');
		}		
	}
	/**
	 * 客户反馈删除操作
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
		$report = D('Report');
		$report->delete("id in($ids_string)");
		$this->redirect('lists');
	}
}
?>