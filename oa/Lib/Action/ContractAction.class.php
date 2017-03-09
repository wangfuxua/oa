<?php
class ContractAction extends PublicAction {
	
	public function create(){
		$user = D('User');
		$user_res = $user->where("uid=$this->_uid")->field("uid,USER_NAME")->find(); // 获取当前用户信息
#########弹出框开始#########  
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        	$this->assign("list_d",$list_d);
        	$this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
#########弹出框结束#########  	

		$this->assign('contract_res', $user_res);
		$this->display();
	}
	
	public function save(){
		$payment_data = array();
		if($_POST){
			/**组合合同记录创建条件*/
			if(!$_POST['content']) unset($_POST['content']);
			if(!$_POST['end_prompt_time']) unset($_POST['end_prompt_time']);
			if(!$_POST['end_prompt_user_id']) unset($_POST['end_prompt_user_id']);
			if(!$_POST['end_prompt_user_name']) unset($_POST['end_prompt_user_name']);
			if(!$_POST['buyer_phone']) unset($_POST['buyer_phone']);
			if(!$_POST['seller_phone']) unset($_POST['seller_phone']);
			if(!$_POST['notation']) unset($_POST['notation']);
			$_POST['time_create'] = date('Y-m-d');
			$_POST['time_modify'] = date('Y-m-d');
			$_POST['builder_id'] = $this->_uid;
			$_POST['last_modifier_id'] = $this->_uid;
		
			$contract = D('Contract');
			if($contract->create($_POST)){
				$id = $contract->add(); // 创建合同记录
				if($id){
					/**创建合同到期提醒记录*/
					if($_POST['end_prompt_time'] && $_POST['end_prompt_user_id']){
						$ids_string = eregi_replace('\s*,$', '', $_POST['end_prompt_user_id']);
						$user = D('User');
						$user_res = $user->where("uid in (".$ids_string.")")->field("USER_ID")->findAll();
						foreach ($user_res as $key => $value){
							$content = "合同 <a href='".__URL__."/view/id/".$id."' target='_blank'>".$_POST['name']."</a> 即将到期,请点击查看详情!";
							$this->send_sms($this->LOGIN_USER_ID,$value['USER_ID'],'18', $content);
						}
					}
//					/**创建回款记录*/
//					if($_POST['product_id']){
//						$payment = D('Payment');
//						for($i = 0; $i < count($_POST['product_id']); $i++){
//							/**组合创建当前回款记录条件*/
//							$payment_data[$i]['contract_id'] = $id;
//							$payment_data[$i]['product_id'] = $_POST['product_id'][$i];
//							$payment_data[$i]['product_name'] = $_POST['product_name'][$i];
//							$payment_data[$i]['product_sale_price'] = $_POST['sale_price'][$i];
//							$payment_data[$i]['product_sale_num'] = $_POST['sale_num'][$i];
//							if($_POST['payment'][$i]) $payment_data[$i]['payment'] = $_POST['payment'][$i];
//							if($_POST['pay_time'][$i]) $payment_data[$i]['pay_time'] = $_POST['pay_time'][$i];
//							if($payment->create($payment_data[$i])){
//								$payment->add(); 
//							}
//						}
//					}
					$this->redirect("view/id/$id");
				}
			}
		}
		$this->error('创建失败,请确认信息无误!');
	}
	
