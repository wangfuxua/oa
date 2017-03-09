<?php
class SaleAction extends PublicAction {
	public function create(){		
		$user = D('User');
		$user_res = $user->where("uid=$this->_uid")->field("uid,USER_NAME")->find();

#########弹出框开始#########  
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        	$this->assign("list_d",$list_d);
        	$this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
#########弹出框结束#########  	

		$this->assign('sale_res', $user_res);
		if($_GET['type']) $this->display();
		else $this->display('screate');
	}
	
	public function save(){	
		$_POST['time_create'] = date('y-m-d H:i:s');
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['builder_id'] = $this->_uid;
		$_POST['last_modifier_id'] = $this->_uid;
		$_POST['product_type'] = isset($_GET['type']) ? 1 : 2;
		if($_POST['payment']){
			for($i = 0; $i < count($_POST['payment']); $i++){
				if($_POST['payment'][$i] == '' || $_POST['no_payment'][$i] == '' || $_POST['pay_time'][$i] == '')$this->error('请正确填写回款记录!');
			}
		}
		$sale = D('Sale');
		//print_r($_POST);exit;
		$sale->create($_POST);
		$id = $sale->add();
		if($id){
			/**创建回款记录*/
			if($_POST['payment']){
				$payment = D('Payment');
				for($i = 0; $i < count($_POST['payment']); $i++){
					/**组合创建当前回款记录条件*/
					$payment_data[$i]['sale_id'] = $id;
					$payment_data[$i]['product_id'] = $_POST['product_id'];
					$payment_data[$i]['product_name'] = $_POST['product_name'];
					$payment_data[$i]['product_sale_price'] = $_POST['sale_price'];
					$payment_data[$i]['product_sale_num'] = $_POST['sale_num'];
					if($_POST['payment'][$i]) $payment_data[$i]['payment'] = $_POST['payment'][$i];
					if($_POST['no_payment'][$i]) $payment_data[$i]['no_payment'] = $_POST['no_payment'][$i];
					if($_POST['pay_time'][$i]) $payment_data[$i]['pay_time'] = $_POST['pay_time'][$i];
					if($payment->create($payment_data[$i])){
						$payment->add(); 
					}
				}
			}			
			if($_GET['type']) $this->redirect("view/id/$id/type/1");
			else $this->redirect("view/id/$id");
		}
		else{
			$this->error('创建失败!');
		}
	}
	
	public function view(){
		$id = $_GET['id'];
		if(!$id){
			if($_GET['type']) $this->redirect('lists/type/1');
			else $this->redirect('lists');
		}
		$sale = D('Sale');
		$sale_res = $sale->find($id);
		$user = D('User');
		$user_res = $user->where("uid=".$sale_res['seller_id'])->field("USER_NAME")->find();
		$sale_res['USER_NAME'] = $user_res['USER_NAME'];
		$payment = D('Payment');
		$payment_res = $payment->where("sale_id=$id")->findAll();
		
		if($sale_res['builder_id'] == $this->_uid) 
			$this->assign('builder', 1);
		$this->assign('sale_res', $sale_res);
		$this->assign('payment_res', $payment_res);
		if($_GET['type']) $this->display();
		else $this->display('sview');
	}

	public function edit(){
		$id = isset($_GET['id']) ? $_GET['id'] : null; 
		$update = isset($_GET['update']) ? $_GET['update'] : null; 
		if (!$id){
			if($_GET['type']) $this->redirect('lists/type/1');
			else $this->redirect('lists');
		}
		$sale = D('Sale');
		$sale_res = $sale->find($id);
		$user = D('User');
		$user_res = $user->where("uid=".$sale_res['seller_id'])->field("uid,USER_NAME")->find();
		$sale_res['uid'] = $user_res['uid'];
		$sale_res['USER_NAME'] = $user_res['USER_NAME'];
		$payment = D('Payment');
		$payment_res = $payment->where("sale_id=$id")->findAll();
		$this->assign('update', $update);
		$this->assign('sale_res',$sale_res);
		$this->assign('payment_res', $payment_res);
		if($_GET['type']) $this->display('create');
		else $this->display('screate');	
	}
	
	public function update(){
		$id = isset($_POST['sale_id']) ? $_POST['sale_id'] : null;
		if (null == $id){
			if($_GET['type']) $this->redirect('lists/type/1');
			else $this->redirect('lists');
		}
		if($_POST['payment']){
			for($i = 0; $i < count($_POST['payment']); $i++){
				if($_POST['payment'][$i] == '' || $_POST['no_payment'][$i] == '' || $_POST['pay_time'][$i] == '')$this->error('请正确填写回款记录!');
			}
		}
		/**根据$_POST对指定id数据进行更新*/
		$_POST['time_modify'] = date('y-m-d H:i:s');
		$_POST['last_modifier_id'] = $this->_uid;
		//$_POST['product_type'] = isset($_GET['type']) ? 1 : 2;
		$sale = D('Sale');
		if ($sale->create()){
			$sale->where("id='$id'")->save();
			/**创建回款记录*/
			if($_POST['payment']){
				$payment = D('Payment');
				for($i = 0; $i < count($_POST['payment']); $i++){
					/**组合创建当前回款记录条件*/
					$payment_data[$i]['sale_id'] = $id;
					$payment_data[$i]['product_id'] = $_POST['product_id'];
					$payment_data[$i]['product_name'] = $_POST['product_name'];
					$payment_data[$i]['product_sale_price'] = $_POST['sale_price'];
					$payment_data[$i]['product_sale_num'] = $_POST['sale_num'];
					if($_POST['payment'][$i]) $payment_data[$i]['payment'] = $_POST['payment'][$i];
					if($_POST['no_payment'][$i]) $payment_data[$i]['no_payment'] = $_POST['no_payment'][$i];
					if($_POST['pay_time'][$i]) $payment_data[$i]['pay_time'] = $_POST['pay_time'][$i];
					if($payment->create($payment_data[$i])){
						if($_POST['pay_id'][$i])$payment->where("id=".$_POST['pay_id'][$i]."")->save(); // 修改
						else $payment->add(); // 新建
					}
				}
			}
		}
		if($_GET['type']) $this->redirect("view/id/$id/type/1");
		else $this->redirect("view/id/$id");
	}
	
