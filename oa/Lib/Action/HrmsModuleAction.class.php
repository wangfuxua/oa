<?php
/**
 +------------------------------------------------------------------------------
 * ThinkOA 人事档案设置
 +------------------------------------------------------------------------------
 * @category   ThinkOA
 * @package  ThinkOA
 * @subpackage  Core
 * @author tiger<liaoqiuhu@163.com>
 * @version  1.0
 +------------------------------------------------------------------------------
 */
class HrmsModuleAction extends WorkFormAction{
	/**
	 +------------------------------------------------------------------------------
	 * 入口程序Index()
	 * 人事档案设置
	 +------------------------------------------------------------------------------
	*/
	public function index(){
		$hrms = D('HrmsField');
		$hrmsList = $hrms->order("state desc")->order("`order` desc")->findAll();
		$this->assign('hrmsList',$hrmsList);
		//$form = $this->typeForm($a['formtype'],$a['fieldname'],$a['setting']);
		//echo $form;
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 添加程序
	 +------------------------------------------------------------------------------
	*/
	public function add(){
		$this->judge($_POST['name'],'name');
		$this->judge($_POST['fieldname'],'fieldname');
		$hrms = D('HrmsField');
		$setting = serialize($_POST['setting']);
		$hrms->name = $_POST['name'];
		$hrms->fieldname = $_POST['fieldname'];
		$hrms->formtype = $_POST['formtype'];
		$hrms->order = $_POST['order'];
		$hrms->setting = $setting;
		$hrms->add();
		$this->tablefiled('hrms',$hrms->fieldname);
		$this->assign('jumpUrl',__URL__."/index"); 
		$this->success('添加字段'.$$hrms->name);
	}
	/**
	 +------------------------------------------------------------------------------
	 * 编辑字段程序
	 +------------------------------------------------------------------------------
	*/
	public function edit(){
		$hrms = D('HrmsField');
		$row = $hrms->find($_GET['fieldId']);
		$form ='oa/Tpl/default/zworkForm/form_'.$row['formtype'].'.html';
		$this->assign('row',$row);
		$this->assign('form',$form);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 更新数据
	 +------------------------------------------------------------------------------
	*/
	public function update(){
		$setting = serialize($_POST['setting']);
		$hrms = D('HrmsField');
		$hrms->save(array('name'=>$_POST['name'],'formtype'=>$_POST['formtype'],'order'=>$_POST['order'],'setting'=>$setting,'order'=>$_POST['order']),'fieldId='.$_POST['fieldId']);
		$this->assign('jumpUrl',__URL__."/index");
		$this->success('更新成功');
	}
	/**
	 +------------------------------------------------------------------------------
	 * 字段判断
	 +------------------------------------------------------------------------------
	*/
	public function judge($name,$field){
		$hrms = D('HrmsField');
		$value = $hrms->where("$field = '$name'")->find();
		if($value){
			$this->error($name."值已经存在,无法操作");
			exit;
		}else{
			return;
		}
	}
	/**
	 +------------------------------------------------------------------------------
	 * 入口程序Index()
	 * 待办工作列表
	 +------------------------------------------------------------------------------
	*/
	public function del(){
		$hrms = D('HrmsField');
		$row = $hrms->find($_GET['fieldId']); 
		$hrms->delete("fieldId=$_GET[fieldId]");
		$this->filedDel('hrms',$row['fieldname']);
		$this->assign('jumpUrl',__URL__."/index");
		$this->success('删除成功');
	}

	/**
	 +------------------------------------------------------------------------------
	 * 创建模型字段
	 +------------------------------------------------------------------------------
	*/
	protected function tablefiled($table,$filed){	
		$Model = new Model();
		$Model->query("ALTER TABLE `$table` ADD `$filed` VARCHAR( 50 ) NULL;");
		return;
	}
	
	/**
	 +------------------------------------------------------------------------------
	 * 删除字段
	 +------------------------------------------------------------------------------
	*/
	protected function filedDel($table,$filed){
		$Model = new Model();
		$Model->query("ALTER TABLE `$table` DROP `$filed`");
		return;
	}
	/**
	 +------------------------------------------------------------------------------
	 * 调用formType模板
	 +------------------------------------------------------------------------------
	*/
	public function select(){
		$this->display('ZworkForm:form_'.$_POST['formtype']);
	}
	/**
	 +------------------------------------------------------------------------------
	 * 更新序列号
	 +------------------------------------------------------------------------------
	*/
	public function fileOrder(){
		$filed = D('HrmsField');
		if($_POST['listorder']){
			foreach($_POST['listorder'] as $k=>$v){	
			$filed->save(array('order'=>$v),'fieldId='.$k);
			}
			$this->success('更新成功');
		}else{
			$this->error("更新失败");
		}
		
	}
}
?>