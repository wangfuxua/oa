<?php
/**
 +------------------------------------------------------------------------------
 * ThinkOA 工作流程
 +------------------------------------------------------------------------------
 * @category   ThinkOA
 * @package  ThinkOA
 * @subpackage  Core
 * @author tiger<liaoqiuhu@163.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
class ZworkFlowAction extends WorkFormAction {
	/**
	 +------------------------------------------------------------------------------
	 * 待办工作列表
	 +------------------------------------------------------------------------------
	 */
	public function index(){
		//获得当前用户控制流程列表
		$flow = new ZworkFlowModel();
		$flowList = $flow->userList($this->LOGIN_USER_ID);	
		//代办工作列表
		$work = new ZworkWorkModel();
		$workRow = $work->workNow($flowList);
		$workRows = $this->sortData($workRow,"grade",0);
		//分页
		$count = count($workRows);
		import("ORG.Util.Page");
		$p = new Page($count,10,10);
		$page = $p->show();
		$this->assign("page",$page);
		$workList = array_slice($workRows,$p->firstRow,$p->listRows);
		$this->assign('workRow',$workList);		
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 新建工作首页
	 +------------------------------------------------------------------------------
	 */
	public function news(){
		$treeOnline = $this->treeOnline();
		$this->assign('treeOnline',$treeOnline);
		//判断URL
		if($_GET['modelId']){
			$URL = 'createNew/modelId/'.$_GET['modelId'];
		}else{
			$URL = 'newsMain';
		}
		$this->assign('URL',$URL);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 *新建工作Main页面
	 +------------------------------------------------------------------------------
	 */
	public function newsMain(){
		$zworkmodule = new ZworkWorkModuleModel();
		$zworkList = $zworkmodule->findall();
		$this->assign('zworkList',$zworkList);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 创建新工作
	 +------------------------------------------------------------------------------
	*/
	public function createNew(){
		$this->judge($_GET['modelId'],__APP__."/WorkFlow/create/moduleId/$_GET[modelId]");
		
		#########弹出框开始#########  
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        	$this->assign("list_d",$list_d);
        	$this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束######### 
		
		//判断值是否存在
		$decide = $this->tableValue('modelId',$_GET['modelId'],'ZworkWorkModule');
		if(!$decide){
			$this->error();
		}
		//文号默认名称
		$zworkmodule = D("ZworkWorkModule");
		$moduleRow = $zworkmodule->find($_GET['modelId']);
		$moduleName = $moduleRow['modelName'].'('.$this->LOGIN_USER_NAME.'--'.date("Y-m-d").')';
		$this->assign('moduleName',$moduleName);
		//工作名称
		$this->assign('modelName',$moduleRow['modelName']);
		//当前时间
		$this->assign('time',date("Y-m-d H:i"));
		//默认用户userId
		$userId = 'U_'.$this->_uid;
		$this->assign('userId',$userId);
		$this->assign('userName',$this->LOGIN_USER_NAME);
		//默认modelId
		$this->assign('modelId',$_GET['modelId']);	
		//工作流程列表
		$moduleFlow = D('ZworkModuleFlow');
		$moduleFlowList = $moduleFlow->where("moduleId = $_GET[modelId]")->order('flowId')->findAll();
		$this->assign('moduleFlowList',$moduleFlowList); 
		//工作记录列表
		$moduleFiledList=$this->flowFiled(1,$_GET['modelId']);
		$this->assign('moduleFiledList',$moduleFiledList);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * ThinkOA 办理程序
	 * 设定流程时间workStart()
	 * 判断模型judge
	 +------------------------------------------------------------------------------
	 */
	public function execute(){
		if($_GET['workId']==''){
			$this->error();
		}
		//设定工作流程开始时间
		$this->workStart($_GET['workId']);
		
		$work = D('ZworkWork');
		$workRow = $work->find($_GET['workId']);
		$this->assign('workRow',$workRow);
		//判断模型
		$this->judge($workRow['modelId'],__APP__."/WorkFlow/execute/workId/$_GET[workId]/moduleId/$workRow[modelId]");
		//模型
		$module = new ZworkWorkModuleModel();
		$moduleRow = $module->find($workRow['modelId']);
		$this->assign('modelName',$moduleRow['modelName']);
		$TableName = 'zwork_'.$moduleRow['modelTableName'];//获得执行的数据库
		$this->assign('TableName',$TableName);
		//页面显示
		$flow = new ZworkFlowModel();
		$flowRow = $flow->where("zworkId=$_GET[workId] and flowId=$workRow[state]")->find();
		$a = explode(",",$flowRow['powerItem']);
		$model = new ZworkModuleFiledModel();
		$modelList = $model->where("modelId=$workRow[modelId]")->findall();
		//调用$TableName数据表数据
		$tableModel = D($TableName);
		$tableList = $tableModel->where("zworkId=$_GET[workId]")->find();
		foreach($modelList as $k=>$v){
			if(in_array($v['filedId'],$a)){	
				$filed = 'kj_'.$v['filedId'];
				$i = 'kj_'.$v['filedId'];
				$modelList[$k]['form'] = $this->typeForm($v['formtype'],$filed,$v['setting'],$tableList[$i]);
			}else{
				$i = 'kj_'.$v['filedId'];
				$value = $tableList[$i];
				$modelList[$k]['form'] = "$value";
			}
		}
		$this->assign('modelList',$modelList);
		$this->display();
	}
	/*************转交更新*********************/
	public function deliver(){
		#########弹出框开始#########  
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
        	$this->assign("list_d",$list_d);
        	$this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束######### 
		$work = new ZworkWorkModel();
		$workRow = $work->where("zworkId=$_GET[zworkId]")->find();
		$flow = new ZworkFlowModel();
		$flowRow = $flow->where("zworkId=$_GET[zworkId] and flowId=$workRow[state]")->find();
		$this->assign('flowRow',$flowRow);
		$this->display();
	}
	/************中止*************/
	public function stop(){
		$this->assign('zworkId',$_GET['zworkId']);
		$work = new ZworkWorkModel();
		$workRow = $work->where("zworkId=$_GET[zworkId]")->find();
		$this->assign('flowId',$workRow['state']);
		$this->display();		
	}
	public function stopSave(){
		$work = new ZworkWorkModel();
		$remark = $this->LOGIN_USER_NAME.':<br>&nbsp;&nbsp;&nbsp;&nbsp;'.$_POST['remark'];
		$work->save(array('state'=>1002,'remark'=>$remark),"zworkId=$_POST[zworkId]");
		$flow = new ZworkFlowModel();
		$flow->save(array('tag'=>$remark,'state'=>4),"flowId=$_POST[flowId] and zworkId=$_POST[zworkId]");
		$this->assign('jumpUrl',__URL__."/index"); 
		$this->success('转交成功!');  
	}
	//=========定义开始工作时间================
	protected function workStart($id){
		$work = new ZworkWorkModel();
		$workRow = $work->where("zworkId=$id")->find();
		$flow = new ZworkFlowModel();
		$flowRow = $flow->where("zworkId=$id and flowId=$workRow[state]")->find();
		if(empty($flowRow['startTime'])){
			$flow->save(array('startTime'=>time(),'state'=>2),"zworkId=$id and flowId=$workRow[state]");
			return ;
		}else{
			return;
		}
	}

	//=========工作添加记录程序==============
	public function add(){
		//判断表是否存在
		$ZWorkWork = new ZworkWorkModel();
		if(false === $ZWorkWork->create()) {
        	$this->error($ZWorkWork->getError());
        }
        $_POST['UserId']=$this->LOGIN_USER_ID;
        $_POST['zworkTime']=time();
        $ZWorkWork->create();
        $ZWorkWork->add();
        $this->assign('jumpUrl',__URL__."/index"); 
		$this->success('建立工作成功!');       	
	}
	//=================表单验证==============================
	public function formCheck($data,$message){
		if(empty($data)){
			$this->error($message);
		}elseif(is_array($data)){
			foreach ($data as $v){
				if(empty($v)){
					$this->error($message);
				}	
			}
		}else{
			return ;
		}
	}
	//=====================状态=============================
	public function flowstate($i){
		$data=array('未开始','正在进行','已结束','中止');
		return $data[$i];
	}
	//======================对zworkwork表做添加记录==================
	public function workAdd($data){
		$work =  new ZworkWorkModel();
		$id = $work->add($data);		
		return $id;
	}
	//=====================对流程zworkflow表做添加记录=============
	public function flowAdds($modelId,$userId,$zworkId){
		$flow = new ZworkFlowModel();
		$moduleflow = new ZworkModuleFlowModel();
		$moduleflowList = $moduleflow->where("moduleId=$modelId")->findAll();
		foreach($moduleflowList as $v){
			$user = substr($userId["$v[flowId]"],0,strlen($userId["$v[flowId]"])-1);
			$data = array('powerItem'=>$v['modulefiled'],'flowName'=>$v['flowName'],'powerUser'=>$user,'zworkId'=>$zworkId,'flowId'=>$v['flowId']);
			$flow->add($data);
		}
		return;
	}
	//===============流程创建程序==================
	public function flowIndex(){
		$work = new ZworkWorkModel();
		$workRow = $work->find($_GET['workId']);
		$this->assign('workRow',$workRow);
		$this->display();
	}
	//===============流程程序======================
	public function select(){
		for($i=1;$i<=$_POST['flowNum'];$i++){
			$v[$i]=$i;
		}
		$this->assign('v',$v);
		$this->display();
	}
	//==============流程添加==================
	public function flowAdd(){		
		$flow = new ZworkFlowModel();		
		foreach($_POST['filed'] as $k=>$v){
			$data = array('flowId'=>$k+1,'zworkId'=>$_POST['workId'],'powerItem'=>$v,'powerUser'=>$_POST['userId'][$k]);
			$flow->add($data);
		}
		$work = new ZworkWorkModel();
		$work->save(array('state'=>1),"zworkId=$_POST[workId]");	
		$this->assign('jumpUrl',__URL__."/index"); 
		$this->success('操作成功!');  
	}
	//============查看流程图================
	public function flowView(){
		//获得work数据
		$work = new ZworkWorkModel();
		$workRow = $work->find($_GET['workId']);
		$this->assign('workRow',$workRow);
		
		/*判断*/
		$this->judge($workRow['modelId'],__APP__."/WorkFlow/flowView/workId/$_GET[workId]");
		
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
		
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 打印程序
	 +------------------------------------------------------------------------------
	 */
	public function printView(){
		$flow = new ZworkFlowModel();
		$flowList = $flow->where("zworkId=$_GET[workId]")->order('flowId')->findAll();
		$this->assign('flowList',$flowList);
		//获得work数据
		$work = new ZworkWorkModel();
		$workRow = $work->find($_GET['workId']);
		$this->assign('workRow',$workRow);
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
		$this->display();
	}
	
	
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
	//===========测试=================
	public function test(){
		$max = count($_POST['userId']);
		for($i=1;$i<=$max;$i++){
			if(empty($_POST['userId'][$i])){
				$this->error('每个流程必须定义一个用户');
			}
		}		
		//写入数据库
		$work = new ZworkWorkModel();
		$workData = array(
			'modelId'=>$_POST['modelId'],
			'zworkName'=>$_POST['zworkName'],
			'UserId'=>$this->LOGIN_USER_ID,
			'grade'=>$_POST['grade'],
			'zworkTime'=>time(),
			'state'=>1
		);
		$id=$work->add($workData);
		$flow = new ZworkFlowModel();
		for ($i=1;$i<=$max;$i++){
			$userId = substr($_POST['userId'][$i],0,strlen($_POST['userId'][$i])-1);
			$filed =$_POST['modulefiled'][$i];
			$data=array(
				'powerItem'=>$filed,
				'powerUser'=>$userId,
				'zworkId'=>$id,
				'flowId'=>$i,
				'UserId'=>$_POST['userId'][$i]
				);
			$flow->add($data);
		}
		$this->assign('jumpUrl',__URL__."/index");
		$this->success("添加成功");
		
	}
	//判断值是否存在
	protected function tableValue($key,$value,$table){
		$d = D($table);
		$date = $d->where("$key=$value")->find();
		$a = (empty($date))?false:true;
		return $a;
	}
	/**
	 +------------------------------------------------------------------------------
	 * 工作监控
	 +------------------------------------------------------------------------------
	*/
	public function workControl(){
		
		//我创建的工作
		$zworkwork = new ZworkWorkModel();
		$zworkList = $zworkwork->where("UserId='$this->LOGIN_USER_ID'")->order('zworkTime desc')->findAll();		
		$this->assign('zworkList',$zworkList);
		$count = count($zworkList);
		import("ORG.Util.Page");
		$p = new Page($count,10,10);
		$page = $p->show();
		$this->assign("page",$page);
		$workList = array_slice($zworkList,$p->firstRow,10);
		$this->assign('zworkList',$workList);	
		//我被指派做的工作
		$workflow = new ZworkFlowModel();
		$workflowList = $workflow->where("powerUser='$this->LOGIN_USER_ID' or powerUser like '%$this->LOGIN_USER_ID%'")->order('startTime desc')->findAll();
		foreach ($workflowList as $k=>$v){
			$workRow=$zworkwork->find("zworkId=$v[zworkId]");
			$workflowList[$k][zworkName]=$workRow[zworkName];
		}
		$count2=count($workflowList);
		$p2 = new Page($count2,10,10);
		$page2 = $p2->show();
		$this->assign("page2",$page2);
		$workflowList = array_slice($workflowList,$p2->firstRow,10);
		$this->assign('workflowList',$workflowList);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 获得流程字段
	 +------------------------------------------------------------------------------
	*/
	protected function flowFiled($id,$moduleId){
		$moduleFlow = new ZworkModuleFlowModel();
		$moduleFlowRow = $moduleFlow->where("flowId=$id and moduleId=$moduleId")->find();
		$fileds = explode(",",$moduleFlowRow['modulefiled']);
		$moduleFiled = new ZworkModuleFiledModel();
		$moduleFiledList = $moduleFiled->where("modelId=$moduleId")->order('`order`')->findall();
		foreach($moduleFiledList as $k=>$v){
			if(in_array($v['filedId'],$fileds)){	
				$filed = 'kj_'.$v['filedId'];
				$i = 'kj_'.$v['filedId'];
				$moduleFiledList[$k]['form'] = $this->typeForm($v['formtype'],$filed,$v['setting'],$value);
			}else{
				$i = 'kj_'.$v['filedId'];
				$value = $tableList[$i];
				$moduleFiledList[$k]['form'] = "$value";
			}
		}
		return $moduleFiledList;
	}
	/**
	 +------------------------------------------------------------------------------
	 * 二维数组排序
	 * 对二维数组排序的另一种方法
	 * @param array $data
	 * @param string $sortByKey
	 * @param int $order
	 * @return array
	 +------------------------------------------------------------------------------
	*/
	public function sortData($data, $sortByKey, $order = 1){
	    if (!is_array($data)) {
	        return false;
	    }
	    $sortKey = array();
	    foreach ($data as $key => $value) {
	        $sortKey[$key] = $value[$sortByKey];
	    }
	    if ($order === 1) {
	        array_multisort($sortKey, SORT_ASC, $data);
	    } else {
	        array_multisort($sortKey, SORT_DESC, $data);
	    }
	    return $data;
	}
	/**
	 +------------------------------------------------------------------------------
	 * 跳转程序
	 +------------------------------------------------------------------------------
	*/
	public function judge($id,$url){
		$module = D('ZworkWorkModule');
		$moduleRow = $module->find($id);
		if($moduleRow['modelTempleate']){
			redirect($url);
			exit;
		}else{
			return;
		}
	}
	/**
	 +----------------------------------------------------------------------------------
	 * 获得树形菜单数据
	 +----------------------------------------------------------------------------------
	 */
	protected function treeOnline(){
		$workmodule = new ZworkWorkModuleModel();
		$workList = $workmodule->findall();
		$treeOnline = "online.add(0,-1,'','','','','','','','tree-root');";	
		$zworkmodule = new ZworkModuleAction();
		$module = $zworkmodule->category();
		foreach ($module as $k=>$v){
			$treeOnline .="online.add($k,0,'$v','','$v','','','','','oTree-bg');";
		}
		foreach ($workList as $k=>$v){
			$treeOnline .="online.add('$v[modelName]',$v[category],'$v[modelName]','/index.php/ZworkFlow/createNew/modelId/$v[modelId]','','news_main','','','','');";
		}
		return $treeOnline;
	}
}
?> 