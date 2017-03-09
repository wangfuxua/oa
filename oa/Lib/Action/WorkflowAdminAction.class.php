<?php
/**********************************
 * 工作流管理员设计及管理模块
 * author:廖秋虎  time:08/12/05
 **********************************/

class WorkflowAdminAction extends WorkflowAction {
	/*入口程序*/
	public function index(){
		$this->display();
	}
	/*头部模板程序*/
	public function menuTop(){
		$color = array('#D9E8FF','#DDDDDD','#B3D1FF');
		$this->assign('color',$color);
		$this->display();
	}
	/*表单页主程序*/
	public function flowForm(){
	//-------列出所有表单-------
		$flowFormType = new FlowFormTypeModel();
		$flowFormType = $flowFormType->order('FORM_ID')->findall();
		$count = 0;
		foreach($flowFormType as $k=>$v){
			//获得table样式
			$flowFormType["$k"]['tableline']= ($k%2==1) ?'TableLine2':'TableLine1';
			//判断获得一个删除的权限0或1
			$flow = new Model();
			$flow = $flow->query("SELECT * from FLOW_TYPE,FLOW_RUN where FLOW_TYPE.FORM_ID=$v[FORM_ID] and FLOW_TYPE.FLOW_ID=FLOW_RUN.FLOW_ID");
			$flowFormType["$k"]['tf'] =  $flow ? 0 : 1;		
		}
		$this->assign('flowFormType',$flowFormType);
		$this->display();
	}
	/*表单创建*/
	public function flowFormNew(){
		$flowFormType = new FlowFormTypeModel();
		if($flowFormType->create()){
			$flowFormType->add();
			$this->redirect('flowForm');		
		}else{
			$this->display();
		}
	}
	/*表单编辑*/
	public function flowFormEdit(){
		$flowFormType = new FlowFormTypeModel();
		if($flowFormType->create()){
			$flowFormType->find('FORM_ID='.$_POST['FORM_ID']);
			$flowFormType->FORM_NAME = $_POST['FORM_NAME'];
			$flowFormType->save();
			$this->redirect('flowForm');
		}elseif($_GET['FORM_ID']){
			$flowFormType = $flowFormType->find('FORM_ID='.$_GET['FORM_ID']);			
			$this->assign('flowFormType',$flowFormType);
			$this->display();
		}else{
			echo 'ewa';
		}
	}
	/*表单删除*/
	public function flowFormDel(){
		$flowFormType = new FlowFormTypeModel();
		$flowFormType ->delete('FORM_ID='.$_GET['FORM_ID']);
		$this->redirect('flowForm');
	}
	/*表单样式导出*/
	public function flowFormExport(){
		$flowFormType = new FlowFormTypeModel();
		$flowFormType = $flowFormType->find('FORM_ID='.$_GET['FORM_ID']);
		$letterStr = "\\,/,:,*,?,\",<,>,|";
		$myArray   = explode(",",$letterStr);
		$arrayCount= sizeof($myArray);
		if($myArray[$arrayCount-1]=="")
			$arrayCount--;
		for($i=0;$i<$arrayCount;$i++){
			$formName=str_replace($myArray[$i],"",$flowFormType['FORM_NAME']);
		}
		ob_end_clean();
		header("Cache-control: private");
 		header('content-type:text/html;charset=utf-8');
 		header("Accept-Ranges: bytes");
 		header("Accept-Length: ".strlen($flowFormType['PRINT_MODEL']));
 		header("Content-Disposition: attachment; filename=" . $formName.".txt");

 		echo $flowFormType['PRINT_MODEL']; 
	}
	/*表单样式导入*/
	public function flowFormImport(){
		$flowFormType = new FlowFormTypeModel();
		if($flowFormType->create()){
			$printModel = @fread($fp = fopen($_FILES['html_file']['tmp_name'],'r'),filesize($_FILES['html_file']['tmp_name']));
			@fclose($fp);
			$printModel = str_replace("'","\'",$printModel);
			$flowFormType->find('FORM_ID='.$_POST['FORM_ID']);
			$flowFormType->PRINT_MODEL = $printModel;
			$flowFormType->save();
			echo $printModel;
			//$this->redirect('flowForm');
		}elseif($_GET['FORM_ID']){
		$flowFormType = $flowFormType->find('FORM_ID='.$_GET['FORM_ID']);
		$this->assign('flowFormType',$flowFormType);
		$this->display();
		}else{
			exit;
		}
	}		
}
?>