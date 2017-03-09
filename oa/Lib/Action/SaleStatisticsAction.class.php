<?php
class SaleStatisticsAction extends PublicAction {
	public function search(){
		UserSelectAction::DeptSelect();
		/**获取客户来源,类型,行业*/
		//$xml_sou = new xml("account_source.xml", 'item');
		$xml_type = new xml("account_type.xml", 'item');
		$xml_ind = new xml("account_industry.xml", 'item');
		$xml_pro_type = new xml("product_type.xml", 'item');

		$this->_popSelectUser(); 	
		
		$this->assign('xml_pro_type',$xml_pro_type->array); // 产品类型记录集
		//$this->assign('xml_sou',$xml_sou->array); // 来源记录集
		$this->assign('xml_type',$xml_type->array); // 类型记录集
		$this->assign('xml_ind',$xml_ind->array); // 行业记录集
		$this->display();
	}
	
	public function statistics(){
		$user_ids_arr = $post_dept_ids_arr = $own_dept_ids_arr = array();
		$condition = $cond = $user_ids_string = '';
		
		$statistics_type = isset($_POST['statistics_type']) ? $_POST['statistics_type'] : '';  // 统计方式
		$sale_depts = isset($_POST['sale_dept_ID']) ? $_POST['sale_dept_ID'] : '';  // 选择的要统计的部门
		$seller_ids = isset($_POST['seller_id']) ? $_POST['seller_id'] : ''; // 选择要统计的人员
		$post_dept_ids_arr = explode(',', $sale_depts); // 将要统计的部门组合成数组
		unset($post_dept_ids_arr[count($post_dept_ids_arr)-1]); // 去除最后的空白元素
		
		$user = D('User');
		$current_user = $user->where("uid=$this->_uid")->find();  // 当前用户信息
		if($this->_uid != 1 && strpos(strtoupper($current_user['POST_DEPT']), 'ALL_DEPT') === false){ // 非系统管理员,非管理所有部门
			if(!$current_user['POST_DEPT']){	// 管理范围为本部门的统计先决条件
				$assign = D('UserAssign');
				$assign_res = $assign->where("manager_id=".$current_user['uid'])->find();  // 获取当前用户的人员配置信息
				if(!$assign_res){ // 没有人员配置信息,则为职员,只能统计自己的相关客户记录
					if(($post_dept_ids_arr && !empty($post_dept_ids_arr)) || ($seller_ids && $seller_ids != $current_user['uid'].",")) 
						$this->error("您只有统计自己客户的权限!");
					$condition .= "a.seller_id=$this->_uid";  
				}
				else{ // 统计管理范围在本部门的设置了人员配置的相关客户记录
					$assign_user_id_arr = explode(',', preg_replace('/\,$/', '', $assign_res['assign_user_id']));
					$post_user_id_arr = explode(',', preg_replace('/\,$/', '', $seller_ids));
					$array_diff = array_diff($post_user_id_arr, $assign_user_id_arr);
					if(($post_dept_ids_arr && !empty($post_dept_ids_arr) && $post_dept_ids_arr != array($current_user['DEPT_ID'])) 
						|| (!empty($array_diff))) 
						$this->error("您不能统计非管理范围内用户的客户!");
					$assign_user = preg_replace('/\,$/', '', $assign_res['assign_user_id']); // 人员配置的id串处理
					$condition .= "a.seller_id in ($assign_user)";
				}
			}else{ // 管理多部门的统计先决条件
				$own_dept_ids_arr = explode(',', $current_user['POST_DEPT']); // 用户管理的部门
				unset($own_dept_ids_arr[count($own_dept_ids_arr)-1]); 
				
				if(is_array($post_dept_ids_arr) && !empty($post_dept_ids_arr)){
					$dept_res = array_diff($post_dept_ids_arr, $own_dept_ids_arr); // 取post过来统计的部门和管理的部门相对与post过来的差集
					if(!empty($dept_res)) $this->error("您不能对没有管理权限的部门进行统计!"); // 如果要统计的部门不在管理范围内,提示信息
				}
				else $dept_res = $own_dept_ids_arr; // 或者默认所有管理的范围
				
				$manager_dept = join(',', $dept_res);
				$seller_ids = preg_replace('/\,$/', '', $seller_ids);
				if($manager_dept) $cond .= "DEPT_ID in ($manager_dept) and ";
				if($seller_ids){
					$select_dept = $user->field("DEPT_ID")->where("uid in ($seller_ids)")->findAll(); // 获取当前用户post过来的用户部门id
					foreach ($select_dept as $key => $value){ // 组合post人员的部门id数组
						$select_dept_arr[] = $value['DEPT_ID'];
					}
					$select_dept_arr = array_flip(array_flip($select_dept_arr)); // 去重
					$select_res = array_diff($select_dept_arr, $own_dept_ids_arr);
					if(!empty($select_res)) $this->error("您不能对没有管理权限人员的客户进行统计!"); // 如果要统计的部门不在管理范围内,提示信息
					$cond .= "uid in ($seller_ids) and ";
				}
				$cond = preg_replace('/\s*and\s*$/','',$cond);
				$user_res = $user->field("uid")->where($cond)->findAll(); // 获取当前用户post过来的部门和用户有权限查看的uid
				if($user_res){
					for($i=0;$i<count($user_res);$i++){
						array_push($user_ids_arr,$user_res[$i]['uid']);
					}
					$user_ids_string = join(',', $user_ids_arr);
					$condition .= "a.seller_id in ($user_ids_string)";
				}else{
					$this->error("您没有选中用户的统计权限!");
				}
			}
		}else{ // 系统管理员或者管理所有部门者统计先决条件组合
			$manager_dept = preg_replace('/\,$/', '', $sale_depts);
			$seller_ids = preg_replace('/\,$/', '', $seller_ids);
			if($manager_dept) $cond .= "DEPT_ID in ($manager_dept) and "; // 存在则组合部门查询条件
			if($seller_ids) $cond .= "uid in ($seller_ids) and "; // 存在则组合人员查询条件
			if($cond) $cond = preg_replace('/\s*and\s*$/','',$cond);
			/**获取组合条件下的要统计的用户*/
			$user_res = $user->field("uid")->where($cond)->findAll(); 
			if($user_res){
				for($i=0;$i<count($user_res);$i++){
					array_push($user_ids_arr,$user_res[$i]['uid']);
				}
				$user_ids_string = join(',', $user_ids_arr);
				$condition .= "a.seller_id in ($user_ids_string)";
			}else{ // 若条件不匹配,提示错误信息
				$this->error("没有相关部门下的用户!");
			}
		}
		/**组合post查询条件*/
		/**销售表统计条件组合*/
		if($_POST['pro_name']) $condition .= " and a.product_type=1 and a.product_name like '%".trim($_POST['pro_name'])."%'";
		if($_POST['ser_name']) $condition .= " and a.product_type=2 and a.product_name like '%".trim($_POST['ser_name'])."%'";
		if($_POST['ser_dec']) $condition .= " and a.description like '%".trim($_POST['ser_dec'])."%'";
		if($_POST['date_from']) $condition .= " and a.time_sale >= '".trim($_POST['date_from'])."'";
		if($_POST['date_to']) $condition .= " and a.time_sale <= '".trim($_POST['date_to'])."'";
		/**产品表统计条件组合*/
		if($_POST['pro_type']) $condition .= " and b.category_name like '%".trim($_POST['pro_type'])."%'";
		if($_POST['pro_encode']) $condition .= " and b.encode like '%".trim($_POST['pro_code'])."%'";
		/**客户表统计条件组合*/
		if($_POST['account_name']) $condition .= " and c.name like '%".trim($_POST['account_name'])."%'";
		if($_POST['type']) $condition .= " and c.type like '%".trim($_POST['type'])."%'";
		if($_POST['industry']) $condition .= " and c.industry like '%".trim($_POST['industry'])."%'";
		if($_POST['description']) $condition .= " and c.description like '%".trim($_POST['description'])."%'";
		/**客户地址信息表统计条件组合*/
		if($_POST['province']) $condition .= " and d.province like '%".trim($_POST['province'])."%'";
		if($_POST['street']) $condition .= " and d.street like '%".trim($_POST['street'])."%'";
		
		if($this->_uid == 1 && $sale_depts =='' && $seller_ids == ''){//系统管理员个人客户统计条件
			$admin_cond = $condition." and a.seller_id=$this->_uid and a.seller_id=e.uid and a.account_id=g.id and g.account_id=c.id and a.product_id=b.id and  c.id = d.record_id and d.table_name='Account'";
			$admin_cond = preg_replace('/^\s*and\s*/','',$admin_cond);
		}	
			
		$condition .= " and a.seller_id=e.uid and a.account_id=g.id and g.account_id=c.id and a.product_id=b.id and e.DEPT_ID=f.DEPT_ID and c.id = d.record_id and d.table_name='Account'";
		$condition = preg_replace('/^\s*and\s*/','',$condition);
		$account = D('Account');
		$account_res = $account->query("select a.id as id,date_format(time_sale,'%Y') as year_sale,date_format(time_sale,'%Y-%m') as month_sale,DEPT_NAME as dept_name,USER_NAME as user_name,b.name as product_name,b.category_name as product_type,b.type as type,a.sale_price as sale_price,a.sale_num as sale_num,industry,c.type as account_type,province from crm_sale a,crm_product b,crm_account c,crm_address d,user e,department f,crm_contract g where $condition order by a.time_create desc");
		
		if($this->_uid == 1 && $sale_depts =='' && $seller_ids == ''){//系统管理员个人客户统计
			$admin_res = $account->query("select a.id as id,date_format(time_sale,'%Y') as year_sale,date_format(time_sale,'%Y-%m') as month_sale,USER_NAME as user_name,b.name as product_name,b.category_name as product_type,b.type as type,a.sale_price as sale_price,a.sale_num as sale_num,industry,c.type as account_type,province from crm_sale a,crm_product b,crm_account c,crm_address d,user e,crm_contract g where $admin_cond order by a.time_create desc");
			if($admin_res){//合并结果数组
				foreach($admin_res as $key => $value){
					$admin_res[$key]['dept_name'] = 'other';
				}
				if($account_res) $account_res = array_merge($account_res,$admin_res);
				else $account_res = $admin_res;
			}
		}
		//echo $account->getLastSql();exit;
//		print_r($account_res);
		if(!$account_res || empty($account_res)) $this->error("没有相关记录!");
		/**根据统计方式进行相关的数组组合*/
		$arr = $vlaue_arr = $val_arr = array();
		if($statistics_type == 'year'){
			$t = 'year_sale';
			$category = '年';
		}
		elseif($statistics_type == 'month'){
			$t = 'month_sale';
			$category = '月';
		}
		elseif($statistics_type == 'dept'){
			$t = 'dept_name';
			$category = '销售部门';
		}
		elseif($statistics_type == 'seller'){
			$t = 'user_name';
			$category = '销售人员';
		}
		elseif($statistics_type == 'product'){
			$t = 'product_name';
			$category = '产品名称';
		}
		elseif($statistics_type == 'product_type'){
			$t = 'product_type';
			$category = '产品类型';
		}
		elseif($statistics_type == 'service'){
			$t = 'product_name';
			$category = '服务名称';
		}
		elseif($statistics_type == 'area'){
			$t = 'province';
			$category = '区域';
		}
		elseif($statistics_type == 'account_type'){
			$t = 'account_type';
			$category = '客户类型';
		}
		elseif($statistics_type == 'account_industry'){
			$t = 'industry';
			$category = '客户行业';
		}
		foreach ($account_res as $key => $value){
			if(in_array($value[$t], $arr)){ // 已有则相加
				$k = array_keys($arr,$value[$t]);
				$k = $k[0];
				if($statistics_type == 'product' || $statistics_type == 'product_type'){ // 产品或产品类型统计方式
					if($value['type'] == 1) $vlaue_arr[$k]['pro_sale'] = $vlaue_arr[$k]['pro_sale']+$value['sale_price']*$value['sale_num'];
				}elseif($statistics_type == 'service'){ // 服务统计方式
					if($value['type'] == 2) $vlaue_arr[$k]['ser_sale'] = $vlaue_arr[$k]['ser_sale']+$value['sale_price']*$value['sale_num'];
				}else{ // 其他统计方式
					if($value['type'] == 1) $vlaue_arr[$k]['pro_sale'] = $vlaue_arr[$k]['pro_sale']+$value['sale_price']*$value['sale_num'];
					elseif($value['type'] == 2) $vlaue_arr[$k]['ser_sale'] = $vlaue_arr[$k]['ser_sale']+$value['sale_price']*$value['sale_num'];
				}
				$vlaue_arr[$k]['all_sale'] = $vlaue_arr[$k]['pro_sale']+$vlaue_arr[$k]['ser_sale'];
			}else{ // 没有则加入数组
				$val_arr['name'] = $value[$t];
				if($statistics_type == 'product' || $statistics_type == 'product_type'){ // 产品或产品类型统计方式
					if($value['type'] == 1){
						array_push($arr, $value[$t]);
						$val_arr['pro_sale'] = $value['sale_price']*$value['sale_num'];
						$val_arr['ser_sale'] = 0;
						$val_arr['all_sale'] = $val_arr['pro_sale']+$val_arr['ser_sale'];
						array_push($vlaue_arr, $val_arr);
					}
				}elseif($statistics_type == 'service'){  // 服务统计方式
					if($value['type'] == 2){
						array_push($arr, $value[$t]);
						$val_arr['ser_sale'] = $value['sale_price']*$value['sale_num'];
						$val_arr['pro_sale'] = 0;
						$val_arr['all_sale'] = $val_arr['pro_sale']+$val_arr['ser_sale'];
						array_push($vlaue_arr, $val_arr);
					}
				}else{ // 其他统计方式
					array_push($arr, $value[$t]);
					if($value['type'] == 1){
						$val_arr['pro_sale'] = $value['sale_price']*$value['sale_num'];
						$val_arr['ser_sale'] = 0;
					}elseif($value['type'] == 2){
						$val_arr['ser_sale'] = $value['sale_price']*$value['sale_num'];
						$val_arr['pro_sale'] = 0;
					}
					$val_arr['all_sale'] = $val_arr['pro_sale']+$val_arr['ser_sale'];
					array_push($vlaue_arr, $val_arr);
				}
			}
		}
		/**销售总额,产品销售额,服务销售额小记*/
		$all_sale = $pro_sale = $ser_sale = array();
		foreach ($vlaue_arr as $k => $v){
			array_push($all_sale, $v['all_sale']);
			array_push($pro_sale, $v['pro_sale']);
			array_push($ser_sale, $v['ser_sale']);
		}
		session_start(); // 写session，以便子统计时用
		$_SESSION['account_res'] = $account_res;
		
		$this->assign('p_t',$statistics_type);
		$this->assign('category',$category);
		$this->assign('res',$vlaue_arr);
		$this->assign('all_sale',array_sum($all_sale));
		$this->assign('pro_sale',array_sum($pro_sale));
		$this->assign('ser_sale',array_sum($ser_sale));
		$this->display();
	}	
}
?>