	public function lists(){
		$sale = D('Sale');
		if($_GET['type']) $condition = "product_type=1";
		else $condition = "product_type=2";
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		$count	= $sale->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows);
		$list	=	$sale->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		$page	=	$p->show();
		$payment = D('Payment');
		if($list){
			for($i=0;$i<count($list);$i++){
				$payment_res = $payment->where("sale_id=".$list[$i]['id'])->field("sum(payment) as payment")->find();
				$list[$i]['payment'] = $payment_res['payment'];
				$list[$i]['no_payment'] = round($list[$i]['sale_price']*$list[$i]['sale_num'],2)-$payment_res['payment'];
			}
		}
		
		$this->assign('sale_res',$list);
		$this->assign('page',$page);
		if($_GET['type']){
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&type=".$_GET['type']."&sort=".$def_sort); // 排序操作路径
			$this->display();
		}
		else{
		$this->assign('sort_url',__URL__."/lists/?&p=".$p->nowPage."&type=".$_GET['type']."&sort=".$def_sort); // 排序操作路径
			$this->display('slists');
		}
	}
	
	function search(){
		$cond = $param = '';
		$sale = D('Sale');
		if($_GET['type']) $condition = "product_type=1";
		else $condition = "product_type=2";
		$param .= 'type='.$_GET['type'].'&';
		//unset($_GET['type']);
		if($_POST){
			/**组合post过来的查询条件$condition和分页url参数$param*/
			foreach($_POST as $key => $value){
				if($key != 'type' && $value){
					if($key == "date_from") $cond .= 'time_sale >="'.$value.' 00:00:00" and ';
					else if($key == "date_to") $cond .= 'time_sale <="'.$value.' 23:59:59" and ';
					else $cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		else if($_GET){
			/**组合get过来的查询条件$condition和分页url参数$param*/
			foreach($_GET as $key => $value){
				if($key != 'type' && $value && $key != 'a' && $key != 'm' && $key != 'p' && $key != 'field' && $key != 'sort'){
					if($key == "date_from") $cond .= 'time_sale >="'.$value.' 00:00:00" and ';
					else if($key == "date_to") $cond .= 'time_sale <="'.$value.' 23:59:59" and ';
					else $cond .= $key.' like "%'.$value.'%" and ';
					$param .= $key."=".urlencode($value)."&";
				}
			}
		}
		$cond = substr($cond,0,-4);
		$param = substr($param,0,-1);
		if($cond)$condition .= " and ".$cond;
		/**字段排序*/
		if($_GET['field']){
			$sort = $_GET['field']." ".$_GET['sort'];
			$def_sort = ($_GET['sort'] == 'asc') ? 'desc' : 'asc';
		}else{
			$sort = 'time_modify desc';
			$def_sort = 'desc';
		}
		
		$count	= $sale->count($condition);
		$rows	=	10;
		$p		=	new Page($count,$rows,$param);
		$list	=	$sale->findAll($condition,'*',$sort,$p->firstRow.','.$p->listRows);
		//echo $sale->getLastSql();exit;
		$page	=	$p->show();
		$payment = D('Payment');
		if($list){
			for($i=0;$i<count($list);$i++){
				$payment_res = $payment->where("sale_id=".$list[$i]['id'])->field("sum(payment) as payment")->find();
				$list[$i]['payment'] = $payment_res['payment'];
				$list[$i]['no_payment'] = round($list[$i]['sale_price']*$list[$i]['sale_num'],2)-$payment_res['payment'];
			}
		}
		
		$this->assign('sale_res',$list);
		$this->assign('page',$page);
		if($_GET['type']){
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
			$this->display('lists');
		}
		else{
		$this->assign('sort_url',__URL__."/search/?&p=".$p->nowPage."&".$param."&sort=".$def_sort); // 排序操作路径
			$this->display('slists');
		}
	}
	
	public function del(){
		//获取传递过来要删除的id数组
		$ids_array = isset($_POST["mass"]) ? $_POST["mass"] : null;
		$id = isset($_POST["id"]) ? $_POST["id"] : null;
		if (null == $ids_array && null == $id){
			if($_GET['type']) $this->redirect('lists/type/1');
			else $this->redirect('lists');
		}
		//将id数组组合成字符串
		if ($id)
			$ids_string = $id;
		else
			$ids_string = join(',', $ids_array);
		
		$sale = D('Sale');
		$sale->delete("id in($ids_string)");
		$payment = D('Payment');
		$payment->deleteAll("sale_id in($ids_string)"); //删除已删除销售记录的相关回款记录
		if($_GET['type']) $this->redirect('lists/type/1');
		else $this->redirect('lists');
	}
}
?>