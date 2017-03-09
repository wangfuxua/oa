<?php
/**********************************
 *       工资管理模块
 * author:廖秋虎  time:08/12/17
 **********************************/
class salaryAction extends PublicAction{
/************************工资管理（及项目定义）入口程序********************************/
	public function index(){
		$salItem = new salItemModel();
		$count = $salItem->count();
		$salList = $salItem->order('item_id')->order("ITEM_ORDER")->limit("0,50")->findAll();
		
		$this->assign('salList',$salList);
		$this->assign('count',$count);
		$this->display();
	}
/************************工资项目添加程序********************************/	
	public function itemAdd(){
		
		$salItem = new salItemModel();
		$count=$salItem->count();
		if ($count>=50) {
			$this->error("您最多只能添加50条工资项目");
		}
		if($salItem->create()){
			
			$id=$salItem->add();
			if ($id) {
			$this->data($id);
			$this->assign('jumpUrl',__URL__."/index"); 
        	$this->success('添加成功!');
			}else {
			$this->assign('jumpUrl',__URL__."/index"); 
        	$this->success('添加失败!');
			}
			
		}else
			exit();
	}
/************************工资项目修改程序********************************/	
	public function itemEdit(){
		$salItem = new salItemModel();
		$salRow = $salItem->where("ITEM_ID=$_GET[ITEM_ID]")->find();
		$this->assign('salRow',$salRow);
		$this->display();
	}
/************************工资项目更新数据入库程序**************************/
	public function itemUpdate(){		
		$salItem = new salItemModel();
		if($salItem->create()){
		$salItem->where("ITEM_ID=$salItem->ITEM_ID")->save();
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('更新成功!');
		}else 
			exit();
	}	
/************************工资项目删除程序********************************/	
	public function itemDel(){
		$salItem = new salItemModel();
		if($_GET[ITEM_ID]){
			$salItem->where("ITEM_ID=$_GET[ITEM_ID]")->delete();
			$this->deletedata($_GET[ITEM_ID]);
			$this->assign('jumpUrl',__URL__."/index"); 
        	$this->success('删除成功!');
		}else 
			exit();
	}
/************************工资项目全部删除程序********************************/
	public function itemDellAll(){
		$salItem = new salItemModel();
		$list=$salItem->findAll();
		foreach ($list as $row){
			$this->deletedata($row[ITEM_ID]);
		}
		$salItem = new salItemModel();
		$salItem->deleteAll();
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('删除成功!');
	}
	
    public function deleteSelectItem(){
    	$DELETE_STR=$_REQUEST[DELETE_STR];
    	$salItem = new salItemModel();
    	
    	$arr=explode(",",$DELETE_STR);
    	foreach ($arr as $item_id){
    		$this->deletedata($item_id);
    		$salItem->where("ITEM_ID='$item_id'")->delete();
    	}
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('删除成功!'); 
    }	
/************************工资上报入口程序********************************/	
	public function flowIndex(){
		$dao=D("salItem");
		$count=$dao->count();
		if (!$count) {
			$this->error("未定义工资项目，不能创建上报流程");
		}
		import("ORG.Util.Page");
		$curDate=date("Y-m-d",$this->CUR_TIME_INT);
		$curMon = date("m",$this->CUR_TIME_INT);
		$this->assign('curDate',$curDate);
		$this->assign('curMon',$curMon);
		
		$salFlow = new SalFlowModel();
		$count=$salFlow->count();
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '';
		}
		$p= new Page($count,$listRows);
		$flowList = $salFlow->order('SEND_TIME')->limit("$p->firstRow,$p->listRows")->findAll();
		$page= $p->show();
		$this->assign("page",$page);
		
		$this->assign('flowList',$flowList);
	
		$this->display();
	}

/************************工资上报添加程序********************************/	
	public function flowAdd(){
		$BEGIN_DATE_F=$_POST['BEGIN_DATE'];
	//-----------------判断日期是否合法---------------------------
		if(!empty($_POST['BEGIN_DATE'])){
			$timeOk=$this->is_date($_POST['BEGIN_DATE']);
			
			if(!$timeOk)
				$this->error('错误：上报起始日期格式不对，应形如1999-1-2',false);
						
		}
		if(!empty($_POST['END_DATE'])){
			$timeOk=$this->is_date($_POST['END_DATE']);
			if(!$timeOk)
				$this->error('错误：上报起始日期格式不对，应形如1999-1-2',false);
				
		}
		$curDate=$this->CUR_TIME_INT;
		$_POST['BEGIN_DATE'] = $this->formatDate($_POST['BEGIN_DATE']);
		$_POST['END_DATE'] = $this->formatDate($_POST['END_DATE']);
		if($_POST['BEGIN_DATE']<($curDate-86400)){
			$this->error('错误：上报起始日期不能早于今日',false);
				
		}
		if($_POST['BEGIN_DATE']>$_POST['END_DATE']){
			$this->error('错误：上报起始日期不能晚于上报截止日期',false);
		}
		
		if ($_POST['BEGIN_DATE']==$_POST['END_DATE']) {
		    $_POST['END_DATE']=$_POST['BEGIN_DATE']+86300;	
		}
		
	//------------------写入数据库-----------------------
		$salFlow = new SalFlowModel();
		if($salFlow->create()){
			$salFlow->SEND_TIME = $this->CUR_TIME_INT;
			$salFlow->add();
			
			  
			  //------- 短信提醒 --------
			  if($_POST[SMS_REMIND]=="on")
			  {
			  	$data[SEND_TIME]=$this->CUR_TIME;
			    if($_POST['BEGIN_DATE']>($curDate-86400))
			       $data[SEND_TIME]=$BEGIN_DATE_F;

			    $data[CONTENT]="请进行工资上报！\n备注：".csubstr($_POST[CONTENT],0,100);
			    $data[FROM_ID]=$this->LOGIN_USER_ID;
			    
			    $dao=new Model();
			    $list=$dao->table("user,user_priv")
			              ->where("user.USER_PRIV=user_priv.USER_PRIV and FUNC_ID_STR like '%,28,%'")
			              ->findAll();
			              
			    $daosms=D("Sms");
			    foreach ($list as $ROW){
			    	$data[TO_ID]=$ROW["USER_ID"];
			    	$data[SMS_TYPE]=4;
			    	$data[REMIND_FLAG]=1;
			    	$daosms->add($data);
			    	
			    }
			  }
  			
			$this->assign('jumpUrl',__URL__."/flowIndex"); 
        	$this->success('添加成功!');
        				
			//$this->redirect('flowIndex');
		}
		
	}
