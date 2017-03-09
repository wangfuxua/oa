<?php
/**
 +------------------------------------------------------------------------------
 * ThinkOA 工作流的数据写入
 +------------------------------------------------------------------------------
 * @category   ThinkOA
 * @package  ThinkOA
 * @subpackage  Core
 * @author tiger<liaoqiuhu@163.com>
 * @version  1.0
 +------------------------------------------------------------------------------
 */
class WorkExecuteAction extends WorkFormAction {
	/**
	 +------------------------------------------------------------------------------
	 * 添加工作信息
	 +------------------------------------------------------------------------------
	*/
	public function Add(){
		//表单验证
		$this->formCheck($_POST['moduleName'],'工作名不能为空');
		$this->formCheck($_POST['userId'],'工作流程承办人不能为空');
		//添加工作记录
		$workData=array('modelId'=>$_POST['modelId'],'zworkName'=>$_POST['moduleName'],'UserId'=>$this->LOGIN_USER_ID,'grade'=>$_POST['grade'],'zworkTime'=>time(),'state'=>2);
		$zworkId = $this->workAdd($workData);
		//添加工作流记录
		$this->flowAdds($_POST['modelId'],$_POST['userId'],$zworkId);
		//完成流程添加记录
		$module = D('ZworkWorkModule');
		$moduleRow = $module->find($_POST['modelId']);
		$_POST['TableName'] = 'zwork_'.$moduleRow['modelTableName'];
		$_POST['zworkId'] = $zworkId;
		$this->workData($_POST['TableName'],$zworkId);
		//结束流程
		$flow = D('ZworkFlow');
		$flow->save(array('startTime'=>time(),'endTime'=>time(),'state'=>3),"zworkId=$zworkId and flowId=1");
		//成功返回
		$this->assign('jumpUrl',__APP__."/ZworkFlow/index"); 
		$this->success('操作成功!');
	}
	/**
	 +------------------------------------------------------------------------------
	 * 流程执行
	 * case-保存 
	 * case-完成 
	 * case-转交
	 * case-中止
	 +------------------------------------------------------------------------------
	*/
	public function FlowSave(){
		$model = D($_POST['TableName']);
		if(false === $model->create()) {
        	$this->error($model->getError());
        }
		switch ($_POST['submit']){
			case '保存':
				$this->workData($_POST['TableName'],$_POST[zworkId]);
				$this->success('保存成功');
				break;
			case '完成':
				$flow = D('ZworkFlow');
				//获取最大流程
				$flowList = $flow->where("zworkId=$_POST[zworkId]")->findAll();
				foreach ($flowList as $k=>$v){
					$arr[$k] = $v['flowId']; 
				}
				$max = max($arr);
				$work = D('ZworkWork');
				$workRow = $work->where("zworkId=$_POST[zworkId]")->find();
				if($workRow['state']==$max){
					$data=array('zworkEndTime'=>time(),'state'=>1001);
					$work->save($data,"zworkId=$_POST[zworkId]");
					$flow->save(array('endTime'=>time(),'state'=>3),"zworkId=$_POST[zworkId] and flowId=$workRow[state]");	
				}else{
					$data=array('state'=>$workRow['state']+1);
					$work->save($data,"zworkId=$_POST[zworkId]");
					$flow->save(array('endTime'=>time(),'state'=>3),"zworkId=$_POST[zworkId] and flowId=$workRow[state]");
				}
				
				$this->workData($_POST['TableName'],$_POST['zworkId']);
				$this->assign('jumpUrl',__APP__."/ZworkFlow/index"); 
				$this->success('工作完成');
				break;
			case '转交':				
				redirect(__APP__."/ZworkFlow/deliver/zworkId/$_POST[zworkId]");
				break;
			case '中止':
				redirect(__APP__."/ZworkFlow/stop/zworkId/$_POST[zworkId]");
				break;
		}
	}
	
	/**
	 +------------------------------------------------------------------------------
	 * 转交
	 +------------------------------------------------------------------------------
	*/
	public function deliverSave(){
		$flow = new ZworkFlowModel();
		$tag = $this->LOGIN_USER_NAME.'转交您:<br>&nbsp;&nbsp;&nbsp;&nbsp;'.$_POST['tag'];
		$user = UserAction::getUser_id($_POST['user_ID']);	
		$powerUser= $this->arrStr($user);
		$data=array('powerUser'=>$powerUser,'tag'=>$tag);
		$flow->save($data,"id=$_POST[id]");
		$this->assign('jumpUrl',__APP__."/ZworkFlow/index"); 
		$this->success('转交成功!');    
	}
	
	/**
	 +------------------------------------------------------------------------------
	 * 表单验证
	 +------------------------------------------------------------------------------
	*/
	protected function formCheck($data,$message){
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
	/**
	 +------------------------------------------------------------------------------
	 * zworkwork表做添加记录
	 +------------------------------------------------------------------------------
	*/
	protected function workAdd($data){
		$work =  new ZworkWorkModel();
		$id = $work->add($data);		
		return $id;
	}
	/**
	 +------------------------------------------------------------------------------
	 * 存储数据
	 +------------------------------------------------------------------------------
	*/
	protected function workData($table,$id){
		$model = D($table);
		$a = $model->where("zworkId=$id")->find();
		if($a){
			$model->create();
			$model->where("zworkId=$id")->save();
		}else{
			$model->create();
			$model->add();
		}
		return;
	}
	/**
	 +------------------------------------------------------------------------------
	 * 流程zworkflow表做添加记录
	 +------------------------------------------------------------------------------
	*/
	protected function flowAdds($modelId,$userId,$zworkId){
		$flow = new ZworkFlowModel();
		$moduleflow = new ZworkModuleFlowModel();
		$moduleflowList = $moduleflow->where("moduleId=$modelId")->findAll();
		foreach($moduleflowList as $v){
			$user = $userId["$v[flowId]"];
			$user = UserAction::getUser_id($user);
			$user = $this->arrStr($user);
			$data = array('powerItem'=>$v['modulefiled'],'flowName'=>$v['flowName'],'powerUser'=>$user,'zworkId'=>$zworkId,'flowId'=>$v['flowId']);
			$flow->add($data);
		}
		return;
	}
	/**
	 +-----------------------------------------------------------------------------
	 * 数组转换成字符串
	 +-----------------------------------------------------------------------------
	 */
	protected function arrStr($arr){
		$count = count($arr);
		if($count==1){
			$value = '';
			foreach ($arr as $v){
				$value .= $v;
			}
		}else{
			$value = '';
			foreach ($arr as $v){
				$value .= $v.',';
			}
			$value = substr($value,'0',strlen($value)-1);
		}
		return $value;
	}
}
?>