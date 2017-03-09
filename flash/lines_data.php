<?php
include_once('php-ofc-library/open-flash-chart.php');
session_start();
$result = $_SESSION['account_res']; // 通过session获取查询结果
$parent = $_GET['p_t']; // 父统计范围
$parent_cond = urldecode($_GET['name']); // 父统计条件
$ch = $_GET['c_t'];  // 子统计条件
$cou = count($_GET); // get参数个数
$new_res = $new_key = $new_val = array();

if($parent == 'dept'){	// 销售部门统计
	foreach ($result as $key => $value){
		if($cou == 1){ // 只按部门统计
			$title = '按销售部门统计';
			$new_res[] = $value['dept_name'];
		}
		else if($value['dept_name'] == $parent_cond){ // 当前部门下的细化统计
			if($ch == 'seller'){
				$title = $parent_cond.'的客户,按销售人员统计';
				$new_res[] = $value['user_name'];
			}
			if($ch == 'area'){
				$title = $parent_cond.'的客户,按地区统计';
				$new_res[] = $value['province'];
			}
			if($ch == 'account_type'){
				$title = $parent_cond.'的客户,按客户类型统计';
				$new_res[] = $value['type'];
			}
			if($ch == 'account_industry'){
				$title = $parent_cond.'的客户,按客户行业统计';
				$new_res[] = $value['industry'];
			}
		}
	}
}
if($parent == 'seller'){	
	foreach ($result as $key => $value){
		if($cou == 1){
			$title = '按销售人员统计';
			$new_res[] = $value['user_name'];
		}
		else if($value['user_name'] == $parent_cond){
			if($ch == 'dept'){
				$title = $parent_cond.'的客户,按销售部门统计';
				$new_res[] = $value['dept_name'];
			}
			if($ch == 'area'){
				$title = $parent_cond.'的客户,按地区统计';
				$new_res[] = $value['province'];
			}
			if($ch == 'account_type'){
				$title = $parent_cond.'的客户,按客户类型统计';
				$new_res[] = $value['type'];
			}
			if($ch == 'account_industry'){
				$title = $parent_cond.'的客户,按客户行业统计';
				$new_res[] = $value['industry'];
			}
		}
	}
}
if($parent == 'area'){	
	foreach ($result as $key => $value){
		if($cou == 1){
			$title = '按客户所在地区统计';
			$new_res[] = $value['province'];
		}else if($cou > 1 && $value['province'] == $parent_cond){
			if($ch == 'dept'){
				$title = $parent_cond.'的客户,按销售部门统计';
				$new_res[] = $value['dept_name'];
			}
			if($ch == 'seller'){
				$title = $parent_cond.'的客户,按销售人员统计';
				$new_res[] = $value['user_name'];
			}
			if($ch == 'account_type'){
				$title = $parent_cond.'的客户,按客户类型统计';
				$new_res[] = $value['type'];
			}
			if($ch == 'account_industry'){
				$title = $parent_cond.'的客户,按客户行业统计';
				$new_res[] = $value['industry'];
			}
		}
	}
}
if($parent == 'account_type'){	
	foreach ($result as $key => $value){
		if($cou == 1){
			$title = '按客户类型统计';
			$new_res[] = $value['type'];
		}
		else if($value['type'] == $parent_cond){
			if($ch == 'dept'){
				$title = $parent_cond.'的客户,按销售部门统计';
				$new_res[] = $value['dept_name'];
			}
			if($ch == 'seller'){
				$title = $parent_cond.'的客户,按销售人员统计';
				$new_res[] = $value['user_name'];
			}
			if($ch == 'area'){
				$title = $parent_cond.'的客户,按客户所在地区统计';
				$new_res[] = $value['province'];
			}
			if($ch == 'account_industry'){
				$title = $parent_cond.'的客户,按客户行业统计';
				$new_res[] = $value['industry'];
			}
		}
	}
}
if($parent == 'account_industry'){	
	foreach ($result as $key => $value){
		if($cou == 1){
			$title = '按客户所属行业统计';
			$new_res[] = $value['industry'];
		}
		else if($value['industry'] == $parent_cond){
			if($ch == 'dept'){
				$title = $parent_cond.'的客户,按销售部门统计';
				$new_res[] = $value['dept_name'];
			}
			if($ch == 'seller'){
				$title = $parent_cond.'的客户,按销售人员统计';
				$new_res[] = $value['user_name'];
			}
			if($ch == 'area'){
				$title = $parent_cond.'的客户,按客户所在地区统计';
				$new_res[] = $value['province'];
			}
			if($ch == 'account_type'){
				$title = $parent_cond.'的客户,按客户类型统计';
				$new_res[] = $value['type'];
			}
		}
	}
}
$new_res = array_count_values($new_res);
foreach ($new_res as $key => $value){
	$new_key[] = $key;
	$new_val[] = $value;
}

foreach ($new_key as $key => $value){
	if($value == '' || $value == null) $new_key[$key] = 'Other';
}

$bar = new bar_outline( 50, '#9933CC', '#8010A0' );

$max_y = max($new_val);
$bar->data = $new_val;
$g = new graph();
$g->title( $title, '{font-size: 16px; color: #404040;}' );
$g->data_sets[] = $bar;
$g->set_x_labels($new_key);
$g->set_x_label_style( 12, '#9933CC', 0, 1 );
$g->set_x_axis_steps( 2 );
//$g->x_axis_colour('#9933CC');
$g->set_y_max( $max_y );
$g->y_label_steps(5);
echo $g->render();
?>