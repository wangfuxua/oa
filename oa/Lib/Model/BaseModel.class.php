<?php
class BaseModel extends Model{
	
	/**
	 * 获取负责人信息
	 * @param $category string  负责人类型
	 * @param $mamager_id int  负责人id
	 * @return array || flase
	 * */
	public function getManagerInfo($category, $mamager_id){
		$res = array();
		switch ($mamager_id){
			case $category == 'User':
				$user =  D('User');
				$manager_res = $user->where("uid='".$mamager_id."'")->find();
				$res['id'] = $manager_res['uid'];
				$res['name'] = $manager_res['USER_NAME'];
				$res['manager_department'] = $manager_res['POST_DEPT'];
				$res['department'] = $manager_res['DEPT_ID'];
				$res['user_role'] = $manager_res['USER_PRIV'];
				$res['category'] = '用户';
				break;
			case $category == 'Department':
				$department = D('Department');
				$manager_res = $department->where("DEPT_ID='".$mamager_id."'")->find();
				$res['id'] = $manager_res['DEPT_ID'];
				$res['name'] = $manager_res['DEPT_NAME'];
				$res['category'] = '部门';
				break;
			case $category == 'UserPriv':
				$user_priv = D('UserPriv');
				$manager_res = $user_priv->where("USER_PRIV='".$mamager_id."'")->find();
				$res['id'] = $manager_res['USER_PRIV'];
				$res['name'] = $manager_res['PRIV_NAME'];
				$res['category'] = '角色';
				break;
			default:
				break;
		}
		return $res;
	}
	
	/**
	 * 获取共享的记录
	 * @param $table_name string 记录所属表
	 * @param $user_id int 当前用户id
	 * @return array || flase
	 * */
	public function getShareInfo($table_name, $user_id){
		$share = D('Share');
		$res = $share->where("table_name='".$table_name."' and user_id=$user_id")->findAll();
		return $res;
	}
	
	/**
	 * 获取共享的单条记录
	 * @param $table_name string 记录所属表
	 * @param $record_id int 当前记录id
	 * @param $user_id int 当前用户id
	 * @return array || flase
	 * */
	public function getShareOne($table_name, $record_id, $user_id){
		$share = D('Share');
		$res = $share->where("table_name='".$table_name."' and record_id =$record_id and user_id=$user_id")->find();
		return $res;
	}	
	
	/**
	 * 相关记录权限和负责人插入
	 * @param $table_name string 记录表
	 * @param $array_all array 查出的所有记录
	 * @param $array_share array 共享的记录
	 * @return array
	 * */
	public function addManager($table_name, $array_all, $array_share){
		$list_count = count($array_all);
		$share_count = count($array_share);
		$lat = D($table_name);
		for ($i = 0; $i < $list_count; $i++){
			if ($array_share) {
				for ($j = 0; $j < $share_count; $j++){
					if ($array_all[$i]['id'] == $array_share[$j]['record_id']) {
						$array_all[$i]['flag'] = $array_share[$j]['flag'];
						break;
					}elseif($array_all[$i]['id'] != $array_share[$j]['record_id'] && $j == $share_count-1){
							$array_all[$i]['flag'] = 5;
					}
				}
			}else{
				$array_all[$i]['flag'] = 5;
			}
			$user = D('User');
			//$manager = $lat->getManagerInfo($array_all[$i]['category_name'], $array_all[$i]['manager_id']);
			$manager = $user->where("uid='".$array_all[$i]['manager_id']."'")->find();
			$array_all[$i]['manager_name'] = $manager['USER_NAME'];	
		}
		return $array_all;
	}
	
	/**
	 * 创建form提交查询条件
	 * @param $arr array || string 查询条件数组
	 * @return string
	 * */
	public function buildingCondition($arr){
		$res = $add_res = null;
		if (is_string($arr)) {
			$res .= $arr.' and';
		}else{
			foreach ($arr as $key => $value){
				if($value && $key != 'a' && $key != 'm' && $key != 'p'){
					if ('date_from' == $key) {
						$res .= 'time_modify >"'.$value.' 00:00:00" and ';
					}else if ('date_to' == $key) {
						$res .= 'time_modify <"'.$value.' 23:59:59" and ';
					}else if ('manager_id' == $key) {
						$res .= $key.'='.$value.' and ';
					}else if ('province' == $key) {
						$add_res .= $key.' like "%'.$value.'%" and ';
					}else if ('city' == $key) {
						$add_res .= $key.' like "%'.$value.'%" and ';
					}else if ('street' == $key) {
						$add_res .= $key.' like "%'.$value.'%" and ';
					}else if ('postcode' == $key) {
						$add_res .= $key.' like "%'.$value.'%" and ';
					}else{
						$res .= $key.' like "%'.$value.'%" and ';
					}
				}
			}
		}
		$res = isset($add_res) ? $add_res.$res : $res;
		$condition = substr($res, 0, strlen($res)-4);
		return $condition;
	}
	
