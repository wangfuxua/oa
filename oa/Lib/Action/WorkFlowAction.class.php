<?php
/**
 +------------------------------------------------------------------------------
 * ThinkOA 工作流新版本
 +------------------------------------------------------------------------------
 * @category   ThinkOA
 * @package  ThinkOA
 * @subpackage  Core
 * @author tiger<liaoqiuhu@163.com>
 * @version  1.0
 * typeForm(表单类型，表单name名称，字段模型setting，表单值)
 +------------------------------------------------------------------------------
 */
class WorkFlowAction extends WorkFormAction {
	/**
	 +------------------------------------------------------------------------------
	 * 创建新工作（有templtate）
	 +------------------------------------------------------------------------------
	*/
	public function create(){
		#########弹出框开始#########  
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        	$this->assign("list_d",$list_d);
        	$this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束#########  
		//文号默认名称
		$zworkmodule = D("ZworkWorkModule");
		$moduleRow = $zworkmodule->find($_GET['moduleId']);
		$moduleName = $moduleRow['modelName'].'('.$this->LOGIN_USER_NAME.'--'.date("Y-m-d").')';
		$this->assign('moduleName',$moduleName);
		//工作名称
		$this->assign('modelName',$moduleRow['modelName']);
		//当前时间
		$this->assign('time',date("Y-m-d H:i"));
		//默认用户userId
		$this->assign('userId','U_'.$this->_uid);
		$this->assign('userName',$this->LOGIN_USER_NAME);
		//默认modelId
		$this->assign('modelId',$_GET['moduleId']);	
		//工作流程列表
		$moduleFlow = new ZworkModuleFlowModel();
		$moduleFlowList = $moduleFlow->where("moduleId = $_GET[moduleId]")->order('flowId')->findAll();
		$this->assign('moduleFlowList',$moduleFlowList); 	
		//form表单
		$templeate = $this->coder($_GET['moduleId'],1);
		$this->assign('templeate',$templeate);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 调用流程执行（有templtate）
	 +------------------------------------------------------------------------------
	*/
	public function Execute(){
		//获得工作模板
		$work = D('ZworkWork');
		$work->find($_GET['workId']);
		$templeate = $this->coder($_GET[moduleId],$work->state,$work->zworkId);
		$this->assign('templeate',$templeate);
		//工作ID
		$this->assign('workId',$work->zworkId);
		//执行table
		//模型
		$module = D ('ZworkWorkModule');
		$module->find($_GET[moduleId]);
		$TableName = 'zwork_'.$module->modelTableName;//获得执行的数据库
		$this->assign('TableName',$TableName);
		$this->assign('modelName',$module->modelName);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 *预览模型
	 +------------------------------------------------------------------------------
	*/
	public function view(){
		//获得工作ID
		$module = D('ZworkWorkModule');
		$row = $module->find($_GET['moduleId']);
		$this->assign('row',$row);
		//获得工作模板
		$templeate = $this->coder($_GET['moduleId']);
		$this->assign('templeate',$templeate);
		$this->display();
	}
	/**
		 +------------------------------------------------------------------------------
		 * 编译程序
		 +------------------------------------------------------------------------------
	*/	
	public function coder($moduleId,$flowId='All',$workId){
		//获得所有字段
		$workFiled = D('ZworkModuleFiled');
		$filed = $workFiled->where("modelId=$moduleId")->findall();
		//获得流程字段
		$moduleFlow = D('ZworkModuleFlow');
		if($flowId=='All'){
			foreach ($filed as $k=>$v){
				$filedArr[$k]=$v['filedId'];
			}
		}else{
		$moduleFlow->where("flowId=$flowId and moduleId=$moduleId")->find();
		$filedArr = explode(',',$moduleFlow->modulefiled);
		}
		//获得工作数据
		$module = D('ZworkWorkModule');
		$module->find($moduleId);
		$table = 'zwork_'.$module->modelTableName;
		$model = D($table);
		$value = $model->find("zworkId=$workId");
		//获得$_标签的数据
		foreach ($filed as $v){
			$filed = '_'.$v['filedId'];
			$i = 'kj_'.$v[filedId];
			if(in_array($v['filedId'],$filedArr)){
				$$filed = $this->typeForm("$v[formtype]","$i",$v['setting'],$value[$i]);
			}else{
				$$filed = $value[$i];
			}
		}
		//获得模板
		$templeate = $this->templeate($moduleId);
		//生成新模板
		$templeate = str_replace('"','\"',$templeate);
		eval("\$templeate=\"$templeate\";");
		return $templeate;
		//print_r($filedArr);
	}
	
	/**
	 +------------------------------------------------------------------------------
	 * 工作监控
	 +------------------------------------------------------------------------------
	*/
	public function flowView(){
		//获得work数据
		$work = new ZworkWorkModel();
		$workRow = $work->find($_GET['workId']);
		$this->assign('workRow',$workRow);
		$flow = new ZworkFlowModel();
		$flowList = $flow->where("zworkId=$_GET[workId]")->order('flowId')->findAll();
		$this->assign('flowList',$flowList);
		//获得workmodule数据
		$workmodule = new ZworkWorkModuleModel();
		$workmoduleRow = $workmodule->find($workRow['modelId']);
		$this->assign('workmoduleRow',$workmoduleRow);
		//获得字段数据
		$moduleFiled = new ZworkModuleFiledModel();
		$moduleFiledList = $moduleFiled->where("modelId=$workmoduleRow[modelId]")->order("`order` asc")->findAll(); 
		$this->assign('moduleFiledList',$moduleFiledList);
		//获得字段对应的值
		$model = D('zwork_'.$workmoduleRow['modelTableName']);
		$modelRow = $model->where("zworkId=$_GET[workId]")->find();
		foreach ($moduleFiledList as $k=>$v){
			$moduleFiledList[$k][filedValue]=$modelRow["kj_$v[filedId]"];
		}
		$this->assign('moduleFiledList',$moduleFiledList);
		$templeate = $this->coder($workRow['modelId'],'',$_GET['workId']);
		$this->assign('templeate',$templeate);
		$this->display();
	}
	/**
	 * 打印页面 
	 */
	public function printView(){
		//获得work数据
		$work = new ZworkWorkModel();
		$workRow = $work->find($_GET['workId']);
		$this->assign('workRow',$workRow);
		$flow = new ZworkFlowModel();
		$flowList = $flow->where("zworkId=$_GET[workId]")->order('flowId')->findAll();
		$this->assign('flowList',$flowList);
		//获得workmodule数据
		$workmodule = new ZworkWorkModuleModel();
		$workmoduleRow = $workmodule->find($workRow['modelId']);
		$this->assign('workmoduleRow',$workmoduleRow);
		//获得字段数据
		$moduleFiled = new ZworkModuleFiledModel();
		$moduleFiledList = $moduleFiled->where("modelId=$workmoduleRow[modelId]")->order("`order` asc")->findAll(); 
		$this->assign('moduleFiledList',$moduleFiledList);
		//获得字段对应的值
		$model = D('zwork_'.$workmoduleRow['modelTableName']);
		$modelRow = $model->where("zworkId=$_GET[workId]")->find();
		foreach ($moduleFiledList as $k=>$v){
			$moduleFiledList[$k][filedValue]=$modelRow["kj_$v[filedId]"];
		}
		$this->assign('moduleFiledList',$moduleFiledList);
		$templeate = $this->coder($workRow['modelId'],'',$_GET['workId']);
		$this->assign('templeate',$templeate);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 模板程序
	 * 调用模板数据
	 * 标签转换
	 +------------------------------------------------------------------------------
	*/
	protected function templeate($id){
		//获得模板
		$workModule = D('ZworkWorkModule');
		$workModule->find($id);
		$templeate = $workModule->modelTempleate;
		//标签转换
		$workFiled = D('ZworkModuleFiled');
		$filed = $workFiled->where("modelId=$id")->findall();
		foreach($filed as $k=>$v){
			$pattern[$k] = "/$v[name]\}/";
			$replacement[$k]="_".$v[filedId]."}";
		}
		return preg_replace($pattern, $replacement, $templeate);
	}
}
?>