/************************工资上报删除程序********************************/
	public function flowDel(){
        $FLOW_ID=$_REQUEST[FLOW_ID];
		
		$salFlow = new SalFlowModel();
		$salData = D("salData");
		$daosms = D("Sms");
		
		$rows = $salFlow->where("FLOW_ID=$_GET[FLOW_ID]")->find();
		$SMS_CONTENT="请进行工资上报！\n备注：".csubstr($rows[CONTENT],0,100);
		$BEGIN_DATE=date("Y-m-d H:i:s",$rows[BEGIN_DATE]);   
		$salFlow->where("FLOW_ID=$_GET[FLOW_ID]")->delete();
		$salData->where("FLOW_ID=$_GET[FLOW_ID]")->delete();
		
		$daosms->where("FROM_ID='$this->LOGIN_USER_ID' and to_days(SEND_TIME)=to_days('$BEGIN_DATE') and CONTENT='$SMS_CONTENT' and SMS_TYPE='4'")->delete();

		$this->redirect('flowIndex');
	}	
/************************工资报表程序********************************/
	public function flowReport(){
		$salItem = new salItemModel();
		$salItemList = $salItem->order('ITEM_ORDER')->findAll();
		
		$this->assign('salItemList',$salItemList);
		
		import("ORG.Util.Page");
		
		$Model = new Model();
		$userModel = $Model->query("SELECT * from user,user_priv,DEPARTMENT where DEPARTMENT.DEPT_ID=user.DEPT_ID and user.USER_PRIV=user_priv.USER_PRIV order by DEPT_NO,PRIV_NO,USER_NAME");
		
		$dao=D("salData");
		foreach ($userModel as $key=>$rows){
			$list=$dao->where("USER_ID='$rows[USER_ID]' AND FLOW_ID='$_REQUEST[FLOW_ID]'")->find();
			
			$sum=0;
			foreach ($salItemList as $i=>$row){
		       $array["s".$row[ITEM_ID]]=$list["s".$row[ITEM_ID]];
		       $sum+=$array["s".$row[ITEM_ID]];
			}
			$userModel[$key][sub]=$array;
			$userModel[$key][sum]=$sum;
			
		}
		$this->assign('userModel',$userModel);
		
		//统计
		    $dao=D("salData");     
			foreach ($salItemList as $i=>$row){
			  $zd="s".$row[ITEM_ID];
			  $v="count".$row[ITEM_ID];
		      $list=$dao->where("FLOW_ID='$_REQUEST[FLOW_ID]'")->field("sum($zd) as $v")->find();
		      $listsum[$zd]=$list[$v];
			}
			$this->assign("listsum",$listsum);
		
		$this->display();
	}
/************************工资终止程序********************************/
	public function flowStop(){
		$curDate=$this->CUR_TIME_INT;
		if($_GET['FLOW_ID']){
		$salFlow = new SalFlowModel();
		$salFlow->setField('END_DATE',$curDate,"FLOW_ID=$_GET[FLOW_ID]");
		$this->redirect('flowIndex');
		}
	}
/***********************日期格式判断**************方法*******************/
	protected function is_date($dateStr){
		$dateStr=explode(" ",$dateStr);
		$dateStr=$dateStr[0];
		if($dateStr==null||strlen($dateStr)==0)
			return false;
		$dateArray=explode("-",$dateStr);
		if(count($dateArray)!=3)
			return false;
		if(!checkdate($dateArray[1],$dateArray[2],$dateArray[0]))
			return false;
		return true;		
	}
/**********************日期格式Y-m-d返回时间戳***************方法***************/
	protected function formatDate($date){
		$dateArray = explode("-",$date);
		if(count($dateArray)!=3)
			return false;
		else{
			$newDate=mktime(0,0,0,$dateArray[1],$dateArray[2],$dateArray[0]);
			return $newDate;
		}
	}
/***************************插入数据库********************************/

/*ALTER TABLE `sal_data` DROP `s8*/
	protected function data($itemid){
		//$salItem = new salItemModel();
		//$a = $salItem ->query("select max(ITEM_ID) as maxITEM_ID from sal_item");
		//$s = 's'.$a[0]['maxITEM_ID'];
		$s = 's'.$itemid;
		$Model = new Model();
		$Model->query("ALTER TABLE `sal_data` ADD `$s` VARCHAR( 50 ) NULL;");
		unlink(APP_PATH.'/Data/salData_fields.php');
		return ;
	}		
	
	protected function deletedata($itemid){
		$s = 's'.$itemid;
		$Model = new Model();
		$Model->query("ALTER TABLE `sal_data` DROP `$s`");
		unlink(APP_PATH.'/Data/salData_fields.php');
		return ;
	}
		
}