	/**
	 * 创建分页查询条件
	 * @param $arr array || string 查询条件数组
	 * @return string
	 * */
	public function buildingPageCondition($arr){
		$res_string = '';
		$join_1 = "=";
		$join_2 = "&";
		if($arr != '' && $arr != null){
			if(is_string($arr)) $res_string =  $arr;
			else if(is_array($arr)){
				foreach ($arr as $key => $value) {
					if($value && $key != 'a' && $key != 'm' && $key != 'p') $res_string .= $key.$join_1.urlencode($value).$join_2;
				}
				$res_string = substr($res_string, 0, -1);
			}
		}
		return $res_string;
	}	
	
	/**
	 * 文件上传
	 * @param $condition['maxSize']         string  文件上传最大值
	 * @param $condition['allowExts']       array   允许上传的文件后缀
	 * @param $condition['allowTypes']      array   允许上传文件的类型  
	 * @param $condition['savePath']        string  存储路径
	 * @param $condition['saveRule']        string  存储规则
	 * @param $condition['supportMulti']    boolen  是否支持多文件上传,默认支持
	 * @param $condition['thumb']           boolen  使用对上传图片进行缩略图处理，默认不处理
	 * @param $condition['thumbMaxWidth']   string  缩略图最大宽度
	 * @param $condition['thumbMaxHeight']  string  缩略图最大高度
	 * @param $condition['thumbSuffix']     string  缩略图后缀，默认_thumb
	 * @param $condition['zipImages']       boolen  压缩上传，默认否
	 * @param $condition['uploadReplace']   string  存在同名是否覆盖，默认不覆盖
	 * @param $condition['hashType']        string  上传文件Hash规则函数名，默认md5_file
	 * @return array || string;
	 * */
	public function file_upload($condition = array()){
		/**初始化上传类基本条件*/
		$maxSize = isset($condition['maxSize']) ? $condition['maxSize'] : '';
		$allowExts = isset($condition['allowExts']) ? $condition['allowExts'] : '';
		$allowTypes = isset($condition['allowTypes']) ? $condition['allowTypes'] : '';
		$savePath = isset($condition['savePath']) ? $condition['savePath'] : UPLOAD_PATH;
		$saveRule = isset($condition['saveRule']) ? $condition['saveRule'] : 'uniqid';
		
		// 示例化上传类
		$upload = new UploadFile($maxSize,$allowExts,$allowTypes,$savePath,$saveRule);
		
		/**初始化上传类其他条件条件*/
		if($condition['supportMulti']) $upload->supportMulti = $condition['supportMulti'];
		if($condition['thumb']) $upload->thumb = $condition['thumb'];
		if($condition['thumbMaxWidth']) $upload->thumbMaxWidth = $condition['thumbMaxWidth'];
		if($condition['thumbMaxHeight']) $upload->thumbMaxHeight = $condition['thumbMaxHeight'];
		if($condition['thumbSuffix']) $upload->thumbSuffix = $condition['thumbSuffix'];
		if($condition['zipImages']) $upload->zipImages = $condition['zipImages'];
		if($condition['uploadReplace']) $upload->uploadReplace = $condition['uploadReplace'];
		if($condition['hashType']) $upload->hashType = $condition['hashType'];
		
		$up = $upload->upload();
		if($up) return $upload->getUploadFileInfo();
		else return $upload->getErrorMsg();
	}
	
	/**
	 * 上传文件信息存储
	 * @return array || string
	 * */
	public function fileinfo_save($condition = array()){
		$res = $date = array();
		$up = $this->file_upload($condition);
		if(is_string($up)) return $up;
		elseif(is_array($up)){
			$date = $up[0];
			$date['addtime']=time();
			$date['filename']=$date['name'];
			$date['attachment']=$date['savename'];
			$date['filesize']=$date['size'];
			$date['filetype']=$date['type'];
			$dao = D('Attachments');
			$dao->create($date);
			$attid=$dao->add();
			if($attid){	
				$res['id']=$attid;
				$res['name']=$date['name'];
				$res['attachment']=$date['savename'];
			}
		}
		return $res;
	}
}
?>