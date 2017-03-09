<?php
/**
 * Function: 客户相关操作Action
 * Author: heliaoping
 * Date: 2009-01-09
 * */
class AccountAction extends PublicAction {
	
	/**
	 * 客户创建操作页面
	 * */
	public function create(){
		/**获取当前用户相关信息*/
		$user = D('User');
		$user_res = $user->where("uid=$this->_uid")->field("uid,USER_NAME")->find();
		$this->assign('account_res', $user_res);
         
		$this->_popSelectUser();
		
		$this->display();
	}
	
	/**
	 * 客户创建操作
	 * */
	public function save(){
		/**新建初始化创建日期，修改日期，创建者id以及最后修改者id*/
		//print_r($_POST);
		$_POST['time_create'] = date('y-m-d H:i:s');
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['builder_id'] = $this->_uid;
		$_POST['last_modifier_id'] = $this->_uid;
		if (null != $_POST['manager_id'] && !is_numeric($_POST['manager_id'])) {
			$_POST['manager_id'] = substr($_POST['manager_id'], 2);
		}
		/**根据$_POST创建新建条件插入以及跳转*/
		$account = D('Account');
		//print_r($_POST);
		if($account->create()){
			$id = $account->add();
			/**客户创建成功后插入该记录的相关地址信息记录*/
			if($id){
				$_POST['table_name'] = 'Account';
				$_POST['record_id'] = $id;
				$address = D('CrmAddress');
				if($address->create()){
					$address_id = $address->add();
					/**地址信息记录创建失败，则删除潜在客户记录*/
					if($address_id){ 
						$bank = D('CrmBank');
						if($bank->create()){
							$bank_id = $bank->add();
							if($bank_id) { 
								$this->redirect("view/id/$id"); 
							}
							else{ // 插入银行信息失败，则删除已插入的客户信息和地址信息
								$address->delete($address_id);
								$account->delete($id);
							}
						}else{ // 银行信息创建失败，则删除已插入的客户信息和地址信息
							$address->delete($address_id);
							$account->delete($id);
						}
						$this->error("银行信息创建失败!");
					}else{
						$account->delete($id);
					}
				}else{
					$account->delete($id);
				}
				$this->error("地址信息创建失败!");
			}
		}
		$this->error("创建失败!");
	}
	
	/**
	 * 客户预览
	 * */	
	public function view(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		if (!$id)
			$this->redirect('lists');
		/**获取指定id的潜在客户信息及地址信息及银行信息*/
		$account = D('Account');
		$account_res = $account->find($id);
		$address = D('CrmAddress');
		$address_res = $address->where("table_name='Account' and record_id=$id")->find();
		$bank = D('CrmBank');
		$bank_res = $bank->where("table_name='Account' and record_id=$id")->find();
		
		/**获取当前记录的相关创建人，最后修改人以及负责人*/
		$user =  D('User');
		$builder_res = $user->where("uid='".$account_res['builder_id']."'")->find();
		$modify_res = $user->where("uid='".$account_res['last_modifier_id']."'")->find();
		$manager_res = $user->where("uid='".$account_res['manager_id']."'")->find();
		$share = $account->getShareOne('Account', $id, $this->_uid);
		if(is_array($share)) $account_res['flag'] = $share['flag'];
		else $account_res['flag'] = 5;
		
		$this->assign('account_res',$account_res);
		$this->assign('address_res',$address_res);
		$this->assign('bankinfo',$bank_res);
		$this->assign('builder_res',$builder_res);
		$this->assign('modify_res',$modify_res);
		$this->assign('manager_res',$manager_res);
		$this->display();		
	}
	
	/**
	 * 客户编辑页面
	 * */	
	public function edit(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		$update = isset($_GET['update']) ? $_GET['update'] : null; 
		if (!$id)
			$this->redirect('lists');
		
		/**获取指定id的潜在客户信息及地址信息及银行信息*/
		$account = D('Account');
		$account_res = $account->find($id);
		$address = D('CrmAddress');
		$address_res = $address->where("table_name='Account' and record_id=$id")->find();
		$bank = D('CrmBank');
		$bank_res = $bank->where("table_name='Account' and record_id=$id")->find();
		/**获取负责人信息*/
		$user = D('User');
		$manager_res = $user->where("uid=".$account_res['manager_id'])->field("uid,USER_NAME")->find();
		$account_res['uid'] = $manager_res['uid'];
		$account_res['USER_NAME'] = $manager_res['USER_NAME'];
		
		$this->assign('update', $update);
		$this->assign('account_res',$account_res);
		$this->assign('address_res',$address_res);
		$this->assign('bankinfo_res',$bank_res);
		$this->display('create');	
	}
	
