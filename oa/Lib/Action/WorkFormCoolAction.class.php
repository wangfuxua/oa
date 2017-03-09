<?php
/**********************************
 *      表单智能设计器
 * author:廖秋虎  time:08/12/05
 **********************************/
class WorkFormCoolAction extends WorkflowAdminAction {
	public function index(){
		$flowFormType = new FlowFormTypeModel();
		$flowFormType = $flowFormType->where('Form_ID='.$_GET['FORM_ID'])->find();
		$PrintModel = str_replace("\"","'",$flowFormType['PRINT_MODEL']);
		$PrintModel = str_replace(chr(10),"",$PrintModel);
		$PrintModel = str_replace(chr(13),"",$PrintModel);
		$this->assign('PrintModel',$PrintModel);
		$this->assign('FormId',$_GET['FORM_ID']);
		$this->display();
	}
	//保存表单
	public function submit(){
		$flowFormType = new FlowFormTypeModel();
		$flowFormType->where('FORM_ID='.$_POST['FORM_ID'])->find();
		$flowFormType->PRINT_MODEL = $_POST['CONTENT'];
		$flowFormType->save();
		
	}
	//宏控件
	public function coolAuto(){
		$this->display();
	}
	//计算控件
	public function coolCalc(){
		$this->display();
	}
	//单选框控件
	public function coolCheckbox(){
		$this->display();
	}
	//下拉菜单控件
	public function coolListmenu(){
		$ITEM_MAX=50;
		$this->assign('ItemMax',$ITEM_MAX);
		$this->display();
	}
	//多行输入框
	public function coolTextarea(){
		$this->display();
	}
	//单行输入框
	public function coolTextfield(){
		$this->display();	
	}
	//日期控件
	public function coolCalendar(){
		$this->display();
	}
	//表单字段设置
	public function ItemIndex(){
		$flowFormType = new FlowFormTypeModel();
		$flowFormType = $flowFormType->where('FORM_ID='.$_GET['FORM_ID'])->find();
		$this->assign('flowFormType',$flowFormType);
		$this->display();
	}
}
?>