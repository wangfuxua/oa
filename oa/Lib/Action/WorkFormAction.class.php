<?php
/**
 +------------------------------------------------------------------------------
 * ThinkOA 工作流表单模型
 +------------------------------------------------------------------------------
 * @category   ThinkOA
 * @package  ThinkOA
 * @subpackage  Core
 * @author tiger<liaoqiuhu@163.com>
 * @version  1.0
 +------------------------------------------------------------------------------
 */
class WorkFormAction extends PublicAction {
	public function index(){
		
	}
	public function create(){
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 输出表格内容
	 * typeForm(表单类型，表单name名称，字段模型setting，表单值)
	 +------------------------------------------------------------------------------
	*/
	public function typeForm($form,$filed,$setting,$value){
		$setting = unserialize($setting);
		$value2 = $value=(empty($value))?$setting['defaultvalue']:$value;				
		eval("\$str = \"$value2\";");
		switch ($form){
			case 'text':
				return "<input type='text' id='formdata' name='$filed' value='$str' size='$setting[size]'>";
				break;
			case 'textarea':
				return "<textarea name='$filed' id='formdata' cols='$setting[cols]' rows='$setting[rows]'>$value2</textarea>";
				break;
			case 'box':
				if($setting['boxtype']=='select'){
					$options = $this->option($setting['options']);
					foreach ($options as $k=>$v){
						if($k==$value){
							$option .="<option value='$k' selected>$v</option>";
						}else{
							$option .="<option value='$k'>$v</option>";
						}
					}				
					return "<select name='$filed' >$option</select>";
				}
				break;
			case 'datetime':
				if($setting[defaulttype]==1){
					$date = date("Y-m-d");
				}elseif($setting[defaulttype]==2){
					$date = $setting[defaultvalue];
				}else{
					$date=='';
				}
				return "<input type='text' name='$filed' value='$date' id='formdata' onfocus=\"WdatePicker({dateFmt:'$setting[format]'})\" >";
				break;
			case 'file':
				return "<input type='file' id='formdata' name='$filed' value='' class='dm_liulan' size='$setting[size]'>";
				break;
		}
	}
	/**
	 +------------------------------------------------------------------------------
	 * 下拉菜单选项
	 +------------------------------------------------------------------------------
	*/
	protected function option($options, $s1 = "\n", $s2 = '|'){
		$options = explode($s1, $options);
		foreach($options as $option)
		{
			if(strpos($option, $s2))
			{
				list($name, $value) = explode($s2, trim($option));
			}
			else
			{
				$name = $value = trim($option);
			}
			$os[$value] = $name;
		}
		return $os;
	}
}
?>