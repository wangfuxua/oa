<?php
/**
 +------------------------------------------------------------------------------
 * ThinkOA 工作流管理模块
 * 支持模板、控件创建修改
 +------------------------------------------------------------------------------
 * @category   ThinkOA
 * @package  ThinkOA
 * @subpackage  Core
 * @author tiger<liaoqiuhu@163.com>
 * @version  1.0
 +------------------------------------------------------------------------------
 */
class ZworkModuleAction extends PublicAction {
	/**
	 +------------------------------------------------------------------------------
	 * 入口程序Index()
	 * 添加模型
	 * 模型列表
	 +------------------------------------------------------------------------------
	 */
	public function index(){
		//模型类别
		$category = $this->category();
		$this->assign('category',$category);
		//模型列表
		$workModule = D('ZworkWorkModule');
		$count = $workModule->count('modelId');
		import("ORG.Util.Page");
		$p = new Page($count,20,10);
		$page = $p->show();
		$this->assign("page",$page);
		$workModuleList = $workModule->limit("$p->firstRow,$p->listRows")->findall();
		$this->assign('workModuleList',$workModuleList);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 更新缓存程序
	 +------------------------------------------------------------------------------
	*/
	public function cache(){
		$dir = $_SERVER["DOCUMENT_ROOT"]."/oa/Data/";
		$dh=opendir($dir);
		while ($file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					deldir($fullpath);
				}
			}
		}
		closedir($dh);
		$this->success("更新缓存成功");
	}
	/**
	 +------------------------------------------------------------------------------
	 * 添加模型数据程序Index()
	 * 待办工作列表
	 +------------------------------------------------------------------------------
	*/
	public function add(){	
        $workModule = D('ZworkWorkModule');  
        //创建数据表
		$table = $this->table($_POST['modelTableName']);
		//判断数据表是否存在
		if(empty($table)){
			$this->error('数据库出错,无法插入,请确认数据名已存在');
		}else{
		//写入Module数据表
			$workModule->create();
			$workModule->add();
		//跳转
			$id = $workModule->max('modelId');
			$this->redirect('filedIndex/modelId/'.$id);
		}
	}
	/**
	 +------------------------------------------------------------------------------
	 * 编辑数据模型程序
	 * 调用编辑数据内容
	 +------------------------------------------------------------------------------
	*/
	public function edit(){
		$workModule = D('ZworkWorkModule');
		$row = $workModule->find($_GET['modelId']);
		$this->assign('row',$row);
		$category = $this->category();
		$this->assign('category',$category);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 更新数据模型程序
	 * 更新数据
	 +------------------------------------------------------------------------------
	*/
	public function update(){
		$workModule = D('ZworkWorkModule');
		if($_POST['modelName']){
			$workModule->save(array('modelName'=>$_POST['modelName'],'category'=>$_POST['category']),"modelId=$_POST[modelId]");
			$this->assign('jumpUrl',__URL__."/index");
			$this->success('更新成功');
		}
	} 
	/**************删除数据模型程序*******************/
	public function del(){
		$workModule = new ZworkWorkModuleModel();		
		if($_GET['modelId']){
			$this->tableDel($_GET['modelId']);
			$workModule->where("modelId=$_GET[modelId]")->delete();
			$this->assign('jumpUrl',__URL__."/index"); 
			$this->success('删除成功!');       	
		}else 
			exit();
	}
//======================================字段管理============================================================================
	/****************控件管理入口程序*************/	
	public function filedIndex(){
		$workModule = D('ZworkWorkModule');
		$row = $workModule->find($_GET['modelId']);
		$this->assign('row',$row);
		
		$this->assign('modelId',$_GET['modelId']);
		$filed = D('ZworkModuleFiled');
		$filedList = $filed->where("modelId=$_GET[modelId]")->order("`order` asc")->findAll();
		$this->assign('filedList',$filedList);
		$this->display();
	}
	/************添加控件数据程序*****************/
	public function filedAdd(){
		$_POST['setting'] = serialize($_POST['setting']);
		$modelFiled = new ZworkModuleFiledModel();
		$modelFiled->create();
		$name = $modelFiled->add();
		$workModule = new ZworkWorkModuleModel();
		$Module = $workModule->find($_POST['modelId']);
		$this->tablefiled($Module['modelTableName'],$name);
		$this->assign('jumpUrl',__URL__."/filedIndex/modelId/$_POST[modelId]"); 
		$this->success('添加字段'.$_POST['filed']);
	}
	//============修改控件数据程序=====================
	public function filedEdit(){
		$moduleFiled = new ZworkModuleFiledModel();
		$row = $moduleFiled->find($_GET['filedId']);
		$setting = unserialize($row['setting']);
		$this->assign('setting',$setting);	
		$form ='oa/Tpl/default/ZworkForm/form_'.$row['formtype'].'.html';
		$this->assign('row',$row);
		$this->assign('form',$form);
		$this->display();
	}
	//=============更新控件数据程序====================
	public function filedUpdate(){
		$moduleFiled = new ZworkModuleFiledModel();
		if(!empty($_POST['name'])||!empty($_POST['formtype'])){
			$moduleFiled->save(array('name'=>$_POST['name'],'formtype'=>$_POST['formtype'],'setting'=>serialize($_POST['setting'])),'filedId='.$_POST['filedId']);
			$this->assign('jumpUrl',__URL__."/filedIndex/modelId/".$_POST['modelId']);
			$this->success('更新成功');
		}elseif($_POST['name']){
			$moduleFiled->save(array('name'=>$_POST['name']),'filedId='.$_POST['filedId']);
			$this->assign('jumpUrl',__URL__."/filedIndex/modelId/".$_POST['modelId']);
			$this->success('更新成功');
		}else{
			$this->error("更新失败");
		}
	}
	//=========删除字段数据程序=======================
	public function fileDel(){
		$modelFiled = new ZworkModuleFiledModel();
		$modelFiledRow = $modelFiled->find($_GET['filedId']);
		$workModel = new ZworkWorkModuleModel();
		$workModelRow = $workModel->find($modelFiledRow['modelId']);
		$this->filedDel($workModelRow['modelTableName'],$modelFiledRow['filed']);
		$modelFiled->delete();
		$this->success('删除成功');
	}
	//===============更新序列号======================
	public function fileOrder(){ 
		$filed = new ZworkModuleFiledModel();
		if($_POST['listorder']){
			foreach($_POST['listorder'] as $k=>$v){	
			$filed->save(array('order'=>$v),'filedId='.$k);
			}
			$this->success('更新成功');
		}else{
			$this->error("更新失败");
		}
		
	}
//=================================流程============================================================
	
	public function flow(){
		$workModule = D('ZworkWorkModule');
		$row = $workModule->find($_GET['modelId']);
		$this->assign('row',$row);
		//获得字段列表控件
		$treefiled = $this->filedSelect($_GET[modelId]);
		$this->assign('treefiled',$treefiled);
	//流程列表	
		$moduleFlow = new ZworkModuleFlowModel();
		$moduleFlowList = $moduleFlow->where("moduleId=$_GET[modelId]")->order('flowId')->findall();
		$filed = new ZworkModuleFiledModel();
		foreach ($moduleFlowList as $k=>$v){
			$fileds = $filed->where("filedId In ($v[modulefiled])")->findAll();	
			$moduleFlowList[$k][filedName] = "";
			foreach ($fileds as $k2=>$v2)	
			$moduleFlowList[$k][filedName] .=$v2['name'].',';
		}
		$this->assign('moduleFlowList',$moduleFlowList);
		$this->assign('modelId',$_GET['modelId']);
		$this->display();
	}
	//========增加流程=========
	public function flowAdd(){
		$moduleFlow = new ZworkModuleFlowModel();
		$moduleList = $moduleFlow->where("moduleId=$_POST[modelId]")->findall();
		$modulefiledId = substr($_POST['TO_ID'],0,strlen($_POST['TO_ID'])-1);
		if($moduleList){ 
			$data = array('flowId'=>count($moduleList)+1,'moduleId'=>$_POST['modelId'],'modulefiled'=>$modulefiledId,'flowName'=>$_POST['flowName']);
			$moduleFlow->add($data);
		}else{
			$data = array('flowId'=>1,'moduleId'=>$_POST['modelId'],'modulefiled'=>$modulefiledId,'flowName'=>$_POST['flowName']);
			$moduleFlow->add($data);
		}
		$this->success("添加流程数据成功");
	}
	//==========删除流程=============
	public function moduleFlowDel(){
		$moduleFlow = new ZworkModuleFlowModel();
		//流程更新操作
		$moduleFlowList = $moduleFlow->where("moduleId=$_GET[moduleId] and flowId > $_GET[flowId]")->findAll();
		foreach($moduleFlowList as $k=>$v){
			$moduleFlow->save(array('flowId'=>$v['flowId']-1),"moduleId=$v[moduleId] and flowId = $v[flowId]");	
		}
		//流程删除操作
		$moduleFlow->where("moduleId=$_GET[moduleId] and flowId=$_GET[flowId]")->find();
		$moduleFlow->delete();
		$this->success("成功删除流程");
	}
	/**
	 +------------------------------------------------------------------------------
	 * 流程编辑程序
	 * 获得流程、字段默认值
	 +------------------------------------------------------------------------------
	*/
	public function flowEdit(){
		$this->assign('flowId',$_GET['flowId']);
		$this->assign('moduleId',$_GET['moduleId']);
		$flowModule = D('ZworkModuleFlow');
		$flowRow = $flowModule->where("flowId=$_GET[flowId] and moduleId=$_GET[moduleId]")->find();
		$this->assign('flowRow',$flowRow);
		//获得字段filedId值
		$filedId = $flowRow['modulefiled'].',';
		$this->assign('filedId',$filedId);
		//获得字段filedName名称
		$fileds = explode(',',$flowRow['modulefiled']);
		$filed = "";
		foreach ($fileds as $v){
			$moduleFiled = D('ZworkModuleFiled');
			$moduleFiledRow = $moduleFiled->find($v);
			$filed .= "$moduleFiledRow[name],";
		}
		$this->assign('filed',$filed);
		//获得字段列表控件
		$treefiled = $this->filedSelect($_GET[moduleId]);
		$this->assign('treefiled',$treefiled);
		$this->display();
	}
	/**
	 +------------------------------------------------------------------------------
	 * 更新流程模型数据
	 +------------------------------------------------------------------------------
	*/
	public function flowSave(){
		$moduleFiled = substr($_POST['TO_ID'],0,strlen($_POST['TO_ID'])-1);
		$flowName = $_POST['flowName'];
		$flow = D('ZworkModuleFlow');
		$flow->save(array('flowName'=>$flowName,'modulefiled'=>$moduleFiled),"flowId=$_POST[flowId] and moduleId=$_POST[moduleId]");
		$this->assign('jumpUrl',__URL__."/flow/modelId/".$_POST['moduleId']);
		$this->success("更新成功");
	}
	/**
	 +------------------------------------------------------------------------------
	 * 字段列表控件程序
	 +------------------------------------------------------------------------------
	*/
	public function filedSelect($id){
		$filed = new ZworkModuleFiledModel();
		$filedList = $filed->where("modelId=$id")->order("`order` asc")->findAll();
		$treefiled = "filed.add(0,-1,'','','','','','','','tree-root');";	
		foreach ($filedList as $k=>$v){
			$treefiled .="filed.add($v[filedId],0,'$v[name]','javascript:ch(\'$v[name]\',\'$v[filedId]\');','','','/oa/Tpl/default/Public/images/treeicon/t_ini.gif','/oa/Tpl/default/Public/images/treeicon/t_ini.gif','','oTree-bg');";
		}
		return $treefiled;
	}

//==============================数据库操作================================================================	
	//==========创建模型数据表==================
	protected function table($name){
		$tablename = 'zwork_'.$name;
		$Model = new Model();
		$a = $Model->query("SHOW TABLES LIKE '$tablename';");
		if(empty($a)){
			$Model->query("CREATE TABLE `$tablename` (`id` INT( 11 ) NOT NULL AUTO_INCREMENT,PRIMARY KEY ( `id` ), `zworkId` INT( 11 ) NOT NULL) ENGINE = MYISAM DEFAULT CHARSET=utf8;");
			$url = $_SERVER["DOCUMENT_ROOT"]."/oa/Lib/Model/".$tablename."Model.class.php";
			$str="<?php class ".$tablename."Model extends Model{\r\n\r\n}";
			file_put_contents($url,$str);
			return true;
		}else{
			return false;
		}
	}
	//===========删除模型数据表==================
	protected function tableDel($modelId){
		$workModule = new ZworkWorkModuleModel();
		$row = $workModule->find($modelId);
		$modelTableName = 'zwork_'.$row['modelTableName'];
		$Model = new Model();
		$Model->query("DROP TABLE `$modelTableName`");
		return;
	}
	//===========创建模型字段=================
	protected function tablefiled($table,$name){	
		$filed = 'kj_'.$name;
		$tablename = 'zwork_'.$table; 
		$Model = new Model();
		$Model->query("ALTER TABLE `$tablename` ADD `$filed` VARCHAR( 50 ) NULL;");
	}
	
	//===========删除字段==================
	protected function filedDel($table,$filed){
		$tableName = 'zwork_'.$table;
		$Model = new Model();
		$Model->query("ALTER TABLE `$tableName` DROP `$filed`");
		return;
	}
	//===========预览模型========================
	public function view(){
		$this->judge($_GET['modelId'],__APP__."/WorkFlow/view/moduleId/$_GET[modelId]");
		$workModule = D('ZworkWorkModule');
		$row = $workModule->find($_GET['modelId']);
		$this->assign('row',$row);
		
		$model = new ZworkModuleFiledModel();
		$modelList = $model->where("modelId=$_GET[modelId]")->order("`order` asc")->findall();
		foreach($modelList as $k=>$v){
			$modelList[$k]['form'] =  $this->typeForm($v['formtype'],$v['filed'],$v['setting']);
		}
		$this->assign('modelList',$modelList);
		$this->display();
	}
	//============表格输出类型===============
	protected function typeForm($form,$filed,$setting,$value){
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
						$option .="<option value='$k'>$v</option>";
					}				
					return "<select name='$filed' >$option</select>";
				}
				break;
			case 'datetime':
				return "<input type='text' name='$filed' value='' id='formdata' onfocus=\"WdatePicker({dateFmt:'$setting[dateformat]'})\" >";
				break;
			case 'number':
				echo "number";
				break;
		}
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
	//==========select添加==================
	public function select(){ 
		$this->display('ZworkForm:form_'.$_POST['formtype']);
	}
	//============模型类别=================
	public function category(){
		$category=array(1=>'公文',2=>'行政',3=>'人事',4=>'财务',5=>'研发',6=>'生产',7=>'销售');
		return $category;
	}
	//===========设计模板=====================
	public function layout(){
		$module = D('ZworkWorkModule');
		$row = $module->find($_GET['modelId']);
		$this->assign('row',$row);
		$model = D('ZworkModuleFiled');
		$modelList = $model->where("modelId=$_GET[modelId]")->order("`order` asc")->findall();
		$this->assign('modelList',$modelList);
		
		if($module->modelTempleate){
			$this->assign('templeate',$module->modelTempleate);
		}else{
			foreach($modelList as $k=>$v){
				$modelList[$k]['form'] =  $this->typeForm($v['formtype'],'kj_'.$v['filedId'],$v['setting']);
			}
			$templeate = "<table width='100%' border='0' cellspacing='0' cellpadding='0' class='dm_tablezana'>";
			foreach ($modelList as $k=>$v){
				$templeate .= "<tr><td>$v[name]</td><td>{\$$v[name]}</td></tr>";
			}
			$templeate .="</table>";
			$this->assign('templeate',$templeate);
		}
		$this->assign('modelId',$_GET['modelId']);
		$this->display();
	}
	
	//=============测试====================
	public function test(){
		$templeate = stripslashes($_POST['FCKeditor1']);
		$module = new ZworkWorkModuleModel();
		$module->find($_POST['modelId']);
		$module->save(array('modelTempleate'=>$templeate),"modelId = $_POST[modelId]");
		$this->success('更新成功');
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
			return ;
		}
	}	
 
}
?>