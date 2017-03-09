<?php
/**********************************
 *       投票管理模块
 * author:廖秋虎  time:08/12/15
 **********************************/
class voteAction extends UserAction {

	/*****************投票入口文件***********************************/
	public function index(){
		$vote = new voteTitleModel();
		
		if($this->LOGIN_USER_PRIV !="1"){
			$map="FROM_ID='$this->LOGIN_USER_ID'";
		}else{
			$map="";
		}
		$count=$vote->count($map);
	    import("ORG.Util.Page");	
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '20';
		}
		$p = new Page($count,$listRows);
				
		$voteList = $vote->where($map)->order("VOTE_ID desc")->limit("$p->firstRow,$p->listRows")->findAll();
		
		//foreach ($voteList as $key=>$v){
			//$voteList[$key][allname]=implode(",",$this->find_allname($v['TO_ID']));
		//}
		 
		$page = $p->show();
		$this->assign("page",$page);
				
		$this->assign('voteList',$voteList);
		$this->display();
	}
	
	
	/*****************投票添加模板文件***********************************/
	
	
	
	public function add(){
		####选择部门
		//UserSelectAction::DeptSelect();
		#########弹出框开始#########
		UserSelectAction::DeptSelect();
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = $this->js_list();  //js人员信息列表
		$list_p=$this->left_privtree();//左侧角色树
		
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束#########  
        
		$itemlist = range(1, 10);
		$this->assign("itemlist",$itemlist);
		$this->display();
	}
	 
	/*****************投票删除文件***********************************/
	public function del(){
		###删除选项
		$item = new voteItemModel();
		$item->where("VOTE_ID='$_REQUEST[VOTE_ID]'")->delete();
		###删除投票
		$vote = new voteTitleModel();
		$vote->where("VOTE_ID='$_REQUEST[VOTE_ID]'")->delete();
		
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('删除成功!');
	}
	/*------------------设置投票状态-----------------------*/
	public function setvote(){
		$VOTE_ID=$_REQUEST[VOTE_ID];
		$OP=$_REQUEST[OP];
		$END_DATE=date("Y-m-d",$this->CUR_TIME_INT-24*60*60);
		$dao=D("voteTitle");
		if ($OP==1) {
		  $dao->setField("BEGIN_DATE",$this->CUR_DATE,"VOTE_ID='$VOTE_ID'");	
		}elseif ($OP==2){
		  $dao->setField("END_DATE",$END_DATE,"VOTE_ID='$VOTE_ID'");	
		}elseif ($OP==3){
		  $dao->setField("END_DATE",'0000-00-00',"VOTE_ID='$VOTE_ID'");	
		}
        header("location:".__URL__."/index");		
	}
	
	
	/******************投票显示**************************************/
	public function show(){
		$vote = new voteTitleModel();
		$voteRow = $vote->find($_GET['VOTE_ID']);
		
		if ($voteRow[VIEW_PRIV]==2) {
			$this->error("不允许查看投票结果");
		}
		if ($voteRow[VIEW_PRIV]==0) {
			if (!find_id($voteRow[READERS],$this->LOGIN_USER_ID)) {
              $this->error("您还没有投票，不允许查看投票结果");    				
			}
		}
		$this->assign("ANONYMITY",$voteRow[ANONYMITY]);
		$this->assign('voteRow',$voteRow);
		
		//统计
		$voteItem = new voteItemModel();
		$sums=$voteItem->where("VOTE_ID=$_GET[VOTE_ID]")->field("sum(VOTE_COUNT) as sum")->find();
		$sum=$sums[sum];
		//$voteItemStat = $voteItem->find("VOTE_ID=$_GET[VOTE_ID]","sum(VOTE_COUNT),max(VOTE_COUNT)");
		$voteItemList = $voteItem->where("VOTE_ID=$_GET[VOTE_ID]")->findAll();
		foreach($voteItemList as $k=>$v){
			$voteItemList[$k][percent]=floor($v['VOTE_COUNT']*100/$sum);
			if (!$v[VOTE_COUNT]) {
			   $voteItemList[$k][wh] = 10;				
			}else{ 
			   $voteItemList[$k][wh] = $v['VOTE_COUNT']*200/$sum;
			}
		}
		
		$this->assign('voteItemList',$voteItemList);
		$this->display();
	}
	/*****************投票更新文件***********************************/
	public function update(){
		#########弹出框开始#########
		UserSelectAction::DeptSelect();
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
		
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束######### 
		$VOTE_ID=intval($_REQUEST[VOTE_ID]); 
		$votetitle = new voteTitleModel();
		$ROW = $votetitle->where("VOTE_ID='$VOTE_ID'")->find();  
		$dao_d=D("Department");//按部门选择人员 
		$dao_p=D("UserPriv");//按角色选择人员 
		$dao_u=D("User");//按个人选择人员 
		$TO_USER_ID=explode(",",$ROW[TO_ID]);
        foreach ($TO_USER_ID as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if($b=="D"){ 
            			$list4 = $dao_d->where("DEPT_ID='$c'")->find(); 
            			$listall[$key][id]="D_".$list4[DEPT_ID];
            			$listall[$key][name]=$list4[DEPT_NAME];
            		}
            		if ($b=="P"){ 
            			$list4 = $dao_p->where("USER_PRIV='$c'")->find();
            			$listall[$key][id]="P_".$list4[USER_PRIV];
            			$listall[$key][name]=$list4[PRIV_NAME];
 					}
 					if ($b=="U"){ 
            			$list4 = $dao_u->where("uid='$c'")->find();
            			$listall[$key][id]="U_".$list4[uid];
            			$listall[$key][name]=$list4[USER_NAME];
  					}   	       
        }
		
		$voteRow = $votetitle->where("VOTE_ID=$_GET[VOTE_ID]")->find(); 
			$voteRow[allname]=implode(",",$this->find_allname($voteRow['TO_ID']));
		 
        $this->assign("listall",$listall); 
		$this->assign('voteRow',$voteRow);
		 
		$this->display();
	}
	
	
	/*****************投票添加程序文件***********************************/
	public function save(){
		$VOTE_ID=$_REQUEST[VOTE_ID];
		$vote = new voteTitleModel();
			if (!$_POST[BEGIN_DATE]) {
                $this->error("请选择开始时间");				
			}elseif($_POST[BEGIN_DATE]<date("Y-m-d")) {
				$this->error("开始时间不能早于今天");
			}
			if ($_POST[END_DATE]&&$_POST[END_DATE]<$_POST[BEGIN_DATE]) {
                $this->error("终止日期不能早于生效日期");
			}
		if ($VOTE_ID) {//修改
			$vote->create();
			$vote->save();
			$this->assign('jumpUrl',__URL__."/index"); 
	        $this->success('成功修改!');		
		}else {//添加
			$_POST['FROM_ID']=$this->LOGIN_USER_ID;
			$_POST['SEND_TIME']=$this->CUR_TIME; 
			$vote->create();
			$vote->add();
			//echo $vote->getLastSql();exit;	
			$this->assign('jumpUrl',__URL__."/index"); 
	        $this->success('新建投票成功!');
		}
	}
	/*****************添加部门****************************************************************/
	public function deptSelect(){
		
		$s = $this->dept_tree_list(0,$privOp);
		$this->assign('s',$s);
		$this->display();			
	}
	/****************投票项目********************************/
	public function item(){
		$vote = new voteTitleModel();
		$voteList = $vote->where("VOTE_ID=$_GET[VOTE_ID]")->find();
		$this->assign("ANONYMITY",$voteList[ANONYMITY]);
		$this->assign('voteList',$voteList);
		
		$voteItem = new voteItemModel();
		$voteItemList = $voteItem->where("VOTE_ID=$_GET[VOTE_ID]")->findAll();
		$this->assign('voteItemList',$voteItemList);
		$this->display();
	}
	/****************投票项目添加**************************/
	public function itemAdd(){
		$voteItem = new voteItemModel();
		if(false === $voteItem->create()) {
        	$this->error($voteItem->getError());
        }
		$voteItem->add();
		header("location:".__URL__."/item/VOTE_ID/$_POST[VOTE_ID]");
		//$this->success('更新成功');
	}
	/*-------------------投票项目更新------*/
	public function itemUpdate(){
		$voteItem = new voteItemModel();
		if(false === $voteItem->create()) {
        	$this->error($voteItem->getError());
        }
		$voteItem->where("ITEM_ID='$_POST[ITEM_ID]'")->save();
		header("location:".__URL__."/item/VOTE_ID/$_POST[VOTE_ID]");		
	}

	/*-------------------投票项目删除------*/
	public function itemDelete(){
		$voteItem = new voteItemModel();
		$voteItem->where("ITEM_ID='$_REQUEST[ITEM_ID]'")->delete();
		header("location:".__URL__."/item/VOTE_ID/$_REQUEST[VOTE_ID]");
	}
	
	/*********************前台展示*******************/
	public function voteIndex(){
		$maparr=array();
		$daou=D("User");
		$rowu=$daou->where("uid='$this->_uid'")->find();
		##角色
		$user_priv="P_".$rowu[USER_PRIV];
		$maparr[].=" (InStr(TO_ID,',$user_priv,')>0 or InStr(TO_ID,'$user_priv,')=1 or InStr(TO_ID,',$user_priv')=1)";
		##部门
		$user_dept=$rowu[DEPT_ID];
		$dpstr=getParentDepts($user_dept,$pre="D_");
		$dparr=explode(",",$dpstr);
		foreach ($dparr as $deptid){
			if ($deptid) {
			$maparr[].=" (InStr(TO_ID,',$deptid,')>0 or InStr(TO_ID,'$deptid,')=1 or InStr(TO_ID,',$deptid')=1)";	
			}
            
		}
		//echo $dp;
		##用户ID
		$user_uid="U_".$rowu[uid];
		$maparr[].=" (InStr(TO_ID,',$user_uid,')>0 or InStr(TO_ID,'$user_uid,')=1 or InStr(TO_ID,',$user_uid')=1)";
		
		$maparr[].=" TO_ID='' or TO_ID='all' or TO_ID='ALL_DEPT'";
		
		$maps=implode(" OR ",$maparr);
		//ECHO $maps;
		$vote = new voteTitleModel();
		//$uid="U_".$this->_uid;
		//var_dump($uid);exit();
        $map.= "(".$maps.")  AND "."BEGIN_DATE<='$this->CUR_DATE' and (END_DATE>='$this->CUR_DATE' or END_DATE = '0000-00-00')"; 
        
		$count=$vote->count($map);
	    import("ORG.Util.Page");	
		if(!empty($_REQUEST['listRows'])) {
			$listRows  =  $_REQUEST['listRows'];
		}else {
			$listRows  =  '15';
		}
		$p = new Page($count,$listRows);
		$voteList = $vote->where($map)->order("VOTE_ID desc")->limit("$p->firstRow,$p->listRows")->findAll(); 
		//ECHO $vote->getlastsql();
		$page = $p->show();
		$this->assign("page",$page);
		$this->assign('voteList',$voteList);
		$this->display();
	}
	/*********************投票页*******************/
	public function voteRead(){
		$vote = new voteTitleModel();
		$voteRow = $vote->find($_GET['VOTE_ID']);
		if($voteRow['TYPE']=="0"){
			$voteRow['TYPE_DESC']="radio";
			$voteRow['ITEM']="ITEM";
		}else {
			$voteRow['TYPE_DESC']="checkbox";
			$voteRow['ITEM']="ITEM[]";
		}

		$this->assign('voteRow',$voteRow);
		
		$voteItem = new voteItemModel();
		$voteItemList = $voteItem->where("VOTE_ID=$_GET[VOTE_ID]")->findAll();
		
		$this->assign('voteItemList',$voteItemList);
		
		$this->display();
	}
	/****************投票*********************/
	public function vote(){
		$vote = new voteTitleModel();
		$voteRow = $vote->find($_POST['VOTE_ID']);
		$arr = explode(',',$voteRow['READERS']);
		if(in_array($this->LOGIN_USER_ID,$arr)){
			$this->error("已经投过票了");
		}else{
			
			 $ITEM=$_POST['ITEM'];
			 if (empty($ITEM)) {
				$this->error("请选择投票项目");
			 }
			
			$READERS = $this->LOGIN_USER_ID.",".$voteRow['READERS'];
			$vote->save(array('READERS'=>$READERS),"VOTE_ID=$_POST[VOTE_ID]");
			$voteItem = new voteItemModel();
			if (is_array($ITEM)) {
				foreach ($ITEM as $ITEM_ID){
					$voteItemRow=$voteItem->where("ITEM_ID=$ITEM_ID")->find();
			        $VOTE_USER = $this->LOGIN_USER_ID.",".$voteItemRow['VOTE_USER'];
			        $voteItem->save(array('VOTE_USER'=>$VOTE_USER,'VOTE_COUNT'=>$voteItemRow['VOTE_COUNT']+1),"ITEM_ID='$ITEM_ID'");		
				}
			}else{ 
				$voteItemRow=$voteItem->where("ITEM_ID=$ITEM")->find();
				$VOTE_USER = $this->LOGIN_USER_ID.",".$voteItemRow['VOTE_USER'];
				$voteItem->save(array('VOTE_USER'=>$VOTE_USER,'VOTE_COUNT'=>$voteItemRow['VOTE_COUNT']+1),"ITEM_ID=$_POST[ITEM]");
			}
			$this->success("投票成功");
		}
	}
	/********************部门方法类***********************************************************/
	private function dept_tree_list($deptId,$privOp){
		$department = new DepartmentModel();
		$department = $department->where("DEPT_PARENT=$deptId")->order(DEPT_NO)->findAll();
		$optionText="";
		$deepCount1=$deepCount;
		$deepCount.=" ";
		foreach ($department as $k=>$v){
			$count++;
			$deptId=$v['DEPT_ID'];
			$deptName=$v['DEPT_NAME'];
			$deptParent=$v['DEPT_PARENT'];
			if($privOp==1)
				$deptPriv=$this->is_dept_priv($deptId);
			else
				$deptPriv=1;
			
			$optionTextChild=$this->dept_tree_list($deptId,$privOp);
			if($deptPriv==1){
				$optionText.="
				<tr class=TableControl>
				<td class='menulines' id='".$deptId."' name='".$deptName."' onclick=javascript:click_dept('".$deptId."') style=cursor:hand>".$deepCount1."├".$deptName."</a></td>
				</tr>";
			}
			if($optionTextChild!="")
				$optionText.=$optionTextChild;
		}
		$deepCount=$deepCount1;
		return $optionText;
	}
	 

}
?>