	public function view(){
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		if(!$id) $this->error('当前记录id不为空!');
		
		$contract = D('Contract');
		$contract_res = $contract->find($id);
		if(!$contract_res) $this->error('当前记录不存在!');
		$user = D('User');
		$manager_res = $user->where("uid=".$contract_res['seller_id'])->field("uid,USER_NAME")->find();
		$contract_res['uid'] = $manager_res['uid'];
		$contract_res['USER_NAME'] = $manager_res['USER_NAME'];
		
		/**获取销售记录*/
		$sale = D('Sale');
		$sale_res = $sale->where("account_id=$id")->findAll();
		if($sale_res){
			/**获取回款记录*/
			$payment = D('Payment');
			foreach($sale_res as $key => $value){
				$sale_res[$key]['money_count'] = round($value['sale_price']*$value['sale_num']); // 计算总金额
				$payment_result = $payment->where("sale_id=".$value['id'])->field("sum(payment) as payment")->findAll();
				$sale_res[$key]['payment'] = $payment_result[0]['payment']; // 计算欠款
				$sale_res[$key]['debt'] = round($sale_res[$key]['money_count']-$sale_res[$key]['payment']); // 计算欠款
				
				$payment_res = $payment->where("sale_id=".$value['id'])->findAll();
				if($payment_res){
					$sale_res[$key]['pay_record'] = $payment_res;
				}
			}
		}
		
		$this->assign('contract_res', $contract_res);
		$this->assign('sale_res', $sale_res);
		//$this->assign('payment_res', $payment_res);
		$this->display();
	}
	
//	public function edit(){
//		$id = isset($_GET['id']) ? $_GET['id'] : null;
//		$update = isset($_GET['update']) ? $_GET['update'] : null;  // 存在则更新,否则复制
//		if(!$id) $this->error('当前记录id不为空!');
//		$contract = D('Contract');
//		$contract_res = $contract->find($id);
//		if(!$contract_res) $this->error('当前记录不存在!');
//		$user = D('User');
//		$manager_res = $user->where("uid=".$contract_res['seller_id'])->field("uid,USER_NAME")->find();
//		$contract_res['uid'] = $manager_res['uid'];
//		$contract_res['USER_NAME'] = $manager_res['USER_NAME'];
//		$payment = D('Payment');
//		$payment_res = $payment->where("contract_id=$id")->findAll();
//		if($payment_res){
//			for($i = 0; $i < count($payment_res); $i++){
//				$payment_res[$i]['money_count'] = round($payment_res[$i]['product_sale_price']*$payment_res[$i]['product_sale_num']); // 计算总金额
//				$payment_res[$i]['debt'] = round($payment_res[$i]['money_count']-$payment_res[$i]['payment']); // 计算欠款
//				if(!$update) unset($payment_res[$i]['id']); // 复制当前记录,释放当前记录的各回款记录的id值
//			}
//		}
//		
//		$this->assign('contract_res', $contract_res);
//		$this->assign('payment_res', $payment_res);
//		$this->assign('update', $update);
//		$this->display('create');		
//	}
	
//	public function update(){
//		$id = isset($_POST['contract_id']) ? $_POST['contract_id'] : null;
//		if(!$id) $this->error('更新记录id不为空!');
//		$payment_data = array();
//		if($_POST){
//			/**合同更新条件创建*/
//			if(!$_POST['content']) unset($_POST['content']);
//			if(!$_POST['end_prompt_time']) unset($_POST['end_prompt_time']);
//			if(!$_POST['end_prompt_user_id']) unset($_POST['end_prompt_user_id']);
//			if(!$_POST['end_prompt_user_name']) unset($_POST['end_prompt_user_name']);
//			if(!$_POST['buyer_phone']) unset($_POST['buyer_phone']);
//			if(!$_POST['seller_phone']) unset($_POST['seller_phone']);
//			if(!$_POST['notation']) unset($_POST['notation']);
//			$_POST['time_modify'] = date('Y-m-d');
//			$_POST['last_modifier_id'] = $this->_uid;
//		
//			$contract = D('Contract');
//			if($contract->create($_POST)){
//				$contract->where("id=$id")->save();
//				/**创建合同到期提醒记录*/
//				if($_POST['end_prompt_time'] && $_POST['end_prompt_user_id']){
//					$ids_string = eregi_replace('\s*,$', '', $_POST['end_prompt_user_id']);
//					$user = D('User');
//					$user_res = $user->where("uid in (".$ids_string.")")->field("USER_ID")->findAll();
//					foreach ($user_res as $key => $value){
//						$content = "合同 <a href='".__URL__."/view/id/".$id."' target='_blank'>".$_POST['name']."</a> 即将到期,请点击查看详情!";
//						$this->send_sms($this->LOGIN_USER_ID,$value['USER_ID'],'18', $content);
//					}
//				}
//				/**创建回款记录*/
//				if($_POST['product_id']){
//					$payment = D('Payment');
//					/**当前合同记录下已有回款记录修改或新建*/
//					for($i = 0; $i < count($_POST['product_id']); $i++){
//						$payment_data[$i]['contract_id'] = $id;
//						$payment_data[$i]['product_id'] = $_POST['product_id'][$i];
//						$payment_data[$i]['product_name'] = $_POST['product_name'][$i];
//						$payment_data[$i]['product_sale_price'] = $_POST['sale_price'][$i];
//						$payment_data[$i]['product_sale_num'] = $_POST['sale_num'][$i];
//						if($_POST['payment'][$i]) $payment_data[$i]['payment'] = $_POST['payment'][$i];
//						if($_POST['pay_time'][$i]) $payment_data[$i]['pay_time'] = $_POST['pay_time'][$i];
//						if($payment->create($payment_data[$i])){
//							if($_POST['pay_id'][$i])$payment->where("id=".$_POST['pay_id'][$i]."")->save(); // 修改
//							else $payment->add(); // 新建
//						}
//					}
//				}
//				$this->redirect("view/id/$id");
//			}	
//		}	
//		$this->error('记录更新失败!');
//	}
	
