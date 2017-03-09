<?php
class SupplierAction extends PublicAction {
	public function create(){
		$this->display();
	}

	public function save(){
		/**新建初始化创建日期，修改日期，创建者id以及最后修改者id*/
		$_POST['time_create'] = date('y-m-d H:i:s');
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['builder_id'] = $this->_uid;
		$_POST['last_modifier_id'] = $this->_uid;
		/**根据$_POST创建新建条件插入以及跳转*/
		$supplier = D('Supplier');
		if($supplier->create()){
			$id = $supplier->add();
			/**创建成功后插入该记录的相关地址信息记录*/
			if($id){
				$_POST['table_name'] = 'Supplier';
				$_POST['record_id'] = $id;
				$address = D('CrmAddress');
				if($address->create()){
					$address_id = $address->add();
					/**地址信息记录创建失败，则删除记录*/
					if($address_id){ 
						$bank = D('CrmBank');
						if($bank->create()){
							$bank_id = $bank->add();
							if($bank_id) $this->redirect("view/id/$id");
							else{ // 插入银行信息失败，则删除已插入的记录信息和地址信息
								$address->delete($address_id);
								$supplier->delete($id);
							}
						}else{ // 银行信息创建失败，则删除已插入的客户信息和地址信息
							$address->delete($address_id);
							$supplier->delete($id);
						}
						$this->error("银行信息创建失败!");
					}else{
						$supplier->delete($id);
					}
				}else{
					$supplier->delete($id);
				}
				$this->error("地址信息创建失败!");
			}
		}
		$this->error("信息创建失败!");
	}

	public function view(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		if (!$id)
			$this->redirect('lists');
		/**获取指定id的潜在客户信息及地址信息及银行信息*/
		$supplier = D('Supplier');
		$supplier_res = $supplier->find($id);
		$address = D('CrmAddress');
		$address_res = $address->where("table_name='Supplier' and record_id=$id")->find();
		$bank = D('CrmBank');
		$bank_res = $bank->where("table_name='Supplier' and record_id=$id")->find();
		
		/**获取当前记录的相关创建人，最后修改人以及负责人*/
		$user =  D('User');
		$builder_res = $user->where("uid='".$supplier_res['builder_id']."'")->find();
		$modify_res = $user->where("uid='".$supplier_res['last_modifier_id']."'")->find();
		
		$this->assign('supplier_res',$supplier_res);
		$this->assign('address_res',$address_res);
		$this->assign('bankinfo',$bank_res);
		$this->assign('builder_res',$builder_res);
		$this->assign('modify_res',$modify_res);
		$this->display();		
	}

	public function edit(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		$update = isset($_GET['update']) ? $_GET['update'] : null; 
		if (!$id)
			$this->redirect('lists');
		
		/**获取指定id的潜在客户信息及地址信息及银行信息*/
		$supplier = D('Supplier');
		$supplier_res = $supplier->find($id);
		$address = D('CrmAddress');
		$address_res = $address->where("table_name='Supplier' and record_id=$id")->find();
		$bank = D('CrmBank');
		$bank_res = $bank->where("table_name='Supplier' and record_id=$id")->find();
		
		$this->assign('update', $update);
		$this->assign('supplier_res',$supplier_res);
		$this->assign('address_res',$address_res);
		$this->assign('bankinfo_res',$bank_res);
		$this->display('create');	
	}

	public function update(){
		$supplier_id = isset($_POST['supplier_id']) ? $_POST['supplier_id'] : null;
		$address_id = isset($_POST['address_id']) ? $_POST['address_id'] : null;
		$bank_id = isset($_POST['bankinfo_id']) ? $_POST['bankinfo_id'] : null;
		if (null == $supplier_id || null == $address_id || null == $bank_id)
			$this->redirect('edit');
		
		/**初始化修改日期和修改者id*/
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['last_modifier_id'] = $this->_uid;
		
		/**根据$_POST对指定id数据进行更新*/
		$supplier = D('Supplier');	
		if ($supplier->create()){
			$supplier->where("id=$supplier_id")->save();
			$address = D('CrmAddress');
			if($address->create()){
				$address->where("id=$address_id")->save();
				$bank = D('CrmBank');
				if($bank->create()){
					$bank->where("id=$bank_id")->save();
					$this->redirect("view/id/$supplier_id");
				}
			}
		}
		$this->error("更新失败!");
	}
	
	public function lists(){
		$supplier = D('Supplier');
		
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		
		/**分页显示*/
		$count = count($supplier->query("select a.id,province,city from crm_supplier a, crm_address b where a.id = record_id and table_name='Supplier'"));
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list = $supplier->query("select a.*,province,city from crm_supplier a, crm_address b where a.id = record_id and table_name='Supplier' order by ".$sort." limit ".$p->firstRow.",".$p->listRows);
		$page	=	$p->show();
		
		$this->assign('supplier_record',$list);
		$this->assign('page',$page);
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('suppierList');	
		}else{
			$this->display();
		}
	}
	
	function search(){
		$condition = $param = '';
		$supplier = D('Supplier');
		if($_POST){
			/**组合post过来的查询条件$condition和分页url参数$param*/
			foreach($_POST as $key => $value){
				if($value){
					$condition .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		else if($_GET){
			/**组合get过来的查询条件$condition和分页url参数$param*/
			foreach($_GET as $key => $value){
				if($value && $key != 'a' && $key != 'm' && $key != 'p' && $key != 'field' && $key != 'sort'){
					$condition .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		$condition = substr($condition,0,-4);
		$param = substr($param,0,-1);
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		if($condition) $condition = " and ".$condition;
		
		/**分页显示*/
		$count = count($supplier->query("select a.* from crm_supplier a, crm_address b where a.id = record_id and table_name='Supplier'".$condition));
		$rows = 10;
		$p = new Page($count,$rows,$param);
		$list =	$supplier->query("select a.*, province,city from crm_supplier a, crm_address b where a.id = record_id and table_name='Supplier'".$condition." order by ".$sort." limit ".$p->firstRow.",".$p->listRows);
		//echo $supplier->getLastSql();exit;
		$page = $p->show();
		
		$this->assign('supplier_record',$list);
		$this->assign('page',$page);
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('suppierList');	
		}else{
			$this->display('lists');
		}
	}	
	
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
		$supplier = D('Supplier');
		$supplier->delete("id in($ids_string)"); // 删除客户记录
		$address = D('CrmAddress');
		$address->deleteAll("table_name='Supplier' and record_id in($ids_string)"); //删除已删除客户记录的相关地址记录
		$bank = D('CrmBank');
		$bank->deleteAll("table_name='Supplier' and record_id in($ids_string)"); //删除已删除客户记录的相关银行记录
		$this->redirect('lists');
	}
}
?>