	/**
	 * 客户更新操作
	 * */	
	public function update(){
		$account_id = isset($_POST['account_id']) ? $_POST['account_id'] : null;
		$address_id = isset($_POST['address_id']) ? $_POST['address_id'] : null;
		$bank_id = isset($_POST['bankinfo_id']) ? $_POST['bankinfo_id'] : null;
		if (null == $account_id || null == $address_id || null == $bank_id)
			$this->redirect('edit');
		
		/**初始化修改日期和修改者id*/
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['last_modifier_id'] = $this->_uid;
		
		/**根据$_POST对指定id数据进行更新*/
		$account = D('Account');	
		if ($account->create()){
			$account->where("id=$account_id")->save();
			$address = D('CrmAddress');
			if($address->create()){
				$address->where("id=$address_id")->save();
				$bank = D('CrmBank');
				if($bank->create()){
					$bank->where("id=$bank_id")->save();
					$this->redirect("view/id/$account_id");
				}
			}
		}
		$this->error("更新失败!");
	}
	
	/**
	 * 客户列表操作
	 * */	
	public function lists(){
		$ids_array = array();
		$ids_string = null;
		$account	= D("Account");
		
		/**获取共享记录id集*/
		$share_res = $account->getShareInfo('Account', $this->_uid);
		$share_count = count($share_res);
		if ($share_res) {
			for ($i = 0; $i < $share_count; $i++){
				array_push($ids_array, $share_res[$i]['record_id']); 
			}
			$ids_string = join(',', $ids_array);
		}
		$listAccount = $this->_listAccountPriv();
		//echo $listAccount;
		if ($listAccount == null) {
			$listAccount = '1' . ' or ';
			//$listAccount = "manager_id = $this->_uid" . 'or ';
		} else {
			$listAccount = $listAccount . ' or ';
		}
		/**组合记录查询条件*/
		if (null != $ids_string) {
			$account_res = "$listAccount  builder_id=$this->_uid or a.id in ($ids_string)";
		}else{
			$account_res = "$listAccount  builder_id=$this->_uid";
		}
		

		
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		
		/**分页显示*/
		$count = count($account->query("select a.id from crm_account a, crm_address b where (".$account_res.") and a.id = record_id and table_name='Account'"));
		//echo $account->getLastSql();
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list = $account->query("select a.*, province,city from crm_account a, crm_address b where (".$account_res.") and a.id = record_id and table_name='Account' order by ".$sort." limit ".$p->firstRow.",".$p->listRows);
		//print_r($list);
		if (null != $list) {
			$callback = D('Callback');
			foreach ($list as &$row) {
				//echo 'aa';
				$count = $callback->count("account_id = " . $row['id']);
				//echo $count;
				$row['count'] = $count;
				//$count = $callback->where("account_id = " . $row[id])->find();
				//print_r($count);
			}
		}
		//print_r($list);
		$page	=	$p->show();
		/**判断记录查看权限,插入到相关记录中*/
		if($list) 	$list = $account->addManager('Account', $list, $share_res);
		/**获取来源,类型,行业*/
		$xml_sou = new xml("account_source.xml", 'item');
		$xml_type = new xml("account_type.xml", 'item');
		$xml_ind = new xml("account_industry.xml", 'item');

		$this->_popSelectUser(); 	

		$this->assign('xml_sou',$xml_sou->array); // 来源记录集
		$this->assign('xml_type',$xml_type->array); // 类型记录集
		$this->assign('xml_ind',$xml_ind->array); // 行业记录集
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&sort=".$def_sort); // 排序操作路径
		$this->assign('account_record',$list);
		$this->assign('page',$page);
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('accountList');	
		}else{
			$this->display();
		}
	}
	
	//查询操作
	function search(){
		$ids_array = array();
		$ids_string = null;
		$condition = $param = '';
		$account = D("Account");
		if($_POST){
			/**组合post过来的查询条件$condition和分页url参数$param*/
			foreach($_POST as $key => $value){
				if($value){
					$value = trim($value);
					if($key == "date_from") $condition .= 'time_modify >="'.$value.' 00:00:00" and ';
					else if($key == "date_to") $condition .= 'time_modify <="'.$value.' 23:59:59" and ';
					else if ($key == 'create_date_from') $condition .= 'time_create >= "' . $value . ' 00:00:00" and ';
					else if ($key == 'create_date_to') $condition .= 'time_create <= "' . $value . ' 23:59:59" and '; 
					else if($key == "manager_id") $condition .= 'manager_id = "' . substr($value, 2) . '" and ';
					else $condition .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		else if($_GET){
			/**组合get过来的查询条件$condition和分页url参数$param*/
			foreach($_GET as $key => $value){
				$value = trim($value);
				if($value && $key != 'a' && $key != 'm' && $key != 'p' && $key != 'field' && $key != 'sort'){
					if($key == "date_from") $condition .= 'time_modify >="'.$value.' 00:00:00" and ';
					else if($key == "date_to") $condition .= 'time_modify <="'.$value.' 23:59:59" and ';
					else if ($key == 'create_date_from') $condition .= 'time_create >= "' . $value . ' 00:00:00" and ';
					else if ($key == 'create_date_to') $condition .= 'time_create <= "' . $value . ' 23:59:59" and '; 
					else if($key == "manager_id") $condition .= 'instr(manager_id, "' . $value . '") and ';
					else $condition .= $key.' like "%'.$value.'%" and ';
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
	
		/**获取共享记录id集*/
		$share_res = $account->getShareInfo('Account', $this->_uid);
		$share_count = count($share_res);
		if ($share_res) {
			for ($i = 0; $i < $share_count; $i++){
				array_push($ids_array, $share_res[$i]['record_id']); 
			}
			$ids_string = join(',', $ids_array);
		}
		/**组合记录联合查询条件*/
		if (null != $ids_string) {
			$account_res = "manager_id=$this->_uid or builder_id=$this->_uid or a.id in ($ids_string) ";
		}else{
			$account_res = "manager_id=$this->_uid or builder_id=$this->_uid ";
		}	
		$account_res =  '('.$account_res.') and a.id = record_id and table_name="Account"';
		if($condition) $account_res .= ' and '.$condition;	
		/**分页显示*/
		$count = count($account->query("select a.* from crm_account a, crm_address b where ".$account_res));
		$rows = 10;
		$p = new Page($count,$rows,$param);
		$list =	$account->query("select a.*, province,city from crm_account a, crm_address b where (".$account_res.") and a.id = record_id order by ".$sort." limit ".$p->firstRow.",".$p->listRows);
		$page = $p->show();
		/**判断记录查看权限,插入到相关记录中*/
		if($list) $list = $account->addManager('Account', $list, $share_res);

		$this->_popSelectUser();

		/**获取来源,类型,行业*/
		$xml_sou = new xml("account_source.xml", 'item');
		$xml_type = new xml("account_type.xml", 'item');
		$xml_ind = new xml("account_industry.xml", 'item');
		
		$this->assign('xml_sou',$xml_sou->array); // 来源记录集
		$this->assign('xml_type',$xml_type->array); // 类型记录集
		$this->assign('xml_ind',$xml_ind->array); // 行业记录集
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
		$this->assign('account_record',$list);
		$this->assign('page',$page);
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('accountList');	
		}else{
			$this->display('lists');
		}
	}
	
	/**
	 * 共享初始化
	 * */
	public function share(){
		if ($_GET['id']) {
			$list_ids = $view_ids = $edit_ids = $delete_ids = array(); // 各共享权限id集合
			$list_ids_merge = $view_ids_merge = $edit_ids_merge = array(); // 各权限的所有合并集合
			$id = $_GET['id'];
			$account = D('Account');
			$account_res = $account->field('id,name')->find($id); // 获取当前记录id和姓名
			$account_res['category_name'] = '我的客户';
			$user = D('User');
			$user_res = $user->where("uid <> $this->_uid")->field('uid,USER_NAME')->findAll(); // 获取所有用户的id和真是姓名
			$share = D('Share');
			$share_res = $share->where("table_name='Account' and record_id=$id")->field('user_id,flag')->findAll(); //获取当前记录共享者id和权限标识
			if($share_res){
				$count = count($share_res);
				for ($i=0;$i<$count;$i++){
					if($share_res[$i]['flag'] == 1){
						array_push($list_ids,$share_res[$i]['user_id']);
					}elseif($share_res[$i]['flag'] == 2){
						array_push($view_ids,$share_res[$i]['user_id']);
					}elseif($share_res[$i]['flag'] == 3){
						array_push($edit_ids,$share_res[$i]['user_id']);
					}elseif($share_res[$i]['flag'] == 4){
						array_push($delete_ids,$share_res[$i]['user_id']);
					}
				}
			}
			$list_ids_merge = array_merge($list_ids, $view_ids, $edit_ids, $delete_ids);
			$view_ids_merge = array_merge($view_ids, $edit_ids, $delete_ids);
			$edit_ids_merge = array_merge($edit_ids, $delete_ids);
			$this->assign('account_res',$account_res);
			$this->assign('user_res',$user_res);
			$this->assign('list_ids_merge',$list_ids_merge);
			$this->assign('view_ids_merge',$view_ids_merge);
			$this->assign('edit_ids_merge',$edit_ids_merge);
			$this->assign('delete_ids',$delete_ids);
			$this->display();
		}
	}
	
	/**
	 * 共享存储
	 * */
	public function shareSave(){
		if ($_GET['list']){ // 共享列表权限
			$id = $_GET['list'];
			$flag = 1;
			$share_arr = $_POST['display_list']; 
		}elseif($_GET['view']){ // 共享查看权限
			$id = $_GET['view'];
			$flag = 2;
			$share_arr = $_POST['display_view']; 
		}elseif($_GET['edit']){ // 共享查看权限
			$id = $_GET['edit'];
			$flag = 3;
			$share_arr = $_POST['display_edit']; 
		}elseif($_GET['del']){ // 共享查看权限
			$id = $_GET['del'];
			$flag = 4;
			$share_arr = $_POST['display_del']; 
		}
		$condition = "table_name='Account' and record_id=$id and flag >0";
		$share = D('Share');
		$share_res = $share->where($condition)->field("id,user_id,flag")->findAll(); // 获取当前记录所有共享信息
		if (!$share_res){ // 相关当前信息共享记录不存在时，如选择了共享权限用户，则插入
			if ($share_arr){
				for ($i=0;$i<count($share_arr);$i++){
					$cond['table_name'] = "Account";
					$cond['record_id'] = $id;
					$cond['user_id'] = $share_arr[$i];
					$cond['flag'] = $flag;
					$share->create($cond);
					$share->add();
				}					
			}
		}else{ // 存在当前记录共享信息
			$share_user_ids = $new_ids = array();
			for ($i=0;$i<count($share_res);$i++){
				array_push($share_user_ids,$share_res[$i]['user_id']);  // 获取已经共享的用户id集
				if (in_array($share_res[$i]['user_id'], $share_arr)) //如果当前共享的用户存在，则修改其共享权限
					$share->setField('flag', $flag, "id=".$share_res[$i]['id']);
				elseif(!in_array($share_res[$i]['user_id'], $share_arr) && $share_res[$i]['flag'] > 1){
					$share->setField('flag', $flag-1, "id=".$share_res[$i]['id']); // 删除共享用户当前权限
				}else{
					$share->delete($share_res[$i]['id']);  // 删除共享用户所有	权限
				}
			}
			$new_ids = array_diff($share_arr, $share_user_ids);  // 获取新增的共享用户id集
			foreach ($new_ids as $key => $value) {  // 插入新记录
				$cond['table_name'] = "Account";
				$cond['record_id'] = $id;
				$cond['user_id'] = $value;
				$cond['flag'] = $flag;
				$share->create($cond);
				$share->add();					
			}
		}
		$this->redirect("view/id/$id");
	}
	
	/**
	 * 客户删除操作
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
		$account = D('Account');
		$account->delete("id in($ids_string)"); // 删除客户记录
		$address = D('CrmAddress');
		$address->deleteAll("table_name='Account' and record_id in($ids_string)"); //删除已删除客户记录的相关地址记录
		$bank = D('CrmBank');
		$bank->deleteAll("table_name='Account' and record_id in($ids_string)"); //删除已删除客户记录的相关银行记录
		$this->redirect('lists');
	}
	
	/**
	 * 客户资料导出
	 */
	public function export()
	{	
		$filename = '客户资料';
		clearstatcache();//清除文件状态缓存
		ob_end_clean();		
       	header('Content-type: application/csv');
		header('Content-Disposition: inline; filename=' . urlencode($filename) . '.csv');
		
		$account = D("Account");
		$address = D('CrmAddress');
		$banks = D('CrmBank');
		$accountKeys = $account->getKeys();
		$addressKeys = $address->getKeys();
		$banksKeys = $banks->getKeys();
		$keys = array_merge($accountKeys, $addressKeys, $banksKeys);
		//print_r($keys);
		$keysSort = array();
		$keyValue = '';
		foreach ($keys as $key => $value) {
			$keysSort[] = $key;	
			if ($this->phpdigVerifyUTF8($value)) {
	        	$value = iconv("UTF-8", "GB2312", $value);
	        }
			$keyValue .= $value . ',';
		}

		echo $keyValue . "\n";	

		$accountList = $account->getList();
		//print_r($accountList);
	    if ($accountList) {
	        foreach ($accountList as $row){
	        	if ($row) {
	        		$disp_keys = '';
	        		foreach ($row as $key => $value) {
	        			if (in_array($key, $keysSort)) {
	        				$keys[$key] = $this->_format($value);
	        			}
	        		}
	        		$disp_keys = implode(',', $keys);
	        		echo $disp_keys . "\n";
	        	}
	        }		
	    }	
	}
	
	/**
	 * 客户资料导入 
	 */
	public function import(){
		$account = D('Account');
		//print_r($account->getFields());
		$GROUP_ID=$_REQUEST[GROUP_ID];
        $this->menu(); 	
        $this->assign("GROUP_ID",$GROUP_ID);			
		$this->display();
	}
	
	public function import_save()
	{	
		$file_name = $_FILES[CSV_FILE][name];//或者$FILE_NAME=$_POST[FILE_NAME];
		$file = $_FILES[CSV_FILE][tmp_name];
		
		if (strtolower(substr($file_name, -3)) != "csv") {
			$this->error("只能导入CSV文件");
		} 
		$fieldData = array();

		$f_handle = fopen($file, "r");
		$fileHead = explode(',', fgets($f_handle));
		
		$accountKeys = '';
		$addressKeys = '';
		$bankKeys = '';
		
		$account = D('Account');
		$address = D('CrmAddress');
		$bank = D('CrmBank');
		$user = D('User');
		
		$accountFields = $account->getKeys();
		foreach ($accountFields as $key => $value) {
			$accountKeys .= $key . ',';
			
		}
		$accountKeys = '(' . substr($accountKeys, 0, -1) . ')';
		//echo $accountKeys;
		
		$addressFields = $address->getKeys();
		foreach ($addressFields as $key => $value) {
			$addressKeys .= $key . ',';
		}
		$addressKeys = '(' . $addressKeys . 'record_id, table_name)';
		
		$bankFields = $bank->getKeys();
		foreach ($bankFields as $key => $value) {
			$bankKeys .= $key . ',';
		}
		$bankKeys = '(' . $bankKeys . 'record_id, table_name)';


		$count = 0;
		while (!feof($f_handle)){
			
			$field = fgets($f_handle);
			if ($field != NULL) {
				$fields = explode(',', $field);
				$i = 0;
				$accountData = '(';
				$addressData = '(';
				$bankData = '(';
				foreach ($fields as $key => $value) {
					if ($i < 18) {	
						if (!$this->phpdigVerifyUTF8($value)) {
				        	$value = iconv("GB2312", "UTF-8", $value);
							$userSele = $user->field("uid")->where("USER_NAME = '$value'")->find();
							if ($userSele) {
								$value = $userSele[uid];
							}
				        	$accountData .= '\'' . $value . '\',';
				        } else {
				        	$accountData .= '\'' . $value . '\',';
				        }
					} else if ($i > 17 && $i < 23) {
						if (!$this->phpdigVerifyUTF8($value)) {
			        		$value = iconv("GB2312", "UTF-8", $value);
			        		$addressData .= '\'' . $value . '\',';
			        	} else {
			        		$addressData .= '\'' . $value . '\',';
			        	}
					} else {
						if (!$this->phpdigVerifyUTF8($value)) {
			        		$value = iconv("GB2312", "UTF-8", $value);
			        		$bankData .= '\'' . $value . '\',';
			        	} else if ($i > 22 && $i < 33) {
			        		$bankData .= '\'' . $value . '\',';
			        	}
					}
					$i++;
				}
				$accountData = substr($accountData, 0, -1) . ')';
				
				
				$id = $account->addCsv($accountKeys, $accountData);
				$addressData = $addressData . $id . ", 'Account')";
				$address->addCsv($addressKeys, $addressData);
				$bankData = $bankData . $id . ", 'Account')";
				$bank->addCsv($bankKeys, $bankData);
			}
			$count++;
		}	
		
		$this->assign("jumpUrl",__URL__."/lists"); 
		$this->success("成功导入 $count 条记录");
	}

	protected 	function phpdigVerifyUTF8($str) {
	  // verify if a given string is encoded in valid utf-8
	  if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
	    return true;
	  }
	  else {
	    return false;
	  }
	}

	public function _format($STR){
	   $STR=str_replace("\"","'",$STR);
          $STR=iconv("UTF-8","GB2312",$STR);
          $STR=trim($STR);
          
	   if(strpos($STR,","))
	      $STR="\"".$STR."\"";
	   
	   return $STR;	
	}
	
	protected function _listAccountPriv()
	{
		$user = D('User');
		$userPriv = D(user_priv);
		$dept = D('Department');
		$currentUser = $user->where("uid = " . $_SESSION[LOGIN_UID])->find();
		//echo $currentUser['USER_PRIV'];
		if ($currentUser['USER_PRIV'] == 27) {
			$selectAccount = " manager_id = " . $currentUser['uid'];
			//echo $selectAccount;
			return $selectAccount;
		} else if ($currentUser['USER_PRIV'] == 4) {
			//echo $currentUser['DEPT_ID'];
			$selectUser = $user->where("DEPT_ID = " . $currentUser['DEPT_ID'])->findall();
			//print_r($selectUser);
			//$valuse = array_values($selectUser);
			//print_r($valuse);
			$users = '';
			foreach ($selectUser as $value) {
				$users .= $value['uid'] . ',';
			}
			//echo $users;
			$users = substr($users, 0, -1);
			$selectAccount = ' manager_id in (' . $users . ')';
			//echo $selectAccount;
			return $selectAccount;
		} else if ($currentUser['USER_PRIV'] == 28) {
			//echo $currentUser['DEPT_ID'];
			$selectDept = $dept->where("DEPT_PARENT = " . $currentUser['DEPT_ID'])->findall();
			//print_r($selectDept);
			if (null == $selectDept) {
				//echo 'null';
				$userDept = $dept->where("DEPT_Id = " . $currentUser['DEPT_ID'])->findall();
				//print_r($userDept);
				$farentDept = $userDept[0]['DEPT_PARENT'];
				//echo $farentDept;
				$selectDept = $dept->where("DEPT_PARENT = " . $farentDept)->findall();
			}
			$depts = '';
			foreach ($selectDept as $value) {
				$depts .= $value['DEPT_ID'] . ',';
			}
			//echo $depts;
			$depts = substr($depts, 0, -1);
			$selectUser = $user->where('DEPT_ID in (' . $depts . ')')->findall();
			//echo $user->getLastSql();
			//print_r($selectUser);
			$users = '';
			foreach ($selectUser as $value) {
				$users .= $value['uid'] . ',';
			}
			//echo $users;
			$users = substr($users, 0, -1);
			$selectAccount = ' manager_id in (' . $users . ') ';
			return $selectAccount;
		} else if ($currentUser['USER_PRIV'] == 24 || $currentUser['USER_PRIV'] == 32 ) {
			$selectAccount = '';
			return $selectAccount;
		}
	}
}
?>