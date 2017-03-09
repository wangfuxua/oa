<?php
class ZWorkWorkModel extends Model{
	var $tableName="zwork_work";
	var $_validate=array(
	       // array("modelName","require","中文名称不能为空"),
	);
	
	/**
	  +-------------------------------------------------------------------------
	  *执行工作数组
	  +-------------------------------------------------------------------------
	*/
	public function workNow($array){
		foreach ($array as $k=>$v){
			$a=$this->where("zworkId=$v[zworkId] AND state=$v[flowId]")->find();
			if(!empty($a)){
				$workRow[$k]=$a;
				$workRow[$k][tag]=$v['tag'];
			}
		}
		return $workRow;
	}
}
?>