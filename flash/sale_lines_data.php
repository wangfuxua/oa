<?php
include_once('php-ofc-library/open-flash-chart.php');
session_start();
$result = $_SESSION['account_res']; // 通过session获取查询结果
$parent = $_GET['p_t']; // 父统计方式
$parent_cond = urldecode($_GET['name']); // 父统计条件
$ch = $_GET['c_t'];  // 子统计条件
$cou = count($_GET); // get参数个数
$count_type = isset($_GET['type']) ? $_GET['type'] : 'all_sale'; // 统计类型:销售总额，产品销售额，服务销售额

//****************获取要分类的数组下标和名称*********************
$t = getFlag($parent);
$category = getName($parent);
$c_t = getFlag($ch);
$c_category = getName($ch);

//****************根据统计类型设置标题*********************
if($count_type == 'all_sale'){
	if($ch) $category = $parent_cond.' 按'.$c_category.' 统计销售总额'; 
	else $category .= ' 销售总额';
}elseif($count_type == 'pro_sale'){
	if($ch) $category = $parent_cond.' 按'.$c_category.' 统计产品销售额';
	else $category .= ' 产品销售额';
}elseif($count_type == 'ser_sale'){
	if($ch) $category = $parent_cond.' 按'.$c_category.' 统计服务销售额';
	else $category .= ' 服务销售额';
}

$vlaue_arr = $val_arr = $arr = $res = array();
if($ch){ // 进一步统计
	foreach ($result as $key => $value){
		if($value[$t] == $parent_cond){
			array_push($res, $value);
		}
	}	
	$t = $c_t;
}else{ // 第一次统计
	$res = $result;
}
//****************统计*********************
foreach ($res as $key => $value){
	if(in_array($value[$t], $arr)){ // 已有则相加
		$k = array_keys($arr,$value[$t]);
		$k = $k[0];
		if($parent == 'product' || $parent == 'product_type'){ // 产品或产品类型统计方式
			if($value['type'] == 1) $vlaue_arr[$k]['pro_sale'] = $vlaue_arr[$k]['pro_sale']+$value['sale_price']*$value['sale_num'];
		}elseif($parent == 'service'){ // 服务统计方式
			if($value['type'] == 2) $vlaue_arr[$k]['ser_sale'] = $vlaue_arr[$k]['ser_sale']+$value['sale_price']*$value['sale_num'];
		}else{ // 其他统计方式
			if($value['type'] == 1) $vlaue_arr[$k]['pro_sale'] = $vlaue_arr[$k]['pro_sale']+$value['sale_price']*$value['sale_num'];
			elseif($value['type'] == 2) $vlaue_arr[$k]['ser_sale'] = $vlaue_arr[$k]['ser_sale']+$value['sale_price']*$value['sale_num'];
		}
		$vlaue_arr[$k]['all_sale'] = $vlaue_arr[$k]['pro_sale']+$vlaue_arr[$k]['ser_sale'];
	}else{ // 没有则加入数组
		$val_arr['name'] = $value[$t];
		if($parent == 'product' || $parent == 'product_type'){ // 产品或产品类型统计方式
			if($value['type'] == 1){
				array_push($arr, $value[$t]);
				$val_arr['pro_sale'] = $value['sale_price']*$value['sale_num'];
				$val_arr['ser_sale'] = 0;
				$val_arr['all_sale'] = $val_arr['pro_sale']+$val_arr['ser_sale'];
				array_push($vlaue_arr, $val_arr);
			}
		}elseif($parent == 'service'){  // 服务统计方式
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

//****************根据统计类型设置数据的名和值*********************
$data = $k = array();
foreach ($vlaue_arr as $key => $value){
	array_push($k, $value['name']);
	array_push($data, $value[$count_type]);
}
//print_r($data);exit;
//****************画比例柱图*********************
$bar = new bar_outline( 50, '#9933CC', '#8010A0' );
$max_y = max($data);
//$min_y = min($data);
$bar->data = $data;
$g = new graph();
$g->title( $category, '{font-size: 16px; color: #404040;}' );
$g->data_sets[] = $bar;
$g->set_x_labels($k);
$g->set_x_label_style( 12, '#9933CC', 0, 1 );
$g->set_x_axis_steps( 2 );
//$g->x_axis_colour('#9933CC');
$g->set_y_max( $max_y );
$g->y_label_steps(5);
echo $g->render();

//****************获取要分类的数组下标*********************
function getFlag($val){
	switch ($val){
		case $val == 'year':
			return 'year_sale';
			break;
		case $val == 'month':
			return 'month_sale';
			break;
		case $val == 'dept':
			return 'dept_name';
			break;
		case $val == 'seller':
			return 'user_name';
			break;
		case $val == 'product':
			return 'product_name';
			break;
		case $val == 'product_type':
			return 'product_type';
			break;
		case $val == 'service':
			return 'product_name';
			break;
		case $val == 'area':
			return 'province';
			break;
		case $val == 'account_type':
			return 'account_type';
			break;
		case $val == 'account_industry':
			return 'industry';
			break;
	}
}
//****************获取要分类的名称*********************
function getName($val){
	switch ($val){
		case $val == 'year':
			return '年';
			break;
		case $val == 'month':
			return '月';
			break;
		case $val == 'dept':
			return '销售部门';
			break;
		case $val == 'seller':
			return '销售人员';
			break;
		case $val == 'product':
			return '产品名称';
			break;
		case $val == 'product_type':
			return '产品类型';
			break;
		case $val == 'service':
			return '服务名称';
			break;
		case $val == 'area':
			return '区域';
			break;
		case $val == 'account_type':
			return '客户类型';
			break;
		case $val == 'account_industry':
			return '客户行业';
			break;
	}
}
?>