	public function lists(){
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		
		$contract = D('Contract');
		$condition = "builder_id= $this->_uid or seller_id=$this->_uid";
		
		$count = $contract->count($condition);
		$rows = 10;
		$p = new Page($count,$rows);
		$list =	$contract->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page = $p->show();
		if($list){
			$user = D('User');
			$sale = D('Sale');
			$payment = D('Payment');
			for($i=0; $i < count($list); $i++){
				$manager = $user->where("uid=".$list[$i]['seller_id'])->field("uid,USER_NAME")->find();
				$list[$i]['seller_name'] = $manager['USER_NAME'];
				$list[$i]['payment'] = $sale_res['payment'];
				$list[$i]['no_payment'] = $sale_res['no_payment'];
				/**获取销售记录*/
				$sale_res = $sale->where("account_id=".$list[$i]['id'])->findAll();
				if($sale_res){
					/**获取回款记录*/
					foreach($sale_res as $key => $value){
						$sale_res[$key]['money_count'] = round($value['sale_price']*$value['sale_num']); // 计算总金额
						$payment_result = $payment->where("sale_id=".$value['id'])->field("sum(payment) as payment")->findAll();
						$list[$i]['payment'][] = $payment_result[0]['payment']; // 计算回款
						$list[$i]['no_payment'][] = round($sale_res[$key]['money_count']-$payment_result[0]['payment']); // 计算欠款
					}
					$list[$i]['payment'] = array_sum($list[$i]['payment']);
					$list[$i]['no_payment'] = array_sum($list[$i]['no_payment']);
				}
			}			
		}
		
		$this->assign('contract_res', $list);
		$this->assign('page', $page);
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('contractList');	
		}else{
			$this->display();
		}	
	}
	
	public function search(){
		$contract = D('Contract');
		$condition = "(builder_id= $this->_uid or seller_id=$this->_uid)";
		$arr = array();
		$param = '';
		if($_POST) $arr = $_POST;
		else if($_GET)  $arr = $_GET;
		if($arr['code']){
			$condition .= ' and code like "%'.trim($arr['code']).'%"';
			$param .= 'code='.urlencode($arr['code']).'&';
		}
		if($arr['name']){
			$condition .= ' and name like "%'.trim($arr['name']).'%"';
			$param .= 'name='.urlencode($arr['name']).'&';
		}
		if($arr['account_name']){
			$condition .= ' and account_name like "%'.trim($arr['account_name']).'%"';
			$param .= 'account_name='.urlencode($arr['account_name']).'&';
		}
		if($arr['date_from']){
			$condition .= " and ".$arr['time_type']." >= '".trim($arr['date_from'])."'";
			$param .= 'date_from='.urlencode($arr['date_from']).'&';
		}
		if($arr['date_to']){
			$condition .= " and ".$arr['time_type']." <= '".trim($arr['date_to'])."'";
			$param .= 'date_to='.urlencode($arr['date_to']).'&';
		}
		$param .= 'time_type='.urlencode($arr['time_type']).'&';
		$param = substr($param,0,-1);
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		
		$count = $contract->count($condition);
		$rows = 10;
		$p = new Page($count,$rows,$param);
		$list =	$contract->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		//echo $contract->getLastSql();exit;
		$page = $p->show();
		$user = D('User');
		$sale = D('Sale');
		if($list){
			for($i=0; $i < count($list); $i++){
				$manager = $user->where("uid=".$list[$i]['seller_id'])->field("uid,USER_NAME")->find();
				$list[$i]['seller_name'] = $manager['USER_NAME'];
				$sale_res = $sale->where("account_id=".$list[$i]['id'])->field("sum(payment) as payment,sum(no_payment) as no_payment")->find();
				$list[$i]['payment'] = $sale_res['payment'];
				$list[$i]['no_payment'] = $sale_res['no_payment'];
			}
		}
		
		$this->assign('contract_res', $list);
		$this->assign('page', $page);
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
		if($_GET['to_id']){
			$this->assign('to_id',$_GET['to_id']);
			$this->assign('to_name',$_GET['to_name']);
			$this->display('contractList');	
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
		$contract = D('Contract');
		$contract->deleteAll("id in($ids_string)"); // 删除合同记录
		$sale_ids_string = array();
		$sale = D('Sale');
		$sale_ids = $sale->where("account_id in($ids_string)")->field("id")->findAll();
		foreach ($sale_ids as $key => $value){
			$sale_ids_string[] = $value['id'];
		}
		$sale_ids_string =join(',', $sale_ids_string);
		$sale->where("account_id in($ids_string)")->deleteAll(); // 删除已删除合同相关销售记录
		
		$payment = D('Payment');
		$payment->deleteAll("sale_id in($sale_ids_string)"); //删除已删除销售记录的相关回款记录
		$this->redirect('lists');
	}
}
?>